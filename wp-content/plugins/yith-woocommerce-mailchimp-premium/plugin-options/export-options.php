<?php
/**
 * Export settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

// retrieve lists
$list_options = YITH_WCMC()->retrieve_lists();

// retrieve terms
$categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
$categories_options = array();

if ( $categories ) {
	foreach ( $categories as $cat ) {
		$categories_options[ $cat->term_id ] = $cat->name;
	}
}

// retrieve tags
$tags = get_terms( 'product_tag', 'orderby=name&hide_empty=0' );
$tags_options = array();

if ( $tags ) {
	foreach ( $tags as $cat ) {
		$tags_options[ $cat->term_id ] = $cat->name;
	}
}

return apply_filters( 'yith_wcmc_export_options', array(
	'export' => array(
		'export-options' => array(
			'title' => __( 'Export', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => __( 'Export a set of users from your store to one of your MailChimp lists', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_export_options'
		),

		'export-list' => array(
			'title' => __( 'MailChimp list', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Select a list for the new user', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_export_list',
			'options' => $list_options,
			'custom_attributes' => empty( $list_options ) ? array(
				'disabled' => 'disabled'
			) : array(),
			'css' => 'min-width:300px;',
			'class' => 'list-select'
		),

		'export-email-type' => array(
			'title' => __( 'Email type', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'id' => 'yith_wcmc_export_email_type',
			'desc' => __( 'User preferential email type (HTML or plain text)', 'yith-woocommerce-mailchimp' ),
			'options' => array(
				'html' => __( 'HTML', 'yith-woocommerce-mailchimp' ),
				'text' => __( 'Text', 'yith-woocommerce-mailchimp' )
			),
			'default' => 'html'
		),

		'export-double-optin' => array(
			'title' => __( 'Double Opt-in', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_export_double_optin',
			'desc' => __( 'When you check this option, MailChimp will send a confirmation email before adding the user to the list', 'yith-woocommerce-mailchimp' ),
			'default' => ''
		),

		'export-update-existing' => array(
			'title' => __( 'Update existing', 'yith-woocommerce-mailchimp' ),
			'type' => 'checkbox',
			'id' => 'yith_wcmc_export_update_existing',
			'desc' => __( 'When you check this option, existing users will be updated and MailChimp servers will not show errors', 'yith-woocommerce-mailchimp' ),
			'default' => ''
		),

		'export-user-set' => array(
			'title' => __( 'Users set', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Select a set of users to export', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_export_user_set',
			'options' => array_merge(
				array(
					'all' => __( 'All Users', 'yith-woocommerce-mailchimp' ),
					'customers' => __( 'All Customers', 'yith-woocommerce-mailchimp' ),
					'set' => __( 'Select manually a set of users', 'yith-woocommerce-mailchimp' ),
					'filter' => __( 'Filter users using custom conditions', 'yith-woocommerce-mailchimp' ),
				),
				defined( 'YITH_WCWTL_PREMIUM' ) ? array(
					'waiting_lists' => __( 'Waiting Lists', 'yith-woocommerce-mailchimp' )
				) : array()
			),
			'css' => 'min-width:300px;',
		),

		'export-users' => array(
			'title' => __( 'Customers to export', 'yith-woocommerce-mailchimp' ),
			'type' => 'text',
			'desc' => __( 'Select customers to export', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_export_users',
			'css' => 'width:300px;',
			'class' => 'wc-customer-search',
			'custom_attributes' => array(
				'data-placeholder' => __( 'Select customers', 'yith-woocommerce-mailchimp' ),
				'data-multiple' => 'true'
			)
		),

		'export-filter-products' => array(
			'title' => __( 'Filter by product', 'yith-woocommerce-mailchimp' ),
			'type' => 'text',
			'desc' => __( 'Export users that bought at least one of the selected products', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_export_filter_product',
			'css' => 'width:300px;',
			'class' => 'wc-product-search',
			'custom_attributes' => array(
				'data-placeholder' => __( 'Select products', 'yith-woocommerce-mailchimp' ),
				'data-multiple' => 'true'
			)
		),

		'export-filter-categories' => array(
			'title' => __( 'Filter by categories', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Export users that bought a product belonging at least to one of the selected categories', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_export_filter_category',
			'css' => 'width:300px;',
			'class' => 'chosen_select',
			'custom_attributes' => array(
				'data-placeholder' => __( 'Select categories', 'yith-woocommerce-mailchimp' ),
				'multiple' => 'multiple'
			),
			'options' => $categories_options
		),

		'export-filter-tags' => array(
			'title' => __( 'Filter by tags', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Export users that bought a product with at least one of the selected tags', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_export_filter_tag',
			'css' => 'width:300px;',
			'class' => 'chosen_select',
			'custom_attributes' => array(
				'data-placeholder' => __( 'Select tags', 'yith-woocommerce-mailchimp' ),
				'multiple' => 'multiple'
			),
			'options' => $tags_options
		),

		'export-filter-date' => array(
			'title' => __( 'Filter by date', 'yith-woocommerce-mailchimp' ),
			'type' => 'date_range',
			'desc' => __( 'Export users that purchased within this date range', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_export_filter_date'
		),

		'export-field-waiting-products' => array(
			'title' => __( 'Waiting products', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'MailChimp field where all products users are waiting for appear', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_export_field_waiting_products',
			'css' => 'width:300px;',
			'options' => array(),
			'custom_attributes' => array(
				'disabled' => 'disabled',
			),
			'class' => 'chosen_select'
		),

		'export-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_export_options'
		),

		'csv-options' => array(
			'title' => __( 'Download CSV', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => __( 'Export a set of users from your store to a CVS file', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_csv_options'
		),

		'csv-user-set' => array(
			'title' => __( 'Users set', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Select a set of users to export', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_csv_user_set',
			'options' => array_merge(
				array(
					'all' => __( 'All Users', 'yith-woocommerce-mailchimp' ),
					'customers' => __( 'All Customers', 'yith-woocommerce-mailchimp' ),
					'set' => __( 'Select manually a set of users', 'yith-woocommerce-mailchimp' ),
					'filter' => __( 'Filter users using custom conditions', 'yith-woocommerce-mailchimp' )
				),
				defined( 'YITH_WCWTL_PREMIUM' ) ? array(
					'waiting_lists' => __( 'Waiting Lists', 'yith-woocommerce-mailchimp' )
				) : array()
			),
			'css' => 'min-width:300px;',
		),

		'csv-users' => array(
			'title' => __( 'Users to export', 'yith-woocommerce-mailchimp' ),
			'type' => 'text',
			'desc' => __( 'Select users to export', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_csv_users',
			'css' => 'width:300px;',
			'class' => 'wc-customer-search',
			'custom_attributes' => array(
				'data-placeholder' => __( 'Select users', 'yith-woocommerce-mailchimp' ),
				'data-multiple' => 'true'
			)
		),

		'csv-filter-products' => array(
			'title' => __( 'Filter by product', 'yith-woocommerce-mailchimp' ),
			'type' => 'text',
			'desc' => __( 'Export users that bought a specific product', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_csv_filter_product',
			'css' => 'width:300px;',
			'class' => 'wc-product-search',
			'custom_attributes' => array(
				'data-placeholder' => __( 'Select products', 'yith-woocommerce-mailchimp' ),
				'data-multiple' => 'true'
			)
		),

		'csv-filter-categories' => array(
			'title' => __( 'Filter by categories', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Export users that bought a product belonging to a specific category', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_csv_filter_category',
			'css' => 'width:300px;',
			'class' => 'chosen_select',
			'custom_attributes' => array(
				'data-placeholder' => __( 'Select categories', 'yith-woocommerce-mailchimp' ),
				'multiple' => 'multiple'
			),
			'options' => $categories_options
		),

		'csv-filter-tags' => array(
			'title' => __( 'Filter by tag', 'yith-woocommerce-mailchimp' ),
			'type' => 'select',
			'desc' => __( 'Export users that bought a product with a specific tag', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_csv_filter_tag',
			'css' => 'width:300px;',
			'class' => 'chosen_select',
			'custom_attributes' => array(
				'data-placeholder' => __( 'Select tags', 'yith-woocommerce-mailchimp' ),
				'multiple' => 'multiple'
			),
			'options' => $tags_options
		),

		'csv-filter-date' => array(
			'title' => __( 'Filter by date', 'yith-woocommerce-mailchimp' ),
			'type' => 'date_range',
			'desc' => __( 'Export users that purchased within this date range', 'yith-woocommerce-mailchimp' ),
			'id' => 'yith_wcmc_csv_filter_date'
		),

		'csv-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_csv_options'
		),
	)
) );