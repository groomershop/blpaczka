<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Api;

use Magento\Store\Api\Data\StoreInterface;

interface ConfigManagementInterface
{
    const LOGO_FILE_ID = 'BLPaczka_MagentoIntegration::images/logo_blpaczka.svg';
    const SHIPPING_METHOD_CODE = 'blpaczka';
    const METHOD_CODE_METHOD_TITLE_MAP = [
        'poczta' => 'Post',
        'poczta_ecommerce_envelope' => 'Post eCommerce Envelope',
        'dpd' => 'DPD',
        'ups' => 'UPS',
        'dhl' => 'DHL',
        'blp_cross_border' => 'BLP Cross-Border',
        'blp_cross_border_eco' => 'BLP Cross-Border Eco',
        'fedex' => 'FedEx',
        'fedex_rest' => 'FedEx REST',
        'gls' => 'GLS',
        'geis' => 'GEIS',
        'geodis' => 'GEODIS',
        'hellman' => 'Hellman',
        'inpost' => 'InPost',
        'inpost_international' => 'InPost International',
        'orlen' => 'Orlen',
        'paczkomaty' => 'InPost Parcel Locker',
        'paczkomaty_eco' => 'InPost Parcel Locker Eco',
        'paczkomaty_to_door' => 'InPost Parcel Locker to Door',
        'paczkomaty_allegro_smart' => 'InPost Parcel Locker Allegro Smart',
        'allegro_smart_dpd' => 'DPD Allegro Smart',
        'allegro_smart_ecommerce' => 'Allegro Smart eCommerce',
        'allegro_smart_poczta' => 'Allegro Smart Post',
        'euro_hermes' => 'Euro Hermes',
        'olza_logistic' => 'Olza Logistic',
        'ambro_express' => 'Ambro Express',
        'spring' => 'Spring',
    ];
    const PUDO_AVAILABLE_COURIERS = [
        'paczkomaty',
        'paczkomaty_eco',
        'paczkomaty_allegro_smart',
        'inpost_international',
        'orlen',
        'dhl',
        'dpd',
        'allegro_smart_dpd',
        'poczta',
        'allegro_smart_poczta',
    ];
    const PUDO_REQUIRED_COURIERS = [
        'paczkomaty',
        'paczkomaty_eco',
        'paczkomaty_allegro_smart',
        'inpost_international',
        'orlen',
    ];
    const METHOD_CODE_BLPACZKA_METHOD_CODE_MAP = [
//        'paczkomaty' => 'inpost',
//        'paczkomaty_eco' => 'inpost',
    ];

    const SANDBOX_API_URL = 'https://sandbox.blpaczka.com';
    const PRODUCTION_API_URL = 'https://send.blpaczka.com';

    const API_URL_MAP = '/pudo-map?api_type={blpaczkaMethodCode}&postalCode={postCode}';
    const API_URL_GET_PROFILE = '/api/getProfile.json';
    const API_URL_GET_VALUATION = '/api/getValuation.json';
    const API_URL_CREATE_ORDER = '/api/createOrderV2.json';
    const API_URL_DOWNLOAD_BILL = '/api/getWaybill.json';
    const API_URL_TRACKING_INFORMATION = '/api/getWaybillTracking.json';
    const API_URL_CANCEL_ORDER = '/api/cancelOrder.json';

    const CONFIG_PATH_MODE = 'carriers/blpaczka/mode';
    const CONFIG_PATH_SANDBOX_EMAIL = 'carriers/blpaczka/sandbox_email';
    const CONFIG_PATH_SANDBOX_API_KEY = 'carriers/blpaczka/sandbox_api_key';
    const CONFIG_PATH_PRODUCTION_API_KEY = 'carriers/blpaczka/production_api_key';
    const CONFIG_PATH_PRODUCTION_EMAIL = 'carriers/blpaczka/production_email';
    const CONFIG_PATH_COURIERS_CONFIG = 'carriers/blpaczka/couriers_config';

    const CONFIG_PATH_DEFAULT_SENDER_FULLNAME = 'blpaczka/default_sender/fullname';
    const CONFIG_PATH_DEFAULT_SENDER_COMPANY = 'blpaczka/default_sender/company';
    const CONFIG_PATH_DEFAULT_SENDER_EMAIL = 'blpaczka/default_sender/email';
    const CONFIG_PATH_DEFAULT_SENDER_STREET = 'blpaczka/default_sender/street';
    const CONFIG_PATH_DEFAULT_SENDER_HOUSENUMBER = 'blpaczka/default_sender/housenumber';
    const CONFIG_PATH_DEFAULT_SENDER_APARTMENTNUMBER = 'blpaczka/default_sender/apartmentnumber';
    const CONFIG_PATH_DEFAULT_SENDER_POSTCODE = 'blpaczka/default_sender/postcode';
    const CONFIG_PATH_DEFAULT_SENDER_CITY = 'blpaczka/default_sender/city';
    const CONFIG_PATH_DEFAULT_SENDER_PHONE_NUMBER = 'blpaczka/default_sender/phonenumber';

    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_TYPE = 'blpaczka/default_shipping_details/type';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_WEIGHT = 'blpaczka/default_shipping_details/weight';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_LENGTH = 'blpaczka/default_shipping_details/length';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_WIDTH = 'blpaczka/default_shipping_details/width';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_HEIGHT = 'blpaczka/default_shipping_details/height';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_CONTENT = 'blpaczka/default_shipping_details/content';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_INSURANCETOTAL = 'blpaczka/default_shipping_details/insurancetotal';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_CASHONDELIVERYTOTAL = 'blpaczka/default_shipping_details/cashondeliverytotal';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_SORTABLE = 'blpaczka/default_shipping_details/sortable';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_WITHOUTCOURIERPICKUP = 'blpaczka/default_shipping_details/withoutcourierpickup';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_COURIERARRIVALDAY = 'blpaczka/default_shipping_details/courierarrivalday';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_COURIERARRIVALTIMEFROM = 'blpaczka/default_shipping_details/courierarrivaltimefrom';
    const CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_COURIERARRIVALTIMETO = 'blpaczka/default_shipping_details/courierarrivaltimeto';


    const CONFIG_PATH_DEFAULT_PAYMENT_DETAILS_TYPE = 'blpaczka/default_payment/type';

    public function getApiUrl(?StoreInterface $store = null): string;
    public function getIsSandboxMode(?StoreInterface $store = null): bool;
    public function getEmail(?StoreInterface $store = null): string;
    public function getApiKey(?StoreInterface $store = null): string;
    public function getMapUrl(?string $methodCode, ?string $postCode, ?StoreInterface $store = null): string;
    public function getCouriers(?StoreInterface $store = null, bool $enabledFilter = false): array;
    public function getCouriersWithPUDOAvailable(): array;
    public function getCouriersWithPUDORequired(): array;
    public function isEnabledCourier(string $methodCode, ?StoreInterface $store = null): bool;
    public function isPUDOCourier(string $methodCode, ?StoreInterface $store = null): bool;
    public function isPUDORequiredCourier(string $methodCode, ?StoreInterface $store = null): bool;
    public function isPUDOAvailableCourier(string $methodCode, ?StoreInterface $store = null): bool;
    public function addSuffixFromMethodCode(string $methodCode, string $methodTitle): string;
    public function removeSuffixFromMethodCode(string $methodCode): string;
    public function getConfigValue(?StoreInterface $store, string $path): ?string;
}
