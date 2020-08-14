jQuery( function ( $ ) {
    var enabled_columns_tab_wrapper = $( '#yith-wcbep-enabled-columns-tab-wrapper' ),
        is_enabled_columns_tab      = enabled_columns_tab_wrapper.length > 0,
        enable_all_btn              = $( '#yith-wcbep-enabled-columns-tab-actions-enable-all' ),
        disable_all_btn             = $( '#yith-wcbep-enabled-columns-tab-actions-disable-all' ),
        ajax_request,
        save_btn                    = $( '#yith-wcbep-enabled-columns-tab-actions-save' ),
        saving                      = $( '#yith-wcbep-enabled-columns-tab-actions-saving' ),
        add_actions                 = function () {
            if ( is_enabled_columns_tab ) {
                enabled_columns_tab_wrapper.on( 'click', '.yith-wcbep-enabled-column-icon', function ( e ) {
                    var target = $( e.target );
                    if ( $( this ).is( '.dashicons-yes' ) ) {
                        $( this ).removeClass( 'dashicons-yes' );
                        $( this ).addClass( 'dashicons-no' );
                    } else {
                        $( this ).removeClass( 'dashicons-no' );
                        $( this ).addClass( 'dashicons-yes' );
                    }
                } );

                enable_all_btn.on( 'click', function () {
                    set_all( 'enabled' );
                } );

                disable_all_btn.on( 'click', function () {
                    set_all( 'disabled' );
                } );

                save_btn.on( 'click', function () {
                    save_enabled_columns();
                } );

            }
        },
        save_enabled_columns        = function () {
            if ( is_enabled_columns_tab ) {
                var enabled_columns = [];

                //saving.fadeIn();
                save_btn.addClass('loading');

                enabled_columns_tab_wrapper.find( '.yith-wcbep-enabled-column-icon' ).each( function () {
                    var id = $( this ).data( 'cols-id' );
                    if ( $( this ).is( '.dashicons-yes' ) ) {
                        enabled_columns.push( id );
                    }
                } );

                var post_data = {
                    enabled_columns: enabled_columns,
                    action: 'yith_wcbep_save_enabled_columns'
                };

                if ( ajax_request ) {
                    ajax_request.abort();
                }

                ajax_request = $.ajax( {
                    type: "POST",
                    data: post_data,
                    url: ajaxurl,
                    success: function ( response ) {
                        //saving.fadeOut();
                        save_btn.removeClass('loading');
                    }
                } );
            }
        },
        set_all                     = function ( status ) {
            if ( is_enabled_columns_tab ) {
                var to_remove = status == 'enabled' ? 'dashicons-no' : 'dashicons-yes',
                    to_add    = status == 'enabled' ? 'dashicons-yes' : 'dashicons-no';

                enabled_columns_tab_wrapper.find( '.yith-wcbep-enabled-column-icon' ).each( function () {
                    $( this ).removeClass( to_remove );
                    $( this ).addClass( to_add );
                } );
            }
        };

    add_actions();
} );