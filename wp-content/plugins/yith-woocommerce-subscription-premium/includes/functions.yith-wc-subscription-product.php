<?php
/**
 * Implements helper functions for YITH WooCommerce Subscription related to subscripion product
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'ywsbs_is_subscription_product' ) ) {
	/**
	 * Check if a product is a subscription.
	 *
	 * @param WC_Product|int $product Product Object or Product ID.
	 * @return bool
	 * @since  2.0.0
	 */
	function ywsbs_is_subscription_product( $product ) {

		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return false;
		}

		$is_subscription = $product->get_meta( '_ywsbs_subscription' );
		$price_is_per    = $product->get_meta( '_ywsbs_price_is_per' );

		$is_subscription = ( 'yes' === $is_subscription && '' !== $price_is_per );
		return apply_filters( 'ywsbs_is_subscription', $is_subscription, $product->get_id() );
	}
}


if ( ! function_exists( 'ywsbs_is_limited_product' ) ) {
	/**
	 * Check if a the subscription product is limited.
	 *
	 * @param WC_Product|int $product Product Object or Product ID.
	 * @return bool|string The value can be false or 'one-active'|'one'.
	 * @since  2.0.0
	 */
	function ywsbs_is_limited_product( $product ) {

		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product || ! ywsbs_is_subscription_product( $product ) ) {
			return false;
		}

		$enable_limit = $product->get_meta( '_ywsbs_enable_limit' );
		$is_limited   = $product->get_meta( '_ywsbs_limit' );

		$is_limited = 'yes' === $enable_limit ? $is_limited : false;
		return apply_filters( 'ywsbs_is_limited_product', $is_limited, $product->get_id() );
	}
}


if ( ! function_exists( 'ywsbs_wp_radio' ) ) {
	/**
	 * Output a radio input box.
	 *
	 * @param array $field Field.
	 */
	function ywsbs_wp_radio( $field ) {
		global $thepostid, $post;

		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

		echo '<fieldset class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><legend>' . wp_kses_post( $field['label'] ) . '</legend>';

		if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
			echo wp_kses_post( wc_help_tip( $field['description'] ) );
		}

		echo '<ul class="wc-radios">';

		foreach ( $field['options'] as $key => $value ) {

			echo '<li><label><input
				name="' . esc_attr( $field['name'] ) . '"
				value="' . esc_attr( $key ) . '"
				type="radio"
				class="' . esc_attr( $field['class'] ) . '"
				style="' . esc_attr( $field['style'] ) . '"
				' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
				/> ' . wp_kses_post( $value ) . '</label>
		</li>';
		}
		echo '</ul>';

		if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

		echo '</fieldset>';
	}
}

if ( ! function_exists( 'yith_ywsbs_get_product_meta' ) ) {
	/**
	 * Return the product meta of a variation product.
	 *
	 * @param YWSBS_Subscription $subscription Subscription.
	 * @param array              $attributes Attributes.
	 * @param bool               $echo Print or return meta flag.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function yith_ywsbs_get_product_meta( $subscription, $attributes = array(), $echo = true ) {

		$item_data = array();

		if ( ! empty( $subscription->get_variation_id() ) ) {
			$variation = wc_get_product( $subscription->get_variation_id() );

			if ( empty( $attributes ) ) {
				$attributes = $variation->get_attributes();
			}

			foreach ( $attributes as $name => $value ) {
				if ( '' === $value ) {
					continue;
				}

				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );
				$label    = '';
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
							$label = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $name ), $name );
						}
					}
				}

				$item_data[] = array(
					'key'   => $label,
					'value' => $value,
				);
			}
		}

		// APPLY_FILTER: ywsbs_item_data: the meta data of a variation product can be filtered : YWSBS_Subscription is passed as argument.
		$item_data = apply_filters( 'ywsbs_item_data', $item_data, $subscription );
		$out       = '';
		// Output flat or in list format.
		if ( count( $item_data ) > 0 ) {
			foreach ( $item_data as $data ) {
				if ( $echo ) {
					echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
				} else {
					$out .= ' - ' . esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . ' ';
				}
			}
		}

		return $out;

	}
}

if ( ! function_exists( 'ywsbs_get_daily_amount_of_a_product' ) ) {
	/**
	 * Calculate the daily amount of a subscription product.
	 *
	 * @param WC_Product $product Product.
	 */
	function ywsbs_get_daily_amount_of_a_product( $product ) {
		$_ywsbs_price_is_per      = (int) $product->get_meta( '_ywsbs_price_is_per' );
		$_ywsbs_price_time_option = $product->get_meta( '_ywsbs_price_time_option' );
		$_price                   = (float) $product->get_price();

		return ywsbs_calculate_daily_amount( $_ywsbs_price_is_per, $_ywsbs_price_time_option, $_price );
	}
}

if ( ! function_exists( 'ywsbs_get_product_fee' ) ) {
	/**
	 * Return the fee of a product.
	 *
	 * @param WC_Product $product Product.
	 * @param string     $context Context.
	 */
	function ywsbs_get_product_fee( $product, $context = 'view' ) {
		$enable_fee = $product->get_meta( '_ywsbs_enable_fee' );
		$fee        = $product->get_meta( '_ywsbs_fee' );

		// retro compatibility.
		if ( empty( $enable_fee ) ) {
			$enable_fee = empty( $fee ) ? 'no' : 'yes';
		}

		$fee = 'yes' === $enable_fee ? $fee : '';
		if ( 'view' === $context ) {
			return apply_filters( 'ywsbs_product_fee', $fee, $product );
		} else {
			return $fee;
		}

	}
}

if ( ! function_exists( 'ywsbs_get_product_trial' ) ) {
	/**
	 * Return the trial of a product.
	 *
	 * @param WC_Product $product Product.
	 */
	function ywsbs_get_product_trial( $product ) {
		$enable_trial = $product->get_meta( '_ywsbs_enable_trial' );
		$trial        = $product->get_meta( '_ywsbs_trial_per' );

		// retro compatibility.
		if ( empty( $enable_trial ) ) {
			$enable_trial = empty( $trial ) ? 'no' : 'yes';
		}

		return 'yes' === $enable_trial ? $trial : '';
	}
}
