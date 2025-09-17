<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Controller\Adminhtml\Order\Shipment;

use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingResponseInterface;
use BLPaczka\MagentoIntegration\Api\OrderManagementInterface;
use BLPaczka\MagentoIntegration\Api\ShippingManagementInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassCreateBLPaczkaOrder extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'BLPaczka_MagentoIntegration::admin';

    private Filter $filter;
    private CollectionFactory $collectionFactory;
    private ShippingManagementInterface $shippingManagement;
    private OrderManagementInterface $orderManagement;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ShippingManagementInterface $shippingManagement,
        OrderManagementInterface $orderManagement
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->shippingManagement = $shippingManagement;
        $this->orderManagement = $orderManagement;
    }

    public function execute(): Redirect
    {
        try {
            /** @var AbstractCollection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('sales/order/');
    }


    protected function massAction(AbstractCollection $collection): void
    {
        $data = $this->_request->getParams();
        $hasValidOrder = false;

        if ($collection->getSize()) {
            /** @var Order $order */
            foreach ($collection as $order) {
                $isBLPaczka = $this->orderManagement->isBLPaczkaOrder($order);

                if ($isBLPaczka) {
                    $hasValidOrder = true;

                    $data[ShippingManagementInterface::BLPACZKA_DATA_KEY] = $this
                        ->shippingManagement
                        ->getDataForCreateOrderForm($order);
                    $data['orderId'] = $order->getId();

                    $response = $this->shippingManagement->createOrder($data);

                    if ($response->getResponseStatus() === ShippingResponseInterface::STATUS_OK) {
                        $this->messageManager->addSuccessMessage(
                            __('Order #%1 - %2. You can now download your shipping label.', $order->getIncrementId(), $response->getResponseMessage())
                        );
                    } else {
                        $this->messageManager->addErrorMessage(
                            __('Order #%1 - %2', $order->getIncrementId(), $response->getResponseMessage())
                        );
                    }
                }
            }
        }

        if (!$hasValidOrder) {
            $this->messageManager->addErrorMessage(__('There are no BLPaczka Orders related to selected items.'));
        }
    }
}
