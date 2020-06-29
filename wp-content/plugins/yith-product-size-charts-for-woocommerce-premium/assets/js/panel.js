/* global yith_wcpsc_params */
jQuery( function ( $ ) {
    var block_params = {
        message        : null,
        css            : {
            border    : 'none',
            background: 'transparent'
        },
        overlayCSS     : {
            background: '#fff',
            opacity   : 0.7
        },
        ignoreIfBlocked: true
    };

    if ( yith_wcpsc_params.wc_3_0 ) {
        $( '.yith-wcpsc-select2' ).select2();
    } else {
        $( '.yith-wcpsc-select2' ).chosen();
    }

    var table_style_sel  = $( '#yith-wcpsc-table-style' ),
        table_base_color = $( '#yith-wcpsc-table-base-color' ),
        table_colors     = {
            default : '#f9f9f9',
            informal: '#ffd200',
            casual  : '#b37c81',
            elegant : '#000000'
        },
        popup_style_sel  = $( '#yith-wcpsc-popup-style' ),
        popup_base_color = $( '#yith-wcpsc-popup-base-color' ),
        popup_colors     = {
            default : '#ffffff',
            informal: '#999999',
            casual  : '#b37c81',
            elegant : '#6d6d6d'
        };


    table_style_sel.on( 'change', function () {
        var selected = table_style_sel.children( ':selected' ).val(),
            color    = table_colors[ selected ];

        table_base_color.val( color );
        table_base_color.trigger( 'keyup' );
    } );

    popup_style_sel.on( 'change', function () {
        var selected = popup_style_sel.children( ':selected' ).val(),
            color    = popup_colors[ selected ];

        popup_base_color.val( color );
        popup_base_color.trigger( 'keyup' );
    } );

    /**
     *  Free to premium update
     */
    var freeToPremium = {
        dom         : {
            update     : $( '#yith-wcpsc-free-to-premium-update' ),
            percentage : $( '#yith-wcpsc-free-to-premium-update__progress__percentage' ),
            progress   : $( '#yith-wcpsc-free-to-premium-update__progress' ),
            progressBar: $( '#yith-wcpsc-free-to-premium-update__progress__bar' ),
            messages   : $( '#yith-wcpsc-free-to-premium-update__messages' ),
            skip       : $( '#yith-wcpsc-free-to-premium-skip' )
        },
        updated     : 0,
        toUpdate    : 0,
        init        : function () {
            if ( this.dom.update.length && this.dom.skip.length && this.dom.percentage.length ) {
                this.toUpdate = this.dom.update.data( 'to-update' );

                this.dom.update.on( 'click', this.update );
                this.dom.skip.on( 'click', this.skip );
            }
        },
        update      : function ( e ) {
            freeToPremium.dom.skip.hide();
            freeToPremium.dom.update.block(block_params);
            freeToPremium.dom.progress.show();
            freeToPremium.singleUpdate();
        },
        singleUpdate: function () {
            $.ajax( {
                        type   : "POST",
                        data   : { action: 'yith_wcpsc_free_to_premium_update' },
                        url    : ajaxurl,
                        success: function ( response ) {
                            if ( typeof response.action !== 'undefined' ) {
                                if ( 'next' === response.action ) {
                                    freeToPremium.singleUpdate();
                                    if ( typeof response.toUpdate !== 'undefined' ) {
                                        var percentage = parseInt( ( freeToPremium.toUpdate - response.toUpdate ) / freeToPremium.toUpdate * 100 );
                                        freeToPremium.dom.percentage.html( percentage + ' %' );
                                        freeToPremium.dom.progressBar.css( { width: percentage + '%' } );
                                    }
                                } else if ( 'complete' === response.action ) {
                                    freeToPremium.dom.percentage.html( '100 %' );
                                    freeToPremium.dom.progressBar.css( { width: '100%' } );

                                    freeToPremium.dom.update.remove();
                                    freeToPremium.dom.skip.remove();

                                    if ( typeof response.message !== 'undefined') {
                                        freeToPremium.dom.messages.html( response.message );
                                        freeToPremium.dom.progress.fadeOut();
                                    }
                                }
                            }
                        }
                    } );
        },
        skip        : function ( e ) {
            var row = freeToPremium.dom.skip.closest( 'tr' );
            row.block( block_params );
            $.ajax( {
                        type   : "POST",
                        data   : { action: 'yith_wcpsc_free_to_premium_skip' },
                        url    : ajaxurl,
                        success: function ( response ) {
                            row.unblock();
                            row.fadeOut();
                        }
                    } );
        }
    };

    freeToPremium.init();

} );