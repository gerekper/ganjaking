/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */

jQuery(document).ready(function ($) {
    "use strict";

    $( '.yith-wrvp-shortcode-tab' ).each( function(){
        var shortcode_option = $(this).find( 'input.shortcode-option, select.shortcode-option' ),
            preview = $( this ).find( '.shortcode-preview' );

        shortcode_option.each( function(){

            $(this).on( 'change', function(){

                if( this.type == 'radio' && ! $(this).is( ':checked' ) ) {
                    return;
                }

                var value = ( this.type == 'checkbox' && ! $(this).is( ':checked' ) ) ? $(this).data('novalue') : $(this).val(),
                    name  = $( this ).data( 'attr_name' ),
                    shortcode = preview.html(),
                    attr;

                // remove old
                var reg = new RegExp( name + '="([^"]*)"', 'g' );
                shortcode = shortcode.replace( reg, '' );

                if( ! value ) {
                    preview.html( shortcode );
                    return;
                }

                // else add attr
                shortcode = shortcode.replace(']', '');

                attr = name + '="' + value + '"';
                preview.html( shortcode + ' ' + attr + ']' )

            });
        });
    });


    $(document).on( 'click', 'button.ywrvp-send-test-email', function(){
        $(this).prev().val(true);
    });

    /*#############################
      ** VALIDATE COUPON
      ############################*/
    var xhr;
    $(document).find('input.yith_wrvp_coupon_validate').on( 'keyup', function(){
        var t   = $(this),
            val = t.val();

        if (xhr) {
            xhr.abort();
        }

        if( ! val ) {
            t.removeClass('error valid loading');
        }
        else {
            t.addClass('loading');
            xhr = $.ajax({
                url: ajaxurl,
                data: {
                    action: 'yith_wrvp_validate_coupon',
                    code: val
                },
                dataType: 'json',
                success: function( response ) {
                    t.removeClass('loading');
                    if( response.valid) {
                        t.removeClass('error').addClass('valid');
                    }
                    else {
                        t.removeClass('valid').addClass('error');
                    }
                }
            });
        }
    }).keyup();

    /*############################
        DEPS ADMIN EMAIL PANEL
    ##############################*/

    $(document).find('select, input').on( 'change', function() {
        var t       = $(this),
            deps    = $(document).find('[data-deps="'+t.attr('name')+'"]'),
            value   = t.val();

        if( deps.length ){
            // check for checkbox
            if( t.is(':checkbox') ) {
                value = t.is( ':checked' ) ? 'yes' : 'no';
            }
            // check for radio. Be sure to consider only the checked one
            if( t.is(':radio') && ! t.is( ':checked' ) ) {
                return;
            }

            $.each(deps, function(){
                var deps_values = ( typeof $(this).data('deps_value') != 'undefined' ) ? $(this).data('deps_value').split(',') : '',
                    elem        = $(this).closest('tr');
                ( ( $.inArray( value, deps_values ) !== -1 ) || ( ! deps_values && value ) ) ? elem.show() : elem.hide();
            });
        }
    }).change();

    /*##########################
      CUSTOM CHECKLIST
    ###########################*/

    var array_unique_noempty, element_box;

    array_unique_noempty = function (array) {
        var out = [];

        $.each(array, function (key, val) {
            val = $.trim(val);

            if (val && $.inArray(val, out) === -1) {
                out.push(val);
            }
        });

        return out;
    };

    element_box = {
        clean: function (tags) {

            tags = tags.replace(/\s*,\s*/g, ',').replace(/,+/g, ',').replace(/[,\s]+$/, '').replace(/^[,\s]+/, '');

            return tags;
        },

        parseTags: function (el) {
            var id = el.id,
                num = id.split('-check-num-')[1],
                element_box = $(el).closest('.ywrvp-checklist-div'),
                values = element_box.find('.ywrvp-values'),
                current_values = values.val().split(','),
                new_elements = [];

            delete current_values[num];

            $.each(current_values, function (key, val) {
                val = $.trim(val);
                if (val) {
                    new_elements.push(val);
                }
            });

            values.val(this.clean(new_elements.join(',')));

            this.quickClicks(element_box);
            return false;
        },

        quickClicks: function (el) {

            var values = $('.ywrvp-values', el),
                values_list = $('.ywrvp-value-list ul', el),

                id = $(el).attr('id'),
                current_values;

            if (!values.length)
                return;

            current_values = values.val().split(',');
            values_list.empty();

            $.each(current_values, function (key, val) {

                var item, xbutton;

                val = $.trim(val);

                if (!val)
                    return;

                item = $('<li class="select2-search-choice" />');
                xbutton = $('<a id="' + id + '-check-num-' + key + '" class="select2-search-choice-close" tabindex="0"></a>');

                xbutton.on('click keypress', function (e) {

                    if (e.type === 'click' || e.keyCode === 13) {

                        if (e.keyCode === 13) {
                            $(this).closest('.ywrvp-checklist-div').find('input.ywrvp-insert').focus();
                        }

                        element_box.parseTags(this);
                    }

                });

                item.prepend('<div><div class="selected-option" data-id="' + val + '">' + val + '</div></div>').prepend(xbutton);

                values_list.append(item);

            });
        },

        flushTags: function (el, a, f) {

            var current_values,
                new_values,
                text,
                values = $('.ywrvp-values', el),
                add_new = $('input.ywrvp-insert', el);

            a = a || false;

            text = a ? $(a).text() : add_new.val();

            if ('undefined' == typeof( text )) {
                return false;
            }

            current_values = values.val();
            new_values = current_values ? current_values + ',' + text : text;
            new_values = this.clean(new_values);
            new_values = array_unique_noempty(new_values.split(',')).join(',');
            values.val(new_values);

            this.quickClicks(el);

            if (!a)
                add_new.val('');
            if ('undefined' == typeof( f ))
                add_new.focus();

            return false;

        },

        init: function () {
            var ajax_div = $('.ywrvp-checklist-ajax');

            $('.ywrvp-checklist-div').each(function () {
                element_box.quickClicks(this);
            });

            $('input.ywrvp-insert', ajax_div).keyup(function (e) {
                if (13 == e.which) {
                    element_box.flushTags( $(this).closest('.ywrvp-checklist-div') );
                    return false;
                }
            }).keypress(function (e) {
                if (13 == e.which) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    };

    element_box.init();

    /*##########################
      TABS NAVIGATION
    ###########################*/

    $(document).on( 'click', '.yith-wrvp-shortcode-tabs-nav a', function(ev){
        ev.preventDefault();
        var dest = $( document ).find( $( this ).attr( 'href' ) ),
            wrap = $(this).closest('li');

        if( ! dest.length )
            return false;

        wrap.addClass('active').siblings().removeClass('active');
        dest.show().siblings( '.yith-wrvp-shortcode-tab' ).hide();
    });

});