<?php

namespace ACP\Settings\Column\NetworkSite;

use AC\Settings;
use AC\View;

class Plugins extends Settings\Column
	implements Settings\FormatValue {

	private $plugin_display;

	protected function define_options() {
		return [ 'plugin_display' ];
	}

	public function create_view() {

		$options = [
			'count' => __( 'Count', 'codepress-admin-columns' ),
			'list'  => __( 'List', 'codepress-admin-columns' ),
		];

		$view = new View( [
			'label'   => __( 'Display Format', 'codepress-admin-columns' ),
			'setting' => $this->create_element( 'select' )->set_options( $options ),
		] );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_plugin_display() {
		return $this->plugin_display;
	}

	/**
	 * @param string $plugin_display
	 *
	 * @return bool
	 */
	public function set_plugin_display( $plugin_display ) {
		$this->plugin_display = $plugin_display;

		return true;
	}

	public function format( $plugins, $blog_id ) {
		if ( empty( $plugins ) ) {
			return false;
		}

		natcasesort( $plugins );

		switch ( $this->get_plugin_display() ) {
			case 'list' :
				// Add link
				if ( current_user_can( 'activate_plugins' ) ) {
					foreach ( $plugins as $k => $plugin ) {
						$plugins[ $k ] = ac_helper()->html->link( get_admin_url( $blog_id, 'plugins.php' ), $plugin );
					}
				}

				$plugins = implode( "<br/>", $plugins );

				break;
			default :
				$plugins = ac_helper()->html->tooltip( count( $plugins ), implode( '<br/>', $plugins ) );
		}

		return $plugins;
	}

}