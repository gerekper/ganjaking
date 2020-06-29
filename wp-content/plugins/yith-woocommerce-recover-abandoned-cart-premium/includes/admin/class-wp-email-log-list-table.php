<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Email Template List Table
 *
 * @class   YITH_YWRAC_Emails_List_Table
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */

class YITH_YWRAC_Email_Log_List_Table extends WP_List_Table {

	private $post_type;

	public function __construct( $args = array() ) {
		parent::__construct( array() );

	}

	function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />',
			'email'    => __( 'Email', 'yith-woocommerce-recover-abandoned-cart' ),
			'template' => __( 'Template', 'yith-woocommerce-recover-abandoned-cart' ),
			'cart_id'  => __( 'Abandoned Cart ID', 'yith-woocommerce-recover-abandoned-cart' ),
			'date'     => __( 'Date', 'yith-woocommerce-recover-abandoned-cart' ),
		);
		return $columns;
	}

	function prepare_items() {
		global $wpdb, $_wp_column_headers;

		$screen = get_current_screen();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$search = ! empty( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'date_send';
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'DESC';

		$order_string = 'ORDER BY ' . $orderby . ' ' . $order;

		$table_name = $wpdb->prefix . 'yith_ywrac_email_log';

		$query = "SELECT ywrac_logs.* FROM $table_name as ywrac_logs $order_string";

		if ( $search != '' ) {
			$query = "SELECT ywrac_logs.* FROM $table_name as ywrac_logs where email_id like '%$search%' $order_string";
		}

		$totalitems = $wpdb->query( $query );

		$perpage = 15;
		// Which page is this?
		$paged = ! empty( $_GET['paged'] ) ? $_GET['paged'] : '';
		// Page Number
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1; }
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

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'email':
				return $item->email_id;
				break;
			case 'template':
				return $item->email_template_id;
				break;
			case 'cart_id':
				return $item->ywrac_cart_id;
				break;
			case 'date':
				return $item->date_send;
				break;
			default:
				return ''; // Show the whole array for troubleshooting purposes
		}
	}

	function get_bulk_actions() {

		$actions = $this->current_action();
		if ( ! empty( $actions ) && isset( $_POST['ywrac_email_ids'] ) ) {
			global $wpdb;

			$table_name = $wpdb->prefix . 'yith_ywrac_email_log';
			$emails     = (array) $_POST['ywrac_email_ids'];
			$emails_in  = implode( ',', $emails );
			$wpdb->query( "DELETE FROM $table_name WHERE id IN ($emails_in)" );

			$this->prepare_items();
		}

		$actions = array(
			'delete' => __( 'Delete', 'yith-woocommerce-recover-abandoned-cart' ),
		);

		return $actions;
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="ywrac_email_ids[]" value="%s" />',
			$item->id
		);
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'email'    => array( 'email_id', false ),
			'template' => array( 'email_template_id', false ),
			'cart_id'  => array( 'ywrac_cart_id', false ),
			'date'     => array( 'date_send', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Display the search box.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $text The search button text
	 * @param string $input_id The search input id
	 */
	public function search_box( $text, $input_id ) {

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}

		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id; ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id; ?>" name="s" value="<?php _admin_search_query(); ?>" placeholder="<?php _e( 'Search Email', 'yith-woocommerce-recover-abandoned-cart' ); ?>"/>
			<?php submit_button( $text, 'button', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
		<?php
	}



}
