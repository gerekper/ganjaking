<?php
/**
 * Handles encryption of stored tokens.
 *
 * @package    WooCommerce Xero
 * @since      x.x.x
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_XR_Encrypt_Legacy_Tokens_Migration.
 *
 * @since x.x.x
 */
class WC_XR_Encrypt_Legacy_Tokens_Migration {
	/**
	 * Migration id.
	 *
	 * @since x.x.x
	 * @var string
	 */
	private $migration_id = 'wc_xr_encrypt_legacy_tokens_migration';

	/**
	 * Register migration.
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function setup_hook() {
		add_action( 'init', array( $this, 'run' ) );
	}

	/**
	 * Should run migration.
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function run() {
		// Exit if migration completed.
		if ( $this->is_completed() ) {
			return;
		}

		$token_data_option_name = 'xero_oauth_options';
		$tokens_data            = get_option( $token_data_option_name, false );

		// Encrypt token only if token data exist.
		if ( $tokens_data ) {
			$wc_xr_data_encryption = new WC_XR_Data_Encryption();

			// Encrypt token only if value is non-empty. Default value of token is NULL.
			if ( $tokens_data['token'] ) {
				$tokens_data['token'] = $wc_xr_data_encryption->encrypt( $tokens_data['token'] );
			}

			// Encrypt refresh_token only if value is non-empty. Default value of refresh_token is NULL.
			if ( $tokens_data['refresh_token'] ) {
				$tokens_data['refresh_token'] = $wc_xr_data_encryption->encrypt( $tokens_data['refresh_token'] );
			}

			// Store token data with encrypted token values.
			update_option( $token_data_option_name, $tokens_data, false );
		}

		// Complete migration.
		update_option( $this->migration_id, 1, 'no' );
	}

	/**
	 * Should return result whether migration completed or not.
	 *
	 * @since x.x.x
	 */
	private function is_completed(): bool {
		return (bool) get_option( $this->migration_id, 0 );
	}
}
