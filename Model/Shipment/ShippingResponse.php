<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model\Shipment;

use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Phrase;

class ShippingResponse extends Json implements ShippingResponseInterface
{
    /**
     * @var ?string
     */
    protected $json = null;

    /**
     * @inheritDoc
     */
    public function getResponseData(): ?array
    {
        $data = $this->json !== null ? json_decode($this->json, true) : null;

        return $data['data'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getResponseStatus(): ?int
    {
        $data = $this->json !== null ? json_decode($this->json, true) : null;

        return $data['status'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getResponseMessage(): ?string
    {
        $data = $this->json !== null ? json_decode($this->json, true) : null;
        $message = $data['message'] ?? null;

        return $message !== null ? (string) $message : null;
    }
}
