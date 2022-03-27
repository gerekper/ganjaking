<?php
/**
 * Plugin Panels Helper
 *
 * @package YITH Plugin Framework
 */

 /**
  * Plugin Panels Helper class.
  */
class YITH_Plugin_FW_Panels_Helper {

	/**
	 * The WooCommerce Panel page.
	 *
	 * @var string
	 */
	public static $wc_panel_page = 'yith_plugin_fw_test_wc_panel';

	/**
	 * Create WC Panel
	 *
	 * @return YIT_Plugin_Panel_WooCommerce
	 */
	public static function create_wc_panel() {

		$admin_tabs = array(
			'wc-panel' => 'WooCommerce Panel',
		);

		$args = array(
			'create_menu_page' => true,
			'parent_slug'      => '',
			'page_title'       => 'WooCommerce Panel',
			'menu_title'       => 'WooCommerce Panel',
			'capability'       => 'manage_options',
			'parent'           => '',
			'parent_page'      => 'yit_plugin_panel',
			'page'             => self::$wc_panel_page,
			'admin-tabs'       => $admin_tabs,
			'options-path'     => YITH_PLUGIN_FRAMEWORK_TESTS_DIR . '/framework/plugin-options',
		);

		return new YIT_Plugin_Panel_WooCommerce( $args );
	}

	public static function init_vars_wc_panel_options_for_saving( $panel, $tab, $subtab = '' ) {

		set_current_screen( 'yith-plugins_page_' . $panel->settings['page'] );
		$_POST = array();
		$_GET  = array();

		$_GET['page']                        = $panel->settings['page'];
		$_GET['tab']                         = $tab;
		$_GET['sub_tab']                     = $subtab;
		$_POST['yit_panel_wc_options_nonce'] = wp_create_nonce( 'yit_panel_wc_options_' . $panel->settings['page'] );

		$prefix = $tab . '-';

		$options     = self::get_fixture( 'all-options' );
		$new_options = array();

		foreach ( $options as $key => $option ) {
			$prefixed_key           = $prefix . $key;
			$value                  = $option['value'];
			$_POST[ $prefixed_key ] = $value;

			$new_options[ $prefixed_key ] = $option;
		}

		return $new_options;
	}

	/**
	 * Get a fixture
	 *
	 * @param string $fixture The fixture.
	 * @return array
	 */
	public static function get_fixture( $fixture ) {
		return include YITH_PLUGIN_FRAMEWORK_TESTS_DIR . '/framework/fixtures/panel-' . $fixture . '.php';
	}
}
