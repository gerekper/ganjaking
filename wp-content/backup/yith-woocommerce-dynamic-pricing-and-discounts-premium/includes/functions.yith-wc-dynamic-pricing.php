<?php
/**
 * General functions.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! function_exists( 'ywdpd_have_dynamic_coupon' ) ) {
	/**
	 * Check if cart has dynamic coupon.
	 *
	 * @return bool
	 */
	function ywdpd_have_dynamic_coupon() {

		$coupons = WC()->cart->get_coupons();

		if ( empty( $coupons ) ) {
			return false;
		}

		$dynamic_coupon = YITH_WC_Dynamic_Pricing()->get_option( 'coupon_label' );

		foreach ( $coupons as $code => $value ) {
			if ( strtolower( $code ) === strtolower( $dynamic_coupon ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'ywdpd_get_discounted_price_table' ) ) {
	/**
	 * Get the discounted price table.
	 *
	 * @param float $price Price.
	 * @param array $rule Rule.
	 *
	 * @return int
	 */
	function ywdpd_get_discounted_price_table( $price, $rule ) {

		if ( empty( $price ) || ! isset( $rule['type_discount'] ) ) {
			return $price;
		}

		$discount = 0;

		if ( 'percentage' === $rule['type_discount'] ) {
			$discount = $price * $rule['discount_amount'];
		} elseif ( 'price' === $rule['type_discount'] ) {
			$discount = $rule['discount_amount'];
		} elseif ( 'fixed-price' === $rule['type_discount'] ) {
			$discount = $price - apply_filters( 'ywdpd_maybe_should_be_converted', (float)$rule['discount_amount'] );
		}

		$new_price = ( ( $price - $discount ) < 0 ) ? 0 : ( $price - $discount );

		return apply_filters( 'ywdp_discounted_price_table', $new_price, $price, $rule );
	}
}

if ( ! function_exists( 'ywdpd_check_cart_coupon' ) ) {
	/**
	 * Check if cart have already coupon applied
	 *
	 * @since 1.1.4
	 * @author Emanuela Castorina
	 */
	function ywdpd_check_cart_coupon() {

		if ( ! WC()->cart ) {
			return false;
		}

		$cart_coupons = WC()->cart->applied_coupons;

		if ( ywdpd_have_dynamic_coupon() ) {
			return false;
		}

		return apply_filters( 'ywdpd_check_cart_coupon', ! empty( $cart_coupons ) );
	}
}

if ( ! function_exists( 'yit_wpml_object_id' ) ) {
	/**
	 * Get id of post translation in current language
	 *
	 * @param int         $element_id Element id.
	 * @param string      $element_type Element type.
	 * @param bool        $return_original_if_missing Bool.
	 * @param null|string $ulanguage_code Language code.
	 *
	 * @return int the translation id
	 * @since  2.0.0
	 * @author Antonio La Rocca <antonio.larocca@yithemes.com>
	 */
	function yit_wpml_object_id( $element_id, $element_type = 'post', $return_original_if_missing = false, $ulanguage_code = null ) {
		if ( function_exists( 'wpml_object_id_filter' ) ) {
			return wpml_object_id_filter( $element_id, $element_type, $return_original_if_missing, $ulanguage_code );
		} elseif ( function_exists( 'icl_object_id' ) ) {
			return icl_object_id( $element_id, $element_type, $return_original_if_missing, $ulanguage_code );
		} else {
			return $element_id;
		}
	}
}

if ( ! function_exists( 'ywdpd_get_note' ) ) {
	/**
	 * Get the note.
	 *
	 * @param string $note Note.
	 * @return mixed|void
	 */
	function ywdpd_get_note( $note ) {
		$wpml_extend_to_translated_object = YITH_WC_Dynamic_Pricing()->get_option( 'wpml_extend_to_translated_object' );
		if ( defined( 'ICL_SITEPRESS_VERSION' ) && ywdpd_is_true( $wpml_extend_to_translated_object ) ) {
			global $sitepress;
			$current_language = $sitepress->get_current_language();
			$epression_rule   = '/(?<=\[' . $current_language . '\])(\s*.*\s*)(?=\[\/' . $current_language . '\])/';
			if ( preg_match( $epression_rule, $note, $match ) ) {
				$note = $match[0];
			}
		}

		return apply_filters( 'ywdpd_get_note', $note );
	}
}

if ( ! function_exists( 'ywdpd_coupon_is_valid' ) ) {

	/**
	 * Check if a coupon is valid
	 *
	 * @param mixed $coupon Coupon.
	 * @param array $object Object.
	 *
	 * @return bool|WP_Error
	 * @throws Exception Return the error.
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_coupon_is_valid( $coupon, $object = array() ) {
		if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
			$wc_discounts = new WC_Discounts( $object );
			$valid        = $wc_discounts->is_coupon_valid( $coupon );
			$valid        = is_wp_error( $valid ) ? false : $valid;
		} else {
			$valid = $coupon->is_valid();
		}

		$valid = apply_filters( 'yith_ywdpd_coupon_is_valid', $valid, $coupon, $object );

		return $valid;
	}
}


if ( ! function_exists( 'ywdpd_check_valid_admin_page' ) ) {
	/**
	 * Return if the current pagenow is valid for a post_type, useful if you want add metabox, scripts inside the editor of a particular post type
	 *
	 * @param string $post_type_name Post Type Name.
	 *
	 * @return bool
	 * @author Emanuela Castorina
	 */
	function ywdpd_check_valid_admin_page( $post_type_name ) {
		global $pagenow;

		$posted = $_REQUEST;
		$post   = isset( $posted['post'] ) ? $posted['post'] : ( isset( $posted['post_ID'] ) ? $posted['post_ID'] : 0 );
		$post   = get_post( $post );

		if ( ( $post && $post->post_type === $post_type_name ) || ( isset( $posted['post_type'] ) && $post_type_name === $posted['post_type'] ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'ywdpd_discount_pricing_mode' ) ) {
	/**
	 * Return the labels of discount types.
	 *
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_discount_pricing_mode() {

		return array(
			'bulk'          => __( 'Quantity Discount', 'ywdpd' ),
			'special_offer' => __( 'Special Offer', 'ywdpd' ),
			'exclude_items' => __( 'Exclude items from rules', 'ywdpd' ),
			'gift_products' => __( 'Gift Products', 'ywdpd' ),
		);
	}
}

if ( ! function_exists( 'yith_ywdpd_check_update_to_cpt' ) ) {
	/**
	 * Check if is necessary transform the rules from option to cpt
	 *
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function yith_ywdpd_check_update_to_cpt() {

		$ywdpd_updated_to_cpt = get_option( 'ywdpd_updated_to_cpt' );
		if ( ! ywdpd_is_true( $ywdpd_updated_to_cpt ) && get_option( 'yit_ywdpd_options' ) ) {
			yith_ywdpd_update_to_cpt();
		}
	}
}

if ( ! function_exists( 'yith_ywdpd_update_to_cpt' ) ) {

	/**
	 * Transforms the old rules in Custom post type
	 *
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function yith_ywdpd_update_to_cpt() {
		$option_types = array( 'pricing', 'cart' );
		$options      = get_option( 'yit_ywdpd_options' );
		$args         = array(
			'post_type'      => 'ywdpd_discount',
			'comment_status' => 'closed',
			'post_status'    => 'publish',
		);
		if ( $options ) {

			foreach ( $option_types as $type ) {
				$priority           = 0;
				$cart_discount_rule = array();
				if ( isset( $options[ $type . '-rules' ] ) ) {
					$rules = $options[ $type . '-rules' ];

					foreach ( $rules as $key => $value ) {
						$priority++;
						$args['post_title'] = $value['description'];

						$id = wp_insert_post( $args );
						if ( $id ) {
							add_post_meta( $id, '_key', $key );
							add_post_meta( $id, '_discount_type', $type );
							add_post_meta( $id, '_priority', $priority );
							foreach ( $value as $_key => $item ) {
								if ( 'cart' === $type ) {
									if ( 'discount_type' === $_key || 'discount_amount' === $_key ) {
										$cart_discount_rule[ $_key ] = $item;
									}
								}
								$meta_key = in_array( $_key, array( 'rules', 'so-rule' ) ) ? $_key : '_' . $_key;
								add_post_meta( $id, $meta_key, $item );
							}

							! empty( $cart_discount_rule ) && add_post_meta( $id, '_discount_rule', $cart_discount_rule );
						}
					}
				}
			}

			update_option( 'ywdpd_updated_to_cpt', 'yes' );
		}
	}
}

if ( ! function_exists( 'ywdpd_recover_rules' ) ) {
	/**
	 * Recover the rules from cache.
	 *
	 * @param string $type Discount type.
	 *
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_recover_rules( $type ) {
		$enable_cache   = apply_filters( 'ywdpd_enable_cache_for_rules', false );
		$transient_name = 'ywdpd_discount_ids_' . $type;
		$post_ids       = get_transient( $transient_name );
		if ( $enable_cache || false === $post_ids ) {
			$args = array(
				'post_type'      => 'ywdpd_discount',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => '_discount_type',
						'value' => $type,
					),
				),
				'orderby'        => 'meta_value_num',
				'meta_key'       => '_priority',
				'order'          => 'ASC',
			);

			$post_ids = get_posts( $args );
			set_transient( $transient_name, $post_ids );
		}

		$rules = array();
		if ( ! empty( $post_ids ) ) {
			foreach ( $post_ids as $post_id ) {
				$metas = get_post_meta( $post_id );

				if ( $metas ) {
					$rule       = array();
					$rule['id'] = $post_id;
					foreach ( $metas as $key => $meta_value ) {
						$new_key          = ywdpd_maybe_remove_prefix_key( $key );
						$rule[ $new_key ] = ywdpd_format_meta_value( reset( $meta_value ), $new_key );
					}

					if ( 'cart' === $type && isset( $rule['discount_rule'] ) ) {
						$rule['discount_amount'] = isset( $rule['discount_rule']['discount_amount'] ) ? $rule['discount_rule']['discount_amount'] : '';
						$rule['discount_type']   = isset( $rule['discount_rule']['discount_type'] ) ? $rule['discount_rule']['discount_type'] : '';
					}
				}

				if ( isset( $rule['key'] ) ) {
					$rules[ $rule['key'] ] = $rule;
				}
			}
		}

		return $rules;
	}
}

if ( ! function_exists( 'ywdpd_format_meta_value' ) ) {
	/**
	 * Get the meta value formatted.
	 *
	 * @param mixed  $value Value.
	 * @param string $key Key.
	 *
	 * @return int|mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_format_meta_value( $value, $key ) {
		$value = maybe_unserialize( $value );

		if ( 'yes' === $value && 'active' !== $key ) {
			$value = 1;
		} elseif ( 'active' === $key && 1 === $value ) {
			$value = 'yes';
		}

		return $value;

	}
}

if ( ! function_exists( 'ywdpd_maybe_remove_prefix_key' ) ) {
	/**
	 * Remove the char '_' from a word
	 *
	 * @param string $key Key to remove.
	 *
	 * @return bool|string
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_maybe_remove_prefix_key( $key ) {
		return '_' === substr( $key, 0, 1 ) ? substr( $key, 1 ) : $key;
	}
}

if ( ! function_exists( 'ywdpd_get_last_priority' ) ) {
	/**
	 * Returns the last priority
	 *
	 * @param string $type Discount type.
	 *
	 * @return int|mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywdpd_get_last_priority( $type ) {

		$args = array(
			'post_type'      => 'ywdpd_discount',
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'   => '_discount_type',
					'value' => $type,
				),
			),
			'orderby'        => 'meta_value_num',
			'meta_key'       => '_priority',
			'order'          => 'DESC',
		);

		$posts = new WP_Query( $args );

		return ( $posts->post ) ? get_post_meta( $posts->post->ID, '_priority', true ) : 1;

	}
}


if ( ! function_exists( 'ywdpd_is_true' ) ) {
	/**
	 * Check if a value is true.
	 *
	 * @param mixed $value Value to check.
	 *
	 * @return bool
	 */
	function ywdpd_is_true( $value ) {
		return true === $value || 1 === $value || '1' === $value || 'yes' === $value;
	}
}

if ( class_exists( 'WOOCS' ) && ! function_exists( 'ywdpd_woocs_maybe_should_be_converted' ) ) {
	add_filter( 'ywdpd_maybe_should_be_converted', 'ywdpd_woocs_maybe_should_be_converted', 10, 2 );
	/**
	 * Integration with WOOCS.
	 *
	 * @param float $price Price.
	 *
	 * @return float|int
	 */
	function ywdpd_woocs_maybe_should_be_converted( $price ) {
		global $WOOCS; //phpcs:ignore

		if ( $WOOCS->is_multiple_allowed ) { //phpcs:ignore
			$current = $WOOCS->current_currency; //phpcs:ignore
			if ( $current != $WOOCS->default_currency ) { //phpcs:ignore
				$currencies = $WOOCS->get_currencies(); //phpcs:ignore
				$rate       = $currencies[ $current ]['rate'];
				$price      = $price * ( $rate );
			}
		}

		return $price;
	}
}
