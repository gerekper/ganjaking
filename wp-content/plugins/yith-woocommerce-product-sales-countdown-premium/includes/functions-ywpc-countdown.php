<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'ywpc_days' ) ) {

	/**
	 * How many days remains to $to
	 *
	 * @since   1.0.0
	 *
	 * @param   $to
	 *
	 * @return  integer
	 * @author  Alberto Ruggiero
	 */
	function ywpc_days( $total_seconds ) {

		return floor( $total_seconds / 60 / 60 / 24 );

	}

}

if ( ! function_exists( 'ywpc_hours' ) ) {

	/**
	 * How many hours remains to $to
	 *
	 * @since   1.0.0
	 *
	 * @param   $to
	 *
	 * @return  integer
	 * @author  Alberto Ruggiero
	 */
	function ywpc_hours( $total_seconds ) {

		return floor( $total_seconds / 60 / 60 );

	}

}

if ( ! function_exists( 'ywpc_minutes' ) ) {

	/**
	 * How many minutes remains to $to
	 *
	 * @since   1.0.0
	 *
	 * @param   $to
	 *
	 * @return  integer
	 * @author  Alberto Ruggiero
	 */
	function ywpc_minutes( $total_seconds ) {

		return floor( $total_seconds / 60 );

	}

}

if ( ! function_exists( 'ywpc_seconds' ) ) {

	/**
	 * How many seconds remains to $to
	 *
	 * @since   1.0.0
	 *
	 * @param   $to
	 *
	 * @return  integer
	 * @author  Alberto Ruggiero
	 */
	function ywpc_seconds( $to ) {

		return ywpc_current_timestamp( $to );

	}

}

if ( ! function_exists( 'ywpc_get_countdown' ) ) {

	/**
	 * Return Countdown
	 *
	 * @since   1.0.0
	 *
	 * @param   $end_date
	 *
	 * @return  array
	 * @author  Alberto Ruggiero
	 */
	function ywpc_get_countdown( $end_date ) {

		$total_seconds = ( $end_date - strtotime( current_time( "Y-m-d H:i:s" ) ) );
		$total_days    = ywpc_days( $total_seconds );
		$total_hours   = ywpc_hours( $total_seconds );
		$total_minutes = ywpc_minutes( $total_seconds );

		$days    = $total_days;
		$hours   = $total_hours - ( $total_days * 24 );
		$minutes = $total_minutes - ( $total_hours * 60 );
		$seconds = $total_seconds - ( $total_minutes * 60 );

		return array(
			'gmt' => get_option( 'gmt_offset' ),
			'to'  => $end_date,
			'dd'  => str_pad( $days, 3, '0', STR_PAD_LEFT ),
			'hh'  => str_pad( $hours, 2, '0', STR_PAD_LEFT ),
			'mm'  => str_pad( $minutes, 2, '0', STR_PAD_LEFT ),
			'ss'  => str_pad( $seconds, 2, '0', STR_PAD_LEFT ),
		);

	}

}

if ( defined( 'YITH_WCPO_PREMIUM' ) && YITH_WCPO_PREMIUM ) {

	add_action( 'ywpc_countdown_expiration', 'ywpc_preorder_expiration', 10, 2 );

	function ywpc_preorder_expiration( $product, $id ) {

		$auto_for_sale = get_option( 'yith_wcpo_enable_pre_order_purchasable' );

		if ( 'yes' == $auto_for_sale ) {

			$pre_order_product = new YITH_Pre_Order_Product( $id );

			if ( $pre_order_product->get_pre_order_status() == 'yes' ) {

				$pre_order_product->clear_pre_order_product();

				wc_delete_product_transients( $id );

				if ( $product->is_type( 'variable' ) ) {

					$variation_object = wc_get_product( $id );

					$args = array(
						'_ywpc_sale_price_dates_from' => '',
						'_ywpc_sale_price_dates_to'   => '',
						'_ywpo_variation'             => ''
					);

					yit_save_prop( $variation_object, $args );

				} else {

					yit_save_prop( $product, '_ywpc_enabled', 'no' );

				}

			}

		}

	}

}

if ( ! function_exists( 'ywpc_get_minified' ) ) {

	/**
	 * Get minified file suffix
	 *
	 * @since   1.0.0
	 * @return  string
	 * @author  Alberto Ruggiero
	 */
	function ywpc_get_minified() {

		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	}

}
if ( ! function_exists( 'ywpc_get_product_args' ) ) {

	/**
	 * Get args for timer and sale bar
	 *
	 * @since   1.0.0
	 *
	 * @param   $prod_id
	 * @param   $extra_class
	 *
	 * @return  array
	 * @author  Alberto Ruggiero
	 */
	function ywpc_get_product_args( $prod_id, $extra_class = '' ) {

		global $post;

		$result = array(
			'items' => array(),
			'class' => $extra_class
		);

		$product_id = ( $prod_id != '' ) ? $prod_id : $post->ID;

		global $sitepress;
		$has_wpml = ! empty( $sitepress ) ? true : false;

		if ( $has_wpml && apply_filters( 'ywpc_wpml_use_default_language_settings', false ) ) {
			$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
		}

		$product          = wc_get_product( $product_id );
		$has_ywpc         = yit_get_prop( $product, '_ywpc_enabled', true );
		$current_time     = strtotime( current_time( "Y-m-d H:i:s" ) );
		$before_sale      = get_option( 'ywpc_before_sale_start' );
		$end_sale         = get_option( 'ywpc_end_sale' );
		$end_sale_summary = get_option( 'ywpc_end_sale_summary' );

		if ( $has_ywpc == 'yes' ) {

			$variation_global = yit_get_prop( $product, '_ywpc_variations_global_countdown' );

			if ( ( ! $product->is_type( 'variable' ) ) || ( $product->is_type( 'variable' ) && $variation_global == 'yes' ) ) {

				$stock_status = yit_get_prop( $product, '_stock_status' );
				$is_preorder  = yit_get_prop( $product, '_ywpo_preorder' );

				if ( ( $stock_status != 'outofstock' ) || ( $stock_status == 'outofstock' && $is_preorder == 'yes' ) ) {

					$expired = false;

					$sale_start   = yit_get_prop( $product, '_ywpc_sale_price_dates_from' );
					$sale_end     = yit_get_prop( $product, '_ywpc_sale_price_dates_to' );
					$discount_qty = yit_get_prop( $product, '_ywpc_discount_qty' );
					$sold_qty     = yit_get_prop( $product, '_ywpc_sold_qty' );

					$result['items'][ $product_id ]['show_bar'] = 'hide';
					$result['items'][ $product_id ]['expired']  = 'valid';

					if ( empty( $sold_qty ) ) {
						$sold_qty = 0;
					}

					if ( $sold_qty < $discount_qty ) {

						$result['items'][ $product_id ]['show_bar']     = 'show';
						$result['items'][ $product_id ]['sold_qty']     = $sold_qty;
						$result['items'][ $product_id ]['discount_qty'] = $discount_qty;
						$result['items'][ $product_id ]['percent']      = intval( $sold_qty / $discount_qty * 100 );

					} else {

						if ( $sold_qty != 0 && $discount_qty != 0 ) {

							$result['items'][ $product_id ]['show_bar'] = 'hide';

							$expired = true;

						}

					}

					if ( ! empty( $sale_end ) && ! empty( $sale_start ) ) {

						if ( $current_time < $sale_start && $before_sale == 'yes' ) {

							$result['items'][ $product_id ]['before']   = true;
							$result['items'][ $product_id ]['end_date'] = $sale_start;

						} elseif ( $current_time >= $sale_start && $current_time <= $sale_end ) {

							$result['items'][ $product_id ]['before']   = false;
							$result['items'][ $product_id ]['end_date'] = $sale_end;

						} elseif ( $current_time > $sale_end ) {

							$expired = true;

						}

					}

					if ( $expired ) {

						$result['items'][ $product_id ]['show_bar'] = 'hide';

						if ( $end_sale == 'disable' && $end_sale_summary == 'yes' && $discount_qty > 0 ) {

							$result['items'][ $product_id ]['show_bar']     = 'show';
							$result['items'][ $product_id ]['sold_qty']     = $sold_qty;
							$result['items'][ $product_id ]['discount_qty'] = $discount_qty;
							$result['items'][ $product_id ]['percent']      = intval( $sold_qty / $discount_qty * 100 );

						}

						$result['items'][ $product_id ]['expired'] = 'expired';

						do_action( 'ywpc_countdown_expiration', $product, $product_id );

					}

				}

			} else {
				$product_variables = $product->get_available_variations();

				if ( count( array_filter( $product_variables ) ) > 0 ) {
					$product_variables    = array_filter( $product_variables );
					$result['active_var'] = 0;
					$result['variable']   = true;
					$default_atts         = yit_get_prop( $product, '_default_attributes' );
					$variation_points     = array();

					foreach ( $product_variables as $product_variable ) {

						$variation    = wc_get_product( $product_variable['variation_id'] );
						$stock_status = yit_get_prop( $variation, '_stock_status' );
						$is_preorder  = yit_get_prop( $variation, '_ywpo_preorder' );

						if ( ( $stock_status != 'outofstock' ) || ( $stock_status == 'outofstock' && $is_preorder == 'yes' ) ) {

							$expired = false;

							$sale_start   = yit_get_prop( $variation, '_ywpc_sale_price_dates_from' );
							$sale_end     = yit_get_prop( $variation, '_ywpc_sale_price_dates_to' );
							$discount_qty = yit_get_prop( $variation, '_ywpc_discount_qty' );
							$sold_qty     = yit_get_prop( $variation, '_ywpc_sold_qty' );

							$discount_qty = ( empty( $discount_qty ) ? 0 : $discount_qty );
							$sold_qty     = ( empty( $sold_qty ) ? 0 : $sold_qty );

							$result['items'][ $product_variable['variation_id'] ]['show_bar'] = 'hide';
							$result['items'][ $product_variable['variation_id'] ]['expired']  = 'valid';

							if ( $sold_qty < $discount_qty ) {

								$result['items'][ $product_variable['variation_id'] ]['show_bar']     = 'show';
								$result['items'][ $product_variable['variation_id'] ]['sold_qty']     = $sold_qty;
								$result['items'][ $product_variable['variation_id'] ]['discount_qty'] = $discount_qty;
								$result['items'][ $product_variable['variation_id'] ]['percent']      = ( $sold_qty && $discount_qty ) ? intval( $sold_qty / $discount_qty * 100 ) : 0;

							} else {

								if ( $sold_qty != 0 && $discount_qty != 0 ) {

									$result['items'][ $product_variable['variation_id'] ]['show_bar'] = 'hide';

									$expired = true;

								}

							}

							if ( ! empty( $sale_end ) && ! empty( $sale_start ) ) {

								if ( $current_time < $sale_start && $before_sale == 'yes' ) {

									$result['items'][ $product_variable['variation_id'] ]['before']   = true;
									$result['items'][ $product_variable['variation_id'] ]['end_date'] = $sale_start;

								} elseif ( $current_time >= $sale_start && $current_time <= $sale_end ) {

									$result['items'][ $product_variable['variation_id'] ]['before']   = false;
									$result['items'][ $product_variable['variation_id'] ]['end_date'] = $sale_end;

								} elseif ( $current_time > $sale_end ) {

									$expired = true;

								}

							}

							if ( $expired ) {

								$result['items'][ $product_variable['variation_id'] ]['show_bar'] = 'hide';

								if ( $end_sale == 'disable' && $end_sale_summary == 'yes' ) {

									$result['items'][ $product_variable['variation_id'] ]['show_bar']     = 'show';
									$result['items'][ $product_variable['variation_id'] ]['sold_qty']     = $sold_qty;
									$result['items'][ $product_variable['variation_id'] ]['discount_qty'] = $discount_qty;
									$result['items'][ $product_variable['variation_id'] ]['percent']      = ( $sold_qty && $discount_qty ) ? intval( $sold_qty / $discount_qty * 100 ) : 0;

								}

								$result['items'][ $product_variable['variation_id'] ]['expired'] = 'expired';

								do_action( 'ywpc_countdown_expiration', $product, $product_variable['variation_id'] );

							}

							if ( $default_atts ) {

								foreach ( $default_atts as $key => $val ) {

									$variation_points[ $product_variable['variation_id'] ] = 0;

									if ( isset( $product_variable['attributes'][ 'attribute_' . $key ] ) && $product_variable['attributes'][ 'attribute_' . $key ] != '' ) {

										if ( $product_variable['attributes'][ 'attribute_' . $key ] == $val ) {

											$variation_points[ $product_variable['variation_id'] ] ++;

										}

									}

								}

							}

						}

					}

					if ( ! empty( $variation_points ) ) {

						$result['active_var'] = max( $variation_points ) > 0 ? array_search( max( $variation_points ), $variation_points ) : 0;

					}

				}

			}

		}

		return $result;

	}

}

if ( ! function_exists( 'ywpc_get_template' ) ) {

	/**
	 * Get template
	 *
	 * @since   1.0.0
	 *
	 * @param   $product_id
	 * @param   $type
	 * @param   $shortcode
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	function ywpc_get_template( $product_id, $type, $shortcode = false ) {

		$what_show = get_option( 'ywpc_what_show' );
		$args      = ywpc_get_product_args( $product_id );

		if ( $shortcode ) {
			$args['shortcode'] = true;
		}

		if ( $what_show == 'timer' || $what_show == 'both' ) {
			wc_get_template( '/frontend/' . $type . '-timer.php', array( 'args' => $args ), '', YWPC_TEMPLATE_PATH );
		}

		if ( $what_show == 'bar' || $what_show == 'both' ) {
			wc_get_template( '/frontend/' . $type . '-bar.php', array( 'args' => $args ), '', YWPC_TEMPLATE_PATH );
		}
	}

}
