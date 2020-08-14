<?php

if ( ! function_exists( 'ywcm_get_shop_categories' ) ) {
    function ywcm_get_shop_categories( $show_all = true ) {
        global $wpdb;

        $terms = $wpdb->get_results( 'SELECT name, slug FROM ' . $wpdb->prefix . 'terms, ' . $wpdb->prefix . 'term_taxonomy WHERE ' . $wpdb->prefix . 'terms.term_id = ' . $wpdb->prefix . 'term_taxonomy.term_id AND taxonomy = "product_cat" ORDER BY name ASC;' );

        $categories = array();
        if ( $show_all ) {
            $categories['0'] = __( 'All categories', 'yith-woocommerce-cart-messages' );
        }
        if ( $terms ) {
            foreach ( $terms as $cat ) {
                $categories[$cat->slug] = ( $cat->name ) ? $cat->name : 'ID: ' . $cat->slug;
            }
        }
        return $categories;
    }
}

if ( ! function_exists( 'ywcm_get_current_user_name' ) ) {
	function ywcm_get_current_user_name() {

		if ( ! is_user_logged_in() ) {
			return apply_filters( 'ywcm_guest_user_name', __('Dear Guest', 'yith-woocommerce-cart-messages' ) );
		}

		$user_name = get_user_meta( get_current_user_id(), 'billing_first_name', true );
		if ( empty( $user_name ) ) {
			$user      = get_user_by( 'id', get_current_user_id() );
			$user_name = empty( $user->display_name ) ? $user->user_nicename : $user->display_name;
		}

		return apply_filters( 'ywcm_get_current_user_name', $user_name, get_current_user_id() );
	}
}

if ( ! function_exists( 'ywcm_get_roles' ) ) {
	/**
	 * Return the roles of users
	 *
	 * @return array
	 * @since 1.5.4
	 */
	function ywcm_get_roles() {
		global $wp_roles;
		$roles = array();
		foreach( $wp_roles->get_names() as $key => $role ) {
			$roles[$key] = translate_user_role( $role );
		}
		return array_merge( array( 'all' => __( 'All', 'yith-woocommerce-cart-messages' ) ), $roles );
	}
}