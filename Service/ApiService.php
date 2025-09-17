<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Service;

use BLPaczka\MagentoIntegration\Api\ApiServiceInterface;
use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface;
use BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterfaceFactory;
use BLPaczka\MagentoIntegration\Api\OrderManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\StoreInterface;

class ApiService implements ApiServiceInterface
{
    private ConfigManagementInterface $configManagement;
    private ApiResponseInterfaceFactory $apiResponseFactory;

    public function __construct(
        ConfigManagementInterface   $configManagement,
        ApiResponseInterfaceFactory $apiResponseFactory
    )
    {
        $this->configManagement = $configManagement;
        $this->apiResponseFactory = $apiResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function getProfile(?StoreInterface $store = null): ApiResponseInterface
    {
        try {
            $jsonResponse = $this->postRequest(ConfigManagementInterface::API_URL_GET_PROFILE, [], $store);

            $status = ApiResponseInterface::STATUS_NOK;
            $message = !empty($jsonResponse['message']) ? $jsonResponse['message'] : null;

            if (isset($jsonResponse['success']) && !!$jsonResponse['success']) {
                $status = ApiResponseInterface::STATUS_OK;
                $message = $message ?? __('You are connected with BLPaczka!');
            }

            $message ??= __('You are not connected with BLPaczka!');

            return $this->apiResponseFactory
                ->create()
                ->setStatus($status)
                ->setMessage(__($message));
        } catch (LocalizedException $e) {
            return $this->apiResponseFactory
                ->create()
                ->setStatus(ApiResponseInterface::STATUS_NOK)
                ->setMessage(__($e->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function getValuation(array $data, ?StoreInterface $store = null): ApiResponseInterface
    {
        try {
            $jsonResponse = $this->postRequest(ConfigManagementInterface::API_URL_GET_VALUATION, $data, $store);

            $success = (bool) ($jsonResponse['success'] ?? false);
            $validationErrors = $jsonResponse['data'] ?? [];
            $validationErrors = $validationErrors['validationErrors'] ?? [];
            $validationErrors = $this->multiDimensionalArrayToString($validationErrors);

            $couriersData = $jsonResponse['data'] ?? [];
            $couriersData = $couriersData['results'] ?? [];

            $message = $jsonResponse['message'] ?? '';
            if (!empty($validationErrors)) {
                $message .= '; ' . $validationErrors;
                $message = trim($message, '; ');
            }

            return $this->apiResponseFactory
                ->create()
                ->setStatus($success ? ApiResponseInterface::STATUS_OK : ApiResponseInterface::STATUS_NOK)
                ->setMessage(__($message))
                ->setData(empty($couriersData) ? null : $couriersData);
        } catch (LocalizedException $e) {
            return $this->apiResponseFactory
                ->create()
                ->setStatus(ApiResponseInterface::STATUS_NOK)
                ->setMessage(__($e->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function createOrder(array $data, ?StoreInterface $store = null): ApiResponseInterface
    {
        try {
            $jsonResponse = $this->postRequest(ConfigManagementInterface::API_URL_CREATE_ORDER, $data, $store);

            $success = (bool) ($jsonResponse['success'] ?? false);
            $validationErrors = $jsonResponse['data'] ?? [];
            $validationErrors = $validationErrors['validationErrors'] ?? [];
            $validationErrors = $this->multiDimensionalArrayToString($validationErrors);

            $data = $jsonResponse['data'] ?? [];

            $message = $jsonResponse['message'] ?? '';
            if (!empty($validationErrors)) {
                $message .= '; ' . $validationErrors;
                $message = trim($message, '; ');
            }

            return $this->apiResponseFactory
                ->create()
                ->setStatus($success ? ApiResponseInterface::STATUS_OK : ApiResponseInterface::STATUS_NOK)
                ->setMessage(__($message))
                ->setData($data);
        } catch (LocalizedException $e) {
            return $this->apiResponseFactory
                ->create()
                ->setStatus(ApiResponseInterface::STATUS_NOK)
                ->setMessage(__($e->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function downloadWayBillA4(array $data, ?StoreInterface $store = null): ApiResponseInterface
    {
        $data['Order']['printer_type'] = OrderManagementInterface::FORMAT_A4;

        return $this->downloadWayBill($data);
    }

    /**
     * @inheritDoc
     */
    public function downloadWayBillA6(array $data, ?StoreInterface $store = null): ApiResponseInterface
    {
        $data['Order']['printer_type'] = OrderManagementInterface::FORMAT_A6;

        return $this->downloadWayBill($data);
    }

    /**
     * @inheritDoc
     */
    public function getTrackingInformation(array $data, ?StoreInterface $store = null): ApiResponseInterface
    {
        try {
            $jsonResponse = $this->postRequest(ConfigManagementInterface::API_URL_TRACKING_INFORMATION, $data, $store);

            $success = (bool) ($jsonResponse['success'] ?? false);
            $validationErrors = $jsonResponse['data'] ?? [];
            $validationErrors = $validationErrors['validationErrors'] ?? [];
            $validationErrors = $this->multiDimensionalArrayToString($validationErrors);

            $data = $jsonResponse['data'] ?? [];

            $message = $jsonResponse['message'] ?? '';
            if (!empty($validationErrors)) {
                $message .= '; ' . $validationErrors;
                $message = trim($message, '; ');
            }

            return $this->apiResponseFactory
                ->create()
                ->setStatus($success ? ApiResponseInterface::STATUS_OK : ApiResponseInterface::STATUS_NOK)
                ->setMessage(__(empty($message) ? 'Something went wrong!' : $message))
                ->setData($data);
        } catch (LocalizedException $e) {
            return $this->apiResponseFactory
                ->create()
                ->setStatus(ApiResponseInterface::STATUS_NOK)
                ->setMessage(__($e->getMessage()));
        }
    }

    private function downloadWayBill(array $data, ?StoreInterface $store = null): ApiResponseInterface
    {
        try {
            $jsonResponse = $this->postRequest(ConfigManagementInterface::API_URL_DOWNLOAD_BILL, $data, $store);

            $success = (bool) ($jsonResponse['success'] ?? false);
            $validationErrors = $jsonResponse['data'] ?? [];
            $validationErrors = $validationErrors['validationErrors'] ?? [];
            $validationErrors = $this->multiDimensionalArrayToString($validationErrors);

            $data = $jsonResponse['data'] ?? [];

            $message = $jsonResponse['message'] ?? '';
            if (!empty($validationErrors)) {
                $message .= '; ' . $validationErrors;
                $message = trim($message, '; ');
            }

            return $this->apiResponseFactory
                ->create()
                ->setStatus($success ? ApiResponseInterface::STATUS_OK : ApiResponseInterface::STATUS_NOK)
                ->setMessage(__(empty($message) ? 'Something went wrong!' : $message))
                ->setData($data);
        } catch (LocalizedException $e) {
            return $this->apiResponseFactory
                ->create()
                ->setStatus(ApiResponseInterface::STATUS_NOK)
                ->setMessage(__($e->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function isAuthorized(?StoreInterface $store = null): bool
    {
        $profile = $this->getProfile($store);

        return $profile->getStatus() === ApiResponseInterface::STATUS_OK;
    }

    /**
     * @inheritDoc
     */
    public function cancelOrder(array $data, ?StoreInterface $store = null): ApiResponseInterface
    {
        try {
            $jsonResponse = $this->postRequest(ConfigManagementInterface::API_URL_CANCEL_ORDER, $data, $store);

            $success = (bool) ($jsonResponse['success'] ?? false);
            $validationErrors = $jsonResponse['data'] ?? [];
            $validationErrors = $validationErrors['validationErrors'] ?? [];
            $validationErrors = $this->multiDimensionalArrayToString($validationErrors);

            $message = $jsonResponse['message'] ?? 'The BLPaczka shipment has been canceled!';
            if (!empty($validationErrors)) {
                $message .= '; ' . $validationErrors;
                $message = trim($message, '; ');
            }

            $data = $jsonResponse['data'] ?? [];

            return $this->apiResponseFactory
                ->create()
                ->setStatus($success ? ApiResponseInterface::STATUS_OK : ApiResponseInterface::STATUS_NOK)
                ->setMessage(__($message))
                ->setData($data);
        } catch (LocalizedException $e) {
            return $this->apiResponseFactory
                ->create()
                ->setStatus(ApiResponseInterface::STATUS_NOK)
                ->setMessage(__($e->getMessage()));
        }
    }

    /**
     * @throws LocalizedException
     */
    private function postRequest(string $endpoint, array $data, ?StoreInterface $store): array
    {
        $baseUrl = $this->configManagement->getApiUrl($store);
        $url = $baseUrl . $endpoint;
        $data['auth'] = [
            'login' => $this->configManagement->getEmail($store),
            'api_key' => $this->configManagement->getApiKey($store),
        ];

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new LocalizedException(__('BLPaczka Request Error: %1', curl_error($ch)));
            }

            curl_close($ch);

            $result = json_decode($response, true);

            return is_array($result) ? $result : [$result];
        } catch (\Throwable $t) {
            throw new LocalizedException(__('BLPaczka Request Error: %1', $t->getMessage()));
        }
    }

    private function multiDimensionalArrayToString(array $array): string
    {
        $strings = [];
        foreach ($array as $k => $item) {
            $title = !is_numeric($k) ? $k . ': ' : '';
            $strings[] = $title . (is_array($item) ? $this->multiDimensionalArrayToString($item) : $item);
        }

        return implode("<br>", $strings);
    }

}
