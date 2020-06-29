<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_SRE_Row_Total_Sign_Ups extends WC_SRE_Report_Row {

	/**
	 * The constructor
	 *
	 * @param $date_range
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct( $date_range ) {
		parent::__construct( $date_range, 'total-sign-ups', __( 'Total Sign Ups', 'woocommerce-sales-report-email' ) );
	}

	/**
	 * Prepare the data
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function prepare() {

		/**
		 * Copied from WC_Report_Customers#162 till #188
		 * Because the current reports classes are setup as Control and View classes combined it's not possible to properly reuse them.
		 * When these classes are refactored the new Report Control classes should be used here.
		 */
		$admin_users = new WP_User_Query(
			array(
				'role'   => 'administrator',
				'fields' => 'ID'
			)
		);

		$manager_users = new WP_User_Query(
			array(
				'role'   => 'shop_manager',
				'fields' => 'ID'
			)
		);

		$users_query = new WP_User_Query(
			array(
				'fields'  => array( 'user_registered' ),
				'exclude' => array_merge( $admin_users->get_results(), $manager_users->get_results() )
			)
		);

		$customers = $users_query->get_results();

		foreach ( $customers as $key => $customer ) {
			if ( strtotime( $customer->user_registered ) < $this->get_date_range()->get_start_date()->format( 'U' ) || strtotime( $customer->user_registered ) > $this->get_date_range()->get_end_date()->format( 'U' ) ) {
				unset( $customers[$key] );
			}
		}

		$this->set_value( count( $customers ) );
	}

}