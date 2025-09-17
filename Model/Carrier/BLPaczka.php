<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model\Carrier;

use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Tracking\Result\Status;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

class BLPaczka extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = ConfigManagementInterface::SHIPPING_METHOD_CODE;

    /**
     * @var bool
     */
    protected $_isFixed = true;

    private ResultFactory $rateResultFactory;

    private MethodFactory $rateMethodFactory;
    private ConfigManagementInterface $configManagement;
    private StoreRepositoryInterface $storeRepository;
    private StatusFactory $trackStatusFactory;

    public function __construct(
        ScopeConfigInterface      $scopeConfig,
        ErrorFactory              $rateErrorFactory,
        LoggerInterface           $logger,
        ResultFactory             $rateResultFactory,
        MethodFactory             $rateMethodFactory,
        ConfigManagementInterface $configManagement,
        StoreRepositoryInterface  $storeRepository,
        StatusFactory             $trackStatusFactory,
        array                     $data = []
    )
    {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->configManagement = $configManagement;
        $this->storeRepository = $storeRepository;
        $this->trackStatusFactory = $trackStatusFactory;
    }

    public function isTrackingAvailable(): bool
    {
        return true;
    }

    public function isShippingLabelsAvailable(): bool
    {
        return true;
    }

    public function collectRates(RateRequest $request): ?Result
    {
        /** @var StoreInterface|null $store */
        $store = $this->getStoreInstance();

        if (!$this->getConfigFlag('active') || !$store) {
            return null;
        }

        $result = $this->rateResultFactory->create();

        $shippingCode = $this->_code;
        $shippingName = $this->getConfigData('name');
        $shippingCost = (float)$this->getConfigData('shipping_cost');

        $couriersConfig = $this->configManagement->getCouriers($store, true);

        foreach ($couriersConfig as $code => $courierConfig) {
            $method = $this->rateMethodFactory->create();

            $method->setCarrier($shippingCode);
            $method->setCarrierTitle($shippingName);

            $method->setMethod($courierConfig['courier_code'] ?? $shippingCode);
            $method->setMethodTitle($courierConfig['courier_name'] ?? $shippingName);
            $method->setPrice($courierConfig['courier_cost'] ?? $shippingCost);
            $method->setCost($courierConfig['courier_cost'] ?? $shippingCost);

            $result->append($method);
        }

        return $result;
    }

    public function getAllowedMethods(): array
    {
        /** @var StoreInterface|null $store */
        $store = $this->getStoreInstance();

        if (!$this->getConfigFlag('active') || !$store) {
            return [];
        }

        $result = [];

        $shippingCode = $this->_code;
        $shippingName = $this->getConfigData('name');

        $couriersConfig = $this->configManagement->getCouriers($store, true);

        foreach ($couriersConfig as $code => $courierConfig) {
            if ($this->configManagement->isEnabledCourier($code, $store)) {
                $result[$courierConfig['courier_code'] ?? $shippingCode] = $courierConfig['courier_name'] ?? $shippingName;
            }
        }

        return $result;
    }

    private function getStoreInstance(): ?StoreInterface
    {
        /** @var StoreInterface|int|null $storeId */
        $storeId = $this->getStore();

        if ($storeId && $storeId instanceof StoreInterface) {
            return $storeId;
        }

        if (!$storeId) {
            return null;
        }

        try {
            return $this->storeRepository->getById((int)$storeId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    public function getTrackingInfo(string $trackingNumber): Status
    {
        $tracking = $this->trackStatusFactory->create();

        $tracking->setData([
            'carrier' => $this->_code,
            'carrier_title' => $this->getConfigData('title'),
            'tracking' => $trackingNumber,
            'url' => '',
        ]);

        return $tracking;
    }
}
