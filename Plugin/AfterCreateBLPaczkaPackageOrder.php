<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Plugin;

use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingResponseInterface;
use BLPaczka\MagentoIntegration\Api\OrderManagementInterface;
use BLPaczka\MagentoIntegration\Api\ShippingManagementInterface;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceCommentCreationInterface;
use Magento\Sales\Api\Data\InvoiceCommentCreationInterfaceFactory;
use Magento\Sales\Api\Data\InvoiceItemCreationInterface;
use Magento\Sales\Api\Data\InvoiceItemCreationInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface;
use Magento\Sales\Model\Order\Shipment\Validation\QuantityValidator;
use Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader;

class AfterCreateBLPaczkaPackageOrder
{
    private OrderRepositoryInterface $orderRepository;
    private InvoiceOrderInterface $invoiceOrder;
    private InvoiceCommentCreationInterfaceFactory $invoiceCommentCreationFactory;
    private InvoiceItemCreationInterfaceFactory $invoiceItemCreationFactory;
    private ShipmentRepositoryInterface $shipmentRepository;
    private OrderManagementInterface $orderManagement;
    private ShipmentTrackRepositoryInterface $shipmentTrackRepository;
    private ShipmentTrackInterfaceFactory $shipmentTrackFactory;
    private ShipmentLoader $shipmentLoader;
    private ShipmentValidatorInterface $shipmentValidator;
    private TransactionFactory $transactionFactory;

    public function __construct(
        OrderRepositoryInterface               $orderRepository,
        InvoiceOrderInterface                  $invoiceOrder,
        InvoiceCommentCreationInterfaceFactory $invoiceCommentCreationFactory,
        InvoiceItemCreationInterfaceFactory    $invoiceItemCreationFactory,
        ShipmentRepositoryInterface            $shipmentRepository,
        OrderManagementInterface               $orderManagement,
        ShipmentTrackRepositoryInterface       $shipmentTrackRepository,
        ShipmentTrackInterfaceFactory          $shipmentTrackFactory,
        ShipmentLoader                         $shipmentLoader,
        ShipmentValidatorInterface             $shipmentValidator,
        TransactionFactory                     $transactionFactory
    )
    {
        $this->orderRepository = $orderRepository;
        $this->invoiceOrder = $invoiceOrder;
        $this->invoiceCommentCreationFactory = $invoiceCommentCreationFactory;
        $this->invoiceItemCreationFactory = $invoiceItemCreationFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderManagement = $orderManagement;
        $this->shipmentTrackRepository = $shipmentTrackRepository;
        $this->shipmentTrackFactory = $shipmentTrackFactory;
        $this->shipmentLoader = $shipmentLoader;
        $this->shipmentValidator = $shipmentValidator;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @throws LocalizedException
     */
    public function afterCreateOrder(
        ShippingManagementInterface $subject,
        ShippingResponseInterface   $shippingResponse
    ): ShippingResponseInterface
    {
        try {
            $this->execute($shippingResponse);
        } catch (\Throwable $t) {}

        return $shippingResponse;
    }

    private function execute(ShippingResponseInterface $shippingResponse): void
    {
        $data = $shippingResponse->getResponseData();
        $orderId = $data['MagentoOrderId'] ?? null;
        $shippingMethodName = $data['Order'] ?? [];
        $shippingMethodName = $shippingMethodName[0] ?? [];
        $shippingMethodName = (string)__('BLPaczka - %1', $shippingMethodName['name'] ?? '');

        /** @var InvoiceCommentCreationInterface $invoiceMessage */
        $invoiceMessage = $this->invoiceCommentCreationFactory->create();
        $invoiceMessage->setComment((string)__('Invoice created!'));


        if (!$orderId) {
            throw new LocalizedException(__('Order ID is required!'));
        }

        /** @var Order $order */
        $order = $this->orderRepository->get((int)$orderId);
        $items = $this->getItems($order);

        $blpaczkaTrack = $this->getTrackingInformation($order, $data, $shippingMethodName);
        if ($blpaczkaTrack) {
            /** @var Order\Shipment $shipment */
            foreach ($order->getShipmentsCollection() as $shipment) {
                /** @var ShipmentTrackInterface $track */
                foreach ($shipment->getTracksCollection() as $track) {
                    if ($blpaczkaTrack->getCarrierCode() === $track->getCarrierCode()) {
                        $this->shipmentTrackRepository->delete($track);
                    }
                }
            }
        }

        if (!empty($items['shippingItems']['items'])) {
            try {
                $this->createShipment($order, $items['shippingItems'], $shippingMethodName, $blpaczkaTrack);
            } catch (\Throwable $t) {
            }
        } else {
            if ($blpaczkaTrack && $blpaczkaTrack->getTrackNumber()) {
                /** @var Order\Shipment $shipment */
                foreach ($order->getShipmentsCollection() as $shipment) {
                    $shipment->addTrack($blpaczkaTrack);
                    $this->shipmentRepository->save($shipment);
                }
            }
        }

        /** @var Order $order */
        $order = $this->orderRepository->get((int)$orderId);
        $this->orderManagement->createShippingLabel($order);
        $this->orderManagement->createShippingLabel($order, true);
        $this->orderRepository->save($order);

        if (!empty($items['invoiceItems'])) {
            try {
                $this
                    ->invoiceOrder
                    ->execute($orderId, false, $items['invoiceItems'], false, $invoiceMessage);
            } catch (\Throwable $t) {
            }
        }
    }

    private function getItems(Order $order): array
    {
        $items = [
            'shippingItems' => ['items' => []],
            'invoiceItems' => [],
        ];

        foreach ($order->getAllItems() as $item) {
            /**
             * @see Order::canShip()
             */
            $qtyToShip = (int)$item->getQtyToShip();
            $isVirtual = $item->getIsVirtual();
            $lockedDoShip = $item->getLockedDoShip();
            $isRefunded = $item->getQtyRefunded() == $item->getQtyOrdered();

            if ($qtyToShip > 0 && !$isVirtual && !$lockedDoShip && !$isRefunded) {
                /** @var InvoiceItemCreationInterface $invoiceItem */
                $invoiceItem = $this->invoiceItemCreationFactory->create();

                $invoiceItem->setOrderItemId($item->getItemId());
                $invoiceItem->setQty($item->getQtyToShip());

                $items['shippingItems']['items'][$item->getItemId()] = $qtyToShip;
                $items['invoiceItems'][] = $invoiceItem;
            }
        }

        return $items;
    }

    private function createShipment(
        OrderInterface          $order,
        array                   $shipmentData,
        string                  $shippingMethodName,
        ?ShipmentTrackInterface $blpaczkaTrack
    ): void
    {
        $shipmentMessage = (string)__('Shipment created with BLPaczka! Shipping Method: %1', $shippingMethodName);

        $this->shipmentLoader->setOrderId($order->getId());
        $this->shipmentLoader->setShipmentId(null);
        $this->shipmentLoader->setShipment($shipmentData);
        $this->shipmentLoader->setTracking(null);
        $shipment = $this->shipmentLoader->load();

        $shipment->addComment($shipmentMessage, false, true);
        $shipment->setCustomerNote($shipmentMessage);
        $shipment->setCustomerNoteNotify(false);

        if ($blpaczkaTrack) {
            $shipment->addTrack($blpaczkaTrack);
        }

        $validationResult = $this->shipmentValidator->validate($shipment, [QuantityValidator::class]);

        if (!$validationResult->hasMessages()) {
            $shipment->register();
            $shipment->getOrder()->setCustomerNoteNotify(false);

            $shipment->getOrder()->setIsInProcess(true);
            $this->transactionFactory
                ->create()
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();
        }
    }

    private function getTrackingInformation(Order $order, array $orderData, string $shippingMethodName): ?ShipmentTrackInterface
    {
        $waybillNo = $orderData['Order'] ?? [];
        $waybillNo = $waybillNo[0] ?? [];
        $waybillNo = $waybillNo['waybill_no'] ?? null;

        $tracking = $this->shipmentTrackFactory->create();
        $tracking
            ->setTitle($shippingMethodName)
            ->setTrackNumber(null)
            ->setCarrierCode(ConfigManagementInterface::SHIPPING_METHOD_CODE);

        if ($waybillNo) {
            $tracking->setTrackNumber($waybillNo);
        }

        return $tracking;
    }
}
