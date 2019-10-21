/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'ko',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/shipping-rate-service',
    'jquery/validate'
], function (
    $,
    _,
    Component,
    ko,
    customer,
    addressList,
    addressConverter,
    quote,
    createShippingAddress,
    selectShippingAddress,
    shippingRatesValidator,
    formPopUpState,
    shippingService,
    selectShippingMethodAction,
    rateRegistry,
    setShippingInformationAction,
    stepNavigator,
    modal,
    checkoutDataResolver,
    checkoutData,
    registry,
    $t
) {
    'use strict';

    var popUp = null;

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping',
            shippingFormTemplate: 'Magento_Checkout/shipping-address/form',
            shippingMethodListTemplate: 'Magento_Checkout/shipping-address/shipping-method-list',
            shippingMethodItemTemplate: 'Magento_Checkout/shipping-address/shipping-method-item'
        },
        visible: ko.observable(!quote.isVirtual()),
        errorValidationMessage: ko.observable(false),
        isCustomerLoggedIn: customer.isLoggedIn,
        isFormPopUpVisible: formPopUpState.isVisible,
        isFormInline: addressList().length === 0,
        isNewAddressAdded: ko.observable(false),
        saveInAddressBook: 1,
        quoteIsVirtual: quote.isVirtual(),

        /**
         * @return {exports}
         */
        initialize: function () {
            var self = this;
            var checkIfPhoneFieldExist = setInterval(function(){
                if($('#shipping-new-address-form input[name="telephone"]').length){
                    self.renderPhoneForm();
                    clearInterval(checkIfPhoneFieldExist);
                }
            }, 500);
            var self = this,
                hasNewAddress,
                fieldsetName = 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset';

            this._super();

            if (!quote.isVirtual()) {
                stepNavigator.registerStep(
                    'shipping',
                    '',
                    $t('Shipping'),
                    this.visible, _.bind(this.navigate, this),
                    10
                );
            }
            checkoutDataResolver.resolveShippingAddress();

            hasNewAddress = addressList.some(function (address) {
                return address.getType() == 'new-customer-address'; //eslint-disable-line eqeqeq
            });

            this.isNewAddressAdded(hasNewAddress);

            this.isFormPopUpVisible.subscribe(function (value) {
                if (value) {
                    self.getPopUp().openModal();
                }
            });

            quote.shippingMethod.subscribe(function () {
                self.errorValidationMessage(false);
            });

            registry.async('checkoutProvider')(function (checkoutProvider) {
                var shippingAddressData = checkoutData.getShippingAddressFromData();

                if (shippingAddressData) {
                    checkoutProvider.set(
                        'shippingAddress',
                        $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                    );
                }
                checkoutProvider.on('shippingAddress', function (shippingAddrsData) {
                    checkoutData.setShippingAddressFromData(shippingAddrsData);
                });
                shippingRatesValidator.initFields(fieldsetName);

            });
            return this;
        },

        /**
         * Navigator change hash handler.
         *
         * @param {Object} step - navigation step
         */
        navigate: function (step) {
            step && step.isVisible(true);
        },

        registerPhone: function(id){
            var phoneCode = $('#'+id+' .countryPhoneCode').find(":selected").data('phone-code');
            var localPhoneNumber = $('#'+id+' .localTelephoneNumber').val();
            var internationalPhoneNumber = phoneCode + localPhoneNumber;
            $('#'+id+' input[name="telephone"]').val(internationalPhoneNumber).change();
        },

        validatePhone: function(id){
            var localPhoneNumber = $('#'+id+' .localTelephoneNumber').first().val();
            var localPhoneNumberArray = localPhoneNumber.split('');
            var outputlocalPhoneNumberArray = [];
            $.each(localPhoneNumberArray, function( index, value ) {
                if((value === "0" || value === "٠") && outputlocalPhoneNumberArray.length == 0){
                } else if (value === "٠"){
                    outputlocalPhoneNumberArray.push("0");
                } else if (value === "١"){
                    outputlocalPhoneNumberArray.push("1");
                } else if (value === "٢"){
                    outputlocalPhoneNumberArray.push("2");
                } else if (value === "٣"){
                    outputlocalPhoneNumberArray.push("3");
                } else if (value === "٤"){
                    outputlocalPhoneNumberArray.push("4");
                } else if (value === "٥"){
                    outputlocalPhoneNumberArray.push("5");
                } else if (value === "٦"){
                    outputlocalPhoneNumberArray.push("6");
                } else if (value === "٧"){
                    outputlocalPhoneNumberArray.push("7");
                } else if (value === "٨"){
                    outputlocalPhoneNumberArray.push("8");
                } else if (value === "٩"){
                    outputlocalPhoneNumberArray.push("9");
                } else if(value === "0" || value === "1" || value === "2" || value === "3" || value === "4" || value === "5" || value === "6" || value === "7" || value === "8" || value === "9"){
                    outputlocalPhoneNumberArray.push(value);
                } else {
                    // do nothing
                }
            });
            var outputlocalPhoneNumber = outputlocalPhoneNumberArray.join("");
            $('#'+id+' .localTelephoneNumber').val(outputlocalPhoneNumber);
        },
        renderPhoneForm: function(){
          var self = this;
          if(!$('#shipping-new-address-form .phoneArea').length){


            var defaultCountry = window.checkoutConfig.default_country_config;
            var allowedCountries = window.checkoutConfig.allowed_countries_config;
            var countriesPhonesList = window.checkoutConfig.countries_phones_list;
            var countriesPhonesList = JSON.parse(countriesPhonesList);
            var allowedCountries = allowedCountries.split(',');

            var html = '<div class="phoneArea">';
                  html += '<div class="encloser" style="width: 100px;display: inline-block;float: left;">';
                    html += '<select class="countryPhoneCode" style="width: 140px;box-shadow: unset !important;pointer-events: none;">';
                      $.each(allowedCountries, function( index, countryCode ) {
                          var countryInformation = countriesPhonesList[countryCode];
                        html += '<option data-phone-code="+'+ countryInformation["phone"][0] +'" value="'+ countryCode +'">+'+ countryInformation["phone"][0] +'</option>';
                      });
                    html += '</select>';
                  html += '</div>';
                html += '<input type="text" class="input-text localTelephoneNumber required-entry" style="width: calc(100% - 136px);display: inline-block;">';
                /*
                $.each(allowedCountries, function( index, countryCode ) {
                    var countryInformation = countriesPhonesList[countryCode];
                  html += '<input type="text" class="input-text localTelephoneNumber required-entry phone-length-'+countryCode+'" style="width: calc(100% - 136px);display: inline-block;">';
                });
                */

              html += '</div>';

            $('#shipping-new-address-form input[name="telephone"]').hide();
            $('#shipping-new-address-form input[name="telephone"]').before( html );

            $("#shipping-new-address-form .localTelephoneNumber").first().on('change keydown paste input', function(){
                self.validatePhone("shipping-new-address-form");
                self.registerPhone("shipping-new-address-form");
            });
            $('#shipping-new-address-form select[name="country_id"]').change(function () {
                var end = this.value;
                $("#shipping-new-address-form .countryPhoneCode").first().val(end).change();
            });
            if($('#shipping-new-address-form select[name="country_id"]').val()){
              $("#shipping-new-address-form .countryPhoneCode").first().val($('#shipping-new-address-form select[name="country_id"]').val()).change();
            }
          }
        },
        /**
         * @return {*}
         */
        getPopUp: function () {
            var self = this,
                buttons;
            if (!popUp) {
                buttons = this.popUpForm.options.buttons;
                this.popUpForm.options.buttons = [
                    {
                        text: buttons.save.text ? buttons.save.text : $t('Save Address'),
                        class: buttons.save.class ? buttons.save.class : 'action primary action-save-address',
                        click: self.saveNewAddress.bind(self)
                    },
                    {
                        text: buttons.cancel.text ? buttons.cancel.text : $t('Cancel'),
                        class: buttons.cancel.class ? buttons.cancel.class : 'action secondary action-hide-popup',

                        /** @inheritdoc */
                        click: this.onClosePopUp.bind(this)
                    }
                ];

                /** @inheritdoc */
                this.popUpForm.options.closed = function () {
                    self.isFormPopUpVisible(false);
                };

                this.popUpForm.options.modalCloseBtnHandler = this.onClosePopUp.bind(this);
                this.popUpForm.options.keyEventHandlers = {
                    escapeKey: this.onClosePopUp.bind(this)
                };

                /** @inheritdoc */
                this.popUpForm.options.opened = function () {
                    // Store temporary address for revert action in case when user click cancel action
                    self.temporaryAddress = $.extend(true, {}, checkoutData.getShippingAddressFromData());
                };
                popUp = modal(this.popUpForm.options, $(this.popUpForm.element));
            }

            return popUp;
        },

        /**
         * Revert address and close modal.
         */
        onClosePopUp: function () {
            checkoutData.setShippingAddressFromData($.extend(true, {}, this.temporaryAddress));
            this.getPopUp().closeModal();
        },

        /**
         * Show address form popup
         */
        showFormPopUp: function () {
            this.isFormPopUpVisible(true);
        },

        /**
         * Save new shipping address
         */
        saveNewAddress: function () {
            var addressData,
                newShippingAddress;

            this.source.set('params.invalid', false);
            this.triggerShippingDataValidateEvent();

            if (!this.source.get('params.invalid')) {
                addressData = this.source.get('shippingAddress');
                // if user clicked the checkbox, its value is true or false. Need to convert.
                addressData['save_in_address_book'] = this.saveInAddressBook ? 1 : 0;

                // New address must be selected as a shipping address
                newShippingAddress = createShippingAddress(addressData);
                selectShippingAddress(newShippingAddress);
                checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                checkoutData.setNewCustomerShippingAddress($.extend(true, {}, addressData));
                this.getPopUp().closeModal();
                this.isNewAddressAdded(true);
            }
        },

        /**
         * Shipping Method View
         */
        rates: shippingService.getShippingRates(),
        isLoading: shippingService.isLoading,
        isSelected: ko.computed(function () {
            return quote.shippingMethod() ?
                quote.shippingMethod()['carrier_code'] + '_' + quote.shippingMethod()['method_code'] :
                null;
        }),

        /**
         * @param {Object} shippingMethod
         * @return {Boolean}
         */
        selectShippingMethod: function (shippingMethod) {
            selectShippingMethodAction(shippingMethod);
            checkoutData.setSelectedShippingRate(shippingMethod['carrier_code'] + '_' + shippingMethod['method_code']);

            return true;
        },

        /**
         * Set shipping information handler
         */
        setShippingInformation: function () {
            if (this.validateShippingInformation()) {
                quote.billingAddress(null);
                checkoutDataResolver.resolveBillingAddress();
                setShippingInformationAction().done(
                    function () {
                        stepNavigator.next();
                    }
                );
            }
        },

        /**
         * @return {Boolean}
         */
        validateShippingInformation: function () {
            var shippingAddress,
                addressData,
                loginFormSelector = 'form[data-role=email-with-possible-login]',
                emailValidationResult = customer.isLoggedIn(),
                field,
                country = registry.get(this.parentName + '.shippingAddress.shipping-address-fieldset.country_id'),
                countryIndexedOptions = country.indexedOptions,
                option = countryIndexedOptions[quote.shippingAddress().countryId],
                messageContainer = registry.get('checkout.errors').messageContainer;

            if (!quote.shippingMethod()) {
                this.errorValidationMessage(
                    $t('The shipping method is missing. Select the shipping method and try again.')
                );

                return false;
            }

            if (!customer.isLoggedIn()) {
                $(loginFormSelector).validation();
                emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
            }

            if (this.isFormInline) {
                this.source.set('params.invalid', false);
                this.triggerShippingDataValidateEvent();

                if (emailValidationResult &&
                    this.source.get('params.invalid') ||
                    !quote.shippingMethod()['method_code'] ||
                    !quote.shippingMethod()['carrier_code']
                ) {
                    this.focusInvalid();

                    return false;
                }

                shippingAddress = quote.shippingAddress();
                addressData = addressConverter.formAddressDataToQuoteAddress(
                    this.source.get('shippingAddress')
                );

                //Copy form data to quote shipping address object
                for (field in addressData) {
                    if (addressData.hasOwnProperty(field) &&  //eslint-disable-line max-depth
                        shippingAddress.hasOwnProperty(field) &&
                        typeof addressData[field] != 'function' &&
                        _.isEqual(shippingAddress[field], addressData[field])
                    ) {
                        shippingAddress[field] = addressData[field];
                    } else if (typeof addressData[field] != 'function' &&
                        !_.isEqual(shippingAddress[field], addressData[field])) {
                        shippingAddress = addressData;
                        break;
                    }
                }

                if (customer.isLoggedIn()) {
                    shippingAddress['save_in_address_book'] = 1;
                }
                selectShippingAddress(shippingAddress);
            } else if (customer.isLoggedIn() &&
                option &&
                option['is_region_required'] &&
                !quote.shippingAddress().region
            ) {
                messageContainer.addErrorMessage({
                    message: $t('Please specify a regionId in shipping address.')
                });

                return false;
            }

            if (!emailValidationResult) {
                $(loginFormSelector + ' input[name=username]').focus();

                return false;
            }

            return true;
        },

        /**
         * Trigger Shipping data Validate Event.
         */
        triggerShippingDataValidateEvent: function () {
            this.source.trigger('shippingAddress.data.validate');

            if (this.source.get('shippingAddress.custom_attributes')) {
                this.source.trigger('shippingAddress.custom_attributes.data.validate');
            }
        }
    });
});
