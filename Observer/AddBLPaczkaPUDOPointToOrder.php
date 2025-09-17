<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class AddBLPaczkaPUDOPointToOrder implements ObserverInterface
{
    private CartRepositoryInterface $quoteRepository;

    public function __construct(
        CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    public function execute(Observer $observer): self
    {
        $order = $observer->getOrder();

        if ($order && $order->getQuoteId()) {
            $quote = $this->quoteRepository->get($order->getQuoteId());

            if ($quote->getData('blpaczka_pudo_point')) {
                $order->setData('blpaczka_pudo_point', $quote->getData('blpaczka_pudo_point'));
            }
        }

        return $this;
    }
}
