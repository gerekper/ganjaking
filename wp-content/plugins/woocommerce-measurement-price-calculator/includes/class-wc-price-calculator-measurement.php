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
 * This class represents a product physical attribute measurement, consisting
 * of a name, value and unit, and makes it easy to convert between units.  Also
 * contains whether this measurement is editable within the calculator
 *
 * @since 1.0
 */
class WC_Price_Calculator_Measurement {

	/** @var mixed $value the measurement value, int or float */
	private $value;

	/** @var string $unit the measurement unit abbreviation, ie 'sq. in.', 'ml', 'tn', etc */
	private $unit;

	/** @var string $common_unit the common unit for this measurement */
	private $common_unit;

	/** @var string $name the measurement name, one of 'length', 'width', 'height', 'area', 'volume', 'weight' */
	private $name;

	/** @var string $label the measurement label, which is displayed for the customer */
	private $label;

	/** @var boolean $editable whether this measurement is editable in the price calculator */
	private $is_editable;

	/** @var array $options associative array of option value to option label */
	private $options;

	/** @var array All units to their corresponding standard unit */
	private static $normalize_table;

	/** @var array All standard units to all other units */
	private static $conversion_table;


	/**
	 * __construct function.
	 *
	 * @param string $unit the measurement unit abbreviation, ie 'sq. in.', 'ml', 'tn', etc
	 * @param int|float $value $value the measurement value, int or float. Defaults to 1
	 * @param string $name optional measurement name, one of 'length', 'width', 'height', 'area', 'volume', 'weight'
	 * @param string $label optional label to display on the frontend
	 * @param string $is_editable optional "yes"/"no" whether this measurement is editable in the price calculator.  Defaults to 'no'
	 * @param array $options array of options for the frontend
	 */
	public function __construct( $unit, $value = 1, $name = '', $label = '', $is_editable = 'no', $options = array() ) {

		$this->unit        = $unit;
		$this->value       = $value;
		$this->name        = $name;
		$this->label       = $label;
		$this->is_editable = $is_editable;
		$this->options     = $options;
	}


	/**
	 * Sets the measurement value to $value.  $value must be in the current
	 * measurement units
	 *
	 * @param int|float $value measurement value
	 */
	public function set_value( $value ) {
		$this->value = $value;
	}


	/**
	 * Get the measurement value optionally converted to the specified unit.
	 * If no $unit is supplied, the measurement value is returned in the current
	 * measurement units
	 *
	 * @param string $unit optional unit to return the measurement value in
	 * @return int|float the measurement value, int or float
	 */
	public function get_value( $unit = null ) {
		return ! $unit ? $this->value : self::convert( $this->value, $this->unit, $unit );
	}


	/**
	 * Gets the measurement value converted (as needed) to the configured common
	 * unit, used when deriving compound measurments (ie area, volume).
	 *
	 * @return int|float the measurement value converted to common units
	 */
	public function get_value_common() {
		return self::convert( $this->value, $this->unit, $this->get_unit_common() );
	}


	/**
	 * Returns the common unit for this measurement
	 *
	 * @since 3.0
	 * @return string the common unit for this measurement, ie 'sq. ft.' or 'sq m', etc
	 */
	public function get_unit_common() {

		// default to the standard unit if not set
		if ( null === $this->common_unit ) {
			$this->common_unit = self::get_standard_unit( $this->unit );
		}

		return $this->common_unit;
	}


	/**
	 * Sets the common unit to use for this measurement.  If the dimension of
	 * the supplied $common_unit is incorrect for this measurement type it will
	 * be converted as needed, ie if the measurement type is 'area' and 'ft'
	 * is passed for $common_unit it will be converted to 'sq. ft.'
	 *
	 * @since 3.0
	 * @param string $common_unit the common unit, ie 'ft', 'sq. ft.', etc
	 * @return string the common unit, which may or may not be the same as $common_unit
	 */
	public function set_common_unit( $common_unit ) {

		// ensure the supplied unit is in the correct dimensions, ie 'ft', 'sq. ft.' or 'cu. ft.'
		switch( $this->name ) {
			case 'length':
			case 'width':
			case 'height':
				$common_unit = self::to_dimension_unit( $common_unit );
			break;
			case 'area':
				$common_unit = self::to_area_unit( $common_unit );
			break;
			case 'volume':
				$common_unit = self::to_volume_unit( $common_unit );
			break;
		}

		$this->common_unit = self::get_standard_unit( $common_unit );

		return $this->common_unit;
	}


	/**
	 * Sets the measurement's unit and changes the measurement value if needed
	 *
	 * @param string $unit the unit to convert to, ie 'sq. in.', 'ml', 'tn', etc
	 * @return int|float returns the measurement in the new units
	 */
	public function set_unit( $unit ) {

		// convert to $unit and set
		$this->value = self::convert( $this->value, $this->unit, $unit );
		$this->unit  = $unit;

		// return the value in the new units
		return $this->value;
	}


	/**
	 * Get the measurement unit
	 *
	 * @return string the measurement value
	 */
	public function get_unit() {
		return $this->unit;
	}


	/**
	 * Get the measurement unit for display on the frontend
	 *
	 * @since 3.0.1
	 * @return string the measurement unit label for display on the frontend
	 */
	public function get_unit_label() {

		/**
		 * Filter the measurement unit frontend label
		 *
		 * @since 3.0.1
		 * @param string $label The measurement unit frontend label.
		 * @param \WC_Price_Calculator_Measurement $measurement instance of this class
		 */
		return apply_filters( 'wc_measurement_price_calculator_unit_label', $this->unit, $this );
	}


	/**
	 * Get the measurement name, one of 'length', 'width', 'height', 'area', 'volume', 'weight'
	 *
	 * @see get_type()
	 * @return string the measurement name
	 */
	public function get_name() {
		return $this->name;
	}


	/**
	 * Gets the type of measurement, one of 'dimension', 'area', 'volume' or 'weight'
	 *
	 * @return string type of measurement
	 */
	public function get_type() {

		switch ( $this->get_name() ) {
			case 'length':
			case 'width':
			case 'height': return 'dimension';
			default: return $this->get_name();
		}
	}


	/**
	 * Returns true if this measurement is editable by the customer in the frontend
	 * calculator
	 *
	 * @return boolean true if this measurement is editable in the price calculator
	 */
	public function is_editable() {
		return 'yes' === $this->is_editable;
	}


	/**
	 * Returns the measurement frontend label
	 *
	 * @return string measurement label
	 */
	public function get_label() {

		/**
		 * Filter the measurement frontend label
		 *
		 * @since 3.4.0
		 * @param string $label The measurement frontend label
		 * @param \WC_Price_Calculator_Measurement $measurement instance of this class
		 */
		return apply_filters( 'wc_measurement_price_calculator_label', $this->label, $this );
	}


	/**
	 * Returns an array of options for the measurement for the frontend.
	 *
	 * @since 3.0
	 * @return array of option values
	 */
	public function get_options() {

		/**
		 * Filters the array of options for the measurement
		 *
		 * @since 3.10.1
		 * @param array $options array of option values
		 * @param \WC_Price_Calculator_Measurement $measurement instance of this class
		 */
		return apply_filters( 'wc_measurement_price_calculator_measurement_options', $this->options, $this );
	}


	/**
	 * Helper function to convert the measurement value to the given unit, or the
	 * default unit
	 *
	 * @since 3.0
	 * @param int|float $value the value to convert
	 * @param string $unit the unit, ie 'sq. in.', 'ml', 'tn', etc
	 * @param string $to_unit the unit to convert to, ie 'sq. in.', 'ml', 'tn', etc.
	 * @return int|float returns the converted measurement value
	 */
	public static function convert( $value, $unit, $to_unit ) {

		if ( is_string( $value ) ) {
			$value = (float) $value;
		}

		// all units to their corresponding standard unit
		$normalize_table = self::get_normalize_table();

		// conversions from our standard units to all other units
		$conversion_table = self::get_conversion_table();

		// convert from $unit to the corresponding standard unit
		if ( isset( $normalize_table[ $unit ] ) ) {

			if ( isset( $normalize_table[ $unit ]['inverse'] ) && $normalize_table[ $unit ]['inverse'] ) {
				$value /= $normalize_table[ $unit ]['factor'];
			} else {
				$value *= $normalize_table[ $unit ]['factor'];
			}

			$unit = $normalize_table[ $unit ]['unit'];
		}

		// convert from the standard unit to $to_unit
		if ( isset( $conversion_table[ $unit ][ $to_unit ] ) ) {

			if ( isset( $conversion_table[ $unit ][ $to_unit ]['inverse'] ) && $conversion_table[ $unit ][ $to_unit ]['inverse'] ) {
				$value /= $conversion_table[ $unit ][ $to_unit ]['factor'];
			} else {
				$value *= $conversion_table[ $unit ][ $to_unit ]['factor'];
			}
		}

		return $value;
	}


	/**
	 * Converts a string that could be a number or a fraction to a numerical float.
	 *
	 * @since 3.4.0
	 *
	 * @param string|int|float $value the value to convert, it could be a number (integer, float) or a fraction
	 * @return string|float will output a float when using dot as decimal separator, a numerical string in all other cases
	 */
	public static function convert_to_float( $value ) {

		$fraction    = null;
		$decimal_sep = trim( wc_get_price_decimal_separator() );

		// we got a measurement fraction, like "2 1/2" (two and half inches, for example)
		if ( preg_match('#(\d+)\s+(\d+)\/(\d+)#', $value, $matches ) ) {
			$fraction = (float) $matches[3] !== 0 ? $matches[1] + ( $matches[2] / $matches[3] ) : $matches[1];
		// we got a simple fraction, like "3/4" (three fourths of a gallon, for example)
		} elseif ( preg_match('#(\d+)\/(\d+)#', $value, $matches ) ) {
			$fraction = (float) $matches[2] !== 0 ? $matches[1] / $matches[2] : 0;
		}

		if ( null !== $fraction ) {

			// account for comma or alternative separators, for the correct value to return
			if  ( '.' !== $decimal_sep ) {
				$fraction = str_replace( '.', $decimal_sep, (string) $fraction );
			}

			$value = (string) $fraction;
		}

		// if not using the dot as decimal separator, let this be handled by JS, otherwise caste as float
		return '.' !== $decimal_sep ? $value : (float) $value;
	}


	/**
	 * Returns the standard unit for $unit.  Ie, for 'sq. in.', 'sq. ft.',
	 * or 'acs' this will return 'sq. ft.', while 'sq mm' will return 'sq m'
	 *
	 * @since 3.0
	 * @param string $unit the unit
	 * @return string the standard unit or null if none is found
	 */
	public static function get_standard_unit( $unit ) {

		$normalize_table = self::get_normalize_table();

		if ( isset( $normalize_table[ $unit ]['unit'] ) ) {
			return $normalize_table[ $unit ]['unit'];
		}

		return null;
	}


	/**
	 * Returns a conversion table which has conversion factors to normalize
	 * any given measurement unit to one of only a few others, to simplify
	 * the challenge of unit conversion
	 *
	 * @since 3.0
	 * @return array of conversion factors
	 */
	public static function get_normalize_table() {

		if ( null === self::$normalize_table ) {

			self::$normalize_table = apply_filters( 'wc_measurement_price_calculator_normalize_table',
				array(
					'in' => array( 'factor' => 12, 'unit' => 'ft', 'inverse' => true ),
					'ft' => array( 'factor' => 1,    'unit' => 'ft' ),
					'yd' => array( 'factor' => 3,    'unit' => 'ft' ),
					'mi' => array( 'factor' => 5280, 'unit' => 'ft' ),

					'mm' => array( 'factor' => 0.001, 'unit' => 'm' ),
					'cm' => array( 'factor' => 0.01,  'unit' => 'm' ),
					'm'  => array( 'factor' => 1,     'unit' => 'm' ),
					'km' => array( 'factor' => 1000,  'unit' => 'm' ),

					'sq. in.' => array( 'factor' => 144,    'unit' => 'sq. ft.', 'inverse' => true ),
					'sq. ft.' => array( 'factor' => 1,        'unit' => 'sq. ft.' ),
					'sq. yd.' => array( 'factor' => 9,        'unit' => 'sq. ft.' ),
					'acs'     => array( 'factor' => 43560,    'unit' => 'sq. ft.' ),
					'sq. mi.' => array( 'factor' => 27878400, 'unit' => 'sq. ft.' ),

					'sq mm' => array( 'factor' => 0.000001, 'unit' => 'sq m' ),
					'sq cm' => array( 'factor' => 0.0001,   'unit' => 'sq m' ),
					'sq m'  => array( 'factor' => 1,        'unit' => 'sq m' ),
					'ha'    => array( 'factor' => 10000,    'unit' => 'sq m' ),
					'sq km' => array( 'factor' => 1000000,  'unit' => 'sq m' ),

					'fl. oz.' => array( 'factor' => 1,   'unit' => 'fl. oz.' ),
					'cup'     => array( 'factor' => 8,   'unit' => 'fl. oz.' ),
					'pt'      => array( 'factor' => 16,  'unit' => 'fl. oz.' ),
					'qt'      => array( 'factor' => 32,  'unit' => 'fl. oz.' ),
					'gal'     => array( 'factor' => 128, 'unit' => 'fl. oz.' ),
					'cu. in.' => array( 'factor' => 1728,'unit' => 'cu. ft.', 'inverse' => true ),
					'cu. ft.' => array( 'factor' => 1,   'unit' => 'cu. ft.' ),
					'cu. yd.' => array( 'factor' => 27,  'unit' => 'cu. ft.' ),

					'ml'    => array( 'factor' => 0.000001, 'unit' => 'cu m' ),
					'cu cm' => array( 'factor' => 0.000001, 'unit' => 'cu m' ),
					'l'     => array( 'factor' => 0.001,    'unit' => 'cu m' ),
					'cu m'  => array( 'factor' => 1,        'unit' => 'cu m' ),

					'oz'  => array( 'factor' => 16, 'unit' => 'lbs', 'inverse' => true ),
					'lbs' => array( 'factor' => 1,    'unit' => 'lbs' ),
					'tn'  => array( 'factor' => 2000, 'unit' => 'lbs' ),

					'g'  => array( 'factor' => 0.001, 'unit' => 'kg' ),
					'kg' => array( 'factor' => 1,     'unit' => 'kg' ),
					't'  => array( 'factor' => 1000,  'unit' => 'kg' ),
				)
			);
		}

		return self::$normalize_table;
	}


	/**
	 * Returns a conversion table which has conversion factors from our limited
	 * "standard" units to all other units.
	 *
	 * The rather verbose tables found in this method and the associated
	 * get_normalize_table() method are used to provide as simple and accurate
	 * of a measurement unit conversion as possible.  Previously all units were
	 * converted to a much smaller number of "standard" units, ie all lengths
	 * were converted to inches first and then to the final unit.  This led to
	 * rounding issues when converting between units of different systems of
	 * measurement.  For instance, 2 m * 2 m => 3.999999999 m
	 * With this more complex setup, conversions within a single system of
	 * measurement will be accurate and correct, and while the rounding issue
	 * will still remain when converting from say feet to meters, this should
	 * be a very uncommon (or hopefully non-existant) occurrence.
	 *
	 * @since 3.0
	 * @return array of conversion factors
	 */
	public static function get_conversion_table() {

		if ( null === self::$conversion_table ) {

			self::$conversion_table = apply_filters( 'wc_measurement_price_calculator_conversion_table',
				array(
					'ft' => array(
						'in' => array( 'factor' => 12 ),
						'ft' => array( 'factor' => 1 ),
						'yd' => array( 'factor' => 3,    'inverse' => true ),
						'mi' => array( 'factor' => 5280, 'inverse' => true ),
						'mm' => array( 'factor' => 304.8 ),
						'cm' => array( 'factor' => 30.48 ),
						'm'  => array( 'factor' => 0.3048 ),
						'km' => array( 'factor' => 0.0003048 ),
					),
					'm' => array(
						'mm' => array( 'factor' => 1000 ),
						'cm' => array( 'factor' => 100 ),
						'm'  => array( 'factor' => 1 ),
						'km' => array( 'factor' => 0.001 ),
						'in' => array( 'factor' => 39.3701 ),
						'ft' => array( 'factor' => 3.28084 ),
						'yd' => array( 'factor' => 1.09361 ),
						'mi' => array( 'factor' => 0.000621371 ),
					),
					'sq. ft.' => array(
						'sq. in.' => array( 'factor' => 144 ),
						'sq. ft.' => array( 'factor' => 1 ),
						'sq. yd.' => array( 'factor' => 9,        'inverse' => true ),
						'acs'     => array( 'factor' => 43560,    'inverse' => true ),
						'sq. mi.' => array( 'factor' => 27878400, 'inverse' => true ),
						'sq mm'   => array( 'factor' => 92903.04 ),
						'sq cm'   => array( 'factor' => 929.0304 ),
						'sq m'    => array( 'factor' => 0.092903 ),
						'sq km'   => array( 'factor' => 0.000000092903 ),
					),
					'sq m' => array(
						'sq mm'   => array( 'factor' => 1000000 ),
						'sq cm'   => array( 'factor' => 10000 ),
						'sq m'    => array( 'factor' => 1 ),
						'ha'      => array( 'factor' => 0.0001 ),
						'sq km'   => array( 'factor' => 0.000001 ),
						'sq. in.' => array( 'factor' => 1550 ),
						'sq. ft.' => array( 'factor' => 10.7639 ),
						'sq. yd.' => array( 'factor' => 1.19599 ),
						'acs'     => array( 'factor' => 0.000247105 ),
						'sq. mi.' => array( 'factor' => 0.000000386102 ),
					),
					'fl. oz.' => array(
						'fl. oz.' => array( 'factor' => 1 ),
						'cup'     => array( 'factor' => 8,   'inverse' => true ),
						'pt'      => array( 'factor' => 16,  'inverse' => true ),
						'qt'      => array( 'factor' => 32,  'inverse' => true ),
						'gal'     => array( 'factor' => 128, 'inverse' => true ),
						'cu. in.' => array( 'factor' => 231/128 ),
						'cu. ft.' => array( 'factor' => 0.00104438 ),
						'cu. yd.' => array( 'factor' => 0.0000386807163 ),
						'ml'      => array( 'factor' => 29.5735 ),
						'cu cm'   => array( 'factor' => 29.5735 ),
						'l'       => array( 'factor' => 0.0295735 ),
						'cu m'    => array( 'factor' => 0.0000295735 ),
					),
					'cu. ft.' => array(
						'fl. oz.' => array( 'factor' => 957.506 ),
						'cup'     => array( 'factor' => 119.688 ),
						'pt'      => array( 'factor' => 59.8442 ),
						'qt'      => array( 'factor' => 29.9221 ),
						'gal'     => array( 'factor' => 7.48052 ),
						'cu. in.' => array( 'factor' => 1728 ),
						'cu. ft.' => array( 'factor' => 1 ),
						'cu. yd.' => array( 'factor' => 27, 'inverse' => true ),
						'ml'      => array( 'factor' => 28316.8466 ),
						'cu cm'   => array( 'factor' => 28316.8466 ),
						'l'       => array( 'factor' => 28.3168466 ),
						'cu m'    => array( 'factor' => 0.0283168466 ),
					),
					'cu m' => array(
						'ml'      => array( 'factor' => 1000000 ),
						'cu cm'   => array( 'factor' => 1000000 ),
						'l'       => array( 'factor' => 1000 ),
						'cu m'    => array( 'factor' => 1 ),
						'fl. oz.' => array( 'factor' => 33814 ),
						'cup'     => array( 'factor' => 4226.75 ),
						'pt'      => array( 'factor' => 2113.38 ),
						'qt'      => array( 'factor' => 1056.69 ),
						'gal'     => array( 'factor' => 264.172 ),
						'cu. in.' => array( 'factor' => 61023.7 ),
						'cu. ft.' => array( 'factor' => 35.3147 ),
						'cu. yd.' => array( 'factor' => 1.30795062 ),
					),
					'lbs' => array(
						'oz'  => array( 'factor' => 16 ),
						'lbs' => array( 'factor' => 1 ),
						'tn'  => array( 'factor' => 2000, 'inverse' => true ),
						'g'   => array( 'factor' => 453.592 ),
						'kg'  => array( 'factor' => 0.453592 ),
						't'   => array( 'factor' => 0.000453592 ),
					),
					'kg' => array(
						'g'   => array( 'factor' => 1000 ),
						'kg'  => array( 'factor' => 1 ),
						't'   => array( 'factor' => 0.001 ),
						'oz'  => array( 'factor' => 35.274 ),
						'lbs' => array( 'factor' => 2.20462 ),
						'tn'  => array( 'factor' => 0.00110231 ),
					),
				)
			);
		}

		return self::$conversion_table;
	}


	/**
	 * Given a unit, returns the corresponding dimensional unit.  For instance
	 * a unit of 'sq. ft.' or 'cu. ft.' returns 'ft'
	 *
	 * @since 3.0
	 * @param string $unit the unit, it 'sq. ft.'
	 * @return string corresponding dimensional unit, ie 'ft'
	 */
	public static function to_dimension_unit( $unit ) {

		switch( $unit ) {
			case 'mm':
			case 'sq mm': return 'mm';
			case 'cm':
			case 'sq cm':
			case 'ml':
			case 'cu cm': return 'cm';
			case 'm':
			case 'sq m':
			case 'cu m':
			case 'ha': return 'm';  // special case: hectare
			case 'km':
			case 'sq km': return 'km';
			case 'in':
			case 'sq. in.':
			case 'cu. in.': return 'in';
			case 'ft':
			case 'sq. ft.':
			case 'cu. ft.':
			case 'acs': return 'ft';  // special case: acres
			case 'yd':
			case 'sq. yd.':
			case 'cu. yd.': return 'yd';
			case 'mi':
			case 'sq. mi.': return 'mi';
			default : return $unit;
		}
	}


	/**
	 * Given a unit, returns the corresponding area unit.  For instance,
	 * a $unit of 'in' returns 'sq. in.'
	 *
	 * @param string $unit ie 'in'
	 * @return string corresponding area unit, ie 'sq. in.'
	 */
	public static function to_area_unit( $unit ) {

		switch( $unit ) {
			case 'mm':
			case 'sq mm': return 'sq mm';
			case 'cm':
			case 'sq cm':
			case 'ml':
			case 'cu cm': return 'sq cm';
			case 'm':
			case 'sq m':
			case 'cu m': return 'sq m';
			case 'km':
			case 'sq km': return 'sq km';
			case 'in':
			case 'sq. in.':
			case 'cu. in.': return 'sq. in.';
			case 'ft':
			case 'sq. ft.':
			case 'cu. ft.': return 'sq. ft.';
			case 'yd':
			case 'sq. yd.':
			case 'cu. yd.': return 'sq. yd.';
			case 'mi':
			case 'sq. mi.': return 'sq. mi.';

			// hectare and acres
			case 'ha':  return 'ha';
			case 'acs': return 'acs';

			default: return apply_filters( 'wc_measurement_price_calculator_to_area_unit', null, $unit );
		}
	}


	/**
	 * Given a unit, returns the corresponding volume unit.  For instance,
	 * a $unit of 'in' or 'sq. in.' returns 'cu. in.'
	 *
	 * @param string $unit ie 'in'
	 * @return string corresponding volume unit, ie 'cu. in.'
	 */
	public static function to_volume_unit( $unit ) {

		switch( $unit ) {
			case 'cm':
			case 'sq cm':
			case 'ml':
			case 'cu cm': return 'ml';
			case 'm':
			case 'sq m':
			case 'cu m': return 'cu m';
			case 'in':
			case 'sq. in.':
			case 'cu. in.': return 'cu. in.';
			case 'ft':
			case 'sq. ft.':
			case 'cu. ft.': return 'cu. ft.';
			case 'yd':
			case 'sq. yd.':
			case 'cu. yd.': return 'cu. yd.';

			// no length or area units
			case 'l':       return 'l';
			case 'gal':     return 'gal';
			case 'qt':      return 'qt';
			case 'pt':      return 'pt';
			case 'cup':     return 'cup';
			case 'fl. oz.': return 'fl. oz.';

			default: return apply_filters( 'wc_measurement_price_calculator_to_volume_unit', null, $unit );
		}
	}


	/**
	 * Compare units, disregarding measurement type (length/area/volume/weight)
	 * meaning that 'in' will compare as true to 'in', 'sq. in.' and 'cu. in.',
	 * etc.
	 *
	 * @param string $unit1 first unit to compare
	 * @param string $unit2 second unit to compare
	 * @return bool if the units are the 'same'
	 */
	public static function compare_units( $unit1, $unit2 ) {

		$compare = false;

		// straight comparison
		if ( $unit1 === $unit2 ) {
			$compare = true;
		}

		// match found as area units (ie perhaps 'in' and 'sq. in.')  This also covers volume units
		if (    \WC_Price_Calculator_Measurement::to_area_unit( $unit1 ) && \WC_Price_Calculator_Measurement::to_area_unit( $unit2 )
		     && \WC_Price_Calculator_Measurement::to_area_unit( $unit1 ) === \WC_Price_Calculator_Measurement::to_area_unit( $unit2 ) ) {

			$compare = true;
		}

		return $compare;
	}


}
