<?php
/**
 * Lists options page
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Options
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters(
	'yith_wcwl_list_options',
	array(
		'dashboard-lists' => array(
			'wishlists' => array(
				'type'                 => 'yith-field',
				'yith-type'            => 'list-table',
				'class'                => 'yith-plugin-ui--classic-wp-list-style',
				'list_table_class'     => 'YITH_WCWL_Admin_Table',
				'list_table_class_dir' => YITH_WCWL_INC . 'tables/class-yith-wcwl-admin-table.php',
				'search_form'          => array(
					'text'     => __( 'Search list', 'yith-woocommerce-wishlist' ),
					'input_id' => 'search_list',
				),
				'id'                   => 'wishlist-filter',
			),
		),
	)
);
