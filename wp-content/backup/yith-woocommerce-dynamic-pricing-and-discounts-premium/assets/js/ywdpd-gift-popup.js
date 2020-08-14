jQuery(document).ready(function ($) {

    var gift_popup = $(document).find('.ywdpd_popup'),
        block_params = {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            },
            ignoreIfBlocked: true
        },
        show_popup = function () {

            init_slider();
            gift_popup.fadeIn(300);
        },
        close_popup = function () {
            gift_popup.fadeOut(300);
            $(document).trigger('wc_update_cart');
        },
        update_counter = function (single_rule) {

            var allowed_items = single_rule.data('allowed_items'),
                remain = allowed_items - 1;
            single_rule.data('allowed_items', remain );

            if (remain > 0) {

                single_rule.find('.ywdpd_quantity').html(remain);
            } else {
                single_rule.addClass('hide_rule');
            }

            $(document).trigger('ywdpd_counter_updated', [remain, single_rule]);
        },
        init_slider = function () {
            var max_items = 3,
                sliders = gift_popup.find('.ywdpd_product_stage');

            sliders.each(function () {
                var slider = $(this).find('.ywdpd_products.owl-carousel'),
                    item = slider.find('li').size();

                item = item > max_items ? max_items : item;

                slider.owlCarousel({
                    loop: false,
                    margin: 10,
                    nav: false,
                    responsive: {
                        0: {
                            items: 1
                        },
                        600: {
                            items: item
                        },
                        1000: {
                            items: item
                        }
                    }
                });

                slider.on('onInitialized', setTimeout(function(){
                    center();
                }, 500 ) );
            });
        },
        back_to_step1 = function () {
            gift_popup.find('.ywdpd_step2').fadeOut(300, function () {
                gift_popup.find('.ywdpd_step1').fadeIn(300);
                gift_popup.find('.ywdpd_step2').html('');
            });

        },
        go_to_step2 = function (template) {

            gift_popup.find('.ywdpd_step1').fadeOut(300, function () {
                gift_popup.find('.ywdpd_step2').html(template);
                gift_popup.find('.ywdpd_step2 .variations_form').each(function () {
                    $(this).wc_variation_form();
                });
                variation_events();
                add_variable_gift();
                gift_popup.find('.ywdpd_step2').fadeIn(300);
            });

        },
        center = function() {

            var popup_wrapper = gift_popup.find('.ywdpd_popup_wrapper'),
                w = popup_wrapper.outerWidth(),
                h = popup_wrapper.outerHeight(),
                W = $(window).width(),
                H = $(window).height();

            popup_wrapper.css({
                position: 'fixed',
                top: (( H - h) / 2)  + "px",//'15%',
                left:  ( (W - w) / 2) + "px"
            });
        };

    show_popup();
    gift_popup.on('click', '.ywdpd_close', close_popup);
    gift_popup.on('click', '.ywdpd_step1 ul.ywdpd_products.products li a', function (e) {

        e.preventDefault();
        var li = $(this).parent(),
            product_id = li.data('product_id'),
            rule_id = li.data('ywdpd_rule_id'),
            product_type = li.data('product_type');

            if ('simple' === product_type) {
                //add gift product in the cart


                var product_id = li.data('product_id'),
                    rule_id = li.data('ywdpd_rule_id'),
                    data = {
                        product_id: product_id,
                        rule_id: rule_id,
                        action: ywdpd_popup_args.actions.add_gift_to_cart
                    };


                $.ajax({
                    type: 'POST',
                    url: ywdpd_popup_args.ajax_url,
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        li.parents('.ywdpd_product_stage').block(block_params);
                    },
                    success: function (response) {

                        li.addClass('added');
                        update_counter($(document).find('#ywdpd_single_rule_' + rule_id));

                    },
                    complete: function () {
                        li.parents('.ywdpd_product_stage').unblock();
                    }
                });

            } else {
                //show second step
                var data = {
                    product_id: product_id,
                    rule_id: rule_id,
                    action: ywdpd_popup_args.actions.show_second_step
                };
                $.ajax({
                    type: 'POST',
                    url: ywdpd_popup_args.ajax_url,
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        li.find('img.ywdpd_loader').show();
                    },
                    success: function (response) {

                        if (response.template !== '') {
                            go_to_step2(response.template);
                        }

                    },
                    complete: function () {
                        li.find('img.ywdpd_loader').hide();
                    }
                });
            }


    });

    var variation_events = function () {
            $('.variations_form.cart').on('found_variation', function (e, variation) {

                var form = $(this),
                    rule_id = $(this).parent().find('.ywdpd_rule_id').val(),
                    data = {
                    'ywdp_check_rule_id' : rule_id,
                    'product_id': variation.variation_id,
                    action: ywdpd_popup_args.actions.check_variable
                };

                $.ajax({
                    type: 'POST',
                    url: ywdpd_popup_args.ajax_url,
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                       form.block(block_params);
                    },
                    success: function (response) {

                        if( !response.variation_found){
                            $('.ywdpd_add_to_gift').attr('disabled', false);
                        }else{
                            $('.ywdpd_add_to_gift').attr('disabled', true);
                        }
                    },
                    complete: function () {
                        form.unblock();
                    }
                });


            }).on('reset_data', function (e) {
                $('.ywdpd_add_to_gift').attr('disabled', true);
            });
        },
        get_variation = function () {
            var variations = $(document).find('select[name^="attribute"]'),
                var_items = {};

            variations.each(function () {

                var t = $(this),
                    name = t.attr('name');

                var_items[name] = t.val();
            });

            return var_items;
        },
        add_variable_gift = function () {
            $(document).on('click', '.ywdpd_add_to_gift', function (e) {
                e.preventDefault();

                var product_id = $('input[name="product_id"]'),
                    variation_id = $('input.variation_id'),
                    variations = get_variation(),
                    rule_id = $('input.ywdpd_rule_id'),
                    data = {
                        product_id: product_id.val(),
                        variation_id: variation_id.val(),
                        rule_id: rule_id.val(),
                        variations: variations,
                        action: ywdpd_popup_args.actions.add_gift_to_cart
                    },
                    button = $(this);


                $.ajax({
                    type: 'POST',
                    url: ywdpd_popup_args.ajax_url,
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        button.addClass('loading');
                    },
                    success: function (response) {

                        var rule = $(document).find('#ywdpd_single_rule_' + rule_id.val()),
                            li = rule.find('ul.ywdpd_products li[data-product_id="' + product_id.val() + '"]');
                        li.addClass('added');
                        update_counter(rule);
                        back_to_step1();
                    },
                    complete: function () {
                        button.removeClass('loading');
                    }
                });

            });
        };

    $(document).on('ywdpd_counter_updated', function (e, remain, rule) {

        var active_rule = gift_popup.find('.ywdpd_single_rule_container').not('.hide_rule');

        if (active_rule.size() == 0) {
            close_popup();
        } else {
            gift_popup.find('.ywdpd_single_rule_container.hide_rule').fadeOut(300);
        }
    });

    $(document).on('click', '.ywdpd_back', function (e) {
        back_to_step1();
    });

    $(document).on('click', '.ywdpd_footer a', function (e) {
        e.preventDefault();
        close_popup();
    });
    $(document).on('keyup', function (e) {
        if (e.keyCode == 27) {
            close_popup();
        }

    });

    $(window).on('resize', function(){
        center();
    });
});