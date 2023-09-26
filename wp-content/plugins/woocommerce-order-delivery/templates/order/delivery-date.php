<?php
/**
 * Order delivery details.
 *
 * @package WC_OD/Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var array  $args             Template arguments.
 * @var string $title            The section title.
 * @var string $details_template The details template to load.
 */
?>
<section id="wc-od" class="wc-od-order-details">
	<?php
	if ( ! empty( $title ) ) :
		echo '<h2 class="wc-od-order-details-title">' . esc_html( $title ) . '</h2>';
	endif;

	/**
	 * Fired before displaying the delivery details in the order view.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Template arguments.
	 */
	do_action( 'wc_od_order_before_delivery_details', $args );

	if ( ! empty( $details_template ) ) :
		wc_od_get_template( $details_template, $args );
	endif;

	/**
	 * Fired after displaying the delivery details in the order view.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Template arguments.
	 */
	do_action( 'wc_od_order_after_delivery_details', $args );
	?>
</section>
