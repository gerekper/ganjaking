<?php

/**
 * Class CT_Ultimate_GDPR_Controller_Plugins
 */
class CT_Ultimate_GDPR_Controller_Plugins extends CT_Ultimate_GDPR_Controller_Abstract {

	/**
	 *
	 */
	const ID = 'ct-ultimate-gdpr-plugins';

	/**
	 *
	 */
	const PLUGIN_COMPATIBLE_NO = false;
	/**
	 *
	 */
	const PLUGIN_COMPATIBLE_PARTLY = 'partly';
	/**
	 *
	 */
	const PLUGIN_COMPATIBLE_YES = true;

	/**
	 *
	 */
	const PLUGIN_COLLECTS_DATA_YES = true;
	/**
	 *
	 */
	const PLUGIN_COLLECTS_DATA_PROBABLY = 'probably';
	/**
	 *
	 */
	const PLUGIN_COLLECTS_DATA_UNKNOWN = 'unknown';
	/**
	 *
	 */
	const PLUGIN_COLLECTS_DATA_NO = false;

	/**
	 * Get unique controller id (page name, option id)
	 */
	public function get_id() {
		return self::ID;
	}

	/**
	 * Init after construct
	 */
	public function init() {

	}

	/**
	 * Do actions on frontend
	 */
	public function front_action() {
	}

	/**
	 * Do actions in admin (general)
	 */
	public function admin_action() {
	}

	/**
	 * Do actions on current admin page
	 */
	protected function admin_page_action() {

		$plugins        = array();
		$active_plugins = array_merge( get_option( 'active_plugins', array() ), array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
		$all_plugins    = get_plugins();

		foreach ( $active_plugins as $active_plugin ) {

			$plugin_data = ct_ultimate_gdpr_get_value( $active_plugin, $all_plugins, array() );

			if ( ! $plugin_data ) {
				continue;
			}

			$plugin                  = array();
			$plugin['name']          = ct_ultimate_gdpr_get_value( 'Name', $plugin_data, '' );
			$plugin['collects_data'] = $this->check_plugin_collects_data( $active_plugin );
			$plugin['compatible']    = $this->check_plugin_compatible( $active_plugin );
			$plugins[]               = $plugin;

		}

		$plugins_collect_yes      = array();
		$plugins_collect_probably = array();
		$plugins_collect_unknown  = array();
		$plugins_collect_no       = array();

		foreach ( $plugins as $plugin ) {
			if ( self::PLUGIN_COLLECTS_DATA_YES === $plugin['collects_data'] ) {
				$plugins_collect_yes[] = $plugin;
			} elseif ( self::PLUGIN_COLLECTS_DATA_PROBABLY === $plugin['collects_data'] ) {
				$plugins_collect_probably[] = $plugin;
			} elseif ( self::PLUGIN_COLLECTS_DATA_UNKNOWN === $plugin['collects_data'] ) {
				$plugins_collect_unknown[] = $plugin;
			} else {
				$plugins_collect_no[] = $plugin;
			}
		}

		$plugins = array_merge(
			$plugins_collect_yes,
			$plugins_collect_probably,
			$plugins_collect_unknown,
			$plugins_collect_no
		);

		$this->add_view_option( 'plugins', $plugins );
	}

	/**
	 * Get view template string
	 * @return string
	 */
	public function get_view_template() {
		return 'admin/admin-plugins';
	}

	/**
	 * Add menu page (if not added in admin controller)
	 */
	public function add_menu_page() {
		add_submenu_page(
			'ct-ultimate-gdpr',
			esc_html__( 'Plugins', 'ct-ultimate-gdpr' ),
			esc_html__( 'Plugins', 'ct-ultimate-gdpr' ),
			'manage_options',
			'ct-ultimate-gdpr-plugins',
			array( $this, 'render_menu_page' )
		);
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {
	}

	/**
	 * @param $active_plugin
	 *
	 * @return mixed|
	 */
	private function check_plugin_collects_data( $active_plugin ) {

		$collects = self::PLUGIN_COLLECTS_DATA_UNKNOWN;
		$searches = array(
			'user_email',
			'add_user_meta',
			'update_user_meta',
		);
		$pattern  = WP_PLUGIN_DIR . "/" . basename( dirname( $active_plugin ) ) . "/*/*.php";

		foreach ( glob( $pattern ) as $filename ) {

			$contents = file_get_contents( $filename );

			foreach ( $searches as $search ) {

				if ( false !== strpos( $contents, $search ) ) {
					$collects = self::PLUGIN_COLLECTS_DATA_PROBABLY;
				}

			}

		}

		return apply_filters( "ct_ultimate_gdpr_controller_plugins_collects_data_$active_plugin", $collects );
	}

	/**
	 * @param $active_plugin
	 *
	 * @return mixed|
	 */
	private function check_plugin_compatible( $active_plugin ) {
		return apply_filters( "ct_ultimate_gdpr_controller_plugins_compatible_$active_plugin", self::PLUGIN_COMPATIBLE_NO );
	}
}