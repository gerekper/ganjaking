<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * YITH WooCommerce Points and Rewards Customer History List Table
 *
 * @class YITH_WC_Points_Rewards_Customer_History_List_Table
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */


class YITH_WC_Points_Rewards_Customer_History_List_Table extends WP_List_Table {

	/**
	 * @var
	 */
	protected $user_id;

	/**
	 * YITH_WC_Points_Rewards_Customer_History_List_Table constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array() );

		if ( ! isset( $_REQUEST['user_id'] ) || ! $_REQUEST['user_id'] ) {
			return;
		}

		$this->user_id = $_REQUEST['user_id'];
		$this->process_action();
	}

	/**
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function get_columns() {
		$columns = array(
			'id'           => __( 'ID', 'yith-woocommerce-points-and-rewards' ),
			'action'       => __( 'Action', 'yith-woocommerce-points-and-rewards' ),
			'order_id'     => __( 'Order No.', 'yith-woocommerce-points-and-rewards' ),
			'description'  => __( 'Description', 'yith-woocommerce-points-and-rewards' ),
			'amount'       => __( 'Amount', 'yith-woocommerce-points-and-rewards' ),
			'date_earning' => __( 'Date', 'yith-woocommerce-points-and-rewards' ),
		);
		return $columns;
	}

	/**
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function prepare_items() {
		global $wpdb, $_wp_column_headers;
		$screen                = get_current_screen();
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$user_id = ! empty( $_GET['user_id'] ) ? $_GET['user_id'] : 0;

		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'date_earning';
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'DESC';

		$order_string = 'ORDER BY ' . $orderby . ' ' . $order;

		$table_name = $wpdb->prefix . 'yith_ywpar_points_log';

		$query = "SELECT ywpar_points.* FROM $table_name as ywpar_points where user_id = $user_id $order_string";

		$totalitems = $wpdb->query( $query );

		$perpage = 25;
		// Which page is this?
		$paged = ! empty( $_GET['paged'] ) ? $_GET['paged'] : '';
		// Page Number
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}
		// How many pages do we have in total?
		$totalpages = ceil( $totalitems / $perpage );
		// adjust the query to take pagination into account
		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}

		/* -- Register the pagination -- */
		$this->set_pagination_args(
			array(
				'total_items' => $totalitems,
				'total_pages' => $totalpages,
				'per_page'    => $perpage,
			)
		);
		// The pagination links are automatically built according to those parameters

		$_wp_column_headers[ $screen->id ] = $columns;
		$this->items                       = $wpdb->get_results( $query );

	}

	/**
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function process_action() {

		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}

		$action = $_REQUEST['action'];
		if ( 'save' == $action && isset( $_REQUEST['user_points'] ) && 0 != $_REQUEST['user_points'] && wp_verify_nonce( $_POST['ywpar_update_points'], 'update_points' ) ) {
			$points_to_add = $_REQUEST['user_points'];
			$description   = $_REQUEST['description'];
			YITH_WC_Points_Rewards()->add_point_to_customer( $this->user_id, $points_to_add, 'admin_action', $description );
		}
	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string|void
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return $item->id;
				break;
			case 'order_id':
				if ( $item->order_id != 0 ) {
					return '<a href="' . admin_url( 'post.php?post=' . $item->order_id . '&action=edit' ) . '">' . sprintf( _x( 'Order #%d','Placeholder: Order number', 'yith-woocommerce-points-and-rewards' ), $item->order_id ) . '</a>';
				}break;
			case 'action':
				return YITH_WC_Points_Rewards()->get_action_label( $item->action );
				break;
			case 'description':
				return stripslashes( $item->description );
			   break;

			default:
				return ( isset( $item->$column_name ) ) ? $item->$column_name : '';
		}
	}


	/**
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'id'           => array( 'ID', false ),
			'action'       => array( 'action', false ),
			'order_id'     => array( 'order_id', false ),
			'amount'       => array( 'amount', false ),
			'date_earning' => array( 'date_earning', false ),
		);
		return $sortable_columns;
	}

}
