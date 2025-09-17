<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Api\Data\Shipment;

interface PaymentInterface
{
    const PAYMENT_TYPE_BANK = 'bank';
    const PAYMENT_TYPE_PAY_LATER = 'pay_later';
    const PAYMENT_TYPES = [
        self::PAYMENT_TYPE_BANK => 'Bank Payment',
        self::PAYMENT_TYPE_PAY_LATER => 'Pay Later',
    ];
    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\PaymentInterface
     */
    public function setPaymentType(?string $value): PaymentInterface;

    /**
     * @return string|null
     */
    public function getPaymentType(): ?string;
}
