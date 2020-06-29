( function ( $ ) {
    var multiStock = {
        dom : {
            inventoryMultiStockManagement: $( '#inventory_product_data .yith-pos-stock-management' ),
            manageStock                  : $( '#_manage_stock' )
        },
        init: function () {
            $( document ).on( 'click', '.clone-stock-group', multiStock.addOption );
            $( document ).on( 'click', '.yith-pos-group .yith-icon-trash', multiStock.deleteOption );

            multiStock.dom.manageStock.on( 'change', multiStock.handleGeneralMultiStockVisibility );
            multiStock.handleGeneralMultiStockVisibility();

            $( document ).on( 'change', '.yith-pos-product-multistock-enabled', multiStock.handleMultiStockOptionsVisibility );
            $( document ).on( 'woocommerce_variations_loaded', '#woocommerce-product-data', multiStock.initAllMultiStockOptionsVisibility );
            multiStock.initAllMultiStockOptionsVisibility();

            multiStock.initAll();
            $( document ).on( 'change', '.variable_manage_stock', multiStock.initAll );
        },

        initAll: function () {
            $( '.yith-pos-product-multi-stock' ).each( function () {
                var $t = $( this );
                if ( !$t.find( '.yith-pos-group' ).length ) {
                    $t.find( '.clone-stock-group' ).click();
                }
            } );
        },

        addOption: function ( e ) {
            e.preventDefault();
            var t        = $( this ),
                loop     = t.data( 'loop' ),
                target   = t.closest( '.yith-pos-product-multi-stock' ).find( '.yith-pos-multistock-options' ),
                template = wp.template( 'yith-pos-stock-manager' + loop ),
                counter  = target.find( '.yith-pos-group' ).length;
            target.append( template( { id: counter, loop: loop } ) );

            $( document.body ).trigger( 'wc-enhanced-select-init' );
        },

        deleteOption: function ( e ) {
            e.preventDefault();
            var group   = $( this ).closest( '.yith-pos-group' ),
                options = group.closest( '.yith-pos-multistock-options' );

            group.remove();

            // re-create the first one if the list is empty
            if ( !options.find( '.yith-pos-group' ).length ) {
                options.closest( '.yith-pos-product-multi-stock' ).find( '.clone-stock-group' ).first().click();
            }
        },

        handleGeneralMultiStockVisibility: function () {
            var enabled = multiStock.dom.manageStock.is( ':checked' );
            multiStock.dom.inventoryMultiStockManagement.fadeTo( 0.5, enabled, function () {
                enabled ? $( this ).show() : $( this ).hide();
            } );
        },

        handleMultiStockOptionsVisibility: function () {
            var onOffWrapper      = $( this ),
                onOff             = onOffWrapper.find( 'input' ),
                wrapper           = onOffWrapper.closest( '.yith-pos-stock-management' ),
                multiStockWrapper = wrapper.find( '.yith-pos-product-multi-stock' );

            if ( 'yes' === onOff.val() ) {
                multiStockWrapper.show();
            } else {
                multiStockWrapper.hide();
            }
        },

        initAllMultiStockOptionsVisibility: function () {
            $( '.yith-pos-product-multistock-enabled' ).each( multiStock.handleMultiStockOptionsVisibility );
        }
    };

    multiStock.init();

} )( jQuery );
