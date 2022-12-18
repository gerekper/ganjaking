<?php

$attrs = '';
global $product;
if ( ! empty( $product ) && isset( $atts['link_source'] ) ) {
	$common_cls = 'porto-tb-woo-link';

	global $porto_settings;
	$icon_html = '';
	if ( 'cart' == $atts['link_source'] && ! empty( $atts['icon_cls_variable'] ) && $product->is_type( 'variable' ) ) {
		$icon_html .= '<i class="' . esc_attr( $atts['icon_cls_variable'] ) . '"></i>';
		if ( empty( $atts['hide_title'] ) ) {
			$common_cls .= ' porto-tb-icon-' . ( ! empty( $atts['icon_pos'] ) ? $atts['icon_pos'] : 'left' );
		}
	} elseif ( ! empty( $atts['icon_cls'] ) ) {
		$icon_html .= '<i class="' . esc_attr( $atts['icon_cls'] ) . '"></i>';
		if ( empty( $atts['hide_title'] ) ) {
			$common_cls .= ' porto-tb-icon-' . ( ! empty( $atts['icon_pos'] ) ? $atts['icon_pos'] : 'left' );
		}
	}
	if ( ! empty( $atts['el_class'] ) && wp_is_json_request() ) {
		$common_cls .= ' ' . esc_attr( $atts['el_class'] );
	}
	if ( ! empty( $atts['className'] ) ) {
		$common_cls .= ' ' . esc_attr( trim( $atts['className'] ) );
	}

	if ( 'cart' == $atts['link_source'] ) {
		if ( $porto_settings['category-addlinks-convert'] ) {
			$tag = 'span';
		} else {
			$tag = 'a';
		}

		$btn_classes = $common_cls . ' porto-tb-addcart add_to_cart_button ' . 'product_type_' . $product->get_type() . ' viewcart-style-' . ( $porto_settings['add-to-cart-notification'] ? (int) $porto_settings['add-to-cart-notification'] : '1' );
		if ( isset( $args['class'] ) ) {
			$btn_classes .= ' ' . trim( $args['class'] );
		}
		if ( $product->is_purchasable() && $product->is_in_stock() ) {
			if ( $product->supports( 'ajax_add_to_cart' ) ) {
				$btn_classes .= ' ajax_add_to_cart';
			}
		} else {
			$btn_classes .= ' add_to_cart_read_more';
		}

		if ( apply_filters( 'porto_product_loop_show_price', true ) ) {

			global $porto_tb_catalog_mode;

			if ( ! empty( $atts['show_quantity_input'] ) && $product->is_type( 'simple' ) && $product->is_purchasable() ) {
				woocommerce_quantity_input(
					array(
						'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
						'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
						'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( sanitize_text_field( wp_unslash( $_POST['quantity'] ) ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
					)
				);
			}

			echo apply_filters(
				'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
				sprintf(
					'<%s href="%s" data-quantity="%s" class="' . esc_attr( apply_filters( 'porto_elements_wrap_css_class', $btn_classes, $atts, 'porto-tb/porto-woo-buttons' ) ) . ' %s" %s%s%s%s>%s</%s>',
					$tag,
					esc_url( apply_filters( 'porto_cpo_add_to_cart_url', $product->add_to_cart_url(), $product ) ),
					esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
					esc_attr( ( isset( $args['class'] ) ? $args['class'] : '' ) . ( $product->is_purchasable() && $product->is_in_stock() ? '' : ' add_to_cart_read_more' ) ),
					isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
					$porto_tb_catalog_mode ? '' : ' data-product_id="' . absint( $product->get_id() ) . '"',
					$porto_tb_catalog_mode ? '' : ' data-product_sku="' . esc_attr( $product->get_sku() ) . '"',
					' aria-label="' . wp_strip_all_tags( $product->add_to_cart_description() ) . '" rel="nofollow"',
					( ! isset( $atts['icon_pos'] ) || 'right' != $atts['icon_pos'] ? $icon_html : '' ) . ( empty( $atts['hide_title'] ) ? esc_html( $product->add_to_cart_text() ) : '' ) . ( isset( $atts['icon_pos'] ) && 'right' == $atts['icon_pos'] ? $icon_html : '' ),
					$tag
				),
				$product,
				isset( $args ) ? $args : array()
			);
		}
	} elseif ( 'wishlist' == $atts['link_source'] && defined( 'YITH_WCWL' ) ) {
		$exists    = YITH_WCWL()->is_product_in_wishlist( $product->get_id() );
		$shortcode = '[yith_wcwl_add_to_wishlist';
		/*if ( ! empty( $atts['icon_cls'] ) ) {
			$shortcode .= ' icon="' . esc_attr( $atts['icon_cls'] ) . '"';
		}*/
		$shortcode .= ']';
		echo '<div class="' . esc_attr( apply_filters( 'porto_elements_wrap_css_class', $common_cls . ' porto-tb-wishlist' . ( $exists ? ' exists' : '' ), $atts, 'porto-tb/porto-woo-buttons' ) ) . '">';
		echo do_shortcode( $shortcode );
		echo '</div>';
	} elseif ( 'compare' == $atts['link_source'] && function_exists( 'porto_template_loop_compare' ) ) {
		porto_template_loop_compare( apply_filters( 'porto_elements_wrap_css_class', $common_cls . ' porto-tb-compare', $atts, 'porto-tb/porto-woo-buttons' ), isset( $atts['hide_title'] ) ? $atts['hide_title'] : false, isset( $atts['icon_cls'] ) ? $atts['icon_cls'] : '', isset( $atts['icon_pos'] ) ? $atts['icon_pos'] : 'left', isset( $atts['icon_cls_added'] ) ? $atts['icon_cls_added'] : '' );
	} elseif ( 'quickview' == $atts['link_source'] ) {
		if ( ! wp_script_is( 'wc-add-to-cart-variation' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}

		$label = ! empty( $porto_settings['product-quickview-label'] ) ? $porto_settings['product-quickview-label'] : __( 'Quick View', 'porto' );

		$inner_html_escaped = '';
		if ( empty( $atts['hide_title'] ) ) {
			$inner_html_escaped = esc_html( $label );
		}
		if ( $icon_html ) {
			if ( ! isset( $atts['icon_pos'] ) || 'right' != $atts['icon_pos'] ) {
				$inner_html_escaped = $icon_html . $inner_html_escaped;
			} else {
				$inner_html_escaped .= $icon_html;
			}
		}
		echo '<div class="' . esc_attr( apply_filters( 'porto_elements_wrap_css_class', $common_cls . ' porto-tb-quickview quickview', $atts, 'porto-tb/porto-woo-buttons' ) ) . '" data-id="' . absint( $product->get_id() ) . '" title="' . esc_attr( $label ) . '">' . $inner_html_escaped . '</div>';

	} elseif ( 'swatch' == $atts['link_source'] && function_exists( 'porto_woocommerce_display_variation_on_shop_page' ) ) {
		if ( ! wp_script_is( 'wc-add-to-cart-variation' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}
		porto_woocommerce_display_variation_on_shop_page( apply_filters( 'porto_elements_wrap_css_class', $common_cls . ' porto-tb-swatch', $atts, 'porto-tb/porto-woo-buttons' ) );
	}
}
