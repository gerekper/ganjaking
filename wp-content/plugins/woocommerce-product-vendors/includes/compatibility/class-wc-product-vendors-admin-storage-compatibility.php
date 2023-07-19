<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( '\WC_Product_Vendors_Utils' ) ) {
	require_once( dirname( __FILE__ ) . '/../class-wc-product-vendors-utils.php' );
}

/**
 * Migrate Vendor Admin Storage Class.
 *
 * Migrate vendor admin storage from the vendor_data meta object to individual meta entries.
 */
class WC_Product_Vendors_Admin_Storage_Compatibility {
	const BATCH_SIZE = 100;

	/**
	 * Initialize actions.
	 */
	public function init() {
		add_action( 'wcpv_process_admin_migration_batch', array( $this, 'migrate_batch' ) );
	}

	/**
	 * Schedule admin storage migration.
	 */
	public function schedule_migration() {
		if ( $this->is_migration_scheduled() ) {
			return;
		}
		$this->update_scheduled_migration_status( true );
		try {	
			$batch = 0;

			do {
				$vendors = $this->get_vendors_to_migrate( $batch, self::BATCH_SIZE );
				as_schedule_single_action(
					time(),
					'wcpv_process_admin_migration_batch',
					[
						$vendors,
					],
					'woocommerce-product-vendors'
				);
				$batch++;
			} while ( count( $vendors ) === self::BATCH_SIZE );
		} catch ( Exception $e ) {
			$this->update_scheduled_migration_status( false );
			return false;
		}
		return true;
	}

	/**
	 * Update the scheduled migration status.
	 *
	 * @param bool $status
	 */
	public function update_scheduled_migration_status( $status ) {
		update_option( 'wcpv_admin_storage_migration_scheduled', $status );
	}

	/**
	 * Check if admin storage migration is scheduled.
	 *
	 * @return bool
	 */
	public function is_migration_scheduled() {
		return '1' === get_option( 'wcpv_admin_storage_migration_scheduled', '0' );
	}


	/**
	 * Migrate admin storage batch.
	 *
	 * @param array $args
	 */
	public function migrate_batch( $vendors ) {
		$this->migrate_vendors( $vendors );

		// Last batch will be smaller then batch size, complete migration.
		if ( count( $vendors ) < self::BATCH_SIZE ) {
			$this->set_admin_storage_migrated();
		}
	}

	/**
	 * Process a batch of vendor admin storage migration.
	 * @since future
	 * @version future
	 * @param array $vendors Array of vendor vendors.
	 * 
	 * @return bool
	 */
	public function migrate_vendors( $vendors ) {
		if ( empty( $vendors ) ) {
			return;
		}

		$success = true;
		foreach ( $vendors as $vendor ) {
			foreach ( $vendor['admin_ids'] as $admin_id ) {
				$result = WC_Product_Vendors_Utils::set_vendor_admin(
					$vendor['vendor_id'],
					$admin_id
				);

				if ( false === $result ) {
					$success = false;
				}
			}
		}

		return $success;
	}

	/**
	 * Get the next batch of vendors to migrate.
	 * 
	 * @since future
	 * @version future
	 * @param int $batch_size The number of vendors to get.
	 * @param int $batch Optional param to get a specific batch.
	 * @return array
	 */
	public function get_vendors_to_migrate( $batch, $batch_size ) {
		global $wpdb;

		return array_map(
			function( $vendor ) {
				$vendor_data = maybe_unserialize( $vendor->vendor_data );
				$vendor_admins = empty( $vendor_data['admins'] ) ? array() : $vendor_data['admins'];
				if ( ! is_array( $vendor_admins ) ) {
					$vendor_admins = array( $vendor_admins );
				}
				return array(
					'vendor_id' => absint( $vendor->vendor_id ),
					'admin_ids' => array_map(
						'absint',
						$vendor_admins
					),
				);
			},
			$wpdb->get_results(
				$wpdb->prepare(
					"SELECT
						vendor_id,
						vendor_data
					FROM (
						SELECT
							vendor.term_id as vendor_id,
							vendor.meta_value as vendor_data,
							admin.meta_value as admin_id
						FROM {$wpdb->prefix}termmeta vendor
						LEFT JOIN {$wpdb->prefix}termmeta admin
							ON admin.term_id = vendor.term_id
							AND admin.meta_key = '_wcpv_admin_id'
						WHERE vendor.meta_key = 'vendor_data'
					) vendors
					WHERE vendors.admin_id IS NULL
					LIMIT %d OFFSET %d",
					$batch_size,
					$batch_size * $batch
				)
			)
		);
	} 

	/**
	 * Set the admin storage as migrated.
	 */
	public function set_admin_storage_migrated() {
		return update_option( 'wcpv_admin_storage_migrated', true );
	}
}

(new WC_Product_Vendors_Admin_Storage_Compatibility())->init();