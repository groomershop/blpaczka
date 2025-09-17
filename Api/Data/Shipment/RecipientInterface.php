<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Api\Data\Shipment;

interface RecipientInterface
{
    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setFullName(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getFullName(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setCompany(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getCompany(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setPhoneNumber(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setPointNumber(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getPointNumber(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setEmail(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setFullAddress(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getFullAddress(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setStreet(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getStreet(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setHouseNumber(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getHouseNumber(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setApartmentNumber(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getApartmentNumber(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setPostCode(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getPostCode(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface
     */
    public function setCity(?string $value): RecipientInterface;

    /**
     * @return string|null
     */
    public function getCity(): ?string;
}
