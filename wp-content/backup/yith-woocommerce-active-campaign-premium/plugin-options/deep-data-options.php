<?php
/**
 * Store settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

$is_store_connected = YITH_WCAC_Deep_Data()->is_store_connected();
$is_store_syncing = YITH_WCAC_Deep_Data()->is_store_syncing();
$is_store_synced = YITH_WCAC_Deep_Data()->is_store_synced();
$is_sync_paused = YITH_WCAC_Deep_Data()->is_sync_paused();
$status_message = YITH_WCAC_Deep_Data()->get_sync_status_message();

return apply_filters( 'yith_wcac_store_options', array(
	'deep-data' => array_merge(
		array(
			'deep-data-options' => array(
				'title' => __( 'Deep Data integration', 'yith-woocommerce-active-campaign' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'yith_wcac_deep_data_integration_options',
			),
		),

		$is_store_connected ? array(
			'store-connection-status' => array(
				'title' => __( 'Connection status', 'yith-woocommerce-active-campaign' ),
				'type' => 'yith_wcac_deep_data_connection_status',
				'id' => 'yith_wcac_deep_data_connection_status',
			),
		) :
		array(

			'store-connection-name' => array(
				'title' => __( 'Store name', 'yith-woocommerce-active-campaign' ),
				'type' => 'text',
				'id' => 'yith_wcac_store_connection_name',
				'desc' => __( 'Select the name that you want to assign to your store on Active Campaign dashboard', 'yith-woocommerce-active-campaign' ),
				'custom_attributes' => $is_store_connected ? array(
					'disabled' => 'disabled',
				) : array(),
				'default' => get_bloginfo( 'name' ),
			),

			'store-connection-logo' => array(
				'title' => __( 'Store logo', 'yith-woocommerce-active-campaign' ),
				'type' => 'yith-field',
				'yith-type' => 'upload',
				'id' => 'yith_wcac_store_connection_logo',
				'desc' => __( 'Select the logo that you want to assign to your store on Active Campaign dashboard', 'yith-woocommerce-active-campaign' ),
				'custom_attributes' => $is_store_connected ? array(
					'disabled' => 'disabled',
				) : array(),
				'default' => function_exists( 'yith_plugin_fw_get_default_logo' ) ? yith_plugin_fw_get_default_logo() : '',
			),


		),

		array(
			'store-integration-options-end' => array(
				'type'  => 'sectionend',
				'id'    => 'yith_wcac_deep_data_integration_options',
			),
		),

		$is_store_connected ? array(
			'store-integration-abandoned-cart' => array(
				'title' => __( 'Abandoned cart', 'yith-woocommerce-active-campaign' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'yith_wcac_store_integration_abandoned_cart',
			),
			'store-integration-abandoned-cart-enable' => array(
				'title' => __( 'Enable', 'yith-woocommerce-active-campaign' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable abandoned cart registration', 'yith-woocommerce-active-campaign' ),
				'id' => 'yith_wcac_store_integration_abandoned_cart_enable',
				'default' => 'yes',
			),
			'store-integration-abandoned-cart-enable-guest' => array(
				'title' => __( 'Enable guest', 'yith-woocommerce-active-campaign' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable abandoned cart registration, for guest users too', 'yith-woocommerce-active-campaign' ),
				'id' => 'yith_wcac_store_integration_abandoned_cart_enable_guest',
				'default' => 'yes',
			),
			'store-integration-abandoned-cart-enable-guest-after-tc' => array(
				'title' => __( 'Wait for T&C agreement', 'yith-woocommerce-active-campaign' ),
				'type' => 'checkbox',
				'desc' => __( 'Register guest billing email only after Terms & Conditions agreement', 'yith-woocommerce-active-campaign' ),
				'id' => 'yith_wcac_store_integration_abandoned_cart_enable_guest_after_tc',
				'default' => 'yes',
			),
			'store-integration-abandoned-cart-dalay' => array(
				'title' => __( 'Delay', 'yith-woocommerce-active-campaign' ),
				'type' => 'select',
				'options' => array(
					'1' => __( '1 Hour', 'yith-woocommerce-active-campaign' ),
					'2' => __( '2 Hours', 'yith-woocommerce-active-campaign' ),
					'6' => __( '6 Hours', 'yith-woocommerce-active-campaign' ),
					'10' => __( '10 Hours', 'yith-woocommrce-active-campaign' ),
					'24' => __( '24 Hours', 'yith-woocommerce-active-campaign' ),
				),
				'desc' => __( 'Select the time system should wait before registering a cart as abandoned', 'yith-woocommerce-active-campaign' ),
				'id' => 'yith_wcac_store_integration_abandoned_cart_delay',
			),
			'store-integration-abandoned-cart-end' => array(
				'type' => 'sectionend',
				'id' => 'yith_wcac_store_integration_abandoned_cart',
			),
		) : array(),

		$is_store_connected ? array(
			'store-integration-sync' => array(
				'title' => __( 'Store Synchronization', 'yith-woocommerce-active-campaign' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'yith_wcac_store_integration_sync'
			)
		) : array(),

		( $is_store_connected && ! $is_store_syncing && ! $is_store_synced && ! $is_sync_paused ) ? array(
			'store-integration-start-sync' => array(
				'title' => __( 'Start sync', 'yith-woocommerce-active-campaign' ),
				'type' => 'yith-field',
				'yith-type' => 'buttons',
				'buttons' => array(
					array(
						'name' => __( 'Start Sync', 'yith-woocommerce-active-campaign' ),
						'class' => 'btn send-request',
						'data' => array(
							'url' => add_query_arg( 'action', 'yith_wcac_sync_start', admin_url( 'admin.php' ) ),
						),
					),
				),
				'id' => 'yith_wcac_store_integration_start_sync',
				'desc' => __( 'Start store synchronization <b>Note that this may take a while, and will be processed in the background</b>', 'yith-woocommerce-active-campaign' ) . '<br/>' . $status_message,
			),
		) : array(),

		( $is_store_connected && ! $is_store_syncing && $is_sync_paused ) ? array(
			'store-integration-restart-sync' => array(
				'title' => __( 'Resume sync', 'yith-woocommerce-active-campaign' ),
				'type' => 'yith-field',
				'yith-type' => 'buttons',
				'buttons' => array(
					array(
						'name' => __( 'Resume Sync', 'yith-woocommerce-active-campaign' ),
						'class' => 'btn send-request',
						'data' => array(
							'url' => add_query_arg( 'action', 'yith_wcac_sync_resume', admin_url( 'admin.php' ) )
						)
					)
				),
				'id' => 'yith_wcac_store_integration_restart_sync',
				'desc' => __( 'Start store syncronization <b>Note that this may take a while, and will be processed in the background</b>', 'yith-woocommerce-active-campaign' ) . '<br/>' . $status_message,
			)
		) : array(),

		( $is_store_connected && ! $is_store_syncing && $is_store_synced ) ? array(
			'store-integration-sync-again' => array(
				'title' => __( 'Sync again', 'yith-woocommerce-active-campaign' ),
				'type' => 'yith-field',
				'yith-type' => 'buttons',
				'buttons' => array(
					array(
						'name' => __( 'Sync Again', 'yith-woocommerce-active-campaign' ),
						'class' => 'btn send-request',
						'data' => array(
							'url' => add_query_arg( 'action', 'yith_wcac_sync_restart', admin_url( 'admin.php' ) )
						)
					)
				),
				'id' => 'yith_wcac_store_integration_sync_again',
				'desc' => __( 'Rerun store synchronization <b>Note that this may take a while, and will be processed in the background</b>', 'yith-woocommerce-active-campaign' ) . '<br/>',
			)
		) : array(),

		( $is_store_connected && $is_store_syncing ) ? array(
			'store-integration-stop-sync' => array(
				'title' => __( 'Stop sync', 'yith-woocommerce-active-campaign' ),
				'type' => 'yith-field',
				'yith-type' => 'buttons',
				'buttons' => array(
					array(
						'name' => __( 'Stop Sync', 'yith-woocommerce-active-campaign' ),
						'class' => 'btn send-request',
						'data' => array(
							'url' => add_query_arg( 'action', 'yith_wcac_sync_stop', admin_url( 'admin.php' ) )
						)
					)
				),
				'id' => 'yith_wcac_store_integration_stop_sync',
				'desc' => __( 'Stop store synchronization', 'yith-woocommerce-active-campaign' ) . '<br/>' . $status_message,
			)
		) : array(),

		$is_store_connected ? array(
			'store-integration-sync-end' => array(
				'type'  => 'sectionend',
				'id'    => 'yith_wcac_store_integration_sync'
			)
		) : array()
	)
) );