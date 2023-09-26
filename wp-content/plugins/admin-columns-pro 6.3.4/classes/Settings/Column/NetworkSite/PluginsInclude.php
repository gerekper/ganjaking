<?php

namespace ACP\Settings\Column\NetworkSite;

use AC\Settings;
use AC\View;

class PluginsInclude extends Settings\Column
	implements Settings\FormatValue {

	/**
	 * @var bool
	 */
	private $include_network;

	protected function define_options() {
		return [ 'include_network' ];
	}

	public function create_view() {

		$options = [
			'include_network' => __( 'Include network plugins', 'codepress-admin-columns' ),
		];

		$view = new View( [
			'label'   => __( 'Plugins' ),
			'setting' => $this->create_element( 'checkbox' )->set_options( $options ),
		] );

		return $view;
	}

	public function get_include_network() {
		return $this->include_network;
	}

	/**
	 * @param string $include_network
	 *
	 * @return bool
	 */
	public function set_include_network( $include_network ) {
		$this->include_network = $include_network;

		return true;
	}

	public function format( $value, $original_value ) {
		if ( $this->get_include_network() ) {
			foreach ( get_plugins() as $basename => $plugin ) {
				if ( is_plugin_active_for_network( $basename ) ) {
					$value[ $basename ] = $plugin['Name'];
				}
			}
		}

		return $value;
	}

}