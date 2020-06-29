<?php
/**
 * List table: coupons.
 *
 * @package WC_Store_Credit/Admin/List_Tables
 * @since   3.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Store_Credit_Admin_List_Table_Coupons', false ) ) {
	return;
}

if ( ! class_exists( 'WC_Store_Credit_Admin_List_Table', false ) ) {
	include_once 'abstract-wc-store-credit-admin-list-table.php';
}

/**
 * Class WC_Store_Credit_Admin_List_Table_Coupons.
 */
class WC_Store_Credit_Admin_List_Table_Coupons extends WC_Store_Credit_Admin_List_Table {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $list_table_type = 'shop_coupon';

	/**
	 * Registers the custom filters.
	 *
	 * @since 3.1.0
	 */
	protected function register_filters() {
		$this->filters = array(
			'allowed_email' => array(
				'id'                => 'allowed_email',
				'type'              => 'select',
				'class'             => 'wc-customer-search',
				'style'             => 'width: 240px;',
				'custom_attributes' => array(
					'data-placeholder' => _x( 'Filter by customer or email', 'shop coupon filter', 'woocommerce-store-credit' ),
					'data-allow_clear' => true,
					'data-tags'        => true, // Allow guest users.
				),
			),
		);

		parent::register_filters();
	}

	/**
	 * Renders the 'allowed_email' filter.
	 *
	 * @since 3.1.0
	 *
	 * @param array $filter Filter data.
	 */
	protected function render_allowed_email_filter( $filter ) {
		if ( ! empty( $_GET[ $filter['id'] ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$value = wc_clean( wp_unslash( $_GET[ $filter['id'] ] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			$label = $value;

			if ( is_numeric( $value ) ) {
				$label = wc_store_credit_get_customer_choice_label( intval( $value ) );
			}

			if ( $label ) {
				$filter['options'] = array( $value => $label );
			}
		}

		$this->render_select_filter( $filter );
	}

	/**
	 * Query the 'allowed_email' filter.
	 *
	 * @since 3.1.0
	 *
	 * @param array $filter     Filter data.
	 * @param array $query_vars Query vars.
	 * @return mixed
	 */
	protected function query_allowed_email_filter( $filter, $query_vars ) {
		$value = ( ! empty( $_GET[ $filter['id'] ] ) ? wc_clean( wp_unslash( $_GET[ $filter['id'] ] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

		if ( $value ) {
			$email = wc_store_credit_get_customer_email( $value );

			if ( $email ) {
				if ( ! isset( $query_vars['meta_query'] ) ) {
					$query_vars['meta_query'] = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				}

				$query_vars['meta_query'][] = array(
					'key'     => 'customer_email',
					'value'   => $email,
					'compare' => 'LIKE',
				);
			}
		}

		return $query_vars;
	}
}
