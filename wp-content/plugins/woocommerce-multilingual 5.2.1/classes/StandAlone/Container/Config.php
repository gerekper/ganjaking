<?php

namespace WCML\StandAlone\Container;

class Config {

	/**
	 * @return array
	 */
	public static function getSharedInstances() {
		global $wpdb;

		return [
			$wpdb
		];
	}

	/**
	 * @return array
	 */
	public static function getAliases() {
		global $wpdb;

		$aliases = [];

		$wpdb_class = get_class( $wpdb );

		if ( 'wpdb' !== $wpdb_class ) {
			$aliases['wpdb'] = $wpdb_class;
		}

		return $aliases;
	}

	/**
	 * @return array
	 */
	public static function getSharedClasses() {
		return [
			\WPML\Core\ISitePress::class,
			\WPML_Notices::class,
		];
	}

	/**
	 * @return array
	 */
	public static function getDelegated() {
		return [
			\WPML_Notices::class => 'wcml_wpml_get_admin_notices',
		];
	}
}
