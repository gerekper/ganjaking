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

use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_With_Options;

defined( 'ABSPATH' ) or exit;

/**
 * Other Add-On Display Rule Class
 *
 * @since 2.1.0
 */
class Other_Add_On extends Display_Rule {


	/** @var string the rule type */
	protected $rule_type = 'other_add_on';


	/**
	 * Sets up the rule.
	 *
	 * @since 2.1.0
	 *
	 * @param array $data
	 *
	 * @type array $values
	 */
	public function __construct( $data = [] ) {

		parent::__construct( [
			'property' => ! empty( $data['property'] ) ? $data['property'] : '',
			'operator' => ! empty( $data['operator'] ) ? $data['operator'] : '',
			'values'   => ! empty( $data['values'] ) ? $data['values'] : [],
			'tooltip'  => __( 'Show this add-on based on the selected option or value of another add-on.', 'woocommerce-checkout-add-ons' ),
			'add_on'   => ! empty( $data['add_on'] ) ? $data['add_on'] : null,
		] );
	}


	/**
	 * Gets property field.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_property_field() {

		return [
			'id'      => 'other_add_on_property',
			'name'    => 'rules[other_add_on][property]',
			'type'    => 'select',
			'style'   => 'width: 150px;',
			'value'   => $this->get_property(),
			'options' => $this->get_add_on_options(),
		];
	}


	/**
	 * Gets form fields.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_fields() {

		$fields = [
			'other_add_on_current_add_on_id' => [
				'id'    => 'other_add_on_current_add_on_id',
				'name'  => 'rules[other_add_on][current_add_on_id]',
				'type'  => 'hidden',
				'value' => $this->get_add_on() ? $this->get_add_on()->get_id() : '',
			],
		];

		$other_add_on_id = $this->get_property();
		$other_add_on    = Add_On_Factory::get_add_on( $other_add_on_id );

		if ( ! empty( $other_add_on ) ) {

			$fields['other_add_on_operator'] = [
				'id'          => 'other_add_on_operator',
				'name'        => 'rules[other_add_on][operator]',
				'type'        => 'select',
				'style'       => 'width: 300px;',
				'options'     => $this->get_operator_options(),
				'text_before' => '<div id="other_add_on_operator_wrapper">',
				'text_after'  => '</div>',
				'value'       => $this->get_operator(),
			];

			if ( ! in_array( $this->get_operator(), [ Display_Rule::OPERATOR_IS_EMPTY, Display_Rule::OPERATOR_IS_NOT_EMPTY ] ) ) {

				switch ( $other_add_on->get_type() ) {

					case 'text':
					case 'textarea':
						$fields['other_add_on_input_value'] = [
							'id'          => 'other_add_on_input_value',
							'name'        => 'rules[other_add_on][values]',
							'type'        => 'text',
							'style'       => 'width: 300px;',
							'text_before' => '<div>',
							'text_after'  => '</div>',
							'value'       => is_array( $this->get_values() ) ? current( $this->get_values() ) : $this->get_values(),
						];
					break;

					case 'select':
					case 'radio':
						$fields['other_add_on_select_value'] = [
							'id'          => 'other_add_on_select_value',
							'name'        => 'rules[other_add_on][values]',
							'type'        => 'select',
							'style'       => 'width: 300px;',
							'text_before' => '<div>',
							'text_after'  => '</div>',
							'options'     => $this->get_select_options(),
							'value'       => is_array( $this->get_values() ) ? current( $this->get_values() ) : $this->get_values(),
						];
					break;

					case 'multiselect':
					case 'multicheckbox':
						$fields['other_add_on_multiselect_value'] = [
							'id'          => 'other_add_on_multiselect_value',
							'name'        => 'rules[other_add_on][values]',
							'type'        => 'multiselect',
							'class'       => 'rules-multiselect',
							'style'       => 'width: 300px;',
							'text_before' => '<div>',
							'text_after'  => '</div>',
							'options'     => $this->get_select_options(),
							'value'       => $this->get_values(),
						];
					break;
				}
			}
		}

		return $fields;
	}


	/**
	 * Gets add-ons and returns key-value pair of active add-ons.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	private function get_add_on_options() {

		$add_ons = Add_On_Factory::get_add_ons();

		$options = [
			'' => __( 'Choose an add-on', 'woocommerce-checkout-add-ons' ),
		];

		// remove current add-on from list
		$current_add_on_id = '';

		if ( $this->get_add_on() ) {
			$current_add_on_id = $this->get_add_on()->get_id();
		}

		foreach ( $add_ons as $add_on ) {

			if ( $add_on->get_enabled() && $add_on->get_id() && $add_on->get_id() !== $current_add_on_id ) {

				$options[ $add_on->get_id() ] = $add_on->get_name();
			}
		}

		return $options;
	}


	/**
	 * Gets the operator options, based on the other add-on type.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	private function get_operator_options() {

		$other_add_on_id = $this->get_property();
		$other_add_on    = Add_On_Factory::get_add_on( $other_add_on_id );

		if ( empty( $other_add_on ) ) {
			return [
				'' => ''
			];
		}

		$options = [];

		$supported_operators = self::get_supported_operators_per_type( $other_add_on->get_type() );

		foreach ( $supported_operators as $operator ) {
			$label                = self::get_operator_label( $operator );
			$options[ $operator ] = $label;
		}

		return $options;
	}


	/**
	 * Gets the supported operators, based on the other add-on type.
	 *
	 * @since 2.1.0
	 *
	 * @param string $add_on_type add-on type
	 *
	 * @return array
	 */
	private static function get_supported_operators_per_type( $add_on_type ) {

		switch ( $add_on_type ) {

			case 'text':
			case 'textarea':
				return [
					Display_Rule::OPERATOR_EQUALS,
					Display_Rule::OPERATOR_DOES_NOT_EQUAL,
					Display_Rule::OPERATOR_CONTAINS,
					Display_Rule::OPERATOR_DOES_NOT_CONTAIN,
					Display_Rule::OPERATOR_STARTS_WITH,
					Display_Rule::OPERATOR_ENDS_WITH,
					Display_Rule::OPERATOR_IS_EMPTY,
					Display_Rule::OPERATOR_IS_NOT_EMPTY,
				];
			break;

			case 'file':
			case 'checkbox':
				return [
					Display_Rule::OPERATOR_IS_EMPTY,
					Display_Rule::OPERATOR_IS_NOT_EMPTY,
				];
			break;

			case 'select':
			case 'radio':
				return [
					Display_Rule::OPERATOR_EQUALS,
					Display_Rule::OPERATOR_DOES_NOT_EQUAL,
					Display_Rule::OPERATOR_IS_EMPTY,
					Display_Rule::OPERATOR_IS_NOT_EMPTY,
				];
			break;

			case 'multiselect':
			case 'multicheckbox':
				return [
					Display_Rule::OPERATOR_INCLUDES,
					Display_Rule::OPERATOR_DOES_NOT_INCLUDE,
					Display_Rule::OPERATOR_IS_EMPTY,
					Display_Rule::OPERATOR_IS_NOT_EMPTY,
				];
			break;

			default:
				return [];
		}
	}


	/**
	 * Gets the operator label.
	 *
	 * @since 2.1.0
	 *
	 * @param string $operator the operator
	 *
	 * @return string
	 */
	private static function get_operator_label( $operator ) {

		$labels = [
			Display_Rule::OPERATOR_EQUALS           => __( 'equals', 'woocommerce-checkout-add-ons' ),
			Display_Rule::OPERATOR_DOES_NOT_EQUAL   => __( 'does not equal', 'woocommerce-checkout-add-ons' ),
			Display_Rule::OPERATOR_CONTAINS         => __( 'contains', 'woocommerce-checkout-add-ons' ),
			Display_Rule::OPERATOR_DOES_NOT_CONTAIN => __( 'does not contain', 'woocommerce-checkout-add-ons' ),
			Display_Rule::OPERATOR_STARTS_WITH      => __( 'starts with', 'woocommerce-checkout-add-ons' ),
			Display_Rule::OPERATOR_ENDS_WITH        => __( 'ends with', 'woocommerce-checkout-add-ons' ),
			Display_Rule::OPERATOR_IS_EMPTY         => __( 'is empty', 'woocommerce-checkout-add-ons' ),
			Display_Rule::OPERATOR_IS_NOT_EMPTY     => __( 'is not empty', 'woocommerce-checkout-add-ons' ),
			Display_Rule::OPERATOR_INCLUDES         => __( 'includes', 'woocommerce-checkout-add-ons' ),
			Display_Rule::OPERATOR_DOES_NOT_INCLUDE => __( 'does not include', 'woocommerce-checkout-add-ons' ),
		];

		return ! empty( $labels[ $operator ] ) ? $labels[ $operator ] : '';
	}


	/**
	 * Gets the select options, based on the other add-on type and operator.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	private function get_select_options() {

		$select_options = [];

		if ( ! in_array( $this->get_operator(), [ Display_Rule::OPERATOR_IS_EMPTY, Display_Rule::OPERATOR_IS_NOT_EMPTY ] ) ) {

			$other_add_on = Add_On_Factory::get_add_on( $this->get_property() );
			if ( $other_add_on instanceof Add_On_With_Options ) {

				$options = $other_add_on->get_options( 'edit' );
				foreach ( $options as $option ) {

					$key = sanitize_title( esc_html( $option['label'] ), '', 'wc_checkout_add_ons_sanitize' );

					$select_options[ $key ] = $option['label'];
				}
			}
		}

		return $select_options;
	}


	/**
	 * Evaluates the rule, based on the cart contents.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function evaluate() {

		// empty rule
		if ( empty( $this->get_property() ) ) {
			return true;
		}

		$other_add_on_id = $this->get_property();
		$other_add_on    = Add_On_Factory::get_add_on( $other_add_on_id );

		if ( empty( $other_add_on ) || ! $other_add_on->get_enabled() ) {
			// the add-on was deleted or disabled, ignore the rule
			return true;
		}

		// check if the operator is supported for the selected add-on type
		$supported_operators = self::get_supported_operators_per_type( $other_add_on->get_type() );
		if ( ! in_array( $this->get_operator(), $supported_operators, true ) ) {
			// this should not happen, ignore the rule
			return true;
		}

		// get single value from rule
		$single_value = is_array( $this->get_values() ) ? current( $this->get_values() ) : $this->get_values();

		$cart_add_on_value = wc_checkout_add_ons()->get_frontend_instance()->checkout_get_add_on_value( $other_add_on->get_default_value(), $other_add_on_id );

		{

			switch ( $this->get_operator() ) {

				case Display_Rule::OPERATOR_IS_EMPTY:
					return empty( $cart_add_on_value );
				break;

				case Display_Rule::OPERATOR_IS_NOT_EMPTY:
					return ! empty( $cart_add_on_value );
				break;

				case Display_Rule::OPERATOR_EQUALS:
					return ! empty( $cart_add_on_value ) && $cart_add_on_value === $single_value;
				break;

				case Display_Rule::OPERATOR_DOES_NOT_EQUAL:
					return empty( $cart_add_on_value ) || $cart_add_on_value !== $single_value;
				break;

				case Display_Rule::OPERATOR_CONTAINS:
					// string comparison
					return ! empty( $cart_add_on_value ) && strpos( $cart_add_on_value, $single_value ) !== false;
				break;

				case Display_Rule::OPERATOR_DOES_NOT_CONTAIN:
					// string comparison
					return empty( $cart_add_on_value ) || strpos( $cart_add_on_value, $single_value ) === false;
				break;

				case Display_Rule::OPERATOR_STARTS_WITH:
					// string comparison
					return ! empty( $cart_add_on_value ) && substr( $cart_add_on_value, 0, strlen( $single_value ) ) === $single_value;
				break;

				case Display_Rule::OPERATOR_ENDS_WITH:
					// string comparison
					return ! empty( $cart_add_on_value ) && substr( $cart_add_on_value, - strlen( $single_value ) ) === $single_value;
				break;

				case Display_Rule::OPERATOR_INCLUDES:
					if ( ! empty( $cart_add_on_value ) ) {
						foreach ( $this->get_values() as $value ) {
							if ( in_array( $value, $cart_add_on_value, true ) ) {
								return true;
							}
						}
					}

					return false;
				break;

				case Display_Rule::OPERATOR_DOES_NOT_INCLUDE:
					if ( ! empty( $cart_add_on_value ) ) {
						foreach ( $this->get_values() as $value ) {
							if ( in_array( $value, $cart_add_on_value, true ) ) {
								return false;
							}
						}
					}

					return true;
				break;
			}
		}

		return false;
	}


	/**
	 * Gets a human readable description.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_description() {

		$description = '';

		if ( ! empty( $this->get_property() ) && ! empty( $this->get_operator() ) ) {

			$other_add_on_id = $this->get_property();
			$other_add_on    = Add_On_Factory::get_add_on( $other_add_on_id );

			if ( ! empty( $other_add_on ) || ! $other_add_on->get_enabled() ) {

				// check if the operator is supported for the selected add-on type
				$supported_operators = self::get_supported_operators_per_type( $other_add_on->get_type() );
				if ( in_array( $this->get_operator(), $supported_operators ) ) {

					$values = '';
					if ( ! empty( $this->get_values() ) ) {
						$values = $this->get_values();
						if ( ! is_array( $values ) ) {
							$values = [ $values ];
						}

						// get option labels
						foreach ( $values as $index => $value ) {

							if ( $other_add_on instanceof Add_On_With_Options ) {

								$options = $other_add_on->get_options( 'edit' );
								foreach ( $options as $option ) {

									$key = sanitize_title( esc_html( $option['label'] ), '', 'wc_checkout_add_ons_sanitize' );
									if ( $key === $value ) {
										$values[ $index ] = $option['label'];
									}
								}
							}
						}

						$values = implode( ',', $values );
					}

					$description = sprintf( '%1$s%2$s%3$s %4$s %5$s%6$s%7$s',
						'<strong>',
						$other_add_on->get_name(),
						'</strong>',
						self::get_operator_label( $this->get_operator() ),
						'<em>',
						$values,
						'</em>'
					);
				}
			}
		}

		return $description;
	}


}
