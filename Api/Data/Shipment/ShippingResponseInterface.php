<?php

namespace BLPaczka\MagentoIntegration\Api\Data\Shipment;

use Magento\Framework\Phrase;

interface ShippingResponseInterface
{
    const STATUS_NOK = 0;
    const STATUS_OK = 1;

    /**
     * @param mixed $data
     * @param bool $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param array $options Additional options used during encoding
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingResponseInterface
     */
    public function setData($data, $cycleCheck = false, $options = []);

    /**
     * @return array|null
     */
    public function getResponseData(): ?array;

    /**
     * @return int|null
     */
    public function getResponseStatus(): ?int;

    /**
     * @return string|null
     */
    public function getResponseMessage(): ?string;
}
