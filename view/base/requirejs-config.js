/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */
var config = {
    config: {
        mixins: {
            'Magento_Ui/js/grid/massactions': {
                'BLPaczka_MagentoIntegration/js/grid/massactions-mixin': true
            },
        }
    }
};
