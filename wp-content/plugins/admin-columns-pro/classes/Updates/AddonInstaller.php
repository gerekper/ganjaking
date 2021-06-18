<?php

namespace ACP\Updates;

use AC\Ajax;
use AC\Registrable;
use ACP\API;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\RequestDispatcher;
use ACP\Type\SiteUrl;
use Plugin_Upgrader;
use WP_Ajax_Upgrader_Skin;
use WP_Error;

class AddonInstaller implements Registrable {

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	/** @var LicenseRepository */
	private $license_repository;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	public function __construct( RequestDispatcher $api, LicenseRepository $license_repository, LicenseKeyRepository $license_key_repository, SiteUrl $site_url ) {
		$this->api = $api;
		$this->license_repository = $license_repository;
		$this->license_key_repository = $license_key_repository;
		$this->site_url = $site_url;
	}

	public function register() {
		$this->get_ajax_handler()->register();
	}

	/**
	 * @return Ajax\Handler
	 */
	protected function get_ajax_handler() {
		$handler = new Ajax\Handler();
		$handler->set_action( 'acp-install-addon' )
		        ->set_callback( [ $this, 'ajax_handle_request' ] );

		return $handler;
	}

	public function ajax_handle_request() {
		$this->get_ajax_handler()->verify_request();

		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$license_key = $this->license_key_repository->find();

		$license = $license_key
			? $this->license_repository->find( $license_key )
			: null;

		if ( ! $license || ! $license->is_active() ) {
			$message = __( 'License is not active.', 'codepress-admin-columns' ) . ' ' . sprintf( __( 'Enter your license key on <a href="%s">the settings page</a>.', 'codepress-admin-columns' ), acp_get_license_page_url() );

			wp_send_json_error( $message );
		}

		$plugin_name = filter_input( INPUT_POST, 'plugin_name' );

		$response = $this->api->dispatch( new API\Request\DownloadInformation( $plugin_name, $license->get_key(), $this->site_url ) );

		// Check download permission by requesting download information.
		if ( $response->has_error() ) {
			wp_send_json_error( $response->get_error()->get_error_message() );
		}

		$plugin_basename = $this->install_plugin( $response->get( 'download_link' ) );

		if ( is_wp_error( $plugin_basename ) ) {
			wp_send_json_error( $plugin_basename->get_error_message() );
		}

		if ( ! $plugin_basename ) {
			wp_send_json_error( __( 'Install failed.', 'codepress-admin-columns' ) );
		}

		$network_wide = '1' === filter_input( INPUT_POST, 'network_wide' );

		$is_active = null === activate_plugin( $plugin_basename, '', $network_wide );

		$status = __( 'Installed', 'codepress-admin-columns' );

		if ( $is_active ) {
			$status = $network_wide
				? __( 'Network Active', 'codepress-admin-columns' )
				: __( 'Active', 'codepress-admin-columns' );
		}

		wp_send_json_success( [
			'activated' => $is_active,
			'status'    => $status,
		] );
	}

	/**
	 * @param string $package_url zip file
	 *
	 * @return string|WP_Error|false Plugin basename on success. False or WP_Error when failed.
	 */
	private function install_plugin( $package_url ) {
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		$result = $upgrader->install( $package_url );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( $skin->get_errors()->get_error_codes() ) {
			return $skin->get_errors();
		}

		if ( true !== $result ) {
			return false;
		}

		return $upgrader->plugin_info();
	}

}