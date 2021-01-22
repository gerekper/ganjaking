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

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Data Store for persisting Checkout Add-Ons to the Options table.
 *
 * @since 2.0.0
 */
class Data_Store_Options implements \WC_Object_Data_Store_Interface {


	/** @var string the key for the option that contains the add-on data */
	protected $option_name = 'wc_checkout_add_ons';

	/** @var string the key for the option that tracks the next add-on ID  */
	protected $id_option_name = 'wc_checkout_add_ons_next_add_on_id';


	/**
	 * Creates an add-on in the data store.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $add_on the add-on object
	 */
	public function create( &$add_on ) {

		$add_ons = $this->get_add_ons_data();

		do {

			$add_on_id = $this->get_next_add_on_id();

		} while ( isset( $add_ons[ $add_on_id ] ) );

		$add_on->set_id( $add_on_id );
		$add_on->apply_changes();

		$this->store_add_on( $add_on_id, $add_on->get_data() );
	}


	/**
	 * Reads an add-on from the data store.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $add_on the add-on object
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function read( &$add_on ) {

		$add_on->set_defaults();

		$add_on_id   = $add_on->get_id();
		$add_on_data = $this->get_add_on_data( $add_on_id );

		if ( ! $add_on_data ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid Add-On', 'woocommerce-checkout-add-ons' ) );
		}

		$add_on->set_props( $add_on_data );
		$add_on->set_object_read( true );
	}


	/**
	 * Updates an add-on in the data store.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $add_on the add-on object
	 */
	public function update( &$add_on ) {

		$add_on_id = $add_on->get_id();

		// disabling add-on
		if ( ( $changes = $add_on->get_changes() ) && isset( $changes['enabled'] ) && ! $changes['enabled'] ) {

			// reset any display rules using this add-on
			$this->reset_dependent_display_rules( $add_on_id );
		}

		// in most data stores, changes are applied to the object after they are
		// saved, but since we are storing all add-ons in a single wp_option, it
		// actually costs more to recursively update only the changes rather
		// than the entire data array at once.
		$add_on->apply_changes();
		$this->store_add_on( $add_on_id, $add_on->get_data() );
	}


	/**
	 * Deletes an add-on from the data store.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $add_on the add-on object
	 * @param array $args array of args to pass to the delete method
	 */
	public function delete( &$add_on, $args = array() ) {

		$add_on_id = $add_on->get_id();
		$add_ons   = $this->get_add_ons_data();

		unset( $add_ons[ $add_on_id ] );

		foreach ( $add_ons as $add_on_data_id => $add_on_data ) {

			// in case our array keys are somehow corrupt, loop over the add-ons and also delete any add-ons that have a matching 'id' param
			if ( isset( $add_on_data['id'] ) && ! empty( $add_on_data['id'] ) && $add_on_id === $add_on_data['id'] ) {

				unset( $add_ons[ $add_on_data_id ] );
				continue;
			}

			// reset any display rules using this add-on
			if ( ! empty( $add_on_data['rules']['other_add_on']['property'] ) && $add_on_id === $add_on_data['rules']['other_add_on']['property'] ) {
				$add_ons[ $add_on_data_id ]['rules']['other_add_on']['property'] = '';
				$add_ons[ $add_on_data_id ]['rules']['other_add_on']['operator'] = '';
				$add_ons[ $add_on_data_id ]['rules']['other_add_on']['values']   = [];
			}
		}

		$this->store_add_ons( $add_ons );
	}


	/**
	 * Reorders the add-ons in the data store.
	 *
	 * Accepts an array of add-on IDs as the new order, adding these IDs to the
	 * top and appending any IDs that exist but are not included in the param.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $ids the IDs to reorder
	 * @return bool success
	 */
	public function reorder( $ids ) {

		$add_ons     = $this->get_add_ons_data();
		$new_add_ons = array();

		foreach ( $ids as $id ) {

			if ( isset( $add_ons[ $id ] ) ) {
				$new_add_ons[ $id ] = $add_ons[ $id ];
				unset( $add_ons[ $id ] );
			}
		}

		$new_add_ons = array_merge( $new_add_ons, $add_ons );

		return (bool) $this->store_add_ons( $new_add_ons );
	}


	/**
	 * Gets the info needed to create an object for this add-on.
	 *
	 * @since 2.0.0
	 *
	 * @param string $add_on_id the add-on ID
	 * @return bool|string[]
	 *     @type string $type add-on type
	 *     @type string $classname add-on class name
	 */
	public function get_object_info( $add_on_id = '' ) {

		$info = false;

		if ( $add_on_data = $this->get_add_on_data( $add_on_id ) ) {

			$info = array(
				'type'      => isset( $add_on_data['type'] ) ? $add_on_data['type'] : '',
				'classname' => isset( $add_on_data['classname'] ) ? $add_on_data['classname'] : '',
			);
		}

		return $info;
	}


	/**
	 * Gets the array of add-on data from the database.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_add_ons_data() {

		return get_option( $this->option_name, array() );
	}


	/**
	 * Loads an add-on from the database.
	 *
	 * @since 2.0.0
	 *
	 * @param string $add_on_id the add-on ID
	 * @return array|bool false on failure
	 */
	public function get_add_on_data( $add_on_id ) {

		$add_on_id = is_string( $add_on_id ) ? $add_on_id : '';
		$add_on    = false;

		if ( '' !== $add_on_id ) {

			$add_ons = $this->get_add_ons_data();
			$add_on  = isset( $add_ons[ $add_on_id ] ) ? $add_ons[ $add_on_id ] : false;

			// if we don't have a key for the add-on, loop over the add-ons to check for a discrepancy with the ID, and repair it if found
			if ( false === $add_on ) {

				foreach ( $add_ons as $add_on_key => $add_on_data ) {

					if ( isset( $add_on_data['id'] ) && ! empty( $add_on_data['id'] ) && $add_on_id === $add_on_data['id'] ) {

						$add_ons[ $add_on_id ] = $add_on_data;
						$add_on                 = $add_on_data;

						unset( $add_ons[ $add_on_key ] );
					}
				}
			}
		}

		return $add_on;
	}


	/**
	 * Stores the array of add-ons to the database.
	 *
	 * @since 2.0.0
	 *
	 * @param array $add_ons array of add-ons data
	 * @return bool success
	 */
	protected function store_add_ons( array $add_ons ) {

		return update_option( $this->option_name, $add_ons );
	}


	/**
	 * Stores a single add-on to the database.
	 *
	 * @since 2.0.0
	 *
	 * @param string $add_on_id the add-on ID
	 * @param array $add_on_data the add-on data
	 */
	protected function store_add_on( $add_on_id, array $add_on_data ) {

		$add_ons               = $this->get_add_ons_data();
		$add_ons[ $add_on_id ] = $add_on_data;

		$this->store_add_ons( $add_ons );
	}


	/**
	 * Get the next available add-on ID
	 *
	 * @since 2.0.0
	 *
	 * @return string the next available add-on ID
	 */
	protected function get_next_add_on_id() {

		$add_ons = $this->get_add_ons_data();

		do {

			$next_add_on_id = substr( md5( microtime() ), 0, 7 );

		// make sure we aren't using an ID that's already in use, or a pure-numeric ID
		// to avoid any chance of it getting converted to an int and breaking the array key
		// also make sure the first character isn't numeric so it can be a valid CSS class
		} while ( is_numeric( $next_add_on_id ) || isset( $add_ons[ $next_add_on_id ] ) || is_numeric( $next_add_on_id[0] ) );

		return $next_add_on_id;
	}


	/**
	 * Resets dependent display rules when an add-on is disabled/deleted.
	 *
	 * @since 2.1.0
	 *
	 * @param int $add_on_id ID of the add-on being disabled/deleted
	 */
	private function reset_dependent_display_rules( $add_on_id ) {

		$add_ons     = $this->get_add_ons_data();
		$should_save = false;
		foreach ( $add_ons as $add_on_data_id => $add_on_data ) {

			// reset any display rules using this add-on
			if ( ! empty( $add_on_data['rules']['other_add_on']['property'] ) && $add_on_id === $add_on_data['rules']['other_add_on']['property'] ) {
				$add_ons[ $add_on_data_id ]['rules']['other_add_on']['property'] = '';
				$add_ons[ $add_on_data_id ]['rules']['other_add_on']['operator'] = '';
				$add_ons[ $add_on_data_id ]['rules']['other_add_on']['values']   = [];

				$should_save = true;
			}
		}

		if ( $should_save ) {
			$this->store_add_ons( $add_ons );
		}
	}


	/** No-op: Checkout Add-Ons do not support meta data */
	public function read_meta( &$data ) {}

	/** No-op: Checkout Add-Ons do not support meta data */
	public function delete_meta( &$data, $meta ) {}

	/** No-op: Checkout Add-Ons do not support meta data */
	public function add_meta( &$data, $meta ) {}

	/** No-op: Checkout Add-Ons do not support meta data */
	public function update_meta( &$data, $meta ) {}


}
