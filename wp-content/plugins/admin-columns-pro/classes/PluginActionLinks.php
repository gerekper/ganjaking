<?php

namespace ACP;

use AC\Registrable;

class PluginActionLinks implements Registrable {

	/**
	 * @var string
	 */
	private $basename;

	public function __construct( $basename ) {
		$this->basename = $basename;
	}

	public function register() {
		add_filter( 'plugin_action_links', [ $this, 'add_settings_link' ], 1, 2 );
		add_filter( 'network_admin_plugin_action_links', [ $this, 'add_network_settings_link' ], 1, 2 );
	}

	public function add_settings_link( $links, $file ) {
		if ( $file === $this->basename ) {
			array_unshift( $links, $this->settings_link( ac_get_admin_url( 'settings' ) ) );
		}

		return $links;
	}

	public function add_network_settings_link( $links, $file ) {
		if ( $file === $this->basename ) {
			array_unshift( $links, $this->settings_link( ac_get_admin_network_url( 'settings' ) ) );
		}

		return $links;
	}

	/**
	 * @param string $url
	 *
	 * @return string
	 */
	private function settings_link( $url ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( $url ), __( 'Settings', 'codepress-admin-columns' ) );
	}

}