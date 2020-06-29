<?php
if ( ! function_exists( 'yith_wcpb_help_tip' ) ) {
	function yith_wcpb_help_tip( $tip, $allow_html = false ) {
		if ( function_exists( 'wc_help_tip' ) ) {
			return wc_help_tip( $tip, $allow_html );
		} else {
			if ( $allow_html ) {
				$tip = wc_sanitize_tooltip( $tip );
			} else {
				$tip = esc_attr( $tip );
			}
			$image_src = WC()->plugin_url() . '/assets/images/help.png';

			return "<img class='woocommerce-help-tip' heigth='16' width='16' data-tip='$tip' src='$image_src' />";
		}
	}
}

if ( ! function_exists( 'yith_wcpb_get_allowed_product_types' ) ) {
	function yith_wcpb_get_allowed_product_types() {
		$types = array(
			'simple'   => __( 'Simple', 'yith-woocommerce-product-bundles' ),
			'variable' => __( 'Variable', 'yith-woocommerce-product-bundles' ),
		);

		if ( ! defined( 'YITH_WCPB_PREMIUM' ) ) {
			unset( $types['variable'] );
		}

		return $types;
	}
}

if ( ! function_exists( 'yith_wcpb_wc_dropdown_variation_attribute_options' ) ) {
	function yith_wcpb_wc_dropdown_variation_attribute_options( $args = array() ) {
		if ( apply_filters( 'yith_wcpb_use_wc_dropdown_variation_attribute_options', true ) ) {
			wc_dropdown_variation_attribute_options( $args );
		} else {
			$args = wp_parse_args(
				apply_filters( 'yith_wcpb_wc_dropdown_variation_attribute_options_args', $args ),
				array(
					'options'          => false,
					'attribute'        => false,
					'product'          => false,
					'selected'         => false,
					'name'             => '',
					'id'               => '',
					'class'            => '',
					'show_option_none' => __( 'Choose an option', 'woocommerce' ),
				)
			);

			// Get selected value.
			if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
				$selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
				$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
			}

			$options               = $args['options'];
			$product               = $args['product'];
			$attribute             = $args['attribute'];
			$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
			$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
			$class                 = $args['class'];
			$show_option_none      = (bool) $args['show_option_none'];
			$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

			if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
				$attributes = $product->get_variation_attributes();
				$options    = $attributes[ $attribute ];
			}

			$html = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
			$html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					// Get terms if this is a taxonomy - ordered. We need the names too.
					$terms = wc_get_product_terms(
						$product->get_id(),
						$attribute,
						array(
							'fields' => 'all',
						)
					);

					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options, true ) ) {
							$html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
						}
					}
				} else {
					foreach ( $options as $option ) {
						// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
						$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
						$html     .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
					}
				}
			}

			$html .= '</select>';

			echo apply_filters( 'yith_wcpb_wc_dropdown_variation_attribute_options_html', $html, $args ); // WPCS: XSS ok.
		}
	}
}

if ( ! function_exists( 'yith_wcpb_get_bundle_products_by_item' ) ) {
	function yith_wcpb_get_bundle_products_by_item( $product ) {
		$product = wc_get_product( $product );
		if ( ! $product ) {
			return array();
		}

		$product_id        = $product->get_id();
		$product_id_strlen = strlen( (string) $product_id );
		$to_search         = '"product_id";s:' . $product_id_strlen . ':"' . $product_id . '"';


		$args = array(
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_query'     => array( array( 'key' => '_yith_wcpb_bundle_data', 'value' => $to_search, 'compare' => 'LIKE' ) ),
			'tax_query'      => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'yith_bundle' ) ),
			'fields'         => 'ids',
		);

		return get_posts( $args );
	}
}

if ( ! function_exists( 'yith_wcpb_get_price_to_display' ) ) {
	/**
	 * @param WC_Product   $product
	 * @param string|float $price
	 * @param int          $qty
	 *
	 * @return float
	 * @since 1.3.2
	 */
	function yith_wcpb_get_price_to_display( $product, $price = '', $qty = 1 ) {
		return (float) wc_get_price_to_display( $product, array( 'qty' => $qty, 'price' => $price ) );
	}
}

if ( ! function_exists( 'yith_wcpb_round_bundled_item_price' ) ) {

	function yith_wcpb_round_bundled_item_price( $bundled_item_price ) {
		$rounded = $bundled_item_price;
		if ( apply_filters( 'yith_wcpb_round_bundled_item_price', true ) ) {
			$rounded = wc_add_number_precision( $rounded );
			$rounded = round( $rounded );
			$rounded = wc_remove_number_precision( $rounded );
		}

		return apply_filters( 'yith_wcpb_round_bundled_item_price_rounded', $rounded, $bundled_item_price );
	}
}