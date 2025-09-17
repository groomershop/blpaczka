<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Api;

use BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface;
use Magento\Store\Api\Data\StoreInterface;

interface ApiServiceInterface
{

    /**
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface
     */
    public function getProfile(?StoreInterface $store = null): ApiResponseInterface;

    /**
     * @param array[] $data
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface
     */
    public function getValuation(array $data, ?StoreInterface $store = null): ApiResponseInterface;

    /**
     * @param array[] $data
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface
     */
    public function createOrder(array $data, ?StoreInterface $store = null): ApiResponseInterface;

    /**
     * @param array[] $data
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface
     */
    public function downloadWayBillA4(array $data, ?StoreInterface $store = null): ApiResponseInterface;

    /**
     * @param array[] $data
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface
     */
    public function downloadWayBillA6(array $data, ?StoreInterface $store = null): ApiResponseInterface;

    /**
     * @param array[] $data
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface
     */
    public function getTrackingInformation(array $data, ?StoreInterface $store = null): ApiResponseInterface;

    /**
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return bool
     */
    public function isAuthorized(?StoreInterface $store = null): bool;

    /**
     * @param array[] $data
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface
     */
    public function cancelOrder(array $data, ?StoreInterface $store = null): ApiResponseInterface;
}
