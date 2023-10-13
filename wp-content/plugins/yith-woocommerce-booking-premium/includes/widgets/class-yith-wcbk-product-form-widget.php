<?php
/**
 * Class YITH_WCBK_Product_Form_Widget
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Product_Form_Widget' ) ) {
	/**
	 * YITH_WCBK_Product_Form_Widget
	 *
	 * @since  2.0.0
	 */
	class YITH_WCBK_Product_Form_Widget extends WC_Widget {
		/**
		 * Constructor
		 */
		public function __construct() {
			$this->widget_cssclass    = 'yith_wcbk_booking_product_form_widget';
			$this->widget_description = __( 'Display booking form', 'yith-booking-for-woocommerce' );
			$this->widget_id          = 'yith_wcbk_product_form';
			$this->widget_name        = _x( 'Bookable Product Form', 'Widget Name', 'yith-booking-for-woocommerce' );

			$this->settings = array();

			parent::__construct();
		}

		/**
		 * Print the widget
		 *
		 * @param array $args     Arguments.
		 * @param array $instance Widget data.
		 */
		public function widget( $args, $instance ) {
			global $product;

			if ( $this->get_cached_widget( $args ) ) {
				return;
			}

			if ( is_product() && yith_wcbk_is_booking_product( $product ) ) {
				wp_enqueue_script( 'yith-wcbk-mobile-fixed-form' );

				$mobile_fixed_enabled = apply_filters( 'yith_wcbk_product_form_widget_mobile_fixed', true );
				if ( $mobile_fixed_enabled ) {
					$mobile_move_to_footer = apply_filters( 'yith_wcbk_product_form_widget_mobile_move_to_footer', true );

					$classes = array(
						'yith_wcbk_booking_product_form_widget',
						'yith-wcbk-mobile-fixed-form',
						'with-full-width-button',
						$mobile_move_to_footer ? 'move-to-footer-in-mobile' : '',
					);

					$classes = implode( ' ', array_filter( $classes ) );

					$args['before_widget'] = str_replace( 'yith_wcbk_booking_product_form_widget', $classes, $args['before_widget'] );
				}

				ob_start();
				$this->widget_start( $args, $instance );

				wc_get_template( 'single-product/add-to-cart/booking-form/widget-booking-form.php', compact( 'product' ), '', YITH_WCBK_TEMPLATE_PATH );

				$this->widget_end( $args );
				wp_reset_postdata();
				echo $this->cache_widget( $args, ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Outputs the settings update form.
		 *
		 * @param array $instance Widget data.
		 */
		public function form( $instance ) {
			parent::form( $instance );

			echo '<p>' . esc_html__( 'The bookable product form', 'yith-booking-for-woocommerce' ) . '</p>';
		}
	}
}
