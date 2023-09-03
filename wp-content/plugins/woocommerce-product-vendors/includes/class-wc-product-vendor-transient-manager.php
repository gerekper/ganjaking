<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Manage vendor related transient data
 *
 * @package WooCommerce Product Vendors/Cache
 * @since 2.2.0
 */
final class WC_Product_Vendor_Transient_Manager {
	private $vendor_id;
	private $transient_key;
	private $expiration = DAY_IN_SECONDS;
	private $transient_version;

	/**
	 * @param int $vendor_id
	 */
	public function __construct( $vendor_id = null ) {
		$this->vendor_id         = absint( $vendor_id );
		$this->transient_key     = 'wcpv_vendor_transients_data_' . $this->vendor_id;
		$this->transient_version = $this->get_transient_version();
	}

	/**
	 * Should return class object.
	 *
	 * @param int $vendor_id
	 *
	 * @return self
	 */
	public static function make( $vendor_id = null ) {
		$vendor_id = $vendor_id ?: WC_Product_Vendors_Utils::get_logged_in_vendor( 'id' );

		return new self( (int) $vendor_id );
	}

	/**
	 * Should return vendor report data version.
	 *
	 * @param bool $refresh
	 *
	 * @return string
	 */
	public function get_transient_version( $refresh = false ) {
		/**
		 * Assign transient version globally to discard/clear cache for all vendors.
		 * Same version assigned because add-on core does not have logic to invalidate vendor specific transient.
		 *
		 * Note:
		 * When we have logic in the core, we can assign unique transient version to each vendor's transient data.
		 * Assign unique version replace group name with "$this->transient_key" when core ready.
		 */
		return WC_Cache_Helper::get_transient_version( 'wcpv_vendor_transients_data', $refresh );
	}

	/**
	 * Should return specific vendor report data patrr.
	 *
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	public function get( $key ) {
		$transient_value = get_transient( $this->transient_key );

		if (
			isset( $transient_value['version'], $transient_value['data'][ $key ] ) &&
			$this->transient_version === $transient_value['version']
		) {
			return $transient_value['data'][ $key ];
		}

		return null;
	}

	/**
	 * Save vendor report data.
	 *
	 * @param $key
	 * @param $data
	 *
	 * @return bool
	 */
	public function save( $key, $data ) {
		$transient_value = get_transient( $this->transient_key );
		$transient_value = $transient_value ?: [];

		$transient_value['version']      = $this->get_transient_version();
		$transient_value['data'][ $key ] = $data;

		return set_transient( $this->transient_key, $transient_value, $this->expiration );
	}

	/**
	 * Delete vendor report data.
	 *
	 * This function reset transient version which invalidate existing transient report data.
	 *
	 * @return string
	 */
	public function delete() {
		$this->transient_version = $this->get_transient_version( true );
		delete_transient( $this->transient_key );

		return $this->transient_version;
	}
}
