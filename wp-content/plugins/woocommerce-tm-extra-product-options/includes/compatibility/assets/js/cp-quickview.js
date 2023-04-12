( function( window, document, $ ) {
	'use strict';

	var TMEPOJS;
	var tcAPI;

	// document ready
	$( function() {
		TMEPOJS = window.TMEPOJS || null;
		tcAPI = $.tcAPI ? $.tcAPI() : null;

		if ( ! TMEPOJS || ! tcAPI ) {
			return;
		}

		// Sober theme quickview fix
		$( 'body' ).on( 'sober_quickview_opened', function() {
			var product_id;
			var epo_id;
			var tmLazyloadContainer;

			tmLazyloadContainer = $( '#quick-view-modal' );
			$.tcepo.tmLazyloadContainer( tmLazyloadContainer );

			product_id = tmLazyloadContainer.find( tcAPI.epoSelector ).attr( 'data-product-id' );
			epo_id = tmLazyloadContainer.find( tcAPI.epoSelector ).attr( 'data-epo-id' );

			$.tcepo.tm_init_epo( tmLazyloadContainer, true, product_id, epo_id );
			$( window ).trigger( 'tmlazy' );
			$( window ).trigger( 'tm_epo_loaded_quickview' );
			if ( $.jMaskGlobals ) {
				tmLazyloadContainer.find( $.jMaskGlobals.maskElements ).each( function() {
					var t = $( this );

					if ( t.attr( 'data-mask' ) ) {
						t.mask( t.attr( 'data-mask' ) );
					}
				} );
			}
		} );
		if ( window.ctEvents ) {
			window.ctEvents.on( 'blocksy:frontend:init', function() {
				// quickview plugins
				var qv_container = TMEPOJS.quickview_array || 'null';
				var fromaddons = TMEPOJS.quickview_container || 'null';
				var added = {};
				var selectors;
				var container;
				var product_id;
				var epo_id;
				var noProductCheck;
				var time = 1;
				var key = 'blocsky_get_woo_quick_view';
				var tmLazyloadContainer;

				$( '.tm-formepo-normal' ).remove();
				$( '.tm-formepo' ).remove();

				qv_container = $.epoAPI.util.parseJSON( qv_container );

				fromaddons = $.epoAPI.util.parseJSON( fromaddons );

				for ( selectors in fromaddons ) {
					if ( Object.prototype.hasOwnProperty.call( fromaddons, selectors ) ) {
						added[ fromaddons[ selectors ][ 0 ] ] = $( fromaddons[ selectors ][ 1 ] );
					}
				}

				$.extend( qv_container, added );

				noProductCheck = false;
				container = $( qv_container[ key ] );

				if ( container.find( '.product' ).length === 0 && container.is( '.product' ) ) {
					noProductCheck = true;
				}

				if ( container.length && ( container.find( '.product' ).length > 0 || noProductCheck ) ) {
					container.removeClass( 'tc-init' );

					tmLazyloadContainer = container;
					$.tcepo.tmLazyloadContainer( tmLazyloadContainer );

					setTimeout( function() {
						product_id = tmLazyloadContainer.find( tcAPI.epoSelector ).attr( 'data-product-id' );
						epo_id = tmLazyloadContainer.find( tcAPI.epoSelector ).attr( 'data-epo-id' );
						if ( key === 'woodmart_quick_shop' ) {
							container.addClass( 'has-options' );
						}

						// Reset element cache
						tcAPI.getElementFromFieldCache = [];
						$.tcepo.tm_init_epo( tmLazyloadContainer, true, product_id, epo_id );
						$( window ).trigger( 'tmlazy' );
						$( window ).trigger( 'tm_epo_loaded_quickview' );
						if ( $.jMaskGlobals ) {
							tmLazyloadContainer.find( $.jMaskGlobals.maskElements ).each( function() {
								var t = $( this );

								if ( t.attr( 'data-mask' ) ) {
									t.mask( t.attr( 'data-mask' ) );
								}
							} );
						}
					}, time );
				}
			} );
		}
	} );
}( window, document, window.jQuery ) );
