<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Migrate Vendor Admin Storage Class.
 *
 * Migrate vendor admin storage from the vendor_data meta object to individual meta entries.
 *
 * @category CLI
 * @package WooCommerce Product Vendors/CLI
 * @version future
 * @since future
 */
class WC_Product_Vendors_Cli_Vendor_Admin_Storage extends \WP_CLI_Command {
	/**
	 * Migrate vendor admin storage.
	 * @since future
	 * @version future
	 * @param array $args
	 *
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : Simulate script without doing any live changes.
	 *
	 * [--batch-size]
	 * : Number of items to process in a single batch.
	 */
	public function __invoke( $args, $assoc_args ) {
		WP_CLI::log( 'Starting Vendor Admin storage migration' );

		$dry_mode = isset( $assoc_args['dry-run'] );

		if ( true === $dry_mode ) {
			\WP_CLI::log( '===Dry Run===' );
		} else {
			\WP_CLI::log( 'Doing it live!' );
		}

		$batch_size = (int) ( $assoc_args['batch-size'] ?? WC_Product_Vendors_Admin_Storage_Compatibility::BATCH_SIZE );
		\WP_CLI::log(
			sprintf(
				'Batch size set to %d',
				$batch_size
			)
		);

		$batch = 0;
		$admin_storage = new WC_Product_Vendors_Admin_Storage_Compatibility();
		do {
			\WP_CLI::log(
				sprintf(
					'Running batch %d...',
					$batch
				)
			);
	
			$vendors = $admin_storage->get_vendors_to_migrate(
				// If we're in dry mode, we want to items won't be processed in the next batch. This ensures we can loop through all items.
				( true === $dry_mode ) ? $batch : 0,
				$batch_size
			);

			
			\WP_CLI::log(
				sprintf(
					'Found %d vendors to migrate in batch %d',
					count( $vendors ),
					$batch
				)
			);
			if ( $dry_mode ) {
				\WP_CLI::log(
					sprintf(
						'DRY RUN migrating %d vendors in batch %d',
						count( $vendors ),
						$batch
					)
				);
			} else {
				\WP_CLI::log(
					sprintf(
						'Migrating %d vendors in batch %d',
						count( $vendors ),
						$batch
					)
				);
				$success = $admin_storage->migrate_vendors( $vendors );
				if ( false === $success ) {
					\WP_CLI::error( 		
						sprintf(
							'Migration of batch %d failed',
							$batch
						)
					);
					continue;
				}

				\WP_CLI::log(
					sprintf(
						'Migrated %d vendors in batch %d',
						count( $vendors ),
						$batch
					)
				);
			}

			\WP_CLI::log(
				sprintf(
					'Completed batch %d',
					$batch
				)
			);
			$this->pause();
			$batch++;
		} while ( count( $vendors ) === $batch_size );

		// Set the admin storage as migrated.
		if ( ! $dry_mode ) {
			WP_CLI::line( 'Activated vendor admin meta storage. To revert, run: wp option delete wcpv_admin_storage_migrated' );
			$admin_storage->set_admin_storage_migrated();
		}

		WP_CLI::line( 'Finished Vendor Admin storage migration' );
	}

	/**
	 * Pause to allow the database to catch up.
	 */
	private function pause() {
		sleep( 3 );
	}
}