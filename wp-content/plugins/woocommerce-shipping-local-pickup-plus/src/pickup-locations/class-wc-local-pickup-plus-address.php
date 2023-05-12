<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments\Timezones;

/**
 * Pickup location address object.
 *
 * - Normalizes an address and its parts as an object.
 * - Comes with helper methods to convert a country or a state code to their corresponding long names.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Address {


	/** @var int ID of the corresponding pickup location */
	private $location_id;

	/** @var string country */
	private $country = '';

	/** @var string state */
	private $state = '';

	/** @var string postcode */
	private $postcode = '';

	/** @var string city */
	private $city = '';

	/** @var string address line 1 */
	private $address_1 = '';

	/** @var string address line 2 */
	private $address_2 = '';

	/* @var string place name */
	private $name = '';


	/**
	 * Address constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param array $address address in array format
	 * @param int $location_id optional, ID of the corresponding pickup location (useful to pass in hooks)
	 */
	public function __construct( $address = array(), $location_id = 0 ) {

		$address = $this->parse_address( $address );

		$this->set_properties( $address );

		$this->location_id = (int) $location_id;
	}


	/**
	 * Set address pieces for the current object.
	 *
	 * @since 2.0.0
	 *
	 * @param array $address associative array
	 */
	private function set_properties( $address ) {

		foreach ( $address as $key => $value ) {

			if ( 'location_id' !== $key && property_exists( $this, $key ) ) {

				// country and state codes must be exactly 2-digits long
				if ( in_array( $key, array( 'country', 'state' ), true ) ) {

					$this->$key = '';

					if ( strlen( $key ) !== 2 ) {
						$this->$key = strtoupper( $value );
					}

				} else {

					$this->$key = $value;
				}
			}
		}
	}


	/**
	 * Parse address.
	 *
	 * @since 2.0.0
	 *
	 * @param array $address associative array
	 * @return array
	 */
	private function parse_address( $address = array() ) {

		$address = wp_parse_args( $address, array(
			'name'      => '',
			'country'   => '',
			'state'     => '',
			'postcode'  => '',
			'city'      => '',
			'address_1' => '',
			'address_2' => '',
		) );

		return $address;
	}


	/**
	 * Set address.
	 *
	 * @since 2.0.0
	 *
	 * @param array $address associative array
	 */
	public function set_address( array $address ) {

		$address = $this->parse_address( $address );

		$this->set_properties( $address );
	}


	/**
	 * Get the address in array format.
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array
	 */
	public function get_array() {
		return array(
			'country'   => $this->get_country(),
			'state'     => $this->get_state(),
			'postcode'  => $this->get_postcode(),
			'city'      => $this->get_city(),
			'address_1' => $this->get_address_line_1(),
			'address_2' => $this->get_address_line_2(),
		);
	}


	/**
	 * Get the address in HTML according to location's country format.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $one_line whether to return address as a single line (true) or multiple lines with line breaks (false, default)
	 * @return string HTML
	 */
	public function get_formatted_html( $one_line = false ) {

		$formatted = '';

		if ( $this->get_country() ) {

			// pass empty defaults to WC otherwise we might get a bunch of notices
			$formatted = WC()->countries->get_formatted_address( array_merge( array(
				'first_name' => null,
				'last_name'  => null,
				'country'    => null,
				'state'      => null,
			), $this->get_array() ) );

			if ( true === $one_line ) {
				$formatted = str_replace( array( '<br>', '<br/>', '<br />', "\n" ), ' ', $formatted );
			}
		}

		return $formatted;
	}


	/**
	 * Get the country code.
	 *
	 * @since 2.0.0
	 *
	 * @return string a two or three characters code
	 */
	public function get_country() {

		return $this->country;
	}


	/**
	 * Get the country name.
	 *
	 * @since 2.0.0
	 *
	 * @return string country full name
	 */
	public function get_country_name() {

		$country = $this->get_country();

		if ( ! empty( $country ) ) {
			$countries = WC()->countries->get_countries();
			$country    = isset( $countries[ $country ] ) ? $countries[ $country ] : '';
		}

		return stripslashes( $country );
	}


	/**
	 * Get the state code.
	 *
	 * @since 2.0.0
	 *
	 * @return string a two or three characters code
	 */
	public function get_state() {

		return $this->state;
	}


	/**
	 * Checks whether the address has a state code.
	 *
	 * @since 2.3.15
	 *
	 * @return bool
	 */
	public function has_state() {

		$state = $this->get_state();

		return ! empty( $state );
	}


	/**
	 * Get the state name.
	 *
	 * @since 2.0.0
	 *
	 * @return string state full name
	 */
	public function get_state_name() {

		$state = $this->get_state();

		if ( ! empty( $state ) && ( $country = $this->get_country() ) ) {
			$states = WC()->countries->get_states( $country );
			$state  = isset( $states[ $state ] ) ? $states[ $state ] : '';
		}

		return stripslashes( $state );
	}


	/**
	 * Get a country-state code for this address.
	 *
	 * @since 2.0.0
	 *
	 * @param string $sep Optional separator, defaults to ":" colon standard used in WooCommerce
	 *
	 * @return string single 2-digit code or couple of 2-digit codes separated by $sep
	 */
	public function get_country_state_code( $sep = ':' ) {

		$code = '';

		if ( '' !== $this->country ) {
			$code = '' === $this->state ? $this->country : $this->country . $sep . $this->state;
		}

		return $code;
	}


	/**
	 * Get the postcode.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_postcode() {
		return stripslashes( $this->postcode );
	}


	/**
	 * Checks whether the location has a postcode.
	 *
	 * @since 2.3.15
	 *
	 * @return bool
	 */
	public function has_postcode() {

		$postcode = $this->get_postcode();

		return ! empty( $postcode );
	}


	/**
	 * Get the address line 1.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_address_line_1() {
		return stripslashes( $this->address_1 );
	}


	/**
	 * Get the address line 2.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_address_line_2() {
		return stripslashes( $this->address_2 );
	}


	/**
	 * Get the address.
	 *
	 * @since 2.0.0
	 *
	 * @param string $format either 'array' or 'string'
	 * @param string $separator optional, valid for 'string' return format, default `<br>` HTML line break
	 * @return array|string|null address in the specified $format
	 */
	public function get_street_address( $format = 'array', $separator = '<br>' ) {

		$address = array_unique( array(
			$this->get_address_line_1(),
			$this->get_address_line_2(),
		) );

		if ( 'array' === $format ) {
			return $address;
		} elseif ( 'string' === $format && is_string( $separator ) ) {
			return implode( $separator, $address );
		}

		return null;
	}


	/**
	 * Checks whether the location has a non-empty street address.
	 *
	 * @since 2.3.15
	 *
	 * @return bool
	 */
	public function has_street_address() {

		$address = $this->get_street_address();

		return ! empty( $address );
	}


	/**
	 * Get the city.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_city() {
		return stripslashes( $this->city );
	}


	/**
	 * Checks whether the location has a city defined.
	 *
	 * @since 2.3.15
	 *
	 * @return bool
	 */
	public function has_city() {

		$city = $this->get_city();

		return ! empty( $city );
	}


	/**
	 * Get the place name.
	 *
	 * @since 2.1.1
	 *
	 * @return string
	 */
	public function get_name() {
		return stripslashes( $this->name );
	}


	/**
	 * Gets the ID of a pickup location associated with the address, if any
	 *
	 * @since 2.7.0
	 *
	 * @return int
	 */
	public function get_pickup_location_id() {

		return $this->location_id;
	}


	/**
	 * Gets a pickup location associated with the address, if any
	 *
	 * @since 2.7.0
	 *
	 * @return WC_Local_Pickup_Plus_Pickup_Location|null
	 */
	public function get_pickup_location() {

		return $this->location_id > 0 ? wc_local_pickup_plus_get_pickup_location( $this->location_id ) : null;
	}


	/**
	 * Gets the timezone matching the address.
	 *
	 * @since 2.7.0
	 *
	 * @return \DateTimeZone defaults to site timezone
	 */
	public function get_timezone() {

		return Timezones::get_timezone_from_address( $this );
	}


}
