<?php

namespace Essential_Addons_Elementor\Pro\Classes\License\Contracts;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use Exception;
use Essential_Addons_Elementor\Pro\Classes\License\LicenseManager;

#[\AllowDynamicProperties]
abstract class ApiAdapter {
	protected $config = null;

	/**
	 * @var LicenseManager
	 */
	protected $license_manager;

	public function __construct( $license_manager ) {
		$this->license_manager     = $license_manager;
		$this->config              = $this->license_manager->get_args( $this->license_manager->api );
		$this->config['handle']    = $this->license_manager->get_args( 'scripts_handle' );
		$this->config['screen_id'] = $this->license_manager->get_args( 'screen_id' );
		$this->config['item_id']   = $this->license_manager->get_args( 'item_id' );

		$this->register();

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ], 11 );
	}

	public function enqueue( $hook ) {
		if ( is_array( $this->screen_id ) && ! in_array( $hook, $this->screen_id ) ) {
			return;
		}

		if ( ! is_array( $this->screen_id ) && $this->screen_id !== $hook ) {
			return;
		}

		wp_localize_script( $this->handle, 'wpdeveloperLicenseManagerConfig', $this->get_api_config() );
	}

	public function get_api_config() {
		return [
			'textdomain' => $this->license_manager->textdomain,
			'apiType'    => $this->license_manager->api,
			'nonce'      => wp_create_nonce( "wpdeveloper_sl_{$this->item_id}_nonce" )
		];
	}

	/**
	 * @throws Exception
	 */
	public function __get( $name ) {
		if ( isset( $this->config[ $name ] ) ) {
			return $this->config[ $name ];
		} elseif ( isset( $this->license_manager->{$name} ) ) {
			return $this->license_manager->get_args( $name );
		} else {
			throw new Exception( "Please provide $name for api configuration." );
		}
	}

	public function __isset( $name ) {
		return isset( $this->config[ $name ] );
	}

	protected function verify_nonce( $nonce ) {
		return wp_verify_nonce( $nonce, "wpdeveloper_sl_{$this->item_id}_nonce" );
	}

	/**
	 * This method is responsible for checking permissions.
	 * @return bool
	 */
	public function permission_check() {
		return current_user_can( isset( $this->permission ) ? $this->permission : 'delete_users' );
	}

	abstract public function register();

	abstract public function activate( $request );

	abstract public function deactivate( $request );
}
