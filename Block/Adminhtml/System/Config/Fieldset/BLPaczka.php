<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Block\Adminhtml\System\Config\Fieldset;

use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class BLPaczka extends Fieldset
{
    private Repository $assetRepository;

    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Repository $assetRepository,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data, $secureRenderer);
        $this->assetRepository = $assetRepository;
    }

    protected function _getHeaderCommentHtml($element): string
    {
        $image = $this->assetRepository->getUrl(ConfigManagementInterface::LOGO_FILE_ID);
        $alt = (string) __('BLPaczka');
        $imageHtml = "<img src=\"$image\" alt=\"$alt\"/>";

        return $imageHtml . parent::_getHeaderCommentHtml($element);
    }
}
