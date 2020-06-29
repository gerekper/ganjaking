/**
 * YITH WooCommerce Multi Step Checkout
 * @version 2.0.0
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */

/**
 * ================= *
 * Standard Step Mapping
 * before version 2.0.0
 *
 * 0 -> Login
 * 1 -> Billing
 * 2 -> Shipping
 * 3 -> Order Info
 * 4 -> Payment
 * ================= *
 */

(function ($) {
    $.fn.yith_trigger_multiple = function (list) {
        return this.each(function () {
            var $this = $(this); // cache target

            $.each(list, function (k, v) { // split string and loop through params
                $this.trigger(v); // trigger each passed param
            });
        });
    };

    //yith_wcms.dom element are documented in /includes\class.yith-multistep-checkout-frontend-premium.php:187
    var $body = $('body'),
        $html = $('html'),
        login = $(yith_wcms.dom.login),
        billing = $(yith_wcms.dom.billing),
        shipping = $(yith_wcms.dom.shipping),
        order = $(yith_wcms.dom.order),
        payment = $(yith_wcms.dom.payment),
        form_actions = $(yith_wcms.dom.form_actions),
        coupon = $(yith_wcms.dom.coupon),
        create_account = $(yith_wcms.dom.create_account),
        create_account_wrapper = $(yith_wcms.dom.create_account_wrapper),
        account_password = $( yith_wcms.dom.account_password ),
        shipping_check = $(yith_wcms.dom.shipping_check),
        back_to_cart = $(yith_wcms.dom.button_back_to_cart),
        steps = {
            login: login,
            billing: billing,
            shipping: shipping,
            order: order,
            payment: payment
        },
        is_user_logged_in = $body.hasClass('logged-in'),
        cookie = {
            form: 'yith_wcms_checkout_form',
            step: 'yith_wcms_checkout_current_step'
        },
        get_prev_and_next_step = function( $current_step ){
            return yith_wcms.steps_timeline[ $current_step ];
        },
        check_create_account_fields = function( invalid_fields, create_account, create_account_wrapper ){
            if (create_account.length != 0 && ! create_account.is(':checked') && invalid_fields != 0) {
                invalid_fields = invalid_fields - create_account_wrapper.find('.validate-required').length;
                if( yith_wcms.wp_gdpr.is_enabled && yith_wcms.wp_gdpr.add_consent_on_checkout == 1 ){
                    invalid_fields = invalid_field + yith_wcms.wp_gdpr.consents_number;
                }
            }

            if (create_account.length != 0 && create_account.is(':checked') && yith_wcms.wp_gdpr.is_enabled && yith_wcms.wp_gdpr.add_consent_on_checkout == 1) {
                $.each( yith_wcms.wp_gdpr.consents, function( index, element ){
                    consent = "#user_consents_" + index;

                    if( ! $(consent).is(':checked') ){
                        invalid_fields = invalid_fields + 1;
                    }
                });
            }

            return invalid_fields;
        };

    $body.on('updated_checkout yith_wcms_myaccount_order_pay', function (e) {
        if (e.type == 'updated_checkout') {
            steps['payment'] = $(yith_wcms.dom.payment);
        }

        var current_step = form_actions.data('step');
        if (current_step == 'payment') {
            $(yith_wcms.dom.payment).show();
        }

        $body.trigger('yith_wcms_updated_checkout');
    });

    if ($body.hasClass('woocommerce-order-pay')) {
        $body.trigger('yith_wcms_myaccount_order_pay');
    }

    if (yith_wcms.live_fields_validation == 'yes') {

        // radio validation
        var checkout_form = $(yith_wcms.dom.checkout_form),
            radio_validation = function () {
                var $this = $(this),
                    $parent = $this.closest('.form-row'),
                    validated = true;

                if ($parent.is('.validate-required')) {
                    if ('radio' == $this.attr('type')) {
                        var radio_group_name = $this.attr('name');
                        if (!$('input[name=' + radio_group_name + ']').is(':checked')) {
                            $parent.removeClass('woocommerce-validated').addClass('woocommerce-invalid woocommerce-invalid-required-field');
                            validated = false;
                        }
                    }

                    if (validated) {
                        $parent.removeClass('woocommerce-invalid woocommerce-invalid-required-field').addClass('woocommerce-validated');
                    }
                }
            };

        $body.on('blur', 'input:radio', radio_validation);
    }

    //enable select2
    $body.on('yith_wcms_select2', function (event) {
        if ($().select2) {
            var wc_country_select_select2 = function () {
                $('select.country_select, select.state_select').each(function () {
                    var select2_args = {
                        placeholder: $(this).attr('placeholder'),
                        placeholderOption: 'first',
                        width: '100%'
                    };

                    $(this).select2(select2_args);
                });
            };

            wc_country_select_select2();

            $body.bind('country_to_state_changed', function () {
                wc_country_select_select2();
            });
        }
    });

    if (yith_wcms.wc_shipping_multiple != 1) {
        $body.trigger('yith_wcms_select2');
    }

    $('.yith-wcms-pro ' + yith_wcms.dom.checkout_timeline + ' li').on('click', function (e) {

        var t = $(this);

        if (t.hasClass('active')) {
            return false;
        }

        var current_step = $(yith_wcms.dom.checkout_timeline).find(yith_wcms.dom.active_timeline).data('step'),
            destination_step = t.data('step'),
            current_linked_step = get_prev_and_next_step(destination_step),
            linked_step = current_linked_step['prev'] !== false ? get_prev_and_next_step(current_linked_step['prev']) : get_prev_and_next_step(current_linked_step['next']),
            next_step = linked_step['next'],
            prev_step = linked_step['prev'],
            action = null;

            if( next_step == destination_step ){
                action = form_actions.find(yith_wcms.dom.button_next);
            }

            else if( prev_step == destination_step ){
                action = form_actions.find(yith_wcms.dom.button_prev);
            }


        if ( ( current_step == 'login' && is_user_logged_in ) || action == null ) {
            return false;
        }

        change_step(action, current_step, next_step, prev_step);
    });

    form_actions.find(yith_wcms.dom.button_prev).add(yith_wcms.dom.button_next).on('click', function (e) {
        var t = $(this),
            current_step = form_actions.data('step'),
            linked_step = get_prev_and_next_step( current_step ),
            next_step = linked_step['next'],
            prev_step = linked_step['prev'];

        change_step(t, current_step, next_step, prev_step);

    });

    var change_step = function (t, current_step, next_step, prev_step) {

        var timeline = $(yith_wcms.dom.checkout_timeline),
            action = t.data('action'),
            prev = form_actions.find(yith_wcms.dom.button_prev),
            next = form_actions.find(yith_wcms.dom.button_next),
            active_step = timeline.find('.active').data('step'),
            checkout_form = $(yith_wcms.dom.checkout_form);

        if( action == 'prev' ){
            next.removeClass( 'disabled' ).removeAttr( 'disabled' );
        }

        if (yith_wcms.is_scroll_top_enabled == 'yes') {
            var scroll_top_anchor = $(yith_wcms.dom.scroll_top_anchor),
                pos = scroll_top_anchor.offset().top - scroll_top_anchor.outerHeight(true);

            $html.add($body).animate({
                scrollTop: pos
            }, 500);
        }

        var show_coupon = function (current_step) {
            // Your order
            if ( current_step == 'order' || $(document).triggerHandler( 'yith_wmcs_show_coupon', current_step ) ) {
                coupon.fadeIn(yith_wcms.transition_duration);
            }

            else {
                coupon.fadeOut(yith_wcms.transition_duration);
            }
        };

        // live fields validation
        if (yith_wcms.live_fields_validation == 'yes') {
            var allowed_steps = ( active_step == 'login' || active_step == 'billing' || active_step == 'shipping' );

            /**
             * YITH WooCommerce Delivery Date Support
             */
            if( yith_wcms.is_delivery_date_enabled ){
                allowed_steps = allowed_steps || active_step == 'order';
            }

            if ( allowed_steps && action == 'next') {
                var checkout_form = $(yith_wcms.dom.checkout_form),
                    invalid_field = 0;

                // Inline validation
                if (active_step == 'billing' || active_step == 'shipping' || ( yith_wcms.is_delivery_date_enabled && active_step == 'order' ) ) {
                    checkout_form.find(yith_wcms.dom.required_fields_check).yith_trigger_multiple(yith_wcms.validate_checkout_event);
                }

                //billing or login step
                if (active_step == 'billing' || active_step == 'login') {

                    /**
                     * YITH WooCommerce Coupon Email System Support
                     */
                    if (yith_wcms.is_coupon_email_system_enabled) {
                        var dob = $(yith_wcms.dom.day_of_birth);
                        if (typeof dob != 'undefined') {
                            var pattern = dob.attr('pattern'),
                                regex = new RegExp('^' + pattern + '$', 'g'),
                                dob_value = dob.val();

                            if (dob_value != '' && regex.test(dob_value) == false) {
                                dob.parent().removeClass('woocommerce-validated').addClass('woocommerce-invalid-required-field').addClass('woocommerce-invalid');
                            }
                        }
                    }

                    var email = $(yith_wcms.dom.email);

                    if( typeof email != 'undefined' && email.hasClass('woocommerce-invalid-email') ){
                        email.addClass('woocommerce-invalid-required-field');
                    }

                    invalid_field = billing.find(yith_wcms.dom.wc_invalid_required).not('.ywsfd-validate-required').length;

                    invalid_field = check_create_account_fields( invalid_field, create_account, create_account_wrapper );
                }

                //shipping
                else if (active_step == 'shipping') {
                    var shipping_check_exists = shipping_check.length;

                    if (
                        ( shipping_check_exists != 0 && shipping_check.is(':checked') )
                        ||
                        ( shipping_check_exists == 0 && shipping.find('input').length )
                    ) {
                        invalid_field = shipping.find(yith_wcms.dom.wc_invalid_required).not('.ywsfd-validate-required').length;
                    }

                    //Added Support to WooCommerce Checkout Add-ons
                    if (yith_wcms.is_wc_checkout_addons_enabled) {
                        var wc_checkout_addons = $(yith_wcms.dom.wc_checkout_addons);
                        invalid_field = invalid_field + wc_checkout_addons.find(yith_wcms.dom.wc_invalid_required).length;
                    }

                    var extra_fields = shipping.find(yith_wcms.dom.additional_fields).find('>' + yith_wcms.dom.wc_invalid_required).length;
                    invalid_field = invalid_field + extra_fields;
                }

                else if( yith_wcms.is_delivery_date_enabled && active_step == 'order' ){
                    invalid_field = order.find(yith_wcms.dom.wc_invalid_required).length;
                }

                if (invalid_field != 0) {
                    if (active_step != 'login') {
                        next.addClass( 'disabled' ).attr( 'disabled', true );
                        return false;
                    }

                    else {
                        next_step = 'billing';
                    }
                }
            }
        }

        timeline.find('.active').removeClass('active');

        if (action == 'next') {
            form_actions.data('step', next_step);
            steps[current_step].fadeOut(yith_wcms.transition_duration, function () {
                steps[next_step].fadeIn(yith_wcms.transition_duration);
                show_coupon(next_step);
            });

            $(yith_wcms.dom.timeline_id_prefix + next_step).toggleClass('active');
        }

        else if (action == 'prev') {
            form_actions.data('step', prev_step);
            steps[current_step].fadeOut(yith_wcms.transition_duration, function () {
                steps[prev_step].fadeIn(yith_wcms.transition_duration);
            });

            show_coupon(prev_step);
            $(yith_wcms.dom.timeline_id_prefix + prev_step).toggleClass('active');
        }

        current_step = form_actions.data('step');

        if (yith_wcms.use_cookie == true) {
            Cookies.set(cookie.step, current_step, {path: '/'});
        }

        /**
         * Show Skip Login button instead of next in Login step
         *
         * action == 'prev' && yith_wcms.steps_timeline[prev_step][action] === false
         * check if the customer is in the  is the first step
         *
         * prev_step == 'login'
         * and last step is 'login'
         *
         * Please note: we can't check if the current user is logged in
         * because it's possible to remove login step
         */
        var show_skip_login_button = action == 'prev' && yith_wcms.steps_timeline[prev_step][action] === false && prev_step == 'login';

        if( show_skip_login_button === true ){
            next.val(yith_wcms.skip_login_label);
        }

        else {
            next.val(yith_wcms.next_label);
        }

        /** Disable Prev Button if...
         * 1. Current step is billing and current user logged in
         * 2. Current step is login and current user not logged in
         * Disable Next Button if in Payment step
         */
        var disable_prev_button = false,
            disable_next_button = false;
        /**
         * yith_wcms.steps_timeline[next_step][action] === false
         *
         * Means: if I haven't next step, if this is the last step
         */
        if( action == 'next' && yith_wcms.steps_timeline[next_step][action] === false ){
            disable_next_button = true;
        }

        /**
         * yith_wcms.steps_timeline[prev_step][action] === false
         *
         * Means: if I haven't previous step, if this is the first step
         */
        if( action == 'prev' && yith_wcms.steps_timeline[prev_step][action] === false ){
            disable_prev_button = true;
        }

        if ( disable_prev_button === true ) {
            prev.fadeOut(yith_wcms.transition_duration);
        }

        else {
            prev.fadeIn(yith_wcms.transition_duration);
        }

        if( disable_next_button === true ){
            next.fadeOut(yith_wcms.transition_duration);
        }

        else {
            next.fadeIn(yith_wcms.transition_duration);
        }

        // Last step
        if (current_step == 'payment') {
            checkout_form.removeClass('processing');
            /**
             * Disable prev button if the admin
             * want to remove prev button in last step
             * (added via plugin option)
             */
            if (yith_wcms.disabled_prev_button == 'yes') {
                prev.fadeOut(yith_wcms.transition_duration);
            }

            /**
             * Disable back to cart button if the admin
             * want to remove the button in last step
             * (added via plugin option)
             */
            if (yith_wcms.disabled_back_to_cart_button == 'yes') {
                back_to_cart.fadeOut(yith_wcms.transition_duration);
            }
        }

        else {
            checkout_form.addClass('processing');

            if (yith_wcms.disabled_back_to_cart_button == 'yes') {
                back_to_cart.fadeIn(yith_wcms.transition_duration);
            }
        }
    };

    /**
     * Add Enable/Disable next button if customer
     * have set invalid fields
     */
    var next_button = form_actions.find(yith_wcms.dom.button_next);



    billing.find('.validate-required').find( 'input' ).add(create_account).on( 'blur click change', function(event){
        var invalid_fields = billing.find( yith_wcms.dom.wc_invalid_required ).length;

        invalid_fields = check_create_account_fields( invalid_fields, create_account, create_account_wrapper );

        if( invalid_fields != 0 ){
            next_button.addClass( 'disabled' ).attr( 'disabled', true );
        }

        else {
            next_button.removeClass( 'disabled' ).removeAttr( 'disabled' );
        }
    });

    shipping.find('.validate-required').find( 'input' ).add(shipping_check).on( 'blur click change', function(event){
        if( shipping_check.is( ':checked' ) ){
            var invalid_fields = shipping.find( yith_wcms.dom.wc_invalid_required ).length;

            if( invalid_fields != 0 ){
                next_button.addClass( 'disabled' ).attr( 'disabled', true );
            }

            else {
                next_button.removeClass( 'disabled' ).removeAttr( 'disabled' );
            }
        }

        else {
            next_button.removeClass( 'disabled' ).removeAttr( 'disabled' );
        }
    });

    var preset_form_value = Cookies.get(cookie.form),
        preset_current_step = Cookies.get(cookie.step);

    var set_cookie_value = function () {
        var form_temp = $('.checkout.woocommerce-checkout').serialize();
        Cookies.set(cookie.form, form_temp, {path: '/'});
    };

    var cache_form_value = function () {
        shipping_check.find('input').on('change', function (e) {
            set_cookie_value();
        });

        $(yith_wcms.dom.checkout_form).find(yith_wcms.dom.required_fields_check).on('blur change', function (e) {
            set_cookie_value();
        });
    };

    var set_cached_value = function (preset_form_value, preset_current_step) {
        if (typeof preset_form_value != 'undefined') {
            var form_temp = preset_form_value.split('&');
            for (var i in form_temp) {

                var elem = form_temp[i];

                if (typeof elem != 'string') {
                    continue;
                }

                var form_value = elem.split('='),
                    input_field_name = decodeURIComponent('input[name="' + form_value[0] + '"]'),
                    input_field = $(input_field_name);

                if (typeof input_field != 'undefined') {
                    var cached_value = decodeURIComponent(form_value[1]).replace(/\+/g, ' ');
                    //Select2 Cached Value
                    if (yith_wcms.dom.select2_fields.indexOf(form_value[0]) != -1) {
                        var country_field = $('#' + decodeURIComponent(form_value[0]));
                        country_field.add(input_field).val(cached_value);
                        if (country_field.is('select')) {
                            country_field.val(cached_value).trigger('change');
                        }
                        else if (country_field.is('input')) {
                            input_field.val(cached_value);
                        }
                    }
                    else {
                        // skip cached value for nonce fields or other private WordPress fields
                        var skip_current_value = false;

                        if (form_value[0].indexOf('payment_method') != -1 && yith_wcms.skip_payment_method == true) {
                            skip_current_value = true;
                        }

                        if (form_value[0].indexOf('shipping_method') != -1 && yith_wcms.skip_shipping_method == true) {
                            skip_current_value = true;
                        }

                        if (form_value[0].indexOf('_wp') == -1 && cached_value && skip_current_value == false) {
                            if (input_field.prop('type') == 'checkbox' && cached_value == 1) {
                                input_field.prop('checked', 'checked');
                            }

                            else {

                                input_field.val(cached_value);
                            }
                        }
                    }
                }
            }
        }
    };

    if (yith_wcms.use_cookie == true) {
        cache_form_value();

        set_cached_value(preset_form_value, preset_current_step);

        $body.on('country_to_state_changed', function (e, value, obj) {
            cache_form_value();
        });
    }

    if (typeof Cookies.get(cookie.step) != 'undefined' && yith_wcms.use_cookie == true) {
        $body.on('updated_checkout yith_wcms_myaccount_order_pay', function () {
            $('.yith-wcms-pro ' + yith_wcms.dom.checkout_timeline + ' li#timeline-' + Cookies.get(cookie.step)).trigger('click');
        });
    }

    //Delete cookie after order complete
    if (yith_wcms.is_order_received_endpoint == 1 && yith_wcms.use_cookie == true) {
        Cookies.remove(cookie.form, {path: '/'});
        Cookies.remove(cookie.step, {path: '/'});
    }
})(jQuery);
