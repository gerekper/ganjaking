<?php
/**
 * WooCommerce Checkout Add-Ons
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Display_Rules;

defined( 'ABSPATH' ) or exit;

/**
 * Display Rule Factory Class
 *
 * @since 2.1.0
 */
class Display_Rule_Factory {


	/** @var string namespace for built-in display rules */
	const DISPLAY_RULES_NAMESPACE = 'SkyVerge\\WooCommerce\\Checkout_Add_Ons\\Add_Ons\\Display_Rules\\';


	/**
	 * Gets the registered display rule classnames.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public static function get_display_rule_classnames() {

		/**
		 * Filters the classnames for each display rule type.
		 *
		 * @since 2.1.0
		 *
		 * @param array $display_rule_classnames the display rule classnames
		 */
		return apply_filters( 'wc_checkout_add_ons_display_rule_classnames', [
			'cart_subtotal'       => 'Cart_Subtotal',
			'product_or_category' => 'Product_Or_Category',
			'other_add_on'        => 'Other_Add_On',
		] );
	}


	/**
	 * Instantiates and returns a display rule.
	 *
	 * @since 2.1.0
	 *
	 * @param string $display_rule_type the display rule type string
	 * @param array $params (optional) param to pass into the new display rule's constructor -- @see Display_Rule::__construct() for more info
	 *
	 * @return Display_Rule|bool false on failure
	 */
	public static function create_display_rule( $display_rule_type, $params = [] ) {

		$classname    = self::get_display_rule_classname( $display_rule_type );
		$display_rule = class_exists( $classname ) ? new $classname( $params ) : false;

		return $display_rule instanceof Display_Rule ? $display_rule : false;
	}


	/**
	 * Gets the fully-qualified classname for a display rule.
	 *
	 * @since 2.1.0
	 *
	 * @param string $display_rule_type the display rule type string
	 *
	 * @return string
	 */
	public static function get_display_rule_classname( $display_rule_type ) {

		$display_rule_classnames = self::get_display_rule_classnames();
		$classname               = isset( $display_rule_classnames[ $display_rule_type ] ) ? $display_rule_classnames[ $display_rule_type ] : '';

		if ( '' !== $classname ) {

			// namespace classname
			$classname = self::DISPLAY_RULES_NAMESPACE . $classname;
		}

		/**
		 * Filters the classname used to instantiate a given display rule type.
		 *
		 * @since 2.1.0
		 *
		 * @param string $classname the fully-qualified classname to instantiate
		 * @param string $display_rule_type the display rule type string
		 */
		$classname = apply_filters( 'wc_checkout_add_ons_get_display_rule_classname', $classname, $display_rule_type );

		$class_parents = class_exists( $classname ) ? class_parents( $classname ) : [];

		// make sure the class extends our abstract Display_Rule class
		return is_array( $class_parents ) && in_array( self::DISPLAY_RULES_NAMESPACE . 'Display_Rule', $class_parents, true ) ? $classname : '';
	}


}
