<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model\Config\Source;

class ModeSelect implements  \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var string
     */
    const SANDBOX = 'sandbox';

    /**
     * @var string
     */
    const PRODUCTION = 'production_mode';

    public function toOptionArray(): array
    {
        return [
            ['label' => __('Sandbox'), 'value' => self::SANDBOX],
            ['label' => __('Production'), 'value' => self::PRODUCTION],
        ];
    }
}
