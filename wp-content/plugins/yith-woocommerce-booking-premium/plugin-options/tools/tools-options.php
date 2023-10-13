<?php
/**
 * Tools options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit();

$cache_field = yith_plugin_fw_get_field(
	array(
		'type'  => 'onoff',
		'id'    => 'yith-wcbk-cache-enabled',
		'name'  => 'yith-wcbk-cache-enabled',
		'value' => yith_wcbk()->settings->is_cache_enabled() ? 'yes' : 'no',
	)
);

$cache_field .= "<input type='hidden' name='yith-wcbk-cache-check-for-transient-creation' value='yes'/>";

$tab_options = array(
	'tools-tools' => array(
		'tools-options'                      => array(
			'title' => __( 'Tools', 'yith-booking-for-woocommerce' ),
			'type'  => 'title',
		),
		'debug'                              => array(
			'id'        => 'yith-wcbk-debug',
			'name'      => __( 'Debug', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => sprintf(
			// translators: %s is  logs tab path (YITH > Booking > Tools > Logs).
				__( 'If enabled, the plugin will add some bookings-related debug logs that will be available in the "%s" tab.', 'yith-booking-for-woocommerce' ),
				sprintf(
					'YITH > Booking > %s > %s',
					_x( 'Tools', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					_x( 'Logs', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' )
				)
			),
			'default'   => 'no',
		),
		'booking-cache'                      => array(
			'name'             => __( 'Booking Cache', 'yith-booking-for-woocommerce' ),
			'type'             => 'yith-field',
			'yith-type'        => 'html',
			'yith-display-row' => true,
			'extra_row_class'  => 'yith-plugin-fw__panel__option--onoff',
			'html'             => $cache_field,
			'desc'             => implode(
				'<br />',
				array(
					__( 'If enabled, booking data are stored in cache to speed up the site.', 'yith-booking-for-woocommerce' ),
					__( 'Important: we suggest to <strong>keep it enabled</strong>; disable it only for testing purpose.', 'yith-booking-for-woocommerce' ),
					__( 'Please note: you can disable this option only for 24 hours; so it will be automatically activated 24 hours after disabling it.', 'yith-booking-for-woocommerce' ),
				)
			),
		),
		'sync-booking-product-prices'        => array(
			'name'             => __( 'Sync Prices', 'yith-booking-for-woocommerce' ),
			'type'             => 'yith-field',
			'yith-type'        => 'html',
			'yith-display-row' => true,
			'html'             => sprintf(
				'<a href="%s" class="yith-plugin-fw__button--secondary yith-plugin-fw__button--with-icon"><i class="yith-icon yith-icon-update"></i>%s</a>',
				wp_nonce_url(
					add_query_arg(
						array(
							'yith_wcbk_tools_action'   => 'sync_booking_product_prices',
							'yith_wcbk_tools_redirect' => rawurlencode( admin_url( 'admin.php?page=yith_wcbk_panel&tab=tools' ) ),
						),
						admin_url()
					),
					'yith_wcbk_tools_sync_booking_product_prices'
				),
				esc_html__( 'Sync Bookable Product Prices', 'yith-booking-for-woocommerce' )
			),
		),
		'booking-lookup-tables-regeneration' => array(
			'name'             => __( 'Booking look-up tables', 'yith-booking-for-woocommerce' ),
			'type'             => 'yith-field',
			'yith-type'        => 'html',
			'yith-display-row' => true,
			'html'             =>
				sprintf(
					'<a href="%s" class="yith-plugin-fw__button--secondary yith-plugin-fw__button--with-icon"><i class="yith-icon yith-icon-update"></i>%s</a>',
					wp_nonce_url(
						add_query_arg(
							array(
								'yith_wcbk_tools_action'   => 'regenerate_booking_lookup_tables',
								'yith_wcbk_tools_redirect' => rawurlencode( admin_url( 'admin.php?page=yith_wcbk_panel&tab=tools' ) ),
							),
							admin_url()
						),
						'yith_wcbk_tools_regenerate_booking_lookup_tables'
					),
					esc_html__( 'Regenerate', 'yith-booking-for-woocommerce' )
				),

		),
		'tools-options-end'                  => array(
			'type' => 'sectionend',
			'id'   => 'yith-wcbk-general-options',
		),

	),
);

if ( has_filter( 'yith_wcbk_is_cache_enabled' ) ) {
	$tab_options['tools-tools']['booking-cache']['desc'] .= '<br />';
	// translators: %s is the name of the filter.
	$tab_options['tools-tools']['booking-cache']['desc'] .= '<strong style="color:#e47400">' . sprintf( esc_html__( 'Warning: value overridden through %s filter', 'yith-booking-for-woocommerce' ), '<code>yith_wcbk_is_cache_enabled</code>' ) . '</strong>';
}

if ( yith_wcbk_sync_booking_product_prices_is_running() ) {
	$tab_options['tools-tools']['sync-booking-product-prices']['html'] = sprintf(
		'%s <a href="%s">%s &rarr;</a>',
		esc_html__( 'Updating bookable product prices in the background.', 'yith-booking-for-woocommerce' ),
		esc_url( admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=yith_wcbk_sync_booking_product_prices&status=pending' ) ),
		esc_html__( 'View progress', 'yith-booking-for-woocommerce' )
	);
}

if ( yith_wcbk_update_product_lookup_tables_is_running() ) {
	$tab_options['tools-tools']['booking-lookup-tables-regeneration']['html'] = sprintf(
		'%s <a href="%s">%s &rarr;</a>',
		esc_html__( 'Updating booking data in the background.', 'yith-booking-for-woocommerce' ),
		esc_url( admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=yith_wcbk_update_booking_lookup_tables&status=pending' ) ),
		esc_html__( 'View progress', 'yith-booking-for-woocommerce' )
	);
}

if ( isset( $_GET['debug'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$debug_options = array(
		'tools-info-options'                => array(
			'title' => 'Info',
			'type'  => 'title',
		),
		'tools-info-stored-booking-version' => array(
			'name'             => 'Stored Booking Version',
			'type'             => 'yith-field',
			'yith-type'        => 'html',
			'yith-display-row' => true,
			'html'             => get_option( YITH_WCBK_Install::VERSION_OPTION, '' ),
		),
		'tools-info-db-version'             => array(
			'name'             => 'DB Version',
			'type'             => 'yith-field',
			'yith-type'        => 'html',
			'yith-display-row' => true,
			'html'             => YITH_WCBK_Install::get_db_version(),
		),
		'tools-info-options-end'            => array(
			'type' => 'sectionend',
		),
	);

	$tab_options['tools-tools'] = array_merge( $tab_options['tools-tools'], $debug_options );
}

return apply_filters( 'yith_wcbk_panel_tools_tools_options', $tab_options );
