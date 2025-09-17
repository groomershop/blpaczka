<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model\Shipment;

use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class ShippingDetails implements \BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface
{
    private ?string $shipmentSelectedShippingMethodDescription = null;
    private ?string $shipmentSelectedShippingMethodCode = null;
    private ?string $shipmentCourierCode = null;
    private ?string $shipmentCountryCode = null;
    private ?string $shipmentType = null;
    private ?float $shipmentWeight = null;
    private ?float $shipmentLength = null;
    private ?float $shipmentWidth = null;
    private ?float $shipmentHeight = null;
    private ?string $shipmentContent = null;
    private ?float $shipmentInsuranceTotal = null;
    private ?float $shipmentCashOnDeliveryTotal = null;
    private ?int $shipmentSortable = null;
    private ?int $shipmentWithoutCourierPickUp = null;
    private ?\DateTimeInterface $shipmentCourierArrivalDay = null;
    private ?\DateTimeInterface $shipmentCourierArrivalTimeFrom = null;
    private ?\DateTimeInterface $shipmentCourierArrivalTimeTo = null;
    private ?string $shipmentForeign = null;
    private TimezoneInterface $timezone;

    public function __construct(
        TimezoneInterface $timezone
    )
    {
        $this->timezone = $timezone;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentSelectedShippingMethodDescription(?string $value): ShippingDetailsInterface
    {
        $this->shipmentSelectedShippingMethodDescription = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentSelectedShippingMethodDescription(): ?string
    {
        return $this->shipmentSelectedShippingMethodDescription;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentSelectedShippingMethodCode(?string $value): ShippingDetailsInterface
    {
        $this->shipmentSelectedShippingMethodCode = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentSelectedShippingMethodCode(): ?string
    {
        return $this->shipmentSelectedShippingMethodCode;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentCourierCode(?string $value): ShippingDetailsInterface
    {
        $this->shipmentCourierCode = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentCourierCode(): ?string
    {
        return $this->shipmentCourierCode;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentCountryCode(?string $value): ShippingDetailsInterface
    {
        $this->shipmentCountryCode = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentCountryCode(): ?string
    {
        return $this->shipmentCountryCode;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentType(?string $value): ShippingDetailsInterface
    {
        $this->shipmentType = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentType(): ?string
    {
        return $this->shipmentType;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentWeight(?string $value): ShippingDetailsInterface
    {
        $this->shipmentWeight = $value !== null ? (float)$value : null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentWeight(): ?float
    {
        return $this->shipmentWeight;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentLength(?string $value): ShippingDetailsInterface
    {
        $this->shipmentLength = $value !== null ? (float)$value : null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentLength(): ?float
    {
        return $this->shipmentLength;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentWidth(?string $value): ShippingDetailsInterface
    {
        $this->shipmentWidth = $value !== null ? (float)$value : null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentWidth(): ?float
    {
        return $this->shipmentWidth;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentHeight(?string $value): ShippingDetailsInterface
    {
        $this->shipmentHeight = $value !== null ? (float)$value : null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentHeight(): ?float
    {
        return $this->shipmentHeight;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentContent(?string $value): ShippingDetailsInterface
    {
        $this->shipmentContent = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentContent(): ?string
    {
        return $this->shipmentContent;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentInsuranceTotal(?string $value): ShippingDetailsInterface
    {
        $this->shipmentInsuranceTotal = $value !== null ? (float)$value : null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentInsuranceTotal(): ?float
    {
        return $this->shipmentInsuranceTotal;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentCashOnDeliveryTotal(?string $value): ShippingDetailsInterface
    {
        $this->shipmentCashOnDeliveryTotal = $value !== null ? (float)$value : null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentCashOnDeliveryTotal(): ?float
    {
        return $this->shipmentCashOnDeliveryTotal;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentSortable(?string $value): ShippingDetailsInterface
    {
        $this->shipmentSortable = $value !== null ? (int)$value : null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentSortable(): ?int
    {
        return $this->shipmentSortable;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentWithoutCourierPickUp(?string $value): ShippingDetailsInterface
    {
        $this->shipmentWithoutCourierPickUp = $value !== null ? (int)$value : null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentWithoutCourierPickUp(): ?int
    {
        return $this->shipmentWithoutCourierPickUp;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentCourierArrivalDay(?string $value): ShippingDetailsInterface
    {

        if ($value) {
            $days = (int)$value;
            try {
                $this->shipmentCourierArrivalDay = $this->timezone->date();
                $this->shipmentCourierArrivalDay->modify("+{$days} day");
            } catch (\Exception $e) {
                $this->shipmentCourierArrivalDay = null;
            }
        } else {
            $this->shipmentCourierArrivalDay = null;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentCourierArrivalDay(): ?string
    {
        return $this->shipmentCourierArrivalDay ? $this->shipmentCourierArrivalDay->format('Y-m-d') : null;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentCourierArrivalTimeFrom(?string $value): ShippingDetailsInterface
    {
        if ($value) {
            $time = explode(':', $value);
            $h = $time[0] ?? 0;
            $m = $time[1] ?? 0;
            $s = $time[2] ?? 0;
            try {
                $this->shipmentCourierArrivalTimeFrom = $this->timezone->date();
                $this->shipmentCourierArrivalTimeFrom->setTime((int)$h, (int)$m, (int)$s);
            } catch (\Exception $e) {
                $this->shipmentCourierArrivalTimeFrom = null;
            }
        } else {
            $this->shipmentCourierArrivalTimeFrom = null;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentCourierArrivalTimeFrom(): ?string
    {
        return $this->shipmentCourierArrivalTimeFrom ? $this->shipmentCourierArrivalTimeFrom->format('H:i:s') : null;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentCourierArrivalTimeTo(?string $value): ShippingDetailsInterface
    {
        if ($value) {
            $time = explode(':', $value);
            $h = $time[0] ?? 0;
            $m = $time[1] ?? 0;
            $s = $time[2] ?? 0;
            try {
                $this->shipmentCourierArrivalTimeTo = $this->timezone->date();
                $this->shipmentCourierArrivalTimeTo->setTime((int)$h, (int)$m, (int)$s);
            } catch (\Exception $e) {
                $this->shipmentCourierArrivalTimeTo = null;
            }
        } else {
            $this->shipmentCourierArrivalTimeTo = null;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentCourierArrivalTimeTo(): ?string
    {
        return $this->shipmentCourierArrivalTimeTo ? $this->shipmentCourierArrivalTimeTo->format('H:i:s') : null;
    }

    /**
     * @inheritDoc
     */
    public function setShipmentForeign(?string $value): ShippingDetailsInterface
    {
        $this->shipmentForeign = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShipmentForeign(): ?string
    {
        return $this->shipmentForeign;
    }
}
