<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Controller\Adminhtml\Integration;

use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingResponseInterface;
use BLPaczka\MagentoIntegration\Api\ShippingManagementInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;

class GetValuation extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'BLPaczka_MagentoIntegration::admin';
    private ShippingManagementInterface $shippingManagement;

    public function __construct(
        Context $context,
        ShippingManagementInterface $shippingManagement
    ) {
        parent::__construct($context);
        $this->shippingManagement = $shippingManagement;
    }

    public function execute(): ShippingResponseInterface
    {
        $postData = $this->_request->getParams();

        return $this->shippingManagement->getValuation($postData ?? []);
    }
}
