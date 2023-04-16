<?php
/**
 * Handle sensitive data encryption and decryption.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Bookings_Encryption.
 *
 * @since 1.15.79
 */
class WC_Bookings_Encryption {

	const ENCRYPTION_MIGRATE_OPTION = 'wc_bookings_encrypt_tokens_migrated';

	/**
	 * Holds instance of class.
	 *
	 * @var WC_Bookings_Encryption
	 */
	protected static $instance = null;

	/**
	 * Encryption Key.
	 *
	 * @since 1.15.79
	 * @var string
	 */
	private $key;

	/**
	 * Encryption Salt.
	 *
	 * @since 1.15.79
	 * @var string
	 */
	private $salt;

	/**
	 * Class constructor.
	 *
	 * @since 1.15.79
	 */
	public function __construct() {
		$this->key  = $this->get_default_key();
		$this->salt = $this->get_default_salt();

		if ( 0 === intval( get_option( self::ENCRYPTION_MIGRATE_OPTION, 0 ) ) ) {
			add_action( 'init', array( $this, 'migrate' ) );
		}
	}

	/**
	 * Perform migration of non-encrypted tokens
	 *
	 * @return void
	 */
	public function migrate() {
		// Exit if migration completed.
		if ( 1 === intval( get_option( self::ENCRYPTION_MIGRATE_OPTION, 0 ) ) ) {
			return;
		}

		// Delete transients to be recreated.
		delete_transient( 'wc_bookings_gcalendar_sync_token' );
		delete_transient( 'wc_bookings_gcalendar_access_token' );

		$refresh_token = get_option( 'wc_bookings_gcalendar_refresh_token' );

		// Only encrypt if the token exists and it is not encrypted.
		if ( ! empty( $refresh_token ) && empty( $this->decrypt( $refresh_token ) ) ) {
			$refresh_token = $this->encrypt( $refresh_token );
			update_option( 'wc_bookings_gcalendar_refresh_token', $refresh_token );
		}

		// Complete migration.
		update_option( self::ENCRYPTION_MIGRATE_OPTION, 1 );
	}

	/**
	 * Get the Logged-in key for encryption.
	 *
	 * @since 1.15.79
	 */
	private function get_default_key(): string {
		if ( '' !== wp_salt( 'logged_in' ) ) {
			return wp_salt( 'logged_in' );
		}

		// If this is reached, you're either not on a live site or have a serious security issue.
		return 'this-is-a-dangerous-key';
	}

	/**
	 * Get the SALT key for encryption.
	 *
	 * @since 1.15.79
	 */
	private function get_default_salt(): string {
		if ( defined( 'LOGGED_IN_SALT' ) && '' !== LOGGED_IN_SALT ) {
			return LOGGED_IN_SALT;
		}

		// If this is reached, you're either not on a live site or have a serious security issue.
		return 'this-is-a-dangerous-salt';
	}

	/**
	 * The encryption method.
	 *
	 * @param string $value Value to encryption.
	 *
	 * @since 1.15.79
	 */
	public function encrypt( string $value ): string {
		if ( ! extension_loaded( 'openssl' ) ) {
			return $value;
		}

		$method = 'aes-256-ctr';
		$ivlen  = openssl_cipher_iv_length( $method );
		$iv     = openssl_random_pseudo_bytes( $ivlen );

		$raw_value = openssl_encrypt( $value . $this->salt, $method, $this->key, 0, $iv );
		if ( ! $raw_value ) {
			return false;
		}

		return base64_encode( $iv . $raw_value ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * The decryption method.
	 *
	 * @param string $raw_value Encrypted value for decryption.
	 * @since 1.15.79
	 */
	public function decrypt( string $raw_value ): string {
		if ( ! extension_loaded( 'openssl' ) ) {
			return $raw_value;
		}

		$raw_value = base64_decode( $raw_value, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		$method = 'aes-256-ctr';
		$ivlen  = openssl_cipher_iv_length( $method );
		$iv     = substr( $raw_value, 0, $ivlen );

		$raw_value = substr( $raw_value, $ivlen );

		$value = openssl_decrypt( $raw_value, $method, $this->key, 0, $iv );
		if ( ! $value || substr( $value, - strlen( $this->salt ) ) !== $this->salt ) {
			return false;
		}

		return substr( $value, 0, - strlen( $this->salt ) );
	}

	/**
	 * Returns the single instance of class.
	 *
	 * @return WC_Bookings_Encryption
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
