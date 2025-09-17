<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Controller\Adminhtml\Integration;

use Magento\Framework\App\Filesystem\DirectoryList;

class DownloadWaybill extends DownloadWaybillValidation
{
    public function execute()
    {
        $response = $this->getArrayResult();

        if (!$response['success']) {
            $this->messageManager->addErrorMessage($response['message']);

            return $this->resultRedirectFactory->create()->setPath($this->_redirect->getRefererUrl());
        }

        $requiredData = $this->getRequiredData();

        return $this->fileFactory->create(
            (string) __('BLPaczkaShippingLabel%1.pdf', $requiredData['order']->getIncrementId()),
            $requiredData['shippingLabelContent'],
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
