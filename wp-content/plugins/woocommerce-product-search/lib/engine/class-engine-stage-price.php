<?php
/**
 * class-engine-stage-price.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class Engine_Stage_Price extends Engine_Stage {

	const CACHE_GROUP = 'ixwps_pretium';

	const CACHE_LIFETIME = Cache::MONTH;

	protected $stage_id = 'price';

	private $min_price = null;

	private $max_price = null;

	public function __construct( $args = array() ) {
		$args = apply_filters( 'woocommerce_product_search_engine_stage_parameters', $args, $this );
		parent::__construct( $args );
		if ( is_array( $args ) && count( $args ) > 0 ) {
			$params = array();
			foreach ( $args as $key => $value ) {
				$set_param = true;
				switch ( $key ) {
					case 'min_price':
					case 'max_price':
						if ( is_string( $value ) || is_numeric( $value ) ) {
							$value = sanitize_text_field( trim( '' . $value ) );
							if ( strlen( $value ) === 0 ) {
								$value = null;
							} else {
								$value = $this->to_float( $value );
							}
						} else {
							$value = null;
						}
						break;
					default:
						$set_param = false;
				}
				if ( $set_param ) {
					$params[$key] = $value;
				}
			}
			foreach ( $params as $key => $value ) {
				$this->$key = $value;
			}
		}

		if ( $this->min_price !== null && $this->min_price <= 0 ) {
			$this->min_price = null;
		}
		if ( $this->max_price !== null && $this->max_price <= 0 ) {
			$this->max_price = null;
		}
		if ( $this->min_price !== null && $this->max_price !== null && $this->max_price < $this->min_price ) {
			$this->max_price = null;
		}
		$this->min_max_price_adjust( $this->min_price, $this->max_price );
	}

	public function get_parameters() {
		return array_merge(
			array(
				'min_price' => $this->min_price,
				'max_price' => $this->max_price
			),
			parent::get_parameters()
		);
	}

	public function get_matching_ids( &$ids ) {

		global $wpdb;

		$this->timer->start();

		$cache_context = $this->get_cache_context();
		$cache_key = $this->get_cache_key( $cache_context );

		$cache = Cache::get_instance();
		$ids = $cache->get( $cache_key, self::CACHE_GROUP );
		if ( is_array( $ids ) ) {
			$this->count = count( $ids );
			$this->is_cache_hit = true;
			$this->timer->stop();
			$this->timer->log( 'verbose' );
			return;
		}
		$this->is_cache_hit = false;

		$ids = array();

		$min_price = $this->min_price;
		$max_price = $this->max_price;

		if ( $min_price !== null || $max_price !== null ) {

			if (
				wc_tax_enabled() &&
				'incl' === get_option( 'woocommerce_tax_display_shop' ) &&
				!wc_prices_include_tax()
			) {

				$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' );
				$tax_rates = \WC_Tax::get_rates( $tax_class );
				if ( $tax_rates ) {
					if ( $min_price !== null ) {
						$min_price -= \WC_Tax::get_tax_total( \WC_Tax::calc_inclusive_tax( $min_price, $tax_rates ) );
						if ( $min_price < 0.0 ) {
							$min_price = 0.0;
						}
					}
					if ( $max_price !== null ) {
						$max_price -= \WC_Tax::get_tax_total( \WC_Tax::calc_inclusive_tax( $max_price, $tax_rates ) );
						if ( $max_price < 0.0 ) {
							$max_price = 0.0;
						}
					}
				}
			}

			global $woocommerce_wpml;
			if (
				isset( $woocommerce_wpml ) &&
				class_exists( '\woocommerce_wpml' ) &&
				( $woocommerce_wpml instanceof \woocommerce_wpml )
			) {
				$multi_currency = $woocommerce_wpml->get_multi_currency();
				if (
					!empty( $multi_currency->prices ) &&
					class_exists( '\WCML_Multi_Currency_Prices' ) &&
					( $multi_currency->prices instanceof \WCML_Multi_Currency_Prices )
				) {
					if ( method_exists( $multi_currency, 'get_client_currency' ) ) {

						$currency = $multi_currency->get_client_currency();

						$base_currency = get_option( 'woocommerce_currency' );

						if ( $currency !== $base_currency ) {
							if ( $min_price !== null ) {
								$min_price = $multi_currency->prices->convert_price_amount_by_currencies( $min_price, $currency, $base_currency );
							}
							if ( $max_price !== null ) {
								$max_price = $multi_currency->prices->convert_price_amount_by_currencies( $max_price, $currency, $base_currency );
							}
						}

					}
				}
			}

			if ( $min_price !== null && $max_price === null ) {

				if ( !$this->variations ) {
					$query = sprintf(
						"SELECT wc_product_meta_lookup.product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup " .
						"LEFT JOIN $wpdb->posts p ON p.ID = wc_product_meta_lookup.product_id " .
						"WHERE max_price >= %s " .
						"AND p.post_type != 'product_variation'",
						floatval( $min_price )

					);
				} else {
					$query = sprintf(
						"SELECT wc_product_meta_lookup.product_id, p.post_parent FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup " .
						"LEFT JOIN $wpdb->posts p ON p.ID = wc_product_meta_lookup.product_id " .
						"WHERE max_price >= %s",
						floatval( $min_price )

					);
				}
			} else if ( $min_price === null && $max_price !== null ) {

				if ( !$this->variations ) {
					$query = sprintf(
						"SELECT wc_product_meta_lookup.product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup " .
						"LEFT JOIN $wpdb->posts p ON p.ID = wc_product_meta_lookup.product_id " .
						"WHERE min_price <= %s " .
						"AND p.post_type != 'product_variation'",
						floatval( $max_price )

					);
				} else {
					$query = sprintf(
						"SELECT wc_product_meta_lookup.product_id, p.post_parent FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup " .
						"LEFT JOIN $wpdb->posts p ON p.ID = wc_product_meta_lookup.product_id " .
						"WHERE min_price <= %s",
						floatval( $max_price )

					);
				}
			} else {

				if ( !$this->variations ) {
					$query = sprintf(
						"SELECT wc_product_meta_lookup.product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup " .
						"LEFT JOIN $wpdb->posts p ON p.ID = wc_product_meta_lookup.product_id " .
						"WHERE max_price >= %s AND min_price <= %s " .
						"AND p.post_type != 'product_variation'",
						floatval( $min_price ),
						floatval( $max_price )

					);
				} else {
					$query = sprintf(
						"SELECT wc_product_meta_lookup.product_id, p.post_parent FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup " .
						"LEFT JOIN $wpdb->posts p ON p.ID = wc_product_meta_lookup.product_id " .
						"WHERE max_price >= %s AND min_price <= %s",
						floatval( $min_price ),
						floatval( $max_price )

					);
				}
			}

			if ( $this->limit !== null ) {
				$query .= ' LIMIT ' . intval( $this->limit );
			}

			$results = $wpdb->get_results( $query );
			if ( is_array( $results ) ) {
				foreach ( $results as $result ) {
					$is_variation = !empty( $result->post_parent );
					if ( $is_variation ) {
						$ids[] = (int) $result->post_parent;
					}
					if ( !$is_variation || $this->variations ) {
						$ids[] = (int) $result->product_id;
					}
				}

				if ( $this->variations ) {
					Tools::unique( $ids );
				}
			}
		}

		$this->count = count( $ids );
		$this->is_cache_write = $cache->set( $cache_key, $ids, self::CACHE_GROUP, $this->get_cache_lifetime() );

		$extra = sprintf(
			'%1$s %2$s %3$s',
			$min_price !== null ? $min_price : '',
			html_entity_decode( '&hellip;' ),
			$max_price !== null ? $max_price : ''
		);

		$this->timer->stop();
		$this->timer->log( 'verbose', $extra );
	}

	/**
	 * Float conversion.
	 *
	 * @param string|float|null $x to convert
	 *
	 * @return float|null converted or null
	 */
	public function to_float( $x ) {

		if ( $x !== null && !is_float( $x ) && is_string( $x ) ) {
			$locale = localeconv();
			$decimal_characters = array_unique( array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], '.', ',' ) );
			$x = str_replace( $decimal_characters, '.', trim( $x ) );
			$x = preg_replace( '/[^0-9\.,-]/', '', $x );
			$i = strrpos( $x, '.' );
			if ( $i !== false ) {
				$x = ( $i > 0 ? str_replace( '.', '', substr( $x, 0, $i ) ) : '' ) . '.' . ( $i < strlen( $x ) ? str_replace( '.', '', substr( $x, $i + 1 ) ) : '' );
			}
			if ( strlen( $x ) > 0 ) {
				$x = floatval( $x );
			} else {
				$x = null;
			}
		}
		return $x;
	}

	/**
	 * Min-max adjustment
	 *
	 * @param $min_price float
	 * @param $max_price float
	 */
	public function min_max_price_adjust( &$min_price, &$max_price ) {

		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_classes = array_merge( array( '' ), \WC_Tax::get_tax_classes() );
			$min = $min_price;
			$max = $max_price;
			foreach ( $tax_classes as $tax_class ) {
				if ( $tax_rates = \WC_Tax::get_rates( $tax_class ) ) {
					if ( $min !== null ) {
						$min = $min_price - \WC_Tax::get_tax_total( \WC_Tax::calc_inclusive_tax( $min_price, $tax_rates ) );
						$min = round( $min, wc_get_price_decimals(), PHP_ROUND_HALF_DOWN );
					}
					if ( $max !== null ) {
						$max = $max_price - \WC_Tax::get_tax_total( \WC_Tax::calc_inclusive_tax( $max_price, $tax_rates ) );
						$max = round( $max, wc_get_price_decimals(), PHP_ROUND_HALF_UP );
					}
				}
			}
			$decimals = apply_filters( 'woocommerce_product_search_service_min_max_price_adjust_decimals', \WooCommerce_Product_Search_Filter_Price::DECIMALS );
			if ( !is_numeric( $decimals ) ) {
				$decimals = \WooCommerce_Product_Search_Filter_Price::DECIMALS;
			}
			$decimals = max( 0, intval( $decimals ) );
			$factor = pow( 10, $decimals );
			if ( $min !== null && $min !== '' ) {
				$min = floor( $min * $factor ) / $factor;
			}
			if ( $max !== null && $max !== '' ) {
				$max = ceil( $max * $factor ) / $factor;
			}
			$min_price = $min;
			$max_price = $max;
		}
	}

}
