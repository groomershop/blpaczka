<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model;

use BLPaczka\MagentoIntegration\Api\Data\BLPaczkaRateInterface;

class BLPaczkaRate implements BLPaczkaRateInterface
{
    private string $mapOriginUrl;
    private bool $isPudo;
    private string $pudoMapUrl;

    public function getMapOriginUrl(): string
    {
        return $this->mapOriginUrl;
    }

    public function setMapOriginUrl(string $value): BLPaczkaRateInterface
    {
        $this->mapOriginUrl = $value;
        return $this;
    }

    public function getIsPudo(): bool
    {
        return $this->isPudo;
    }

    public function setIsPudo(bool $value): self
    {
        $this->isPudo = $value;
        return $this;
    }

    public function getPudoMapUrl(): string
    {
        return $this->pudoMapUrl;
    }

    public function setPudoMapUrl(string $value): self
    {
        $this->pudoMapUrl = $value;
        return $this;
    }
}
