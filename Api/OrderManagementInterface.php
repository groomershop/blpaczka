<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

interface OrderManagementInterface
{
    const ORDER_DATA_SHIPPING_LABEL_A4 = 'blpaczka_shipping_label_blpaczka_a4';
    const ORDER_DATA_SHIPPING_LABEL_A6 = 'blpaczka_shipping_label_blpaczka_a6';
    const FORMAT_A4 = 'A4';
    const FORMAT_A6 = 'LBL';

    public function hasBLPaczkaOrderData(OrderInterface $order): bool;
    public function isBLPaczkaOrder(OrderInterface $order): bool;
    public function getBLPaczkaData(OrderInterface $order): ?array;
    public function setBLPaczkaData(OrderInterface $order, ?array $data): void;

    /**
     * @throws NoSuchEntityException
     */
    public function createShippingLabel(OrderInterface $order, bool $isA6 = false): string;
    public function getBLPaczkaCartOrderItem(OrderInterface $order): ?array;
    public function getBLPaczkaOrderItem(OrderInterface $order): ?array;
    public function getBLPaczkaPaymentLink(OrderInterface $order): ?string;
    public function getBLPaczkaWaybillLink(OrderInterface $order): ?string;
    public function getBLPaczkaWaybillLinkA4Validation(OrderInterface $order): ?string;
    public function getBLPaczkaWaybillLinkA6Validation(OrderInterface $order): ?string;
    public function getBLPaczkaWaybillLinkA4(OrderInterface $order): ?string;
    public function getBLPaczkaWaybillLinkA6(OrderInterface $order): ?string;

    /**
     * @throws NoSuchEntityException
     */
    public function getTrackingInformation(OrderInterface $order): array;
}
