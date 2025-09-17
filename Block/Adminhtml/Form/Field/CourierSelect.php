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

class CourierSelect extends Select
{
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

        return '<div style="width: 200px">' . parent::_toHtml() . '</div>';
    }

    private function getSourceOptions(): array
    {
        $results = [];
        foreach (ConfigManagementInterface::METHOD_CODE_METHOD_TITLE_MAP as $code => $title) {
            $results[] = [
                'label' => __($title),
                'value' => $code,
            ];
        }

        return $results;
    }
}
