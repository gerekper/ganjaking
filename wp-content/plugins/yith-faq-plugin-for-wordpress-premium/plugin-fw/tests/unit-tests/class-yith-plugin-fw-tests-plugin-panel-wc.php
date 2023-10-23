<?php
/**
 * Test class for Plugin Panel WooCommerce
 *
 * @package YITH Plugin Framework
 */

/**
 * Class YITH_Plugin_FW_Tests_Plugin_Panel_WC
 */
class YITH_Plugin_FW_Tests_Plugin_Panel_WC extends WP_UnitTestCase {

	/**
	 * The panel.
	 *
	 * @var YIT_Plugin_Panel_WooCommerce
	 */
	protected $panel;

	/**
	 * Set Up
	 *
	 * @return void
	 */
	public function setUp() {
		$this->panel = YITH_Plugin_FW_Panels_Helper::create_wc_panel();

		// Include admin functions to use woocommerce_update_options().
		include_once WC_ABSPATH . '/includes/admin/wc-admin-functions.php';
	}

	/**
	 * Test simple tab with options.
	 */
	public function test_simple_tab() {
		$options = YITH_Plugin_FW_Panels_Helper::init_vars_wc_panel_options_for_saving( $this->panel, 'wc-panel' );

		$this->panel->woocommerce_update_options();

		foreach ( $options as $key => $option ) {
			$value    = get_option( $key );
			$expected = $option['php_unit_expected'];
			$message  = sprintf( 'Test for %s [type: %s]', $key, $option['type'] );

			$this->assertEquals( $expected, $value, $message );
		}

	}
}
