<?php
/**
 * Export settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

// retrieve lists
$list_options = YITH_WCAC()->retrieve_lists();

// retrieve terms
$categories         = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
$categories_options = array();

if ( $categories ) {
	foreach ( $categories as $cat ) {
		$categories_options[ $cat->term_id ] = $cat->name;
	}
}

// retrieve tags
$tags         = get_terms( 'product_tag', 'orderby=name&hide_empty=0' );
$tags_options = array();

if ( $tags ) {
	foreach ( $tags as $cat ) {
		$tags_options[ $cat->term_id ] = $cat->name;
	}
}

$users_placeholder             = __( 'Select customers', 'yith-woocommerce-active-campaign' );
$filter_products_placeholder   = __( 'Select products', 'yith-woocommerce-active-campaign' );
$filter_categories_placeholder = __( 'Select categories', 'yith-woocommerce-active-campaign' );
$filter_tags_placeholder       = __( 'Select tags', 'yith-woocommerce-active-campaign' );

$export_users = array(
	'title'   => __( 'Customers to export', 'yith-woocommerce-active-campaign' ),
	'desc'    => __( 'Select customers to export', 'yith-woocommerce-active-campaign' ),
	'id'      => 'yith_wcac_export_users',
	'css'     => 'width:300px;',
	'class'   => 'wc-customer-search',
	'options' => array()
);

$export_filter_products = array(
	'title'   => __( 'Filter by product', 'yith-woocommerce-active-campaign' ),
	'desc'    => __( 'Export users that bought at least one of the selected products', 'yith-woocommerce-active-campaign' ),
	'id'      => 'yith_wcac_export_filter_product',
	'css'     => 'width:300px;',
	'class'   => 'wc-product-search',
	'options' => array()
);

$export_filter_categories = array(
	'title'             => __( 'Filter by category', 'yith-woocommerce-active-campaign' ),
	'desc'              => __( 'Export users that bought a product belonging at least to one of the selected categories', 'yith-woocommerce-active-campaign' ),
	'id'                => 'yith_wcac_export_filter_category',
	'css'               => 'width:300px;',
	'class'             => 'chosen_select',
	'custom_attributes' => array(),
	'options'           => $categories_options
);

$export_filter_tags = array(
	'title'             => __( 'Filter by tag', 'yith-woocommerce-active-campaign' ),
	'desc'              => __( 'Export users that bought a product with at least one of the selected tags', 'yith-woocommerce-active-campaign' ),
	'id'                => 'yith_wcac_export_filter_tag',
	'css'               => 'width:300px;',
	'class'             => 'chosen_select',
	'custom_attributes' => array(),
	'options'           => $tags_options
);

$csv_users = array(
	'title'             => __( 'Users to export', 'yith-woocommerce-active-campaign' ),
	'desc'              => __( 'Select users to export', 'yith-woocommerce-active-campaign' ),
	'id'                => 'yith_wcac_csv_users',
	'css'               => 'width:300px;',
	'class'             => 'wc-customer-search',
	'custom_attributes' => array(),
	'options'           => array()
);

$csv_filter_products = array(
	'title'             => __( 'Filter by product', 'yith-woocommerce-active-campaign' ),
	'desc'              => __( 'Export users that bought a specific product', 'yith-woocommerce-active-campaign' ),
	'id'                => 'yith_wcac_csv_filter_product',
	'css'               => 'width:300px;',
	'class'             => 'wc-product-search',
	'custom_attributes' => array(),
	'options'           => array()
);

$csv_filter_categories = array(
	'title'             => __( 'Filter by category', 'yith-woocommerce-active-campaign' ),
	'desc'              => __( 'Export users that bought a product belonging to a specific category', 'yith-woocommerce-active-campaign' ),
	'id'                => 'yith_wcac_csv_filter_category',
	'css'               => 'width:300px;',
	'class'             => 'chosen_select',
	'custom_attributes' => array(),
	'options'           => $categories_options
);

$csv_filter_tags = array(
	'title'             => __( 'Filter by tag', 'yith-woocommerce-active-campaign' ),
	'desc'              => __( 'Export users that bought a product with a specific tag', 'yith-woocommerce-active-campaign' ),
	'id'                => 'yith_wcac_csv_filter_tag',
	'css'               => 'width:300px;',
	'class'             => 'chosen_select',
	'custom_attributes' => array(),
	'options'           => $tags_options
);

if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
	$select_multiple = array(
		'type'      => 'multiselect',
	);

	$export_users                      = array_merge( $export_users, $select_multiple );
	$export_users['custom_attributes'] = 'data-placeholder = "' . $users_placeholder . '"';

	$export_filter_products                      = array_merge( $export_filter_products, $select_multiple );
	$export_filter_products['custom_attributes'] = 'data-placeholder = "' . $filter_products_placeholder . '"';

	$export_filter_categories                      = array_merge( $export_filter_categories, $select_multiple );
	$export_filter_categories['custom_attributes'] = 'data-placeholder = "' . $filter_categories_placeholder . '"';

	$export_filter_tags                      = array_merge( $export_filter_tags, $select_multiple );
	$export_filter_tags['custom_attributes'] = 'data-placeholder = "' . $filter_tags_placeholder . '"';


	$csv_users                      = array_merge( $csv_users, $select_multiple );
	$csv_users['custom_attributes'] = 'data-placeholder = "' . $users_placeholder . '"';

	$csv_filter_products                      = array_merge( $csv_filter_products, $select_multiple );
	$csv_filter_products['custom_attributes'] = 'data-placeholder = "' . $filter_products_placeholder . '"';

	$csv_filter_categories                      = array_merge( $csv_filter_categories, $select_multiple );
	$csv_filter_categories['custom_attributes'] = 'data-placeholder = "' . $filter_categories_placeholder . '"';

	$csv_filter_tags                      = array_merge( $csv_filter_tags, $select_multiple );
	$csv_filter_tags['custom_attributes'] = 'data-placeholder = "' . $filter_tags_placeholder . '"';

} else {
	$export_users['type']                                  = 'text';
	$export_users['custom_attributes']['data-multiple']    = 'true';
	$export_users['custom_attributes']['data-placeholder'] = $users_placeholder;

	$export_filter_products['type']                                  = 'text';
	$export_filter_products['custom_attributes']['data-multiple']    = 'true';
	$export_filter_products['custom_attributes']['data-placeholder'] = $filter_products_placeholder;

	$export_filter_categories['type']                                  = 'select';
	$export_filter_categories['custom_attributes']['data-multiple']    = 'true';
	$export_filter_categories['custom_attributes']['data-placeholder'] = $filter_categories_placeholder;

	$export_filter_tags['type']                                  = 'select';
	$export_filter_tags['custom_attributes']['data-multiple']    = 'true';
	$export_filter_tags['custom_attributes']['data-placeholder'] = $filter_tags_placeholder;


	$csv_users['type']                                  = 'text';
	$csv_users['custom_attributes']['data-multiple']    = 'true';
	$csv_users['custom_attributes']['data-placeholder'] = $users_placeholder;

	$csv_filter_products['type']                                  = 'text';
	$csv_filter_products['custom_attributes']['data-multiple']    = 'true';
	$csv_filter_products['custom_attributes']['data-placeholder'] = $filter_products_placeholder;

	$csv_filter_categories['type']                                  = 'select';
	$csv_filter_categories['custom_attributes']['data-multiple']    = 'true';
	$csv_filter_categories['custom_attributes']['data-placeholder'] = $filter_categories_placeholder;

	$csv_filter_tags['type']                                  = 'select';
	$csv_filter_tags['custom_attributes']['data-multiple']    = 'true';
	$csv_filter_tags['custom_attributes']['data-placeholder'] = $filter_tags_placeholder;

}

return apply_filters( 'yith_wcac_export_options', array(
	'export' => array(
		'export-options' => array(
			'title' => __( 'Export', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => __( 'Export a group of users from your store to one of your Active Campaign lists', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_export_options'
		),

		'export-list' => array(
			'title'             => __( 'Active Campaign list', 'yith-woocommerce-active-campaign' ),
			'type'              => 'select',
			'desc'              => __( 'Select a list for new users', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_export_list',
			'options'           => $list_options,
			'custom_attributes' => empty( $list_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'css'               => 'min-width:300px;',
			'class'             => 'list-select'
		),

		'export-general-status' => array(
			'title'     => __( 'Status', 'yith-woocommerce-active-campaign' ),
			'type'      => 'select',
			'id'        => 'yith_wcac_export_status',
			'desc'      => __( 'Define the default contact status', 'yith-woocommerce-active-campaign' ),
			'options'   => array(
				'1' => __( 'Active', 'yith-woocommerce-active-campaign' ),
				'0' => __( 'Unsubscribe', 'yith-woocommerce-active-campaign' )
			),
			'default'   => '1'
		),

		'export-user-set' => array(
			'title'   => __( 'Groups of users', 'yith-woocommerce-active-campaign' ),
			'type'    => 'select',
			'desc'    => __( 'Select a group of users to export', 'yith-woocommerce-active-campaign' ),
			'id'      => 'yith_wcac_export_user_set',
			'options' => array_merge(
				array(
					'all'       => __( 'All Users', 'yith-woocommerce-active-campaign' ),
					'customers' => __( 'All Customers', 'yith-woocommerce-active-campaign' ),
					'set'       => __( 'Select a group of users manually', 'yith-woocommerce-active-campaign' ),
					'filter'    => __( 'Filter users using custom conditions', 'yith-woocommerce-active-campaign' ),
				),
				defined( 'YITH_WCWTL_PREMIUM' ) ? array(
					'waiting_lists' => __( 'Waiting Lists', 'yith-woocommerce-active-campaign' )
				) : array()
			),
			'css'     => 'min-width:300px;',
		),

		'export-users' => $export_users,

		'export-filter-products' => $export_filter_products,

		'export-filter-categories' => $export_filter_categories,

		'export-filter-tags' => $export_filter_tags,

		'export-filter-date' => array(
			'title' => __( 'Filter by date', 'yith-woocommerce-active-campaign' ),
			'type'  => 'date_range',
			'desc'  => __( 'Export users that purchased within this date range', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_export_filter_date'
		),

		'export-field-waiting-products' => array(
			'title'             => __( 'Waiting products', 'yith-woocommerce-active-campaign' ),
			'type'              => 'select',
			'desc'              => __( 'Specify an Active Campaign field where saving the waiting-list products subscribed by users', 'yith-woocommerce-active-campaign' ),
			'id'                => 'yith_wcac_export_field_waiting_products',
			'css'               => 'width:300px;',
			'options'           => array(),
			'custom_attributes' => array(
				'disabled' => 'disabled',
			),
			'class'             => 'chosen_select'
		),

		'export-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_export_options'
		),

		'csv-options' => array(
			'title' => __( 'CSV Download', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => __( 'Export a group of users from your store to a CSV file', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_csv_options'
		),

		'csv-user-set' => array(
			'title'   => __( 'Groups of users', 'yith-woocommerce-active-campaign' ),
			'type'    => 'select',
			'desc'    => __( 'Select a group of users to export', 'yith-woocommerce-active-campaign' ),
			'id'      => 'yith_wcac_csv_user_set',
			'options' => array_merge(
				array(
					'all'       => __( 'All Users', 'yith-woocommerce-active-campaign' ),
					'customers' => __( 'All Customers', 'yith-woocommerce-active-campaign' ),
					'set'       => __( 'Select a group of users manually', 'yith-woocommerce-active-campaign' ),
					'filter'    => __( 'Filter users using custom conditions', 'yith-woocommerce-active-campaign' )
				),
				defined( 'YITH_WCWTL_PREMIUM' ) ? array(
					'waiting_lists' => __( 'Waiting Lists', 'yith-woocommerce-active-campaign' )
				) : array()
			),
			'css'     => 'min-width:300px;',
		),

		'csv-users' => $csv_users,

		'csv-filter-products' => $csv_filter_products,

		'csv-filter-categories' => $csv_filter_categories,

		'csv-filter-tags' => $csv_filter_tags,

		'csv-filter-date' => array(
			'title' => __( 'Filter by date', 'yith-woocommerce-active-campaign' ),
			'type'  => 'date_range',
			'desc'  => __( 'Export users that purchased within this date range', 'yith-woocommerce-active-campaign' ),
			'id'    => 'yith_wcac_csv_filter_date'
		),

		'csv-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_csv_options'
		),
	)
) );