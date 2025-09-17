/**
 * @category  KnockoutUiCore
 * @package   Polcode\KnockoutUiCore
 * @author    Dawid Zastawny <dawid.zastawny@polcode.net>
 * @copyright 2023 Copyright (c) Polcode (http://wwww.polcode.com)
 *
 */
define([
    'uiComponent',
    'ko',
    'jquery',
    'domReady!',
    'mage/translate',
    'jquery/validate',
], function (Component, ko, $) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'BLPaczka_MagentoIntegration/order/view/shipment-form'
        },
        formId: 'blpaczka-shipment-form',
        recipientPointNumberPreviousValue: null,
        data: null,
        initialize: function (config) {
            let self = this;
            self._super();

            self.data = config.data;
            self._initFieldSets(config)
            self._initMapListener();

            self.observe('message couriers courier stepStates');
            self.observe('blpaczkaOrderedItem blpaczkaOrderedCartItem');
            self.observe('blpaczkaOrderedItemShippingLabelA4Link blpaczkaOrderedItemShippingLabelA6Link');
            self.observe('blpaczkaOrderedItemShippingLabelA4LinkValidation blpaczkaOrderedItemShippingLabelA6LinkValidation');

            self.blpaczkaOrderedItemShippingLabelA4Link(self.data.blpaczkaOrderedItemShippingLabelA4Link);
            self.blpaczkaOrderedItemShippingLabelA4LinkValidation(self.data.blpaczkaOrderedItemShippingLabelA4LinkValidation);
            self.blpaczkaOrderedItemShippingLabelA6Link(self.data.blpaczkaOrderedItemShippingLabelA6Link);
            self.blpaczkaOrderedItemShippingLabelA6LinkValidation(self.data.blpaczkaOrderedItemShippingLabelA6LinkValidation);
            self.blpaczkaOrderedCartItem(self.data.blpaczkaOrderedCartItem);
            self.blpaczkaOrderedItem(self.data.blpaczkaOrderedItem);
            self.stepStates({
                create: ko.pureComputed(function () {
                    return !self.blpaczkaOrderedItem();
                }),
                cancel: ko.pureComputed(function () {
                    return !!self.blpaczkaOrderedItem();
                }),
            })

            return self;
        },

        _initFieldSets: function (config) {
            let self = this;

            self.observe('fieldSets');

            let countryCode = self.data.fieldSets.shippingDetails.shipmentCountryCode,
                countryCodeLowerCase = countryCode.toString().toLowerCase();

            self.fieldSets({
                shippingDetails: {
                    title: $.mage.__('Shipping Details'),
                    class: 'shipping-details',
                    fields: {
                        shipmentSelectShippingMethod: {
                            title: $.mage.__('The Shipping Method selected by the customer'),
                            type: 'plaintext',
                            id: 'blpaczka_shippingdetails_selectedshippingmethod',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(true),
                            value: ko.pureComputed(function () {
                                return '[%1] %2'
                                    .replace('%1', self.data.fieldSets.shippingDetails.shipmentSelectedShippingMethodCode)
                                    .replace('%2', self.data.fieldSets.shippingDetails.shipmentSelectedShippingMethodDescription)
                            }),
                        },
                        shipmentCourierCode: {
                            title: $.mage.__('Courier'),
                            type: 'select',
                            id: 'blpaczka_shippingdetails_shipmentcouriercode',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            options: self.data.shippingMethods,
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentCourierCode)
                        },
                        shipmentCountryCode: {
                            title: $.mage.__('Country Code'),
                            type: 'text',
                            id: 'blpaczka_shippingdetails_shipmentcountrycode',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(true),
                            value: ko.observable(countryCode),
                        },
                        shipmentForeign: {
                            title: $.mage.__('Is Foreign?'),
                            type: 'select',
                            id: 'blpaczka_shippingdetails_shipmentforeign',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(true),
                            options: config.data.foreignTypes,
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentForeign),
                        },
                        shipmentType: {
                            title: $.mage.__('Shipment Type'),
                            type: 'select',
                            id: 'blpaczka_shippingdetails_shipmenttype',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            options: config.data.shipmentTypes,
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentType),
                        },
                        shipmentWeight: {
                            title: $.mage.__('Weight (kg)'),
                            type: 'number',
                            id: 'blpaczka_shippingdetails_shipmentweight',
                            validationRules: {
                                required: true,
                                'validate-number': true,
                                'validate-greater-than-zero': true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentWeight),
                        },
                        shipmentLength: {
                            title: $.mage.__('Length (cm)'),
                            type: 'number',
                            id: 'blpaczka_shippingdetails_shipmentlength',
                            validationRules: {
                                required: true,
                                'validate-number': true,
                                'validate-greater-than-zero': true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentLength),
                        },
                        shipmentWidth: {
                            title: $.mage.__('Width (cm)'),
                            type: 'number',
                            id: 'blpaczka_shippingdetails_shipmentwidth',
                            validationRules: {
                                required: true,
                                'validate-number': true,
                                'validate-greater-than-zero': true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentWidth),
                        },
                        shipmentHeight: {
                            title: $.mage.__('Height (cm)'),
                            type: 'number',
                            id: 'blpaczka_shippingdetails_shipmentheight',
                            validationRules: {
                                required: true,
                                'validate-number': true,
                                'validate-greater-than-zero': true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentHeight),
                        },
                        shipmentContent: {
                            title: $.mage.__('Contents of the shipment'),
                            type: 'text',
                            id: 'blpaczka_shippingdetails_shipmentcontent',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentContent),
                        },
                        shipmentInsuranceTotal: {
                            title: $.mage.__('Insurance Total (in PLN)'),
                            type: 'number',
                            id: 'blpaczka_shippingdetails_shipmentinsurancetotal',
                            validationRules: {
                                required: true,
                                'validate-number': true,
                                'validate-greater-than-zero': true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentInsuranceTotal),
                        },
                        shipmentCashOnDeliveryTotal: {
                            title: $.mage.__('Cash on Delivery Total (in PLN)'),
                            type: 'number',
                            id: 'blpaczka_shippingdetails_shipmentcashondeliverytotal',
                            validationRules: {
                                required: true,
                                'validate-number': true,
                                'validate-zero-or-greater': true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentCashOnDeliveryTotal),
                        },
                        shipmentSortable: {
                            title: $.mage.__('Sortable'),
                            type: 'checkbox',
                            id: 'blpaczka_shippingdetails_shipmentsortable',
                            validationRules: {
                                required: false,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentSortable),
                        },
                        shipmentWithoutCourierPickUp: {
                            title: $.mage.__('Without courier pick-up'),
                            type: 'checkbox',
                            id: 'blpaczka_shippingdetails_shipmentwithoutcourierpickup',
                            validationRules: {
                                required: false,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentWithoutCourierPickUp),
                        },
                        shipmentCourierArrivalDay: {
                            title: $.mage.__('Courier arrival day'),
                            type: 'date',
                            id: 'blpaczka_shippingdetails_shipmentcourierarrivalday',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentCourierArrivalDay),
                        },
                        shipmentCourierArrivalTimeFrom: {
                            title: $.mage.__('Time FROM which the courier can arrive:'),
                            type: 'time',
                            id: 'blpaczka_shippingdetails_shipmentcourierarrivaltimefrom',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentCourierArrivalTimeFrom),
                        },
                        shipmentCourierArrivalTimeTo: {
                            title: $.mage.__('Time BY which the courier can arrive:'),
                            type: 'time',
                            id: 'blpaczka_shippingdetails_shipmentcourierarrivaltimeto',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.shippingDetails.shipmentCourierArrivalTimeTo),
                        },
                    }
                },
                senderDetails: {
                    title: $.mage.__('Sender Details'),
                    class: 'sender-details',
                    fields: {
                        senderFullName: {
                            title: $.mage.__('Full name'),
                            type: 'text',
                            id: 'blpaczka_senderdetails_senderfullname',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.senderDetails.senderFullName),
                        },
                        senderCompany: {
                            title: $.mage.__('Company'),
                            type: 'text',
                            id: 'blpaczka_senderdetails_sendercompany',
                            validationRules: {
                                required: false,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.senderDetails.senderCompany),
                        },
                        senderEmail: {
                            title: $.mage.__('E-mail'),
                            type: 'email',
                            id: 'blpaczka_senderdetails_senderemail',
                            validationRules: {
                                required: true,
                                'validate-email': true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.senderDetails.senderEmail),
                        },
                        senderStreet: {
                            title: $.mage.__('Street Address'),
                            type: 'text',
                            id: 'blpaczka_senderdetails_senderstreet',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.senderDetails.senderStreet),
                        },
                        senderHouseNumber: {
                            title: $.mage.__('House Number'),
                            type: 'text',
                            id: 'blpaczka_senderdetails_senderhousenumber',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.senderDetails.senderHouseNumber),
                        },
                        senderApartmentNumber: {
                            title: $.mage.__('Apartment Number'),
                            type: 'text',
                            id: 'blpaczka_senderdetails_senderapartmentnumber',
                            validationRules: {
                                required: false,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.senderDetails.senderApartmentNumber),
                        },
                        senderPostCode: {
                            title: $.mage.__('Post Code'),
                            type: 'text',
                            id: 'blpaczka_senderdetails_senderpostcode',
                            validationRules: {
                                required: true,
                                'validate-zip-international': !['us', 'pl'].includes(countryCodeLowerCase),
                                'validate-zip-us': countryCodeLowerCase === 'us',
                                'pattern': countryCodeLowerCase === 'pl' ? '^[0-9]{2}-[0-9]{3}$' : false
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.senderDetails.senderPostCode),
                        },
                        senderCity: {
                            title: $.mage.__('City'),
                            type: 'text',
                            id: 'blpaczka_senderdetails_sendercity',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.senderDetails.senderCity),
                        },
                        senderPhoneNumber: {
                            title: $.mage.__('Phone Number'),
                            type: 'tel',
                            id: 'blpaczka_senderdetails_senderphonenumber',
                            validationRules: {
                                required: true,
                                'validate-phoneLax': !['us', 'gb', 'pl'].includes(countryCodeLowerCase),
                                'pattern': countryCodeLowerCase === 'pl' ? '^(?:(?:(?:(?:\\+|00)\\d{2})?[ -]?(?:(?:\\(0?\\d{2}\\))|(?:0?\\d{2})))?[ -]?(?:\\d{3}[- ]?\\d{2}[- ]?\\d{2}|\\d{2}[- ]?\\d{2}[- ]?\\d{3}|\\d{7})|(?:(?:(?:\\+|00)\\d{2})?[ -]?\\d{3}[ -]?\\d{3}[ -]?\\d{3}))$' : false,
                                'phoneUS': countryCodeLowerCase === 'us',
                                'phoneUK': countryCodeLowerCase === 'gb',
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.senderDetails.senderPhoneNumber),
                        },
                    }
                },
                recipientDetails: {
                    title: $.mage.__('Recipient Details'),
                    class: 'recipient-details',
                    fields: {
                        recipientFullName: {
                            title: $.mage.__('Full name'),
                            type: 'text',
                            id: 'blpaczka_recipientdetails_recipientfullname',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientFullName),
                        },
                        recipientCompany: {
                            title: $.mage.__('Company'),
                            type: 'text',
                            id: 'blpaczka_recipientdetails_recipientcompany',
                            validationRules: {
                                required: false,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientCompany),
                        },
                        recipientPhoneNumber: {
                            title: $.mage.__('Phone Number'),
                            type: 'tel',
                            id: 'blpaczka_recipientdetails_recipientphonenumber',
                            validationRules: {
                                required: true,
                                'validate-phoneLax': !['us', 'gb', 'pl'].includes(countryCodeLowerCase),
                                'pattern': countryCodeLowerCase === 'pl' ? '^(?:(?:(?:(?:\\+|00)\\d{2})?[ -]?(?:(?:\\(0?\\d{2}\\))|(?:0?\\d{2})))?[ -]?(?:\\d{3}[- ]?\\d{2}[- ]?\\d{2}|\\d{2}[- ]?\\d{2}[- ]?\\d{3}|\\d{7})|(?:(?:(?:\\+|00)\\d{2})?[ -]?\\d{3}[ -]?\\d{3}[ -]?\\d{3}))$' : false,
                                'phoneUS': countryCodeLowerCase === 'us',
                                'phoneUK': countryCodeLowerCase === 'gb',
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientPhoneNumber),
                        },
                        recipientPointNumber: {
                            title: $.mage.__('Point Number'),
                            type: 'text',
                            id: 'blpaczka_recipientdetails_recipientpointnumber',
                            requiredWhenCustomCourierSelected: ko.observable(false),
                            validationRules: {
                                required: false,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientPointNumber),
                            mapUrl: ko.pureComputed(function () {
                                let courierCode = self.fieldSets().shippingDetails.fields.shipmentCourierCode.value(),
                                    postCode = self.fieldSets().recipientDetails.fields.recipientPostCode.value(),
                                    mapUrl = self.data.mapUrl;

                                return mapUrl.replace('{blpaczkaMethodCode}', courierCode).replace('{postCode}', postCode);
                            })
                        },
                        recipientEmail: {
                            title: $.mage.__('E-mail'),
                            type: 'email',
                            id: 'blpaczka_recipientdetails_recipientemail',
                            validationRules: {
                                required: true,
                                'validate-email': true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientEmail),
                        },
                        recipientFullAddress: {
                            title: $.mage.__('Check if the address is correctly separated into parts'),
                            type: 'plaintext',
                            id: 'blpaczka_recipientdetails_recipientfulladdress',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(true),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientFullAddress),
                        },
                        recipientStreet: {
                            title: $.mage.__('Street Address'),
                            type: 'textarea',
                            id: 'blpaczka_recipientdetails_recipientstreet',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientStreet),
                        },
                        recipientHouseNumber: {
                            title: $.mage.__('House Number'),
                            type: 'text',
                            id: 'blpaczka_recipientdetails_recipienthousenumber',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientHouseNumber),
                        },
                        recipientApartmentNumber: {
                            title: $.mage.__('Apartment Number'),
                            type: 'text',
                            id: 'blpaczka_recipientdetails_recipientapartmentnumber',
                            validationRules: {
                                required: false,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientApartmentNumber),
                        },
                        recipientPostCode: {
                            title: $.mage.__('Post Code'),
                            type: 'text',
                            id: 'blpaczka_recipientdetails_recipientpostcode',
                            validationRules: {
                                required: true,
                                'validate-zip-international': !['us', 'pl'].includes(countryCodeLowerCase),
                                'validate-zip-us': countryCodeLowerCase === 'us',
                                'pattern': countryCodeLowerCase === 'pl' ? '^[0-9]{2}-[0-9]{3}$' : false
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientPostCode),
                        },
                        recipientCity: {
                            title: $.mage.__('City'),
                            type: 'text',
                            id: 'blpaczka_recipientdetails_recipientcity',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            value: ko.observable(self.data.fieldSets.recipientDetails.recipientCity),
                        },
                    }
                },
                paymentDetails: {
                    title: $.mage.__('Payment Details'),
                    class: 'payment-details',
                    fields: {
                        paymentType: {
                            title: $.mage.__('Payment Type'),
                            type: 'select',
                            id: 'blpaczka_paymentdetails_paymenttype',
                            validationRules: {
                                required: true,
                            },
                            disabled: ko.observable(false),
                            options: self.data.paymentTypes,
                            value: ko.observable(self.data.fieldSets.paymentDetails.paymentType),
                        },
                    }
                }
            })

            let shipmentWithoutCourierPickUpSubscription = function () {
                if (self.fieldSets().shippingDetails.fields.shipmentWithoutCourierPickUp.value()) {
                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalDay.disabled(true);
                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalTimeFrom.disabled(true);
                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalTimeTo.disabled(true);

                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalDay.value(null);
                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalTimeFrom.value(null);
                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalTimeTo.value(null);
                } else {
                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalDay.disabled(false);
                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalTimeFrom.disabled(false);
                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalTimeTo.disabled(false);

                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalDay.value(
                        self.data.fieldSets.shippingDetails.shipmentCourierArrivalDay
                    );
                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalTimeFrom.value(
                        self.data.fieldSets.shippingDetails.shipmentCourierArrivalTimeFrom
                    );
                    self.fieldSets().shippingDetails.fields.shipmentCourierArrivalTimeTo.value(
                        self.data.fieldSets.shippingDetails.shipmentCourierArrivalTimeTo
                    );
                }

                if ($(`#${self.formId}`).length > 0) {
                    self.validateForm();
                }
            }
            let pointNumberDisabledSubscription = function () {
                let courierCode = self.fieldSets().shippingDetails.fields.shipmentCourierCode.value(),
                    isPUDOAvailable = self.data.couriersWithPUDOAvailable.includes(courierCode),
                    isPUDORequired = self.data.couriersWithPUDORequired.includes(courierCode);

                self.fieldSets().recipientDetails.fields.recipientPointNumber.disabled(!isPUDOAvailable);
                self.fieldSets().recipientDetails.fields.recipientPointNumber.requiredWhenCustomCourierSelected(isPUDORequired);

                if ($(`#${self.formId}`).length > 0) {
                    self.validateForm();
                }
            }

            shipmentWithoutCourierPickUpSubscription();
            pointNumberDisabledSubscription();

            self.fieldSets().shippingDetails.fields.shipmentWithoutCourierPickUp.value.subscribe(function () {
                shipmentWithoutCourierPickUpSubscription();
            });
            self.fieldSets().shippingDetails.fields.shipmentCourierCode.value.subscribe(function () {
                pointNumberDisabledSubscription();
            });
        },

        validateElement: function (element) {
            return $(`#${element.id}`).valid();
        },

        validateForm: function () {
            let self = this,
                formSelector = `#${self.formId}`,
                pointNumberValue = self.fieldSets().recipientDetails.fields.recipientPointNumber.value(),
                isRequiredByCourierSelect = self.fieldSets().recipientDetails.fields.recipientPointNumber.requiredWhenCustomCourierSelected();

            if (isRequiredByCourierSelect && !pointNumberValue) {
                self.message({
                    error: true,
                    title: $.mage.__('Point Number is required for the selected courier!')
                });

                return false;
            }

            return $(formSelector).valid();
        },

        createBLPaczkaOrder: function () {
            let self = this;

            return self._sendRequest(
                self.data.createOrderUrl,
                self._prepareRequestData(),
                true,
                function () {
                    self.couriers(null);
                    self.courier(null);

                    self.blpaczkaOrderedCartItem(null);
                    self.blpaczkaOrderedItem(null);
                    self.blpaczkaOrderedItemShippingLabelA4Link(null);
                    self.blpaczkaOrderedItemShippingLabelA6Link(null);
                },
                function () {
                    window.location.reload();
                }
            );
        },

        getValuation: function (validateForm = true, chooseFirst = false) {
            let self = this;

            return self._sendRequest(
                self.data.getValuationUrl,
                self._prepareRequestData(),
                validateForm,
                function () {
                    self.couriers(null);
                    self.courier(null);
                },
                function (data) {
                    self.couriers(data);

                    if (chooseFirst && data && data[0]) {
                        self.chooseCourier(data[0]);
                    }
                }
            );
        },

        chooseCourier: function (courier) {
            let self = this;

            self.courier(courier);
            self.fieldSets().shippingDetails.fields.shipmentCourierCode.value(courier.Courier.courier_code);
            self.fieldSets().recipientDetails.fields.recipientPointNumber.requiredWhenCustomCourierSelected(false);

            if (courier.Courier.custom_pickup_enable) {
                self.fieldSets().shippingDetails.fields.shipmentWithoutCourierPickUp.disabled(false);
                self.fieldSets().shippingDetails.fields.shipmentWithoutCourierPickUp.value(
                    self.data.fieldSets.shippingDetails.shipmentWithoutCourierPickUp
                );
            } else {
                self.fieldSets().shippingDetails.fields.shipmentWithoutCourierPickUp.disabled(true);
                self.fieldSets().shippingDetails.fields.shipmentWithoutCourierPickUp.value(true);
            }

            if (courier.Courier.taker_point_required) {
                self.fieldSets().recipientDetails.fields.recipientPointNumber.requiredWhenCustomCourierSelected(courier.Courier.taker_point_required);
            }

            self.message(null);
            self.validateForm();
        },

        downloadShippingLabel: function (validationUrl, downloadUrl) {
            let self = this;

            self.message(null);

            $.ajax({
                showLoader: true,
                url: validationUrl,
                data: {form_key: window.FORM_KEY},
                type: 'POST',
                dataType: 'json'
            })
                .done(function (response) {
                    if (!!response.success) {
                        window.location.href = downloadUrl;
                        self.message({
                            error: false,
                            title: response.message,
                        });
                    } else {
                        self.message({
                            error: true,
                            title: response.message,
                        });
                    }
                })
                .fail(function () {
                    window.location.reload();
                })
        },

        cancelBLPaczkaOrderedItem: function () {
            let self = this,
                confirmed = confirm(
                    $.mage.__('Are you sure you want to cancel this package?')
                );

            if (!confirmed) {
                return;
            }

            self._sendRequest(
                self.data.cancelOrderUrl,
                self._prepareRequestData(),
                false,
                function () {},
                function () {
                    window.location.reload();
                }
            );
        },

        _initMapListener: function () {
            let self = this;

            window.addEventListener('message', function (event) {
                let isMapEvent = event.origin === self.data.mapOriginUrl;

                if (event.data.type === 'SELECT_CHANGE' && isMapEvent) {
                    let point = event.data.value;

                    self.fieldSets().recipientDetails.fields.recipientPointNumber.value(point.name);
                    self.message(null);
                    self.validateForm();
                    self.closeMap();
                }
            });
        },

        mapTitle: function () {
            return this.fieldSets().recipientDetails.fields.recipientPointNumber.value()
                ? $.mage.__('Change collection point')
                : $.mage.__('Select a collection point');
        },

        openMap: function (selector) {
            $(selector).modal('openModal');
        },

        closeMap: function () {
            $('.blpaczka-map-modal').closest('aside[role="dialog"]').find('.action.close').click();
        },

        _prepareRequestData: function () {
            let self = this,
                pointNumberDisabled = self.fieldSets().recipientDetails.fields.recipientPointNumber.disabled();

            return {
                'shippingDetails': {
                    'shipmentSelectedShippingMethodDescription': self.data.fieldSets.shippingDetails.shipmentSelectedShippingMethodDescription,
                    'shipmentSelectedShippingMethodCode': self.data.fieldSets.shippingDetails.shipmentSelectedShippingMethodCode,
                    'shipmentCourierCode': self.fieldSets().shippingDetails.fields.shipmentCourierCode.value(),
                    'shipmentCountryCode': self.fieldSets().shippingDetails.fields.shipmentCountryCode.value(),
                    'shipmentType': self.fieldSets().shippingDetails.fields.shipmentType.value(),
                    'shipmentWeight': self.fieldSets().shippingDetails.fields.shipmentWeight.value(),
                    'shipmentLength': self.fieldSets().shippingDetails.fields.shipmentLength.value(),
                    'shipmentWidth': self.fieldSets().shippingDetails.fields.shipmentWidth.value(),
                    'shipmentHeight': self.fieldSets().shippingDetails.fields.shipmentHeight.value(),
                    'shipmentContent': self.fieldSets().shippingDetails.fields.shipmentContent.value(),
                    'shipmentInsuranceTotal': self.fieldSets().shippingDetails.fields.shipmentInsuranceTotal.value(),
                    'shipmentCashOnDeliveryTotal': self.fieldSets().shippingDetails.fields.shipmentCashOnDeliveryTotal.value(),
                    'shipmentSortable': self.fieldSets().shippingDetails.fields.shipmentSortable.value(),
                    'shipmentWithoutCourierPickUp': self.fieldSets().shippingDetails.fields.shipmentWithoutCourierPickUp.value(),
                    'shipmentCourierArrivalDay': self.fieldSets().shippingDetails.fields.shipmentCourierArrivalDay.value(),
                    'shipmentCourierArrivalTimeFrom': self.fieldSets().shippingDetails.fields.shipmentCourierArrivalTimeFrom.value(),
                    'shipmentCourierArrivalTimeTo': self.fieldSets().shippingDetails.fields.shipmentCourierArrivalTimeTo.value(),
                    'shipmentForeign': self.fieldSets().shippingDetails.fields.shipmentForeign.value(),
                },
                'senderDetails': {
                    'senderFullName': self.fieldSets().senderDetails.fields.senderFullName.value(),
                    'senderCompany': self.fieldSets().senderDetails.fields.senderCompany.value(),
                    'senderEmail': self.fieldSets().senderDetails.fields.senderEmail.value(),
                    'senderStreet': self.fieldSets().senderDetails.fields.senderStreet.value(),
                    'senderHouseNumber': self.fieldSets().senderDetails.fields.senderHouseNumber.value(),
                    'senderApartmentNumber': self.fieldSets().senderDetails.fields.senderApartmentNumber.value(),
                    'senderPostCode': self.fieldSets().senderDetails.fields.senderPostCode.value(),
                    'senderCity': self.fieldSets().senderDetails.fields.senderCity.value(),
                    'senderPhoneNumber': self.fieldSets().senderDetails.fields.senderPhoneNumber.value(),
                },
                'recipientDetails': {
                    'recipientFullName': self.fieldSets().recipientDetails.fields.recipientFullName.value(),
                    'recipientCompany': self.fieldSets().recipientDetails.fields.recipientCompany.value(),
                    'recipientPhoneNumber': self.fieldSets().recipientDetails.fields.recipientPhoneNumber.value(),
                    'recipientPointNumber': pointNumberDisabled ? '' : self.fieldSets().recipientDetails.fields.recipientPointNumber.value(),
                    'recipientEmail': self.fieldSets().recipientDetails.fields.recipientEmail.value(),
                    'recipientFullAddress': self.fieldSets().recipientDetails.fields.recipientFullAddress.value(),
                    'recipientStreet': self.fieldSets().recipientDetails.fields.recipientStreet.value(),
                    'recipientHouseNumber': self.fieldSets().recipientDetails.fields.recipientHouseNumber.value(),
                    'recipientApartmentNumber': self.fieldSets().recipientDetails.fields.recipientApartmentNumber.value(),
                    'recipientPostCode': self.fieldSets().recipientDetails.fields.recipientPostCode.value(),
                    'recipientCity': self.fieldSets().recipientDetails.fields.recipientCity.value(),
                },
                'paymentDetails': {
                    'paymentType': self.fieldSets().paymentDetails.fields.paymentType.value(),
                }
            };
        },

        _sendRequest: function (url, data, validateForm, clearDataCallback, setDataCallback) {
            let self = this;

            if (validateForm && !self.validateForm()) {
                return null;
            }

            self.message(null);
            clearDataCallback();

            return $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    form_key: window.FORM_KEY,
                    orderId: self.data.orderId,
                    BLPaczkaData: JSON.stringify(data),
                },
                showLoader: true,
            }).done(function (response) {
                let success = response['status'] === 1,
                    message = response['message'] ?? $.mage.__('Something went wrong!'),
                    data = response['data'] ?? null;

                if (success) {
                    setDataCallback(data);
                }

                if (!success) {
                    self.message({
                        error: !success,
                        title: message && message.length > 0 ? message : $.mage.__('Something went wrong!')
                    })
                }
            }).fail(function () {
                self.message({
                    error: true,
                    title: $.mage.__('Something went wrong!')
                })
            })
        },
    });
});
