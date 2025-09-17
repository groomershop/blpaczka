<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Controller\Adminhtml\Order;

use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingResponseInterface;
use BLPaczka\MagentoIntegration\Api\ShippingManagementInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;

class CreateBLPaczkaOrder extends Action
{
    const ADMIN_RESOURCE = 'BLPaczka_MagentoIntegration::admin';
    private ShippingManagementInterface $shippingManagement;

    public function __construct(
        Context $context,
        ShippingManagementInterface $shippingManagement
    ) {
        parent::__construct($context);
        $this->shippingManagement = $shippingManagement;
    }

    public function execute(): Redirect
    {
        $postData = $this->_request->getParams();

        $shippingResponse = $this->shippingManagement->createOrder($postData ?? []);

        if ($shippingResponse->getResponseStatus() === ShippingResponseInterface::STATUS_OK) {
            $this->messageManager->addSuccessMessage($shippingResponse->getResponseMessage());
        } else {
            $this->messageManager->addErrorMessage($shippingResponse->getResponseMessage());
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('sales/order/');
    }
}
