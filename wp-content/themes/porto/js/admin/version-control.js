/**
 * Version Control
 * 
 * @since 6.3.0
 */

( function ( $ ) {
    $( '.porto-refresh-versions' ).on( 'click', function ( e ) {
        e.preventDefault();
        $.ajax( {
            url: ajaxurl,
            type: 'POST',
            data: {
                'action': 'porto_refresh_versions',
                '_wpnonce': js_porto_admin_vars.nonce
            },
            success: function ( response ) {
                window.location.reload( true );
            },
            failure: function () {
                alert( wp.i18n.__( 'Failed. Please refresh and try again.', 'porto' ) );
            }
        } );
    } );
    var afterAjax = function () {
        $( '.porto-rollback-button' ).removeClass( 'prevent-click' );
        $( '.porto-rollback-button i' ).removeClass( 'fa-spin' );
    }
    $( document.body ).on( 'click', '.porto-rollback-button', function ( e ) {
        e.preventDefault();
        var $this = $( this );
        if ( $this.hasClass( 'prevent-click' ) ) {
            return;
        }
        var rollback_version = $( '.porto-rollback-version' ).val();
        if ( !rollback_version ) return;
        if ( window.confirm( wp.i18n.__( 'Are you sure you want to rollback to a previous version?', 'porto' ) ) ) {
            $this.addClass( 'prevent-click' );
            $this.find( 'i' ).addClass( 'fa-spin' );
            $.ajax( {
                url: ajaxurl,
                type: 'POST',
                data: {
                    'action': 'porto_rollback_version',
                    'version': rollback_version,
                    '_wpnonce': js_porto_admin_vars.nonce
                },
                success: function ( response ) {
                    if ( !response || !response.success ) {
                        afterAjax();
                        alert( wp.i18n.__( 'Failed. Please refresh and try again.', 'porto' ) );
                    } else { // success
                        $.ajax( {
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                'action': 'porto_apply_version',
                                'version': rollback_version,
                                '_wpnonce': js_porto_admin_vars.nonce
                            },
                            success: function ( response ) {
                                afterAjax();
                                if ( response ) {
                                    alert( wp.i18n.__( 'Rollback was finished!', 'porto' ) );
                                }
                                // window.location.reload( true );
                            },
                            failure: function () {
                                afterAjax();
                                alert( wp.i18n.__( 'Failed. Please refresh and try again.', 'porto' ) );
                            }
                        } );
                    }
                },
                failure: function () {
                    afterAjax();
                    alert( wp.i18n.__( 'Failed. Please refresh and try again.', 'porto' ) );
                }
            } );
        }
    } );
} )( window.jQuery );