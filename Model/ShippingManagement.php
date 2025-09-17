<?php
/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */

declare(strict_types=1);

namespace BLPaczka\MagentoIntegration\Model;

use BLPaczka\MagentoIntegration\Api\ApiServiceInterface;
use BLPaczka\MagentoIntegration\Api\ConfigManagementInterface;
use BLPaczka\MagentoIntegration\Api\Data\ApiResponseInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\PaymentInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\PaymentInterfaceFactory;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\RecipientInterfaceFactory;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\SenderInterfaceFactory;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingDetailsInterfaceFactory;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingResponseInterface;
use BLPaczka\MagentoIntegration\Api\Data\Shipment\ShippingResponseInterfaceFactory;
use BLPaczka\MagentoIntegration\Api\OrderManagementInterface;
use BLPaczka\MagentoIntegration\Api\ShippingManagementInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;

class ShippingManagement implements ShippingManagementInterface
{
    private PaymentInterfaceFactory $paymentFactory;
    private RecipientInterfaceFactory $recipientFactory;
    private SenderInterfaceFactory $senderFactory;
    private ShippingDetailsInterfaceFactory $shipmentDetailsFactory;
    private ConfigManagementInterface $configManagement;
    private ApiServiceInterface $apiService;
    private ShippingResponseInterfaceFactory $shippingResponseFactory;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        PaymentInterfaceFactory          $paymentFactory,
        RecipientInterfaceFactory        $recipientFactory,
        SenderInterfaceFactory           $senderFactory,
        ShippingDetailsInterfaceFactory  $shipmentDetailsFactory,
        ConfigManagementInterface        $configManagement,
        ApiServiceInterface              $apiService,
        ShippingResponseInterfaceFactory $shippingResponseFactory,
        OrderRepositoryInterface         $orderRepository
    )
    {
        $this->paymentFactory = $paymentFactory;
        $this->recipientFactory = $recipientFactory;
        $this->senderFactory = $senderFactory;
        $this->shipmentDetailsFactory = $shipmentDetailsFactory;
        $this->configManagement = $configManagement;
        $this->apiService = $apiService;
        $this->shippingResponseFactory = $shippingResponseFactory;
        $this->orderRepository = $orderRepository;
    }


    /**
     * @inheritDoc
     */
    public function getPayment(?StoreInterface $store = null): PaymentInterface
    {
        $type = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_PAYMENT_DETAILS_TYPE);

        return $this->paymentFactory
            ->create()
            ->setPaymentType($type);
    }


    /**
     * @inheritDoc
     */
    public function getRecipient(OrderInterface $order): RecipientInterface
    {
        $shippingAddress = $order->getShippingAddress();
        $store = $order->getStore();

        $fullName = null;
        $company = null;
        $phoneNumber = null;
        $email = null;
        $fullAddress = null;
        $street = null;
        $houseNumber = null;
        $apartmentNumber = null;
        $postCode = null;
        $city = null;

        if ($shippingAddress) {
            $fullName = $shippingAddress->getData(OrderAddressInterface::FIRSTNAME)
                . ' ' . $shippingAddress->getData(OrderAddressInterface::LASTNAME);
            $company = $shippingAddress->getData(OrderAddressInterface::COMPANY);
            $phoneNumber = $shippingAddress->getData(OrderAddressInterface::TELEPHONE);
            $email = $shippingAddress->getData(OrderAddressInterface::EMAIL);
            $fullAddress = $shippingAddress->getData(OrderAddressInterface::STREET);
            $fullAddress = is_array($fullAddress) ? implode(' ', $fullAddress) : $fullAddress;
            $fullAddress = str_replace(PHP_EOL, ' ', $fullAddress);
            $explodedStreet = $this->explodeAddress($fullAddress);
            $street = $explodedStreet['street'];
            $houseNumber = $explodedStreet['houseNumber'];
            $apartmentNumber = $explodedStreet['apartmentNumber'];
            $postCode = $shippingAddress->getData(OrderAddressInterface::POSTCODE);
            $city = $shippingAddress->getData(OrderAddressInterface::CITY);
        }

        $pointNumber = $order->getData('blpaczka_pudo_point');

        if ($pointNumber) {
            try {
                $pointNumber = json_decode($pointNumber, true);
                $pointNumber = $pointNumber['name'] ?? null;
            } catch (\Throwable $t) {
                $pointNumber = null;
            }
        }

        return $this->recipientFactory
            ->create()
            ->setFullName($fullName)
            ->setCompany($company)
            ->setPhoneNumber($phoneNumber)
            ->setPointNumber($pointNumber)
            ->setEmail($email)
            ->setFullAddress($fullAddress)
            ->setStreet($street)
            ->setHouseNumber($houseNumber)
            ->setApartmentNumber($apartmentNumber)
            ->setPostCode($postCode)
            ->setCity($city);
    }

    /**
     * @inheritDoc
     */
    public function getSender(?StoreInterface $store = null): SenderInterface
    {
        $fullName = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SENDER_FULLNAME);
        $company = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SENDER_COMPANY);
        $email = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SENDER_EMAIL);
        $street = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SENDER_STREET);
        $houseNumber = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SENDER_HOUSENUMBER);
        $apartmentNumber = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SENDER_APARTMENTNUMBER);
        $postCode = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SENDER_POSTCODE);
        $city = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SENDER_CITY);
        $phoneNumber = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SENDER_PHONE_NUMBER);

        return $this->senderFactory
            ->create()
            ->setFullName($fullName)
            ->setCompany($company)
            ->setEmail($email)
            ->setStreet($street)
            ->setHouseNumber($houseNumber)
            ->setApartmentNumber($apartmentNumber)
            ->setPostCode($postCode)
            ->setCity($city)
            ->setPhoneNumber($phoneNumber);
    }

    /**
     * @inheritDoc
     */
    public function getShippingDetails(OrderInterface $order): ShippingDetailsInterface
    {
        $shippingMethod = null;
        $shippingAddress = null;
        $paymentMethod = null;
        $store = $order->getStore();

        if ($order->getShippingMethod()) {
            $shippingMethod = $order->getShippingMethod(true);
        }

        if ($order->getShippingAddress()) {
            $shippingAddress = $order->getShippingAddress();
        }

        if ($order->getPayment()) {
            $paymentMethod = $order->getPayment();
        }

        $selectedShippingMethodCode = null;
        $selectedShippingMethod = $order->getData('shipping_description');
        $courierCode = null;
        $countryCode = null;
        $type = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_TYPE);
        $weight = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_WEIGHT);
        $length = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_LENGTH);
        $width = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_WIDTH);
        $height = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_HEIGHT);
        $content = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_CONTENT);
        $insuranceTotal = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_INSURANCETOTAL);
        $cashOnDeliveryTotal = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_CASHONDELIVERYTOTAL);
        $sortable = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_SORTABLE);
        $withoutCourierPickUp = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_WITHOUTCOURIERPICKUP);
        $courierArrivalDay = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_COURIERARRIVALDAY);
        $courierArrivalTimeFrom = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_COURIERARRIVALTIMEFROM);
        $courierArrivalTimeTo = $this
            ->configManagement
            ->getConfigValue($store, ConfigManagementInterface::CONFIG_PATH_DEFAULT_SHIPPING_DETAILS_COURIERARRIVALTIMETO);
        $foreign = ShippingDetailsInterface::FOREIGN_TYPE_FOREIGN;

        if ($shippingMethod) {
            $selectedShippingMethodCode = $this
                ->configManagement
                ->removeSuffixFromMethodCode($shippingMethod->getData('method'));
            $courierCode = $selectedShippingMethodCode;
        }

        if ($shippingAddress) {
            $countryCode = $shippingAddress->getCountryId();
        }

        if ($order->getWeight()) {
            $weight = $order->getWeight();
        }

        if ($order->getBaseGrandTotal()) {
            $insuranceTotal = $order->getBaseGrandTotal();
        }

        if ($paymentMethod && $paymentMethod->getData('method') === 'cashondelivery') {
            $cashOnDeliveryTotal = $order->getBaseGrandTotal();
        }

        if ($courierArrivalTimeFrom) {
            $courierArrivalTimeFrom = str_replace(',', ':', $courierArrivalTimeFrom);
        }

        if ($courierArrivalTimeTo) {
            $courierArrivalTimeTo = str_replace(',', ':', $courierArrivalTimeTo);
        }

        if ($shippingAddress && $countryCode) {
            $foreignType = ShippingDetailsInterface::FOREIGN_TYPE_COUNTRY_CODE_MAP[strtolower($countryCode)] ?? null;
            $foreign = $foreignType ?? $foreign;
        }

        return $this->shipmentDetailsFactory
            ->create()
            ->setShipmentSelectedShippingMethodCode($selectedShippingMethodCode)
            ->setShipmentSelectedShippingMethodDescription($selectedShippingMethod)
            ->setShipmentCourierCode($courierCode)
            ->setShipmentCountryCode($countryCode)
            ->setShipmentType($type)
            ->setShipmentWeight($weight)
            ->setShipmentLength($length)
            ->setShipmentWidth($width)
            ->setShipmentHeight($height)
            ->setShipmentContent($content)
            ->setShipmentInsuranceTotal($insuranceTotal)
            ->setShipmentCashOnDeliveryTotal($cashOnDeliveryTotal)
            ->setShipmentSortable($sortable)
            ->setShipmentWithoutCourierPickUp($withoutCourierPickUp)
            ->setShipmentCourierArrivalDay($courierArrivalDay)
            ->setShipmentCourierArrivalTimeFrom($courierArrivalTimeFrom)
            ->setShipmentCourierArrivalTimeTo($courierArrivalTimeTo)
            ->setShipmentForeign($foreign);
    }

    /**
     * @inheritDoc
     */
    public function getBLPaczkaShippingMethods(?StoreInterface $store = null): array
    {
        $shippingMethods = [['title' => __('Any courier'), 'code' => self::SHIPPING_METHOD_CODE_ANY_COURIER]];
        foreach (ConfigManagementInterface::METHOD_CODE_METHOD_TITLE_MAP as $code => $title) {
            $shippingMethods[] = [
                'title' => __($title),
                'code' => $code,
            ];
        }

        return $shippingMethods;
    }

    public function connect(): ShippingResponseInterface
    {
        $apiResponse = $this->apiService->getProfile(null);

        return $this->shippingResponseFactory->create()->setData([
            'status' => $apiResponse->getStatus(),
            'message' => $apiResponse->getMessage(),
        ]);
    }

    public function getBLPaczkaDataFromRequestData(array $requestData): ?array
    {
        $data = $requestData[self::BLPACZKA_DATA_KEY] ?? null;

        if (!$data) {
            return null;
        }

        if (is_array($data)) {
            return $data;
        }

        try {
            return json_decode($data, true);
        } catch (\Throwable $t) {
            return null;
        }
    }

    public function getDataForCreateOrderForm(OrderInterface $order): array
    {
        $defaultShippingDetails = $this->getShippingDetails($order);
        $defaultSender = $this->getSender($order->getStore());
        $defaultRecipient = $this->getRecipient($order);
        $defaultPayment = $this->getPayment($order->getStore());

        return [
            'shippingDetails' => [
                'shipmentSelectedShippingMethodDescription' => $defaultShippingDetails->getShipmentSelectedShippingMethodDescription(),
                'shipmentSelectedShippingMethodCode' => $defaultShippingDetails->getShipmentSelectedShippingMethodCode(),
                'shipmentCourierCode' => $defaultShippingDetails->getShipmentCourierCode(),
                'shipmentCountryCode' => $defaultShippingDetails->getShipmentCountryCode(),
                'shipmentType' => $defaultShippingDetails->getShipmentType(),
                'shipmentWeight' => $defaultShippingDetails->getShipmentWeight(),
                'shipmentLength' => $defaultShippingDetails->getShipmentLength(),
                'shipmentWidth' => $defaultShippingDetails->getShipmentWidth(),
                'shipmentHeight' => $defaultShippingDetails->getShipmentHeight(),
                'shipmentContent' => $defaultShippingDetails->getShipmentContent(),
                'shipmentInsuranceTotal' => $defaultShippingDetails->getShipmentInsuranceTotal(),
                'shipmentCashOnDeliveryTotal' => $defaultShippingDetails->getShipmentCashOnDeliveryTotal(),
                'shipmentSortable' => $defaultShippingDetails->getShipmentSortable(),
                'shipmentWithoutCourierPickUp' => $defaultShippingDetails->getShipmentWithoutCourierPickUp(),
                'shipmentCourierArrivalDay' => $defaultShippingDetails->getShipmentCourierArrivalDay(),
                'shipmentCourierArrivalTimeFrom' => $defaultShippingDetails->getShipmentCourierArrivalTimeFrom(),
                'shipmentCourierArrivalTimeTo' => $defaultShippingDetails->getShipmentCourierArrivalTimeTo(),
                'shipmentForeign' => $defaultShippingDetails->getShipmentForeign(),
            ],
            'senderDetails' => [
                'senderFullName' => $defaultSender->getFullName(),
                'senderCompany' => $defaultSender->getCompany(),
                'senderEmail' => $defaultSender->getEmail(),
                'senderStreet' => $defaultSender->getStreet(),
                'senderHouseNumber' => $defaultSender->getHouseNumber(),
                'senderApartmentNumber' => $defaultSender->getApartmentNumber(),
                'senderPostCode' => $defaultSender->getPostCode(),
                'senderCity' => $defaultSender->getCity(),
                'senderPhoneNumber' => $defaultSender->getPhoneNumber(),
            ],
            'recipientDetails' => [
                'recipientFullName' => $defaultRecipient->getFullName(),
                'recipientCompany' => $defaultRecipient->getCompany(),
                'recipientPhoneNumber' => $defaultRecipient->getPhoneNumber(),
                'recipientPointNumber' => $defaultRecipient->getPointNumber(),
                'recipientEmail' => $defaultRecipient->getEmail(),
                'recipientFullAddress' => $defaultRecipient->getFullAddress(),
                'recipientStreet' => $defaultRecipient->getStreet(),
                'recipientHouseNumber' => $defaultRecipient->getHouseNumber(),
                'recipientApartmentNumber' => $defaultRecipient->getApartmentNumber(),
                'recipientPostCode' => $defaultRecipient->getPostCode(),
                'recipientCity' => $defaultRecipient->getCity(),
            ],
            'paymentDetails' => [
                'paymentType' => $defaultPayment->getPaymentType(),
            ]
        ];
    }

    public function getDataForCancelOrderForm(OrderInterface $order): array
    {
        try {
            $orderData = $order->getData('blpaczka_order_data');
            $orderData = $orderData ? json_decode($orderData, true) : [];
            $orderId = $orderData['Order'] ?? [];
            $orderId = $orderId[0] ?? [];
            $orderId = $orderId['id'] ?? null;
        } catch (\Throwable $t) {
            $orderId = null;
        }

        return [
            'orderDetails' => [
                'id' => $orderId,
            ]
        ];
    }


    public function convertDataForCreateOrderRequest(OrderInterface $order, array $data): array
    {
        $courierCode = $data['shippingDetails']['shipmentCourierCode'];
        $noCustomPickup = (bool) ($data['shippingDetails']['shipmentWithoutCourierPickUp'] ?? false);
        $requestData = [
            'origin' => 'Magento',
            'CourierSearch' => [
                'type' => $data['shippingDetails']['shipmentType'] ?? '',
                'country_code' => $data['shippingDetails']['shipmentCountryCode'] ?? '',
                'weight' => $data['shippingDetails']['shipmentWeight'] ?? '',
                'side_x' => $data['shippingDetails']['shipmentLength'] ?? '',
                'side_y' => $data['shippingDetails']['shipmentWidth'] ?? '',
                'side_z' => $data['shippingDetails']['shipmentHeight'] ?? '',
                'sortable' => (bool) ($data['shippingDetails']['shipmentSortable'] ?? false),
                'cover' => $data['shippingDetails']['shipmentInsuranceTotal'] ?? '',
                'uptake' => $data['shippingDetails']['shipmentCashOnDeliveryTotal'] ?? '',
                'foreign' => $data['shippingDetails']['shipmentForeign'] ?? '',
                'no_pickup' => $noCustomPickup,
                'is_return' => false,
            ],
            'CartOrder' => [
                'payment' => $data['paymentDetails']['paymentType'] ?? '',
            ],
            'Cart' => [[
                'Order' => [
                    'name' => $data['senderDetails']['senderFullName'] ?? '',
                    'vat_company' => $data['senderDetails']['senderCompany'] ?? '',
                    'email' => $data['senderDetails']['senderEmail'] ?? '',
                    'street' => $data['senderDetails']['senderStreet'] ?? '',
                    'house_no' => $data['senderDetails']['senderHouseNumber'] ?? '',
                    'locum_no' => $data['senderDetails']['senderApartmentNumber'] ?? '',
                    'postal' => $data['senderDetails']['senderPostCode'] ?? '',
                    'city' => $data['senderDetails']['senderCity'] ?? '',
                    'phone' => $data['senderDetails']['senderPhoneNumber'] ?? '',

                    'taker_name' => $data['recipientDetails']['recipientFullName'] ?? '',
                    'taker_vat_company' => $data['recipientDetails']['recipientCompany'] ?? '',
                    'taker_phone' => $data['recipientDetails']['recipientPhoneNumber'] ?? '',
                    'taker_street' => $data['recipientDetails']['recipientStreet'] ?? '',
                    'taker_city' => $data['recipientDetails']['recipientCity'] ?? '',
                    'taker_house_no' => $data['recipientDetails']['recipientHouseNumber'] ?? '',
                    'taker_locum_no' => $data['recipientDetails']['recipientApartmentNumber'] ?? '',
                    'taker_postal' => $data['recipientDetails']['recipientPostCode'] ?? '',
                    'taker_email' => $data['recipientDetails']['recipientEmail'] ?? '',

                    'custom_pickup' => !$noCustomPickup,
                    'package_content' => $data['shippingDetails']['shipmentContent'] ?? '',
                    'taker_point' => $data['recipientDetails']['recipientPointNumber'] ?? '',
                ]
            ]],
        ];

        if (!$noCustomPickup) {
            $timeFrom = $data['shippingDetails']['shipmentCourierArrivalTimeFrom'] ?? '';
            $timeTo = $data['shippingDetails']['shipmentCourierArrivalTimeTo'] ?? '';

            $timeFrom = explode(':', $timeFrom);
            $timeTo = explode(':', $timeTo);


            $requestData['Cart'][0]['Order']['pickup_date'] = $data['shippingDetails']['shipmentCourierArrivalDay'] ?? '';
            $requestData['Cart'][0]['Order']['pickup_ready_time'] = $timeFrom[0] ?? '';
            $requestData['Cart'][0]['Order']['pickup_ready_time_minute'] = $timeFrom[1] ?? '';
            $requestData['Cart'][0]['Order']['pickup_close_time'] = $timeTo[0] ?? '';
            $requestData['Cart'][0]['Order']['pickup_close_time_minute'] = $timeTo[1] ?? '';
            $requestData['Cart'][0]['Order']['pickup_name'] = $data['recipientDetails']['recipientFullName'] ?? '';
            $requestData['Cart'][0]['Order']['pickup_phone'] = $data['recipientDetails']['recipientPhoneNumber'] ?? '';
            $requestData['Cart'][0]['Order']['pickup_city'] = $data['recipientDetails']['recipientCity'] ?? '';
            $requestData['Cart'][0]['Order']['pickup_street'] = $data['recipientDetails']['recipientStreet'] ?? '';
            $requestData['Cart'][0]['Order']['pickup_house_no'] = $data['recipientDetails']['recipientHouseNumber'] ?? '';
            $requestData['Cart'][0]['Order']['pickup_locum_no'] = $data['recipientDetails']['recipientApartmentNumber'] ?? '';
            $requestData['Cart'][0]['Order']['pickup_postal'] = $data['recipientDetails']['recipientPostCode'] ?? '';
        }

        if ($courierCode && $courierCode !== ShippingManagementInterface::SHIPPING_METHOD_CODE_ANY_COURIER) {
            $requestData['CourierSearch']['courier_code'] = $courierCode;
        }

        return $requestData;
    }

    public function convertDataForGetValuationRequest(array $data): array
    {
        $courierCode = $data['shippingDetails']['shipmentCourierCode'];
        $requestData = [
            'CourierSearch' => [
                'type' => $data['shippingDetails']['shipmentType'] ?? '',
                'country_code' => $data['shippingDetails']['shipmentCountryCode'] ?? '',
                'weight' => $data['shippingDetails']['shipmentWeight'] ?? '',
                'side_x' => $data['shippingDetails']['shipmentLength'] ?? '',
                'side_y' => $data['shippingDetails']['shipmentWidth'] ?? '',
                'side_z' => $data['shippingDetails']['shipmentHeight'] ?? '',
                'sortable' => (bool) ($data['shippingDetails']['shipmentSortable'] ?? false),
                'cover' => $data['shippingDetails']['shipmentInsuranceTotal'] ?? '',
                'uptake' => $data['shippingDetails']['shipmentCashOnDeliveryTotal'] ?? '',
                'foreign' => $data['shippingDetails']['shipmentForeign'] ?? '',
                'no_pickup' => (bool) ($data['shippingDetails']['shipmentWithoutCourierPickUp'] ?? false),
                'is_return' => false,
            ],
        ];

        if ($courierCode && $courierCode !== ShippingManagementInterface::SHIPPING_METHOD_CODE_ANY_COURIER) {
            $requestData['CourierSearch']['courier_code'] = $courierCode;
        }

        return $requestData;
    }

    public function convertDataForCancelOrderRequest(array $data): array
    {
        $orderId = $data['orderDetails']['id'];

        return [
            'Order' => [
                'id' => $orderId,
            ]
        ];
    }

    public function getValuation(array $data): ShippingResponseInterface
    {
        if (!isset($data['orderId'])) {
            return $this->shippingResponseFactory->create()->setData([
                'status' => ShippingResponseInterface::STATUS_NOK,
                'message' => __('Order ID is required!'),
                'data' => null,
            ]);
        }

        try {
            $order = $this->orderRepository->get((int)$data['orderId']);
        } catch (\Throwable $t) {
            return $this->shippingResponseFactory->create()->setData([
                'status' => ShippingResponseInterface::STATUS_NOK,
                'message' => __('Order #%1 does not exists!', $data['orderId']),
                'data' => null,
            ]);
        }

        $BLPaczkaData = $this->getBLPaczkaDataFromRequestData($data);
        $BLPaczkaData = $this->convertDataForGetValuationRequest($BLPaczkaData);
        $apiResponse = $this->apiService->getValuation($BLPaczkaData, $order->getStore());

        return $this->shippingResponseFactory->create()->setData([
            'status' => $apiResponse->getStatus(),
            'message' => $apiResponse->getMessage(),
            'data' => $apiResponse->getData(),
        ]);
    }

    public function createOrder(array $data): ShippingResponseInterface
    {
        if (!isset($data['orderId'])) {
            return $this->shippingResponseFactory->create()->setData([
                'status' => ShippingResponseInterface::STATUS_NOK,
                'message' => __('Order ID is required!'),
                'data' => null,
            ]);
        }

        try {
            $order = $this->orderRepository->get((int)$data['orderId']);
        } catch (\Throwable $t) {
            return $this->shippingResponseFactory->create()->setData([
                'status' => ShippingResponseInterface::STATUS_NOK,
                'message' => __('Order #%1 does not exists!', $data['orderId']),
                'data' => null,
            ]);
        }

        try {
            $alreadyCreated = $order->getData('blpaczka_order_data') ?? '[]';
            $alreadyCreated = json_decode($alreadyCreated, true);
            $alreadyCreated = $alreadyCreated ?: [];
            $alreadyCreated = $alreadyCreated['Order'] ?? [];
            $alreadyCreated = $alreadyCreated[0] ?? [];
            $alreadyCreated = $alreadyCreated['id'] ?? null;
            if ($alreadyCreated) {
                return $this->shippingResponseFactory->create()->setData([
                    'status' => ShippingResponseInterface::STATUS_NOK,
                    'message' => __('The BLPaczka shipment has already been ordered!'),
                    'data' => null,
                ]);
            }
        } catch (\Throwable $t) {
            $alreadyCreated = null;
        }

        $BLPaczkaData = $this->getBLPaczkaDataFromRequestData($data);
        $BLPaczkaData = $BLPaczkaData ?? $this->getDataForCreateOrderForm($order);
        $BLPaczkaData = $this->convertDataForCreateOrderRequest($order, $BLPaczkaData);
        $apiResponse = $this->apiService->createOrder($BLPaczkaData, $order->getStore());

        $responseData = $apiResponse->getData() ?? [];
        $responseData['MagentoOrderId'] = (int)$data['orderId'];

        if ($apiResponse->getStatus() === ShippingResponseInterface::STATUS_OK) {
            $order->setData('blpaczka_order_data', $apiResponse->getDataJson());

            $shippingMethodName = $responseData['Order'] ?? [];
            $shippingMethodName = $shippingMethodName['name'] ?? null;
            if ($shippingMethodName) {
                $order->setShippingDescription(
                    (string)__('BLPaczka - %1', $shippingMethodName)
                );
            }

            try {
                $this->orderRepository->save($order);
            } catch (\Throwable $t) {
                return $this->shippingResponseFactory->create()->setData([
                    'status' => ShippingResponseInterface::STATUS_OK,
                    'message' => __('BLPaczka package has been ordered, but the Magento order has not been updated!'),
                    'data' => $responseData,
                ]);
            }
        } else {
            $order->setData('blpaczka_order_data', null);
            $order->setData(OrderManagementInterface::ORDER_DATA_SHIPPING_LABEL_A4, null);
            $order->setData(OrderManagementInterface::ORDER_DATA_SHIPPING_LABEL_A6, null);

            try {
                $this->orderRepository->save($order);
            } catch (\Throwable $t) {
                return $this->shippingResponseFactory->create()->setData([
                    'status' => ShippingResponseInterface::STATUS_OK,
                    'message' => __('BLPaczka package has NOT been ordered and the Magento order has not been updated!'),
                    'data' => $responseData,
                ]);
            }
        }

        return $this->shippingResponseFactory->create()->setData([
            'status' => $apiResponse->getStatus(),
            'message' => $apiResponse->getMessage(),
            'data' => $responseData,
        ]);
    }

    public function cancelOrder(array $data): ShippingResponseInterface
    {
        if (!isset($data['orderId'])) {
            return $this->shippingResponseFactory->create()->setData([
                'status' => ShippingResponseInterface::STATUS_NOK,
                'message' => __('Order ID is required!'),
                'data' => null,
            ]);
        }

        try {
            $order = $this->orderRepository->get((int)$data['orderId']);
        } catch (\Throwable $t) {
            return $this->shippingResponseFactory->create()->setData([
                'status' => ShippingResponseInterface::STATUS_NOK,
                'message' => __('Order #%1 does not exists!', $data['orderId']),
                'data' => null,
            ]);
        }

        $cancelOrderData = $this->getDataForCancelOrderForm($order);
        $cancelOrderData = $this->convertDataForCancelOrderRequest($cancelOrderData);

        $apiResponse = $this->apiService->cancelOrder($cancelOrderData);
        $responseData = $apiResponse->getData() ?? [];
        $responseData['MagentoOrderId'] = (int)$data['orderId'];

        if ($apiResponse->getStatus() === ShippingResponseInterface::STATUS_OK) {
            $order->setData('blpaczka_order_data', null);
            $order->setData(OrderManagementInterface::ORDER_DATA_SHIPPING_LABEL_A4, null);
            $order->setData(OrderManagementInterface::ORDER_DATA_SHIPPING_LABEL_A6, null);

            try {
                $this->orderRepository->save($order);
            } catch (\Throwable $t) {
                return $this->shippingResponseFactory->create()->setData([
                    'status' => ShippingResponseInterface::STATUS_OK,
                    'message' => __('BLPaczka package has been canceled, but the Magento order has not been updated!'),
                    'data' => $responseData,
                ]);
            }
        }

        return $this->shippingResponseFactory->create()->setData([
            'status' => $apiResponse->getStatus(),
            'message' => $apiResponse->getMessage(),
            'data' => $responseData,
        ]);
    }

    private function explodeAddress(string $address): array
    {
        $patternToExplodeAddress = '/^(.*?)\s+(\d+(?:\s*m)?)(?:\s*(?:m|\/)\.?(\w+))?(?:,\s*(\w+))?\s*$/';
        $street = '';
        $houseNumber = '';
        $apartmentNumber = '';

        if (preg_match($patternToExplodeAddress, trim($address), $matches)) {
            $street = $matches[1] ?? '';
            $houseNumber = $matches[2] ?? '';
            $apartmentNumber = $matches[3] ?? '';
        }

        return [
            'street' => $street,
            'houseNumber' => $houseNumber,
            'apartmentNumber' => $apartmentNumber,
        ];
    }
}
