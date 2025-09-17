<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class CouriersConfig extends AbstractFieldArray
{
    const COURIER_CODE = 'courier_code';
    const COURIER_ENABLED = 'courier_enabled';
    const COURIER_PUDO = 'courier_pudo';
    const COURIER_NAME = 'courier_name';
    const COURIER_COST = 'courier_cost';
    private ?CourierSelect $courierSelect = null;
    private ?YesNoSelect $courierEnabledSelect = null;
    private ?YesNoSelect $courierPUDOSelect = null;

    /**
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(self::COURIER_CODE, [
            'label' => __('BLPaczka Courier'),
            'renderer' => $this->getCourierSelectRenderer(),
            'class' => 'required-entry',
        ]);
        $this->addColumn(self::COURIER_ENABLED, [
            'label' => __('Enabled'),
            'renderer' => $this->getCourierEnabledSelectRenderer(),
            'class' => 'required-entry',
        ]);
        $this->addColumn(self::COURIER_PUDO, [
            'label' => __('PUDO'),
            'renderer' => $this->getCourierPUDOSelectRenderer(),
            'class' => 'required-entry',
        ]);
        $this->addColumn(self::COURIER_NAME, [
            'label' => __('Label in the checkout'),
            'class' => 'required-entry',
            'style' => 'width: 240px',
        ]);
        $this->addColumn(self::COURIER_COST, [
            'label' => __('Cost'),
            'class' => 'required-entry validate-number',
            'style' => 'width: 80px',
        ]);
        $this->_addAfter = false;
    }

    /**
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $courierCode = $row->getData(self::COURIER_CODE);
        if ($courierCode !== null) {
            $options['option_' . $this->getCourierSelectRenderer()->calcOptionHash($courierCode)] = 'selected="selected"';
        }

        $courierEnabled = $row->getData(self::COURIER_ENABLED);
        if ($courierEnabled !== null) {
            $options['option_' . $this->getCourierSelectRenderer()->calcOptionHash($courierEnabled)] = 'selected="selected"';
        }

        $courierPUDO = $row->getData(self::COURIER_PUDO);
        if ($courierPUDO !== null) {
            $options['option_' . $this->getCourierPUDOSelectRenderer()->calcOptionHash($courierPUDO)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @throws LocalizedException
     */
    private function getCourierSelectRenderer(): CourierSelect
    {
        if (!$this->courierSelect) {
            /** @var CourierSelect $block */
            $block = $this->getLayout()->createBlock(
                CourierSelect::class,
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => true,
                    ]
                ]
            );
            $this->courierSelect = $block;
        }

        return $this->courierSelect;
    }

    /**
     * @throws LocalizedException
     */
    private function getCourierEnabledSelectRenderer(): YesNoSelect
    {
        if (!$this->courierEnabledSelect) {
            /** @var YesNoSelect $block */
            $block = $this->getLayout()->createBlock(
                YesNoSelect::class,
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => true,
                    ]
                ]
            );
            $this->courierEnabledSelect = $block;
        }

        return $this->courierEnabledSelect;
    }

    /**
     * @throws LocalizedException
     */
    private function getCourierPUDOSelectRenderer(): YesNoSelect
    {
        if (!$this->courierPUDOSelect) {
            /** @var YesNoSelect $block */
            $block = $this->getLayout()->createBlock(
                YesNoSelect::class,
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => true,
                        'is_pudo' => true,
                    ]
                ]
            );
            $this->courierPUDOSelect = $block;
        }

        return $this->courierPUDOSelect;
    }
}
