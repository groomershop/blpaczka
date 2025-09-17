<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Api\Data\Shipment;

interface ShippingDetailsInterface
{
    const FOREIGN_TYPE_LOCAL = 'local';
    const FOREIGN_TYPE_FOREIGN = 'foreign';
    const FOREIGN_TYPES = [
        self::FOREIGN_TYPE_LOCAL => 'Local',
        self::FOREIGN_TYPE_FOREIGN => 'Foreign',
    ];
    const FOREIGN_TYPE_COUNTRY_CODE_MAP = [
        'pl' => self::FOREIGN_TYPE_LOCAL,
    ];

    const SHIPMENT_TYPE_PACKAGE = 'package';
    const SHIPMENT_TYPE_PALLET = 'pallet';
    const SHIPMENT_TYPE_ENVELOPE = 'envelope';
    const SHIPMENT_TYPE_NOT_STANDARD = 'not_standard';
    const SHIPMENT_TYPE_PACKAGE_TITLE_MAP = [
        self::SHIPMENT_TYPE_PACKAGE => 'Package',
        self::SHIPMENT_TYPE_PALLET => 'Pallet',
        self::SHIPMENT_TYPE_ENVELOPE => 'Envelope',
        self::SHIPMENT_TYPE_NOT_STANDARD => 'Not Standard',
    ];

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentSelectedShippingMethodDescription(?string $value): ShippingDetailsInterface;

    /**
     * @return string|null
     */
    public function getShipmentSelectedShippingMethodDescription(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentSelectedShippingMethodCode(?string $value): ShippingDetailsInterface;

    /**
     * @return string|null
     */
    public function getShipmentSelectedShippingMethodCode(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentCourierCode(?string $value): ShippingDetailsInterface;

    /**
     * @return string|null
     */
    public function getShipmentCourierCode(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentCountryCode(?string $value): ShippingDetailsInterface;

    /**
     * @return string|null
     */
    public function getShipmentCountryCode(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentType(?string $value): ShippingDetailsInterface;

    /**
     * @return string|null
     */
    public function getShipmentType(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentWeight(?string $value): ShippingDetailsInterface;

    /**
     * @return float|null
     */
    public function getShipmentWeight(): ?float;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentLength(?string $value): ShippingDetailsInterface;

    /**
     * @return float|null
     */
    public function getShipmentLength(): ?float;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentWidth(?string $value): ShippingDetailsInterface;

    /**
     * @return float|null
     */
    public function getShipmentWidth(): ?float;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentHeight(?string $value): ShippingDetailsInterface;

    /**
     * @return float|null
     */
    public function getShipmentHeight(): ?float;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentContent(?string $value): ShippingDetailsInterface;

    /**
     * @return string|null
     */
    public function getShipmentContent(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentInsuranceTotal(?string $value): ShippingDetailsInterface;

    /**
     * @return float|null
     */
    public function getShipmentInsuranceTotal(): ?float;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentCashOnDeliveryTotal(?string $value): ShippingDetailsInterface;

    /**
     * @return float|null
     */
    public function getShipmentCashOnDeliveryTotal(): ?float;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentSortable(?string $value): ShippingDetailsInterface;

    /**
     * @return int|null
     */
    public function getShipmentSortable(): ?int;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentWithoutCourierPickUp(?string $value): ShippingDetailsInterface;

    /**
     * @return int|null
     */
    public function getShipmentWithoutCourierPickUp(): ?int;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentCourierArrivalDay(?string $value): ShippingDetailsInterface;

    /**
     * @return string|null
     */
    public function getShipmentCourierArrivalDay(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentCourierArrivalTimeFrom(?string $value): ShippingDetailsInterface;

    /**
     * @return string|null
     */
    public function getShipmentCourierArrivalTimeFrom(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentCourierArrivalTimeTo(?string $value): ShippingDetailsInterface;

    /**
     * @return string|null
     */
    public function getShipmentCourierArrivalTimeTo(): ?string;

    /**
     * @param string|null $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
     */
    public function setShipmentForeign(?string $value): ShippingDetailsInterface;

    /**
     * @return string|null
     */
    public function getShipmentForeign(): ?string;
}
