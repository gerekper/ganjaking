<?php
/**
 * Rest API Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_rest_check_manager_permissions' ) ) {
	/**
	 * Check the "manager" permissions for the Rest API.
	 * Useful for example to check for permissions for Global Rules (availability and price).
	 *
	 * @param string $object_type The object type.
	 * @param string $context     The REST context (create, read, update, delete).
	 *
	 * @return bool
	 * @since 5.3.1
	 */
	function yith_wcbk_rest_check_manager_permissions( string $object_type, string $context = 'read' ): bool {
		$object_capability_map = array(
			'global_availability_rules' => 'manage_options',
			'global_price_rules'        => 'manage_options',
		);

		$permission = isset( $object_capability_map[ $object_type ] ) && current_user_can( $object_capability_map[ $object_type ] );

		if ( 'global_availability_rules' === $object_type ) {
			yith_wcbk_deprecated_filter( 'yith_wcbk_rest_capability_for_manage_availability_rules', '5.3.1', 'yith_wcbk_rest_check_manager_permissions' );
			$deprecated_cap = apply_filters( 'yith_wcbk_rest_capability_for_manage_availability_rules', 'manage_options' );
			if ( 'manage_option' !== $deprecated_cap ) {
				$permission = current_user_can( $object_capability_map[ $object_type ] );
			}
		}

		return apply_filters( 'yith_wcbk_rest_check_manager_permissions', $permission, $context, $object_type );
	}
}
