var sfn_ajax_search = null;
jQuery(document).ready(function($) {

    // Select2 Enhancement if it exists
    if ( $().select2 ) {

        sfn_ajax_search = function(){
            $( '.sfn-product-search' ).filter( ':not(.enhanced)' ).each( function() {
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
                        data: function( term, page ) {
                            return {
                                term:     sfn_product_search.bwc ? term : term.term,
                                action:   $( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
                                security: sfn_product_search.security
                            };
                        },
                        processResults: function( data, page ) {
                            var terms = [];
                            if ( data ) {
                                $.each( data, function( id, text ) {
                                    terms.push( { id: id, text: text } );
                                });
                            }
                            return { results: terms };
                        },
                        cache: true
                    }
                };

                if ( sfn_product_search.bwc ) {
                    select2_args.multiple = $( this ).data( 'multiple' ) ? true : false;
                    select2_args.ajax.results = select2_args.ajax.processResults;

                    if ( $( this ).data( 'multiple' ) === true ) {
                        select2_args.initSelection = function( element, callback ) {
                            var data     = $.parseJSON( element.attr( 'data-selected' ) );
                            var selected = [];

                            $( element.val().split( "," ) ).each( function( i, val ) {
                                selected.push( { id: val, text: data[ val ] } );
                            });

                            return callback( selected );
                        };
                        select2_args.formatSelection = function( data ) {
                            return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                        };
                    } else {
                        select2_args.initSelection = function( element, callback ) {
                            var data = {id: element.val(), text: element.attr( 'data-selected' )};
                            return callback( data );
                        };
                    }
                }

                $( this ).select2( select2_args ).addClass( 'enhanced' );
            });
        }

    } else {

        sfn_ajax_search = function() {
            $( '.sfn-product-search').filter( ':not(.enhanced)' ).each( function() {
                $(this).ajaxChosen({
                    method:     "GET",
                    url:        ajaxurl,
                    dataType:   "json",
                    afterTypeDelay: 100,
                    data:       {
                        action:         "woocommerce_json_search_products_and_variations",
                        security:       sfn_product_search.security
                    }
                }, function (data) {
                    var terms = {};

                    jQuery.each(data, function (i, val) {
                        terms[i] = val;
                    });

                    return terms;
                }).addClass('enhanced');
            } );

        };

    }

    sfn_ajax_search();

} );
