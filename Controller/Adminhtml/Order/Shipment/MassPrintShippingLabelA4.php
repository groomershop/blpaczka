<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Controller\Adminhtml\Order\Shipment;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class MassPrintShippingLabelA4 extends MassPrintShippingLabelA4Validation
{
    const IS_A6 = false;

    protected function massAction(AbstractCollection $collection)
    {
        $response = $this->getArrayResult($collection);
        $data = $this->getRequiredData($collection);

        if (!empty($data['labelsContent'])) {
            return $this->fileFactory->create(
                'ShippingLabels.pdf',
                $data['labelsContent'],
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }

        $this->messageManager->addErrorMessage(
            !empty($response['message'])
                ? __($response['message'])
                : __('There are no BLPaczka Shipping Labels related to selected items.')
        );
        return $this->resultRedirectFactory->create()->setPath('sales/order/');
    }
}
