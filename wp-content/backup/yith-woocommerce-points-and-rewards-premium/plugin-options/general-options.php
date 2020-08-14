<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$section1 = array(
	'general_title'                       => array(
		'name' => __( 'General Settings', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_general_option',
	),

	'enabled'                             => array(
		'name'      => __( 'Enable Points and Rewards', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable the plugin.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enabled',
	),

	'enabled_shop_manager'                => array(
		'name'      => __( 'Allow Shop Managers to edit customers\' points', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable users with Shop Manager role to update the points of your customers.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_enabled_shop_manager',
	),

	'general_settings_end'                => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_general_option_end',
	),

	'show_options'                        => array(
		'name' => __( 'Show Points', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_show_options',
	),

	'hide_point_system_to_guest'          => array(
		'name'      => __( 'Hide points messages from guests', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'If you enable this option, all messages about points will be hidden from guest users.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_hide_point_system_to_guest',
	),
	'show_point_list_my_account_page'     => array(
		'name'      => __( 'Show points on "My Account" page', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'If checked, the list of points received/used will appear on "My Account" page', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_show_point_list_my_account_page',
	),
	'show_points_worth_money'             => array(
		'name'      => __( 'Show Points Worth', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Show the points value as money on My Points page', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_show_point_worth_my_account',
		'deps'      => array(
			'id'    => 'ywpar_show_point_list_my_account_page',
			'value' => 'yes',
			'type'  => 'hide',
		),

	),

	'my_account_page_label'               => array(
		'name'      => __( 'Label', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Text of Points Tab in My Account. "Show points on My Account page" must be enabled.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'My Points', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_my_account_page_label',
		'deps'      => array(
			'id'    => 'ywpar_show_point_list_my_account_page',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),
	'my_account_page_endpoint'            => array(
		'name'      => __( 'Endpoint', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Endpoint of the "My points" tab in my account. "Show points on My Account page" must be enabled. Endpoints cannot contain any spaces nor uppercase letters.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => 'my-points',
		'id'        => 'ywpar_my_account_page_endpoint',
		'deps'      => array(
			'id'    => 'ywpar_show_point_list_my_account_page',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),
	'show_point_summary_on_order_details' => array(
		'name'      => __( 'Show points earned and spent', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Show points earned and spent in My Account > Order details', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_show_point_summary_on_order_details',
	),
	'show_point_summary_on_email'         => array(
		'name'      => __( '', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Show points earned and spent in the email of Order completed', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_show_point_summary_on_email',
	),
	'show_options_end'                    => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_show_options',
	),
);

return array( 'general' => $section1 );
