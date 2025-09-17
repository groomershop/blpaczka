<?php

namespace BLPaczka\MagentoIntegration\Plugin;

use BLPaczka\MagentoIntegration\Api\OrderManagementInterface;
use Magento\Shipping\Controller\Adminhtml\Shipment\MassPrintShippingLabel as Subject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class PrepareBLPaczkaShippingLabelsBeforeShipmentMassAction
{
    private OrderManagementInterface $orderManagement;
    private Filter $filter;
    private CollectionFactory $collectionFactory;

    public function __construct(
        OrderManagementInterface $orderManagement,
        Filter $filter,
        CollectionFactory $collectionFactory
    )
    {
        $this->orderManagement = $orderManagement;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    public function beforeExecute(Subject $subject): void
    {
        try {
            $this->execute();
        } catch (\Throwable $t) {}
    }

    private function execute(): void
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        foreach ($collection->getItems() ?? [] as $shipment) {
            if ($shipment instanceof Shipment) {
                $order = $shipment->getOrder();
                $isBLPaczka = $this->orderManagement->isBLPaczkaOrder($order);
                $hasBLPaczkaData = $this->orderManagement->hasBLPaczkaOrderData($order);

                if ($isBLPaczka && $hasBLPaczkaData) {
                    if (empty($shipment->getData('shipping_label'))) {
                        try {
                            $blpaczkaWaybillA4 = $this->orderManagement->createShippingLabel($order);
                            $shipment->setData('shipping_label', $blpaczkaWaybillA4);
                        } catch (NoSuchEntityException $e) {
                            continue;
                        }
                    }

                    if (empty($shipment->getData('shipping_label_blpaczka_a6'))) {
                        try {
                            $blpaczkaWaybillA6 = $this->orderManagement->createShippingLabel($order, true);
                            $shipment->setData('shipping_label_blpaczka_a6', $blpaczkaWaybillA6);
                        } catch (NoSuchEntityException $e) {
                            continue;
                        }
                    }
                }
            }
        }
    }
}
