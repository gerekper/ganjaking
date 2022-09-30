jQuery( function ( $ ) {

    var block_params = {
        message        : null,
        overlayCSS     : {
            background: '#000',
            opacity   : 0.6
        },
        ignoreIfBlocked: true
    };
    
    $.fn.yith_wcmbs_downloads_by_product = function () {
        var $downloads_by_product = $( this ),
            downloads_table       = $downloads_by_product.find( '#yith-wcmbs-reports-table-downloads' ),
            order                 = downloads_table.data( 'order' );

        $downloads_by_product.on( 'yith_wcmbs_update_table', '.yith-wcmbs-reports-download-reports-table', function ( event ) {
                var $target          = $( event.target ),
                    $table_container = $target.closest( '.yith-wcmbs-reports-download-reports-table' ),
                    $table           = $table_container.find( '.yith-wcmbs-reports-table-downloads' ).first(),
                    order            = $table.data( 'order' ),
                    user_id          = $table.data( 'user-id' ),
                    post_data        = {
                        user_id: user_id,
                        order  : order,
                        action : 'yith_wcmbs_get_download_table_reports'
                    };

                $table_container.block( block_params );

                $.ajax( {
                            type    : "POST",
                            data    : post_data,
                            url     : ajaxurl,
                            success : function ( response ) {
                                $table_container.html( response );
                            },
                            complete: function () {
                                $table_container.unblock();
                            }
                        } );

            } )

            .on( 'click', '.yith-wcmbs-reports-filter-button', function ( event ) {
                var $target          = $( event.target ),
                    $container       = $target.closest( '#yith-wcmbs-reports-downloads-content-downloads-by-product' ),
                    $filter_user_id  = $container.find( '.yith-wcmbs-reports-filter-user-id' ).first(),
                    $table_container = $container.find( '.yith-wcmbs-reports-download-reports-table' ).first(),
                    $table           = $table_container.find( '.yith-wcmbs-reports-table-downloads' ).first();

                $table.data( 'user-id', $filter_user_id.val() );
                $table_container.trigger( 'yith_wcmbs_update_table' );
            } )

            .on( 'click', '.yith-wcmbs-reports-filter-reset', function ( event ) {
                var $target         = $( event.target ),
                    $container      = $target.closest( '#yith-wcmbs-reports-downloads-content-downloads-by-product' ),
                    $filter_user_id = $container.find( '.yith-wcmbs-reports-filter-user-id' ).first(),
                    $filter_button  = $container.find( '.yith-wcmbs-reports-filter-button' ).first();

                $filter_user_id.trigger( 'yith_wcmbs_select2_reset' );
                $filter_button.trigger( 'click' );
            } )

            .on( 'click', '.yith-wcmbs-reports-table-downloads-order-by-downloads', function ( event ) {
                var $target          = $( event.target ),
                    $container       = $target.closest( '#yith-wcmbs-reports-downloads-content-downloads-by-product' ),
                    $table_container = $target.closest( '.yith-wcmbs-reports-download-reports-table' ),
                    $table           = $table_container.find( '.yith-wcmbs-reports-table-downloads' ).first();


                $table.data( 'order', $target.data( 'order' ) );
                $table_container.trigger( 'yith_wcmbs_update_table' );
            } );

    };

    $( '#yith-wcmbs-download-reports-downloads-by-product' ).yith_wcmbs_downloads_by_product();

    var $download_reports_menu    = $( '.yith-wcmbs-reports-downloads-menu' ),
        $download_content_wrapper = $( '.yith-wcmbs-reports-downloads-content-wrapper' );

    $( $download_reports_menu ).on( 'click', 'a', function ( event ) {
        event.preventDefault();
        var $target = $( event.target ).closest( 'a' ),
            type    = $target.data( 'type' );

        $download_reports_menu.find( 'a' ).removeClass( 'active' );
        $target.addClass( 'active' );

        $( '.yith-wcmbs-reports-downloads-content' ).hide();
        $( '#yith-wcmbs-reports-downloads-content-' + type ).show();
    } );

    $( $download_reports_menu ).on( 'click', 'span.close', function ( event ) {
        event.preventDefault();
        var $target = $( event.target ).closest( 'span.close' ),
            $li     = $target.closest( 'li' ),
            $a      = $li.find( 'a' ).first(),
            type    = $a.data( 'type' );

        if ( $a.is( '.active' ) ) {
            $download_reports_menu.find( 'a[data-type="downloads-by-user"]' ).trigger( 'click' );
        }
        $li.remove();
        $( '#yith-wcmbs-reports-downloads-content-' + type ).remove();
    } );

    /**
     * AJAX TABLE
     */
    var get_query_var = function ( query, variable ) {
        var vars = query.split( "&" );
        for ( var i = 0; i < vars.length; i++ ) {
            var pair = vars[ i ].split( "=" );
            if ( pair[ 0 ] == variable )
                return pair[ 1 ];
        }
        return false;
    };

    $.fn.yith_wcmbs_ajax_table = function () {
        $( this ).each( function () {
            var $table             = $( this ),
                action             = $table.data( 'action' ),
                delay              = 500,
                timer,
                $nav_top           = $table.find( '.tablenav.top .tablenav-pages' ),
                $nav_bottom        = $table.find( '.tablenav.bottom .tablenav-pages' ),
                table_block_params = {
                    message        : null,
                    overlayCSS     : {
                        background: '#000',
                        opacity   : 0.6
                    },
                    ignoreIfBlocked: true
                },
                default_data       = {
                    paged   : '1',
                    order   : 'DESC',
                    orderby : 'ID',
                    per_page: '10'
                };

            default_data = $.extend(
                default_data,
                $table.data()
            );

            $table.on( 'click', '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a', function ( e ) {
                e.preventDefault();

                var query = this.search.substring( 1 ),
                    data  = $.extend(
                        default_data,
                        {
                            paged   : get_query_var( query, 'paged' ) || default_data.paged,
                            order   : get_query_var( query, 'order' ) || default_data.order,
                            orderby : get_query_var( query, 'orderby' ) || default_data.orderby,
                            per_page: get_query_var( query, 'per_page' ) || default_data.per_page
                        }
                    );
                $table.trigger( 'update', data );
            } );

            $table.on( 'change', 'input[name=paged]', function ( e ) {
                if ( 13 == e.which )
                    e.preventDefault();

                var query       = $table.find( '.tablenav-pages a' )[ 0 ].search.substring( 1 ),
                    input_paged = $table.find( 'input[name=paged]' );

                var data = $.extend(
                    default_data,
                    {
                        paged   : parseInt( input_paged.val() ) || default_data.paged,
                        order   : get_query_var( query, 'order' ) || default_data.order,
                        orderby : get_query_var( query, 'orderby' ) || default_data.orderby,
                        per_page: get_query_var( query, 'per_page' ) || default_data.per_page
                    }
                );

                window.clearTimeout( timer );
                timer = window.setTimeout( function () {
                    $table.trigger( 'update', data );
                }, delay );
            } );

            $table.on( 'click', '.yith-wcmbs-ajax-table-apply-button', function () {
                var per_page = $table.find( '.yith-wcmbs-ajax-table-per-page' ).val();

                var data = $.extend(
                    default_data,
                    {
                        per_page: per_page || get_query_var( query, 'per_page' ) || default_data.per_page,
                        paged   : '1'

                    }
                );
                $table.trigger( 'update', data );
            } );

            $table.on( 'update', function ( event, data ) {
                $table.block( table_block_params );

                $.ajax( {
                            url     : ajaxurl,
                            data    : $.extend(
                                {
                                    _yith_wcmbs_ajax_table_nonce: $table.find( '#_yith_wcmbs_ajax_table_nonce' ).val(),
                                    action                      : action
                                },
                                data
                            ),
                            success : function ( resp ) {

                                var response = $.parseJSON( resp );
                                // Add the requested rows
                                if ( response.rows.length )
                                    $table.find( '.wp-list-table tbody' ).html( response.rows );
                                // Update column headers for sorting
                                if ( response.column_headers.length )
                                    $table.find( 'thead tr, tfoot tr' ).html( response.column_headers );
                                // Update pagination for navigation
                                if ( response.pagination.bottom.length )
                                    $nav_top.html( $( response.pagination.top ).html() );
                                if ( response.pagination.top.length ) {
                                    $nav_bottom.html( $( response.pagination.bottom ).html() );
                                }

                            },
                            complete: function () {
                                $table.unblock();
                                $table.find( '.pagination-links' ).show();

                                $table.find( '.tips' ).tipTip( {
                                                                   'attribute': 'data-tip',
                                                                   'fadeIn'   : 50,
                                                                   'fadeOut'  : 50,
                                                                   'delay'    : 0
                                                               } );
                            }
                        } );
            } );
        } );
    };

    $( '.yith-wcmbs-ajax-table' ).yith_wcmbs_ajax_table();

    var open_user_details = function ( user_id, user_name, open_behind ) {
        var existing_link = $download_reports_menu.find( 'a[data-type="downloads-by-user-' + user_id + '"]' );

        if ( existing_link.length < 1 ) {
            var $new_li = $( '<li><a href="#" data-type="downloads-by-user-' + user_id + '">' + user_name + '</a> <span class="dashicons dashicons-no-alt close"></span> </li>' );
            $download_reports_menu.append( $new_li );

            var post_data = {
                action : 'yith_wcmbs_get_download_reports_details_by_user_table',
                user_id: user_id
            };

            $new_li.block( block_params );

            $.ajax( {
                        type    : "POST",
                        data    : post_data,
                        url     : ajaxurl,
                        success : function ( response ) {
                            $download_content_wrapper.append( response );

                            var $user_details = $download_content_wrapper.find( '#yith-wcmbs-reports-downloads-content-downloads-by-user-' + user_id );

                            $user_details.find( '.yith-wcmbs-ajax-table' ).yith_wcmbs_ajax_table();
                            $user_details.find( '.yith-wcmbs-tabs' ).tabs();
                            $user_details.find( '.tips-top' ).tipTip( {
                                                                          'attribute'      : 'data-tip',
                                                                          'fadeIn'         : 50,
                                                                          'fadeOut'        : 50,
                                                                          'delay'          : 0,
                                                                          'defaultPosition': 'top'
                                                                      } );
                        },
                        complete: function () {
                            $new_li.unblock();
                            if ( !open_behind )
                                $download_reports_menu.find( 'a[data-type="downloads-by-user-' + user_id + '"]' ).trigger( 'click' );
                        }
                    } );
        } else {
            if ( !open_behind )
                existing_link.trigger( 'click' );
        }
    };

    $( document ).on( 'click', '.yith_wcmbs_download_reports_by_user .report_actions a.details', function ( event ) {
        event.preventDefault();
        var $target     = $( event.target ),
            $button     = $target.closest( 'a.details' ),
            user_id     = $button.data( 'user_id' ),
            user_name   = $button.data( 'user_name' ),
            open_behind = event.ctrlKey;

        open_user_details( user_id, user_name, open_behind );
    } );

    var search_user_select = $( '#yith-wcmbs-download-reports-downloads-details-by-user-search-select' ),
        search_user_button = $( '#yith-wcmbs-download-reports-downloads-details-by-user-search-show-button' );


    search_user_button.on( 'click', function ( event ) {
        var user_id     = search_user_select.val(),
            open_behind = event.ctrlKey;

        if ( !user_id )
            return;

        var select_data = search_user_select.select2( 'data' );
        select_data     = select_data ? select_data[ 0 ] : false;

        var user_name = select_data ? select_data.text : '#' + user_id;
        user_name     = user_name.substring( 0, user_name.indexOf( ' (' ) );

        console.log( user_id );
        console.log( user_name );
        console.log( open_behind );
        open_user_details( user_id, user_name, open_behind );

        search_user_select.val( '' ).trigger( 'change' );
    } );
} );
