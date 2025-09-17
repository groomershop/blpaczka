define([
    'jquery',
    'underscore',
], function ($, _) {
    'use strict';

    return function (Component) {
        return Component.extend({
            _getCallback: function (action, selections) {
                let self = this,
                    originalCallback = self._super(action, selections);

                if (action.isBLPaczkaLink) {
                    return function () {
                        $('body').trigger('processStart');

                        return originalCallback();
                    }
                }

                if (action.isBLPaczkaFile && action.blpaczkaDownloadUrl && action.blpaczkaValidationUrl) {
                    return function () {
                        self._clearOrCreateMessages();

                        $.ajax({
                            showLoader: true,
                            url: action.url.replace(action.blpaczkaDownloadUrl, action.blpaczkaValidationUrl),
                            data: self._getUrlParams(selections),
                            type: 'POST',
                            dataType: 'json'
                        })
                            .done(function (response) {
                                if (!!response.success) {
                                    originalCallback();
                                    self._addSuccessMessage(response.message);
                                } else {
                                    self._addErrorMessage(response.message);
                                }
                            })
                            .fail(function () {
                                window.location.reload();
                            })
                    }
                }

                return originalCallback;
            },

            _addSuccessMessage: function (message) {
                $('#messages .messages').append(`<div class="message message-success success">${message}</div>`);
                window.scrollTo(0, 0);
            },

            _addErrorMessage: function (message) {
                $('#messages .messages').append(`<div class="message message-error error">${message}</div>`);
                window.scrollTo(0, 0);
            },

            _clearOrCreateMessages: function () {
                if ($('#messages').length === 0) {
                    $('.page-content').prepend('<div id="messages"><div class="messages"></div></div>');
                } else {
                    $('#messages .messages .message').remove();
                }
            },

            _getUrlParams: function (data) {
                let itemsType = data.excludeMode ? 'excluded' : 'selected',
                    selections = {};

                selections[itemsType] = data[itemsType];

                if (!selections[itemsType].length) {
                    selections[itemsType] = false;
                }

                _.extend(selections, data.params || {});

                selections['form_key'] = window.FORM_KEY;

                return selections;
            },
        })
    }
})
