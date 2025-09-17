/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'BLPaczka_MagentoIntegration/js/pudo-map/shipping-mixin': true
            },
        }
    }
};
