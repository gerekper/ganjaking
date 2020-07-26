/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

jQuery( function ( $ ) {
    //dependencies handler
    $( '[data-dep-target]' ).each( function () {
        var t = $( this );

        var field = '#' + t.data( 'dep-target' ),
            dep   = '#' + t.data( 'dep-id' ),
            value = t.data( 'dep-value' ),
            type  = t.data( 'dep-type' ),
            event = 'change',
            wrapper = $( dep + '-wrapper' ),
            field_type = wrapper.data( 'type' );

        if( field_type === 'select-images' ){
          event = 'yith_select_images_value_changed';
        }

        dependencies_handler( field, dep, value.toString(), type );

        $( dep ).on( event, function () {
            dependencies_handler( field, dep, value.toString(), type );
        } ).trigger( event );

    } );

    //Handle dependencies.
    function dependencies_handler( id, deps, values, type ) {
        var result = true;
        //Single dependency
        if ( typeof ( deps ) == 'string' ) {
            if ( deps.substr( 0, 6 ) == ':radio' ) {
                deps = deps + ':checked';
            }

            var val = $( deps ).val();

            if ( $( deps ).attr( 'type' ) == 'checkbox' ) {
                var thisCheck = $( deps );
                if ( thisCheck.is( ':checked' ) ) {
                    val = 'yes';
                } else {
                    val = 'no';
                }
            }

            if( $( deps + '-wrapper' ).data( 'type' ) === 'select-images' ){
              val = $( deps + '-wrapper' ).find( 'select' ).first().val();
            }

            values = values.split( ',' );

            for ( var i = 0; i < values.length; i++ ) {
                if ( val != values[ i ] ) {
                    result = false;
                } else {
                    result = true;
                    break;
                }
            }
        }

        var $current_field     = $( id ),
            $current_container = $( id + '-container' ).closest( 'tr' ); // container for YIT Plugin Panel

        if ( $current_container.length < 1 ) {
            // container for YIT Plugin Panel WooCommerce
            $current_container = $current_field.closest( '.yith-plugin-fw-panel-wc-row' );
        }

        var types = type.split( '-' ), j;
        for ( j in types ) {
            var current_type = types[ j ];

            if ( !result ) {
                switch ( current_type ) {
                    case 'disable':
                        $current_container.addClass( 'yith-disabled' );
                        $current_field.attr( 'disabled', true );
                        break;
                    case 'hideme':
                        $current_field.hide();
                        break;
                    case 'fadeInOut':
                    case 'fadeOut':
                        $current_container.hide( 500 );
                        break;
                    case 'fadeIn':
                        $current_container.hide();
                        break;
                    default:
                        if ( !$current_container.hasClass( 'fade-in' ) ) {
                            $current_container.hide();
                            $current_container.css( { 'opacity': '0' } );
                        } else {
                            $current_container.fadeTo( "slow", 0, function () {
                                $( this ).hide().removeClass( 'fade-in' );
                            } );
                        }

                }

            } else {
                switch ( current_type ) {
                    case 'disable':
                        $current_container.removeClass( 'yith-disabled' );
                        $current_field.attr( 'disabled', false );
                        break;
                    case 'hideme':
                        $current_field.show();
                        break;
                    case 'fadeInOut':
                    case 'fadeIn':
                        $current_container.show( 500 );
                        break;
                    case 'fadeOut':
                        $current_container.show();
                        break;
                    default:
                        $current_container.show();
                        $current_container.fadeTo( "slow", 1 ).addClass( 'fade-in' );
                }
            }
        }
    }

    //connected list
    $( '.rm_connectedlist' ).each( function () {
        var ul       = $( this ).find( 'ul' );
        var input    = $( this ).find( ':hidden' );
        var sortable = ul.sortable( {
                                        connectWith: ul,
                                        update     : function ( event, ui ) {
                                            var value = {};

                                            ul.each( function () {
                                                var options = {};

                                                $( this ).children().each( function () {
                                                    options[ $( this ).data( 'option' ) ] = $( this ).text();
                                                } );

                                                value[ $( this ).data( 'list' ) ] = options;
                                            } );

                                            input.val( ( JSON.stringify( value ) ).replace( /[\\"']/g, '\\$&' ).replace( /\u0000/g, '\\0' ) );
                                        }
                                    } ).disableSelection();
    } );

    //google analytics generation
    $( document ).ready( function () {
        $( '.google-analytic-generate' ).click( function () {
            var editor   = $( '#' + $( this ).data( 'textarea' ) ).data( 'codemirrorInstance' );
            var gatc     = $( '#' + $( this ).data( 'input' ) ).val();
            var basename = $( this ).data( 'basename' );

            var text = "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){\n";
            text += "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement( o ),\n";
            text += "m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)\n";
            text += "})(window,document,'script','//www.google-analytics.com/analytics.js','ga');\n\n";
            text += "ga('create', '" + gatc + "', '" + basename + "');\n";
            text += "ga('send', 'pageview');\n";
            editor.replaceRange(
                text,
                editor.getCursor( 'start' ),
                editor.getCursor( 'end' )
            )
        } )
    } );


    // prevents the WC message for changes when leaving the panel page
    $( '.yith-plugin-fw-panel .woo-nav-tab-wrapper' ).removeClass( 'woo-nav-tab-wrapper' ).addClass( 'yith-nav-tab-wrapper' );

    var wrap    = $( '.wrap.yith-plugin-ui' ).first(),
        notices = $( 'div.updated, div.error, div.notice' );

    // prevent moving notices into the wrapper
    notices.addClass( 'inline' );
    if ( wrap.length ) {
        wrap.prepend( notices );
    }


    // TAB MENU AND SUB TABS
    var active_subnav = $(document).find( '.yith-nav-sub-tab.nav-tab-active' );

    if( active_subnav.length ){
        // WP page
        var  mainWrapper = $(document).find( '.yith-plugin-fw-wp-page-wrapper' );
        if( ! mainWrapper.length ){
            mainWrapper = $(document).find( '#wpbody-content > .yith-plugin-ui' );
        }

        if( mainWrapper ){
            // serach first for deafult wrap
            var wrap = mainWrapper.find( '.yit-admin-panel-content-wrap' );
            if( wrap.length ) {
                wrap.addClass( 'has-subnav' );
            }
            else {
                // try to wrap a generic wrap div in main wrapper
                mainWrapper.find('.wrap').wrap('<div class="wrap subnav-wrap"></div>');
            }
        }
    }
} );
