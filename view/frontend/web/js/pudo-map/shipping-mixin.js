/**
 * @category  BLPaczka
 * @package   BLPaczka\MagentoIntegration
 * @copyright 2024 Copyright (c) BLPaczka (https://blpaczka.com)
 *
 */
define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/shipping-service',
    'mage/translate'
], function ($, ko, quote, modal, shippingService) {
    'use strict';

    const blpaczkaPointName = ko.observable(null),
          blpaczkaPointHtml = ko.observable(null)

    const mapComponent = {
        listeners: {},
        init: function (shippingMethod) {
            if (!mapComponent.isShippingMethodValid(shippingMethod)) {
                return;
            }

            let maxCount = 1000,
                counter = 0,
                preStatus = mapComponent.initMapExtension(shippingMethod);

            if (!preStatus) {
                let intervalCallback = setInterval(function () {
                    let status = mapComponent.initMapExtension(shippingMethod);

                    counter++;

                    if (counter > maxCount || status) {
                        clearInterval(intervalCallback);
                    }
                }, 100);
            }

            mapComponent.initListener(shippingMethod);
        },

        isShippingMethodValid: function (shippingMethod) {
            return !!shippingMethod['extension_attributes'] && shippingMethod['carrier_code'] == 'blpaczka';
        },

        getShippingMethodData: function (shippingMethod) {
            let methodLabelId = `label_carrier_${shippingMethod['method_code']}_${shippingMethod['carrier_code']}`,
                labelSelector = `#${methodLabelId}`,
                postCode = quote && quote.shippingAddress ? quote.shippingAddress().postcode : '',
                mapUrl = shippingMethod['extension_attributes']['blpaczka_rate']['pudo_map_url'];

            postCode = postCode ? postCode.toString().replace('-', '') : '';
            mapUrl = mapUrl.replace('{postCode}', postCode);

            return {
                carrierCode: shippingMethod['carrier_code'],
                methodCode: shippingMethod['method_code'],
                methodTitle: shippingMethod['method_title'],
                labelSelector: labelSelector,
                inputSelector: `input[aria-labelledby*="${methodLabelId}"]`,
                isPUDO: shippingMethod['extension_attributes']['blpaczka_rate']['is_pudo'],
                pudoMapUrl: mapUrl,
                mapOriginUrl: shippingMethod['extension_attributes']['blpaczka_rate']['map_origin_url'],

                shippingPointIdInput: `$('[name="swissup_checkout_field[shipping_point_id]"]')`,
                shippingPointNameInput: `$('[name="swissup_checkout_field[shipping_point_name]"]')`,

                mapInfoSelectorAll: `.blpaczka-map-info`,
                mapActionSelectorAll: `.blpaczka-map-action`,
                mapInfoSelector: `${labelSelector} .blpaczka-map-info`,
                mapActionSelector: `${labelSelector} .blpaczka-map-action`,
                mapActionInitMessage: $.mage.__('Select a collection point'),
                mapActionSelectedMessage: $.mage.__('Selected: %1. '),
                mapActionUpdateMessage: $.mage.__('Change collection point'),
                modalGlobalSelector: `.map-modal[data-blpaczka-label-id="${labelSelector}"]`,
                modalLocalSelector: `${labelSelector} .map-modal`,
                popupTitle: $.mage.__('%1: Collection points map').replace('%1', shippingMethod['method_title']),
                popupClose: $.mage.__('Close'),
            }
        },

        _isLabelExists: function (shippingMethod) {
            let shippingMethodData = mapComponent.getShippingMethodData(shippingMethod);

            return $(shippingMethodData.labelSelector).length > 0;
        },

        _isInputChecked: function (shippingMethod) {
            let shippingMethodData = mapComponent.getShippingMethodData(shippingMethod);

            return $(shippingMethodData.inputSelector).is(':checked');
        },

        _isWaitingForVisibility: function (shippingMethod) {
            return !mapComponent._isLabelExists(shippingMethod) && !mapComponent._isInputChecked(shippingMethod);
        },

        _isExistsButNotChecked: function (shippingMethod) {
            return mapComponent._isLabelExists(shippingMethod)  && !mapComponent._isInputChecked(shippingMethod);
        },

        _isExistsAndChecked: function (shippingMethod) {
            return mapComponent._isLabelExists(shippingMethod) && mapComponent._isInputChecked(shippingMethod);
        },

        initMapExtension: function (shippingMethod) {
            let statusOk = true,
                statusFailedAndRetry = false;

            if (!mapComponent.isShippingMethodValid(shippingMethod)) {
                return statusOk;
            }

            let shippingMethodData = mapComponent.getShippingMethodData(shippingMethod);

            if (mapComponent._isWaitingForVisibility(shippingMethod)) {
                return statusFailedAndRetry;
            }

            if (mapComponent._isExistsButNotChecked(shippingMethod)) {
                return statusOk;
            }

            if (shippingMethodData.isPUDO) {
                if (!mapComponent._isLabelExists(shippingMethod)) {
                    return statusFailedAndRetry;
                }

                if (blpaczkaPointName()) {
                    mapComponent.showMapInfo(shippingMethodData, blpaczkaPointHtml());
                    mapComponent.setPUDOPoint({
                        name: blpaczkaPointName(),
                        html: blpaczkaPointHtml(),
                    });
                    $(shippingMethodData.mapInfoSelector).show(0);

                }

                if ($(shippingMethodData.modalGlobalSelector).length > 0) {
                    $(shippingMethodData.modalGlobalSelector).find('iframe').attr('src', shippingMethodData.pudoMapUrl);
                }

                if ($(shippingMethodData.mapActionSelector).length > 0) {
                    $(shippingMethodData.mapActionSelector).show(0);
                } else {
                    $(shippingMethodData.labelSelector).append(`<p class="blpaczka-map-action"><a href="#"></a></p>`);
                    $(shippingMethodData.mapActionSelector).find('a').text(shippingMethodData.mapActionInitMessage);

                    $(shippingMethodData.mapActionSelector).find('a').click(function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();

                        if ($(shippingMethodData.modalGlobalSelector).length === 0) {
                            $(shippingMethodData.labelSelector).append(`<div class="map-modal"></div>`);

                            $(shippingMethodData.modalLocalSelector).css({
                                    'width': '1000px',
                                    'height': 'calc(90vh - 28rem)',
                                    'max-width': '100%',
                                    'max-height': '100vh',
                                    'min-height': '500px',
                                })
                                .attr('data-blpaczka-label-id', shippingMethodData.labelSelector)
                                .append(`<iframe></iframe>`)

                            $(shippingMethodData.modalLocalSelector).find('iframe').attr({
                                'width': '100%',
                                'height': '100%',
                                'src': shippingMethodData.pudoMapUrl
                            })

                            modal({
                                type: 'popup',
                                responsive: true,
                                innerScroll: true,
                                title: shippingMethodData.popupTitle,
                                buttons: [{
                                    text: shippingMethodData.popupClose,
                                    class: '',
                                    click: function () {
                                        this.closeModal();
                                    }
                                }]
                            }, $(shippingMethodData.modalLocalSelector));
                        }

                        $(shippingMethodData.modalGlobalSelector).modal('openModal');
                    });
                }

                if ($(shippingMethodData.mapActionSelector).length > 0) {
                    return statusOk;
                } else {
                    return statusFailedAndRetry;
                }
            }

            return statusOk;
        },

        showMapInfo: function (shippingMethodData, pointHtml) {
            if ($(shippingMethodData.mapInfoSelector).length === 0) {
                $(shippingMethodData.labelSelector).append(`<p class="blpaczka-map-info">${pointHtml}</p>`);
            } else {
                $(shippingMethodData.mapInfoSelector).html(pointHtml);
            }
        },

        initListener: function (shippingMethod) {
            if (!mapComponent.isShippingMethodValid(shippingMethod)) {
                return;
            }

            let shippingMethodData = mapComponent.getShippingMethodData(shippingMethod);

            if (mapComponent.listeners[shippingMethodData.methodCode]) {
                return;
            }

            window.addEventListener('message', function (event) {
                let modalParent = $(shippingMethodData.modalGlobalSelector).closest('aside[role="dialog"]'),
                    isModalVisible = $(modalParent).hasClass('_show'),
                    mapOriginUrl = shippingMethodData.mapOriginUrl,
                    isMapEvent = event.origin === mapOriginUrl,
                    isMapEventAlternative = event.origin === mapOriginUrl.replace('send.blpaczka', 'api.blpaczka');

                if (event.data.type === 'SELECT_CHANGE' && isModalVisible && (isMapEvent || isMapEventAlternative)) {
                    let point = event.data.value;

                    $(shippingMethodData.mapActionSelector).find('a').text(shippingMethodData.mapActionSelectedMessage.replace('%1', point['name']) + '<br />' + shippingMethodData.mapActionUpdateMessage);
                    mapComponent.showMapInfo(shippingMethodData, point['pointData']);

                    blpaczkaPointName(point['name'])
                    blpaczkaPointHtml(point['pointData'])

                    mapComponent.setPUDOPoint({
                        name: point['name'],
                        html: point['pointData'],
                    });

                    $(modalParent).find('.action-close').click();

                    if (shippingMethodData.shippingPointIdInput !== null) {
                    	shippingMethodData.shippingPointIdInput.val(point['name']).trigger('keyup');
                    }
                    if (shippingMethodData.shippingPointNameInput !== null) {
                    	shippingMethodData.shippingPointNameInput.val('Punkt ' + point['name']).trigger('keyup');
                    }
                }

                mapComponent.listeners[shippingMethodData.methodCode] = true;
            });
        },

        setPUDOPoint: function (point) {
            if (quote && quote.shippingAddress()) {
                let shippingAddress = quote.shippingAddress()

                if (!shippingAddress.extension_attributes) {
                    shippingAddress.extension_attributes = {};
                }

                shippingAddress.extension_attributes.blpaczka_pudo_point = point ? JSON.stringify(point) : null;

                quote.shippingAddress(shippingAddress)
            }
        },

        resetAll: function () {
            blpaczkaPointName(null)
            blpaczkaPointHtml(null)
            $('.blpaczka-map-info').hide(0);
            $('.blpaczka-map-action').hide(0);
            mapComponent.setPUDOPoint(null);
        },

        initAll: function (rates) {
            for (let rate of rates) {
                mapComponent.init(rate);
            }
        }
    }


    return function (Component) {
        return Component.extend({
            defaults: {
                shippingMethodListTemplate: {
                    name: Component.defaults.shippingMethodListTemplate,
                    afterRender: function (renderedNodesArray, data) {
                        let rates = shippingService.getShippingRates();
                        if (rates) {
                            mapComponent.initAll(rates());
                        }

                        shippingService.getShippingRates().subscribe(function (rates) {
                            if (rates) {
                                mapComponent.initAll(rates);
                            }
                        })
                    }
                }
            },

            selectShippingMethod: function (shippingMethod) {
                let result = this._super(shippingMethod);

                mapComponent.resetAll();
                mapComponent.init(shippingMethod);

                return result;
            },

            validateShippingInformation: function () {
                let result = this._super()

                if (blpaczkaPointName()) {
                    mapComponent.setPUDOPoint({
                        name: blpaczkaPointName(),
                        html: blpaczkaPointHtml(),
                    });
                }

                let rates = shippingService.getShippingRates();
                if (rates) {
                    mapComponent.initAll(rates());
                }

                let shippingMethod = quote.shippingMethod(),
                    shippingAddress = quote.shippingAddress();

                if (
                    shippingMethod
                    && shippingMethod['carrier_code'] === 'blpaczka'
                    && shippingMethod['extension_attributes']
                    && shippingMethod['extension_attributes']['blpaczka_rate']
                    && !!shippingMethod['extension_attributes']['blpaczka_rate']['is_pudo']
                ) {
                    let blpaczkaPudoPointExists = false,
                        shippingAddressExtensionAttributes = shippingAddress ? shippingAddress['extension_attributes'] : {},
                        blpaczkaPudoPoint = shippingAddressExtensionAttributes ? shippingAddressExtensionAttributes.blpaczka_pudo_point : null

                    if (blpaczkaPudoPoint) {
                        blpaczkaPudoPointExists = true;
                    }

                    if (!blpaczkaPudoPointExists) {
                        this.errorValidationMessage(
                            $.mage.__('The collection point is required. Open the map and select the point.')
                        );

                        return false;
                    }
                }

                return result;
            }
        });
    };
});
