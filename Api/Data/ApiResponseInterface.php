<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Api\Data;

use Magento\Framework\Phrase;

interface ApiResponseInterface
{
    const STATUS_NOK = 0;
    const STATUS_OK = 1;

    /**
     * @param int $status
     * @return \BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface
     */
    public function setStatus(int $status): ApiResponseInterface;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param \Magento\Framework\Phrase $message
     * @return \BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface
     */
    public function setMessage(Phrase $message): ApiResponseInterface;

    /**
     * @return string
     */
    public function getMessage(): string;
    public function getMessagePhrase(): Phrase;

    /**
     * @param array[]|null $data
     * @return \BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface
     */
    public function setData(?array $data): ApiResponseInterface;

    /**
     * @return array[]|null
     */
    public function getData(): ?array;

    /**
     * @return string|null
     */
    public function getDataJson(): ?string;
}
