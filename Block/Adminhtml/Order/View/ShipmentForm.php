<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Block\Adminhtml\Order\View;

use BLPaczka\MagentoIntegration\Api\ApiServiceInterface;
use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\PaymentInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface;
use BLPaczka\MagentoIntegration\Api\OrderManagementInterface;
use BLPaczka\MagentoIntegration\Api\ShippingManagementInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Asset\Repository;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use Magento\Sales\Helper\Admin;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Tax\Helper\Data as TaxHelper;

class ShipmentForm extends AbstractOrder
{
    private ShippingManagementInterface $shippingManagement;
    private ApiServiceInterface $apiService;
    private Repository $assetRepository;
    private OrderManagementInterface $orderManagement;
    private ConfigManagementInterface $configManagement;

    public function __construct(
        Context                     $context,
        Registry                    $registry,
        Admin                       $adminHelper,
        ShippingManagementInterface $shippingManagement,
        ApiServiceInterface         $apiService,
        Repository                  $assetRepository,
        OrderManagementInterface    $orderManagement,
        ConfigManagementInterface   $configManagement,
        array                       $data = [],
        ?ShippingHelper             $shippingHelper = null,
        ?TaxHelper                  $taxHelper = null
    )
    {
        parent::__construct($context, $registry, $adminHelper, $data, $shippingHelper, $taxHelper);
        $this->shippingManagement = $shippingManagement;
        $this->apiService = $apiService;
        $this->assetRepository = $assetRepository;
        $this->orderManagement = $orderManagement;
        $this->configManagement = $configManagement;
    }

    public function isValid(): bool
    {
        return $this->orderManagement->isBLPaczkaOrder($this->getOrder());
    }

    public function getLogoUrl(): string
    {
        return $this->assetRepository->getUrl(ConfigManagementInterface::LOGO_FILE_ID);
    }

    public function getDataJson(): string
    {
        $shippingMethods = $this->shippingManagement->getBLPaczkaShippingMethods($this->getOrder()->getStore());

        $data = [
            'logoUrl' => $this->getLogoUrl(),
            'mapUrl' => $this->configManagement->getMapUrl(null, null, $this->getOrder()->getStore()),
            'mapOriginUrl' => $this->configManagement->getApiUrl($this->getOrder()->getStore()),
            'isAuthorized' => $this->apiService->isAuthorized($this->getOrder()->getStore()),
            'createOrderUrl' => $this->_urlBuilder->getUrl('blpaczka/integration/createOrder'),
            'getValuationUrl' => $this->_urlBuilder->getUrl('blpaczka/integration/getValuation'),
            'cancelOrderUrl' => $this->_urlBuilder->getUrl('blpaczka/integration/cancelOrder'),
            'shipmentTypes' => $this->getShipmentTypes(),
            'foreignTypes' => $this->getForeignTypes(),
            'paymentTypes' => $this->getPaymentTypes(),
            'shippingMethods' => $shippingMethods,
            'couriersWithPUDOAvailable' => [ShippingManagementInterface::SHIPPING_METHOD_CODE_ANY_COURIER] + $this->configManagement->getCouriersWithPUDOAvailable(),
            'couriersWithPUDORequired' => $this->configManagement->getCouriersWithPUDORequired(),
            'orderId' => $this->getOrder()->getId(),
            'blpaczkaOrderedCartItem' => $this->orderManagement->getBLPaczkaCartOrderItem($this->getOrder()),
            'blpaczkaOrderedItem' => $this->orderManagement->getBLPaczkaOrderItem($this->getOrder()),
            'blpaczkaOrderedItemShippingLabelA4Link' => $this->orderManagement->getBLPaczkaWaybillLinkA4($this->getOrder()),
            'blpaczkaOrderedItemShippingLabelA4LinkValidation' => $this->orderManagement->getBLPaczkaWaybillLinkA4Validation($this->getOrder()),
            'blpaczkaOrderedItemShippingLabelA6Link' => $this->orderManagement->getBLPaczkaWaybillLinkA6($this->getOrder()),
            'blpaczkaOrderedItemShippingLabelA6LinkValidation' => $this->orderManagement->getBLPaczkaWaybillLinkA6Validation($this->getOrder()),
            'shippingMethodCodeAnyCourier' => ShippingManagementInterface::SHIPPING_METHOD_CODE_ANY_COURIER,
            'fieldSets' => $this->shippingManagement->getDataForCreateOrderForm($this->getOrder()),
        ];

        return json_encode($data);
    }

    private function getShipmentTypes(): array
    {
        $types = [];
        foreach (ShippingDetailsInterface::SHIPMENT_TYPE_PACKAGE_TITLE_MAP as $code => $title) {
            $types[] = [
                'code' => $code,
                'title' => __($title),
            ];
        }

        return $types;
    }

    private function getForeignTypes(): array
    {
        $types = [];
        foreach (ShippingDetailsInterface::FOREIGN_TYPES as $code => $title) {
            $types[] = [
                'code' => $code,
                'title' => __($title),
            ];
        }

        return $types;
    }

    private function getPaymentTypes(): array
    {
        $types = [];
        foreach (PaymentInterface::PAYMENT_TYPES as $code => $title) {
            $types[] = [
                'code' => $code,
                'title' => __($title),
            ];
        }

        return $types;
    }
}
