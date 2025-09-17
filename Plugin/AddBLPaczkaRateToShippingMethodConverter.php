<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Plugin;

use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use BLPaczka\MagentoIntegration\Api\Data\BLPaczkaRateInterface;
use BLPaczka\MagentoIntegration\Api\Data\BLPaczkaRateInterfaceFactory;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Store\Model\StoreManagerInterface;

class AddBLPaczkaRateToShippingMethodConverter
{
    private BLPaczkaRateInterfaceFactory $BLPaczkaRateFactory;
    private ConfigManagementInterface $configManagement;
    private StoreManagerInterface $storeManager;

    public function __construct(
        BLPaczkaRateInterfaceFactory $BLPaczkaRateFactory,
        ConfigManagementInterface $configManagement,
        StoreManagerInterface $storeManager
    ) {
        $this->BLPaczkaRateFactory = $BLPaczkaRateFactory;
        $this->configManagement = $configManagement;
        $this->storeManager = $storeManager;
    }

    public function afterModelToDataObject(
        ShippingMethodConverter $subject,
        $result
    ) {
        if ($result instanceof ShippingMethodInterface && $result->getCarrierCode() === ConfigManagementInterface::SHIPPING_METHOD_CODE) {
            $blPaczkaRate = $this->BLPaczkaRateFactory->create();
            $store = $this->storeManager->getStore();

            $apiUrl = $this->configManagement->getApiUrl($store);
            $isPUDO = $this->configManagement->isPUDOCourier($result->getMethodCode(), $store);
            $isPUDORequired = $this->configManagement->isPUDORequiredCourier($result->getMethodCode(), $store);
            $isPUDOAvailable = $this->configManagement->isPUDOAvailableCourier($result->getMethodCode(), $store);
            $pudoMapUrl = $this->configManagement->getMapUrl($result->getMethodCode(), null, $store);
            $pudoMapOriginUrl = preg_replace('/\/pudo-map.*?$/', '', $pudoMapUrl);

            if ($isPUDORequired) {
                $isPUDO = true;
            }

            if (!$isPUDOAvailable) {
                $isPUDO = false;
            }

            $blPaczkaRate->setMapOriginUrl(is_string($pudoMapOriginUrl) ? $pudoMapOriginUrl : $apiUrl);
            $blPaczkaRate->setIsPudo($isPUDO);
            $blPaczkaRate->setPudoMapUrl($pudoMapUrl);

            $result->getExtensionAttributes()->setBlpaczkaRate($blPaczkaRate);
        }

        return $result;
    }
}
