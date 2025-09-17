<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Controller\Adminhtml\Order\Shipment;

use BLPaczka\MagentoIntegration\Api\OrderManagementInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Shipping\Controller\Adminhtml\Order\Shipment\MassPrintShippingLabel;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Ui\Component\MassAction\Filter;

class MassPrintShippingLabelA4Validation extends MassPrintShippingLabel
{
    const IS_A6 = false;
    const ADMIN_RESOURCE = 'BLPaczka_MagentoIntegration::admin';

    private OrderManagementInterface $orderManagement;
    private Json $json;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FileFactory $fileFactory,
        LabelGenerator $labelGenerator,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        OrderManagementInterface $orderManagement,
        Json $json
    ) {
        parent::__construct($context, $filter, $collectionFactory, $fileFactory, $labelGenerator, $shipmentCollectionFactory);
        $this->orderManagement = $orderManagement;
        $this->json = $json;
    }

    protected function massAction(AbstractCollection $collection)
    {
        $resultArray = $this->getArrayResult($collection);

        return $this->json->setData($resultArray);
    }

    protected function getArrayResult(AbstractCollection $collection): array
    {
        $data = $this->getRequiredData($collection);

        if (!$data['labelsContent']) {
            return [
                'success' => false,
                'message' => __('There are no BLPaczka Shipping Labels related to selected items.')
            ];
        }

        return [
            'success' => true,
            'message' => __('Shipping Labels have been downloaded!'),
        ];
    }

    public function getRequiredData(AbstractCollection $collection): array
    {
        $data = ['labelsContent' => null];

        if ($collection->getSize()) {
            /** @var Order $order */
            foreach ($collection as $order) {
                if ($this->orderManagement->isBLPaczkaOrder($order)) {
                    try {
                        $labelContent = $this->orderManagement->createShippingLabel($order, static::IS_A6);
                        if ($labelContent) {
                            $labelsContent[] = $labelContent;
                        }
                    } catch (NoSuchEntityException $e) {
                        continue;
                    }
                }
            }
        }

        if (!empty($labelsContent)) {
            $data['labelsContent'] = $this->labelGenerator->combineLabelsPdf($labelsContent)->render();
        }

        return $data;
    }
}
