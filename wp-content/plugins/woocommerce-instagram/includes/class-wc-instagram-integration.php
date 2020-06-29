<?php
/**
 * WooCommerce Instagram Integration
 *
 * @package WC_Instagram
 * @since   2.0.0
 */

/**
 * Instagram integration class.
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

		add_action( 'init', array( $this, 'init_settings_api' ) );
		add_action( "woocommerce_update_options_integration_{$this->id}", array( $this, 'process_admin_options' ) );
	}

	/**
	 * Initializes the settings API.
	 *
	 * @since 3.0.0
	 */
	public function init_settings_api() {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_GET['section'] ) && 'instagram' === $_GET['section'] && isset( $_GET['catalog_id'] ) ) {
			$catalog_id = (string) wc_clean( wp_unslash( $_GET['catalog_id'] ) );
			$action     = ( isset( $_GET['action'] ) ? wc_clean( wp_unslash( $_GET['action'] ) ) : '' );

			if ( 'download' === $action ) {
				$format = ( ! empty( $_GET['format'] ) ? wc_clean( wp_unslash( $_GET['format'] ) ) : 'xml' );
				$this->export_product_catalog( $catalog_id, $format );
			} elseif ( 'delete' === $action ) {
				$this->delete_product_catalog( $catalog_id );
			} elseif ( 'deleted' === $action ) {
				WC_Admin_Settings::add_message( _x( 'Catalog deleted successfully.', 'settings notice', 'woocommerce-instagram' ) );
			} else {
				include_once 'admin/settings/class-wc-instagram-settings-product-catalog.php';
				$this->settings_api = new WC_Instagram_Settings_Product_Catalog( $catalog_id );
				return;
			}
		}

		include_once 'admin/settings/class-wc-instagram-settings-general.php';
		$this->settings_api = new WC_Instagram_Settings_General();
		// phpcs:enable WordPress.Security.NonceVerification
	}

	/**
	 * Output the settings screen.
	 *
	 * @since 2.0.0
	 */
	public function admin_options() {
		$this->settings_api->admin_options();
	}

	/**
	 * Processes and saves options.
	 *
	 * @since 2.0.0
	 *
	 * @return bool was anything saved?
	 */
	public function process_admin_options() {
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

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html_x( 'You do not have permission to delete the catalog.', 'error notice', 'woocommerce-instagram' ) );
		}

		$product_catalogs = wc_instagram_get_product_catalogs();

		if ( ! isset( $product_catalogs[ $catalog_id ] ) ) {
			wp_die( esc_html_x( 'Product catalog not found.', 'error notice', 'woocommerce-instagram' ) );
		}

		unset( $product_catalogs[ $catalog_id ] );

		update_option( 'wc_instagram_product_catalogs', $product_catalogs );

		wp_safe_redirect(
			wc_instagram_get_settings_url(
				array(
					'catalog_id' => $catalog_id,
					'action'     => 'deleted',
				)
			)
		);
		exit();
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
			wp_die( esc_html_x( 'Product catalog not found.', 'error notice', 'woocommerce-instagram' ) );
		}

		$charset   = get_option( 'blog_charset' );
		$formatter = wc_instagram_get_product_catalog_formatter( $product_catalog, array( 'charset' => $charset ), $format );

		if ( ! $formatter ) {
			wp_die( esc_html_x( 'The format used to export the product catalog is not valid.', 'error notice', 'woocommerce-instagram' ) );
		}

		$filename = sprintf( '%1$s-%2$s.%3$s', $product_catalog->get_slug(), gmdate( 'Y-m-d' ), $format );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/' . $format . '; charset=' . $charset, true );

		echo $formatter->get_output(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		die();
	}
}
