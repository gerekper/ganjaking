<?php
/**
 * Handle "Booking form" Gutenberg block.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Booking_Form_Block' ) ) {
	/**
	 * Booking form block class
	 *
	 * @since 3.1.0
	 */
	class YITH_WCBK_Booking_Form_Block extends YITH_WCBK_Render_Block_With_Style {

		/**
		 * Block attributes
		 *
		 * @var array
		 */
		protected $attributes = array(
			'productId'          => 0,
			'showTitle'          => false,
			'titleTag'           => 'h2',
			'showReviews'        => true,
			'showMeta'           => false,
			'useFullWidthButton' => true,
		);

		/**
		 * Parse attributes.
		 *
		 * @param array $attributes Attributes.
		 */
		protected function parse_attributes( $attributes ) {
			$attributes = $this->map_from_old_version( $attributes );
			$attributes = parent::parse_attributes( $attributes );

			$boolean_props = array(
				'showTitle',
				'showReviews',
				'showMeta',
				'useFullWidthButton',
			);

			foreach ( $boolean_props as $boolean_prop ) {
				if ( ! is_bool( $attributes[ $boolean_prop ] ) && in_array( $attributes[ $boolean_prop ], array( 'true', 'false' ), true ) ) {
					$attributes[ $boolean_prop ] = 'true' === $attributes[ $boolean_prop ];
				}
			}

			return $attributes;
		}

		/**
		 * Map attributes from old version.
		 *
		 * @param array $attributes Attributes.
		 *
		 * @return array
		 */
		protected function map_from_old_version( array $attributes ): array {
			$mapping = array(
				'product_id'            => 'productId',
				'show_title'            => 'showTitle',
				'title_tag'             => 'titleTag',
				'show_reviews'          => 'showReviews',
				'show_meta'             => 'showMeta',
				'use_full_width_button' => 'useFullWidthButton',
				'text_color'            => 'textColor',
				'background_color'      => 'backgroundColor',
			);

			foreach ( $mapping as $old => $new ) {
				if ( isset( $attributes[ $old ] ) ) {
					$attributes[ $new ] = $attributes[ $old ];
					unset( $attributes[ $old ] );
				}
			}

			return $attributes;
		}

		/**
		 * Get the product.
		 *
		 * @return string
		 */
		public function get_product_id() {
			return $this->attributes['productId'];
		}

		/**
		 * Get the show_title.
		 *
		 * @return string
		 */
		public function get_show_title() {
			return $this->attributes['showTitle'];
		}

		/**
		 * Get the title_tag.
		 *
		 * @return string
		 */
		public function get_title_tag() {
			return $this->attributes['titleTag'];
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
		 * Get the show_meta.
		 *
		 * @return string
		 */
		public function get_show_meta() {
			return $this->attributes['showMeta'];
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
		 * Get the product.
		 *
		 * @return WC_Product_Booking|false
		 */
		public function get_product() {
			$product = wc_get_product( $this->get_product_id() );

			return yith_wcbk_is_booking_product( $product ) ? $product : false;
		}

		/**
		 * Render
		 */
		public function render() {
			$the_product = $this->get_product();

			if ( $the_product ) {
				if ( $this->is_block_preview() ) {
					wp_dequeue_script( 'yith-wcbk-booking-form' );
				}
				global $product, $post;
				$old_product = $product;
				$old_post    = $post;
				$post        = get_post( $the_product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$product     = $the_product;

				$classes = array(
					'yith-wcbk-booking-form-block',
					$this->get_use_full_width_button() ? 'with-full-width-button' : false,
					'woocommerce',
				);
				$classes = array_filter( $classes );

				$product_classes = wc_get_product_class( array(), $product );
				$style           = $this->get_css_style();

				echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" style="' . esc_attr( $style ) . '">';
				echo '<div class="' . esc_attr( implode( ' ', $product_classes ) ) . '">';

				if ( $this->get_show_title() ) {
					$tag   = $this->get_title_tag();
					$start = '<' . $tag . '>';
					$end   = '</' . $tag . '>';
					the_title( $start, $end );
				}

				woocommerce_template_single_price();

				if ( $this->get_show_reviews() ) {
					woocommerce_template_single_rating();
				}

				do_action( 'yith_wcbk_booking_add_to_cart_form' );

				if ( $this->get_show_meta() ) {
					woocommerce_template_single_meta();
				}

				echo '</div>';
				echo '</div>';

				$product = $old_product;
				$post    = $old_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			} else {
				if ( $this->is_blank_state_allowed() ) {
					$this->render_blank_state();
				}
			}

		}

		/**
		 * Retrieve blank state params.
		 *
		 * @return array
		 */
		public function get_blank_state_params() {
			$message = __( 'Please, select a bookable product!', 'yith-booking-for-woocommerce' );

			return array(
				'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
				'message'  => $message,
			);
		}
	}
}
