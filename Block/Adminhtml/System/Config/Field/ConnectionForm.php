<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ConnectionForm extends Field
{
    protected $_template = 'BLPaczka_MagentoIntegration::system/config/connection-form.phtml';

    public function getLinkUrl(): string
    {
        return $this->getUrl('blpaczka/integration/connect');
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->toHtml();
    }

    public function getConfigStoreId(): ?int
    {
        $store = $this->_request->getParam('store');
        return $store ? (int) $store : null;
    }

    public function getConfigWebsiteId(): ?int
    {
        $website = $this->_request->getParam('website');
        return $website ? (int) $website : null;
    }
}
