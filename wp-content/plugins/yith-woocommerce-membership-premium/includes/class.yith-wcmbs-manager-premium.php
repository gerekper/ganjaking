<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Manager Class
 *
 * @class   YITH_WCMBS_Manager_Premium
 * @package YITH WooCommece Membership
 * @since   1.0.0
 */
class YITH_WCMBS_Manager_Premium extends YITH_WCMBS_Manager {

	/**
	 * Single instance of the class
	 *
	 * @var YITH_WCMBS_Manager_Premium
	 * @since      1.0.0
	 * @deprecated 1.4.0 | use YITH_WCMBS_Manager instead
	 */
	protected static $_instance;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since  1.0.0
	 */
	protected function __construct() {
		parent::__construct();
	}
}
