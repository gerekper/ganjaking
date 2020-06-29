/*global wc_enhanced_select_params, user_email_search */
jQuery( function( $ ) {

    function getEnhancedSelectFormatString() {
        var formatString = {
            noResults: function() {
                return wc_enhanced_select_params.i18n_no_matches;
            },
            errorLoading: function() {
                return wc_enhanced_select_params.i18n_ajax_error;
            },
            inputTooShort: function( args ) {
                var remainingChars = args.minimum - args.input.length;

                if ( 1 === remainingChars ) {
                    return wc_enhanced_select_params.i18n_input_too_short_1;
                }

                return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
            },
            inputTooLong: function( args ) {
                var overChars = args.input.length - args.maximum;

                if ( 1 === overChars ) {
                    return wc_enhanced_select_params.i18n_input_too_long_1;
                }

                return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
            },
            maximumSelected: function( args ) {
                if ( args.maximum === 1 ) {
                    return wc_enhanced_select_params.i18n_selection_too_long_1;
                }

                return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
            },
            loadingMore: function() {
                return wc_enhanced_select_params.i18n_load_more;
            },
            searching: function() {
                return wc_enhanced_select_params.i18n_searching;
            }
        };
    }

    $( 'body' )

        .on( 'wc-enhanced-select-init', function() {

            $( '.wc-user-search' ).filter( ':not(.enhanced)' ).each( function() {
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
                        data: function( term, page ) {
                            return {
                                term:     user_email_search.isLessThanWC30 ? term : term.term,
                                action:   $( this ).data( 'action' ) || 'warranty_user_search',
                                security: wc_enhanced_select_params.search_customers_nonce
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

                if ( user_email_search.isLessThanWC30 ) {
                    if ( $( this ).data( 'multiple' ) === true ) {
                        select2_args.multiple = true;
                        select2_args.initSelection = function( element, callback ) {
                            var data     = $.parseJSON( element.attr( 'data-selected' ) );
                            var selected = [];

                            $( element.val().split( ',' ) ).each( function( i, val ) {
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
                }

                select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

                $( this ).select2( select2_args ).addClass( 'enhanced' );
            });

            $(".email-search-select").filter(":not(.enhanced)").each( function() {
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
                                term:     user_email_search.isLessThanWC30 ? term : term.term,
                                action:   jQuery( this ).data( 'action' ) || 'warranty_search_for_email'
                            };
                        },
                        processResults: function( data, page ) {
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

                if ( user_email_search.isLessThanWC30 ) {
                    select2_args.ajax.results = select2_args.ajax.processResults;

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
                }

                jQuery(this).select2(select2_args).addClass( 'enhanced' );
            } );
        })

        .trigger( 'wc-enhanced-select-init' );

});
