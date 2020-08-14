<?php
/**
 * Store settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

$is_store_connected = YITH_WCMC_Store()->is_store_connected();
$is_store_syncing = YITH_WCMC_Store()->is_store_syncing();
$is_store_synced = YITH_WCMC_Store()->is_store_synced();
$is_sync_paused = YITH_WCMC_Store()->is_sync_paused();
$status_message = YITH_WCMC_Store()->get_sync_status_message();

return apply_filters( 'yith_wcmc_store_options', array(
	'store' => array_merge(
		array(
			'store-integration-options' => array(
				'title' => __( 'Store integrations', 'yith-woocommerce-mailchimp' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'yith_wcmc_store_integration_options'
			)
		),

		$is_store_connected ? array(
			'store-integration-status' => array(
				'title' => __( 'Integration status', 'yith-woocommerce-mailchimp' ),
				'type' => 'yith_wcmc_store_integration_status',
				'id' => 'yith_wcmc_store_integration_status'
			)
		) :
		array(
			'store-integration-list' => array(
				'title' => __( 'Store list', 'yith-woocommerce-mailchimp' ),
				'type' => 'select',
				'id' => 'yith_wcmc_store_integration_list',
				'desc' => ! $is_store_connected ? __( 'Select the list that you want to use for your store integration', 'yith-woocommerce-mailchimp' ) : __( 'Select the list that you want to use for your store integration; you cannot change this option, unless you delete the previously created store', 'yith-woocommerce-mailchimp' ),
				'options' => $list_options,
				'custom_attributes' => ( empty( $list_options ) || $is_store_connected ) ? array(
					'disabled' => 'disabled'
				) : array(),
				'css' => 'min-width:300px;',
				'class' => 'list-select'
			),

			'store-integration-name' => array(
				'title' => __( 'Store name', 'yith-woocommerce-mailchimp' ),
				'type' => 'text',
				'id' => 'yith_wcmc_store_integration_name',
				'desc' => __( 'Select the name that you want to assign to your store on Mailchimp dashboard', 'yith-woocommerce-mailchimp' ),
				'custom_attributes' => $is_store_connected ? array(
					'disabled' => 'disabled'
				) : array(),
				'default' => get_bloginfo( 'name' )
			),

			'store-integration-address-line-1' => array(
				'title' => __( 'Store address line 1', 'yith-woocommerce-mailchimp' ),
				'type' => 'text',
				'id' => 'yith_wcmc_store_address_line_1',
				'desc' => __( 'Enter address of your store (leave empty to use global store settings)', 'yith-woocommerce-mailchimp' ),
				'custom_attributes' => $is_store_connected ? array(
					'disabled' => 'disabled'
				) : array(),
			),

			'store-integration-address-line-2' => array(
				'title' => __( 'Store address line 2', 'yith-woocommerce-mailchimp' ),
				'type' => 'text',
				'id' => 'yith_wcmc_store_address_line_2',
				'desc' => __( 'Enter address of your store (leave empty to use global store settings)', 'yith-woocommerce-mailchimp' ),
				'custom_attributes' => $is_store_connected ? array(
					'disabled' => 'disabled'
				) : array(),
			),

			'store-integration-city' => array(
				'title' => __( 'Store city', 'yith-woocommerce-mailchimp' ),
				'type' => 'text',
				'id' => 'yith_wcmc_store_city',
				'desc' => __( 'Enter city of your store (leave empty to use global store settings)', 'yith-woocommerce-mailchimp' ),
				'custom_attributes' => $is_store_connected ? array(
					'disabled' => 'disabled'
				) : array(),
			),

			'store-integration-postcode' => array(
				'title' => __( 'Store postcode', 'yith-woocommerce-mailchimp' ),
				'type' => 'text',
				'id' => 'yith_wcmc_store_postcode',
				'desc' => __( 'Enter postcode of your store (leave empty to use global store settings)', 'yith-woocommerce-mailchimp' ),
				'custom_attributes' => $is_store_connected ? array(
					'disabled' => 'disabled'
				) : array(),
			),

			'store-integration-state' => array(
				'title' => __( 'Store state', 'yith-woocommerce-mailchimp' ),
				'type' => 'text',
				'id' => 'yith_wcmc_store_state',
				'desc' => __( 'Enter state of your store (leave empty to use global store settings)', 'yith-woocommerce-mailchimp' ),
				'custom_attributes' => $is_store_connected ? array(
					'disabled' => 'disabled'
				) : array(),
			),

			'store-integration-country' => array(
				'title' => __( 'Store country', 'yith-woocommerce-mailchimp' ),
				'type' => 'select',
				'id' => 'yith_wcmc_store_country',
				'desc' => __( 'Enter state of your store (leave empty to use global store settings)', 'yith-woocommerce-mailchimp' ),
				'custom_attributes' => $is_store_connected ? array(
					'disabled' => 'disabled'
				) : array(),
				'options' => WC()->countries->get_countries()
			),
		),

		array(
			'store-integration-options-end' => array(
				'type'  => 'sectionend',
				'id'    => 'yith_wcmc_store_integration_options'
			)
		),

		$is_store_connected ? array(
			'store-integration-sync' => array(
				'title' => __( 'Store Synchronization', 'yith-woocommerce-mailchimp' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'yith_wcmc_store_integration_sync'
			)
		) : array(),

		( $is_store_connected && ! $is_store_syncing && ! $is_store_synced && ! $is_sync_paused ) ? array(
			'store-integration-start-sync' => array(
				'title' => __( 'Start sync', 'yith-woocommerce-mailchimp' ),
				'type' => 'yith-field',
				'yith-type' => 'buttons',
				'buttons' => array(
					array(
						'name' => __( 'Start Sync', 'yith-woocommerce-mailchimp' ),
						'class' => 'btn send-request',
						'data' => array(
							'url' => add_query_arg( 'action', 'yith_wcmc_sync_start', admin_url( 'admin.php' ) )
						)
					)
				),
				'id' => 'yith_wcmc_store_integration_start_sync',
				'desc' => __( 'Start store synchronization <b>Note that this may take a while, and will be processed in the background</b>', 'yith-woocommerce-mailchimp' ) . '<br/>' . $status_message,
			)
		) : array(),

		( $is_store_connected && ! $is_store_syncing && $is_sync_paused ) ? array(
			'store-integration-restart-sync' => array(
				'title' => __( 'Resume sync', 'yith-woocommerce-mailchimp' ),
				'type' => 'yith-field',
				'yith-type' => 'buttons',
				'buttons' => array(
					array(
						'name' => __( 'Resume Sync', 'yith-woocommerce-mailchimp' ),
						'class' => 'btn send-request',
						'data' => array(
							'url' => add_query_arg( 'action', 'yith_wcmc_sync_resume', admin_url( 'admin.php' ) )
						)
					)
				),
				'id' => 'yith_wcmc_store_integration_restart_sync',
				'desc' => __( 'Start store syncronization <b>Note that this may take a while, and will be processed in the background</b>', 'yith-woocommerce-mailchimp' ) . '<br/>' . $status_message,
			)
		) : array(),

		( $is_store_connected && ! $is_store_syncing && $is_store_synced ) ? array(
			'store-integration-sync-again' => array(
				'title' => __( 'Sync again', 'yith-woocommerce-mailchimp' ),
				'type' => 'yith-field',
				'yith-type' => 'buttons',
				'buttons' => array(
					array(
						'name' => __( 'Sync Again', 'yith-woocommerce-mailchimp' ),
						'class' => 'btn send-request',
						'data' => array(
							'url' => add_query_arg( 'action', 'yith_wcmc_sync_restart', admin_url( 'admin.php' ) )
						)
					)
				),
				'id' => 'yith_wcmc_store_integration_sync_again',
				'desc' => __( 'Rerun store synchronization <b>Note that this may take a while, and will be processed in the background</b>', 'yith-woocommerce-mailchimp' ) . '<br/>',
			)
		) : array(),

		( $is_store_connected && $is_store_syncing ) ? array(
			'store-integration-stop-sync' => array(
				'title' => __( 'Stop sync', 'yith-woocommerce-mailchimp' ),
				'type' => 'yith-field',
				'yith-type' => 'buttons',
				'buttons' => array(
					array(
						'name' => __( 'Stop Sync', 'yith-woocommerce-mailchimp' ),
						'class' => 'btn send-request',
						'data' => array(
							'url' => add_query_arg( 'action', 'yith_wcmc_sync_stop', admin_url( 'admin.php' ) )
						)
					)
				),
				'id' => 'yith_wcmc_store_integration_stop_sync',
				'desc' => __( 'Stop store synchronization', 'yith-woocommerce-mailchimp' ) . '<br/>' . $status_message,
			)
		) : array(),

		$is_store_connected ? array(
			'store-integration-sync-end' => array(
				'type'  => 'sectionend',
				'id'    => 'yith_wcmc_store_integration_sync'
			)
		) : array()
	)
) );