<?php
/**
 * WooCommerce Points and Rewards
 *
 * @package     WC-Points-Rewards/Classes
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product class
 *
 * Handle messages for the single product page, and calculations for how many points are earned for a product purchase,
 * along with the discount available for a specific product
 *
 * @since 1.0
 */
class WC_Points_Rewards_Product {
	/**
	 * Add product-related hooks / filters
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// add single product message immediately after product excerpt
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'render_product_message' ), 15 );

		// add variation message before the price is displayed
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'add_variation_message_to_product_summary' ), 25 );

		// add the points message just before the variation price.
		add_filter( 'woocommerce_variation_price_html', array( $this, 'render_variation_message' ), 10, 2 );
		add_filter( 'woocommerce_variation_sale_price_html', array( $this, 'render_variation_message' ), 10, 2 );
		add_filter( 'woocommerce_available_variation', array( $this, 'render_available_variation_message' ), 10, 3 );

		add_filter( 'woocommerce_show_variation_price', '__return_true' );

		// delete transients
		add_action( 'woocommerce_delete_product_transients', array( $this, 'delete_transients' ) );
	}

	/**
	 * Add "Earn X Points when you purchase" message to the single product page for simple products
	 *
	 * @since 1.0
	 */
	public function render_product_message() {
		global $product;

		$message = get_option( 'wc_points_rewards_single_product_message' );

		$points_earned = self::get_points_earned_for_product_purchase( $product );
		$points_earned = WC_Points_Rewards_Manager::round_the_points( $points_earned );

		// bail if none available
		if ( ! $message || ! $points_earned ) {

			$message = '';

		} else {

			// Check to see if Dynamic Pricing is installed
			if ( class_exists( 'WC_Dynamic_Pricing' ) ) {
				// check to see if there are pricing rules for this product, if so, use the 'earn up to X points' message
				if ( get_post_meta( $product->get_id(), '_pricing_rules', true ) ) {
					$message = $this->create_variation_message_to_product_summary( $points_earned );
				}
			}

			// replace message variables
			$message = $this->replace_message_variables( $message, $product, $points_earned );
		}

		echo apply_filters( 'wc_points_rewards_single_product_message', $message, $this );
	}

	/**
	 * Add a message about the points to the product summary
	 *
	 * @since 1.2.6
	 */
	public function add_variation_message_to_product_summary() {
		global $product;

		// make sure the product has variations (otherwise it's probably a simple product)
		if ( method_exists( $product, 'get_available_variations' ) ) {
			// get variations
			$variations = $product->get_available_variations();

			// find the variation with the most points
			$points = $this->get_highest_points_variation( $variations, $product->get_id() );

			$message = '';
			// if we have a points value let's create a message; other wise don't print anything
			if ( $points ) {
				$message = $this->create_variation_message_to_product_summary( $points );
			}

			echo $message;
		}
	}

	/**
	 * Get the variation with the highest points and return the points value
	 *
	 * @since 1.2.6
	 */
	public function get_highest_points_variation( $variations, $product_id ) {

		// get transient name
		$transient_name = $this->transient_highest_point_variation( $product_id );

		// see if we already have this data saved
		$points = get_transient( $transient_name );

		// if we don't have anything saved we'll have to figure it out
		if ( false === $points ) {
			// find the variation with the most points
			$highest = array( 'key' => 0, 'points' => 0 );
			foreach ( $variations as $key => $variation ) {
				// get points
				$points = self::get_points_earned_for_product_purchase( $variation['variation_id'] );
				$points = WC_Points_Rewards_Manager::round_the_points( $points );
				// if this is the highest points value save it
				if ( $points > $highest['points'] ) {
					$highest = array( 'key' => $key, 'points' => $points );
				}
			}
			$points = $highest['points'];

			// save this for future use
			set_transient( $transient_name, $points, YEAR_IN_SECONDS );
		}

		return $points;
	}

	/**
	 * Create the "Earn up to X" message
	 *
	 * @since 1.2.6
	 */
	public function create_variation_message_to_product_summary( $points ) {
		global $wc_points_rewards;

		$message = get_option( 'wc_points_rewards_variable_product_message', '' );
		if ( ! empty( $message ) ) {

			// replace placeholders inside settings values
			$message = str_replace( '{points}', number_format_i18n( $points ), $message );
			$message = str_replace( '{points_label}', $wc_points_rewards->get_points_label( $points ), $message );

		}

		$message = '<p class="points">' . $message . '</p>';
		return $message;
	}


	/**
	 * Create the "Earn more than X" message
	 *
	 * @since 1.2.6
	 */
	public function create_at_least_message_to_product_summary( $points ) {
		// write the message
		$message = sprintf(
			/* translators: 1: points */
			__( 'Earn at least <strong>%d</strong> points by purchasing this product.', 'woocommerce-points-and-rewards' ),
			$points
		);

		// wrap it
		$message = '<p class="points">' . $message . '</p>';

		return $message;
	}

	/**
	 * Add "Earn X Points when you purchase" message to the single product page
	 * for variable products
	 *
	 * @param array      $data
	 * @param WC_Product $product
	 * @param WC_Product $variation
	 *
	 * @return array
	 */
	public function render_available_variation_message( $data, $product, $variation ) {

		if ( ! is_product() ) {
			return $data;
		}

		$message = get_option( 'wc_points_rewards_single_product_message' );

		$points_earned = self::get_points_earned_for_product_purchase( $variation );
		$points_earned = WC_Points_Rewards_Manager::round_the_points( $points_earned );

		// bail if none available
		if ( ! $message || ! $points_earned ) {
			return $data;
		}

		// replace message variables
		$data['price_html'] = $this->replace_message_variables( $message, $variation ) . ' ' . $data['price_html'];

		return $data;
	}

	/**
	 * Add "Earn X Points when you purchase" message to the single product page for variable products
	 *
	 * @since 1.0
	 */
	public function render_variation_message( $price_html, $product ) {

		if ( ! is_product() ) {
			return $price_html;
		}

		$message = get_option( 'wc_points_rewards_single_product_message' );

		$points_earned = self::get_points_earned_for_product_purchase( $product );
		$points_earned = WC_Points_Rewards_Manager::round_the_points( $points_earned );

		// bail if none available
		if ( ! $message || ! $points_earned ) {
			return $price_html;
		}

		// replace message variables
		$price_html = $this->replace_message_variables( $message, $product ) . ' ' . $price_html;

		return $price_html;
	}

	/**
	 * Replace product page message variables :
	 *
	 * {points} - the points earned for purchasing the product
	 * {points_value} - the monetary value of the points earned
	 * {points_label} - the label used for points
	 *
	 * @since 1.0
	 * @param string $message the message set in the admin settings
	 * @param object $product the product
	 * @return string the message with variables replaced
	 */
	private function replace_message_variables( $message, $product ) {

		global $wc_points_rewards;

		$points_earned = self::get_points_earned_for_product_purchase( $product );
		$points_earned = WC_Points_Rewards_Manager::round_the_points( $points_earned );
		// the min/max points earned for variable products can't be determined reliably, so the 'earn X points...' message
		// is not shown until a variation is selected, unless the prices for the variations are all the same
		// in which case, treat it like a simple product and show the message
		if ( method_exists( $product, 'get_variation_price' ) && $product->get_variation_price( 'min' ) != $product->get_variation_price( 'max' ) ) {
			return '';
		}

		// For BW compatibility, check to see if wc_min_points_earned exists, if not create it.
		if ( method_exists( $product, 'get_variation_price' ) ) {

			$wc_min_points_earned = get_post_meta( $product->get_id(), '_wc_min_points_earned', true );

			if ( ! $wc_min_points_earned ) {
				$wc_max_points_earned = '';
				$variable_points = array();

				if ( count( $product->get_children() ) > 0 ) {
					foreach ( $product->get_children() as $child ) {
						$earned = get_post_meta( $child, '_wc_points_earned', true );
						if ( '' !== $earned ) {
							$variable_points[] = $earned;
						}
					}
				}

				if ( count( $variable_points ) > 0 ) {
					$wc_min_points_earned = min( $variable_points );
				}

				update_post_meta( $product->get_id(), '_wc_min_points_earned', $wc_min_points_earned );
			}
		}

		$max_points_earned = get_post_meta( $product->get_id(), '_wc_points_max_discount', true );

		// Check to see if the minimum points earned is different from the max points earned, if so, dont show the message
		if ( method_exists( $product, 'get_variation_price' ) && isset( $wc_min_points_earned ) && $wc_min_points_earned != $max_points_earned ) {
			return '';
		}

		// Check to see if any max_points_earned is less than what the user would get with regular point applied for a variation
		if ( method_exists( $product, 'get_variation_price' ) ) {
			$variations = $product->get_available_variations();

			if ( $this->get_highest_points_variation( $variations, $product->get_id() ) > $max_points_earned ) {
				return '';
			}
		}

		// points earned
		$message = str_replace( '{points}', number_format_i18n( $points_earned ), $message );

		// points label
		$message = str_replace( '{points_label}', $wc_points_rewards->get_points_label( $points_earned ), $message );

		if ( method_exists( $product, 'get_variation_price' ) ) {
			$message = '<span class="wc-points-rewards-product-variation-message">' . $message . '</span><br />';
		} else {
			$message = '<span class="wc-points-rewards-product-message">' . $message . '</span><br />';
		}
		return $message;
	}

	/**
	 * Return the points earned when purchasing a product. If points are set at both the product and category level,
	 * the product points are used. If points are not set at the product or category level, the points are calculated
	 * using the default points per currency and the price of the product
	 *
	 * @since 1.0
	 * @param object $product the product to get the points earned for
	 * @return int the points earned
	 */
	public static function get_points_earned_for_product_purchase( $product, $order = null, $context = 'view' ) {

		// if we don't have a product object let's try to make one (hopefully they gave us the ID)
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		// check if earned points are set at product-level
		$points = self::get_product_points( $product, $order );

		if ( is_numeric( $points ) ) {
			return $points;
		}

		// check if earned points are set at category-level
		$points = self::get_category_points( $product, $order );

		if ( is_numeric( $points ) ) {
			return $points;
		}
		// otherwise, show the default points set for the price of the product
		return WC_Points_Rewards_Manager::calculate_points( $product->get_price( $context ) );
	}

	/**
	 * Return the points earned at the product level if set. If a percentage multiplier is set (e.g. 200%), the points are
	 * calculated based on the price of the product then multiplied by the percentage
	 *
	 * @since 1.0
	 * @param object $product the product to get the points earned for
	 * @return int the points earned
	 */
	public static function get_product_points( $product, $order = null ) {
		$variation_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $product->variation_id ) ) ? $product->variation_id : $product->get_id();

		if ( empty( $variation_id ) ) {
			// simple or variable product, for variable product return the maximum possible points earned
			$points = get_post_meta( $product->get_id(), '_wc_points_max_discount', true );
			if ( ! method_exists( $product, 'get_variation_price' ) ) {
				// subscriptions integration - if subscriptions is active check if this is a renewal order
				if ( self::is_order_renewal( $order ) ) {
					$renewal_points = get_post_meta( $variation_id, '_wc_points_renewal_points', true );
					$points = ( $renewal_points ) ? $renewal_points : $points;
				}
			}
		} else {
			// variation product
			$points = get_post_meta( $variation_id, '_wc_points_earned', true );

			// subscriptions integration - if subscriptions is active check if this is a renewal order
			if ( self::is_order_renewal( $order ) ) {
				$renewal_points = get_post_meta( $variation_id, '_wc_points_renewal_points', true );
				$points = ( $renewal_points ) ? $renewal_points : $points;
			}

			// if points aren't set at variation level, use them if they're set at the product level
			if ( '' === $points ) {
				$points = get_post_meta( $product->get_id(), '_wc_points_earned', true );

				// subscriptions integration - if subscriptions is active check if this is a renewal order
				if ( self::is_order_renewal( $order ) ) {
					$renewal_points = get_post_meta( $product->get_id(), '_wc_points_renewal_points', true );
					$points = ( $renewal_points ) ? $renewal_points : $points;
				}
			}
		} // End if().

		// if a percentage modifier is set, adjust the points for the product by the percentage
		if ( false !== strpos( $points, '%' ) ) {
			$points = self::calculate_points_multiplier( $points, $product );
		}

		return $points;
	}

	/**
	 * Return the points earned at the category level if set. If a percentage multiplier is set (e.g. 200%), the points are
	 * calculated based on the price of the product then multiplied by the percentage
	 *
	 * @since 1.0
	 * @param object $product the product to get the points earned for
	 * @return int the points earned
	 */
	public static function get_category_points( $product, $order = null ) {
		global $wpdb;

		if ( $product->is_type( 'variation' ) ) {
			$product_id = version_compare( WC_VERSION, '3.0.0', '<' ) ? $product->parent_id : $product->get_parent_id();
		} else {
			$product_id = $product->get_id();
		}

		$category_ids = wc_get_product_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

		if ( ! $category_ids ) {
			return '';
		}

		$category_points = '';

		$category_ids_string = implode( ',', array_map( 'intval', $category_ids ) );

		if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
			$category_points_query = "SELECT term_id AS category_id, meta_value AS points FROM {$wpdb->termmeta} WHERE meta_key = '_wc_points_earned' AND term_id IN ( $category_ids_string );";
		} else {
			$category_points_query = "SELECT woocommerce_term_id AS category_id, meta_value AS points FROM {$wpdb->woocommerce_termmeta} WHERE meta_key = '_wc_points_earned' AND woocommerce_term_id IN ( $category_ids_string );";
		}

		$category_points_data = $wpdb->get_results( $category_points_query );

		$category_points_array = array();

		if ( $category_points_data && count( $category_points_data ) > 0 ) {
			foreach ( $category_points_data as $category ) {
				$category_points_array[ $category->category_id ] = $category->points;
			}
		}

		foreach ( $category_points_array as $category_id => $points ) {

			// subscriptions integration - if subscriptions is active check if this is a renewal order
			if ( self::is_order_renewal( $order ) ) {
				$renewal_points = version_compare( WC_VERSION, '3.6', 'ge' ) ? get_term_meta( $category_id, '_wc_points_renewal_points', true ) : get_woocommerce_term_meta( $category_id, '_wc_points_renewal_points', true );
				$points = ( $renewal_points ) ? $renewal_points : $points;
			}

			// if a percentage modifier is set, adjust the default points earned for the category by the percentage
			if ( false !== strpos( $points, '%' ) ) {
				$points = self::calculate_points_multiplier( $points, $product );
			}

			if ( ! is_numeric( $points ) ) {
				continue;
			}

			// in the case of a product being assigned to multiple categories with differing points earned, we want to return the biggest one
			if ( $points >= (int) $category_points ) {
				$category_points = $points;
			}
		}

		return $category_points;
	}

	/**
	 * Calculate the points earned when a product or category is set to a percentage. This modifies the default points
	 * earned based on the global "Earn Points Conversion Rate" setting and products price by the given $percentage.
	 * e.g. a 200% multiplier will change 5 points to 10.
	 *
	 * @since 1.0
	 * @param string $percentage the percentage to multiply the default points earned by
	 * @param object $product the product to get the points earned for
	 * @return int the points earned after adjusting for the multiplier
	 */
	private static function calculate_points_multiplier( $percentage, $product ) {

		$percentage = str_replace( '%', '', $percentage ) / 100;

		return $percentage * WC_Points_Rewards_Manager::calculate_points( $product->get_price() );
	}

	/**
	 * Return the maximum discount available for redeeming points. If a max discount is set at both the product and
	 * category level, the product max discount is used. A global max discount can be set which is used as a fallback if
	 * no other max discounts are set
	 *
	 * @since 1.0
	 * @param object $product the product to get the maximum discount for
	 * @return float|string the maximum discount or an empty string which means a maximum discount is not set for the given product
	 */
	public static function get_maximum_points_discount_for_product( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		// check if max discount is set at product-level
		$max_discount = self::get_product_max_discount( $product );

		if ( is_numeric( $max_discount ) ) {
			return $max_discount;
		}

		// check if max discount is are set at category-level
		$max_discount = self::get_category_max_discount( $product );

		if ( is_numeric( $max_discount ) ) {
			return $max_discount;
		}

		// limit the discount available by the global maximum discount if set
		$max_discount = get_option( 'wc_points_rewards_max_discount' );

		// if the global max discount is a percentage, calculate it by multiplying the percentage by the product price
		if ( false !== strpos( $max_discount, '%' ) ) {
			$max_discount = self::calculate_discount_modifier( $max_discount, $product );
		}

		if ( is_numeric( $max_discount ) ) {
			return $max_discount;
		}

		// otherwise, there is no maximum discount set
		return '';
	}

	/**
	 * Return the maximum point discount at the product level if set. If a percentage multiplier is set (e.g. 35%),
	 * the maximum discount is equal to the product's price times the percentage
	 *
	 * @since 1.0
	 * @param object $product the product to get the maximum discount for
	 * @return float|string the maximum discount
	 */
	private static function get_product_max_discount( $product ) {
		$variation_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $product->variation_id ) ) ? $product->variation_id : $product->get_id();

		if ( empty( $variation_id ) ) {

			// simple product
			$max_discount = get_post_meta( $product->get_id(), '_wc_points_max_discount', true );

		} else {
			// variable product
			$points_max_discount = get_post_meta( $variation_id, '_wc_points_max_discount', true );
			$max_discount = ( isset( $points_max_discount ) ? $points_max_discount : '' );
		}

		// if a percentage modifier is set, set the maximum discount using the price of the product
		if ( false !== strpos( $max_discount, '%' ) ) {
			$max_discount = self::calculate_discount_modifier( $max_discount, $product );
		}

		return $max_discount;
	}

	/**
	 * Return the maximum points discount at the category level if set. If a percentage multiplier is set (e.g. 35%),
	 * the maximum discount is equal to the product's price times the percentage
	 *
	 * @since 1.0
	 * @param object $product the product to get the maximum discount for
	 * @return float|string the maximum discount
	 */
	private static function get_category_max_discount( $product ) {
		global $wpdb;

		if ( $product->is_type( 'variation' ) ) {
			$product_id = version_compare( WC_VERSION, '3.0.0', '<' ) ? $product->parent_id : $product->get_parent_id();
		} else {
			$product_id = $product->get_id();
		}

		$category_ids = wc_get_product_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

		if ( ! $category_ids ) {
			return '';
		}

		$category_max_discount = '';

		$category_ids_string = implode( ',', array_map( 'intval', $category_ids ) );

		if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
			$category_discount_query = "SELECT term_id AS category_id, meta_value AS max_discount FROM {$wpdb->termmeta} WHERE meta_key = '_wc_points_max_discount' AND term_id IN ( $category_ids_string );";
		} else {
			$category_discount_query = "SELECT woocommerce_term_id AS category_id, meta_value AS max_discount FROM {$wpdb->woocommerce_termmeta} WHERE meta_key = '_wc_points_max_discount' AND woocommerce_term_id IN ( $category_ids_string );";
		}
		$category_discount_data = $wpdb->get_results( $category_discount_query );

		$category_discount_array = array();

		if ( $category_discount_data && count( $category_discount_data ) > 0 ) {
			foreach ( $category_discount_data as $category ) {
				$category_discount_array[ $category->category_id ] = $category->max_discount;
			}
		}

		foreach ( $category_discount_array as $category_id => $max_discount ) {

			// if a percentage modifier is set, set the maximum discount using the price of the product
			if ( false !== strpos( $max_discount, '%' ) ) {
				$max_discount = self::calculate_discount_modifier( $max_discount, $product );
			}

			// get the minimum discount if the product belongs to multiple categories with differing maximum discounts
			if ( ! is_numeric( $category_max_discount ) || $max_discount < $category_max_discount ) {
				$category_max_discount = $max_discount;
			}
		}

		return $category_max_discount;
	}

	/**
	 * Calculate the maximum points discount when it's set to a percentage by multiplying the percentage times the product's
	 * price
	 *
	 * @since 1.0
	 * @param string $percentage the percentage to multiply the price by
	 * @param object $product the product to get the maximum discount for
	 * @return float the maximum discount after adjusting for the percentage
	 */
	private static function calculate_discount_modifier( $percentage, $product ) {

		$percentage = str_replace( '%', '', $percentage ) / 100;

		return $percentage * $product->get_price();
	}

	/**
	 * Get highest point variation transient name
	 *
	 * @since 1.2.6
	 */
	public function transient_highest_point_variation( $product_id ) {
		return 'wc_points_rewards_highest_point_variation_' . $product_id;
	}

	/**
	 * Get lowest point variation transient name
	 *
	 * @since 1.4.1
	 */
	public function transient_lowest_point_variation( $product_id ) {
		return 'wc_points_rewards_lowest_point_variation_' . $product_id;
	}

	/**
	 * Delete transients
	 *
	 * @since 1.2.6
	 */
	public function delete_transients( $product_id ) {
		delete_transient( $this->transient_highest_point_variation( $product_id ) );
		delete_transient( $this->transient_lowest_point_variation( $product_id ) );
	}

	/**
	 * Check if order is renewal in case Subscriptions is enabled.
	 *
	 * @param WC_Order $order
	 *
	 * @return bool
	 */
	protected static function is_order_renewal( $order ) {
		if ( ! function_exists( 'wcs_order_contains_resubscribe' ) || ! function_exists( 'wcs_order_contains_renewal' ) ) {
			return false;
		}

		if ( ! wcs_order_contains_resubscribe( $order ) && ! wcs_order_contains_renewal( $order ) ) {
			return false;
		}

		return true;
	}

} // end \WC_Points_Rewards_Product class
