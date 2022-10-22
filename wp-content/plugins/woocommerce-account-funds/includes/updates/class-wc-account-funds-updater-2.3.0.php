<?php
/**
 * Class that updates DB in 2.3.0.
 *
 * @package WC_Account_Funds
 * @since   2.7.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Updater for 2.3.0.
 */
class WC_Account_Funds_Updater_2_3_0 implements WC_Account_Funds_Updater {

	/**
	 * {@inheritdoc}
	 */
	public function update() {
		/**
		 * We are updating the plugin from an older version than 2.3.0,
		 * so don't need to execute the migration script of version 2.3.7.
		 */
		update_option( 'wc_account_funds_skip_migration_237', 'yes' );
	}
}

return new WC_Account_Funds_Updater_2_3_0();
