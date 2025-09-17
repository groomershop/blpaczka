<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Service\Data;

use BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface;
use Magento\Framework\Phrase;

class ApiResponse implements ApiResponseInterface
{
    private int $status;
    private Phrase $message;
    private ?array $data;

    /**
     * @inheritDoc
     */
    public function setStatus(int $status): ApiResponseInterface
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setMessage(Phrase $message): ApiResponseInterface
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return (string) $this->message;
    }

    public function getMessagePhrase(): Phrase
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function getDataJson(): ?string
    {
        return $this->getData() ? json_encode($this->getData()) : null;
    }

    /**
     * @inheritDoc
     */
    public function setData(?array $data): self
    {
        $this->data = $data;
        return $this;
    }
}
