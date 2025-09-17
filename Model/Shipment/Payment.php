<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model\Shipment;

use BLPaczka\MagentoIntegration\Api\Data\Shipment\PaymentInterface;

class Payment implements PaymentInterface
{
    private ?string $paymentType = null;

    /**
     * @inheritDoc
     */
    public function setPaymentType(?string $value): PaymentInterface
    {
        $this->paymentType = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }
}
