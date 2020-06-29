<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Dynamic Pricing Compatibility Class
 *
 * @class   YITH_WCBM_Dynamic_Pricing_Compatibility
 * @package Yithemes
 * @since   1.2.8
 * @author  Yithemes
 *
 */
class YITH_WCBM_Dynamic_Pricing_Compatibility {

	/**
	 * Single instance of the class
	 *
	 * @var YITH_WCBM_Dynamic_Pricing_Compatibility
	 * @since 1.0.0
	 */
	protected static $_instance;


	/**
	 * @var array
	 */
	public $dynamic_rules;


	/**
	 * Returns single instance of the class
	 *
	 * @return YITH_WCBM_Dynamic_Pricing_Compatibility
	 * @since 1.0.0
	 */
	public static function get_instance() {
		return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
	}

	/**
	 * Constructor
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct() {
		add_filter( 'yith_wcbm_settings_admin_tabs', array( $this, 'add_admin_tabs' ) );

		add_filter( 'yith_wcmb_get_badges_premium', array( $this, 'add_dynamic_pricing_badges' ), 10, 2 );
	}

	/**
	 * get dynamic Pricing Rules
	 *
	 * @return array
	 */
	public function get_rules() {
		if ( isset( $this->dynamic_rules ) ) {
			return $this->dynamic_rules;
		}

		if ( is_callable( array( YITH_WC_Dynamic_Pricing(), 'recover_pricing_rules' ) ) ) {
			$this->dynamic_rules = YITH_WC_Dynamic_Pricing()->recover_pricing_rules();
		} else {
			$this->dynamic_rules = YITH_WC_Dynamic_Pricing()->get_option( 'pricing-rules' );
		}

		return $this->dynamic_rules;
	}

	/**
	 * @param string $badge_html
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	public function add_dynamic_pricing_badges( $badge_html, $product ) {
		$rules   = $this->get_rules();
		$product = wc_get_product( $product );

		if ( $product && ! empty( $rules ) ) {
			foreach ( $rules as $rule_id => $rule ) {
				$rule_badge         = get_option( 'yith-wcbm-dynamic-pricing-badge-' . $rule_id );
				$product_is_in_rule = $this->product_is_in_rule( $product->get_id(), $rule );

				if ( ! empty( $rule_badge ) && $rule_badge != 'none' && $product_is_in_rule ) {
					$badge_html .= yith_wcbm_get_badge_premium( $rule_badge, $product->get_id() );
				}
			}
		}

		return $badge_html;
	}

	/**
	 * Add Admin Setting Tabs
	 *
	 * @param $admin_tabs_free
	 *
	 * @return mixed
	 */
	public function add_admin_tabs( $admin_tabs_free ) {
		$admin_tabs_free['dynamic-pricing'] = __( 'Dynamic Pricing', 'yith-woocommerce-badges-management' );

		return $admin_tabs_free;
	}

	/**
	 * check if a product is in one rule
	 *
	 * @param $product_id
	 * @param $rule
	 *
	 * @return bool
	 */
	public function product_is_in_rule( $product_id, $rule ) {

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return false;
		}

		$other_variations  = apply_filters('yith_wcbm_valid_product_to_apply_with_other_variations', false, $product_id, $rule );
		$is_in_rule = is_callable( array( YITH_WC_Dynamic_Pricing_Helper(), 'valid_product_to_apply' ) ) ?
			YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply( $rule, $product, $other_variations ) :
			YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $rule, $product );

		// SCHEDULE VALIDATIONa
		if ( $is_in_rule && isset( $rule['schedule_from'] ) && isset( $rule['schedule_to'] ) && ( $rule['schedule_from'] != '' || $rule['schedule_to'] != '' ) ) {
			$is_in_rule = YITH_WC_Dynamic_Pricing_Helper()->validate_schedule( $rule['schedule_from'], $rule['schedule_to'] );
		}

		// USER VALIDATION
		if ( $is_in_rule && isset( $rule['user_rules'] ) && ( $rule['user_rules'] != 'everyone' && ! YITH_WC_Dynamic_Pricing_Helper()->validate_user( $rule['user_rules'], $rule[ 'user_rules_' . $rule['user_rules'] ] ) ) ) {
			$is_in_rule = false;
		}

		// ON SALE VALIDATION
		if ( $is_in_rule ) {
			$apply_on_sale = isset( $rule['apply_on_sale'] );
			if ( $product->is_on_sale() ) {
				$is_in_rule = $apply_on_sale;
			}
		}

		return $is_in_rule;
	}

}
