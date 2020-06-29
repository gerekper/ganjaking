jQuery( document ).ready( function ( $ ) {

    var startDateTextBox = $('.wcdls_datepicker');
    startDateTextBox.datepicker({
        dateFormat: 'yy-mm-dd',
    });

    // Shared function by categories and tags
    var results = function ( data ) {
        var terms = [];
        if ( data ) {
            $.each( data, function ( id, text ) {
                terms.push( { id: id, text: text } );
            } );
        }
        return {
            results: terms
        };
    };

    var initSelection   = function ( element, callback ) {
        var data     = $.parseJSON( element.attr( 'data-selected' ) );
        var selected = [];

        $( element.val().split( ',' ) ).each( function ( i, val ) {
            selected.push( {
                id  : val,
                text: data[ val ]
            } );
        } );
        return callback( selected );
    };
    var formatSelection = function ( data ) {
        return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
    };


    var $add_new_condition          = $( '#yith-wcdls-new-condition' ),
        $condition_list             = $( '#yith-wcdls-list-conditions' );

    $add_new_condition.on( 'click', function ( event ) {

        event.preventDefault();
        var post_data = {
            index : $( '.yith-wcdls-conditions-row' ).size(),
            action: 'yith_wcdls_add_condition_row'
        };
        $.ajax( {
            type   : "POST",
            data   : post_data,
            url    : yith_wcdls_admin.ajaxurl,
            success: function ( response ) {
                $condition_list.append( response );

            }
        } );
    } );

    var ywcdls_rule_metabox = {
        init                  : function () {
            $( document ).on( 'click', '.yith-wcdls-delete-condition', this.delete_condition );
            $( document ).on( 'change', '.yith-wcdls-get-type-restriction', this.yith_change );
            $( document ).on( 'change', '.yith-wcdls-remove-product-option', this.yith_remove_product_option);
            $( document ).on( 'change', '.yith-wcdls-offer-no-accepted-option', this.yith_show_another_offer);
            $( document ).on( 'change','#yith_wcdls_product_selector_added',this.yith_apply_offer);
            this.show_fields();
            this.yith_remove_product_option();
            this.yith_show_another_offer();
            this.yith_apply_offer();
        },

        delete_condition      : function () {
            $( this ).closest( '.yith-wcdls-conditions-row' ).remove();
        },
        show_fields           : function () {
            $( '.yith-wcdls-li' ).each( function () {
                var row = $( this ).closest( '.yith-wcdls-row' );
                if ( $( this ).hasClass( 'yith-wcdls-hide-rule-set' ) ) {
                    row.hide();
                } else {
                    if ( $( this ).hasClass( 'yith-wcdls-rule-set' ) ) {
                        $( this ).show();
                        if ( $( this ).hasClass( 'yith-wcdls-select' ) ){
                            if ($( this ).hasClass( 'yith-wcdls-selector2' ) ) {
                                //undefined for prevent colissions with the event .change
                                ywcdls_rule_metabox.yith_change( 'undefined', $( this ), $( this ).data( 'type' ) );
                            } else {
                                $( this ).select2();
                            }
                        }
                    }
                }
            } );
        },
        yith_change           : function ( event, selector, type_restriction ) {
            if ( typeof(selector) === 'undefined' ) selector = $( this );
            if ( typeof(type_restriction) === 'undefined' ) type_restriction = $( this ).val();
            var row = selector.closest( '.yith-wcdls-conditions-row' );
            row.find( '.yith-wcdls-select2' ).hide();
            row.find( '.yith-wcdls' ).css( 'display', 'none' );
            switch ( type_restriction ) {
                case 'price':
                    row.find( '.yith-wcdls-restriction-type option[value=""]' ).attr( 'selected', 'selected' );
                    row.find( '.yith-wcdls-restriction-by-price' ).show();
                    row.find( '.yith-wcdls-rule-price' ).select2();
                    row.find( '.yith-wcdls-input-price' ).css( 'display', 'inline' );
                    break;
                case 'category':
                    row.find( '.yith-wcdls-restriction-by' ).show();
                    row.find( '.yith-wcdls-restriction-type' ).select2();

                    row.find( '.yith-wcdls-select2-categories' ).show();
                    row.find( '.yith-wcdls-categories' ).select2();
                    row.find( ':input.yith-wcdls-category-search' ).filter( ':not(.enhanced)' ).each( function () {
                        var ajax = {
                            url        : yith_wcdls_admin.ajaxurl,
                            dataType   : 'json',
                            quietMillis: 250,
                            data       : function ( term ) {
                                return {
                                    term    : term,
                                    action  : 'yith_wcdls_category_search',
                                    security: yith_wcdls_admin.search_categories_nonce
                                };
                            },
                            cache      : true
                        };

                        if ( yith_wcdls_admin.before_3_0 ) {
                            ajax.results = results;
                        } else {
                            ajax.processResults = results;
                        }
                        var select2_args = {
                            initSelection     : yith_wcdls_admin.before_3_0 ? initSelection : null,
                            formatSelection   : yith_wcdls_admin.before_3_0 ? formatSelection : null,
                            multiple          : $( this ).data( 'multiple' ),
                            allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
                            placeholder       : $( this ).data( 'placeholder' ),
                            minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                            escapeMarkup      : function ( m ) {
                                return m;
                            },
                            ajax              : ajax
                        };
                        $( this ).select2( select2_args ).addClass( 'enhanced' ).on( 'change', function () {

                        } );
                        $( document.body ).trigger( 'wc-enhanced-select-init' );

                    } );

                    break;

                case 'tag':

                    row.find( '.yith-wcdls-restriction-by' ).show();
                    row.find( '.yith-wcdls-restriction-type' ).select2();

                    row.find( '.yith-wcdls-select2-tags' ).show();
                    row.find( '.yith-wcdls-tags' ).select2();

                    row.find( ':input.yith-wcdls-tags-search' ).filter( ':not(.enhanced)' ).each( function () {
                        var ajax = {
                            url        : yith_wcdls_admin.ajaxurl,
                            dataType   : 'json',
                            quietMillis: 250,
                            data       : function ( term ) {
                                return {
                                    term    : term,
                                    action  : 'yith_wcdls_tag_search',
                                    security: yith_wcdls_admin.search_tags_nonce
                                };
                            },
                            cache      : true
                        };

                        if ( yith_wcdls_admin.before_3_0 ) {
                            ajax.results = results;
                        } else {
                            ajax.processResults = results;
                        }
                        var select2_args = {
                            initSelection     : yith_wcdls_admin.before_3_0 ? initSelection : null,
                            formatSelection   : yith_wcdls_admin.before_3_0 ? formatSelection : null,
                            multiple          : $( this ).data( 'multiple' ),
                            allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
                            placeholder       : $( this ).data( 'placeholder' ),
                            minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                            escapeMarkup      : function ( m ) {
                                return m;
                            },
                            ajax              : ajax
                        };
                        $( this ).select2( select2_args ).addClass( 'enhanced' ).on( 'change', function () {

                        } );

                        $( document.body ).trigger( 'wc-enhanced-select-init' );
                    } );


                    break;

                case 'product':
                    row.find( '.yith-wcdls-restriction-by' ).show();
                    row.find( '.yith-wcdls-restriction-type' ).select2();

                    row.find( '.yith-wcdls-select2-product' ).show();
                    row.find( '.yith-wcdls-product-search' ).css( 'display', 'inline' );
                    $( document.body ).trigger( 'wc-enhanced-select-init' );
                    break;

                case 'geolocalization':
                    row.find( '.yith-wcdls-restriction-by' ).show();
                    row.find( '.yith-wcdls-restriction-type' ).select2();
                    row.find( '.yith-wcdls-select2-geolocalization' ).show();
                    row.find( '.yith-wcdls-geolocalization-search' ).select2();
                    break;

                case 'role':
                    row.find( '.yith-wcdls-restriction-by' ).show();
                    row.find( '.yith-wcdls-restriction-type' ).select2();
                    row.find( '.yith-wcdls-select2-role' ).show();
                    row.find( '.yith-wcdls-role-search' ).select2();
                    break;

                case 'user':
                    row.find( '.yith-wcdls-restriction-by' ).show();
                    row.find( '.yith-wcdls-restriction-type' ).select2();
                    row.find( '.yith-wcdls-select2-users' ).show();
                    $( document.body ).trigger( 'wc-enhanced-select-init' );
                    break;
            }
        },
        yith_remove_product_option: function() {
            if($(this).filter(':checked').val() == 'remove_some_products') {
                //$('.yith-wcdls-apply-type-offer').show(600);
                $('#yith_wcdls_product_selector_remove').prop('disabled',false);
            }else {
                //$('.yith-wcdls-apply-type-offer').hide(600);
                $('#yith_wcdls_product_selector_remove').prop('disabled',true);
            }
        },
        yith_show_another_offer: function() {
            if($(this).filter(':checked').val() == 'show_another_offer') {
                $('#yith-wcdls-no-accepted-option').prop('disabled',true);
                $('.yith-wcdls-select-offers').show(600);
            }else {
                $('.yith-wcdls-select-offers').hide(600);
                $('#yith-wcdls-no-accepted-option').prop('disabled',false);
            }
        },
        yith_apply_offer: function() {
            
            $data = $('#yith_wcdls_product_selector_added').select2('data');
            if(typeof $data !== 'undefined' && $data.length > 0) {
                $('.yith-wcdls-apply-type-offer').show(600);
            }else{
                $('.yith-wcdls-apply-type-offer').hide(600);
            }
        }
    };

    ywcdls_rule_metabox.init();
    $('.yith-wcdls-select-woo').select2();
} );