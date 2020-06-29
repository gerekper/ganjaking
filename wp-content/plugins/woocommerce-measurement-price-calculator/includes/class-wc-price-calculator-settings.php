<?php
/**
 * WooCommerce Measurement Price Calculator
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woocommerce.com/document/measurement-price-calculator/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Admin Settings API used by the price calculator plugin
 *
 * @since 2.0
 */
class WC_Price_Calculator_Settings {


	/** Default area measurement unit */
	const DEFAULT_AREA = 'sq cm';

	/** Default volume measurement unit */
	const DEFAULT_VOLUME = 'ml';

	/** @var \WC_Product the product these settings are associated with (optional) */
	private $product;

	/** @var array the raw settings array */
	private $settings;

	/** @var array raw pricing rules array (if any) */
	private $pricing_rules;


	/**
	 * Construct and initialize the price calculator settings
	 *
	 * @param mixed $product Optional product or product id to load settings from.
	 *                       Otherwise, default settings object is instantiated
	 */
	public function __construct( $product = null ) {

		$settings = null;

		// product id
		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}

		// have a product
		if ( $product instanceof \WC_Product ) {

			if ( $product->is_type( 'variation' ) ) {

				$product = wc_get_product( $product->get_parent_id() );
			}

			$this->product = $product;

			$settings = $product->get_meta( '_wc_price_calculator' );
		}

		$this->set_raw_settings( is_array( $settings ) ? $settings : null );
	}


	/**
	 * Returns the product associated with this settins object, if any
	 *
	 * @return \WC_Product the product object
	 */
	public function get_product() {
		return $this->product;
	}


	/**
	 * Sets the underlying settings array
	 *
	 * @since 3.0
	 * @param array|string $settings array or serialized array of settings
	 * @return array the raw settings
	 */
	public function set_raw_settings( $settings ) {

		$settings = maybe_unserialize( $settings );

		if ( is_array( $settings ) ) {
			$this->settings = $settings;
		} else {
			$this->settings = $this->get_default_settings();
		}

		$this->update_settings();

		return $this->get_raw_settings();
	}


	/**
	 * Returns the underlying settings array
	 *
	 * @return array the settings array
	 */
	public function get_raw_settings() {
		return $this->settings;
	}


	/**
	 * Gets the configured calculator type (if any)
	 *
	 * @return string the calculator type, one of
	 *         'dimension', 'area', 'area-dimension', 'area-linear', 'area-surface',
	 *         'volume', 'volume-dimension', 'volume-area',
	 *         'weight', 'wall-dimension' or ''
	 */
	public function get_calculator_type() {
		return $this->settings['calculator_type'];
	}


	/**
	 * Returns true if the calculator is a derived type (meaning more than one
	 * measurement is supplied to derive a final amount), ie Area (LxW)
	 *
	 * @since 3.0
	 * @return bool true if the calculator type is derived
	 */
	public function is_calculator_type_derived() {
		return in_array( $this->get_calculator_type(), array( 'area-dimension', 'area-linear', 'area-surface', 'volume-dimension', 'volume-area', 'wall-dimension' ), true );
	}


	/**
	 * Gets the measurements settings for the current calculator.  If a frontend
	 * label is not set for a measurement, the unit will be used.  If the
	 * returned measurements include more than one, for instance length, width or
	 * area, height, a common unit will be available on all of them to faciliate
	 * deriving a compound measurement (ie area or volume)
	 *
	 * @return \WC_Price_Calculator_Measurement[] Array of measurements.
	 */
	public function get_calculator_measurements() {

		$calculator_type = $this->get_calculator_type();
		$measurements = array();

		// special case for the dimension calculator, pluck out the enabled measurement (one of length, width or height) and return by itself
		if ( 'dimension' === $calculator_type ) {

			foreach ( $this->settings[ $this->get_calculator_type() ] as $name => $value ) {

				if ( 'pricing' !== $name && 'yes' === $value['enabled'] ) {

					$measurements[] = new \WC_Price_Calculator_Measurement( $value['unit'], 1, $name, $value['label'], $value['editable'], $this->get_options( $name ) );
				}
			}

		} else {

			// otherwise just return the measurement settings with a default value (excluding the 'pricing' setting)
			$measurements = array();
			$common_unit  = null;

			foreach ( $this->settings[ $this->get_calculator_type() ] as $name => $value ) {

				if ( 'pricing' !== $name ) {

					$measurement = new \WC_Price_Calculator_Measurement( $value['unit'], 1, $name, $value['label'], $value['editable'], $this->get_options( $name ) );

					// generate a common unit to use for this set of measurements based on the first measurement encountered
					//  then set that common unit on the subsequent.  This allows us to have the (admittedly crazy) case of
					//  a Volume (AxH) calculator with area in acres and height in meters, so the common unit will be
					//  sq. ft. and ft. respectively.  That way we can multiply A * H and get an answer in known units (cu. ft.)
					//  regardless of the mixture of units that the constituent measurements use
					if ( null === $common_unit ) {
						$common_unit = $measurement->get_unit_common();
					} else {
						$measurement->set_common_unit( $common_unit );
					}

					$measurements[] = $measurement;
				}
			}
		}

		return $measurements;
	}


	/**
	 * Returns true if the calculator is enabled
	 *
	 * @return bool true if the calculator is enabled, false otherwise
	 */
	public function is_calculator_enabled() {
		return '' !== $this->get_calculator_type();
	}


	/**
	 * Returns true if "show product price per unit" is enabled
	 *
	 * @return bool true if the price per unit should be displayed on the frontend
	 */
	public function is_pricing_enabled() {

		$calculator_type = $this->get_calculator_type();

		return isset( $this->settings[ $calculator_type ]['pricing']['enabled'] ) && 'yes' === $this->settings[ $calculator_type ]['pricing']['enabled'];
	}


	/**
	 * Returns true if the quantity calculator is enabled (this is normal mode
	 * where the price of a product is not per unit, ie not $/sq ft)
	 *
	 * @since 3.0
	 * @return bool true if the quantity calculator is enabled
	 */
	public function is_quantity_calculator_enabled() {
		return $this->is_calculator_enabled() && ! $this->is_pricing_calculator_enabled();
	}


	/**
	 * Returns true if the calculator pricing per unit is enabled, meaning that
	 * the product price is defined "per unit" (ie $/sq ft) and the customer
	 * purchases a custom amount
	 *
	 * @since 3.0
	 * @return bool true if calculator pricing is enabled
	 */
	public function is_pricing_calculator_enabled() {

		$calculator_type = $this->get_calculator_type();

		return $this->is_pricing_enabled() && isset( $this->settings[ $calculator_type ]['pricing']['calculator']['enabled'] ) && 'yes' == $this->settings[ $calculator_type ]['pricing']['calculator']['enabled'];
	}


	/**
	 * Returns true if the calculator pricing inventory is enabled.  This means
	 * that inventory is tracked "per foot" or whatever, rather than per item.
	 *
	 * @since 3.0
	 * @return bool true if pricing and pricing inventory is enabled
	 */
	public function is_pricing_inventory_enabled() {

		$calculator_type = $this->get_calculator_type();

		return $this->is_pricing_calculator_enabled() && isset( $this->settings[ $calculator_type ]['pricing']['inventory']['enabled'] ) && 'yes' == $this->settings[ $calculator_type ]['pricing']['inventory']['enabled'];
	}


	/**
	 * Returns true if the calculator pricing calculated weight is enabled.
	 * This means that weight is calcualted "per foot" or whatever, rather
	 * than per item.
	 *
	 * @since 3.0
	 * @return bool true if pricing and calculated weight is enabled
	 */
	public function is_pricing_calculated_weight_enabled() {

		$calculator_type = $this->get_calculator_type();

		return $this->is_pricing_calculator_enabled() && isset( $this->settings[ $calculator_type ]['pricing']['weight']['enabled'] ) && 'yes' == $this->settings[ $calculator_type ]['pricing']['weight']['enabled'];
	}


	/**
	 * Sets the given pricing rules, verifying for correctness: a rule must have
	 * a numeric (non-negative) start and price to be valid.  The pricing rules
	 * will be in terms of the pricing unit.
	 *
	 * @since 3.0
	 * @param array $pricing_rules the pricing rules
	 */
	private function set_pricing_rules( $pricing_rules ) {

		$this->pricing_rules = array();

		if ( is_array( $pricing_rules ) ) {

			foreach ( $pricing_rules as $rule ) {

				/**
				 * Filter a Measurement Price Calculator settings rule.
				 *
				 * @since 3.12.3
				 *
				 * @param array $rule a rule as an associative array
				 * @param \WC_Product $product the product the rule applies to
				 */
				$rule = (array) apply_filters( 'wc_measurement_price_calculator_settings_rule', $rule, $this->product );

				if ( isset( $rule['range_start'], $rule['regular_price'] ) && is_numeric( $rule['range_start'] ) && $rule['range_start'] >= 0 && is_numeric( $rule['regular_price'] ) && $rule['regular_price'] >= 0 ) {
					$this->pricing_rules[] = $rule;
				}
			}
		}
	}


	/**
	 * Returns the pricing rules (if any) associated with this calculator,
	 * which are available only if the pricing calculator is enabled.
	 * Pricing rules ranges default to pricing units.
	 *
	 * @since 3.0
	 * @param string $to_unit optional units to return the pricing rules ranges in,
	 *                        defaults to pricing units
	 * @return array of pricing rules with ranges in terms of $to_unit
	 */
	public function get_pricing_rules( $to_unit = null ) {

		// default if the pricing calculator is not enabled
		$pricing_rules = array();

		if ( $this->product && $this->is_pricing_calculator_enabled() ) {

			// load the pricing rules when needed
			if ( null === $this->pricing_rules ) {

				$rules = $this->product->get_meta( '_wc_price_calculator_pricing_rules' );

				if ( is_array( $rules ) ) {
					$this->set_pricing_rules( $rules  );
				}
			}

			// default pricing rules
			$pricing_rules = $this->pricing_rules;

			// if a conversion
			if ( ! empty( $pricing_rules ) && $to_unit && $to_unit !== $this->get_pricing_unit() ) {

				foreach ( $pricing_rules as &$rule ) {

					$rule['range_start']     = \WC_Price_Calculator_Measurement::convert( $rule['range_start'], $this->get_pricing_unit(), $to_unit );

					if ( '' !== $rule['range_end'] ) {
						$rule['range_end']   = \WC_Price_Calculator_Measurement::convert( $rule['range_end'],   $this->get_pricing_unit(), $to_unit );
					}
				}

			}
		}

		return $pricing_rules;
	}


	/**
	 * Returns true if pricing rules are enabled for this calculator
	 *
	 * @since 3.0
	 * @return bool true if there are pricing rules, false otherwise
	 */
	public function pricing_rules_enabled() {
		return $this->has_pricing_rules();
	}


	/**
	 * Returns true if there are pricing rules available for this calculator
	 *
	 * @since 3.0
	 * @return bool true if there are pricing rules, false otherwise
	 */
	public function has_pricing_rules() {
		return count( $this->get_pricing_rules() ) > 0;
	}


	/**
	 * Gets the price for the given $measurement, if there is a matching pricing
	 * rule, or null
	 *
	 * @since 3.0
	 * @param \WC_Price_Calculator_Measurement $measurement the product total measurement
	 * @return float the price for the given $measurement (regular or sale)
	 */
	public function get_pricing_rules_price( $measurement ) {

		// get the value in pricing units for comparison
		$measurement_value = $measurement->get_value( $this->get_pricing_unit() );

		foreach ( $this->get_pricing_rules() as $rule ) {

			// if we find a matching rule, return the price
			if ( $measurement_value >= $rule['range_start'] && ( '' === $rule['range_end'] || $measurement_value <= $rule['range_end'] ) ) {

				return $rule['price'];
			}
		}

		return null;
	}


	/**
	 * Returns the true if there's a pricing table sale running
	 *
	 * @since 3.0
	 * @return bool true if there's a pricing table sale running, false otherwise
	 */
	public function pricing_rules_is_on_sale() {

		foreach ( $this->get_pricing_rules() as $rule ) {

			if ( '' !== $rule['sale_price'] ) {

				return true;
			}
		}

		return false;
	}


	/**
	 * Returns the minimum possible pricing rule price, or null
	 *
	 * @since 3.0
	 * @return int|float the minimum possible pricing rule price, or null
	 */
	public function get_pricing_rules_minimum_price() {

		$min = null;

		foreach ( $this->get_pricing_rules() as $rule ) {

			if ( null === $min ) $min = PHP_INT_MAX;  // initialize to the largest possible number
			$min = min( $min, $rule['price'] );

		}

		return $min;
	}


	/**
	 * Returns the largest possible pricing rule price, or null
	 *
	 * @since 3.0
	 * @return int|float the largest possible pricing rule price, or null
	 */
	public function get_pricing_rules_maximum_price() {

		$max = null;

		foreach ( $this->get_pricing_rules() as $rule ) {

			if ( null === $max ) $max = -1;  // initialize to an impossible price
			$max = max( $max, $rule['price'] );

		}

		return $max;
	}


	/**
	 * Returns the minimum possible pricing rule price, or null
	 *
	 * @since 3.0
	 * @return int|float the minimum possible pricing rule regular price, or null
	 */
	public function get_pricing_rules_minimum_regular_price() {

		$min = null;

		foreach ( $this->get_pricing_rules() as $rule ) {

			if ( null === $min ) $min = PHP_INT_MAX;  // initialize to the largest possible number
			$min = min( $min, $rule['regular_price'] );

		}

		return $min;
	}


	/**
	 * Returns the maximum possible pricing rule price, or null
	 *
	 * @since 3.4.0
	 * @return int|float the minimum possible pricing rule regular price, or null
	 */
	public function get_pricing_rules_maximum_regular_price() {

		$max = null;

		foreach ( $this->get_pricing_rules() as $rule ) {

			if ( null === $max ) $max = -1;  // initialize to an impossible price
			$max = max( $max, $rule['regular_price'] );

		}

		return $max;
	}


	/**
	 * Returns the minimum possible pricing rule sale price, or null
	 *
	 * @since 3.8.1
	 * @return float the minimum possible pricing rule regular price, or null
	 */
	public function get_pricing_rules_minimum_sale_price() {

		$min = null;

		foreach ( $this->get_pricing_rules() as $rule ) {

			// skip rules with no sale price
			if ( '' === $rule['sale_price'] ) {
				continue;
			}

			if ( null === $min ) {
				// initialize to the largest possible number
				$min = PHP_INT_MAX;
			}

			$min = min( $min, $rule['sale_price'] );

		}

		return $min;
	}


	/**
	 * Returns the maximum possible pricing rule sale price, or null
	 *
	 * @since 3.8.1
	 * @return float the minimum possible pricing rule regular price, or null
	 */
	public function get_pricing_rules_maximum_sale_price() {

		$max = null;

		foreach ( $this->get_pricing_rules() as $rule ) {

			// skip rules with no sale price
			if ( '' === $rule['sale_price'] ) {
				continue;
			}

			if ( null === $max ) {
				$max = -1;  // initialize to an impossible price
			}

			$max = max( $max, $rule['sale_price'] );

		}

		return $max;
	}


	/**
	 * Returns the price html for the given pricing rule, ie:
	 * * -$10 / ft- $5 / ft
	 * * $5 / ft
	 * * -$10 / ft- Free!
	 * * Free!
	 *
	 * @since 3.0
	 * @param array $rule the pricing rule with keys:
	 *                    'range_start', 'range_end',
	 *                    'price', 'regular_price' and 'sale_price'
	 * @return string pricing rule price html
	 */
	public function get_pricing_rule_price_html( $rule ) {

		$price_html = '';
		$sep        = apply_filters( 'wc_measurement_price_calculator_pricing_label_separator', '/' );

		if ( $rule['price'] > 0 ) {

			if ( '' !== $rule['sale_price'] && '' !== ( $rule['regular_price'] ) ) {
				$price_html .= \WC_Price_Calculator_Product::get_price_html_from_to( $rule['regular_price'], $rule['price'], $sep . ' ' . __( $this->get_pricing_label(), 'woocommerce-measurement-price-calculator' ) );
			} else {
				$price_html .= wc_price( $rule['price'] ) . ' ' . $sep . ' ' . __( $this->get_pricing_label(), 'woocommerce-measurement-price-calculator' );
			}

		} elseif ( '' === $rule['price'] ) {

			// no-op (for now)

		} elseif ( 0 == $rule['price'] ) {

			if ( $rule['price'] === $rule['sale_price'] && '' !== $rule['regular_price'] ) {
				$price_html .= \WC_Price_Calculator_Product::get_price_html_from_to( $rule['regular_price'], __( 'Free!', 'woocommerce-measurement-price-calculator' ), __( $this->get_pricing_label(), 'woocommerce-measurement-price-calculator' ) );
			} else {
				$price_html = __( 'Free!', 'woocommerce-measurement-price-calculator' );
			}
		}

		return apply_filters( 'wc_measurement_price_calculator_get_pricing_rule_price_html', $price_html, $rule, $this );
	}


	/**
	 * Returns the calculator pricing unit, if this is a pricing calculator
	 *
	 * @return string pricing unit
	 */
	public function get_pricing_unit() {

		if ( $this->is_pricing_enabled() ) {

			$calculator_type = $this->get_calculator_type();

			if ( isset( $this->settings[ $calculator_type ]['pricing']['unit'] ) && $this->settings[ $calculator_type ]['pricing']['unit'] ) {
				return $this->settings[ $calculator_type ]['pricing']['unit'];
			}
		}

		return '';
	}


	/**
	 * Returns the calculator pricing overage, if this is a pricing calculator.
	 *
	 * @since 3.12.0
	 *
	 * @return float pricing overage percentage
	 */
	public function get_pricing_overage() {

		$overage = 0.0;

		if ( $this->is_pricing_enabled() ) {

			$calculator_type = $this->get_calculator_type();

			if ( isset( $this->settings[ $calculator_type ]['pricing']['overage'] ) && $this->settings[ $calculator_type ]['pricing']['overage'] ) {
				$overage = (float) $this->settings[ $calculator_type ]['pricing']['overage'] / 100;
			}
		}

		return $overage;
	}


	/**
	 * Return the calculator input accepted type.
	 *
	 * @since 3.12.0
	 *
	 * @param string $measurement_input
	 * @return string
	 */
	public function get_accepted_input( $measurement_input ) {

		$calculator_type = $this->get_calculator_type();
		$default_input   = 'free';

		if ( ! isset( $this->settings[ $calculator_type ] ) || ! isset( $this->settings[ $calculator_type ][ $measurement_input ] ) ) {
			return $default_input;
		}

		$measurement_settings = &$this->settings[ $calculator_type ][ $measurement_input ];

		if ( ! isset( $measurement_settings['accepted_input'] ) ) {

			// double-check if there were options, as this means the input is limited
			return ! empty( $measurement_settings['options'] ) ? 'limited' : $default_input;
		}

		return $measurement_settings['accepted_input'];
	}


	/**
	 * Return the calculator input accepted settings.
	 *
	 * @since 3.12.0
	 *
	 * @param string $measurement_input
	 * @return array
	 */
	public function get_input_attributes( $measurement_input ) {

		$calculator_type = $this->get_calculator_type();

		if ( ! isset( $this->settings[ $calculator_type ] ) || ! isset( $this->settings[ $calculator_type ][ $measurement_input ] ) ) {
			return array();
		}

		$measurement_settings = &$this->settings[ $calculator_type ][ $measurement_input ];

		if ( ! isset( $measurement_settings['accepted_input'] ) || 'free' !== $this->get_accepted_input( $measurement_input ) ) {
			return array();
		}

		if ( ! isset( $measurement_settings['input_attributes'] ) ) {
			return array();
		}

		return array_filter( wp_parse_args( $measurement_settings['input_attributes'], array(
			'min'  => '',
			'max'  => '',
			'step' => '',
		) ) );
	}


	/**
	 * Returns an array of option values for the given measurement.  This is
	 * used for the pricing calculator only.
	 *
	 * @since 3.0.0
	 *
	 * @param string $measurement_name the measurement name
	 * @return array associative array of measurement option values to label
	 */
	public function get_options( $measurement_name ) {

		$calculator_type = $this->get_calculator_type();
		$options         = array();

		if ( $this->is_pricing_calculator_enabled() && isset( $this->settings[ $calculator_type ][ $measurement_name ]['options'] ) && is_array( $this->settings[ $calculator_type ][ $measurement_name ]['options'] ) ) {

			foreach ( $this->settings[ $calculator_type ][ $measurement_name ]['options'] as $value ) {

				if ( '' !== $value ) {
					$result = \WC_Price_Calculator_Measurement::convert_to_float( $value );
					$options[ (string) $result ] = $value;
				}
			}
		}

		return $options;
	}


	/**
	 * Returns the calculator pricing label, if this is a pricing calculator.
	 * This is the label that would appear next to the price, as in: $10 ft.
	 *
	 * @return string pricing label
	 */
	public function get_pricing_label() {

		$pricing_label = '';

		if ( $this->is_pricing_enabled() ) {

			$calculator_type = $this->get_calculator_type();

			// default to the unit
			if ( isset( $this->settings[ $calculator_type ]['pricing']['unit'] ) && $this->settings[ $calculator_type ]['pricing']['unit'] ) {
				$pricing_label = $this->settings[ $calculator_type ]['pricing']['unit'];
			}

			// if a label has been configured, use that
			if ( isset( $this->settings[ $calculator_type ]['pricing']['label'] ) && $this->settings[ $calculator_type ]['pricing']['label'] ) {
				$pricing_label = $this->settings[ $calculator_type ]['pricing']['label'];
			}
		}

		return apply_filters( 'wc_measurement_price_calculator_pricing_label', $pricing_label, $this );
	}


	/**
	 * Returns a default settings array
	 *
	 * @return array default settings array
	 */
	private function get_default_settings() {

		// get the system units so we provide a nice convenient default
		$default_dimension_unit = get_option( 'woocommerce_dimension_unit' );
		$default_area_unit      = get_option( 'woocommerce_area_unit' );
		$default_volume_unit    = get_option( 'woocommerce_volume_unit' );
		// see the doc block for this method as to yuno get_option() {BR 2017-04-12}
		$default_weight_unit    = $this->get_woocommerce_weight_unit();

		$length     = esc_html__( 'Length', 'woocommerce-measurement-price-calculator' );
		$req_length = esc_html__( 'Required Length', 'woocommerce-measurement-price-calculator' );
		$width      = esc_html__( 'Width', 'woocommerce-measurement-price-calculator' );
		$req_width  = esc_html__( 'Required Width', 'woocommerce-measurement-price-calculator' );
		$height     = esc_html__( 'Height', 'woocommerce-measurement-price-calculator' );
		$req_height = esc_html__( 'Required Height', 'woocommerce-measurement-price-calculator' );
		$area       = esc_html__( 'Area', 'woocommerce-measurement-price-calculator' );
		$req_area   = esc_html__( 'Required Area', 'woocommerce-measurement-price-calculator' );
		$req_volume = esc_html__( 'Required Volume', 'woocommerce-measurement-price-calculator' );
		$req_weight = esc_html__( 'Required Weight', 'woocommerce-measurement-price-calculator' );
		$distance   = esc_html__( 'Distance around your room', 'woocommerce-measurement-price-calculator' );

		$settings = array(
			'calculator_type' => '',
			'dimension' => array(
				'pricing' => array( 'label' => '',          'unit' => $default_dimension_unit, 'enabled' => 'no', 'calculator' => array( 'enabled' => 'no', ), 'inventory' => array( 'enabled' => 'no', ), 'weight' => array( 'enabled' => 'no', ), ),
				'length'  => array( 'label' => $req_length, 'unit' => $default_dimension_unit, 'editable' => 'yes', 'enabled' => 'yes', 'options' => array(), ),
				'width'   => array( 'label' => $req_width,  'unit' => $default_dimension_unit, 'editable' => 'yes', 'enabled' => 'no', 'options' => array(), ),
				'height'  => array( 'label' => $req_height, 'unit' => $default_dimension_unit, 'editable' => 'yes', 'enabled' => 'no', 'options' => array(), ),
			),
			'area' => array(
				'pricing' => array( 'label' => '',        'unit' => $default_area_unit, 'enabled' => 'no', 'calculator' => array( 'enabled' => 'no', ), 'inventory' => array( 'enabled' => 'no', ), 'weight' => array( 'enabled' => 'no', ), ),
				'area'    => array( 'label' => $req_area, 'unit' => $default_area_unit, 'editable' => 'yes', 'options' => array(), ),
			),
			'area-dimension' => array(
				'pricing' => array( 'label' => '',      'unit' => $default_area_unit, 'enabled' => 'no', 'calculator' => array( 'enabled' => 'no', ), 'inventory' => array( 'enabled' => 'no', ), 'weight' => array( 'enabled' => 'no', ), ),
				'length'  => array( 'label' => $length, 'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
				'width'   => array( 'label' => $width,  'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
			),
			'area-linear' => array(
				'pricing' => array( 'label' => '',      'unit' => $default_dimension_unit, 'enabled' => 'no', 'calculator' => array( 'enabled' => 'no', ), 'inventory' => array( 'enabled' => 'no', ), 'weight' => array( 'enabled' => 'no', ), ),
				'length'  => array( 'label' => $length, 'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
				'width'   => array( 'label' => $width,  'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
			),
			'area-surface' => array(
				'pricing' => array( 'label' => '',      'unit' => $default_area_unit,      'enabled' => 'no', 'calculator' => array( 'enabled' => 'no', ), 'inventory' => array( 'enabled' => 'no', ), 'weight' => array( 'enabled' => 'no', ), ),
				'length'  => array( 'label' => $length, 'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
				'width'   => array( 'label' => $width,  'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
				'height'  => array( 'label' => $height, 'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
			),
			'volume' => array(
				'pricing' => array( 'label' => '',          'unit' => $default_volume_unit, 'enabled' => 'no', 'calculator' => array( 'enabled' => 'no', ), 'inventory' => array( 'enabled' => 'no', ), 'weight' => array( 'enabled' => 'no', ), ),
				'volume'  => array( 'label' => $req_volume, 'unit' => $default_volume_unit, 'editable' => 'yes', 'options' => array(), ),
			),
			'volume-dimension' => array(
				'pricing' => array( 'label' => '',      'unit' => $default_volume_unit, 'enabled' => 'no', 'calculator' => array( 'enabled' => 'no', ), 'inventory' => array( 'enabled' => 'no', ), 'weight' => array( 'enabled' => 'no', ), ),
				'length'  => array( 'label' => $length, 'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
				'width'   => array( 'label' => $width,  'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
				'height'  => array( 'label' => $height, 'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
			),
			'volume-area' => array(
				'pricing' => array( 'label' => '',      'unit' => $default_volume_unit,    'enabled' => 'no', 'calculator' => array( 'enabled' => 'no', ), 'inventory' => array( 'enabled' => 'no', ), 'weight' => array( 'enabled' => 'no', ), ),
				'area'    => array( 'label' => $area,   'unit' => $default_area_unit,      'editable' => 'yes', 'options' => array(), ),
				'height'  => array( 'label' => $height, 'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
			),
			'weight' => array(
				'pricing' => array( 'label' => '',          'unit' => $default_weight_unit, 'enabled' => 'no', 'calculator' => array( 'enabled' => 'no', ), 'inventory' => array( 'enabled' => 'no', ), 'weight' => array( 'enabled' => 'no', ), ),
				'weight'  => array( 'label' => $req_weight, 'unit' => $default_weight_unit, 'editable' => 'yes', 'options' => array(), ),
			),
			// just a special case area calculator
			'wall-dimension' => array(
				'pricing' => array( 'label' => '',        'unit' => $default_area_unit, 'enabled' => 'no', 'calculator' => array( 'enabled' => 'no', ), 'inventory' => array( 'enabled' => 'no', ), 'weight' => array( 'enabled' => 'no', ), ),
				'length'  => array( 'label' => $distance, 'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
				'width'   => array( 'label' => $height,   'unit' => $default_dimension_unit, 'editable' => 'yes', 'options' => array(), ),
			),
		);

		return $settings;
	}


	/**
	 * Return the WooCommerce weight unit. Copied from WP core get_option().
	 *
	 * We have to use a SQL query here to avoid filters, because of the way this class is instantiated --
	 *  we use it for the calculator product and product page classes, some of which filter the weight option.
	 *  As such, if we use get_option(), we get stuck in an infinite filter loop.
	 * We can remove this and use the get_option() call when WC 3.0+ is required, as there are other ways to filter
	 *  the weight unit at that point. {BR 2017-04-12}
	 *
	 * @since 3.11.3
	 * @return string option value
	 */
	protected function get_woocommerce_weight_unit() {
		global $wpdb;

		$row = $wpdb->get_row( "SELECT option_value FROM {$wpdb->options} WHERE option_name = 'woocommerce_weight_unit' LIMIT 1" );

		// Has to be get_row instead of get_var because of funkiness with 0, false, null values
		if ( is_object( $row ) ) {

			$value = $row->option_value;

		} else {

			// option does not exist; we shouldn't even get here, but if so, we can safely return the WC default in this case
			$value = 'kg';
		}

		return $value;
	}


	/**
	 * Returns an array with all the measurement types
	 *
	 * @since 3.0
	 * @return array of measurement type strings
	 */
	public static function get_measurement_types() {
		return array( 'dimension', 'area', 'area-dimension', 'area-linear', 'area-surface', 'volume', 'volume-dimension', 'volume-area', 'weight', 'wall-dimension' );
	}


	/**
	 * Over time it's expected that the settings datastructure will change, the
	 * purpose of this method is to safely ensure that the underlying settings
	 * structure always represents the latest
	 *
	 * @since 3.0
	 */
	private function update_settings() {

		if ( is_array( $this->settings ) ) {

			// pricing 'inventory', weight and 'calculator' sub-settings were added in version 3.0
			foreach ( $this->settings as $calculator_name => $calculator_settings ) {

				if ( is_array( $calculator_settings ) ) {

					foreach ( $calculator_settings as $setting_name => $values ) {

						if ( 'pricing' === $setting_name ) {

							if ( ! isset( $this->settings[ $calculator_name ][ $setting_name ]['inventory'] ) ) {
								$this->settings[ $calculator_name ][ $setting_name ]['inventory'] = array( 'enabled' => 'no' );
							}
							if ( ! isset( $this->settings[ $calculator_name ][ $setting_name ]['weight'] ) ) {
								$this->settings[ $calculator_name ][ $setting_name ]['weight'] = array( 'enabled' => 'no' );
							}
							if ( ! isset( $this->settings[ $calculator_name ][ $setting_name ]['calculator'] ) ) {
								$this->settings[ $calculator_name ][ $setting_name ]['calculator'] = array( 'enabled' => 'no' );
							}
						}
					}
				}
			}

			// measurement 'options' setting (defaults to array()) was added in version 3.0
			foreach ( $this->settings as $calculator_name => $calculator_settings ) {

				if ( is_array( $calculator_settings ) ) {

					foreach ( $calculator_settings as $setting_name => $values ) {

						if ( 'pricing' !== $setting_name && ! isset( $this->settings[ $calculator_name ][ $setting_name ]['options'] ) ) {
							$this->settings[ $calculator_name ][ $setting_name ]['options'] = array();
						}
					}
				}
			}
		}
	}

	/**
	 * Product input(s) cookie name.
	 *
	 * @since 3.12.0
	 *
	 * @return string
	 */
	public function get_product_inputs_cookie_name() {
		return 'wc_price_calc_inputs_' . $this->product->get_id();
	}
}
