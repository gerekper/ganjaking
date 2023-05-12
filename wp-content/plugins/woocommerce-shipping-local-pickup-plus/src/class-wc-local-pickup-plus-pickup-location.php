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
use SkyVerge\WooCommerce\Local_Pickup_Plus\Pickup_Locations\Pickup_Location as Pickup_Location;

/**
 * Local Pickup Location object.
 *
 * This class is initially built as a wrapper around an instance of WP_Post with the 'wc_pickup_location' custom post type, similar to how WC_Product or WC_Order are implemented.
 *
 * This object represents a single location with its specific configuration and properties, some required as the address (at least a country/state must be defined), others optional as phone, business hours and calendar to schedule a pickup, products available at the location and so on.
 * Each property has helper methods to set, get and delete the related object meta.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Pickup_Location {


	/** @var int location (post) unique ID */
	protected $id = 0;

	/** @var \WP_Post location post object */
	protected $post;

	/** @var string location name (post title) */
	protected $name = '';

	/** @var string location (post) slug */
	protected $slug = '';

	/** @var array location coordinates */
	protected $coordinates = array();

	/** @var \WC_Local_Pickup_Plus_Address location address */
	protected $address;

	/** @var string address post meta key name */
	protected $address_meta = '_pickup_location_address';

	/** @var string phone number post meta key name */
	protected $phone_meta = '_pickup_location_phone';

	/** @var string price adjustment post meta key name */
	protected $price_adjustment_meta = '_pickup_location_price_adjustment';

	/** @var string scheduled appointment required lead time post meta key name */
	protected $lead_time_meta = '_pickup_location_pickup_appointment_lead_time';

	/** @var string pickup deadline to schedule an appointment post meta key name */
	protected $deadline_meta = '_pickup_location_pickup_appointment_deadline';

	/** @var string business hours post meta key name */
	protected $business_hours_meta = '_pickup_location_business_hours';

	/** @var string public holidays post meta key name */
	protected $public_holidays_meta = '_pickup_location_public_holidays';

	/** @var string email recipients post meta key name */
	protected $email_recipients_meta = '_pickup_location_email_recipients';

	/** @var string products availability post meta key name */
	protected $products_meta = '_pickup_location_products';

	/** @var null|int[] cached array of product IDs to flag products available at this location */
	protected $products;

	/** @var null|int[] cached array of product categories IDs to flag categories compatible with the current location */
	protected $product_categories;


	/**
	 * Location constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WP_Post|\WC_Local_Pickup_Plus_Pickup_Location $id the post or location ID, object
	 */
	public function __construct( $id ) {

		if ( is_numeric( $id ) ) {
			$post       = get_post( (int) $id );
			$this->post = $post instanceof \WP_Post ? $post : null;
		} elseif ( is_object( $id ) ) {
			$this->post = $id;
		}

		if ( $this->post instanceof \WP_Post ) {

			// set post type data
			$this->id   = (int) $this->post->ID;
			$this->name = $this->post->post_title;
			$this->slug = $this->post->post_name;
		}
	}


	/**
	 * Check whether a geodata record exists in database for this location or not.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function record_exists() {
		global $wpdb;

		$exists = false;

		if ( $this->id > 0 ) {

			wc_local_pickup_plus()->check_tables();

			$table  = "{$wpdb->prefix}woocommerce_pickup_locations_geodata";
			$exists = $wpdb->get_row( $wpdb->prepare( "
				SELECT * from {$table}
 				WHERE post_id = %d
 			", (int) $this->id ) );
		}

		return ! empty( $exists );
	}


	/**
	 * Get the location ID.
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Get the location slug.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}


	/**
	 * Get the location name.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}


	/**
	 * Returns the pickup location formatted name.
	 *
	 * @since 2.3.15
	 *
	 * @param string $context context where the name is intended to be displayed
	 * @return string
	 */
	public function get_formatted_name( $context = 'frontend' ) {

		if ( 'admin' === $context ) {

			// append the location ID, a bit like other WooCommerce objects do in admin panels or search field results (e.g. products, orders)
			$name    = trim( sprintf( is_rtl() ? '(#%2$s) %1$s' : '%1$s (#%2$s)', $this->name, $this->id ) );

		} elseif ( 'frontend' === $context ) {

			// for customer facing purposes, some locations can have the same name, so to help differentiating them the ID is not useful, but the state (or city if no state) and postcode are
			$address = $this->get_address();
			$meta    = trim( implode( ' ', array_unique( array( $address->has_state() ? $address->get_state() : $address->get_city(), $address->get_postcode() ) ) ) );
			$name    = trim( sprintf( is_rtl() ? '%2$s %1$s' : '%1$s %2$s', $this->name, ! empty( $meta ) ? " ({$meta}) " : '' ) );

		} else {

			// just use the plain name if context is not a recognized one
			$name    = $this->name;
		}

		/**
		 * Filters the pickup location formatted name.
		 *
		 * @since 2.3.15
		 *
		 * @param string $name pickup location formatted name
		 * @param string $context context where the name should be displayed
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location pickup location object
		 */
		return (string) apply_filters( 'wc_local_pickup_plus_pickup_location_option_label', $name, $context, $this );
	}


	/**
	 * Set the pickup location name.
	 *
	 * @since 2.0.0
	 *
	 * @param string $name the pickup location name (make sure this is sanitized for db storage!)
	 * @return bool
	 */
	public function set_name( $name ) {
		global $wpdb;

		$success = false;

		if ( $this->id > 0 && is_string( $name ) ) {

			$success = wp_update_post( array(
				'ID'         => $this->id,
				'post_title' => $name,
			) );

			$success = ! $success instanceof \WP_Error && (int) $success !== 0;

			if ( $success ) {

				$table = "{$wpdb->prefix}woocommerce_pickup_locations_geodata";

				if ( $this->record_exists() ) {
					$success = (bool) $wpdb->update(
						$table,
						array(
							'title'        => $name,
							'lat'          => $this->get_latitude(),
							'lon'          => $this->get_longitude(),
							'country'      => $this->get_address()->get_country(),
							'state'        => $this->get_address()->get_state(),
							'postcode'     => $this->get_address()->get_postcode(),
							'city'         => $this->get_address()->get_city(),
							'address_1'    => $this->get_address()->get_address_line_1(),
							'address_2'    => $this->get_address()->get_address_line_2(),
							'last_updated' => date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) ),
						),
						array( 'post_id' => $this->id ),
						array( '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
						array( '%d' )
					);
				} else {
					$success = (bool) $wpdb->insert(
						$table,
						array(
							'post_id'      => $this->id,
							'title'        => $name,
							'lat'          => $this->get_latitude(),
							'lon'          => $this->get_longitude(),
							'country'      => $this->get_address()->get_country(),
							'state'        => $this->get_address()->get_state(),
							'postcode'     => $this->get_address()->get_postcode(),
							'city'         => $this->get_address()->get_city(),
							'address_1'    => $this->get_address()->get_address_line_1(),
							'address_2'    => $this->get_address()->get_address_line_2(),
							'last_updated' => date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) ),
						),
						array( '%d', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
					);
				}

				$this->name = $name;
			}
		}

		return $success;
	}


	/**
	 * Delete the pickup location name.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_name() {
		return $this->id > 0 && $this->set_name( '' );
	}


	/**
	 * Get the post object.
	 *
	 * @since 2.0.0
	 *
	 * @return null|\WP_Post
	 */
	public function get_post() {
		return $this->post;
	}


	/**
	 * Get the location coordinates.
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array with latitude (lat) and longitude (lon)
	 */
	public function get_coordinates() {
		global $wpdb;

		$default_coordinates = array(
			'lat' => 0.0,
			'lon' => 0.0,
		);

		if ( ! empty( $this->coordinates ) ) {

			$coordinates = $this->coordinates;

		} elseif ( $this->id > 0 ) {

			$pickup_locations_table = "{$wpdb->prefix}woocommerce_pickup_locations_geodata";
			$pickup_location_id     = (int) $this->id;

			$results = $wpdb->get_results( "
				SELECT lat, lon
				FROM {$pickup_locations_table}
				WHERE post_id = {$pickup_location_id}
			", ARRAY_A );

			$coordinates = isset( $results[0] ) ? $results[0] : $default_coordinates;

			$this->coordinates = $coordinates;

		} else {

			$coordinates = $default_coordinates;
		}

		/**
		 * Filter the location coordinates.
		 *
		 * @since 2.0.0
		 *
		 * @param array $coordinate associative array with location coordinates
		 * @param \WC_Local_Pickup_Plus_Pickup_Location the current pickup location
		 */
		return apply_filters( 'wc_local_pickup_plus_pickup_location_coordinates', $coordinates, $this );
	}


	/**
	 * Get the location latitude.
	 *
	 * @since 2.0.0
	 *
	 * @return float latitude value
	 */
	public function get_latitude() {

		$coordinates = $this->get_coordinates();

		return $coordinates['lat'];
	}


	/**
	 * Get the location longitude.
	 *
	 * @since 2.0.0
	 *
	 * @return float longitude value
	 */
	public function get_longitude() {

		$coordinates = $this->get_coordinates();

		return $coordinates['lon'];
	}


	/**
	 * Check if the location has correctly set coordinates.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_coordinates() {

		$lat = $this->get_latitude();
		$lon = $this->get_longitude();

		return $this->id > 0 && ( ( $lat > 0 || $lat < 0 ) && ( $lon < 0 || $lon > 0 ) );
	}


	/**
	 * Set the location coordinates.
	 *
	 * @since 2.0.0
	 *
	 * @param float $latitude latitude
	 * @param float $longitude longitude
	 * @return bool
	 */
	public function set_coordinates( $latitude = 0.0, $longitude = 0.0 ) {
		global $wpdb;

		$success = false;

		if ( $this->id > 0 ) {

			$table       = "{$wpdb->prefix}woocommerce_pickup_locations_geodata";
			$coordinates = array( 'lat' => (float) $latitude, 'lon' => (float) $longitude );

			if ( $this->record_exists() ) {
				$success = (bool) $wpdb->update(
					$table,
					array(
						'title'        => $this->name,
						'lat'          => $coordinates['lat'],
						'lon'          => $coordinates['lon'],
						'country'      => $this->get_address()->get_country(),
						'state'        => $this->get_address()->get_state(),
						'postcode'     => $this->get_address()->get_postcode(),
						'city'         => $this->get_address()->get_city(),
						'address_1'    => $this->get_address()->get_address_line_1(),
						'address_2'    => $this->get_address()->get_address_line_2(),
						'last_updated' => date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) ),
					),
					array( 'post_id' => $this->id ),
					array( '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
					array( '%d' )
				);
			} else {
				$success = (bool) $wpdb->insert(
					$table,
					array(
						'post_id'      => $this->id,
						'title'        => $this->name,
						'lat'          => $coordinates['lat'],
						'lon'          => $coordinates['lon'],
						'country'      => $this->get_address()->get_country(),
						'state'        => $this->get_address()->get_state(),
						'postcode'     => $this->get_address()->get_postcode(),
						'city'         => $this->get_address()->get_city(),
						'address_1'    => $this->get_address()->get_address_line_1(),
						'address_2'    => $this->get_address()->get_address_line_2(),
						'last_updated' => date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) ),
					),
					array( '%d', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
				);
			}

			if ( $success ) {
				$this->coordinates = $coordinates;
			}
		}

		return $success;
	}


	/**
	 * Delete the location coordinates.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_coordinates() {
		return $this->id > 0 && $this->set_coordinates( 0, 0 );
	}


	/**
	 * Get the location address.
	 *
	 * @since 2.0.0
	 *
	 * @param null|string $piece optional, to return an address piece instead of the whole object
	 * @return string|\WC_Local_Pickup_Plus_Address address object or string piece
	 */
	public function get_address( $piece = null ) {
		global $wpdb;

		if ( ! $this->address instanceof \WC_Local_Pickup_Plus_Address ) {

			$address_array = array();

			if ( $this->id > 0 ) {

				$table   = "{$wpdb->prefix}woocommerce_pickup_locations_geodata";
				$address = $wpdb->get_row( "
					SELECT *
					FROM {$table}
					WHERE post_id = {$this->id}
				" );

				if ( ! empty( $address ) ) {
					$address_array = array(
						'name'         => $this->name,
						'country'      => $address->country,
						'state'        => $address->state,
						'city'         => $address->city,
						'postcode'     => $address->postcode,
						'address_1'    => $address->address_1,
						'address_2'    => $address->address_2,
					);
				}
			}

			$this->address = new \WC_Local_Pickup_Plus_Address( $address_array );
		}

		/**
		 * Filter the pickup location address.
		 *
		 * @since 2.0.0
		 *
		 * @param \WC_Local_Pickup_Plus_Address address object
		 * @param string|null $piece whether an address piece was requested
		 * @param \WC_Local_Pickup_Plus_Pickup_Location the pickup location
		 */
		$address = apply_filters( 'wc_local_pickup_plus_pickup_location_address', $this->address, $piece, $this );

		if ( is_string( $piece ) ) {
			$address = $address->get_array();
			$address = isset( $address[ $piece ] ) ? $address[ $piece ] : '';
		}

		return $address;
	}


	/**
	 * Set this location address.
	 *
	 * @since 2.0.0
	 *
	 * @param array|\WC_Local_Pickup_Plus_Address $address the address as an associative array or object
	 * @return bool
	 */
	public function set_address( $address ) {
		global $wpdb;

		$success = false;

		if ( $this->id > 0 ) {

			if ( is_array( $address ) ) {
				$address = new \WC_Local_Pickup_Plus_Address( $address, $this->id );
			}

			if ( $address instanceof \WC_Local_Pickup_Plus_Address ) {

				$table = "{$wpdb->prefix}woocommerce_pickup_locations_geodata";

				if ( $this->record_exists() ) {
					$success = (bool) $wpdb->update(
						$table,
						array(
							'title'        => $this->name,
							'lat'          => $this->get_latitude(),
							'lon'          => $this->get_longitude(),
							'country'      => $address->get_country(),
							'state'        => $address->get_state(),
							'postcode'     => $address->get_postcode(),
							'city'         => $address->get_city(),
							'address_1'    => $address->get_address_line_1(),
							'address_2'    => $address->get_address_line_2(),
							'last_updated' => date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) ),
						),
						array( 'post_id' => $this->id ),
						array( '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
						array( '%d' )
					);
				} else {
					$success = (bool) $wpdb->insert(
						$table,
						array(
							'post_id'      => $this->id,
							'title'        => $this->name,
							'lat'          => $this->get_latitude(),
							'lon'          => $this->get_longitude(),
							'country'      => $address->get_country(),
							'state'        => $address->get_state(),
							'postcode'     => $address->get_postcode(),
							'city'         => $address->get_city(),
							'address_1'    => $address->get_address_line_1(),
							'address_2'    => $address->get_address_line_2(),
							'last_updated' => date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) ),
						),
						array( '%d', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
					);
				}

				if ( $success ) {

					$this->address = $address;

					// we need to store the address also as post meta to help WordPress admin search location by address pieces
					foreach ( $address->get_array() as $piece => $value ) {
						update_post_meta( $this->id, "{$this->address_meta}_{$piece}", sanitize_text_field( $value ) );
					}
				}
			}
		}

		return $success;
	}


	/**
	 * Delete the location address.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_address() {

		$deleted = $this->set_address( array(
			'name'      => '',
			'country'   => '',
			'state'     => '',
			'city'      => '',
			'postcode'  => '',
			'address_1' => '',
			'address_2' => '',
		) );

		return $deleted;
	}


	/**
	 * Get location optional notes.
	 *
	 * @since 2.0.0
	 *
	 * @return string HTML
	 */
	public function get_description() {

		$description = $this->post instanceof \WP_Post ? $this->post->post_content : '';

		/**
		 * Filter the pickup location description notes.
		 *
		 * @since 2.0.0
		 *
		 * @param string $description pickup location notes
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the current pickup location
		 */
		return apply_filters( 'wc_local_pickup_plus_pickup_location_description', $description, $this );
	}


	/**
	 * Check if the pickup location has optional notes.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_description() {

		$description = trim( $this->get_description() );

		return ! empty( $description );
	}


	/**
	 * Set location optional notes.
	 *
	 * @since 2.0.0
	 *
	 * @param string $notes notes text content, may contain HTML
	 * @return bool
	 */
	public function set_description( $notes ) {

		$success = false;

		if ( $this->id > 0 && is_string( $notes ) ) {

			$success = wp_update_post( array(
				'ID'           => $this->id,
				'post_content' => wp_kses_post( $notes ),
			) );

			$success = ! $success instanceof \WP_Error && (int) $success !== 0;
		}

		return $success;
	}


	/**
	 * Delete location optional notes.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_description() {
		return $this->id > 0 && $this->set_description( '' );
	}


	/**
	 * Get location phone number.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $html optional: whether to return a HTML phone link; default (false) will return just the phone number string
	 * @return string
	 */
	public function get_phone( $html = false ) {

		$phone = $this->id > 0 ? get_post_meta( $this->id, $this->phone_meta, true ) : '';
		$phone = is_string( $phone ) ? $phone : '';

		/**
		 * Filter the pickup location phone number.
		 *
		 * @since 2.0.0
		 *
		 * @param string $phone a phone number or empty string if not set
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the current pickup location object
		 */
		$phone = apply_filters( 'wc_local_pickup_plus_pickup_location_phone', $phone, $this );

		if ( $html === true && ! empty( $phone ) ) {
			$phone = '<a href="tel:' . esc_attr( $phone ) . '">' . $phone . '</a>';
		}

		return $phone;
	}


	/**
	 * Whether the location has a phone number.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_phone() {

		$phone = trim( $this->get_phone( false ) );

		return ! empty( $phone );
	}


	/**
	 * Set location phone number.
	 *
	 * @since 2.0.0
	 *
	 * @param string $phone_number a phone number string
	 * @return bool
	 */
	public function set_phone( $phone_number ) {

		$success = false;

		if ( $this->id > 0 && is_string( $phone_number ) ) {
			$success = update_post_meta( $this->id, $this->phone_meta, trim( $phone_number ) );
		}

		return (bool) $success;
	}


	/**
	 * Delete the location phone number.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_phone() {
		return $this->id > 0 && delete_post_meta( $this->id, $this->phone_meta );
	}


	/**
	 * Save or update the location business hours for scheduling a pickup appointment.
	 *
	 * @since 2.0.0
	 *
	 * @param array $business_hours pickup appointment weekly schedule
	 * @return bool
	 */
	public function set_business_hours( array $business_hours ) {
		return $this->id > 0 && (bool) update_post_meta( $this->id, $this->business_hours_meta, $business_hours );
	}


	/**
	 * Get location business hours for scheduling a pickup appointment.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Business_Hours
	 */
	public function get_business_hours() {

		$business_hours_meta = $this->id > 0 ? get_post_meta( $this->id, $this->business_hours_meta, true ) : array();

		if ( empty( $business_hours_meta ) || ! is_array( $business_hours_meta ) ) {

			$business_hours_meta    = array();
			$default_business_hours = wc_local_pickup_plus_shipping_method()->get_default_business_hours();

			if ( ! empty( $default_business_hours ) && is_array( $default_business_hours ) ) {
				$business_hours_meta = $default_business_hours;
			}
		}

		$business_hours_object = new \WC_Local_Pickup_Plus_Business_Hours( $business_hours_meta, $this->id );

		/**
		 * Filter the pickup location business hours.
		 *
		 * @since 2.0.0
		 * @param \WC_Local_Pickup_Plus_Business_Hours $business_hours_object the business hours schedule object
		 * @param array $business_hours_meta the business hours schedule as an associative array
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location
		 */
		return apply_filters( 'wc_local_pickup_plus_pickup_location_business_hours', $business_hours_object, $business_hours_meta, $this );
	}


	/**
	 * Check if this location has business hours set.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_business_hours() {
		return $this->id > 0 && $this->get_business_hours()->has_schedule();
	}


	/**
	 * Delete the location business hours information.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_business_hours() {
		return $this->id > 0 && delete_post_meta( $this->id, $this->business_hours_meta );
	}


	/**
	 * Save or update the location public holidays for scheduling a pickup appointment.
	 *
	 * @since 2.0.0
	 *
	 * @param string[]|int[] $schedule Array of timestamps or date strings
	 * @return bool
	 */
	public function set_public_holidays( array $schedule ) {

		$success = false;

		if ( $this->id > 0 ) {
			$calendar = new \WC_Local_Pickup_Plus_Public_Holidays( $schedule, $this->id );
			$success  = (bool) update_post_meta( $this->id, $this->public_holidays_meta, $calendar->get_calendar() );
		}

		return $success;
	}


	/**
	 * Get public holidays for appointment scheduling.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Public_Holidays
	 */
	public function get_public_holidays() {

		$public_holidays_meta = $this->id > 0 ? get_post_meta( $this->id, $this->public_holidays_meta, true ) : array();

		if ( empty( $public_holidays_meta ) || ! is_array( $public_holidays_meta ) ) {

			$public_holidays_meta    = array();
			$default_public_holidays = wc_local_pickup_plus_shipping_method()->get_default_public_holidays();

			if ( ! empty( $default_public_holidays ) && is_array( $default_public_holidays ) ) {
				$public_holidays_meta = $default_public_holidays;
			}
		}

		$public_holidays_object = new \WC_Local_Pickup_Plus_Public_Holidays( $public_holidays_meta, $this->id );

		/**
		 * Filter the pickup location public holidays.
		 *
		 * @since 2.0.0
		 *
		 * @param \WC_Local_Pickup_Plus_Public_Holidays $public_holidays_object the business hours schedule object
		 * @param int[] $public_holidays_meta the closure days calendar as an array of timestamps
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location
		 */
		return apply_filters( 'wc_local_pickup_plus_pickup_location_public_holidays', $public_holidays_object, $public_holidays_meta, $this );
	}


	/**
	 * Check if this location has a public holidays calendar set.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function has_public_holidays() {
		return $this->id > 0 && $this->get_public_holidays()->has_calendar();
	}


	/**
	 * Delete the location public holidays information.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_public_holidays() {
		return $this->id > 0 && delete_post_meta( $this->id, $this->public_holidays_meta );
	}


	/**
	 * Set lead time to schedule an appointment to this location.
	 *
	 * @since 2.0.0
	 *
	 * @param int $amount amount of time
	 * @param string $type type of interval (days, weeks...)
	 * @return bool
	 */
	public function set_pickup_lead_time( $amount, $type ) {

		$success = false;

		if ( $this->id > 0 ) {
			$lead_time = new \WC_Local_Pickup_Plus_Schedule_Adjustment( 'lead-time', "{$amount} {$type}", $this->id );
			$success   = (bool) update_post_meta( $this->id, $this->lead_time_meta, $lead_time->get_value() );
		}

		return $success;
	}


	/**
	 * Get lead time to schedule an appointment to this location.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Schedule_Adjustment
	 */
	public function get_pickup_lead_time() {

		$lead_time_meta = $this->id > 0 ? get_post_meta( $this->id, $this->lead_time_meta, true ) : null;

		if ( empty( $lead_time_meta ) ) {
			$lead_time_meta = wc_local_pickup_plus_shipping_method()->get_default_pickup_lead_time();
		}

		$lead_time = new \WC_Local_Pickup_Plus_Schedule_Adjustment( 'lead-time', $lead_time_meta, $this->id );

		/**
		 * Filter the pickup lead time.
		 *
		 * @since 2.0.0
		 *
		 * @param \WC_Local_Pickup_Plus_Schedule_Adjustment $lead_time the lead time to schedule a pickup
		 * @param string $lead_time_meta the lead time string
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location
		 */
		return apply_filters( 'wc_local_pickup_plus_pickup_location_lead_time', $lead_time, $lead_time_meta, $this );
	}


	/**
	 * Check if this location requires a lead time to set a pickup appointment.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_pickup_lead_time() {

		$lead_time = $this->get_pickup_lead_time();

		return $lead_time && ! $lead_time->is_null() && (int) $lead_time->get_amount() > 0;
	}


	/**
	 * Delete lead time for the location pickup appointment.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_pickup_lead_time() {
		return $this->id > 0 && delete_post_meta( $this->id, $this->lead_time_meta );
	}


	/**
	 * Set deadline to schedule an appointment to this location.
	 *
	 * @since 2.0.0
	 *
	 * @param int $amount amount of time
	 * @param string $type type of interval (days, weeks...)
	 * @return bool
	 */
	public function set_pickup_deadline( $amount, $type ) {

		$success = false;

		if ( $this->id > 0 ) {
			$deadline = new \WC_Local_Pickup_Plus_Schedule_Adjustment( 'deadline', $amount . ' ' . $type, $this->id );
			$success  = (bool) update_post_meta( $this->id, $this->deadline_meta,$deadline->get_value() );
		}

		return $success;
	}


	/**
	 * Get the deadline to schedule an appointment to this location.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Schedule_Adjustment
	 */
	public function get_pickup_deadline() {

		$deadline_meta = $this->id > 0 ? get_post_meta( $this->id, $this->deadline_meta, true ) : null;

		if ( empty( $deadline_meta ) ) {
			$deadline_meta = wc_local_pickup_plus_shipping_method()->get_default_pickup_deadline();
		}

		$deadline = new \WC_Local_Pickup_Plus_Schedule_Adjustment( 'deadline', $deadline_meta, $this->id );

		/**
		 * Filter the pickup deadline.
		 *
		 * @since 2.0.0
		 *
		 * @param \WC_Local_Pickup_Plus_Schedule_Adjustment $deadline the lead time to schedule a pickup
		 * @param string $deadline_meta the deadline time string
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location
		 */
		return apply_filters( 'wc_local_pickup_plus_pickup_location_deadline', $deadline, $deadline_meta, $this );
	}


	/**
	 * Check if this location has a pickup deadline set to schedule a collection.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_pickup_deadline() {

		$deadline = $this->get_pickup_deadline();

		return $deadline && ! $deadline->is_null() && (int) $deadline->get_amount() > 0;
	}


	/**
	 * Delete deadline for the location pickup appointment.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_pickup_deadline() {
		return $this->id > 0 && delete_post_meta( $this->id, $this->deadline_meta );
	}


	/**
	 * Set a price adjustment for this pickup location.
	 *
	 * @since 2.0.0
	 *
	 * @param string $adjustment if the adjustment is a 'discount' or a 'cost'
	 * @param int|float $amount a numerical amount
	 * @param string $type either 'fixed' or 'percentage' type
	 * @return bool
	 */
	public function set_price_adjustment( $adjustment, $amount, $type ) {

		$success = false;

		if ( $this->id > 0 ) {

			$price_adjustment = new \WC_Local_Pickup_Plus_Price_Adjustment( null, $this->id );

			$price_adjustment->set_value( $adjustment, abs( $amount ), $type );

			$success = (bool) update_post_meta( $this->id, $this->price_adjustment_meta, $price_adjustment->get_value() );
		}

		return $success;
	}


	/**
	 * Gets the price adjustment for this location.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Price_Adjustment
	 */
	public function get_price_adjustment() {

		$price_adjustment_meta = $this->id > 0 ? get_post_meta( $this->id, $this->price_adjustment_meta, true ) : null;

		// account for "0" value overrides before using the global default
		if ( '0' !== $price_adjustment_meta && empty( $price_adjustment_meta ) ) {
			$price_adjustment_meta = wc_local_pickup_plus_shipping_method()->get_default_price_adjustment();
		}

		$price_adjustment_object = new \WC_Local_Pickup_Plus_Price_Adjustment( $price_adjustment_meta, $this->id );

		/**
		 * Filters the price adjustment for the pickup location.
		 *
		 * @since 2.0.0
		 *
		 * @param \WC_Local_Pickup_Plus_Price_Adjustment $price_adjustment_object a price adjustment instance
		 * @param string $price_adjustment_meta the value passed to the price adjustment helper object
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location
		 */
		return apply_filters( 'wc_local_pickup_plus_pickup_location_price_adjustment', $price_adjustment_object, $price_adjustment_meta, $this );
	}


	/**
	 * Check if this location has a price adjustment set.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_price_adjustment() {
		return ! $this->get_price_adjustment()->is_null();
	}


	/**
	 * Deletes the price adjustment information for this pickup location.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_price_adjustment() {
		return $this->id > 0 && delete_post_meta( $this->id, $this->price_adjustment_meta );
	}


	/**
	 * Set email addresses for notifications.
	 *
	 * @since 2.0.0
	 *
	 * @param string $recipients comma separated email addresses
	 * @return bool
	 */
	public function set_email_recipients( $recipients ) {

		$success = false;

		if ( $this->id > 0 ) {
			$success = (bool) update_post_meta( $this->id, $this->email_recipients_meta, $recipients );
		}

		return $success;
	}


	/**
	 * Get email recipients for pickup location notifications.
	 *
	 * @uses \is_email()
	 *
	 * @since 2.0.0
	 *
	 * @param string $format default 'array' to return an array - pass 'string' to return comma separated addresses
	 * @param string $separator optional separator when using 'string' format (default comma)
	 * @return null|string[]|string an array or string of email addresses, according to $format (if invalid $format, returns null)
	 */
	public function get_email_recipients( $format = 'array', $separator = ',' ) {

		$email_recipients     = $this->id > 0 ? get_post_meta( $this->id, $this->email_recipients_meta, true ) : '';
		$validated_recipients = array();

		if ( ! empty( $email_recipients ) ) {

			$emails = explode( ',', $email_recipients );

			foreach ( $emails as $email ) {

				$email = trim( $email );

				if ( is_email( $email ) ) {
					$validated_recipients[] = $email;
				}
			}
		}

		switch ( $format ) {
			case 'array' :
				$value = $validated_recipients;
			break;
			case 'string' :
				$value = ! empty( $validated_recipients ) ? trim( implode( $separator, $validated_recipients ) ) : '';
			break;
			default:
				$value = null;
			break;
		}

		/**
		 * Filter email recipients for pickup location.
		 *
		 * @since 2.0.0
		 *
		 * @param string|array|mixed $value the email address(es) in the requested $format
		 * @param string $format the requested format
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location
		 */
		return apply_filters( 'wc_local_pickup_plus_pickup_location_email_recipients', $value, $format, $this );
	}


	/**
	 * Delete email addresses for pickup notifications.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_email_recipients() {
		return $this->id > 0 && delete_post_meta( $this->id, $this->email_recipients_meta );
	}


	/**
	 * Set product relationships for this pickup location.
	 *
	 * @since 2.0.0
	 *
	 * @param int[]|\WC_Product[] $products array of product IDs or objects
	 * @return bool
	 */
	public function set_products( array $products ) {

		$success      = false;
		$set_products = $this->id > 0 ? get_post_meta( $this->id, $this->products_meta, true ) : array();
		$new_products = array();

		if ( $this->id > 0 ) {

			foreach ( $products as $product ) {

				if ( $product instanceof \WC_Product ) {
					$product_id = $product->get_id();
				} elseif ( is_numeric( $product ) ) {
					$product_id = absint( $product );
				}

				if ( ! empty( $product_id ) ) {
					$new_products[] = $product_id;
				}
			}

			if ( is_array( $set_products ) ) {
				$set_products['products'] = $new_products;
			} else {
				$set_products = array(
					'products'           => $new_products,
					'product_categories' => array(),
				);
			}

			$success = (bool) update_post_meta( $this->id, $this->products_meta, $set_products );
		}

		return $success;
	}


	/**
	 * Set product categories relationships for this pickup location.
	 *
	 * @since 2.0.0
	 *
	 * @param int[]|\WP_Term[] $product_categories array of categories IDs or objects
	 * @return bool|int
	 */
	public function set_product_categories( array $product_categories ) {

		$success                = false;
		$set_product_categories = $this->id > 0 ? get_post_meta( $this->id, $this->products_meta, true ) : array();
		$new_product_categories = array();

		if ( $this->id > 0 ) {

			foreach ( $product_categories as $product_category ) {

				if ( $product_category instanceof \WP_Term ) {
					$product_category_id = $product_category->term_id;
				} elseif ( is_numeric( $product_category ) ) {
					$product_category_id = absint( $product_category );
				}

				if ( ! empty( $product_category_id ) ) {
					$new_product_categories[] = $product_category_id;
				}
			}

			if ( is_array( $set_product_categories ) ) {
				$set_product_categories['product_categories'] = $new_product_categories;
			} else {
				$set_product_categories = array(
					'products'           => array(),
					'product_categories' => $new_product_categories,
				);
			}

			$success = (bool) update_post_meta( $this->id, $this->products_meta, $set_product_categories );
		}

		return $success;
	}


	/**
	 * Get products available for this pickup location.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args array of optional arguments as in get_posts()
	 * @return int[]|\WC_Product[] by default uses 'fields' => 'ids', you can pass 'fields' => 'all' to return objects
	 */
	public function get_products( $args = array() ) {

		$products = array();

		if ( $this->id > 0 ) {

			// only use caching when querying IDs
			$querying_ids = empty( $args['fields'] ) || 'ids' === $args['fields'];

			if ( $querying_ids && is_array( $this->products ) ) {

				$products = $this->products;

			} else {

				$set_products = get_post_meta( $this->id, $this->products_meta, true );

				if ( ! empty( $set_products ) ) {

					$product_ids = ! empty( $set_products['products'] ) ? array_map( 'absint', (array) $set_products['products'] ) : array();
					$args        = wp_parse_args( $args, array(
						'nopaging'           => true,
						'fields'             => 'ids',
						'post__in'           => array(),
						'post__not_in'       => array(),
						'exclude_categories' => false,
					) );

					// ensure we always query product post types
					$args['post_type'] = 'product';

					$post__in = array_merge( $args['post__in'], $product_ids );

					if ( empty( $product_ids ) ) {
						$products = array();
					} else {
						$products = get_posts( array_merge( $args, array(
							'post__in' => $post__in,
						) ) );
					}

					if ( ! empty( $set_products['product_categories'] ) && false === $args['exclude_categories'] ) {

						$products = array_merge( $products, get_posts( array_merge( $args, array(
							'post__not_in' => $product_ids,
							'tax_query'    => array(
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'id',
									'terms'    => array_map( 'absint', (array) $set_products['product_categories'] ),
								),
							),
						) ) ) );
					}

					// maybe get product objects
					if ( $args['fields'] === 'all' ) {

						$product_objects = array();

						foreach ( $products as $product_id ) {

							if ( $product_object = wc_get_product( $product_id ) ) {
								$product_objects[] = $product_object;
							}
						}

						$products = $product_objects;
					}
				}

				if ( $querying_ids ) {
					$this->products = $products;
				}
			}
		}

		return $products;
	}


	/**
	 * Get product categories available for this pickup location.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args array of optional arguments as in get_terms()
	 * @return int[]|\WP_Term[] by default uses 'fields' => 'ids', you can pass 'fields' => 'all' to return objects
	 */
	public function get_product_categories( $args = array() ) {

		$product_categories = array();

		if ( $this->id > 0 ) {

			// only use caching when querying IDs
			$querying_ids = empty( $args['fields'] ) || 'ids' === $args['fields'];

			if ( $querying_ids && is_array( $this->product_categories ) ) {

				$product_categories = $this->product_categories;

			} else {

				$set_product_categories = get_post_meta( $this->id, $this->products_meta, true );

				if ( ! empty( $set_product_categories ) && ! empty( $set_product_categories['product_categories'] ) ) {

					$product_categories_ids = array_map( 'absint', (array) $set_product_categories['product_categories'] );
					$args                   = wp_parse_args( $args, array(
						'fields'     => 'ids',
						'hide_empty' => false,
					) );

					// ensure we query relevant product categories
					$args['taxonomy'] = 'product_cat';
					$args['include']  = $product_categories_ids;

					$product_categories = get_terms( $args );
				}

				if ( $querying_ids ) {
					$this->product_categories = $product_categories;
				}
			}
		}

		return $product_categories;
	}


	/**
	 * Whether this location has products or product categories set.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_products() {

		$products = $this->id > 0 ? $this->get_products() : null;

		return ! empty( $products );
	}


	/**
	 * Remove all products associated with the location.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $include_categories whether to also delete product categories (default true)
	 * @return bool
	 */
	public function delete_products( $include_categories = true ) {

		$success = false;

		if ( $this->id > 0 ) {

			$success = $this->set_products( array() );

			if ( true === $include_categories ) {
				$success = $this->delete_product_categories();
			}
		}

		return $success;
	}


	/**
	 * Remove product categories associated with the location.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function delete_product_categories() {
		return $this->id > 0 && $this->set_product_categories( array() );
	}


	/**
	 * Gets the appointments handler for this pickup location.
	 *
	 * @since 2.7.0
	 *
	 * @return Pickup_Location\Appointments
	 */
	public function get_appointments() {

		return new Pickup_Location\Appointments( $this );
	}


}
