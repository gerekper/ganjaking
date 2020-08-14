<?php


if ( ! function_exists( 'yith_pos_get_all_pos_capabilities' ) ) {
	/**
	 * get the POS capabilities
	 */
	function yith_pos_get_all_pos_capabilities() {
		$capabilities = array(
			'yith_pos_view_products',
			'yith_pos_create_products',
			'yith_pos_view_product_cats',
			'yith_pos_create_orders',
			'yith_pos_view_orders',
			'yith_pos_view_reports',
			'yith_pos_manage_pos',
			'yith_pos_use_pos',
			'yith_pos_view_coupons',
			'yith_pos_view_users',
			'yith_pos_edit_users',
			'yith_pos_create_users'
		);

		return $capabilities;
	}
}

if ( ! function_exists( 'yith_pos_get_current_user_pos_capabilities' ) ) {
	/**
	 * get the POS capabilities
	 */
	function yith_pos_get_current_user_pos_capabilities() {
		$capabilities = yith_pos_get_all_pos_capabilities();
		$caps         = array();
		foreach ( $capabilities as $capability ) {
			if ( current_user_can( $capability ) ) {
				$caps[ $capability ] = true;
			}
		}

		return apply_filters( 'yith_pos_get_current_user_pos_capabilities', $caps );
	}
}

if ( ! function_exists( 'yith_pos_post_capabilities' ) ) {
	/**
	 * Create an array of capabilities for the post type
	 *
	 * @param string $post_type
	 * @param bool   $values_only
	 *
	 * @return array
	 */
	function yith_pos_post_capabilities( $post_type, $values_only = true ) {
		$caps = array(
			'edit_post'              => "edit_{$post_type}",
			'delete_post'            => "delete_{$post_type}",
			'edit_posts'             => "edit_{$post_type}s",
			'edit_others_posts'      => "edit_others_{$post_type}s",
			'publish_posts'          => "publish_{$post_type}s",
			'read_private_posts'     => "read_private_{$post_type}s",
			'delete_posts'           => "delete_{$post_type}s",
			'delete_private_posts'   => "delete_private_{$post_type}s",
			'delete_published_posts' => "delete_published_{$post_type}s",
			'delete_others_posts'    => "delete_others_{$post_type}s",
			'edit_private_posts'     => "edit_private_{$post_type}s",
			'edit_published_posts'   => "edit_published_{$post_type}s",
			'create_posts'           => "create_{$post_type}s",
		);

		return $values_only ? array_values( $caps ) : $caps;
	}
}

if ( ! function_exists( 'yith_pos_get_post_capability' ) ) {
	/**
	 * Get a specific capability for a post type
	 *
	 * @param string $capability
	 * @param string $post_type
	 *
	 * @return string
	 */
	function yith_pos_get_post_capability( $capability, $post_type ) {
		$caps = yith_pos_post_capabilities( $post_type, false );

		return array_key_exists( $capability, $caps ) ? $caps[ $capability ] : $capability;
	}
}

if ( ! function_exists( 'yith_pos_get_manager_pos_capabilities' ) ) {
	function yith_pos_get_manager_pos_capabilities() {
		$pos_caps = yith_pos_get_all_pos_capabilities();

		$post_type  = YITH_POS_Post_Types::$store;
		$store_caps = array(
			"edit_{$post_type}",
			"edit_{$post_type}s",
			"edit_others_{$post_type}s",
			"edit_private_{$post_type}s",
			"edit_published_{$post_type}s",
		);

		$manager_caps = array_merge(
			$pos_caps,
			$store_caps,
			yith_pos_post_capabilities( YITH_POS_Post_Types::$register )
		);

		return apply_filters( 'yith_pos_manager_capabilities', $manager_caps );
	}
}

if ( ! function_exists( 'yith_pos_get_admin_pos_capabilities' ) ) {
	function yith_pos_get_admin_pos_capabilities() {
		$admin_pos_caps = array( 'yith_pos_manage_pos', 'yith_pos_manage_others_pos', 'yith_pos_manage_pos_options' );

		return array_unique( array_merge( $admin_pos_caps,
		                                  yith_pos_get_all_pos_capabilities(),
		                                  yith_pos_post_capabilities( YITH_POS_Post_Types::$store ),
		                                  yith_pos_post_capabilities( YITH_POS_Post_Types::$receipt ),
		                                  yith_pos_post_capabilities( YITH_POS_Post_Types::$register ) )
		);
	}
}

if ( ! function_exists( 'yith_pos_get_cashier_pos_capabilities' ) ) {
	function yith_pos_get_cashier_pos_capabilities() {
		return apply_filters( 'yith_pos_cashier_capabilities', array(
			'yith_pos_view_products',
			'yith_pos_view_product_cats',
			'yith_pos_create_orders',
			'yith_pos_view_orders',
			'yith_pos_view_reports',
			'yith_pos_view_coupons',
			'yith_pos_use_pos',
			'yith_pos_view_users',
			'yith_pos_edit_users',
			'yith_pos_create_users'
		) );
	}
}


if ( ! function_exists( 'yith_pos_get_error_message_capabilities' ) ) {
	function yith_pos_get_error_message_capabilities() {
		$error_messages = array(
			'yith_pos_view_products'     => __( 'You do not have permission to view products', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_create_products'   => __( 'You do not have permission to create products', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_view_product_cats' => __( 'You do not have permission to view product categories', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_create_orders'     => __( 'You do not have permission to create orders', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_view_orders'       => __( 'You do not have permission to see orders', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_manage_pos'        => __( 'You do not have permission to manage POS', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_view_reports'      => __( 'You do not have permission to view reports', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_view_coupons'      => __( 'You do not have permission to view coupons', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_use_pos'           => __( 'You do not have permission to use POS', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_view_users'        => __( 'You do not have permission to view users', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_edit_users'        => __( 'You do not have permission to edit users', 'yith-point-of-sale-for-woocommerce' ),
			'yith_pos_create_users'      => __( 'You do not have permission to create users', 'yith-point-of-sale-for-woocommerce' ),
		);

		return apply_filters( 'yith_pos_error_message_capabilities', $error_messages );
	}
}