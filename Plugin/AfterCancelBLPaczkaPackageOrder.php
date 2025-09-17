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
use BLPaczka\MagentoIntegration\Api\ShippingManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceCommentCreationInterface;
use Magento\Sales\Api\Data\InvoiceCommentCreationInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentCommentCreationInterface;
use Magento\Sales\Api\Data\ShipmentCommentCreationInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Magento\Sales\Model\Order;

class AfterCancelBLPaczkaPackageOrder
{
    private OrderRepositoryInterface $orderRepository;
    private InvoiceCommentCreationInterfaceFactory $invoiceCommentCreationFactory;
    private ShipmentCommentCreationInterfaceFactory $shipmentCommentCreationFactory;
    private ShipmentTrackRepositoryInterface $shipmentTrackRepository;
    private ShipmentTrackInterfaceFactory $shipmentTrackFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        InvoiceCommentCreationInterfaceFactory $invoiceCommentCreationFactory,
        ShipmentCommentCreationInterfaceFactory $shipmentCommentCreationFactory,
        ShipmentTrackRepositoryInterface $shipmentTrackRepository,
        ShipmentTrackInterfaceFactory $shipmentTrackFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceCommentCreationFactory = $invoiceCommentCreationFactory;
        $this->shipmentCommentCreationFactory = $shipmentCommentCreationFactory;
        $this->shipmentTrackRepository = $shipmentTrackRepository;
        $this->shipmentTrackFactory = $shipmentTrackFactory;
    }

    /**
     * @throws LocalizedException
     */
    public function afterCancelOrder(
        ShippingManagementInterface $subject,
        ShippingResponseInterface $shippingResponse
    ): ShippingResponseInterface {
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
        $shippingMethodName = (string) __('BLPaczka - %1', $shippingMethodName['name'] ?? '');

        /** @var InvoiceCommentCreationInterface $invoiceMessage */
        $invoiceMessage = $this->invoiceCommentCreationFactory->create();
        /** @var ShipmentCommentCreationInterface $shipmentMessage */
        $shipmentMessage = $this->shipmentCommentCreationFactory->create();

        $invoiceMessage->setComment(
            (string) __('Invoice created!')
        );
        $shipmentMessage->setComment(
            (string) __('Shipment created with BLPaczka! Shipping Method: %1', $shippingMethodName)
        );


        if (!$orderId) {
            throw new LocalizedException(__('Order ID is required!'));
        }

        /** @var Order $order */
        $order = $this->orderRepository->get((int) $orderId);

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
