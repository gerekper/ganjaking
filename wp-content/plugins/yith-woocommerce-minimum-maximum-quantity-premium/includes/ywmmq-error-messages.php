<?php
/**
 * Error messages functions.
 *
 * @package YITH\MinimumMaximumQuantity
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'ywmmq_cart_error' ) ) {

	/**
	 * Sets error message for wrong cart quantity
	 *
	 * @param string $limit        The limit: min, max, step.
	 * @param mixed  $cart_limit   The cart limit.
	 * @param mixed  $total_cart   The total cart.
	 * @param string $current_page The current page.
	 * @param string $limit_type   The limit type.
	 *
	 * @return  string
	 * @since   1.0.0
	 */
	function ywmmq_cart_error( $limit, $cart_limit, $total_cart, $current_page, $limit_type ) {

		$find = array(
			'{limit}',
			'{cart_quantity}',
			'{cart_value}',
			'{cart_quote}',
		);

		$replace = array(
			( 'value' === $limit_type ? wc_price( $cart_limit ) : $cart_limit ),
			( 'value' === $limit_type ? '' : $total_cart ),
			( 'value' === $limit_type ? wc_price( $total_cart ) : '' ),
			( 'cart' === YITH_WMMQ()->contents_type ? esc_html__( 'cart', 'yith-woocommerce-minimum-maximum-quantity' ) : esc_html__( 'quote', 'yith-woocommerce-minimum-maximum-quantity' ) ),
		);

		$message = get_option( 'ywmmq_message_' . $limit . '_cart_' . $limit_type . '_' . $current_page );

		if ( 'no' === get_option( 'ywmmq_cart_value_shipping' ) && 'value' === $limit_type && 'cart' === YITH_WMMQ()->contents_type ) {
			$message .= ' (' . esc_html__( 'Shipping fees and related taxes excluded.', 'yith-woocommerce-minimum-maximum-quantity' ) . ')';
		}

		return str_replace( $find, $replace, $message );

	}
}

if ( ! function_exists( 'ywmmq_product_quantity_error' ) ) {

	/**
	 * Sets error message for wrong product quantity
	 *
	 * @param string  $limit             The limit: min, max, step.
	 * @param integer $product_limit_qty The limit quantity.
	 * @param array   $item              The product item.
	 * @param string  $current_page      The current page.
	 *
	 * @return  string
	 * @since   1.0.0
	 */
	function ywmmq_product_quantity_error( $limit, $product_limit_qty, $item, $current_page ) {

		$product_id   = ( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
		$product      = wc_get_product( $product_id );
		$product_name = '';

		switch ( $current_page ) {
			case 'cart':
				if ( YITH_WMMQ()->contents_type === 'cart' ) {
					if ( isset( $item['data'] ) ) {
						$product_title = $item['data']->get_name();
					} else {
						$product       = wc_get_product( $item['product_id'] );
						$product_title = $product->get_name();
					}
				} else {
					$product_title = $product->get_title();

					if ( $item['variation_id'] ) {

						$variation_data = '';
						$item_data      = array();
						foreach ( $item['variations'] as $name => $value ) {
							$label = '';

							if ( '' === $value ) {
								continue;
							}

							$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

							// If this is a term slug, get the term's nice name.
							if ( taxonomy_exists( $taxonomy ) ) {
								$term = get_term_by( 'slug', $value, $taxonomy );
								if ( ! is_wp_error( $term ) && $term && $term->name ) {
									$value = $term->name;
								}
								$label = wc_attribute_label( $taxonomy );

							} else {

								if ( strpos( $name, 'attribute_' ) !== false ) {
									$custom_att = str_replace( 'attribute_', '', $name );

									if ( '' !== $custom_att ) {
										$label = wc_attribute_label( $custom_att );
									} else {
										$label = $name;
									}
								}
							}

							$item_data[] = array(
								'key'   => $label,
								'value' => $value,
							);
						}

						if ( count( $item_data ) > 0 ) {
							foreach ( $item_data as $data ) {
								$variation_data .= esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . '<br/>';
							}
						}

						$variation_data = rtrim( $variation_data, '<br/>' );

						$product_title .= ' (' . $variation_data . ')';

					}
				}

				$product_name = sprintf( '<a href="%1$s">%2$s</a>', get_permalink( $item['product_id'] ), $product_title );

				break;

			case 'atc':
				$product_name = $product->get_title();
				break;

		}

		$find = array(
			'{limit}',
			'{product_name}',
			'{cart_quote}',
		);

		$replace = array(
			$product_limit_qty,
			$product_name,
			( YITH_WMMQ()->contents_type === 'cart' ? esc_html__( 'cart', 'yith-woocommerce-minimum-maximum-quantity' ) : esc_html__( 'quote', 'yith-woocommerce-minimum-maximum-quantity' ) ),
		);

		$message = get_option( 'ywmmq_message_' . $limit . '_product_quantity_' . $current_page );

		return str_replace( $find, $replace, $message );

	}
}

if ( ! function_exists( 'ywmmq_category_error' ) ) {

	/**
	 * Sets error message for wrong category quantity
	 *
	 * @param string  $limit          The limit: min, max, step.
	 * @param integer $category_limit The limit amount.
	 * @param integer $category_id    The category ID.
	 * @param string  $current_page   The current page.
	 * @param string  $limit_type     The limit type.
	 *
	 * @return  string
	 * @since   1.0.0
	 */
	function ywmmq_category_error( $limit, $category_limit, $category_id, $current_page, $limit_type ) {

		$category = get_term( $category_id, 'product_cat' );

		$category_name = '';

		switch ( $current_page ) {
			case 'cart':
				$category_name = '<a href="' . get_term_link( $category ) . '">' . $category->name . '</a>';
				break;

			case 'atc':
				$category_name = $category->name;
				break;

		}

		$find = array(
			'{limit}',
			'{category_name}',
			'{cart_quote}',
		);

		$replace = array(
			( 'value' === $limit_type ? wc_price( $category_limit ) : $category_limit ),
			$category_name,
			( 'cart' === YITH_WMMQ()->contents_type ? esc_html__( 'cart', 'yith-woocommerce-minimum-maximum-quantity' ) : esc_html__( 'quote', 'yith-woocommerce-minimum-maximum-quantity' ) ),
		);

		$message = get_option( 'ywmmq_message_' . $limit . '_category_' . $limit_type . '_' . $current_page );

		return str_replace( $find, $replace, $message );

	}
}

if ( ! function_exists( 'ywmmq_tag_error' ) ) {

	/**
	 * Sets error message for wrong tag quantity
	 *
	 * @param string  $limit        The limit: min, max, step.
	 * @param integer $tag_limit    The limit amount.
	 * @param integer $tag_id       The tag ID.
	 * @param string  $current_page The current page.
	 * @param string  $limit_type   The limit type.
	 *
	 * @return  string
	 * @since   1.0.0
	 */
	function ywmmq_tag_error( $limit, $tag_limit, $tag_id, $current_page, $limit_type ) {

		$tag = get_term( $tag_id, 'product_tag' );

		$tag_name = '';

		switch ( $current_page ) {
			case 'cart':
				$tag_name = '<a href="' . get_term_link( $tag ) . '">' . $tag->name . '</a>';
				break;

			case 'atc':
				$tag_name = $tag->name;
				break;

		}

		$find = array(
			'{limit}',
			'{tag_name}',
			'{cart_quote}',
		);

		$replace = array(
			( 'value' === $limit_type ? wc_price( $tag_limit ) : $tag_limit ),
			$tag_name,
			( 'cart' === YITH_WMMQ()->contents_type ? esc_html__( 'cart', 'yith-woocommerce-minimum-maximum-quantity' ) : esc_html__( 'quote', 'yith-woocommerce-minimum-maximum-quantity' ) ),
		);

		$message = get_option( 'ywmmq_message_' . $limit . '_tag_' . $limit_type . '_' . $current_page );

		return str_replace( $find, $replace, $message );

	}
}
