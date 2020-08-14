/**
 * frontend.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.0.0
 */

jQuery(document).ready(function($) {
    "use strict";

    var is_variation_modal_opened   = false,
        update_form                 = function( form, wrap, variation_id ){
        var input       = form.find('.yith-wfbt-items input'),
            group       = [],
            unchecked   = [];

        // show only necessary
        input.each(function(i){
            group[i] = this.value;
            if( ! $(this).is(':checked') ) {
                unchecked.push( this.value );
            }
        });

        form.block({
            message: null,
            overlayCSS: {
                background: '#fff url(' + yith_wfbt.loader + ') no-repeat center',
                opacity: 0.6
            }
        });

        $.ajax({
            type: 'post',
            url: yith_wfbt.ajaxurl.toString().replace( '%%endpoint%%', yith_wfbt.refreshForm ),
            data: {
                action: yith_wfbt.refreshForm,
                product_id: form.find( 'input[name="yith-wfbt-main-product"]' ).val(),
                variation_id : variation_id,
                group: group,
                unchecked: unchecked,
                context: 'frontend',
            },
            dataType: 'html',
            success: function( response ) {
                wrap.replaceWith( response );
            },
            complete: function () {
                form.unblock();
                $( document ).trigger( 'yith_wfbt_form_updated', [ form ] );
            }
        });
    }

    $(document).on( 'change', '.yith-wfbt-items input', function(){
        var is_choise_variation = $(this).closest('.yith-wfbt-item').hasClass('choise-variation');
        update_form( $(this).closest('.yith-wfbt-form'), $(this).closest( '.yith-wfbt-section' ), 0 );
    });


    $( document ).on( 'show_variation', '.variations_form', function( ev, data ){
        if( is_variation_modal_opened || ! data.is_in_stock ){
            return;
        }
        update_form( $('.yith-wfbt-form'), $('.yith-wfbt-section' ), data.variation_id );
    });

    /********************
     * SLIDER SHORTCODE
     *******************/

    var slider = $(document).find( '.yith-wfbt-products-list' ),
        nav    = slider.next( '.yith-wfbt-slider-nav' );

    if( slider.length ) {

        slider.owlCarousel({
            loop: true,
            dots: false,
            responsive : {
                0: {
                    items: 2
                },
                // breakpoint from 480 up
                480: {
                    items: 3
                },
                // breakpoint from 768 up
                768: {
                    items: yith_wfbt.visible_elem
                }
            }
        });

        if( nav.length ) {
            nav.find('.yith-wfbt-nav-prev').click(function () {
                slider.trigger('prev.owl.carousel');
            });

            nav.find('.yith-wfbt-nav-next').click(function () {
                slider.trigger('next.owl.carousel');
            })
        }
    }

    /********************
     * SUPPORT FOR VARIATIONS
     *******************/
    $(document).on( 'click','.yith-wfbt-open-modal',function(event){
        event.preventDefault();
        var product_id = $(this).data('product_id'),
            form = $('.yith-wfbt-form'),
            selectVariationsModal = $('#yith-wfbt-modal');

        form.block({
            message: null,
            overlayCSS: {
                background: '#fff url(' + yith_wfbt.loader + ') no-repeat center',
                opacity: 0.6
            }
        });

        $.ajax({
            type: 'post',
            url: yith_wfbt.ajaxurl.toString().replace( '%%endpoint%%', yith_wfbt.loadVariationsDialog ),
            data: {
                action: yith_wfbt.loadVariationsDialog,
                product_id: product_id,
                context: 'frontend'
            },
            dataType: 'html',
            success: function( response ) {
                // set global variable
                is_variation_modal_opened = true;

                selectVariationsModal.html( response );
                form.unblock();
                selectVariationsModal.modal();
                selectVariations();
            },
            complete: function () {
                $( document ).trigger( 'yith_wfbt_modal_opened', [ form ] );
            }
        });


    });

    var selectVariations = function(){

        $('.variations_form').each(function () {
            $(this).wc_variation_form();
        });

        $('.variations_form.cart').on('found_variation', function (e, variation) {

            $('input[name="yith-wfbt-variation-id"]').val( variation.variation_id );

            if( variation.availability_html ) {
                $('.yith-wfbt-stock-status').html( variation.availability_html );
            }

            if( ! variation.is_purchasable || ! variation.is_in_stock || ! variation.variation_is_visible ) {
                $('#yith-wfbt-submit-variation').prop( "disabled", true );
            } else {
                $('#yith-wfbt-submit-variation').prop( "disabled", false );
            }
        });

        $('.variations_form.cart').on( 'click', '.reset_variations', function (e) {
            $('.yith-wfbt-stock-status').html( '' );
            $('#yith-wfbt-submit-variation').prop( "disabled", true );
        });
    };


    $(document).on( 'click','#yith-wfbt-submit-variation',function(event){
        var variationId = $('input[name="yith-wfbt-variation-id"]').val(),
            mainProductId = $('input[name="yith-wfbt-main-product-id"]').val(),
            form = $('.yith-wfbt-form');

        $.modal.close();
        form.find('input[data-variable_product_id='+ mainProductId +']').prop( "disabled", false ).prop('checked',true).val(variationId);
        update_form( form, $('.yith-wfbt-section' ), 0 );
    });

    // empty modal on close to prevent issue
    $(document).on( 'modal:after-close', function() {
        is_variation_modal_opened = false;
        $('#yith-wfbt-modal').html('');
    });

});