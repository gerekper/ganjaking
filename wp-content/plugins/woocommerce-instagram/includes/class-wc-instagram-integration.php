<?php
/**
 * WooCommerce Instagram Integration
 *
 * @package WC_Instagram
 * @since   2.0.0
 */

/**
 * Instagram's integration class.
 */
class WC_Instagram_Integration extends WC_Integration {

	/**
	 * The settings API instance.
	 *
	 * @since 3.0.0
	 *
	 * @var WC_Instagram_Settings_API
	 */
	protected $settings_api;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->plugin_id          = 'wc_';
		$this->id                 = 'instagram';
		$this->method_title       = _x( 'Instagram', 'settings page title', 'woocommerce-instagram' );
		$this->method_description = _x( 'Connect your Instagram Business account.', 'settings page description', 'woocommerce-instagram' );

		add_action( 'init', array( $this, 'init' ) );
		add_action( "woocommerce_update_options_integration_{$this->id}", array( $this, 'process_admin_options' ) );
	}

	/**
	 * Init.
	 *
	 * @since 3.4.4
	 */
	public function init() {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( ! isset( $_GET['tab'] ) || 'integration' !== $_GET['tab'] ||
			( isset( $_GET['section'] ) && 'instagram' !== $_GET['section'] )
		) {
			return;
		}
		// phpcs:enable WordPress.Security.NonceVerification

		$catalog_id = $this->get_catalog_id();

		if ( ! $catalog_id ) {
			$this->init_settings_api();
			return;
		}

		$catalog_id      = ( 'new' === $catalog_id ? 0 : $catalog_id );
		$product_catalog = WC_Instagram_Product_Catalog_Factory::get_catalog( $catalog_id );

		if ( ! $product_catalog || ! wc_instagram_is_connected() ) {
			wp_die( 'Something went wrong' );
		}

		$action = ( isset( $_GET['action'] ) ? wc_clean( wp_unslash( $_GET['action'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

		if ( $action && $catalog_id ) {
			$this->process_catalog_action( $catalog_id, $action );
		} else {
			$this->init_settings_api( $product_catalog );
		}
	}

	/**
	 * Gets the current catalog ID.
	 *
	 * @since 3.4.4
	 *
	 * @return string
	 */
	protected function get_catalog_id() {
		$catalog_id = '';

		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_GET['catalog_id'] ) ) {
			$catalog_id = (string) wc_clean( wp_unslash( $_GET['catalog_id'] ) );
		} elseif ( isset( $_POST['catalog_id'] ) && wc_instagram_is_request( 'ajax' ) ) {
			$catalog_id = (string) wc_clean( wp_unslash( $_POST['catalog_id'] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification

		return $catalog_id;
	}

	/**
	 * Processes a catalog action.
	 *
	 * @since 3.4.4
	 *
	 * @param string $catalog_id The catalog ID.
	 * @param string $action     The action to process.
	 */
	protected function process_catalog_action( $catalog_id, $action ) {
		if ( 'download' === $action ) {
			$format = ( ! empty( $_GET['format'] ) ? wc_clean( wp_unslash( $_GET['format'] ) ) : 'xml' ); // phpcs:ignore WordPress.Security.NonceVerification
			$this->export_product_catalog( $catalog_id, $format );
		} elseif ( 'delete' === $action ) {
			$this->delete_product_catalog( $catalog_id );
		}
	}

	/**
	 * Initializes the settings API.
	 *
	 * @since 3.0.0
	 * @since 3.4.4 Added parameter `$catalog_id`.
	 * @since 4.0.0 The first parameter must be a product catalog object.
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Optional. Product catalog object. Default false.
	 */
	public function init_settings_api( $product_catalog = false ) {
		if ( $product_catalog ) {
			$this->settings_api = new WC_Instagram_Settings_Product_Catalog( $product_catalog );
		} else {
			$this->settings_api = new WC_Instagram_Settings_General();
		}
	}

	/**
	 * Output the settings screen.
	 *
	 * @since 2.0.0
	 */
	public function admin_options() {
		if ( $this->settings_api ) {
			$this->settings_api->admin_options();
		}
	}

	/**
	 * Processes and saves options.
	 *
	 * @since 2.0.0
	 *
	 * @return bool was anything saved?
	 */
	public function process_admin_options() {
		if ( ! $this->settings_api ) {
			return false;
		}

		return $this->settings_api->process_admin_options();
	}

	/**
	 * Generates a Text Input HTML.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key Field key.
	 * @param array  $data Field data.
	 * @return string
	 */
	public function generate_text_html( $key, $data ) {
		// Checks if the field type is defined in the settings form.
		if ( isset( $data['type'] ) && method_exists( $this->settings_api, "generate_{$data['type']}_html" ) ) {
			return call_user_func( array( $this->settings_api, "generate_{$data['type']}_html" ), $key, $data );
		}

		return parent::generate_text_html( $key, $data );
	}

	/**
	 * Validates a Text Field.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key Field key.
	 * @param string $value Posted Value.
	 * @return string
	 */
	public function validate_text_field( $key, $value ) {
		// Checks if the key validation is defined in the settings API instance.
		if ( is_callable( array( $this->settings_api, 'validate_' . $key . '_field' ) ) ) {
			return $this->settings_api->{'validate_' . $key . '_field'}( $key, $value );
		}

		$field = $this->settings_api->get_form_field( $key );

		// Checks if the type validation is defined in the settings API instance.
		if ( $field && isset( $field['type'] ) && is_callable( array( $this->settings_api, 'validate_' . $field['type'] . '_field' ) ) ) {
			return $this->settings_api->{'validate_' . $field['type'] . '_field'}( $key, $value );
		}

		return parent::validate_text_field( $key, $value );
	}

	/**
	 * Deletes the product catalog.
	 *
	 * @since 3.0.0
	 *
	 * @param int $catalog_id The catalog ID.
	 */
	protected function delete_product_catalog( $catalog_id ) {
		check_admin_referer( 'wc_instagram_delete_product_catalog' );

		$result = wc_instagram_delete_product_catalog( $catalog_id );

		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}

		wp_safe_redirect( wc_instagram_get_settings_url( array( 'notice' => 'catalog_deleted' ) ) );
	}

	/**
	 * Exports a product catalog.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $catalog_id The catalog ID.
	 * @param string $format     The file format.
	 */
	protected function export_product_catalog( $catalog_id, $format = 'xml' ) {
		$product_catalog = wc_instagram_get_product_catalog( $catalog_id );

		if ( ! $product_catalog ) {
			wp_die( esc_html__( 'Product catalog not found.', 'woocommerce-instagram' ) );
		}

		$charset       = get_option( 'blog_charset' );
		$format        = strtolower( $format );
		$file          = $product_catalog->get_file( $format );
		$last_modified = $file->get_last_modified();

		if ( ! $last_modified ) {
			wp_die( esc_html__( 'Product catalog file not found.', 'woocommerce-instagram' ) );
		}

		$filename = sprintf( '%1$s-%2$s.%3$s', $product_catalog->get_slug(), $last_modified->date_i18n( 'Y-m-d-H-i' ), $format );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/' . $format . '; charset=' . $charset );

		echo $file->get_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		die();
	}
}
