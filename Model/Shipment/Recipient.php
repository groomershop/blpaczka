<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model\Shipment;

use BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface;

class Recipient implements RecipientInterface
{
    private ?string $fullName = null;
    private ?string $company = null;
    private ?string $phoneNumber = null;
    private ?string $email = null;
    private ?string $pointNumber = null;
    private ?string $fullAddress = null;
    private ?string $street = null;
    private ?string $houseNumber = null;
    private ?string $apartmentNumber = null;
    private ?string $postCode = null;
    private ?string $city = null;

    /**
     * @inheritDoc
     */
    public function setFullName(?string $value): RecipientInterface
    {
        $this->fullName = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @inheritDoc
     */
    public function setCompany(?string $value): RecipientInterface
    {
        $this->company = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @inheritDoc
     */
    public function setPhoneNumber(?string $value): RecipientInterface
    {
        $this->phoneNumber = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @inheritDoc
     */
    public function setPointNumber(?string $value): RecipientInterface
    {
        $this->pointNumber = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPointNumber(): ?string
    {
        return $this->pointNumber;
    }

    /**
     * @inheritDoc
     */
    public function setEmail(?string $value): RecipientInterface
    {
        $this->email = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function setFullAddress(?string $value): RecipientInterface
    {
        $this->fullAddress = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFullAddress(): ?string
    {
        return $this->fullAddress;
    }

    /**
     * @inheritDoc
     */
    public function setStreet(?string $value): RecipientInterface
    {
        $this->street = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @inheritDoc
     */
    public function setHouseNumber(?string $value): RecipientInterface
    {
        $this->houseNumber = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    /**
     * @inheritDoc
     */
    public function setApartmentNumber(?string $value): RecipientInterface
    {
        $this->apartmentNumber = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getApartmentNumber(): ?string
    {
        return $this->apartmentNumber;
    }

    /**
     * @inheritDoc
     */
    public function setPostCode(?string $value): RecipientInterface
    {
        $this->postCode = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    /**
     * @inheritDoc
     */
    public function setCity(?string $value): RecipientInterface
    {
        $this->city = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCity(): ?string
    {
        return $this->city;
    }
}
