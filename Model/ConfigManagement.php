<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model;

use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use BLPaczka\MagentoIntegration\Model\Config\Source\ModeSelect;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigManagement implements ConfigManagementInterface
{
    private ScopeConfigInterface $scopeConfig;
    private EncryptorInterface $encryptor;

    public function __construct(
        ScopeConfigInterface    $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * @inheritDoc
     */
    public function getApiUrl(?StoreInterface $store = null): string
    {
        if ($this->getIsSandboxMode($store)) {
            return self::SANDBOX_API_URL;
        } else {
            return self::PRODUCTION_API_URL;
        }
    }

    /**
     * @inheritDoc
     */
    public function getIsSandboxMode(?StoreInterface $store = null): bool
    {
        $value = $this->getConfigValue($store, self::CONFIG_PATH_MODE);

        return $value !== ModeSelect::PRODUCTION;
    }

    /**
     * @inheritDoc
     */
    public function getEmail(?StoreInterface $store = null): string
    {
        if ($this->getIsSandboxMode($store)) {
            $path = self::CONFIG_PATH_SANDBOX_EMAIL;
        } else {
            $path = self::CONFIG_PATH_PRODUCTION_EMAIL;
        }

        return (string) $this->getConfigValue($store, $path);
    }

    /**
     * @inheritDoc
     */
    public function getApiKey(?StoreInterface $store = null): string
    {
        if ($this->getIsSandboxMode($store)) {
            $path = self::CONFIG_PATH_SANDBOX_API_KEY;
        } else {
            $path = self::CONFIG_PATH_PRODUCTION_API_KEY;
        }

        $apiKey = (string) $this->getConfigValue($store, $path);

        try {
            return $this->encryptor->decrypt($apiKey);
        } catch (\Throwable $t) {
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function getCouriers(?StoreInterface $store = null, bool $enabledFilter = false): array
    {
        $result = $this->getConfigValue($store, self::CONFIG_PATH_COURIERS_CONFIG) ?? '[]';

        try {
            $result = is_array($result) ? $result : json_decode((string) $result, true);
        } catch (\Throwable $t) {
            return [];
        }

        $couriersConfig = [];
        foreach ($result as $courierConfig) {
            if ($enabledFilter && !$courierConfig['courier_enabled']) {
                continue;
            }

            $code = $this->addSuffixFromMethodCode($courierConfig['courier_code'], hash('sha256', $courierConfig['courier_name']));

            $courierConfig['courier_code'] = $code;
            $couriersConfig[$code] = $courierConfig;
        }

        return $couriersConfig;
    }

    /**
     * @inheritDoc
     */
    public function getCouriersWithPUDOAvailable(): array
    {
        return self::PUDO_AVAILABLE_COURIERS;
    }

    /**
     * @inheritDoc
     */
    public function getCouriersWithPUDORequired(): array
    {
        return self::PUDO_REQUIRED_COURIERS;
    }

    /**
     * @inheritDoc
     */
    public function isEnabledCourier(string $methodCode, ?StoreInterface $store = null): bool
    {
        $couriers = $this->getCouriers($store);
        $courier = $couriers[$methodCode] ?? null;

        if (!$courier) {
            return false;
        }

        $value = $courier['courier_enabled'] ?? false;

        return (bool) $value;
    }

    /**
     * @inheritDoc
     */
    public function isPUDOCourier(string $methodCode, ?StoreInterface $store = null): bool
    {
        $couriers = $this->getCouriers($store);
        $courier = $couriers[$methodCode] ?? null;

        if (!$courier) {
            return false;
        }

        $value = $courier['courier_pudo'] ?? false;

        return (bool) $value;
    }

    /**
     * @inheritDoc
     */
    public function isPUDORequiredCourier(string $methodCode, ?StoreInterface $store = null): bool
    {
        $methodCode = $this->removeSuffixFromMethodCode($methodCode);
        $pudoRequiredCouriers = $this->getCouriersWithPUDORequired();

        return in_array($methodCode, $pudoRequiredCouriers);
    }

    /**
     * @inheritDoc
     */
    public function isPUDOAvailableCourier(string $methodCode, ?StoreInterface $store = null): bool
    {
        $methodCode = $this->removeSuffixFromMethodCode($methodCode);
        $pudoAvailableCouriers = $this->getCouriersWithPUDOAvailable();

        return in_array($methodCode, $pudoAvailableCouriers);
    }

    /**
     * @inheritDoc
     */
    public function getMapUrl(?string $methodCode, ?string $postCode, ?StoreInterface $store = null): string
    {
        $url = self::API_URL_MAP;

        if ($methodCode) {
            $methodCode = $this->removeSuffixFromMethodCode($methodCode);
            $blpaczkaMethodCode = str_replace(
                array_keys(self::METHOD_CODE_BLPACZKA_METHOD_CODE_MAP),
                array_values(self::METHOD_CODE_BLPACZKA_METHOD_CODE_MAP),
                $methodCode
            );

            $url = str_replace('{blpaczkaMethodCode}', $blpaczkaMethodCode, $url);
        }

        if ($postCode) {
            $url = str_replace('{postCode}', $blpaczkaMethodCode, $url);
        }

        return str_replace('sandbox.blpaczka.com', 'sandbox2409.blpaczka.com', $this->getApiUrl($store)) . $url;
    }

    /**
     * @inheritDoc
     */
    public function addSuffixFromMethodCode(string $methodCode, string $methodTitle): string
    {
        return $methodCode . '__' . hash('sha256', $methodTitle);
    }

    /**
     * @inheritDoc
     */
    public function removeSuffixFromMethodCode(string $methodCode): string
    {
        $array = explode('__', $methodCode);

        if (!is_array($array)) {
            return $methodCode;
        }

        return $array[0] ?? $methodCode;
    }

    public function getConfigValue(?StoreInterface $store, string $path): ?string
    {
        if ($store) {
            $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, (int) $store->getId());
        } else {
            $value = $this->scopeConfig->getValue($path);
        }

        return $value !== null ? (string) $value : null;
    }
}
