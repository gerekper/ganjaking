<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Rich_Snippet;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class WooCommerce_Model.
 *
 * Recognizes the WooCommerce plugin and provides new fields.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.2.0
 */
final class WooCommerce_Model {

	/**
	 * @param $values
	 *
	 * @return mixed
	 */
	public static function internal_subselect( $values ) {

		if ( ! function_exists( 'WC' ) ) {
			return $values;
		}

		$number_values = [];

		$values['http://schema.org/AggregateRating'][] = array(
			'id'     => 'woocommerce_rating',
			'label'  => esc_html_x( 'WooCommerce: Product Rating', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'rating' ),
		);

		$values['http://schema.org/Rating'][] = array(
			'id'     => 'woocommerce_rating',
			'label'  => esc_html_x( 'WooCommerce: Product Rating', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'rating' ),
		);

		$values['http://schema.org/AggregateRating'][] = array(
			'id'     => 'woocommerce_review_rating',
			'label'  => esc_html_x( 'WooCommerce: Product Review Rating', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'review_rating' ),
		);

		$values['http://schema.org/Rating'][] = array(
			'id'     => 'woocommerce_review_rating',
			'label'  => esc_html_x( 'WooCommerce: Product Review Rating', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'review_rating' ),
		);

		$values['http://schema.org/Text'][] = array(
			'id'     => 'woocommerce_sku',
			'label'  => esc_html_x( 'WooCommerce: Stock Keeping Unit', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'sku' ),
		);

		$values['http://schema.org/Text'][] = array(
			'id'     => 'woocommerce_currency_code',
			'label'  => esc_html_x( 'WooCommerce: Currency Code', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'currency_code' ),
		);

		$values['http://schema.org/Offer'][] = array(
			'id'     => 'woocommerce_offers',
			'label'  => esc_html_x( 'WooCommerce: Offers', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'offers' ),
		);

		$number_values[] = array(
			'id'     => 'woocommerce_height',
			'label'  => esc_html_x( 'WooCommerce: Product Height', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'height' ),
		);

		$number_values[] = array(
			'id'     => 'woocommerce_width',
			'label'  => esc_html_x( 'WooCommerce: Product Width', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'width' ),
		);

		$number_values[] = array(
			'id'     => 'woocommerce_length',
			'label'  => esc_html_x( 'WooCommerce: Product Length', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'length' ),
		);

		$number_values[] = array(
			'id'     => 'woocommerce_weight',
			'label'  => esc_html_x( 'WooCommerce: Product Weight', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'weight' ),
		);

		$number_values[] = array(
			'id'     => 'textfield_woocommerce_product_attribute',
			'label'  => esc_html_x( 'WooCommerce: Product attribute', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'attribute' ),
		);

		$number_values[] = $values['http://schema.org/URL'][] = array(
			'id'     => 'textfield_woocommerce_product_attribute',
			'label'  => esc_html_x( 'WooCommerce: Product attribute', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'attribute' ),
		);

		$values['http://schema.org/Review'][] = array(
			'id'     => 'woocommerce_reviews',
			'label'  => esc_html_x( 'WooCommerce: Product reviews', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'reviews' ),
		);

		$values['http://schema.org/Number'][] = array(
			'id'     => 'woocommerce_price',
			'label'  => esc_html_x( 'WooCommerce: Product Price', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'price' ),
		);

		$values['http://schema.org/Number'][] = array(
			'id'     => 'woocommerce_sales_price',
			'label'  => esc_html_x( 'WooCommerce: Product Sales Price', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'sales_price' ),
		);

		$values['http://schema.org/ItemAvailability'][] = array(
			'id'     => 'woocommerce_sales_price',
			'label'  => esc_html_x( 'WooCommerce: Availability', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'availability' ),
		);

		$values['http://schema.org/Date'][] = array(
			'id'     => 'woocommerce_sales_end_date',
			'label'  => esc_html_x( 'WooCommerce: Sales End Date', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'sales_end_date' ),
		);

		$values['http://schema.org/Date'][] = array(
			'id'     => 'woocommerce_sales_start_date',
			'label'  => esc_html_x( 'WooCommerce: Sales Start Date', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'sales_start_date' ),
		);

		$values['http://schema.org/DateTime'][] = array(
			'id'     => 'woocommerce_sales_end_datetime',
			'label'  => esc_html_x( 'WooCommerce: Sales End Date and Time', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'sales_end_datetime' ),
		);

		$values['http://schema.org/DateTime'][] = array(
			'id'     => 'woocommerce_sales_start_date',
			'label'  => esc_html_x( 'WooCommerce: Sales Start Date and Time', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'sales_start_datetime' ),
		);

		$number_values[] = array(
			'id'     => 'woocommerce_stock_number',
			'label'  => esc_html_x( 'WooCommerce: Stock Number', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'stock_number' ),
		);

		$values['http://schema.org/Text'][] = array(
			'id'     => 'woocommerce_weight_unit',
			'label'  => esc_html_x( 'WooCommerce: Weight Unit', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'weight_unit' ),
		);

		$values['http://schema.org/Text'][] = $values['http://schema.org/Thing'][] = array(
			'id'     => 'textfield_woocommerce_product_attribute',
			'label'  => esc_html_x( 'WooCommerce: Product attribute', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( '\wpbuddy\rich_snippets\pro\WooCommerce_Model', 'attribute' ),
		);

		$values['http://schema.org/QuantitativeValue'] = array_merge( $values['http://schema.org/QuantitativeValue'], $number_values );
		$values['http://schema.org/Number']            = array_merge( $values['http://schema.org/Number'], $number_values );
		$values['http://schema.org/Integer']           = array_merge( $values['http://schema.org/Integer'], $number_values );

		return $values;
	}


	/**
	 * Returns the value of the current rating.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return string
	 * @since 2.2.0
	 *
	 */
	public static function rating( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		$product = wc_get_product( $meta_info['current_post_id'] );

		if ( ! $product instanceof \WC_Product ) {
			$rating_value = 0;
			$rating_count = 0;
		} else {
			$rating_value = floatval( $product->get_average_rating( 'raw' ) );
			$rating_count = floatval( $product->get_rating_count( 'raw' ) );
		}

		# force SNIP to not include aggregateRating at all because there are no ratings
		# This is because rating_count cannot be zero for Googles Structured Data Tester
		if ( $rating_count <= 0 ) {
			return '';
		}

		$rating_snippet       = new Rich_Snippet();
		$rating_snippet->type = 'AggregateRating';


		$rating_snippet->set_props( array(
			array(
				'name'  => 'ratingCount',
				'value' => $rating_count,
			),
			array(
				'name'  => 'bestRating',
				'value' => 5,
			),
			array(
				'name'  => 'ratingValue',
				'value' => $rating_value,
			),
			array(
				'name'  => 'worstRating',
				'value' => $rating_count <= 0 ? 0 : 1, # worstRating must be 0 if ratingCount is 0
			),
		) );

		$rating_snippet->prepare_for_output();

		return $rating_snippet;
	}


	/**
	 * Returns the value of the current review rating.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return string
	 * @since 2.6.0
	 *
	 */
	public static function review_rating( $val, Rich_Snippet $rich_snippet, array $meta_info ) {
		$product = wc_get_product( $meta_info['current_post_id'] );

		if ( ! $product instanceof \WC_Product ) {
			$rating_value = 0;
			$review_count = 0;
		} else {
			$rating_value = floatval( $product->get_average_rating( 'raw' ) );
			$review_count = floatval( $product->get_review_count( 'raw' ) );
		}

		# force SNIP to not include aggregateRating at all because there are no ratings
		# This is because review_count cannot be zero for Googles Structured Data Tester
		if ( $review_count <= 0 ) {
			return '';
		}

		$rating_snippet       = new Rich_Snippet();
		$rating_snippet->type = 'AggregateRating';

		$rating_snippet->set_props( array(
			array(
				'name'  => 'reviewCount',
				'value' => $review_count,
			),
			array(
				'name'  => 'bestRating',
				'value' => 5,
			),
			array(
				'name'  => 'ratingValue',
				'value' => $rating_value,
			),
			array(
				'name'  => 'worstRating',
				'value' => $review_count <= 0 ? 0 : 1, # worstRating must be 0 if ratingCount is 0
			),
		) );

		$rating_snippet->prepare_for_output();

		return $rating_snippet;
	}


	/**
	 * Returns the value of the current SKU.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return string
	 * @since 2.2.0
	 *
	 */
	public static function sku( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		$product = wc_get_product( $meta_info['current_post_id'] );

		if ( $product instanceof \WC_Product || is_subclass_of( $product, 'WC_Product', false ) ) {
			return (string) $product->get_sku( 'raw' );
		}

		return '';
	}


	/**
	 * Returns the weight unit.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return string
	 * @since 2.11.0
	 *
	 */
	public static function weight_unit( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		return (string) get_option( 'woocommerce_weight_unit' );
	}


	/**
	 * Returns the products end sales date.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return string
	 * @since 2.11.0
	 *
	 */
	public static function sales_end_date( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		$product = wc_get_product( $meta_info['current_post_id'] );

		$not_on_sale_time = (string) date_i18n( 'Y-m-d', strtotime( 'NOW + 1 year' ) );

		if ( $product instanceof \WC_Product || is_subclass_of( $product, 'WC_Product', false ) ) {
			if ( $product->is_on_sale() && $product->get_date_on_sale_to() ) {
				return date_i18n( 'Y-m-d', $product->get_date_on_sale_to()->getTimestamp() );
			}
		}

		return $not_on_sale_time;
	}


	/**
	 * Returns the products end sales datetime.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return string
	 * @since 2.11.0
	 *
	 */
	public static function sales_end_datetime( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		return date_i18n( 'c', strtotime( self::sales_end_date( $val, $rich_snippet, $meta_info ) ) );
	}


	/**
	 * Returns the products start sales date.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return string
	 * @since 2.11.0
	 *
	 */
	public static function sales_start_date( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		$product = wc_get_product( $meta_info['current_post_id'] );

		$not_on_sale_time = (string) date_i18n( 'Y-m-d', strtotime( 'NOW - 1 DAY' ) );

		if ( $product instanceof \WC_Product || is_subclass_of( $product, 'WC_Product', false ) ) {
			if ( $product->is_on_sale() && $product->get_date_on_sale_from() ) {
				return date_i18n( 'Y-m-d', $product->get_date_on_sale_from()->getTimestamp() );
			}
		}

		return $not_on_sale_time;
	}


	/**
	 * Returns the products start sales date and time.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return string
	 * @since 2.11.0
	 *
	 */
	public static function sales_start_datetime( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		return date_i18n( 'c', strtotime( self::sales_start_date( $val, $rich_snippet, $meta_info ) ) );
	}


	/**
	 * Returns the value of the current product price
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return float|string
	 * @since 2.11.0
	 *
	 */
	public static function price( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		$product = wc_get_product( $meta_info['current_post_id'] );

		if ( $product instanceof \WC_Product || is_subclass_of( $product, 'WC_Product', false ) ) {
			return number_format( floatval( $product->get_price( 'edit' ) ), 2, '.', '' );
		}

		return '';
	}


	/**
	 * Returns the value of the current products availability.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return float|string
	 * @since 2.11.0
	 */
	public static function availability( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		return self::get_availability( intval( $meta_info['current_post_id'] ) );
	}


	/**
	 * @param int $product_id
	 *
	 * @return string
	 * @since 2.15.2
	 */
	public static function get_availability( int $product_id ): string {
		$product = wc_get_product( $product_id );

		if ( $product instanceof \WC_Product || is_subclass_of( $product, 'WC_Product', false ) ) {

			switch ( $product->get_stock_status() ) {
				case 'onbackorder':
					return 'https://schema.org/PreOrder';
				case 'instock':
					$stock_quantity = $product->get_stock_quantity( 'edit' );

					if ( function_exists( 'wc_get_low_stock_amount' ) ) {
						$low_stock_amount = intval( wc_get_low_stock_amount( $product ) );

						if ( $stock_quantity <= $low_stock_amount ) {
							if ( (bool) get_option( 'wpb_rs/setting/wc_availability_use_preorder', false ) ) {
								return 'https://schema.org/PreOrder';
							} else {
								return 'https://schema.org/LimitedAvailability';
							}
						}
					}

					return 'http://schema.org/InStock';
				case 'outofstock':
					return 'http://schema.org/OutOfStock';
			}
		}

		return '';
	}

	/**
	 * Returns the value of the current product sales price.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return float|string
	 * @since 2.11.0
	 *
	 */
	public static function sales_price( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		$product = wc_get_product( $meta_info['current_post_id'] );

		if ( $product instanceof \WC_Product || is_subclass_of( $product, 'WC_Product', false ) ) {
			return number_format( floatval( $product->get_sale_price( 'edit' ) ), 2, '.', '' );
		}

		return '';
	}


	/**
	 * Returns the value of the stock number.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return \stdClass
	 * @since 2.11.0
	 *
	 */
	public static function stock_number( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		$product = wc_get_product( $meta_info['current_post_id'] );

		if ( $product instanceof \WC_Product || is_subclass_of( $product, 'WC_Product', false ) ) {
			$stock = $product->get_manage_stock( 'edit' );

			$obj               = new \stdClass();
			$obj->{'@context'} = 'http://schema.org';
			$obj->{'@type'}    = 'Offer';
			$obj->value        = $stock;

			return $obj;
		}

		return new \stdClass();
	}


	/**
	 * Returns the currency code.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return float|string
	 * @since 2.11.0
	 *
	 */
	public static function currency_code( $val, Rich_Snippet $rich_snippet, array $meta_info ) {
		return get_woocommerce_currency();
	}


	/**
	 * Returns the offer for a WooCommerce product.
	 *
	 * @param int $post_id
	 *
	 * @return \stdClass
	 * @since 2.2.0
	 *
	 */
	private static function offer( $post_id ) {

		/**
		 * @var \WC_Product_Variation
		 */
		$product = wc_get_product( $post_id );

		if ( ! ( $product instanceof \WC_Product || is_subclass_of( $product, 'WC_Product', false ) ) ) {
			return new \stdClass();
		}

		$obj                = new \stdClass();
		$obj->{'@context'}  = 'http://schema.org';
		$obj->{'@type'}     = 'Offer';
		$obj->availability  = self::get_availability( intval( $post_id ) );
		$obj->priceCurrency = get_woocommerce_currency();
		$obj->price         = wc_format_decimal( $product->get_price(), wc_get_price_decimals() );
		$obj->url           = $product->get_permalink();

		$sale_date = $product->get_date_on_sale_to( 'raw' );

		if ( ! $sale_date instanceof \DateTime ) {
			# If there is no sales date create a fake date to avoid Googles warnings
			# @see https://rich-snippets.io/offers-pricevaliduntil-recommended/
			$obj->priceValidUntil = (string) date_i18n( 'c', strtotime( 'NOW + 1 year' ) );
		} else {
			$obj->priceValidUntil = $sale_date->date( 'c' );
		}

		return $obj;
	}

	/**
	 * Returns a snippet of all offers.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return \stdClass
	 *
	 * @since 2.2.0
	 */
	public static function offers( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		$product = wc_get_product( $meta_info['current_post_id'] );

		if ( $product instanceof \WC_Product_Variable ) {
			$lowest  = $product->get_variation_price( 'min', false );
			$highest = $product->get_variation_price( 'max', false );

			if ( $lowest === $highest ) {
				return self::offer( $meta_info['current_post_id'] );
			} else {
				$obj                = new \stdClass();
				$obj->{'@context'}  = 'http://schema.org';
				$obj->{'@type'}     = 'AggregateOffer';
				$obj->lowPrice      = wc_format_decimal( $lowest, wc_get_price_decimals() );
				$obj->highPrice     = wc_format_decimal( $highest, wc_get_price_decimals() );
				$obj->priceCurrency = get_woocommerce_currency();
				$obj->offerCount    = count( $product->get_children() );
				$obj->url           = $product->get_permalink();

				return $obj;
			}

		} elseif ( $product instanceof \WC_Product ) {
			return self::offer( $meta_info['current_post_id'] );
		}

		return new \stdClass();
	}


	/**
	 * Returns the height of a product.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return \stdClass
	 * @since 2.2.0
	 *
	 */
	public static function height( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		return self::get_product_quantitive_snippet( $meta_info['current_post_id'], 'height' );
	}


	/**
	 * Returns the width of a product.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return \stdClass
	 * @since 2.2.0
	 *
	 */
	public static function width( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		return self::get_product_quantitive_snippet( $meta_info['current_post_id'], 'width' );
	}


	/**
	 * Returns the length of a product.
	 *
	 * @param mixed $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return \stdClass
	 * @since 2.19.0
	 */
	public static function length( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		return self::get_product_quantitive_snippet( $meta_info['current_post_id'], 'length' );
	}


	/**
	 * Get WooCommerce product dimension as a snippet.
	 *
	 * @param int $product_id
	 * @param string $prop width|height|weight
	 *
	 * since 2.2.0
	 *
	 * @return \stdClass
	 */
	private static function get_product_quantitive_snippet( $product_id, $prop ) {

		$product = wc_get_product( $product_id );

		$item = new \stdClass();

		if ( ! is_subclass_of( $product, 'WC_Data', false ) ) {
			return $item;
		}

		$item->{'@context'} = 'http://schema.org';
		$item->{'@type'}    = 'QuantitativeValue';
		$item->value        = method_exists( $product, 'get_' . $prop ) ? $product->{'get_' . $prop}() : '';
		$item->value        = floatval( $item->value );
		$item->unitCode     = 'weight' === $prop ? get_option( 'woocommerce_weight_unit' ) : get_option( 'woocommerce_dimension_unit' );

		return $item;
	}


	/**
	 * Returns the eight of a product.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return \stdClass
	 * @since 2.2.0
	 *
	 */
	public static function weight( $val, Rich_Snippet $rich_snippet, array $meta_info ) {

		return self::get_product_quantitive_snippet( $meta_info['current_post_id'], 'weight' );
	}


	/**
	 * Reads a product attribute from WooCommerce (serialized data).
	 *
	 * @param              $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return string
	 * @since 2.5.0
	 *
	 */
	public static function attribute( $val, Rich_Snippet $rich_snippet, array $meta_info ) {
		if ( ! function_exists( '\wc_get_product' ) ) {
			return '';
		}

		if ( ! is_scalar( $val ) ) {
			return '';
		}

		if ( empty( $val ) ) {
			return '';
		}

		$product = \wc_get_product( $meta_info['current_post_id'] );

		if ( $product instanceof \WC_Product || is_subclass_of( $product, 'WC_Product', false ) ) {
			return $product->get_attribute( $val );
		}

		return '';
	}


	/**
	 * Outputs product reviews from WooCommerce.
	 *
	 * @param              $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 *
	 * @return \stdClass[]
	 * @since 2.7.0
	 *
	 */
	public static function reviews( $val, Rich_Snippet $rich_snippet, array $meta_info ): array {

		if ( ! function_exists( '\wc_get_product' ) ) {
			return [];
		}

		/**
		 * @var \WP_Comment[] $comments
		 */

		$args = [
			'post_id'            => $meta_info['current_post_id'],
			'include_unapproved' => false,
			'number'             => 5,
			'type'               => 'review',
			'meta_query'         => array(
				'city_clause' => array(
					'key'     => 'rating',
					'compare' => 'EXISTS',
				),
			),
		];


		/**
		 * Get comments filter for WooCommerce products.
		 *
		 * Allows to filter the comment arguments when WooCommerce product comments are loaded.
		 *
		 * @hook  wpbuddy/rich_snippets/woocommerce/reviews/args
		 *
		 * @param {array} $args The arguments.
		 * @returns {array} The modified arguments.
		 *
		 * @since 2.7.0
		 */
		$comments = get_comments( apply_filters( 'wpbuddy/rich_snippets/woocommerce/reviews/args', $args ) );

		if ( ! is_array( $comments ) || count( $comments ) <= 0 ) {
			return [];
		}

		$reviews = [];

		foreach ( $comments as $comment ) {
			$review               = new \stdClass();
			$review->{'@context'} = 'http://schema.org';
			$review->{'@type'}    = 'Review';

			$review->author               = new \stdClass();
			$review->author->{'@context'} = 'http://schema.org';
			$review->author->{'@type'}    = 'Person';
			$review->author->name         = $comment->comment_author;

			$review->reviewRating               = new \stdClass();
			$review->reviewRating->{'@context'} = 'http://schema.org';
			$review->reviewRating->{'@type'}    = 'Rating';
			$review->reviewRating->bestRating   = 5;
			$review->reviewRating->worstRating  = 1;
			$review->reviewRating->ratingValue  = max( 1, absint( get_comment_meta( $comment->comment_ID, 'rating', true ) ) );

			$review->reviewBody    = strip_tags( $comment->comment_content );
			$review->datePublished = date_i18n( 'c', strtotime( $comment->comment_date ) );

			$reviews[] = $review;
		}

		return $reviews;
	}


	/**
	 * Adds new loop fields to the dropdown.
	 *
	 * @param array $values
	 *
	 * @return array
	 * @since 2.12.0
	 *
	 */
	public static function wc_loop_fields( $values ) {
		if ( function_exists( 'WC' ) ) {
			$values['variable_products'] = __( 'Variable products (WooCommerce)', 'rich-snippets-schema' );
		}

		return $values;
	}


	/**
	 * Returns the loop items.
	 *
	 * @param array $items
	 * @param Rich_Snippet $snippet
	 * @param int $post_id
	 *
	 * @return array
	 * @since 2.12.0
	 *
	 */
	public static function loop_items( $items, $snippet, $post_id ) {
		if ( ! function_exists( 'WC' ) ) {
			return $items;
		}

		if ( 'variable_products' !== $snippet->get_loop_type() ) {
			return $items;
		}

		$product = wc_get_product( $post_id );

		if ( $product instanceof \WC_Product_Simple ) {
			return [
				$product->get_id() => $product
			];
		}

		if ( $product instanceof \WC_Product_Variable || $product instanceof \WC_Product_Grouped ) {
			$ids = $product->get_children();

			if ( empty( $ids ) ) {
				return $items;
			}

			$products = get_posts( [
				'include'   => $ids,
				'post_type' => [ 'product_variation', 'product' ]
			] );

			if ( ! is_array( $products ) ) {
				return $items;
			}

			if ( count( $products ) <= 0 ) {
				return $items;
			}

			$products = array_combine( wp_list_pluck( $products, 'ID' ), $products );

			return $products;
		}

		return $items;
	}
}