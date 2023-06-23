<?php
/**
 * Redsys Push Notifications Menu
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2023 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Redsys Push Notifications Menu
 */
class Redsys_Push_Notifications_Menu {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_settings_tab_redsys_push', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_settings_tab_redsys_push', __CLASS__ . '::update_settings' );
	}


	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['settings_tab_redsys_push'] = __( 'Redsys Push Notifications', 'woocommerce-redsys' );
		return $settings_tabs;
	}


	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function settings_tab() {
		WCRed()->return_help_notice(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<p><strong>' . esc_html__( 'Check ', 'woocommerce-redsys' ) . '<a href="https://redsys.joseconti.com/guias/configurar-push-notifications/" target="new">' . esc_html__( ' The Guide', 'woocommerce-redsys' ) . '</a>' . esc_html__( 'for configuring Push Notifications. ', 'woocommerce-redsys' ) . '</strong><p>';
		woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function get_settings() {

		$readonly = array(
			'checked'  => 'checked',
			'disabled' => 'disabled',
		);
		$settings = array(
			'title'               => array(
				'name' => esc_html__( 'Redsys Push Notifications (by José Conti)', 'woocommerce-redsys' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'wc_settings_tab_redsys_sort_push_title',
			),
			'push_is_active'      => array(
				'title'   => esc_html__( 'Enable Push Notifications', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Enable Push Notifications.', 'woocommerce-redsys' ),
				'default' => 'no',
				'desc'    => sprintf( esc_html__( 'Enable Push Notifications', 'woocommerce-redsys' ) ),
				'id'      => 'wc_settings_tab_redsys_sort_push_is_active',
			),
			'access_token'        => array(
				'name' => esc_html__( 'Access Token', 'woocommerce-redsys' ),
				'type' => 'text',
				'desc' => esc_html__( 'Access Token', 'woocommerce-redsys' ),
				'id'   => 'wc_settings_tab_redsys_sort_push_access_token',
			),
			'mobile_app_id'       => array(
				'name' => esc_html__( 'Mobile App ID', 'woocommerce-redsys' ),
				'type' => 'text',
				'desc' => esc_html__( 'The Mobile App ID', 'woocommerce-redsys' ),
				'id'   => 'wc_settings_tab_redsys_sort_push_mobile_app_id',
			),
			'identifier'          => array(
				'name' => esc_html__( 'Identifier', 'woocommerce-redsys' ),
				'type' => 'text',
				'desc' => esc_html__( 'Your mobile with country code, Ex, 34666666666', 'woocommerce-redsys' ),
				'id'   => 'wc_settings_tab_redsys_sort_push_identifier',
			),
			'redsys_section_end'  => array(
				'type' => 'sectionend',
				'id'   => 'wc_settings_tab_redsys_sort_push_section_end',
			),
			'notification_types'  => array(
				'name' => esc_html__( 'Notification Types', 'woocommerce-redsys' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'wc_settings_tab_redsys_sort_push_notifications_title',
			),
			'notify_errors'       => array(
				'title'             => esc_html__( 'Errors', 'woocommerce-redsys' ),
				'type'              => 'checkbox',
				'label'             => esc_html__( 'Enable Notify Errors.', 'woocommerce-redsys' ),
				'default'           => 'yes',
				'custom_attributes' => $readonly,
				'desc'              => sprintf( esc_html__( 'Notify Errors', 'woocommerce-redsys' ) ),
				'id'                => 'wc_settings_tab_redsys_sort_push_notify_errors',
			),
			'redsys_section_end2' => array(
				'type' => 'sectionend',
				'id'   => 'wc_settings_tab_redsys_sort_push_notifications_section_end',
			),
		);
		return apply_filters( 'wc_settings_tab_redsys_sort_push_settings', $settings );
	}
}
Redsys_Push_Notifications_Menu::init();
