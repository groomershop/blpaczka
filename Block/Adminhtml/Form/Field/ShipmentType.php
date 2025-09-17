<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Block\Adminhtml\Form\Field;

use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface;

class ShipmentType implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $result = [];

        foreach (ShippingDetailsInterface::SHIPMENT_TYPE_PACKAGE_TITLE_MAP as $code => $title) {
            $result[] = [
                'label' => __($title),
                'value' => $code,
            ];
        }

        return $result;
    }
}
