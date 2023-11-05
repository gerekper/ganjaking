<?php

namespace Smush\Core;

class Plugin_Settings_Watcher extends Controller {
	/**
	 * @var Settings
	 */
	private $settings;
	/**
	 * @var Array_Utils
	 */
	private $array_utils;

	public function __construct() {
		$this->settings    = Settings::get_instance();
		$this->array_utils = new Array_Utils();

		$this->hook_settings_update_interceptor( array( $this, 'trigger_updated_action' ) );
		$this->hook_settings_delete_interceptor( array( $this, 'trigger_deleted_action' ) );

		$this->hook_settings_update_interceptor( array(
			$this,
			'trigger_resize_sizes_updated_action',
		), 'wp-smush-resize_sizes' );

		$this->hook_settings_update_interceptor( array(
			$this,
			'trigger_membership_status_change_action',
		), 'wp_smush_api_auth' );
	}

	private function hook_settings_update_interceptor( $callback, $option_id = 'wp-smush-settings' ) {
		if ( $this->settings->is_network_enabled() ) {
			$this->register_action(
				"update_site_option_$option_id",
				function ( $option, $settings, $old_settings ) use ( $callback ) {
					call_user_func_array( $callback, array( $old_settings, $settings ) );
				},
				10,
				3
			);
		} else {
			$this->register_action( "update_option_$option_id", $callback, 10, 2 );
		}
	}

	private function hook_settings_delete_interceptor( $callback ) {
		if ( $this->settings->is_network_enabled() ) {
			$this->register_action( "delete_site_option_wp-smush-settings", $callback );
		} else {
			$this->register_action( "delete_option_wp-smush-settings", $callback );
		}
	}

	public function trigger_updated_action( $old_settings, $settings ) {
		do_action( 'wp_smush_settings_updated', $old_settings, $settings );
	}

	public function trigger_deleted_action() {
		do_action( 'wp_smush_settings_deleted' );
	}

	public function trigger_resize_sizes_updated_action( $old_settings, $settings ) {
		do_action( 'wp_smush_resize_sizes_updated', $old_settings, $settings );
	}

	public function trigger_membership_status_change_action( $old_settings, $settings ) {
		$api_key      = Helper::get_wpmudev_apikey();
		$old_validity = isset( $old_settings[ $api_key ]['validity'] ) ? $old_settings[ $api_key ]['validity'] : false;
		$new_validity = isset( $settings[ $api_key ]['validity'] ) ? $settings[ $api_key ]['validity'] : false;

		if ( $old_validity !== $new_validity ) {
			do_action( 'wp_smush_membership_status_changed' );
		}
	}
}