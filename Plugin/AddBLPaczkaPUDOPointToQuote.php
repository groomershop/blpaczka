<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Plugin;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Framework\Escaper;
use Magento\Quote\Api\CartRepositoryInterface;

class AddBLPaczkaPUDOPointToQuote
{
    private CartRepositoryInterface $quoteRepository;
    private Escaper $escaper;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Escaper $escaper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->escaper = $escaper;
    }

    public function beforeSaveAddressInformation(
        ShippingInformationManagementInterface $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $extensionAttributes = $addressInformation->getShippingAddress()->getExtensionAttributes();
        $quote = $this->quoteRepository->getActive($cartId);
        $value = null;

        if ($extensionAttributes && $extensionAttributes->getBlpaczkaPudoPoint()) {
            try {
                $pointData = json_decode($extensionAttributes->getBlpaczkaPudoPoint(), true);
                $value = [
                    'name' => $pointData['name'] ?? '',
                    'html' => $pointData['html'] ?? '',
                ];

                $value['name'] = strip_tags($value['name']);
                $value['html'] = strip_tags($value['html']);

                $value = json_encode($value);
            } catch (\Throwable $t) {}
        }

        $quote->setData('blpaczka_pudo_point', $value);
    }
}
