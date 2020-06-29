<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Report_Customer_List' ) ) {
	require_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-report-customer-list.php' );
}

$class = 'YITH_Report_Customer_List';

/**
 * YITH_Report_Customer_List
 *
 * @author      Andrea Grillo <andrea.grillo@yithemes.com>
 * @category    Admin
 * @version     1.8.1
 */
class YITH_Report_Customer_List extends WC_Report_Customer_List {

	/**
	 * column_default function.
	 *
	 * @param WP_User $user
	 * @param string $column_name
	 * @return string
	 */
	public function column_default( $user, $column_name ) {
		global $wpdb;

		if ( 'orders' == $column_name ) {
			if ( ! $count = get_user_meta( $user->ID, '_order_count', true ) ) {
				global $wpdb;

				$count = $wpdb->get_var( "SELECT COUNT(*)
					FROM $wpdb->posts as posts

					LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id

					WHERE   meta.meta_key       = '_customer_user'
					AND     posts.post_type     IN ('" . implode( "','", wc_get_order_types( 'order-count' ) ) . "')
					AND     posts.post_status   IN ('" . implode( "','", array_keys( wc_get_order_statuses() ) ) . "')
					AND     posts.post_parent   = 0
					AND     meta_value          = $user->ID
				" );

				update_user_meta( $user->ID, '_order_count', absint( $count ) );
			}

			$result = absint( $count );
		}

		else {
			$result = parent::column_default( $user, $column_name );
		}

		return $result;
	}
}
