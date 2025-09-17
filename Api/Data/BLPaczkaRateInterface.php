<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Api\Data;

interface BLPaczkaRateInterface
{
    /**
     * @return string
     */
    public function getMapOriginUrl(): string;

    /**
     * @param string $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\BLPaczkaRateInterface
     */
    public function setMapOriginUrl(string $value): BLPaczkaRateInterface;
    /**
     * @return bool
     */
    public function getIsPudo(): bool;

    /**
     * @param bool $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\BLPaczkaRateInterface
     */
    public function setIsPudo(bool $value): BLPaczkaRateInterface;

    /**
     * @return string
     */
    public function getPudoMapUrl(): string;

    /**
     * @param string $value
     * @return \BLPaczka\MagentoIntegration\Api\Data\BLPaczkaRateInterface
     */
    public function setPudoMapUrl(string $value): BLPaczkaRateInterface;
}
