/**
 * Admin YITH WooCommerce Wishlist JS
 *
 * @author YITH
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

jQuery( document ).ready( function( $ ) {

    /* === CUSTOM DEPENDENCIES HANDLER === */

    $.fn.dependency = function (deps, test, complete, args) {
        var t = $(this);

        t.on('change', function () {
            var val = test(t);

            $.each(deps, function (i, v) {
                var elem = $(v);

                if (!elem.length) {
                    return;
                }

                var target = elem.closest('tr');

                if (!target.length) {
                    return;
                }

                if( val ){
                    target.show().fadeTo("slow", 1);
                }
                else{
                    target.is( ':visible' ) ? target.fadeTo("slow", 0, function(){ target.hide() }) : target.css('opacity', 0).hide();
                }

                // val ? target.removeClass('yith-disabled') : target.addClass('yith-disabled');
            });

            if (typeof complete != 'undefined') {
                complete(t, args);
            }
        }).change();
    };

    /* === PROMOTION WIZARD HANDLER === */

    var Wizard = function( el, args ){
            var self = this;

            self.settings = {};

            self.modal = null;

            self._init = function(){
                self.settings = $.extend( {
                    template: el.data('template'),
                    template_data: {},
                    container: '.yith-wcwl-wizard-modal',
                    events: {}
                }, args );

                if( typeof self.settings.events['init'] === 'function' ){
                    self.settings.events.init( el, args );
                }

                self._initOpener();
            };

            self._initOpener = function(){
                el.on( 'click', function( ev ){
                    var t = $(this),
                        settings = self.settings.template_data;

                    ev.preventDefault();

                    // init opener-specific template data
                    if( typeof settings === 'function' ){
                        settings = ( settings )( t );
                    }

                    t.WCBackboneModal({
                        template: self.settings.template,
                        variable: settings
                    });

                    var container = $( self.settings.container );

                    self._initEditor( container );
                    self._initEnhancedSelect( container );
                    self._initTabs( container );
                    self._initSteps( container );
                    self._initOptions( container, settings );
                    self._initEvents( container, self.settings.events );
                } );
            };

            self._initEditor = function( modal ){
                modal.find( '.with-editor' ).each( function(){
                    var t = $(this),
                        id = t.attr('id');

                    // Destroy any existing editor so that it can be re-initialized when popup opens.
                    if ( tinymce.get( id ) ) {
                        restoreTextMode = tinymce.get( id ).isHidden();
                        wp.editor.remove( id );
                    }

                    wp.editor.initialize( id, {
                        tinymce: {
                            wpautop: true,
                            init_instance_callback: function (editor) {
                                editor.on('Change', function (e) {
                                    t.val( editor.getContent() ).change();
                                });
                            }
                        },
                        quicktags: true,
                        mediaButtons: true
                    } );
                } )
            };

            self._initEnhancedSelect = function( modal ){
                $(document.body).trigger( 'wc-enhanced-select-init' );
            };

            self._initTabs = function( modal ){
                modal.find( '.tabs' ).on( 'click', 'a', function( ev ){
                    var t = $(this),
                        ul = t.closest('ul'),
                        a = ul.find( 'a' ),
                        p = ul.parent(),
                        tabs = p.find( '.tab' ),
                        target = t.data( 'target' ),
                        tab = $( target ),
                        changed = false;

                    ev.preventDefault();

                    if( ! t.hasClass( 'active' ) ){
                        changed = true;
                    }

                    a.attr( 'aria-selected', 'false' ).removeClass( 'active' );
                    t.attr( 'aria-selected', 'true' ).addClass( 'active' );

                    tabs.attr( 'aria-expanded', 'false' ).removeClass( 'active' ).hide();
                    tab.attr( 'aria-expanded', 'true' ).addClass( 'active' ).show();

                    if( changed ){
                        t.trigger( 'tabChange' );
                    }
                } );
            };

            self._initOptions = function( modal, values ){
                $.each( values, function( i, v ){
                    var field = modal.find( '[name="' + i + '"]' );

                    if( ! field.length || v === field.val() ){
                        return;
                    }

                    if( field.is( 'select' ) && ! field.find( 'option[value="' + v + '"]' ).length ){
                        field.append( '<option value="' + v + '" selected="selected">' + v + ' </option>' );
                    }
                    else {
                        field.val(v);
                    }
                } );
            };

            self._initSteps = function( modal ){
                // show only first step by default
                modal.find( '.step' ).hide().first().show();

                // init continue button
                modal.find( '.continue-button' ).on( 'click', function( ev ){
                    var t = $(this),
                        current_step = t.closest( '.step' ),
                        next_step = current_step.next( '.step' );

                    ev.preventDefault();

                    if( next_step.length ) {
                        self._changeStep( modal, current_step, next_step );
                    }
                } );

                // init back button
                modal.find( '.back-button' ).on( 'click', function( ev ){
                    var t = $(this),
                        current_step = t.closest( '.step' ),
                        prev_step = current_step.prev( '.step' );

                    ev.preventDefault();

                    if( prev_step.length ) {
                        self._changeStep( modal, current_step, prev_step );
                    }
                } );
            };

            self._initEvents = function( modal, events ){
                if( typeof self.settings.events['open'] === 'function' ){
                    self.settings.events.open( el, modal );
                }

                $.each( events, function( i, v ){
                    var target = null;

                    // exclude general events
                    if( i === 'init' || i === 'open' ){
                        return;
                    }

                    // tab events
                    else if( i === 'tabChange' ){
                        target = modal.find( '.tabs' );
                    }

                    // step events
                    else if( i === 'stepChange' ){
                        target = modal.find( '.step' );
                    }

                    // input changes
                    else{
                        target = modal.find( ':input' );
                    }

                    target.on( i, function( ev ){
                        return ( v )( $(this), modal, ev );
                    } );
                } );
            };

            self._changeStep = function( modal, current, next ){
                current.animate( {
                    opacity: 0
                }, {
                    duration: 200,
                    complete: function(){
                       var modalContent = modal.find( 'article' ),
                            modalContentWidth = modalContent.outerWidth(),
                            modalContentHeight = modalContent.outerHeight();

                        // calculate step size
                        modalContent.outerWidth( 'auto' );
                        modalContent.outerHeight( 'auto' );

                        current.hide();
                        next.show();

                        var nextWidth = next.outerWidth(),
                            nextHeight = next.outerHeight();

                        next.hide();
                        current.css( 'opacity', 1 );

                        // fix modal size
                        modalContent.outerWidth( modalContentWidth );
                        modalContent.outerHeight( modalContentHeight );

                        modalContent.animate( {
                            width: nextWidth,
                            height: nextHeight
                        }, {
                            duration: 200,
                            complete: function(){
                                next.fadeIn( 200 );
                            }
                        } );
                    }
                } );

                next.trigger( 'stepChange' );
            };

            self._init();
        },
        updatePreviewXHR = null,
        updatePreview = function( el, modal, ev ){
            var preview = modal.find( '.email-preview' ),
                template = modal.find('#template').val();

            if( updatePreviewXHR ){
                updatePreviewXHR.abort();
            }

            updatePreviewXHR = $.ajax( {
                url: ajaxurl + '?action=preview_promotion_email',
                data: modal.find('form').serialize(),
                method: 'POST',
                beforeSend: function(){
                    preview.block({
                        message: null,
                        overlayCSS: {
                            background: 'transparent',
                            opacity: 0.6
                        }
                    });
                },
                complete: function(){
                    preview.unblock();
                },
                success: function( data ){
                    preview.removeClass( 'html plain' ).addClass( template ).find('.no-interactions').html( data );
                }
            } );
        },
        getPromotionWizardData = function(){
            return {
                template: 'yith-wcwl-promotion-wizard',
                template_data: function( el ){
                    var data;

                    if( el.hasClass( 'restore-draft' ) ) {
                        data = el.data( 'draft' );
                    }
                    else{
                        data = $.extend( data, {
                            product_id: el.data('product_id'),
                            user_id   : el.data('user_id')
                        } );
                    }

                    return data;
                },
                events: {
                    change: updatePreview,
                    open: function( el, modal, ev ){
                        modal.find( '#content_html-tmce' ).click();
                        updatePreview( el, modal, ev );
                    },
                    tabChange: function( el, modal, ev ){
                        modal.find( '#template' ).val( el.find( '.active' ).data( 'template' ) );
                        updatePreview( el, modal, ev );
                    },
                    stepChange: function( el, modal, ev ){
                        var counter = el.find( '.receivers-count' ),
                            additional_info = el.find( '.show-on-long-queue' ),
                            threshold = additional_info.data('threshold');

                        if( ! counter.length ){
                            return;
                        }

                        $.ajax({
                            url: ajaxurl + '?action=calculate_promotion_email_receivers',
                            data: modal.find('form').serialize(),
                            method: 'post',
                            beforeSend: function(){
                                counter.css( 'opacity', 0.3 );

                                if( additional_info.length ){
                                    additional_info.hide();
                                }
                            },
                            complete: function(){
                                counter.css( 'opacity', 1 );
                            },
                            success: function( data ){
                                if( typeof data.label === 'undefined' ){
                                    return;
                                }

                                counter.html( data.label );

                                if( additional_info.length && typeof data.count !== 'undefined' && data.count > threshold ){
                                    additional_info.show();
                                }
                            }
                        });
                    }
                }
            }
        };

    $.fn.wizard = function ( args ) {
        var t = $(this),
            w = new Wizard( t, args );
    };

    $('.create-promotion').wizard( getPromotionWizardData() );
    $('.restore-draft').wizard( getPromotionWizardData() );

    /* === UTILITY FUNCTIONS === */

    var isRadioYes = function (t) {
            if( ! t.is( 'input[type="radio"]' ) ){
                t = t.find( 'input[type="radio"]:checked' );
            }

            return t.val() === 'yes';
        },
        isRadioNo = function (t) {
            if( ! t.is( 'input[type="radio"]' ) ){
                t = t.find( 'input[type="radio"]:checked' );
            }

            return t.val() === 'no';
        },
        isChecked = function (t) {
            return t.is(':checked');
        };

    /* === SETTINGS HANDLING === */

    var disable_wishlist_for_unauthenticated_users = $('#yith_wcwl_disable_wishlist_for_unauthenticated_users'),
        multi_wishlist_enable = $('#yith_wcwl_multi_wishlist_enable'),
        enable_multi_wishlist_for_unauthenticated_users = $('#yith_wcwl_enable_multi_wishlist_for_unauthenticated_users'),
        modal_enable = $('#yith_wcwl_modal_enable'),
        loop_position = $('#yith_wcwl_loop_position'),
        icon_select = $('.icon-select'),
        add_to_cart_style = $('[name="yith_wcwl_add_to_cart_style"]'),
        add_to_cart_icon = $('#yith_wcwl_add_to_cart_icon'),
        ask_an_estimate_style = $('[name="yith_wcwl_ask_an_estimate_style"]'),
        ask_an_estimate_icon = $('#yith_wcwl_ask_an_estimate_icon'),
        enable_share = $('#yith_wcwl_enable_share'),
        share_facebook = $('#yith_wcwl_share_fb'),
        share_facebook_icon = $('#yith_wcwl_fb_button_icon'),
        share_twitter = $('#yith_wcwl_share_twitter'),
        share_twitter_icon = $('#yith_wcwl_tw_button_icon'),
        share_pinterest = $('#yith_wcwl_share_pinterest'),
        share_pinterest_icon = $('#yith_wcwl_pr_button_icon'),
        share_email = $('#yith_wcwl_share_email'),
        share_email_icon = $('#yith_wcwl_em_button_icon'),
        share_whatsapp = $('#yith_wcwl_share_whatsapp'),
        share_whatsapp_icon = $('#yith_wcwl_wa_button_icon'),
        show_estimate_button = $('#yith_wcwl_show_estimate_button'),
        show_additional_info_textarea = $('#yith_wcwl_show_additional_info_textarea'),
        ask_an_estimate_fields = $('#yith_wcwl_ask_an_estimate_fields'),
        promotion_mail_type = $('#woocommerce_promotion_mail_settings\\[email_type\\]'),
        back_in_stock_mail_enabled = $('#woocommerce_yith_wcwl_back_in_stock_settings\\[enabled\\]'),
        back_in_stock_mail_type = $('#woocommerce_yith_wcwl_back_in_stock_settings\\[email_type\\]'),
        on_sale_item_mail_enabled = $('#woocommerce_yith_wcwl_on_sale_item_settings\\[enabled\\]'),
        on_sale_item_mail_type = $('#woocommerce_yith_wcwl_on_sale_item_settings\\[email_type\\]'),
        ask_an_estimate_type = $('[id^="type_"]');

    loop_position.add('select#yith_wcwl_button_position').on('change', function () {
        var t = $(this),
            v = t.val();

        if ('shortcode' === v) {
            t.parent().next().find('.addon').show();
        } else {
            t.parent().next().find('.addon').hide();
        }
    }).change();

    ask_an_estimate_type.on( 'change', function(){
        var t = $(this),
            v = t.val(),
            options_field = t.closest( '.yith-toggle-content-row' ).next();

        if( v === 'radio' ||  v === 'select' ){
            options_field.show().fadeTo('slow' , 1);
        }
        else{
            options_field.is( ':visible' ) ? options_field.fadeTo('slow', 0, function(){ options_field.hide() }) : options_field.css('opacity', 0).hide();
        }
    } ).change();

    add_to_cart_style.on( 'change', function(){
        add_to_cart_icon.change();
    } );

    ask_an_estimate_style.on( 'change', function(){
        ask_an_estimate_icon.change();
    } );

    icon_select.each( function(){
        var t = $(this),
            renderOption = function (state) {
                if ( ! state.id ) {
                    return state.text;
                }
                return $(
                    '<span><i class="option-icon fa ' + state.element.value.toLowerCase() + '" ></i> ' + state.text + '</span>'
                );
            };

        t.select2({
            templateResult: renderOption
        });
    } );

    disable_wishlist_for_unauthenticated_users.dependency([
        '#yith_wcwl_enable_multi_wishlist_for_unauthenticated_users-yes'
    ], function(){
        return isRadioNo( disable_wishlist_for_unauthenticated_users ) && isChecked( multi_wishlist_enable );
    }, function(){
        enable_multi_wishlist_for_unauthenticated_users.change();
    } );

    multi_wishlist_enable.dependency([
        '#yith_wcwl_enable_multi_wishlist_for_unauthenticated_users-yes'
    ], function(){
        return isRadioNo( disable_wishlist_for_unauthenticated_users ) && isChecked( multi_wishlist_enable );
    }, function(){
        enable_multi_wishlist_for_unauthenticated_users.change();
    } );

    enable_multi_wishlist_for_unauthenticated_users.dependency([
        '#yith_wcwl_show_login_notice',
        '#yith_wcwl_login_anchor_text'
    ], function(){
        return isChecked( multi_wishlist_enable ) && isRadioNo( disable_wishlist_for_unauthenticated_users ) && isRadioNo( enable_multi_wishlist_for_unauthenticated_users );
    });

    modal_enable.dependency([
        '#yith_wcwl_show_exists_in_a_wishlist'
    ], function(){
        var res = modal_enable.find( ':checked' ).val() !== 'default';

        if( ! res ){
            $('#yith_wcwl_show_exists_in_a_wishlist').prop( 'checked', true );
        }

        return res;
    } );

    add_to_cart_icon.dependency([
       '#yith_wcwl_add_to_cart_custom_icon'
    ], function(){
        return 'custom' === add_to_cart_icon.val() && 'button_custom' === add_to_cart_style.filter(':checked').val();
    } );

    ask_an_estimate_icon.dependency([
       '#yith_wcwl_ask_an_estimate_custom_icon'
    ], function(){
        return 'custom' === ask_an_estimate_icon.val() && 'button_custom' === ask_an_estimate_style.filter(':checked').val();
    } );

    enable_share.dependency([
        '#yith_wcwl_share_fb'
    ], isChecked, function(){
        share_facebook.change();
        share_facebook_icon.change();
        share_twitter.change();
        share_twitter_icon.change();
        share_pinterest.change();
        share_pinterest_icon.change();
        share_email.change();
        share_email_icon.change();
        share_whatsapp.change();
        share_whatsapp_icon.change();
    } );

    share_facebook.dependency([
        '#yith_wcwl_fb_button_icon',
        '#yith_wcwl_color_fb_button_background'
    ], function(){
        return isChecked( share_facebook ) && isChecked( enable_share )
    }, function(){
        share_facebook_icon.change();
    } );

    share_facebook_icon.dependency([
        '#yith_wcwl_fb_button_custom_icon'
    ], function(){
        return isChecked( share_facebook ) && isChecked( enable_share ) && 'custom' === share_facebook_icon.val();
    } );

    share_twitter.dependency([
        '#yith_wcwl_tw_button_icon',
        '#yith_wcwl_color_tw_button_background'
    ], function(){
        return isChecked( share_twitter ) && isChecked( enable_share )
    }, function(){
        share_twitter_icon.change();
    } );

    share_twitter_icon.dependency([
        '#yith_wcwl_tw_button_custom_icon'
    ], function(){
        return isChecked( share_twitter ) && isChecked( enable_share ) && 'custom' === share_twitter_icon.val();
    } );

    share_pinterest.dependency([
        '#yith_wcwl_socials_image_url',
        '#yith_wcwl_pr_button_icon',
        '#yith_wcwl_color_pr_button_background'
    ], function(){
        return isChecked( share_pinterest ) && isChecked( enable_share )
    }, function(){
        share_pinterest_icon.change();
    } );

    share_pinterest_icon.dependency([
        '#yith_wcwl_pr_button_custom_icon'
    ], function(){
        return isChecked( share_pinterest ) && isChecked( enable_share ) && 'custom' === share_pinterest_icon.val();
    } );

    share_email.dependency([
        '#yith_wcwl_em_button_icon',
        '#yith_wcwl_color_em_button_background'
    ], function(){
        return isChecked( share_email ) && isChecked( enable_share )
    }, function(){
        share_email_icon.change();
    } );

    share_email_icon.dependency([
        '#yith_wcwl_em_button_custom_icon'
    ], function(){
        return isChecked( share_email ) && isChecked( enable_share ) && 'custom' === share_email_icon.val();
    } );

    share_whatsapp.dependency([
        '#yith_wcwl_wa_button_icon',
        '#yith_wcwl_wa_button_custom_icon',
        '#yith_wcwl_color_wa_button_background'
    ], function(){
        return isChecked( share_whatsapp ) && isChecked( enable_share )
    }, function(){
        share_whatsapp_icon.change();
    } );

    share_whatsapp_icon.dependency([
        '#yith_wcwl_wa_button_custom_icon'
    ], function(){
        return isChecked( share_whatsapp ) && isChecked( enable_share ) && 'custom' === share_whatsapp_icon.val();
    } );

    share_twitter.add( share_pinterest ).dependency([
        '#yith_wcwl_socials_title',
        '#yith_wcwl_socials_text'
    ], function(){
        return ( isChecked( share_twitter ) || isChecked( share_pinterest ) ) && isChecked( enable_share )
    });

    show_estimate_button.dependency([
        '#yith_wcwl_show_additional_info_textarea'
    ], isChecked, function(){
        show_additional_info_textarea.change()
    } );

    show_estimate_button.on( 'change', function(){
        show_additional_info_textarea.change();
    } );

    show_additional_info_textarea.dependency([
        '#yith_wcwl_additional_info_textarea_label'
    ], function(){
        return isChecked( show_estimate_button ) && isChecked( show_additional_info_textarea )
    } );

    show_additional_info_textarea.on( 'change', function(){
        var t = $(this);

        if( t.is(':checked') && show_estimate_button.is(':checked') ){
            ask_an_estimate_fields.removeClass( 'yith-disabled' );
        }
        else {
            ask_an_estimate_fields.addClass( 'yith-disabled' );
        }
    } );

    promotion_mail_type.dependency([
        '#woocommerce_promotion_mail_settings\\[content_html\\]'
    ], function(){
        return 'multipart' === promotion_mail_type.val() || 'html' === promotion_mail_type.val()
    } );

    promotion_mail_type.dependency([
        '#woocommerce_promotion_mail_settings\\[content_text\\]'
    ], function(){
        return 'multipart' === promotion_mail_type.val() || 'plain' === promotion_mail_type.val()
    } );

    back_in_stock_mail_enabled.dependency([
        '#woocommerce_yith_wcwl_back_in_stock_settings\\[product_exclusions\\]',
        '#woocommerce_yith_wcwl_back_in_stock_settings\\[category_exclusions\\]',
        '#woocommerce_yith_wcwl_back_in_stock_settings\\[email_type\\]',
        '#woocommerce_yith_wcwl_back_in_stock_settings\\[heading\\]',
        '#woocommerce_yith_wcwl_back_in_stock_settings\\[subject\\]',
        '#woocommerce_yith_wcwl_back_in_stock_settings\\[content_html\\]',
        '#woocommerce_yith_wcwl_back_in_stock_settings\\[content_text\\]'
    ], function(){
        return isChecked( back_in_stock_mail_enabled )
    }, function(){
        back_in_stock_mail_type.change();
    } );

    back_in_stock_mail_type.dependency([
        '#woocommerce_yith_wcwl_back_in_stock_settings\\[content_html\\]'
    ], function(){
        return ( 'multipart' === back_in_stock_mail_type.val() || 'html' === back_in_stock_mail_type.val() ) && isChecked( back_in_stock_mail_enabled )
    } );

    back_in_stock_mail_type.dependency([
        '#woocommerce_yith_wcwl_back_in_stock_settings\\[content_text\\]'
    ], function(){
        return ( 'multipart' === back_in_stock_mail_type.val() || 'plain' === back_in_stock_mail_type.val() ) && isChecked( back_in_stock_mail_enabled )
    } );

    on_sale_item_mail_enabled.dependency([
        '#woocommerce_yith_wcwl_on_sale_item_settings\\[product_exclusions\\]',
        '#woocommerce_yith_wcwl_on_sale_item_settings\\[category_exclusions\\]',
        '#woocommerce_yith_wcwl_on_sale_item_settings\\[email_type\\]',
        '#woocommerce_yith_wcwl_on_sale_item_settings\\[heading\\]',
        '#woocommerce_yith_wcwl_on_sale_item_settings\\[subject\\]',
        '#woocommerce_yith_wcwl_on_sale_item_settings\\[content_html\\]',
        '#woocommerce_yith_wcwl_on_sale_item_settings\\[content_text\\]'
    ], function(){
        return isChecked( on_sale_item_mail_enabled )
    }, function(){
        on_sale_item_mail_type.change();
    } );

    on_sale_item_mail_type.dependency([
        '#woocommerce_yith_wcwl_on_sale_item_settings\\[content_html\\]'
    ], function(){
        return ( 'multipart' === on_sale_item_mail_type.val() || 'html' === on_sale_item_mail_type.val() ) && isChecked( on_sale_item_mail_enabled )
    } );

    on_sale_item_mail_type.dependency([
        '#woocommerce_yith_wcwl_on_sale_item_settings\\[content_text\\]'
    ], function(){
        return ( 'multipart' === on_sale_item_mail_type.val() || 'plain' === on_sale_item_mail_type.val() ) && isChecked( on_sale_item_mail_enabled )
    } );

    /* === TOGGLE BOX HANDLING === */

    $(document).on( 'yith-add-box-button-toggle', function(){
        var ask_an_estimate_type_new = $('#new_type'),
            ask_an_estimate_options_new = $('#new_options'),
            target = ask_an_estimate_options_new.closest('.yith-add-box-row');

        ask_an_estimate_type_new.on( 'change', function(){
            var v = ask_an_estimate_type_new.val();

            if( v === 'radio' ||  v === 'select' ){
                target.show().fadeTo('slow', 1);
            }
            else{
                target.is( ':visible' ) ? target.fadeTo('slow', 0, function(){ $(this).hide() }) : target.css('opacity', 0).hide();
            }
        } ).change();
    } );
} );