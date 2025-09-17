<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Controller\Adminhtml\Integration;

use BLPaczka\MagentoIntegration\Api\OrderManagementInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;

class DownloadWaybillValidation extends Action
{
    const ADMIN_RESOURCE = 'BLPaczka_MagentoIntegration::admin';
    protected OrderRepositoryInterface $orderRepository;
    protected OrderManagementInterface $orderManagement;
    protected Json $json;
    protected FileFactory $fileFactory;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        OrderManagementInterface $orderManagement,
        Json $json,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->orderManagement = $orderManagement;
        $this->json = $json;
        $this->fileFactory = $fileFactory;
    }

    public function execute()
    {
        $resultArray = $this->getArrayResult();

        return $this->json->setData($resultArray);
    }

    protected function getArrayResult(): array
    {
        $data = $this->getRequiredData();

        if (!$data['orderId']) {
            return [
                'success' => false,
                'message' => __('Order ID is required!')
            ];
        }

        if (!$data['order']) {
            return [
                'success' => false,
                'message' => __('Order #%1 does not exists!', $data['orderId']),
            ];
        }

        if (!$data['shippingLabelContent']) {
            return [
                'success' => false,
                'message' => $data['errorMessage'],
            ];
        }

        return [
            'success' => true,
            'message' => __('The Shipping Label has been downloaded!'),
        ];
    }

    protected function getRequiredData(): array
    {
        $data = $this->_request->getParams();
        $result = [
            'orderId' => $data['order_id'] ?? null,
            'format' => $data['format'] ?? null,
            'errorMessage' => null,
            'order' => null,
            'shippingLabelContent' => null,
        ];

        $isA6 = $result['format'] === OrderManagementInterface::FORMAT_A6;

        if ($result['orderId']) {
            try {
                $result['order'] = $this->orderRepository->get((int) $result['orderId']);

                try {
                    $result['shippingLabelContent'] = $this
                        ->orderManagement
                        ->createShippingLabel($result['order'], $isA6);
                } catch (NoSuchEntityException $e) {
                    $result['errorMessage'] = ($e->getMessage());
                }
            } catch (NoSuchEntityException $e) {
                $result['errorMessage'] = ($e->getMessage());
            }
        } else {
            $result['errorMessage'] = __('Order ID is required!');
        }

        return $result;
    }

}
