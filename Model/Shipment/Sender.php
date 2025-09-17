<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model\Shipment;

use BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface;

class Sender implements SenderInterface
{
    private ?string $fullName = null;
    private ?string $company = null;
    private ?string $email = null;
    private ?string $street = null;
    private ?string $houseNumber = null;
    private ?string $apartmentNumber = null;
    private ?string $postCode = null;
    private ?string $city = null;
    private ?string $phoneNumber = null;

    /**
     * @inheritDoc
     */
    public function setFullName(?string $value): SenderInterface
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
    public function setCompany(?string $value): SenderInterface
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
    public function setEmail(?string $value): SenderInterface
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
    public function setStreet(?string $value): SenderInterface
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
    public function setHouseNumber(?string $value): SenderInterface
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
    public function setApartmentNumber(?string $value): SenderInterface
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
    public function setPostCode(?string $value): SenderInterface
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
    public function setCity(?string $value): SenderInterface
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

    /**
     * @inheritDoc
     */
    public function setPhoneNumber(?string $value): SenderInterface
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
}
