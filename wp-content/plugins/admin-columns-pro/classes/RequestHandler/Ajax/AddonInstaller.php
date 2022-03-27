<?php

namespace ACP\RequestHandler\Ajax;

use AC\Capabilities;
use AC\IntegrationRepository;
use AC\Nonce;
use AC\PluginInformation;
use AC\Request;
use AC\Type\Url;
use ACP\Access\ActivationStorage;
use ACP\ActivationTokenFactory;
use ACP\API;
use ACP\RequestAjaxHandler;
use ACP\RequestDispatcher;
use ACP\Type\SiteUrl;
use Plugin_Upgrader;
use WP_Ajax_Upgrader_Skin;
use WP_Error;

class AddonInstaller implements RequestAjaxHandler {

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var ActivationStorage
	 */
	private $activation_storage;

	/**
	 * @var ActivationTokenFactory
	 */
	private $activation_token_factory;

	/**
	 * @var IntegrationRepository
	 */
	private $integrations;

	/**
	 * @var bool
	 */
	private $is_network_active;

	public function __construct( RequestDispatcher $api, SiteUrl $site_url, ActivationStorage $activation_storage, ActivationTokenFactory $activation_token_factory, IntegrationRepository $integrations, $is_network_active ) {
		$this->api = $api;
		$this->site_url = $site_url;
		$this->activation_storage = $activation_storage;
		$this->activation_token_factory = $activation_token_factory;
		$this->integrations = $integrations;
		$this->is_network_active = (bool) $is_network_active;
	}

	public function handle() {
		$request = new Request();

		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! ( new Nonce\Ajax() )->verify( $request ) ) {
			wp_send_json_error();
		}

		$plugin_slug = $request->get( 'plugin_name' );
		$network_wide = '1' === $request->get( 'network_wide' );

		$integration = $this->integrations->find_by_slug( $plugin_slug );

		if ( ! $integration ) {
			wp_send_json_error( 'Invalid plugin.' );
		}

		$token = $this->activation_token_factory->create();
		$activation = $token
			? $this->activation_storage->find( $token )
			: null;

		$plugin = new PluginInformation( $integration->get_basename() );

		// Install
		if ( ! $plugin->is_installed() ) {
			if ( ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error( 'User does not have the permission to install plugin.' );
			}

			if ( ! $activation || ! $activation->is_active() ) {
				$message = sprintf(
					'%s %s',
					__( 'License is not active.', 'codepress-admin-columns' ),
					sprintf(
						__( 'Enter your license key on <a href="%s">the settings page</a>.', 'codepress-admin-columns' ),
						esc_url( $this->get_license_page_url()->get_url() )
					)
				);

				wp_send_json_error( $message );
			}

			$response = $this->api->dispatch( new API\Request\DownloadInformation( $plugin_slug, $token, $this->site_url ) );

			if ( $response->has_error() ) {
				wp_send_json_error( $response->get_error()->get_error_message() );
			}

			$result = $this->install_plugin( $response->get( 'download_link' ) );

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( $result->get_error_message() );
			}

			if ( ! $result ) {
				wp_send_json_error( __( 'Install failed.', 'codepress-admin-columns' ) );
			}
		}

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( 'User does not have permission to activate plugin.' );
		}

		// Activate
		$is_active = null === activate_plugin( $integration->get_basename(), '', $network_wide );

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
	 * @return Url
	 */
	private function get_license_page_url() {
		return $this->is_network_active
			? new Url\EditorNetwork( 'license' )
			: new Url\Editor( 'license' );
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