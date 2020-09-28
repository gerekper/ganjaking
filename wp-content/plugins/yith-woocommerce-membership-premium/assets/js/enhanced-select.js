/*
 global yith_wcmbs_enhanced_select_params
 */
jQuery( document ).ready( function ( $ ) {
    "use strict";

    $( document.body )
        .on( 'yith-wcmbs-enhanced-select-init', function () {
            // Post Search
            $( ':input.yith-wcmbs-post-search' ).filter( ':not(.enhanced)' ).each( function () {
                if ( yith_wcmbs_enhanced_select_params.wc2_7 ) {
                    var select2_args = {
                        allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
                        placeholder       : $( this ).data( 'placeholder' ),
                        minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                        escapeMarkup      : function ( m ) {
                            return m;
                        },
                        ajax              : {
                            url           : yith_wcmbs_enhanced_select_params.ajax_url,
                            dataType      : 'json',
                            quietMillis   : 250,
                            data          : function ( params ) {
                                return {
                                    term    : params.term,
                                    action  : $( this ).data( 'action' ) || 'yith_wcmbs_json_search_posts',
                                    security: yith_wcmbs_enhanced_select_params.search_posts_nonce,
                                    exclude : $( this ).data( 'exclude' ),
                                    include : $( this ).data( 'include' ),
                                    limit   : $( this ).data( 'limit' ),
                                    post_type: $( this ).data( 'post_type' ) || 'post'

                                };
                            },
                            processResults: function ( data ) {
                                var terms = [];
                                if ( data ) {
                                    $.each( data, function ( id, text ) {
                                        terms.push( { id: id, text: text } );
                                    } );
                                }
                                return {
                                    results: terms
                                };
                            },
                            cache         : true
                        }
                    };

                    $( this ).select2( select2_args ).addClass( 'enhanced' );

                    if ( $( this ).data( 'sortable' ) ) {
                        var $select = $( this );
                        var $list   = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

                        $list.sortable( {
                                            placeholder         : 'ui-state-highlight select2-selection__choice',
                                            forcePlaceholderSize: true,
                                            items               : 'li:not(.select2-search__field)',
                                            tolerance           : 'pointer',
                                            stop                : function () {
                                                $( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function () {
                                                    var id     = $( this ).data( 'data' ).id;
                                                    var option = $select.find( 'option[value="' + id + '"]' )[ 0 ];
                                                    $select.prepend( option );
                                                } );
                                            }
                                        } );
                    }

                } else {
                    var select2_args = {
                        allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
                        placeholder       : $( this ).data( 'placeholder' ),
                        minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                        escapeMarkup      : function ( m ) {
                            return m;
                        },
                        ajax              : {
                            url           : yith_wcmbs_enhanced_select_params.ajax_url,
                            dataType      : 'json',
                            quietMillis   : 250,
                            data          : function ( term ) {
                                return {
                                    term     : term,
                                    action   : $( this ).data( 'action' ) || 'yith_wcmbs_json_search_posts',
                                    security : yith_wcmbs_enhanced_select_params.search_posts_nonce,
                                    exclude  : $( this ).data( 'exclude' ),
                                    include  : $( this ).data( 'include' ),
                                    limit    : $( this ).data( 'limit' ),
                                    post_type: $( this ).data( 'post_type' ) || 'post'
                                };
                            },
                            processResults: function ( data ) {
                                var terms = [];
                                if ( data ) {
                                    $.each( data, function ( id, text ) {
                                        terms.push( { id: id, text: text } );
                                    } );
                                }
                                return {
                                    results: terms
                                };
                            },
                            results       : function ( data ) {
                                var terms = [];
                                if ( data ) {
                                    $.each( data, function ( id, text ) {
                                        terms.push( { id: id, text: text } );
                                    } );
                                }
                                return {
                                    results: terms
                                };
                            },
                            cache         : true
                        }
                    };

                    if ( $( this ).data( 'multiple' ) === true || $( this ).attr( 'multiple' ) ) {
                        select2_args.multiple        = true;
                        select2_args.initSelection   = function ( element, callback ) {
                            var data     = $.parseJSON( element.attr( 'data-selected' ) );
                            var selected = [];

                            if ( element.val() ) {
                                $( element.val().split( ',' ) ).each( function ( i, val ) {
                                    selected.push( {
                                                       id  : val,
                                                       text: data[ val ]
                                                   } );
                                } );
                            }
                            return callback( selected );
                        };
                        select2_args.formatSelection = function ( data ) {
                            return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                        };
                    } else {
                        select2_args.multiple      = false;
                        select2_args.initSelection = function ( element, callback ) {
                            var data = {
                                id  : element.val(),
                                text: element.attr( 'data-selected' )
                            };
                            return callback( data );
                        };
                    }

                    $( this ).select2( select2_args ).addClass( 'enhanced' );
                }
            } );
        } ).trigger( 'yith-wcmbs-enhanced-select-init' );
} );