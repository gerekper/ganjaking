<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update;
use AC\Plugin\Version;

class V5700 extends Update {

	public function __construct() {
		parent::__construct( new Version( '5.7' ) );
	}

	public function apply_update() {
		$this->update_subscription_details();
		$this->update_permissions();
		$this->clear_cache_api();
	}

	protected function clear_cache_api() {
		global $wpdb;

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'ac_api_request_%'" );
	}

	private function update_permissions() {
		$details = $this->get_option( 'acp_subscription_details' );

		if ( ! $details ) {
			return;
		}

		$permissions = [ 'usage' ];

		$status = isset( $details['status'] )
			? $details['status']
			: null;

		$subscription_key = defined( 'ACP_LICENCE' ) && ACP_LICENCE
			? ACP_LICENCE
			: $this->get_option( 'acp_subscription_key' );

		if ( 'active' === $status && $this->get_option( 'acp_subscription_details_key' ) === $subscription_key ) {
			$permissions[] = 'update';
		}

		$this->update_option( '_acp_access_permissions', $permissions );
	}

	private function update_subscription_details() {
		$details = $this->get_option( 'acp_subscription_details' );

		if ( ! $details || ! is_array( $details ) ) {
			return;
		}

		$details['products'] = [];

		$this->update_option( 'acp_subscription_details', $details );
	}

	protected function update_option( $name, $value ) {
		update_option( $name, $value );
	}

	protected function get_option( $name ) {
		return get_option( $name );
	}

}