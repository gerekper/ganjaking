<?php

namespace ACP\Settings\Column\NetworkSite;

use AC\Settings;
use AC\View;
use WP_Theme;

class Theme extends Settings\Column
	implements Settings\FormatValue {

	private $theme_status;

	protected function define_options() {
		return [ 'theme_status' => 'active' ];
	}

	public function create_view() {
		$select = $this
			->create_element( 'select' )
			->set_attribute( 'data-label', 'update' )
			->set_options( $this->get_display_options() );

		$view = new View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	private function get_display_options() {
		$options = [
			'active'    => __( 'Active Theme', 'codepress-admin-columns' ),
			'allowed'   => __( 'Allowed Themes', 'codepress-admin-columns' ),
			'available' => __( 'Available Themes', 'codepress-admin-columns' ),
		];

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_theme_status() {
		return $this->theme_status;
	}

	/**
	 * @param string $theme_status
	 *
	 * @return bool
	 */
	public function set_theme_status( $theme_status ) {
		$this->theme_status = $theme_status;

		return true;
	}

	public function format( $value, $original_value ) {
		$blog_id = $original_value;
		$active_theme = ac_helper()->network->get_active_theme( $blog_id );

		switch ( $this->get_theme_status() ) {
			case 'active' :
				$themes = [ $active_theme ];

				break;
			case 'allowed' :
				$themes = wp_get_themes( [ 'blog_id' => $blog_id, 'allowed' => 'site' ] );

				break;
			case 'available' :
				$themes = wp_get_themes( [ 'blog_id' => $blog_id, 'allowed' => true ] );

				break;
			default:
				$themes = [];
		}

		// Add Tooltip
		foreach ( $themes as $k => $theme ) {
			$tooltip = [];

			/* @var WP_Theme $theme */
			if ( $theme->get_stylesheet() === $active_theme->get_stylesheet() ) {
				$tooltip[] = __( 'Active', 'codepress-admin-columns' );
			}

			if ( $theme->is_allowed( 'network', $blog_id ) ) {
				$tooltip[] = __( 'Network Enabled', 'codepress-admin-columns' );
			} else if ( $theme->is_allowed( 'site', $blog_id ) ) {
				$tooltip[] = __( 'Site Enabled', 'codepress-admin-columns' );
			}

			unset( $themes[ $k ] );

			$themes[ $theme->get_stylesheet() ] = ac_helper()->html->tooltip( $theme->get( 'Name' ), implode( ' | ', $tooltip ) );
		}

		natcasesort( $themes );

		$active_stylesheet = $active_theme->get_stylesheet();

		if ( isset( $themes[ $active_stylesheet ] ) && count( $themes ) > 1 ) {
			// Active first
			$theme = [ $active_stylesheet => $themes[ $active_stylesheet ] ];
			unset( $themes[ $active_stylesheet ] );
			$themes = $theme + $themes;

			// Suffix with active
			$themes[ $active_stylesheet ] = '<strong>' . $themes[ $active_stylesheet ] . '</strong>';
		}

		return implode( "<br>", $themes );
	}

}