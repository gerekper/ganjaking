<?php
/**
 * Checkout order delivery.
 *
 * @package WC_OD/Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var array       $args            Template arguments.
 * @var string      $title           The section title.
 * @var string      $checkout_text   Additional text to display.
 * @var string      $checkout_option The kind of content to display.
 * @var WC_Checkout $checkout        Checkout object.
 */
?>
<div id="wc-od" class="wc-od-checkout checkout-option-<?php echo esc_attr( $checkout_option ); ?>">
	<?php
	if ( ! empty( $title ) ) :
		echo '<h3 class="wc-od-checkout-title">' . esc_html( $title ) . '</h3>';
	endif;

	/**
	 * Fired before displaying the delivery details in the checkout form.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Template arguments.
	 */
	do_action( 'wc_od_checkout_before_delivery_details', $args );

	if ( 'calendar' === $checkout_option ) :
		if ( ! empty( $checkout_text ) ) :
			echo '<p class="wc-od-checkout-text">' . wp_kses_post( $checkout_text ) . '</p>';
		endif;

		wc_od_get_template( 'order-delivery/checkout-delivery-fields.php', compact( 'checkout' ) );
	else :
		/**
		 * Displays the delivery details in the checkout form.
		 *
		 * @since 2.6.0
		 *
		 * @param array $args Template arguments.
		 */
		do_action( 'wc_od_checkout_delivery_details', $args );
	endif;

	/**
	 * Fired after displaying the delivery details in the checkout form.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Template arguments.
	 */
	do_action( 'wc_od_checkout_after_delivery_details', $args );
	?>
</div>
