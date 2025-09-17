<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Block\Adminhtml\Ui\Component\Grid\Column;

use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use BLPaczka\MagentoIntegration\Api\OrderManagementInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class BLPaczka extends Column
{
    /**
     * @var OrderInterface[]
     */
    private array $orders = [];
    private OrderRepositoryInterface $orderRepository;
    private OrderManagementInterface $orderManagement;
    private LayoutInterface $layout;
    private Repository $assetRepository;

    public function __construct(
        ContextInterface         $context,
        UiComponentFactory       $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        OrderManagementInterface $orderManagement,
        LayoutInterface          $layout,
        Repository               $assetRepository,
        array                    $components = [],
        array                    $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->orderRepository = $orderRepository;
        $this->orderManagement = $orderManagement;
        $this->layout = $layout;
        $this->assetRepository = $assetRepository;
    }

    public function prepareDataSource(array $dataSource): array
    {
        $columnName = $this->getData('name');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $orderId = $item['entity_id'];
                $this->orders[$orderId] = $this->orderRepository->get((int)$orderId);
                $order = $this->orders[$orderId];

                $isValid = $this->orderManagement->isBLPaczkaOrder($order);
                $hasBLPaczkaOrder = $this->orderManagement->hasBLPaczkaOrderData($order);
                $logoUrl = $this->assetRepository->getUrl(ConfigManagementInterface::LOGO_FILE_ID);
                $WaybillLinkA4Validation = $this->orderManagement->getBLPaczkaWaybillLinkA4Validation($order);
                $WaybillLinkA6Validation = $this->orderManagement->getBLPaczkaWaybillLinkA6Validation($order);
                $waybillLinkA4 = $this->orderManagement->getBLPaczkaWaybillLinkA4($order);
                $waybillLinkA6 = $this->orderManagement->getBLPaczkaWaybillLinkA6($order);
                $createOrderUrl = $this
                    ->getContext()
                    ->getUrl('blpaczka/order/createBLPaczkaOrder', ['orderId' => $orderId]);

                /** @var Template $block */
                $block = $this->layout
                    ->createBlock(Template::class)
                    ->setTemplate('BLPaczka_MagentoIntegration::order/grid/column/blpaczka.phtml')
                    ->setData('order_id', $orderId)
                    ->setData('is_valid', $isValid)
                    ->setData('has_blpaczka_order', $hasBLPaczkaOrder)
                    ->setData('logo_url', $logoUrl)
                    ->setData('waybill_link_a4_validation', $WaybillLinkA4Validation)
                    ->setData('waybill_link_a6_validation', $WaybillLinkA6Validation)
                    ->setData('waybill_link_a4', $waybillLinkA4)
                    ->setData('waybill_link_a6', $waybillLinkA6)
                    ->setData('create_order_url', $createOrderUrl);

                $item[$columnName] = $block->toHtml();
            }
        }

        return $dataSource;
    }
}
