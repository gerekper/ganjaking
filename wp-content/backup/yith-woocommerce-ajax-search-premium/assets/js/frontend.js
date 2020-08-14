/**
 * frontend.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Ajax Search
 * @version 1.1.1
 */
jQuery(document).ready(function($){
    "use strict";

    var el = $('.yith-s'),
        def_loader = ( typeof woocommerce_params != 'undefined' && typeof woocommerce_params.ajax_loader_url != 'undefined' ) ? woocommerce_params.ajax_loader_url : yith_wcas_params.loading,
        loader_icon = el.data('loader-icon') == '' ? def_loader : el.data('loader-icon'),


        min_chars = el.data('min-chars');


    el.each( function(){
        var $t = $(this),
            $form = $t.closest('form'),
            have_results = false,
            search_categories = $form.find('.search_categories'),
            post_type = $form.find('.yit_wcas_post_type'),
            search_button = $form.find('#yith-searchsubmit'),
            lang = ( $form.find('[name="lang"]').length > 0 ) ?  $form.find('[name="lang"]').val() : '',
            append_to  = ( typeof  $t.data('append-to') == 'undefined') ? $t.closest('.yith-ajaxsearchform-container') : $t.closest( $t.data('append-to') ),
            ajaxurl = yith_wcas_params.ajax_url.toString().replace( '%%endpoint%%', 'yith_ajax_search_products' );


            search_button.on('click', function(){
                if( search_categories.length ){
                   $form.submit();
               }else{
                   if( $form.find('.yith-s').val()=='' ){
                       return false;
                   }
               }
                return true;
            });

            $t.yithautocomplete({
                minChars: min_chars,
                maxHeight: 'auto',
                appendTo: append_to,
                triggerSelectOnValidInput: false,
                serviceUrl: ajaxurl + '&post_type=' + post_type.val()+ '&lang='+ lang + '&action=yith_ajax_search_products',
                onSearchStart: function () {
                    $t.css({'background-image': 'url(' + loader_icon + ')','background-repeat': 'no-repeat', 'background-position': 'center right'});
                },

                onSearchComplete: function () {
                    $t.css('background-image', 'none');
                    $(window).trigger('resize');
                    $t.trigger('focus');
                },

                onSelect: function (suggestion) {
                    if (suggestion.id != -1) {
                        $(this).val('')
                        window.location.href = suggestion.url;
                    }
                },

                beforeRender: function (){

                    if( yith_wcas_params.show_all == 'true' && have_results ){
                        var params = {s: $t.val(), post_type: $form.find('.yit_wcas_post_type').val()};
                        if ($form.find('.search_categories').length > 0) {
                            params['product_cat'] = $form.find('.search_categories').val();
                        }

                        var action = $form.attr('action'),
                            separator = action.indexOf('?') !== -1 ? "&" : "?",
                            url = action + separator + $.param(params),
                            div_result = '<div class="link-result"><a href="' + url + '">' + yith_wcas_params.show_all_text + '</a></div>',
                            $autocomplete = append_to.find('.autocomplete-suggestions');
                            $autocomplete.append(div_result);

                    }
                },

                transformResult: function (response) {
                    response = typeof response === 'string' ? $.parseJSON(response) : response;
                    have_results = response.results;
                    return response ;
                },

                formatResult: function (suggestion, currentValue) {
                    var pattern = '(' + $.YithAutocomplete.utils.escapeRegExChars(currentValue) + ')';
                    var html = '';
                    if ( typeof suggestion.img !== 'undefined' ) {
                        html += suggestion.img;
                    }


                    html += '<div class="yith_wcas_result_content"><div class="title">';
                    html += suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
                    html += '</div>';

					if ( typeof suggestion.sku !== 'undefined' ) {
						html += suggestion.sku ;
					}


                    if ( typeof suggestion.product_categories !== 'undefined' ) {
                        html += ' ' +  suggestion.product_categories;
                    }


                    if ( typeof suggestion.div_badge_open !== 'undefined' ) {
                        html += suggestion.div_badge_open;
                    }

                    if ( typeof suggestion.on_sale !== 'undefined' ) {
                        html += suggestion.on_sale;
                    }

                    if ( typeof suggestion.outofstock !== 'undefined' ) {
                        html += suggestion.outofstock;
                    }

                    if ( typeof suggestion.featured !== 'undefined' ) {
                        html += suggestion.featured;
                    }

                    if ( typeof suggestion.div_badge_close !== 'undefined' ) {
                        html += suggestion.div_badge_close;
                    }

                    if ( typeof suggestion.price !== 'undefined' && suggestion.price != '' ) {
                        html += ' ' + yith_wcas_params.price_label  + ' ' + suggestion.price;
                    }

                    if ( typeof suggestion.excerpt !== 'undefined' ) {
                        html += ' ' +  suggestion.excerpt.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
                    }

                    html += '</div>';

                    return html;
                }
            });

            if( search_categories.length ){
                search_categories.on( 'change', function( e ){
                    var ac = $t.yithautocomplete(),
                        ajaxurl = yith_wcas_params.ajax_url.toString().replace( '%%endpoint%%', 'yith_ajax_search_products' );

                    if( search_categories.val() != '' ) {
                        ac.setOptions({
                            serviceUrl:  ajaxurl + '&product_cat=' + search_categories.val()+ '&lang='+lang
                        });
                    }else{
                        ac.setOptions({
                            serviceUrl:  ajaxurl+ '&lang='+lang
                        });
                    }

                    // update suggestions
                    ac.hide();
                    ac.onValueChange();
                });
            }



            if( post_type.length ){

                if( post_type.val() == 'any' ){
                    search_categories.attr('disabled','disabled');
                }else{
                    search_categories.removeAttr('disabled');
                }

                post_type.on( 'change', function( e ){

                    var ac = $t.yithautocomplete(),
                        ajaxurl = yith_wcas_params.ajax_url.toString().replace( '%%endpoint%%', 'yith_ajax_search_products' );

                    if( post_type.val() == 'any' ){
                        search_categories.attr('disabled','disabled');
                    }else{
                        search_categories.removeAttr('disabled');
                    }

                    if( post_type.val() != '' ) {
                        ac.setOptions({
                            serviceUrl:  ajaxurl + '&post_type=' + post_type.val() + '&lang='+lang
                        });
                    }else{
                        ac.setOptions({
                            serviceUrl:  ajaxurl + '&lang='+lang
                        });
                    }

                    // update suggestions
                    ac.hide();
                    ac.onValueChange();
                });
            }
    });
});