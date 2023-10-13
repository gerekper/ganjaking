<?php
/**
 * JS Template Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_print_js_template' ) ) {
	/**
	 * Print a JS template.
	 *
	 * @param string $id   The ID of the template.
	 * @param string $view The view to be printed.
	 * @param array  $args The arguments.
	 */
	function yith_wcbk_print_js_template( $id, $view, $args = array() ) {
		?>
		<script type="text/html" id="tmpl-<?php echo esc_attr( $id ); ?>">
			<?php yith_wcbk_get_view( $view, $args ); ?>
		</script>
		<?php

	}
}

if ( ! function_exists( 'yith_wcbk_print_create_booking_template' ) ) {
	/**
	 * Print the "Create Booking" template.
	 */
	function yith_wcbk_print_create_booking_template() {
		if ( current_user_can( 'yith_create_booking' ) ) {
			yith_wcbk_print_js_template( 'yith-wcbk-create-booking', 'create-booking.php' );
		}
	}
}
