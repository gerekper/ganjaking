<?php

class WCML_Capabilities {

	public static function set_up_capabilities() {

		$role = get_role( 'administrator' );
		if ( $role ) {
			$role->add_cap( 'wpml_manage_woocommerce_multilingual' );
			$role->add_cap( 'wpml_operate_woocommerce_multilingual' );
		}

		$role = get_role( 'super_admin' );
		if ( $role ) {
			$role->add_cap( 'wpml_manage_woocommerce_multilingual' );
			$role->add_cap( 'wpml_operate_woocommerce_multilingual' );
		}

		if ( is_multisite() ) {
			$super_admins = get_super_admins();
			foreach ( $super_admins as $admin ) {
				$user = new WP_User( $admin );
				$user->add_cap( 'wpml_manage_woocommerce_multilingual' );
				$user->add_cap( 'wpml_operate_woocommerce_multilingual' );
			}
		}

		$role = get_role( 'shop_manager' );
		if ( $role ) {
			$role->add_cap( 'wpml_operate_woocommerce_multilingual' );
		}

		self::reload_capabilities();
	}

	public static function reload_capabilities() {
		$user = wp_get_current_user();
		$user->get_role_caps();
	}

	public static function canManageWcml() {
		$allowedRoles = [ 'shop_manager', 'administrator', 'super_admin' ];

		return (bool) array_intersect( $allowedRoles, wp_get_current_user()->roles );
	}

	/**
	 * @return bool
	 */
	public static function canAccessAllWcmlTabs() {
		return current_user_can( 'wpml_manage_woocommerce_multilingual' )
			|| current_user_can( 'wpml_operate_woocommerce_multilingual' );
	}
}
