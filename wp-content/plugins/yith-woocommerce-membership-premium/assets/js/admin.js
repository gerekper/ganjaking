jQuery( function ( $ ) {
    $( '.tips' ).tipTip( {
        'attribute': 'data-tip',
        'fadeIn': 50,
        'fadeOut': 50,
        'delay': 0
    } );

    // Ajax product search box
    $( ':input.yith-wcmbs-search-products' ).filter( ':not(.enhanced)' ).each( function() {
        var select2_args = {
            allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
            placeholder: $( this ).data( 'placeholder' ),
            minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
            escapeMarkup: function( m ) {
                return m;
            },
            ajax: {
                url:         wc_enhanced_select_params.ajax_url,
                dataType:    'json',
                quietMillis: 250,
                data: function( term ) {
                    return {
                        term:     term,
                        action:   'yith_wcmbs_json_search_products_and_variations',
                        security: wc_enhanced_select_params.search_products_nonce,
                        exclude:  $( this ).data( 'exclude' ),
                        include:  $( this ).data( 'include' ),
                        limit:    $( this ).data( 'limit' )
                    };
                },
                results: function( data ) {
                    var terms = [];
                    if ( data ) {
                        $.each( data, function( id, text ) {
                            terms.push( { id: id, text: text } );
                        });
                    }
                    return {
                        results: terms
                    };
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

        select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

        $( this ).select2( select2_args ).addClass( 'enhanced' );
    });
} );