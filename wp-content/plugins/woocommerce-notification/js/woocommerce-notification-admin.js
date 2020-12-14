'use strict';
jQuery(document).ready(function ($) {
    jQuery('.vi-ui.tabular.menu .item').vi_tab({
        history: true,
        historyType: 'hash'
    });

    /*Setup tab*/
    var tabs,
        tabEvent = false,
        initialTab = 'general',
        navSelector = '.vi-ui.menu',
        panelSelector = '.vi-ui.tab',
        panelFilter = function () {
            jQuery(panelSelector + ' a').filter(function () {
                return jQuery(navSelector + ' a[title=' + jQuery(this).attr('title') + ']').size() != 0;
            }).each(function (event) {
                jQuery(this).attr('href', '#' + $(this).attr('title').replace(/ /g, '_'));
            });
        };
    // Initializes plugin features
    jQuery.address.strict(false).wrap(true);

    if (jQuery.address.value() == '') {
        jQuery.address.history(false).value(initialTab).history(true);
    }

    // Address handler
    jQuery.address.init(function (event) {

        // Adds the ID in a lazy manner to prevent scrolling
        jQuery(panelSelector).attr('id', initialTab);

        panelFilter();

        // Tabs setup
        tabs = jQuery('.vi-ui.menu')
            .vi_tab({
                history: true,
                historyType: 'hash'
            })

    });
    /*End setup tab*/
    jQuery('.vi-ui.checkbox').checkbox();
    jQuery('select.vi-ui.dropdown').dropdown();
    /*Search*/
    jQuery(".product-search").select2({
        closeOnSelect: false,
        placeholder: "Please fill in your  product title",
        ajax: {
            url: "admin-ajax.php?action=wcn_search_product",
            dataType: 'json',
            type: "GET",
            quietMillis: 50,
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1
    });
    /*Search*/
    jQuery(".product-search-parent").select2({
        closeOnSelect: false,
        placeholder: "Please fill in your  product title",
        ajax: {
            url: "admin-ajax.php?action=wcn_search_product_parent",
            dataType: 'json',
            type: "GET",
            quietMillis: 50,
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1
    });
    /*Search*/
    jQuery(".category-search").select2({
        closeOnSelect: false,
        placeholder: "Please fill in your category title",
        ajax: {
            url: "admin-ajax.php?action=wcn_search_cate",
            dataType: 'json',
            type: "GET",
            quietMillis: 50,
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1
    });
    /*Save Submit button*/
    jQuery('.wn-submit').one('click', function () {
        jQuery(this).addClass('loading');
    });
    /*Add field depend*/
    jQuery('input[type="checkbox"]').unbind().on('change', function () {
        if (jQuery(this).prop('checked')) {
            jQuery(this).parent().find('input[type="hidden"]').val('1');
        } else {
            jQuery(this).parent().find('input[type="hidden"]').val('');
        }
    });
    /*Products*/
    if (jQuery('.get_from_billing').length > 0) {

        jQuery('.get_from_billing').dependsOn({
            'select[name="wnotification_params[archive_page]"]': {
                values: ['0']
            }
        });
    }
    if (jQuery('.show-close-icon').length > 0) {

        jQuery('.show-close-icon').dependsOn({
            'input[name="wnotification_params[show_close_icon]"]': {
                checked: true
            }
        });
    }
    if (jQuery('.latest-product-select-categories').length > 0) {

        jQuery('.latest-product-select-categories').dependsOn({
            'select[name="wnotification_params[archive_page]"]': {
                values: ['2', '3', '4']
            }
        });
    }
    if (jQuery('.select-categories').length > 0) {

        jQuery('.select-categories').dependsOn({
            'select[name="wnotification_params[archive_page]"]': {
                values: ['3']
            }
        });
    }
    if (jQuery('.select_product').length > 0) {

        jQuery('.select_product').dependsOn({
            'select[name="wnotification_params[archive_page]"]': {
                values: ['1', '2', '3', '4']
            }
        });
    }
    if (jQuery('.select_only_product').length > 0) {

        jQuery('.select_only_product').dependsOn({
            'select[name="wnotification_params[archive_page]"]': {
                values: ['1']
            }
        });
    }
    if (jQuery('.exclude_products').length > 0) {

        jQuery('.exclude_products').dependsOn({
            'select[name="wnotification_params[archive_page]"]': {
                values: ['0']
            }
        });
    }
    if (jQuery('.only_current_product').length > 0) {

        jQuery('.only_current_product').dependsOn({
            'select[name="wnotification_params[notification_product_show_type]"]': {
                values: ['0']
            }
        });
    }
    if (jQuery('.virtual_address').length > 0) {
        jQuery('.virtual_address').dependsOn({
            'select[name="wnotification_params[archive_page]"]': {
                values: ['1', '2', '3', '4']
            },
            'select[name="wnotification_params[country]"]': {
                values: ['1']
            }
        });
    }
    if (jQuery('select[name="wnotification_params[archive_page]').length > 0) {
        jQuery('select[name="wnotification_params[archive_page]"]').on('change', function () {
            var data = jQuery(this).val();
            if (data == 0) {
                jQuery('.virtual_address').hide();
            } else {
                var data1 = jQuery('select[name="wnotification_params[country]"]').val();
                if (data1 > 0) {
                    jQuery('.virtual_address').show();
                }
            }
        });
    }
    if (jQuery('.time_loop').length > 0) {

        /*Time*/
        jQuery('.time_loop').dependsOn({
            'input[name="wnotification_params[loop]"]': {
                checked: true
            }
        });
    }
    if (jQuery('.initial_delay_random').length > 0) {

        /*Initial time random*/
        jQuery('.initial_delay_random').dependsOn({
            'input[name="wnotification_params[initial_delay_random]"]': {
                checked: true
            }
        });
    }
    /*Logs*/
    if (jQuery('.save_logs').length > 0) {
        jQuery('.save_logs').dependsOn({
            'input[name="wnotification_params[save_logs]"]': {
                checked: true
            }
        });
    }
// Color picker
    jQuery('.color-picker').iris({
        change: function (event, ui) {
            jQuery(this).parent().find('.color-picker').css({backgroundColor: ui.color.toString()});
            var ele = jQuery(this).data('ele');
            if (ele == 'highlight') {
                jQuery('#message-purchased').find('a').css({'color': ui.color.toString()});
            } else if (ele == 'textcolor') {
                jQuery('.message-purchase-main').css({'color': ui.color.toString()});
            } else if (ele == 'close_icon_color') {
                jQuery('#woocommerce-notification-close-icon-color').html('#message-purchased #notify-close:before{color:'+ui.color.toString()+'}');
            } else {
                jQuery('.message-purchase-main').css({backgroundColor: ui.color.toString()});
            }
        },
        hide: true,
        border: true
    }).on('click',function () {
        jQuery('.iris-picker').hide();
        jQuery(this).closest('td').find('.iris-picker').show();
    });

    jQuery('body').on('click',function () {
        jQuery('.iris-picker').hide();
    });
    jQuery('.color-picker').on('click',function (event) {
        event.stopPropagation();
    });
    jQuery('input[name="wnotification_params[position]"]').on('change', function () {
        var data = jQuery(this).val();
        if (data == 1) {
            jQuery('#message-purchased').removeClass('top_left top_right').addClass('bottom_right');
        } else if (data == 2) {
            jQuery('#message-purchased').removeClass('bottom_right top_right').addClass('top_left');
        } else if (data == 3) {
            jQuery('#message-purchased').removeClass('bottom_right top_left').addClass('top_right');
        } else {
            jQuery('#message-purchased').removeClass('bottom_right top_left top_right');
        }
    });
    jQuery('select[name="wnotification_params[image_position]"]').on('change', function () {
        var data = jQuery(this).val();
        if (data == 1) {
            jQuery('#message-purchased').addClass('img-right');
        } else {
            jQuery('#message-purchased').removeClass('img-right');
        }
    });

    /*add optgroup to select box semantic*/
    jQuery('.vi-ui.dropdown.selection').has('optgroup').each(function () {
        var $menu = jQuery('<div/>').addClass('menu');
        jQuery(this).find('optgroup').each(function () {
            $menu.append("<div class=\"header\">" + this.label + "</div><div class=\"divider\"></div>");
            return jQuery(this).children().each(function () {
                return $menu.append("<div class=\"item\" data-value=\"" + this.value + "\">" + this.innerHTML + "</div>");
            });
        });
        return jQuery(this).find('.menu').html($menu.html());
    });

    jQuery('#message-purchased').attr('data-effect_display', '');
    jQuery('#message-purchased').attr('data-effect_hidden', '');

    jQuery('select[name="wnotification_params[message_display_effect]"]').on('change', function () {
        var data = jQuery(this).val(),
            message_purchased = jQuery('#message-purchased');

        switch (data) {
            case 'bounceIn':
                message_purchased.attr('data-effect_display', 'bounceIn');
                break;
            case 'bounceInDown':
                message_purchased.attr('data-effect_display', 'bounceInDown');
                break;
            case 'bounceInLeft':
                message_purchased.attr('data-effect_display', 'bounceInLeft');
                break;
            case 'bounceInRight':
                message_purchased.attr('data-effect_display', 'bounceInRight');
                break;
            case 'bounceInUp':
                message_purchased.attr('data-effect_display', 'bounceInUp');
                break;
            case 'fade-in':
                message_purchased.attr('data-effect_display', 'fade-in');
                break;
            case 'fadeInDown':
                message_purchased.attr('data-effect_display', 'fadeInDown');
                break;
            case 'fadeInDownBig':
                message_purchased.attr('data-effect_display', 'fadeInDownBig');
                break;
            case 'fadeInLeft':
                message_purchased.attr('data-effect_display', 'fadeInLeft');
                break;
            case 'fadeInLeftBig':
                message_purchased.attr('data-effect_display', 'fadeInLeftBig');
                break;
            case 'fadeInRight':
                message_purchased.attr('data-effect_display', 'fadeInRight');
                break;
            case 'fadeInRightBig':
                message_purchased.attr('data-effect_display', 'fadeInRightBig');
                break;
            case 'fadeInUp':
                message_purchased.attr('data-effect_display', 'fadeInUp');
                break;
            case 'fadeInUpBig':
                message_purchased.attr('data-effect_display', 'fadeInUpBig');
                break;
            case 'flipInX':
                message_purchased.attr('data-effect_display', 'flipInX');
                break;
            case 'flipInY':
                message_purchased.attr('data-effect_display', 'flipInY');
                break;
            case 'lightSpeedIn':
                message_purchased.attr('data-effect_display', 'lightSpeedIn');
                break;
            case 'rotateIn':
                message_purchased.attr('data-effect_display', 'rotateIn');
                break;
            case 'rotateInDownLeft':
                message_purchased.attr('data-effect_display', 'rotateInDownLeft');
                break;
            case 'rotateInDownRight':
                message_purchased.attr('data-effect_display', 'rotateInDownRight');
                break;
            case 'rotateInUpLeft':
                message_purchased.attr('data-effect_display', 'rotateInUpLeft');
                break;
            case 'rotateInUpRight':
                message_purchased.attr('data-effect_display', 'rotateInUpRight');
                break;
            case 'slideInUp':
                message_purchased.attr('data-effect_display', 'slideInUp');
                break;
            case 'slideInDown':
                message_purchased.attr('data-effect_display', 'slideInDown');
                break;
            case 'slideInLeft':
                message_purchased.attr('data-effect_display', 'slideInLeft');
                break;
            case 'slideInRight':
                message_purchased.attr('data-effect_display', 'slideInRight');
                break;
            case 'zoomIn':
                message_purchased.attr('data-effect_display', 'zoomIn');
                break;
            case 'zoomInDown':
                message_purchased.attr('data-effect_display', 'zoomInDown');
                break;
            case 'zoomInLeft':
                message_purchased.attr('data-effect_display', 'zoomInLeft');
                break;
            case 'zoomInRight':
                message_purchased.attr('data-effect_display', 'zoomInRight');
                break;
            case 'zoomInUp':
                message_purchased.attr('data-effect_display', 'zoomInUp');
                break;
            case 'rollIn':
                message_purchased.attr('data-effect_display', 'rollIn');
                break;
        }

    });

    jQuery('select[name="wnotification_params[message_hidden_effect]"]').on('change', function () {
        var data = jQuery(this).val(),
            message_purchased = jQuery('#message-purchased');

        switch (data) {
            case 'bounceOut':
                message_purchased.attr('data-effect_hidden', 'bounceOut');
                break;
            case 'bounceOutDown':
                message_purchased.attr('data-effect_hidden', 'bounceOutDown');
                break;
            case 'bounceOutLeft':
                message_purchased.attr('data-effect_hidden', 'bounceOutLeft');
                break;
            case 'bounceOutRight':
                message_purchased.attr('data-effect_hidden', 'bounceOutRight');
                break;
            case 'bounceOutUp':
                message_purchased.attr('data-effect_hidden', 'bounceOutUp');
                break;
            case 'fade-out':
                message_purchased.attr('data-effect_hidden', 'fade-out');
                break;
            case 'fadeOutDown':
                message_purchased.attr('data-effect_hidden', 'fadeOutDown');
                break;
            case 'fadeOutDownBig':
                message_purchased.attr('data-effect_hidden', 'fadeOutDownBig');
                break;
            case 'fadeOutLeft':
                message_purchased.attr('data-effect_hidden', 'fadeOutLeft');
                break;
            case 'fadeOutLeftBig':
                message_purchased.attr('data-effect_hidden', 'fadeOutLeftBig');
                break;
            case 'fadeOutRight':
                message_purchased.attr('data-effect_hidden', 'fadeOutRight');
                break;
            case 'fadeOutRightBig':
                message_purchased.attr('data-effect_hidden', 'fadeOutRightBig');
                break;
            case 'fadeOutUp':
                message_purchased.attr('data-effect_hidden', 'fadeOutUp');
                break;
            case 'fadeOutUpBig':
                message_purchased.attr('data-effect_hidden', 'fadeOutUpBig');
                break;
            case 'flipOutX':
                message_purchased.attr('data-effect_hidden', 'flipOutX');
                break;
            case 'flipOutY':
                message_purchased.attr('data-effect_hidden', 'flipOutY');
                break;
            case 'lightSpeedOut':
                message_purchased.attr('data-effect_hidden', 'lightSpeedOut');
                break;
            case 'rotateOut':
                message_purchased.attr('data-effect_hidden', 'rotateOut');
                break;
            case 'rotateOutDownLeft':
                message_purchased.attr('data-effect_hidden', 'rotateOutDownLeft');
                break;
            case 'rotateOutDownRight':
                message_purchased.attr('data-effect_hidden', 'rotateOutDownRight');
                break;
            case 'rotateOutUpLeft':
                message_purchased.attr('data-effect_hidden', 'rotateOutUpLeft');
                break;
            case 'rotateOutUpRight':
                message_purchased.attr('data-effect_hidden', 'rotateOutUpRight');
                break;
            case 'slideOutUp':
                message_purchased.attr('data-effect_hidden', 'slideOutUp');
                break;
            case 'slideOutDown':
                message_purchased.attr('data-effect_hidden', 'slideOutDown');
                break;
            case 'slideOutLeft':
                message_purchased.attr('data-effect_hidden', 'slideOutLeft');
                break;
            case 'slideOutRight':
                message_purchased.attr('data-effect_hidden', 'slideOutRight');
                break;
            case 'zoomOut':
                message_purchased.attr('data-effect_hidden', 'zoomOut');
                break;
            case 'zoomOutDown':
                message_purchased.attr('data-effect_hidden', 'zoomOutDown');
                break;
            case 'zoomOutLeft':
                message_purchased.attr('data-effect_hidden', 'zoomOutLeft');
                break;
            case 'zoomOutRight':
                message_purchased.attr('data-effect_hidden', 'zoomOutRight');
                break;
            case 'zoomOutUp':
                message_purchased.attr('data-effect_hidden', 'zoomOutUp');
                break;
            case 'rollOut':
                message_purchased.attr('data-effect_hidden', 'rollOut');
                break;
        }

    });

    /*Add new message*/
    jQuery('.add-message').on('click', function () {
        var tr = jQuery('.message-purchased').find('tr').last().clone();
        jQuery(tr).appendTo('.message-purchased');
        remove_message()
    });
    remove_message();

    function remove_message() {
        jQuery('.remove-message').unbind();
        jQuery('.remove-message').on('click', function () {
            if (confirm("Would you want to remove this message?")) {
                if (jQuery('.message-purchased tr').length > 1) {
                    var tr = jQuery(this).closest('tr').remove();
                }
            } else {

            }
        });
    }

    jQuery('input[name="wnotification_params[border_radius]"]').on('change', function () {
        var data = parseInt(jQuery('input[name="wnotification_params[background_image]"]').val());
        var image_padding = parseInt(jQuery('input[name="wnotification_params[image_padding]"]').val());
        if (parseInt(data) == 0) {
            jQuery('.message-purchase-main').css({'border-radius': jQuery(this).val() + 'px'});
            if(image_padding){
                jQuery('.wn-notification-image').css({'border-radius': jQuery(this).val() + 'px'});
            }else{
                jQuery('.wn-notification-image').css({'border-radius': '0'});
            }
        } else {
            console.log(data)
        }
    });

    jQuery('input[name="wnotification_params[image_padding]"]').on('change', function () {
        var data = parseInt(jQuery(this).val());
        var border_radius = parseInt(jQuery('input[name="wnotification_params[border_radius]"]').val());
        var p_padding = 20 - data;
        jQuery('.wn-notification-image-wrapper').css({'padding': data + 'px'});
        if(jQuery('body').hasClass('rtl')){
            jQuery('.wn-notification-message-container').css({'padding-right': p_padding + 'px'})
        }else{
            jQuery('.wn-notification-message-container').css({'padding-left': p_padding + 'px'})
        }
        if(data>0){
            jQuery('.wn-notification-image').css({'border-radius': border_radius + 'px'});
        }else{
            jQuery('.wn-notification-image').css({'border-radius': '0'});
        }
    });
    jQuery('input[name="wnotification_params[background_image]"]').on('change', function () {
        var data = jQuery(this).val();
        var init_data = {
            'black': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'red': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'pink': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'yellow': {
                'hightlight': '#000000',
                'text': '#000000',

            },
            'violet': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'blue': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'spring': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'grey': {
                'hightlight': '#000000',
                'text': '#000000',

            },
            'autumn': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'orange': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'summer': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'winter': {
                'hightlight': '#3c8b90',
                'text': '#3c8b90',

            },
            'black_friday': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'new_year': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'valentine': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'halloween': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'kids': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'father_day': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'mother_day': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'shoes': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            't_shirt': {
                'hightlight': '#ffffff',
                'text': '#ffffff',

            },
            'christmas': {
                'hightlight': '#6bbeaa',
                'text': '#6bbeaa',

            },
        };
        if (parseInt(data) == 0) {
            jQuery('#message-purchased').removeClass('wn-extended');
            jQuery('.message-purchase-main').css({'color': '#212121','background-color':'#ffffff'});
            jQuery('input[name="wnotification_params[highlight_color]"]').val('#212121').trigger('change');
            jQuery('input[name="wnotification_params[text_color]"]').val('#212121').trigger('change');
            jQuery('input[name="wnotification_params[close_icon_color]"]').val('#212121').trigger('change');
            jQuery('input[name="wnotification_params[backgroundcolor]"]').val('#ffffff').trigger('change');
        } else {
            jQuery('#message-purchased').addClass('wn-extended');
            jQuery('#woocommerce-notification-background-image').html('#message-purchased .message-purchase-main::before {background-image: url(../wp-content/plugins/woocommerce-notification/images/background/bg_'+data+'.png);');
            jQuery('input[name="wnotification_params[highlight_color]"]').val(init_data[data]['hightlight']).trigger('change');
            jQuery('input[name="wnotification_params[text_color]"]').val(init_data[data]['text']).trigger('change');
            jQuery('input[name="wnotification_params[close_icon_color]"]').val(init_data[data]['text']).trigger('change');
        }

    });
    let depending = $('.wn-rounded-conner-depending');
    if ($('input[name="wnotification_params[rounded_corner]"]').prop('checked')) {
        depending.hide()
    } else {
        depending.show()
    }
    $('input[name="wnotification_params[rounded_corner]"]').on('change', function () {
        let depending = $('.wn-rounded-conner-depending');
        let message = $('#message-purchased');
        if ($(this).prop('checked')) {
            message.addClass('wn-rounded-corner');
            depending.hide()
        } else {
            message.removeClass('wn-rounded-corner');
            depending.show()
        }
    });
    let close_icon = $('#notify-close');
    if ($('input[name="wnotification_params[show_close_icon]"]').prop('checked')) {
        close_icon.show()
    } else {
        close_icon.hide()
    }
    $('input[name="wnotification_params[show_close_icon]"]').on('change', function () {
        if ($(this).prop('checked')) {
            close_icon.show()
        } else {
            close_icon.hide()
        }
    });
    /**
     * Start Get download key
     */
    jQuery('.villatheme-get-key-button').one('click', function (e) {
        var v_button = jQuery(this);
        v_button.addClass('loading');
        var data = v_button.data();
        var item_id = data.id;
        var app_url = data.href;
        var main_domain = window.location.hostname;
        main_domain = main_domain.toLowerCase();
        var popup_frame;
        e.preventDefault();
        var download_url = v_button.attr('data-download');
        popup_frame = window.open(app_url, "myWindow", "width=380,height=600");
        window.addEventListener('message', function (event) {
            /*Callback when data send from child popup*/
            var obj = jQuery.parseJSON(event.data);
            var update_key = '';
            var message = obj.message;
            var support_until = '';
            var check_key = '';
            if (obj['data'].length > 0) {
                for (var i = 0; i < obj['data'].length; i++) {
                    if (obj['data'][i].id == item_id && (obj['data'][i].domain == main_domain || obj['data'][i].domain == '' || obj['data'][i].domain == null)) {
                        if (update_key == '') {
                            update_key = obj['data'][i].download_key;
                            support_until = obj['data'][i].support_until;
                        } else if (support_until < obj['data'][i].support_until) {
                            update_key = obj['data'][i].download_key;
                            support_until = obj['data'][i].support_until;
                        }
                        if (obj['data'][i].domain == main_domain) {
                            update_key = obj['data'][i].download_key;
                            break;
                        }
                    }
                }
                if (update_key) {
                    check_key = 1;
                    jQuery('.villatheme-autoupdate-key-field').val(update_key);
                }
            }
            v_button.removeClass('loading');
            if (check_key) {
                jQuery('<p><strong>' + message + '</strong></p>').insertAfter(".villatheme-autoupdate-key-field");
                jQuery(v_button).closest('form').submit();
            } else {
                jQuery('<p><strong> Your key is not found. Please contact support@villatheme.com </strong></p>').insertAfter(".villatheme-autoupdate-key-field");
            }
        });
    });
    /**
     * End get download key
     */
});
