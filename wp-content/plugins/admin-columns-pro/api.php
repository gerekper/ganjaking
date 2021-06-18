<?php

use AC\Asset;
use AC\Request;
use ACP\Bookmark\SegmentRepository;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @return ACP\AdminColumnsPro
 */
function ACP() {
	return ACP\AdminColumnsPro::instance();
}

/**
 * @return bool
 * @since 5.1
 */
function acp_sorting_show_all_results() {
	return ( new Sorting\Settings\AllResults() )->is_enabled();
}

/**
 * @return string
 * @since 4.4
 */
function acp_support_email() {
	return 'support@admincolumns.com';
}

/**
 * Check if an addon is compatible or not
 *
 * @param string $namespace
 * @param string $version
 *
 * @return bool
 */
function acp_is_addon_compatible( $namespace, $version ) {
	$addons = [
		'ACA\ACF'   => '2.4',
		'ACA\BP'    => '1.3.2',
		'ACA\EC'    => '1.2.3',
		'ACA\NF'    => '1.2.1',
		'ACA\Pods'  => '1.2.1',
		'ACA\Types' => '1.3.3',
		'ACA\WC'    => '3.2',
	];

	$namespace = rtrim( $namespace, '\\' );

	if ( ! array_key_exists( $namespace, $addons ) ) {

		return true;
	}

	return version_compare( $addons[ $namespace ], $version, '<=' );
}

/**
 * @return string
 */
function acp_get_license_page_url() {
	if ( is_multisite() && ACP()->is_network_active() ) {
		return ac_get_admin_network_url( 'settings' );
	}

	return ac_get_admin_url( 'settings' );
}

/**
 * @return Filtering\Helper
 * @since      4.2
 * @deprecated 4.5
 */
function acp_filtering_helper() {
	_deprecated_function( __FUNCTION__, '4.5', 'ACP\Filtering\Helper' );

	return new Filtering\Helper();
}

/**
 * @return Editing\Helper
 * @deprecated 4.5
 */
function acp_editing_helper() {
	_deprecated_function( __FUNCTION__, '4.5', 'ACP\Editing\Helper' );

	return new Editing\Helper();
}

/**
 * @since      4.0
 * @deprecated 5.1
 */
function acp_editing() {
	_deprecated_function( __FUNCTION__, '5.1' );

	return new Editing\Addon( AC()->get_storage(), new Asset\Location\Absolute( ACP()->get_url(), ACP()->get_dir() ), new Request() );
}

/**
 * @deprecated 5.1
 * @since      4.0
 */
function acp_filtering() {
	_deprecated_function( __FUNCTION__, '5.1' );

	return new Filtering\Addon( AC()->get_storage(), new Asset\Location\Absolute( ACP()->get_url(), ACP()->get_dir() ), new Request() );
}

/**
 * @deprecated 5.1
 * @since      4.0
 */
function acp_sorting() {
	_deprecated_function( __FUNCTION__, '5.1' );

	return new Sorting\Addon(
		AC()->get_storage(),
		new Asset\Location\Absolute( ACP()->get_url(), ACP()->get_dir() ),
		new Sorting\NativeSortableFactory(),
		new Sorting\ModelFactory(),
		new SegmentRepository()
	);
}

/**
 * @deprecated 5.1
 */
function ac_addon_export() {
	_deprecated_function( __FUNCTION__, '5.1' );

	return new Export\Addon( new Asset\Location\Absolute( ACP()->get_url(), ACP()->get_dir() ) );
}

/**
 * @deprecated 5.1
 */
function ac_addon_search() {
	_deprecated_function( __FUNCTION__, '5.1' );

	return new Search\Addon(
		AC()->get_storage(),
		new Asset\Location\Absolute( ACP()->get_url(), ACP()->get_dir() ),
		new SegmentRepository()
	);
}