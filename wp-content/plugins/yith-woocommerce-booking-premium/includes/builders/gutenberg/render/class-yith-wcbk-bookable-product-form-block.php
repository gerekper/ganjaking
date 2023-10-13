<?php
/**
 * Handle "Bookable product form" Gutenberg block.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Bookable_Product_Form_Block' ) ) {
	/**
	 * Booking form block class
	 *
	 * @since 4.0.0
	 */
	class YITH_WCBK_Bookable_Product_Form_Block extends YITH_WCBK_Render_Block_With_Style {

		/**
		 * Block attributes
		 *
		 * @var array
		 */
		protected $attributes = array(
			'showReviews'        => true,
			'useFullWidthButton' => true,
			'isFixedOnMobile'    => true,
		);

		/**
		 * Parse attributes.
		 *
		 * @param array $attributes Attributes.
		 */
		protected function parse_attributes( $attributes ) {
			$attributes = parent::parse_attributes( $attributes );

			$boolean_props = array(
				'showReviews',
				'useFullWidthButton',
				'isFixedOnMobile',
			);

			foreach ( $boolean_props as $boolean_prop ) {
				if ( ! is_bool( $attributes[ $boolean_prop ] ) && in_array( $attributes[ $boolean_prop ], array( 'true', 'false' ), true ) ) {
					$attributes[ $boolean_prop ] = 'true' === $attributes[ $boolean_prop ];
				}
			}

			return $attributes;
		}

		/**
		 * Get the show_reviews.
		 *
		 * @return string
		 */
		public function get_show_reviews() {
			return $this->attributes['showReviews'];
		}

		/**
		 * Get the use_full_width_button.
		 *
		 * @return string
		 */
		public function get_use_full_width_button() {
			return $this->attributes['useFullWidthButton'];
		}

		/**
		 * Get the is_fixed_on_mobile.
		 *
		 * @return string
		 */
		public function get_is_fixed_on_mobile() {
			return $this->attributes['isFixedOnMobile'];
		}

		/**
		 * Get the product.
		 *
		 * @return WC_Product_Booking|false
		 */
		protected function get_product() {
			global $product;

			$the_product = false;
			if ( $this->is_block_preview() ) {
				$context    = wc_clean( wp_unslash( $_REQUEST['context'] ?? array() ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$post_type  = $context['postType'] ?? false;
				$product_id = 'product' === $post_type ? ( $context['postId'] ?? false ) : false;

				if ( ! $product_id ) {
					$booking_product_ids = wc_get_products(
						array(
							'limit' => 1,
							'type'  => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
						)
					);
					if ( $booking_product_ids ) {
						$product_id = current( $booking_product_ids );
					}
				}

				if ( $product_id ) {
					$the_product = yith_wcbk_get_booking_product( $product_id );
				} else {
					$the_product = new WC_Product_Booking();
					$the_product->set_base_price( 100 );
					$the_product->set_rating_counts( array( 5 => 10 ) );
					$the_product->set_average_rating( 5 );
					$the_product->set_status( 'publish' );
				}
			} else {
				if ( $product instanceof WC_Product ) {
					$the_product = $product;
				} else {
					$the_product = wc_get_product();
				}
			}

			return yith_wcbk_is_booking_product( $the_product ) ? $the_product : false;
		}

		/**
		 * Render
		 */
		public function render() {
			$the_product        = $this->get_product();
			$is_fixed_on_mobile = $this->get_is_fixed_on_mobile() && ! $this->is_block_preview();

			if ( $the_product ) {
				if ( $this->is_block_preview() ) {
					wp_dequeue_script( 'yith-wcbk-booking-form' );
				}
				global $product, $post;
				$old_product = $product;
				$old_post    = $post;
				$product     = $the_product;

				if ( ! $the_product->get_id() && $this->is_block_preview() ) {
					$post            = false; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$product_classes = array( 'product', 'type-product', 'status-publish', 'instock', 'purchasable', 'product-type-booking' );
				} else {
					$post            = get_post( $product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$product_classes = wc_get_product_class( array(), $product );
				}

				$classes = array(
					'yith-wcbk-bookable-product-form-block',
					$this->get_use_full_width_button() ? 'with-full-width-button' : false,
					$is_fixed_on_mobile ? 'yith-wcbk-mobile-fixed-form' : false,
					'move-to-footer-in-mobile',
					'woocommerce',
				);
				$classes = array_filter( $classes );

				$style = $this->get_css_style();

				echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" style="' . esc_attr( $style ) . '">';
				if ( $is_fixed_on_mobile ) {
					wp_enqueue_script( 'yith-wcbk-mobile-fixed-form' );
					?>
					<div class="yith-wcbk-mobile-fixed-form__mouse-trap"></div>
					<span class="yith-wcbk-mobile-fixed-form__close"><?php yith_wcbk_print_svg( 'no' ); ?></span>
					<?php
				}

				echo '<div class="' . esc_attr( implode( ' ', $product_classes ) ) . '">';

				woocommerce_template_single_price();

				if ( $this->get_show_reviews() ) {
					woocommerce_template_single_rating();
				}

				do_action( 'yith_wcbk_booking_add_to_cart_form' );

				echo '</div>';
				echo '</div>';

				$product = $old_product;
				$post    = $old_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}
	}
}
