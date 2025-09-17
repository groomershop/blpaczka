<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model;

use BLPaczka\MagentoIntegration\Api\ApiServiceInterface;
use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface;
use BLPaczka\MagentoIntegration\Api\OrderManagementInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Store\Api\Data\StoreInterface;

class OrderManagement implements OrderManagementInterface
{
    private ApiServiceInterface $apiService;
    private Filesystem $filesystem;
    private UrlInterface $urlBuilder;
    private OrderRepositoryInterface $orderRepository;
    private InvoiceRepositoryInterface $invoiceRepository;
    private LabelGenerator $labelGenerator;

    public function __construct(
        ApiServiceInterface $apiService,
        Filesystem $filesystem,
        UrlInterface $urlBuilder,
        OrderRepositoryInterface $orderRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        LabelGenerator $labelGenerator
    ){
        $this->apiService = $apiService;
        $this->filesystem = $filesystem;
        $this->urlBuilder = $urlBuilder;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->labelGenerator = $labelGenerator;
    }

    public function hasBLPaczkaOrderData(OrderInterface $order): bool
    {
        return !empty($order->getData('blpaczka_order_data'));
    }

    public function isBLPaczkaOrder(OrderInterface $order): bool
    {
        if ($order->getShippingMethod()) {
            $shippingMethod = $order->getShippingMethod(true);

            return $shippingMethod->getData('carrier_code') === ConfigManagementInterface::SHIPPING_METHOD_CODE;
        } else {
            return false;
        }
    }

    public function getBLPaczkaData(OrderInterface $order): ?array
    {
        $orderedItem = $order->getData('blpaczka_order_data');

        try {
            return $orderedItem ? json_decode($orderedItem, true) : null;
        } catch (\Throwable $t) {
            return null;
        }
    }

    public function setBLPaczkaData(OrderInterface $order, ?array $data): void
    {
        if (empty($data)) {
            $order->setData('blpaczka_order_data', null);
        } else {
            try {
                $json = json_encode($data);
                $order->setData('blpaczka_order_data', $json);
            } catch (\Throwable $t) {
                return;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function createShippingLabel(OrderInterface $order, bool $isA6 = false): string
    {
        $format = $isA6 ? OrderManagementInterface::FORMAT_A6 : OrderManagementInterface::FORMAT_A4;
        $key = $isA6 ? 'blpaczka_shipping_label_blpaczka_a6' : 'blpaczka_shipping_label_blpaczka_a4';
        $shippingLabel = $order->getData($key);

        if ($shippingLabel) {
            return (string) $shippingLabel;
        }

        $blpaczkaOrderData = $this->getBLPaczkaOrderItem($order);

        if (isset($blpaczkaOrderData['id'])) {
            $message = __('BLPaczka shipping label is empty!');

            try {
                $shippingLabel = $this->getWaybillFileContent($blpaczkaOrderData['id'], $format, $order->getStore());
            } catch (LocalizedException $e) {
                $message = __($e->getMessage());
                $shippingLabel = '';
            }

            if (empty($shippingLabel)) {
                throw new NoSuchEntityException($message);
            }

            $order->setData($key, $shippingLabel);
            $this->orderRepository->save($order);
        } else {
            throw new NoSuchEntityException(__('BLPaczka order id is empty!'));
        }

        return $shippingLabel;
    }

    public function getBLPaczkaCartOrderItem(OrderInterface $order): ?array
    {
        $blpaczkaData = $this->getBLPaczkaData($order);

        return $blpaczkaData ? ($blpaczkaData['CartOrder'] ?? null) : null;
    }

    public function getBLPaczkaOrderItem(OrderInterface $order): ?array
    {
        $blpaczkaData = $this->getBLPaczkaData($order);

        $blpaczkaOrderedItem = $blpaczkaData ? ($blpaczkaData['Order'] ?? null) : null;

        return $blpaczkaOrderedItem ? ($blpaczkaOrderedItem[0] ?? null) : null;
    }

    public function getBLPaczkaPaymentLink(OrderInterface $order): ?string
    {
        $blpaczkaCartItemData = $this->getBLPaczkaCartOrderItem($order);

        return $blpaczkaCartItemData ? ($blpaczkaCartItemData['payment_url'] ?? null) : null;
    }

    public function getBLPaczkaWaybillLink(OrderInterface $order): ?string
    {
        $blpaczkaData = $this->getBLPaczkaData($order);

        return $blpaczkaData ? ($blpaczkaData['waybill_link'] ?? null) : null;
    }

    public function getBLPaczkaWaybillLinkA4Validation(OrderInterface $order): ?string
    {
        return $this->urlBuilder->getUrl('blpaczka/integration/downloadWaybillValidation', [
            'order_id' => $order->getId(),
            'format' => OrderManagementInterface::FORMAT_A4,
        ]);
    }

    public function getBLPaczkaWaybillLinkA6Validation(OrderInterface $order): ?string
    {
        return $this->urlBuilder->getUrl('blpaczka/integration/downloadWaybillValidation', [
            'order_id' => $order->getId(),
            'format' => OrderManagementInterface::FORMAT_A6,
        ]);
    }

    public function getBLPaczkaWaybillLinkA4(OrderInterface $order): ?string
    {
        return $this->urlBuilder->getUrl('blpaczka/integration/downloadWaybill', [
            'order_id' => $order->getId(),
            'format' => OrderManagementInterface::FORMAT_A4,
        ]);
    }

    public function getBLPaczkaWaybillLinkA6(OrderInterface $order): ?string
    {
        return $this->urlBuilder->getUrl('blpaczka/integration/downloadWaybill', [
            'order_id' => $order->getId(),
            'format' => OrderManagementInterface::FORMAT_A6,
        ]);
    }

    /**
     * @inheritDoc
     */
    private function getWaybillFileContent(string $blpaczkaOrderId, string $format, ?StoreInterface $store = null): string
    {
        $response = $this->getWaybillFileResponse($blpaczkaOrderId, $format);

        try {
            return base64_decode($response->getData()[0]['file'] ?? '', true);
        } catch (\Throwable $t) {
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function getTrackingInformation(OrderInterface $order): array
    {
        $blpaczkaOrderData = $this->getBLPaczkaOrderItem($order);

        if (isset($blpaczkaOrderData['id'])) {
            $data = [
                'Order' => [
                    'id' => $blpaczkaOrderData['id'],
                ]
            ];

            $response = $this->apiService->getTrackingInformation($data, $order->getStore());
        } else {
            return [];
        }

        if ($response->getStatus() !== ApiResponseInterface::STATUS_OK) {
            return [];
        }

        return $response->getData()['Tracking'] ?? [];
    }

    /**
     * @throws LocalizedException
     */
    private function getWaybillFileResponse(string $blpaczkaOrderId, string $format, ?StoreInterface $store = null): ApiResponseInterface
    {
        if (empty($blpaczkaOrderId)) {
            throw new LocalizedException(__('BLPaczka Order ID is required!'));
        }

        $data = [
            'Order' => [
                'id' => $blpaczkaOrderId,
                'printer_type' => in_array($format, [OrderManagementInterface::FORMAT_A4, OrderManagementInterface::FORMAT_A6]) ? $format : OrderManagementInterface::FORMAT_A4,
            ]
        ];

        if ($data['Order']['printer_type'] === OrderManagementInterface::FORMAT_A4) {
            $response = $this->apiService->downloadWayBillA4($data, $store);
        } else {
            $response = $this->apiService->downloadWayBillA6($data, $store);
        }

        if ($response->getStatus() !== ApiResponseInterface::STATUS_OK) {
            throw new LocalizedException($response->getMessagePhrase());
        }

        $name = $response->getData()[0]['name'] ?? null;

        if (!$name) {
            throw new LocalizedException($response->getMessagePhrase());
        }

        return $response;
    }
}
