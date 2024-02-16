
var WooCheckoutPro = function ($scope, $) {
    var $ea_woo_checkout_pro = $scope.find(".ea-woo-checkout").eq(0);

    var tabs_wrapper,
        tabs,
        tabs_content,
        tabFirstTab,
        tabs_panel,
        tabLastTab,
        tabs_common_class,
        first_step_tab,
        tabs_common_class_without_tag,
        first_prev,
        last_next,
        layout_type,
        coupon;


    // Common variable
    var button_prev = $('.ea-woo-checkout-btn-prev'),
        button_next = $('.ea-woo-checkout-btn-next'),
        first_step = 0,
        last_step = 0,
        active_step = 1;

    if ($ea_woo_checkout_pro.hasClass("layout-split")) {
        tabs_panel = 'split-tab-panel';
        tabs_wrapper = $('.layout-split-container');
        coupon = tabs_wrapper.data('coupon');
        tabs = $('.split-tabs');
        tabs_content = $('.split-tabs-content');
        tabFirstTab = 'li.split-tab.first';
        tabLastTab = 'li.split-tab.last';
        tabs_common_class = 'li.split-tab';
        tabs_common_class_without_tag = 'split-tab';
        layout_type = 'split';
        first_prev = 'split-first-prev';
        last_next = 'split-last-next';
    } else if ($ea_woo_checkout_pro.hasClass("layout-multi-steps")) {
        tabs_panel = 'ms-tab-panel';
        tabs_wrapper = $('.layout-multi-steps-container');
        coupon = tabs_wrapper.data('coupon');
        tabs = $('.ms-tabs');
        tabs_content = $('.ms-tabs-content');
        tabFirstTab = 'li.ms-tab.first';
        tabLastTab = 'li.ms-tab.last';
        tabs_common_class = 'li.ms-tab';
        tabs_common_class_without_tag = 'ms-tab';
        layout_type = 'multi';
        first_prev = 'ms-first-prev';
        last_next = 'ms-last-next';
    }

    // Common variable
    var button_prev = $('.ea-woo-checkout-btn-prev');
    var button_next = $('.ea-woo-checkout-btn-next');
    var first_step = 0;
    var last_step = 0;
    var active_step = 1;

    $(".woo-checkout-login, .woo-checkout-coupon, #customer_details, .woo-checkout-payment").addClass(tabs_panel);
    $('.woo-checkout-login').addClass(tabs_panel + '-0');
    if (coupon == 1) {
        $('.woo-checkout-coupon').addClass(tabs_panel + '-1');
        $('#customer_details').addClass(tabs_panel + '-2');
        $('.woo-checkout-payment').addClass(tabs_panel + '-3');
    } else {
        $('#customer_details').addClass(tabs_panel + '-1');
        $('.woo-checkout-payment').addClass(tabs_panel + '-2');
    }

    //Common function
    function validate_email(email) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,})$/;
        if (reg.test(email) == false) {
            return false;
        }
        return true;
    }

    function isEmpty(str) {
        return (!str || 0 === str.length);
    }

    function get_field_value(type, elm, name) {
        var value = '';
        switch (type) {
            case 'radio':
                value = $("input[type=radio][name='" + name + "']:checked").val();
                value = value ? value : '';
                break;
            case 'checkbox':
                if (elm.data('multiple') == 1) {
                    var valueArr = [];
                    $("input[type=checkbox][name='" + name + "']:checked").each(function () {
                        valueArr.push($(this).val());
                    });
                    value = valueArr;
                    if ($.isEmptyObject(value)) {
                        value = "";
                    }
                } else {
                    value = $("input[type=checkbox][name='" + name + "']:checked").val();
                    value = value ? value : '';
                }
                break;
            case 'select':
                value = elm.val();
                break;
            case 'multiselect':
                value = elm.val();
                break;
            case 'hidden':
                value = $("input[type=hidden][name='" + name + "']").val();
                break;
            default:
                value = elm.val();
                break;
        }
        return value;
    }

    function initialize_split_multistep_tab() {
        if (tabs_wrapper && tabs_wrapper.length) {
            first_step_tab = tabs.find(tabFirstTab);
            first_step = first_step_tab.data('step');
            last_step = tabs.find(tabLastTab).data('step');
            if (layout_type == "split") {
                jump_to_step(first_step, first_step_tab);
            } else if (layout_type == "multi") {
                jump_to_step_multistep(first_step, first_step_tab);
            }
            tabs.find(tabs_common_class).click(function () {
                var step_number = $(this).data('step');
                if (step_number < active_step) {
                    if (layout_type == "split") {
                        jump_to_step(step_number, $(this));
                    } else if (layout_type == "multi") {
                        jump_to_step_multistep(step_number, $(this));
                    }
                }
            });
            button_prev.click(function () {
                $(window).scrollTop($ea_woo_checkout_pro.offset().top-50);
                var step_number = active_step - 1;
                if (step_number >= first_step) {
                    if (layout_type == "split") {
                        jump_to_step(step_number, false);
                    } else if (layout_type == "multi") {
                        jump_to_step_multistep(step_number, false);
                    }
                }
            });
            button_next.click(function () {
                $(window).scrollTop($ea_woo_checkout_pro.offset().top-50);
                var step_number = active_step + 1;
                if (step_number <= last_step) {
                    if (layout_type == "split") {
                        validate_checkout_step(active_step, step_number);
                    } else if (layout_type == "multi") {
                        validate_checkout_step_multi_step(active_step, step_number);
                    }
                }
            });
        }
    }

    function validate_checkout_step(active_step, next_step) {
        var valid = validate_step_fields(active_step);

        if (valid) {
            tabs.find('.step-' + active_step).addClass('split-finished-step');

            jump_to_step(next_step, false);
        } else {
            scrol_to_error();
        }
    }

    function scrol_to_error() {
        var topPosition = $('.ea-woo-checkout');
        if(topPosition){
            var top = topPosition.offset().top-50;
            document.body.scrollTop = top;
            document.documentElement.scrollTop = top;
        }

    }

    function display_error_message(msg) {
        var error_div = $('.woocommerce-error');
        var error = '<ul class="woocommerce-error" role="alert">' + msg + '</ul>';
        tabs_content.prepend('<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + error + '</div>');
    }

    function clear_validation_error() {
        $('.ea-wrapper .woocommerce-NoticeGroup-checkout, .ea-wrapper .woocommerce-error, .ea-wrapper .woocommerce-message, .woocommerce .woocommerce-error').remove();
    }

    function validate_step_fields(active_step) {
        clear_validation_error();
        var active_section = $('.' + tabs_panel + '-' + active_step);

        if (active_section) {
            var all_inputs = active_section.find(":input").not('.ea-disabled-field, .woocommerce-validated');
            var ship_to_different_address = $('input[name="ship_to_different_address"]');
            var is_account_field = $('#createaccount');

            var valid = true;
            var msg = '<p>error msg</p>';

            var errorMsgTxt = '';
            var liTagStart = "<li>";
            var liTagEnd = "</li>";

            var dataSet = {};

            $.each(all_inputs, function (field) {

                var type = $(this).getType();
                var name = $(this).attr('name');

                if (type == 'checkbox' || type == 'select') {
                    var formated_name = name.replace('[]', '');
                    var parent = $('#' + formated_name + '_field');
                } else {
                    var parent = $('#' + name + '_field');
                }


                var is_shipping_field = parent.parents('.shipping_address');

                //Check - is shipping address enable from woo commerce setting that means this section exist or not and second condition is
                // Is ship to different address checkbox is checked or not So if condition fullfilled ignore the validation that means return
                //valid and othersie those have to validate
                if (is_shipping_field.length > 0 && ship_to_different_address.prop('checked') != true) {
                    return valid;
                }
                var value = get_field_value(type, $(this), name);
                dataSet[name] = value;

                if (parent.hasClass('validate-required')) {
                    if (isEmpty(value)) {
                        var label = $("label[for=" + name + "]").clone().children().remove().end().text();
                        var getBillingOrShippingText = name.split('_');
                        getBillingOrShippingText = getBillingOrShippingText[0];

                        if(getBillingOrShippingText == 'billing'){
                            getBillingOrShippingText = localize.eael_translate_text.billing_text
                        }

                        if(getBillingOrShippingText == 'shipping'){
                            getBillingOrShippingText = localize.eael_translate_text.shipping_text
                        }

                        var singleErrorMsgTxt = liTagStart + getBillingOrShippingText + ' ' + label + localize.eael_translate_text.required_text + liTagEnd;
                        errorMsgTxt += singleErrorMsgTxt;
                        valid = false;
                        scrol_to_error();
                    }
                }
                else if (parent.hasClass('validate-email')) {
                    var valid_email = validate_email(value);
                    if (!valid_email) {
                        var label = $("label[for=" + name + "]").clone().children().remove().end().text();
                        var getBillingOrShippingText = name.split('_');
                        getBillingOrShippingText = getBillingOrShippingText[0];
                        var singleErrorMsgTxt = liTagStart + localize.eael_translate_text.invalid_text + getBillingOrShippingText + ' ' + label + liTagEnd;
                        errorMsgTxt += singleErrorMsgTxt;
                        valid = false;
                        scrol_to_error();
                    }
                }
                else if (parent.hasClass('validate-postcode')) {
                    let country;
                    if( name == 'billing_postcode' ) {
                        country = dataSet?.['billing_country'] ?? '';
                    } else if( name == 'shipping_postcode' ) {
                        country = dataSet?.['shipping_country'] ?? '';
                    }

                    $.ajax({
                        type: 'POST',
                        url: localize.ajaxurl,
                        async: false,
                        data: {
                            action: 'woo_checkout_post_code_validate',
                            data: {
                                country: country,
                                postcode: value
                            }
                        },
                        success: function (data) {
                            if (!data?.valid) {
                                //$("#step-2").trigger("click");
                                var label = $("label[for=" + name + "]").clone().children().remove().end().text();
                                var getBillingOrShippingText = name.split('_');
                                getBillingOrShippingText = getBillingOrShippingText[0];
                                var singleErrorMsgTxt = liTagStart + data.message + ' ' + liTagEnd;
                                errorMsgTxt += singleErrorMsgTxt;
                                valid = false;
                                scrol_to_error();
                            }
                        }
                    });
                }
            });
            if (!valid) {
                display_error_message(errorMsgTxt);
            }
        }
        return valid;
    }

    $.fn.getType = function () {
        try {
            return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase();
        } catch (err) {
            return 'E001';
        }
    }

    function jump_to_step(step_number, step) {
        if (!step) {
            step = tabs.find('#step-' + step_number);
        }
        var numberOfTabs = $('.' + tabs_common_class_without_tag).length;

        for (var x = 1; x <= numberOfTabs; x++) {
            var nextStepNumber = step_number + x;
            if (nextStepNumber <= numberOfTabs) {
                $("[data-step=" + nextStepNumber + "]").removeClass('completed');
            }
        }

        tabs.find('li').removeClass('active');
        var active_tab_panel = tabs_wrapper.find('.split-tab-panel-' + step_number);

        if (!step.hasClass("completed")) {
            step.addClass("completed");
        }
        if (!step.hasClass("active")) {
            step.addClass("active");
        }

        tabs_wrapper.find('div.' + tabs_panel).not('.' + tabs_panel + '-' + step_number).hide();
        active_tab_panel.show();
        active_step = step_number;

        button_prev.prop('disabled', false);
        button_next.prop('disabled', false);

        button_prev.removeClass(first_prev);
        button_next.removeClass(last_next);
        button_next.data('next');
        button_next.show();
        $("#ea_place_order").hide();
        button_prev.show();

        if (active_step == first_step) {
            button_prev.prop('disabled', true);
            button_prev.addClass('split-first-prev');
            button_prev.hide();
        }
        if (active_step == last_step) {
            button_next.prop('disabled', false);
            button_next.addClass('split-last-next');
            button_next.hide();
            $("#ea_place_order").show();
        }
    }

    function validate_checkout_step_multi_step(active_step, next_step) {
        var valid = validate_step_fields(active_step);
        if (valid) {
            tabs.find('.step-' + active_step).addClass('ms-finished-step');
            jump_to_step_multistep(next_step, false);
        } else {
            // display_error_message(msg);
            scrol_to_error();
        }
    }

    function jump_to_step_multistep(step_number, step) {
        if (!step) {
            step = tabs.find('#step-' + step_number);
        }

        var numberOfTabs = $('.' + tabs_common_class_without_tag).length;

        for (var x = 1; x <= numberOfTabs; x++) {
            var nextStepNumber = step_number + x;
            if (nextStepNumber <= numberOfTabs) {
                $("[data-step=" + nextStepNumber + "]").removeClass('completed');
            }
        }

        tabs.find('li').removeClass('active');
        var active_tab_panel = tabs_wrapper.find('.ms-tab-panel-' + step_number);

        if (!step.hasClass("completed")) {
            step.addClass("completed");
        }
        if (!step.hasClass("active")) {
            step.addClass("active");
        }

        tabs_wrapper.find('div.ms-tab-panel').not('.ms-tab-panel-' + step_number).hide();
        active_tab_panel.show();
        active_step = step_number;
        button_prev.prop('disabled', false);
        button_next.prop('disabled', false);
        button_prev.removeClass('ms-first-prev');
        button_next.removeClass('ms-last-next');
        button_next.data('next');
        button_next.show();
        $("#ea_place_order").hide();
        button_prev.show();
        if (active_step == first_step) {
            button_prev.prop('disabled', true);
            button_prev.addClass('ms-first-prev');
            button_prev.hide();
        }
        if (active_step == last_step) {
            button_next.prop('disabled', false);
            button_next.addClass('ms-last-next');
            button_next.hide();
            $("#ea_place_order").show();
        }
    }

    initialize_split_multistep_tab();
    if (tabs_wrapper) {
        tabs_wrapper.on('click', '#ea_place_order', function () {
            $("#place_order").trigger("click");
        });
    }

    if ( $('.ea-woo-checkout', $scope).hasClass('layout-multi-steps') || $('.ea-woo-checkout', $scope).hasClass('layout-split') ) {
        $(document).ajaxComplete(function() {
            var login_btn = $('.ea-woo-checkout .woocommerce-NoticeGroup-checkout a.showlogin', $scope);
            login_btn.on('click', function(e) {
                $('#step-0', $scope).trigger('click');
            });
        });
    }
};

jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/eael-woo-checkout.default",
        WooCheckoutPro
    );
});
