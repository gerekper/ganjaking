<?php
/**
 * Refresh checkout on payment methods change.
 *
 * @package WooCommerce/Redsys/iupay
 */

defined( 'ABSPATH' ) || exit;

/**
 * Refresh checkout on payment methods change.
 */
function redsys_refresh_checkout_on_payment_methods_change() {
	?>
		<script type="text/javascript">
			// Added by WooCommerce Redsys Gateway https://woocommerce.com/products/redsys-gateway/
			(function($){
				$('form.checkout').on( 'change', 'input[name^="payment_method"]', function() {
					var t = { updateTimer: !1,  dirtyInput: !1,
						reset_update_checkout_timer: function() {
							clearTimeout(t.updateTimer)
						},
						trigger_update_checkout: function() {
							t.reset_update_checkout_timer(), t.dirtyInput = !1,
							$(document.body).trigger("update_checkout")
						}
					};
					t.trigger_update_checkout();
				});
			} )(jQuery);
		</script>
	<?php
	if ( WCRed()->is_gateway_enabled( 'insite' ) ) {
		?>
			<script type="text/javascript">
				// Added by WooCommerce Redsys Gateway https://woocommerce.com/products/redsys-gateway/
				(function($){
					$('form.checkout').on( 'change', 'input[name^="payment_method"]', function() {
						var t = { updateTimer: !1,  dirtyInput: !1,
							reset_update_checkout_timer: function() {
								clearTimeout(t.updateTimer)
							},
							trigger_update_checkout: function() {
								t.reset_update_checkout_timer(), t.dirtyInput = !1,
								$(document.body).trigger("update_checkout")
							}
						};
						t.trigger_update_checkout();
					});
				} )(jQuery);
				jQuery( document.body ).one( 'checkout_error', function() {
					if (jQuery('#payment_method_insite').is(':checked')) {
						setTimeout(location.reload.bind(location), 4000);
					}
				} );
				( function( $ ) {
					var orderReviewSection = $('#order_review');
					function toggleInsiteFields( display ) {
						var fields = $('#redsys-submit,.redsys-new-card-data,#redsys_save_token');
						var paymentMethodInsiteCheckbox = $( '#payment_method_insite' );
						var checkoutButton = $( '#place_order' );
						if ( ! fields.length ) {
							return;
						}
						if ( paymentMethodInsiteCheckbox.attr( 'checked' ) ) {
							fields.css( { display: display ? 'block' : 'none' } );
							checkoutButton.css( {
								display: display ? 'none' : 'inline-block',
								visibility: display ? 'hidden' : 'visible',
							});
						}
					}
					// Order review event delegation (the input is still not there).
					orderReviewSection.on( 'change', 'input[name="token"]', function( e ) {
						toggleInsiteFields( e.target.value === 'add' );
					} );
				}( jQuery ) );
			</script>
		<?php
	}
	if ( WCRed()->is_gateway_enabled( 'redsys' ) ) {
		?>
			<script type="text/javascript">
				// Added by WooCommerce Redsys Gateway https://woocommerce.com/products/redsys-gateway/
				(function($){
					$('form.checkout').on( 'change', 'input[name^="payment_method"]', function() {
						var t = { updateTimer: !1,  dirtyInput: !1,
							reset_update_checkout_timer: function() {
								clearTimeout(t.updateTimer)
							},
							trigger_update_checkout: function() {
								t.reset_update_checkout_timer(), t.dirtyInput = !1,
								$(document.body).trigger("update_checkout")
							}
						};
						t.trigger_update_checkout();
					});
				} )(jQuery);				
				( function( $ ) {
					var orderReviewSection = $('#order_review');
					function toggleRedsysFields( display ) {
						var fields = $('#redsys_save_token');
						var paymentMethodRedsysCheckbox = $( '#payment_method_redsys' );
						if ( ! fields.length ) {
							return;
						}
						if ( paymentMethodRedsysCheckbox.attr( 'checked' ) ) {
							fields.css( { display: display ? 'block' : 'none' } );
						}
					}
					// Order review event delegation (the input is still not there).
					orderReviewSection.on( 'change', 'input[name="token"]', function( e ) {
						toggleRedsysFields( e.target.value === 'add' );
					} );
				}( jQuery ) );
				</script>
			<?php
	}
	if ( WCRed()->is_gateway_enabled( 'googlepayredsys' ) ) {
		?>
			<script type="text/javascript">
				// Added by WooCommerce Redsys Gateway https://woocommerce.com/products/redsys-gateway/
				(function($) {
					$('form.checkout').on('change', 'input[name^="payment_method"]', function() {
						var t = {
							updateTimer: false,
							dirtyInput: false,
							reset_update_checkout_timer: function() {
								clearTimeout(t.updateTimer);
							},
							trigger_update_checkout: function() {
								t.reset_update_checkout_timer();
								t.dirtyInput = false;
								$(document.body).trigger("update_checkout");
							}
						};
						var paymentMethod = $(this).attr('id');
						if (paymentMethod === 'payment_method_googlepayredsys') {
							onGooglePayLoaded();
						} else {
							t.trigger_update_checkout();
						}
					});
				})(jQuery);
			</script>
		<?php
	}
}
add_action( 'wp_footer', 'redsys_refresh_checkout_on_payment_methods_change' );
