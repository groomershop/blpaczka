<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Api\Data\Shipment;

interface SenderInterface
{
    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface
     */
    public function setFullName(?string $value): SenderInterface;

    /**
     * @return string|null
     */
    public function getFullName(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface
     */
    public function setCompany(?string $value): SenderInterface;

    /**
     * @return string|null
     */
    public function getCompany(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface
     */
    public function setEmail(?string $value): SenderInterface;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface
     */
    public function setStreet(?string $value): SenderInterface;

    /**
     * @return string|null
     */
    public function getStreet(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface
     */
    public function setHouseNumber(?string $value): SenderInterface;

    /**
     * @return string|null
     */
    public function getHouseNumber(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface
     */
    public function setApartmentNumber(?string $value): SenderInterface;

    /**
     * @return string|null
     */
    public function getApartmentNumber(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface
     */
    public function setPostCode(?string $value): SenderInterface;

    /**
     * @return string|null
     */
    public function getPostCode(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface
     */
    public function setCity(?string $value): SenderInterface;

    /**
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface
     */
    public function setPhoneNumber(?string $value): SenderInterface;

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string;
}
