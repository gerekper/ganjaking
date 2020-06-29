var init_fue_product_search,
    init_fue_customer_search,
    init_fue_select,
    init_fue_coupon_search;

(function( $ ) {
    init_fue_product_search = function() {

        $(":input.wc-product-search").filter(":not(.enhanced)").each( function() {
            var select2_args = {
                allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
                placeholder: jQuery( this ).data( 'placeholder' ),
                dropdownAutoWidth: 'true',
                minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
                escapeMarkup: function( m ) {
                    return m;
                },
                ajax: {
                    url:         ajaxurl,
                    dataType:    'json',
                    quietMillis: 250,
                    data: function( term, page ) {
                        return {
                            term:     term,
                            action:   jQuery( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
                            security: FUE.nonce
                        };
                    },
                    results: function( data, page ) {
                        var terms = [];
                        if ( data ) {
                            jQuery.each( data, function( id, text ) {
                                terms.push( { id: id, text: text } );
                            });
                        }
                        return { results: terms };
                    },
                    cache: true
                }
            };

            if ( jQuery( this ).data( 'multiple' ) === true ) {
                select2_args.multiple = true;
                select2_args.initSelection = function( element, callback ) {
                    var data     = jQuery.parseJSON( element.attr( 'data-selected' ) );
                    var selected = [];

                    jQuery( element.val().split( "," ) ).each( function( i, val ) {
                        selected.push( { id: val, text: data[ val ] } );
                    });
                    return callback( selected );
                };
                select2_args.formatSelection = function( data ) {
                    return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                };
            } else {
                select2_args.multiple = false;
                select2_args.initSelection = function( element, callback ) {
                    var data = {id: element.val(), text: element.attr( 'data-selected' )};
                    return callback( data );
                };
            }


            jQuery(this).select2(select2_args).addClass( 'enhanced' );
        } );

    }

    init_fue_customer_search = function() {
        $( ':input.fue-customer-search' ).filter( ':not(.enhanced)' ).each( function() {
            var select2_args = {
                allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
                placeholder: $( this ).data( 'placeholder' ),
                minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                escapeMarkup: function( m ) {
                    return m;
                },
                ajax: {
                    url:         ajaxurl,
                    dataType:    'json',
                    quietMillis: 250,
                    data: function( term ) {
                        return {
                            term:     term,
                            action:   'fue_json_search_customers',
                            nonce: $( this ).data( 'nonce' ),
                            exclude:  $( this ).data( 'exclude' )
                        };
                    },
                    results: function( data ) {
                        var terms = [];
                        if ( data ) {
                            $.each( data, function( id, text ) {
                                terms.push({
                                    id: id,
                                    text: text
                                });
                            });
                        }
                        return { results: terms };
                    },
                    cache: true
                }
            };
            if ( $( this ).data( 'multiple' ) === true ) {
                select2_args.multiple = true;
                select2_args.initSelection = function( element, callback ) {
                    var data     = $.parseJSON( element.attr( 'data-selected' ) );
                    var selected = [];

                    $( element.val().split( ',' ) ).each( function( i, val ) {
                        selected.push({
                            id: val,
                            text: data[ val ]
                        });
                    });
                    return callback( selected );
                };
                select2_args.formatSelection = function( data ) {
                    return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                };
            } else {
                select2_args.multiple = false;
                select2_args.initSelection = function( element, callback ) {
                    var data = {
                        id: element.val(),
                        text: element.attr( 'data-selected' )
                    };
                    return callback( data );
                };
            }

            $( this ).select2( select2_args ).addClass( 'enhanced' );
        });

        $(":input.email-search-select").filter(":not(.enhanced)").each( function() {
            var select2_args = {
                allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
                placeholder: jQuery( this ).data( 'placeholder' ),
                dropdownAutoWidth: 'true',
                minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
                escapeMarkup: function( m ) {
                    return m;
                },
                ajax: {
                    url:         ajaxurl,
                    dataType:    'json',
                    quietMillis: 250,
                    data: function( term, page ) {
                        return {
                            term:     term,
                            action:   jQuery( this ).data( 'action' ) || 'fue_search_for_email'
                        };
                    },
                    results: function( data, page ) {
                        var terms = [];
                        if ( data ) {
                            jQuery.each( data, function( id, text ) {
                                terms.push( { id: id, text: text } );
                            });
                        }
                        return { results: terms };
                    },
                    cache: true
                }
            };

            if ( jQuery( this ).data( 'multiple' ) === true ) {
                select2_args.multiple = true;
                select2_args.initSelection = function( element, callback ) {
                    var data     = jQuery.parseJSON( element.attr( 'data-selected' ) );
                    var selected = [];

                    jQuery( element.val().split( "," ) ).each( function( i, val ) {
                        selected.push( { id: val, text: data[ val ] } );
                    });
                    return callback( selected );
                };
                select2_args.formatSelection = function( data ) {
                    return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                };
            } else {
                select2_args.multiple = false;
                select2_args.initSelection = function( element, callback ) {
                    var data = {id: element.val(), text: element.attr( 'data-selected' )};
                    return callback( data );
                };
            }


            jQuery(this).select2(select2_args).addClass( 'enhanced' );
        } );

    }

    init_fue_select = function() {
        $(":input.select2").filter(":not(.enhanced)").each( function() {
            $(this).select2();
        } );
    }

    init_fue_coupon_search = function() {

        $(":input.wc-coupon-search").filter(":not(.enhanced)").each( function() {
            var select2_args = {
                allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
                placeholder: jQuery( this ).data( 'placeholder' ),
                dropdownAutoWidth: 'true',
                minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
                escapeMarkup: function( m ) {
                    return m;
                },
                ajax: {
                    url:         ajaxurl,
                    dataType:    'json',
                    quietMillis: 250,
                    data: function( term, page ) {
                        return {
                            term:     term,
                            action:   jQuery( this ).data( 'action' ) || 'fue_wc_json_search_coupons',
                            security: FUE.nonce
                        };
                    },
                    results: function( data, page ) {
                        var terms = [];
                        if ( data ) {
                            jQuery.each( data, function( id, text ) {
                                terms.push( { id: id, text: text } );
                            });
                        }
                        return { results: terms };
                    },
                    cache: true
                }
            };

            if ( jQuery( this ).data( 'multiple' ) === true ) {
                select2_args.multiple = true;
                select2_args.initSelection = function( element, callback ) {
                    var data     = jQuery.parseJSON( element.attr( 'data-selected' ) );
                    var selected = [];

                    jQuery( element.val().split( "," ) ).each( function( i, val ) {
                        selected.push( { id: val, text: data[ val ] } );
                    });
                    return callback( selected );
                };
                select2_args.formatSelection = function( data ) {
                    return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                };
            } else {
                select2_args.multiple = false;
                select2_args.initSelection = function( element, callback ) {
                    var data = {id: element.val(), text: element.attr( 'data-selected' )};
                    return callback( data );
                };
            }


            jQuery(this).select2(select2_args).addClass( 'enhanced' );
        } );

    }

    init_fue_product_search();
    init_fue_customer_search();
    init_fue_select();
    init_fue_coupon_search();
}(jQuery));