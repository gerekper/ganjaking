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

use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On;

defined( 'ABSPATH' ) or exit;

/**
 * Abstract Display Rule Base Class
 *
 * @since 2.1.0
 */
abstract class Display_Rule {


	/** supported operators */
	const OPERATOR_EQUALS           = 'equals';
	const OPERATOR_DOES_NOT_EQUAL   = 'does_not_equal';
	const OPERATOR_CONTAINS         = 'contains';
	const OPERATOR_DOES_NOT_CONTAIN = 'does_not_contain';
	const OPERATOR_STARTS_WITH      = 'starts_with';
	const OPERATOR_ENDS_WITH        = 'ends_with';
	const OPERATOR_IS_EMPTY         = 'is_empty';
	const OPERATOR_IS_NOT_EMPTY     = 'is_not_empty';
	const OPERATOR_INCLUDES         = 'includes';
	const OPERATOR_DOES_NOT_INCLUDE = 'does_not_include';

	/** @var string the rule type -- override in concrete classes */
	protected $rule_type = '';

	/** @var string the property used for comparison */
	protected $property = '';

	/** @var string the operator used for comparison */
	protected $operator = '';

	/** @var array the values used for comparison */
	protected $values = [];

	/** @var string the tooltip displayed when configuring */
	protected $tooltip = '';

	/** @var Add_On the add-on */
	protected $add_on;


	/**
	 * Sets up the rule.
	 *
	 * @since 2.1.0
	 *
	 * @param array $data
	 *  @type string $property what to compare against (e.g. cart subtotal, product, category or other add-on)
	 *  @type string $operator comparison operator
	 *  @type array $values value(s) to compare with (could be a number, a string, products, categories or another add-on value)
	 *  @type string $tooltip to be displayed in the admin area
	 *  @type Add_On $add_on the add-on that owns this rule
	 */
	public function __construct( array $data = [] ) {

		foreach ( $data as $key => $value ) {
			if ( ! empty( $value ) ) {
				$this->$key = $value;
			}
		}
	}


	/**
	 * Gets the display rule type.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_type() {

		return $this->rule_type;
	}


	/**
	 * Gets the property used for comparison.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_property() {

		return $this->property;
	}


	/**
	 * Gets the operator used for comparison.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_operator() {

		return $this->operator;
	}


	/**
	 * Gets the values used for comparison.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_values() {

		return $this->values;
	}


	/**
	 * Gets the tooltip displayed when configuring.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_tooltip() {

		return $this->tooltip;
	}


	/**
	 * Gets the add-on.
	 *
	 * @since 2.1.0
	 *
	 * @return Add_On
	 */
	public function get_add_on() {

		return $this->add_on;
	}


	/**
	 * Gets property field.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	abstract public function get_property_field();


	/**
	 * Gets form fields.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	abstract public function get_fields();


	/**
	 * Evaluates the rule, based on the cart contents.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	abstract public function evaluate();


	/**
	 * Gets a human readable description.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	abstract public function get_description();


}
