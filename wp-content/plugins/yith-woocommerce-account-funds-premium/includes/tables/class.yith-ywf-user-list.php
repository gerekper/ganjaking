<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * YITH WooCommerce Account Fudns Customers List Table
 * @class YITH_WC_Funds_User_List_Table
 * @package YITH WooCommerce Account Funds
 * @since   1.3.0
 * @author  YITH
 */

class YITH_WC_Funds_User_List_Table extends WP_List_Table {


	/**
	 * YITH_WC_Funds_User_List_Table constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array() );

		$this->process_bulk_action();
	}

	/**
	 * Column list.
	 *
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function get_columns() {
		$columns = array(
			'user_id'   => __( 'ID', 'yith-woocommerce-account-funds' ),
			'user_info' => __( 'User', 'yith-woocommerce-account-funds' ),
			'funds'    => __( 'Funds', 'yith-woocommerce-account-funds' ),
			'action'    => __( 'Action', 'yith-woocommerce-account-funds' ),
		);
		return $columns;
	}

	/**
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function prepare_items() {

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$users_per_page = 25;

		$paged = ( isset( $_GET['paged'] ) ) ? $_GET['paged'] : '';

		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
		);

		if ( $this->is_site_users )
			$args['blog_id'] = $this->site_id;

		if ( isset( $_REQUEST['orderby'] ) ){
			if(  $_REQUEST['orderby'] == 'meta_value_num' ){
				$args['meta_key'] = '_customer_fund';
			}
			$args['orderby'] = $_REQUEST['orderby'];
		}

		if ( isset( $_REQUEST['order'] ) ){
			$args['order'] = $_REQUEST['order'];
		}

		$args = $this->add_filter_args( $args );

		$wp_user_search = new WP_User_Query( $args );

		$this->items = $wp_user_search->get_results();
		$this->set_pagination_args( array(
			'total_items' => $wp_user_search->get_total(),
			'per_page' => $users_per_page,
		) );

	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return mixed|string|void
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'user_id':
				return $item->ID;
				break;
			case 'user_info':
				$email  = '<a href="mailto:' . $item->user_email . '">' . $item->user_email . '</a>';
				return $item->display_name . '<br>' . $email ;
				break;
			case 'funds':
				$funds = get_user_meta( $item->ID, '_customer_fund', true );
				$currency = get_woocommerce_currency();
				return wc_price( $funds, array( 'currency' => $currency ) );
				break;
			default:
				return ''; //Show the whole array for troubleshooting purposes
		}

	}


	/**
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'user_id'   => array( 'ID', false ),
			'user_info' => array( 'display_name', false ),
			'funds'    => array( 'meta_value_num', false ),
		);
		return $sortable_columns;
	}


	/**
	 * Handles the checkbox column output.
	 *
	 * @since 1.0.0
	 *
	 * @param object $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="user[]" value="%s" />', $item->ID );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function column_action( $item ) {
		$arg = remove_query_arg( array('paged','orderby','order'));
		$history_button = '<a href="' . add_query_arg( array( 'action'  => 'update',
		                                                      'user_id' => $item->ID
			), $arg ) . '" class="ywf_update_funds button action">' . __( 'View Logs', 'yith-woocommerce-account-funds' ) . '</a>';

		return $history_button;
	}



	/**
	 * Adds in any query arguments based on the current filters
	 *
	 * @since 1.0
	 * @param array $args associative array of WP_Query arguments used to query and populate the list table
	 * @return array associative array of WP_Query arguments used to query and populate the list table
	 */
	private function add_filter_args( $args ) {
		// filter by customer
		if ( isset( $_POST['_customer_user'] ) && $_POST['_customer_user'] > 0 ) {
			$args['include'] = array( $_POST['_customer_user'] );
		}

		return $args;
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination, which
	 * includes our Filters: Customers, Products, Availability Dates
	 *
	 * @see WP_List_Table::extra_tablenav();
	 * @since 1.0
	 * @param string $which the placement, one of 'top' or 'bottom'
	 */
	public function extra_tablenav( $which ) {
		if ( 'top' == $which ) {
			// Customers, products
			;

			echo '<div class="alignleft actions">';

				$user_string = '';
				$user_id = 0;
				$sel = '';
				if ( ! empty( $_REQUEST['_customer_user'] ) ) {
					$user_id     = absint( $_REQUEST['_customer_user'] );
					$user    = get_user_by( 'id', $user_id );
					$user_string = sprintf(
					/* translators: 1: user display name 2: user ID 3: user email */
						esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce' ),
						$user->display_name,
						absint( $user->ID ),
						$user->user_email
					);
					$sel[$user_id] = $user_string;
				}
				?>
            <select class="wc-customer-search" name="_customer_user" data-placeholder="<?php esc_attr_e( 'Filter by registered customer', 'woocommerce' ); ?>" data-allow_clear="true">
                <?php if( !empty( $sel ) ):?>
                <option value="<?php echo esc_attr( $user_id ); ?>" selected="selected"><?php echo htmlspecialchars( wp_kses_post( $user_string ) ); // htmlspecialchars to prevent XSS when rendered by selectWoo. ?><option>
            <?php endif;?>
            </select>
<?php
				submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );

			echo '</div>';
		}
	}


}
