;(function ( $ ) {
	'use strict';
	
	$.fn.tp_woo_variation_swatches_form = function () {
		return this.each( function() {
			var $woo_form = $( this );

			$woo_form.addClass( 'swatches-support' ).on( 'click', '.tp-swatches', function ( e ) {
					e.preventDefault();
					var $this = $( this ),
						$select = $this.closest( '.value' ).find( 'select' ),
						value = $this.attr( 'data-value' );

					if ( $this.hasClass( 'disabled' ) ) {
						return;
					}

					// older version of woo commerce
					$select.trigger( 'focusin' );

					// valid selector
					if ( ! $select.find( 'option[value="' + value + '"]' ).length ) {
						$this.siblings( '.tp-swatches' ).removeClass( 'selected' );
						$select.val( '' ).change();
						$woo_form.trigger( 'tp-woo_no_matching_variations', [$this] );
						return;
					}

					if ( $this.hasClass( 'selected' ) ) {
						$select.val( '' );
						$this.removeClass( 'selected' );
					} else {
						$this.addClass( 'selected' ).siblings( '.selected' ).removeClass( 'selected' );
						$select.val( value );
					}

					$select.change();
				} )
				.on( 'click', '.reset_variations', function () {
					$woo_form.find( '.tp-swatches.selected' ).removeClass( 'selected' );
					$woo_form.find( '.tp-swatches.disabled' ).removeClass( 'disabled' );
				} )
				.on( 'woocommerce_update_variation_values', function() {
					setTimeout( function() {
						$woo_form.find( 'tbody tr' ).each( function() {
							var $variation = $( this ),
								$options = $variation.find( 'select' ).find( 'option' ),
								$selected = $options.filter( ':selected' ),
								values = [];

							$options.each( function( index, option ) {
								if ( option.value !== '' ) {
									values.push( option.value );
								}
							} );

							$variation.find( '.tp-swatches' ).each( function() {
								var $swatches = $( this ),
									value = $swatches.attr( 'data-value' );

								if ( values.indexOf( value ) > -1 ) {
									$swatches.removeClass( 'disabled' );
								} else {
									$swatches.addClass( 'disabled' );

									if ( $selected.length && value === $selected.val() ) {
										$swatches.removeClass( 'selected' );
									}
								}
							} );
						} );
					}, 100 );
				} )
				.on( 'tp-woo_no_matching_variations', function() {
					window.alert( wc_add_to_cart_variation_params.i18n_no_matching_variations_text );
				} );
		} );
	};

	$( function () {
		$( '.variations_form' ).tp_woo_variation_swatches_form();
		$( document.body ).trigger( 'tp-woo_initialized' );
	} );
})( jQuery );