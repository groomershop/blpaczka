<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Api;

use BLPaczka\MagentoIntegration\Api\Data\Shipment\PaymentInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingResponseInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\Data\StoreInterface;

interface ShippingManagementInterface
{
    const SHIPPING_METHOD_CODE_ANY_COURIER = 'any-courier';
    const BLPACZKA_DATA_KEY = 'BLPaczkaData';

    /**
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\PaymentInterface
     */
    public function getPayment(?StoreInterface $store = null): PaymentInterface;

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function getRecipient(OrderInterface $order): RecipientInterface;

    /**
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface
     */
    public function getSender(?StoreInterface $store = null): SenderInterface;

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function getShippingDetails(OrderInterface $order): ShippingDetailsInterface;

    /**
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return array
     */
    public function getBLPaczkaShippingMethods(?StoreInterface $store = null): array;

    public function connect(): ShippingResponseInterface;
    public function getBLPaczkaDataFromRequestData(array $requestData): ?array;
    public function getDataForCreateOrderForm(OrderInterface $order): array;
    public function getDataForCancelOrderForm(OrderInterface $order): array;
    public function convertDataForCreateOrderRequest(OrderInterface $order, array $data): array;
    public function convertDataForGetValuationRequest(array $data): array;
    public function convertDataForCancelOrderRequest(array $data): array;
    public function getValuation(array $data): ShippingResponseInterface;
    public function createOrder(array $data): ShippingResponseInterface;
    public function cancelOrder(array $data): ShippingResponseInterface;
}
