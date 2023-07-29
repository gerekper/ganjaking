<?php
/**
 * WooCommerce Customer/Order/Coupon Export
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\CSV_Export\Automations;

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Data Store for persisting Automated Exports to the Options table.
 *
 * @since 5.0.0
 */
class Automation_Data_Store_Options implements \WC_Object_Data_Store_Interface {


	/** @var string the key for the option that contains the automations data */
	protected $option_name = 'wc_customer_order_export_automations';


	/**
	 * Creates an automation in the data store.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation the automated export object
	 */
	public function create( &$automation ) {

		$automation_id = $this->get_next_automation_id();

		$automation->set_id( $automation_id );
		$automation->apply_changes();

		$this->store_automation( $automation_id, $automation->get_data() );

		/**
		 * Fires after a new Automation has been created.
		 *
		 * @since 5.0.0
		 *
		 * @param string $automation_id the Automation ID
		 * @param Automation $automation the Automation object
		 */
		do_action( 'wc_customer_order_export_new_automation', $automation_id, $automation );
	}


	/**
	 * Reads an automation from the data store.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation the automation object
	 * @throws Framework\SV_WC_Plugin_Exception if the automation cannot be found
	 */
	public function read( &$automation ) {

		$automation->set_defaults();

		$automation_data = $this->get_automation_data( $automation->get_id() );

		if ( ! $automation_data ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid Automation', 'woocommerce-customer-order-csv-export' ) );
		}

		$automation->set_props( $automation_data );
		$automation->set_object_read( true );
	}


	/**
	 * Updates an automation in the data store.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation the automation object
	 */
	public function update( &$automation ) {

		$automation->apply_changes();

		// in most data stores, changes are applied to the object after they are
		// saved, but since we are storing all automations in a single wp_option, it
		// actually costs more to recursively update only the changes rather
		// than the entire data array at once.
		$this->store_automation( $automation->get_id(), $automation->get_data() );

		/**
		 * Fires after an Automation has been updated.
		 *
		 * @since 5.0.0
		 *
		 * @param string $automation_id the Automation ID
		 * @param Automation $automation the Automation object
		 */
		do_action( 'wc_customer_order_export_update_automation', $automation->get_id(), $automation );
	}


	/**
	 * Deletes an automation from the data store.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation the automation object
	 * @param array $args array of args to pass to the delete method
	 */
	public function delete( &$automation, $args = array() ) {

		$automations   = $this->get_automations_data();
		$automation_id = $automation->get_id();

		unset( $automations[ $automation_id ] );

		foreach ( $automations as $automation_data_id => $automation_data ) {

			// in case our array keys are somehow corrupt, loop over the automations and also delete any automations that have a matching 'id' param
			if ( ! empty( $automation_data['id'] ) && $automation_id === $automation_data['id'] ) {
				unset( $automations[ $automation_data_id ] );
			}
		}

		$this->store_automations_data( $automations );

		/**
		 * Fires after an Automation has been deleted.
		 *
		 * @since 5.0.0
		 *
		 * @param string $automation_id the Automation ID
		 */
		do_action( 'wc_customer_order_export_delete_automation', $automation_id );
	}


	/**
	 * Gets the array of automations data from the database.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function get_automations_data() {

		return get_option( $this->option_name, [] );
	}


	/**
	 * Loads an automation from the database.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id the ID of an automation
	 * @return array|bool false on failure
	 */
	public function get_automation_data( $automation_id ) {

		$automation_id = is_string( $automation_id ) ? $automation_id : '';
		$automation    = false;

		if ( '' !== $automation_id ) {

			$automations = $this->get_automations_data();
			$automation  = isset( $automations[ $automation_id ] ) ? $automations[ $automation_id ] : false;

			// if we don't have a key for the automation, loop over the automations to check for
			// a discrepancy with the ID, and repair it if found
			if ( false === $automation ) {

				foreach ( $automations as $automation_data_id => $automation_data ) {

					if ( $automation_id === $automation_data['id'] ) {

						$automations[ $automation_id ] = $automation_data;
						$automation                    = $automation_data;

						unset( $automations[ $automation_data_id ] );
					}
				}

				if ( false !== $automation ) {
					$this->store_automations_data( $automations );
				}
			}
		}

		return $automation;
	}


	/**
	 * Stores the array of automations to the database.
	 *
	 * @since 5.0.0
	 *
	 * @param array $automations array of automations data
	 * @return bool success
	 */
	protected function store_automations_data( array $automations ) {

		return update_option( $this->option_name, $automations );
	}


	/**
	 * Stores a single automation to the database.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id the ID of the automation
	 * @param array $automation_data the automation data
	 */
	protected function store_automation( $automation_id, array $automation_data ) {

		$automations = $this->get_automations_data();

		$automations[ $automation_id ] = $automation_data;

		$this->store_automations_data( $automations );
	}


	/**
	 * Gets the next available automation ID
	 *
	 * @since 5.0.0
	 *
	 * @return string the next available automation ID
	 */
	protected function get_next_automation_id() {

		$automations = $this->get_automations_data();

		do {

			$next_id = substr( md5( microtime() ), 0, 7 );

		// make sure we aren't using an ID that's already in use, or a pure-numeric ID
		// to avoid any chance of it getting converted to an int and breaking the array key
		// also make sure the first character isn't numeric so it can be a valid CSS class
		} while ( is_numeric( $next_id ) || isset( $automations[ $next_id ] ) || is_numeric( $next_id[0] ) );

		return $next_id;
	}


	/** No-op: Automations do not support meta data */
	public function read_meta( &$data ) {}


	/** No-op: Automations do not support meta data */
	public function delete_meta( &$data, $meta ) {}


	/** No-op: Automations do not support meta data */
	public function add_meta( &$data, $meta ) {}


	/** No-op: Automations do not support meta data */
	public function update_meta( &$data, $meta ) {}


}
