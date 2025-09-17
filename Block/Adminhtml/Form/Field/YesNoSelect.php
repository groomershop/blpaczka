<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Block\Adminhtml\Form\Field;

use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Template;

class YesNoSelect extends Select
{
    private ConfigManagementInterface $configManagement;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        ConfigManagementInterface $configManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configManagement = $configManagement;
    }

    /**
     * @var int
     */
    const NO = 0;

    /**
     * @var int
     */
    const YES = 1;

    public function setInputName(string $value): self
    {
        return $this->setName($value);
    }

    public function setInputId(string $value): self
    {
        return $this->setId($value);
    }

    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        $script = '';

        if ($this->getData('is_pudo')) {
            $couriersConfigInheritSelector = 'input#carriers_blpaczka_couriers_config_inherit';
            $couriersWithPUDORequired = $this->configManagement->getCouriersWithPUDORequired() ?? [];
            $couriersWithPUDOAvailable = $this->configManagement->getCouriersWithPUDOAvailable() ?? [];

            $courierPUDOSelector = '#carriers_blpaczka_couriers_config select#' . $this->getId();
            $courierCodeSelector = '#carriers_blpaczka_couriers_config select#' . str_replace(
                    CouriersConfig::COURIER_PUDO,
                    CouriersConfig::COURIER_CODE,
                    $this->getId()
                );

            $script = $this
                ->getLayout()
                ->createBlock(Template::class)
                ->setTemplate('BLPaczka_MagentoIntegration::system/config/yes-no-js.phtml')
                ->setData('yesValue', self::YES)
                ->setData('noValue', self::NO)
                ->setData('couriersConfigInheritSelector', $couriersConfigInheritSelector)
                ->setData('couriersWithPUDORequired', json_encode($couriersWithPUDORequired))
                ->setData('couriersWithPUDOAvailable', json_encode($couriersWithPUDOAvailable))
                ->setData('courierPUDOSelector', $courierPUDOSelector)
                ->setData('courierCodeSelector', $courierCodeSelector)
                ->toHtml();
        }

        return parent::_toHtml() . $script;
    }

    private function getSourceOptions(): array
    {
        return [
            ['label' => __('No'), 'value' => self::NO],
            ['label' => __('Yes'), 'value' => self::YES],
        ];
    }
}
