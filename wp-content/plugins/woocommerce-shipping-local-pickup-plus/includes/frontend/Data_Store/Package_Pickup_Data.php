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
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Pickup data storage component for shipping packages.
 *
 * @since 2.7.0
 */
class Package_Pickup_Data extends Pickup_Data {


	/**
	 * Data storage constructor.
	 *
	 * @since 2.7.0
	 *
	 * @param int|string $package_key key index of current package
	 */
	public function __construct( $package_key ) {

		$this->object_id = $package_key;
	}


	/**
	 * Gets the pickup location data.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param string $piece specific data to get. Defaults to getting all available data.
	 * @return array|string
	 */
	public function get_pickup_data( $piece = '' ) {
		return wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( $this->object_id, $piece );
	}


	/**
	 * Sets the pickup location data.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param array $pickup_data pickup data
	 */
	public function set_pickup_data( array $pickup_data ) {
		wc_local_pickup_plus()->get_session_instance()->set_package_pickup_data( $this->object_id, $pickup_data );
	}


	/**
	 * Deletes the pickup location data.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 */
	public function delete_pickup_data() {
		wc_local_pickup_plus()->get_session_instance()->delete_package_pickup_data( $this->object_id );
	}


	/**
	 * Get the current package.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array
	 */
	public function get_package() {

		$package    = [];
		$package_id = $this->object_id;

		if ( null !== $package_id ) {

			$packages = WC()->shipping()->get_packages();

			if ( ! empty( $packages[ $package_id ] ) ) {
				$package = $packages[ $package_id ];
			}
		}

		return $package;
	}


	/**
	 * Gets the value of a package key.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $key the key to retrieve a value for
	 * @param null|mixed $default the default value (optional)
	 *
	 * @return null|string|int|array
	 */
	public function get_package_key( $key = null, $default = null ) {

		$value   = $default;
		$package = $this->get_package();

		if ( '' !== $key && is_string( $key ) && ! empty( $package ) ) {
			$value = isset( $package[ $key ] ) ? $package[ $key ] : $value;
		}

		return $value;
	}


	/**
	 * Gets the ID of the pickup location associated with the package.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	public function get_pickup_location_id() {
		return $this->get_package_key( 'pickup_location_id', 0 );
	}


	/**
	 * Gets the pickup location associated with the package.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return null|\WC_Local_Pickup_Plus_Pickup_Location
	 */
	public function get_pickup_location() {

		$pickup_location_id = $this->get_pickup_location_id();
		$pickup_location_id = 0 === $pickup_location_id ? wc_local_pickup_plus()->get_packages_instance()->get_package_only_pickup_location_id( $this->get_package() ) : $pickup_location_id;

		return $pickup_location_id > 0 ? wc_local_pickup_plus_get_pickup_location( $pickup_location_id ) : null;
	}


}
