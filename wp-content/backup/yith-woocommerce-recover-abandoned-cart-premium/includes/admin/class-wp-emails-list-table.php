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

class YITH_YWRAC_Emails_List_Table extends WP_List_Table {

	private $post_type;

	public function __construct( $args = array() ) {
		parent::__construct( array() );
		$this->post_type = YITH_WC_Recover_Abandoned_Cart_Email()->post_type_name;
	}

	function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'post_title' => __( 'Name', 'yith-woocommerce-recover-abandoned-cart' ),
			'send_after' => __( 'Send after', 'yith-woocommerce-recover-abandoned-cart' ),
			'subject'    => __( 'Subject', 'yith-woocommerce-recover-abandoned-cart' ),
			'message'    => __( 'Message', 'yith-woocommerce-recover-abandoned-cart' ),
			'conversion' => __( 'Conversion Rate', 'yith-woocommerce-recover-abandoned-cart' ),
			'status'     => __( 'Status', 'yith-woocommerce-recover-abandoned-cart' ),
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

		$args  = array(
			'post_type' => $this->post_type,
		);
		$query = new WP_Query( $args );

		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : '';
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'DESC';

		$link         = '';
		$order_string = '';
		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$order_string = 'ORDER BY ywrac_pm.meta_value ' . $order;
			switch ( $orderby ) {
				case 'subject':
					$link = " AND ( ywrac_pm.meta_key = '_ywrac_email_subject' ) ";
					break;
				case 'message':
					$order_string = ' ORDER BY ywrac_p.post_content ' . $order;
					break;
				case 'status':
					$link = " AND ( ywrac_pm.meta_key = '_ywrac_email_active' ) ";
					break;
				default:
					$order_string = ' ORDER BY ' . $orderby . ' ' . $order;
			}
		}

		$query = $wpdb->prepare(
			"SELECT ywrac_p.* FROM $wpdb->posts as ywrac_p INNER JOIN " . $wpdb->prefix . "postmeta as ywrac_pm ON ( ywrac_p.ID = ywrac_pm.post_id )
        WHERE 1=1 $link
        AND ywrac_p.post_type = %s
        AND (ywrac_p.post_status = 'publish' OR ywrac_p.post_status = 'future' OR ywrac_p.post_status = 'draft' OR ywrac_p.post_status = 'pending' OR ywrac_p.post_status = 'private')
        GROUP BY ywrac_p.ID $order_string",
			$this->post_type
		);

		$totalitems = $wpdb->query( $query );

		$perpage = 5;
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
			case 'post_title':
				return $item->$column_name;
				break;
			case 'send_after':
				$type_time = get_post_meta( $item->ID, '_ywrac_type_time', true );
				$time      = get_post_meta( $item->ID, '_ywrac_time', true );
				return $time . ' ' . $type_time;
				break;
			case 'subject':
				$user_email = get_post_meta( $item->ID, '_ywrac_email_subject', true );
				return $user_email;
				break;
			case 'message':
				$message = yith_ywrac_get_excerpt( $item->ID );
				return '<span class="ywrac-content-message">' . $message . '</span>';
				break;
			case 'conversion':
				$email_sent      = intval( apply_filters( 'ywrac_email_template_sent_counter', get_post_meta( $item->ID, '_email_sent_counter', true ), $item->ID ) );
				$recovered_carts = intval( apply_filters( 'ywrac_email_template_cart_recovered', get_post_meta( $item->ID, '_cart_recovered', true ), $item->ID ) );
				if ( $email_sent != 0 ) {
					$conversion = number_format( 100 * $recovered_carts / $email_sent, 2, '.', '' ) . ' %';
				} else {
					$conversion = '0.00 %';
				}
				return $conversion;
				break;
			case 'status':
				$status = get_post_meta( $item->ID, '_ywrac_email_active', true );
				$class  = ( $status == 'yes' ) ? 'active' : 'deactive';
				return '<span class="ywrac-email-status ' . $class . '">' . $status . '</span>';
				break;
			default:
				return ''; // Show the whole array for troubleshooting purposes
		}
	}

	function get_bulk_actions() {

		$actions = $this->current_action();
		if ( ! empty( $actions ) && isset( $_POST['ywrac_email_ids'] ) ) {

			$emails = (array) $_POST['ywrac_email_ids'];
			if ( $actions == 'activate' ) {
				foreach ( $emails as $email_id ) {
					YITH_WC_Recover_Abandoned_Cart_Email()->activate( $email_id, true );
				}
			} elseif ( $actions == 'deactivate' ) {
				foreach ( $emails as $email_id ) {
					YITH_WC_Recover_Abandoned_Cart_Email()->activate( $email_id, false );
				}
			} elseif ( $actions == 'delete' ) {
				foreach ( $emails as $email_id ) {
					wp_delete_post( $email_id, true );
				}
			}

			$this->prepare_items();
		}

		$actions = array(
			'activate'   => __( 'Activate', 'yith-woocommerce-recover-abandoned-cart' ),
			'deactivate' => __( 'Deactivate', 'yith-woocommerce-recover-abandoned-cart' ),
			'delete'     => __( 'Delete', 'yith-woocommerce-recover-abandoned-cart' ),
		);

		return $actions;
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="ywrac_email_ids[]" value="%s" />',
			$item->ID
		);
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'post_title' => array( 'post_title', false ),
			'status'     => array( 'status', false ),
		);
		return $sortable_columns;
	}

	function column_post_title( $item ) {
		admin_url( 'post.php?post=' . $item->ID . 'action=edit' );
		$actions = array(
			'edit'   => '<a href="' . admin_url( 'post.php?post=' . $item->ID . '&action=edit' ) . '">' . __( 'Edit', 'yith-woocommerce-recover-abandoned-cart' ) . '</a>',
			'delete' => '<a href="' . YITH_WC_Recover_Abandoned_Cart_Email()->get_delete_post_link( '', $item->ID ) . '">' . __( 'Delete', 'yith-woocommerce-recover-abandoned-cart' ) . '</a>',
		);

		return sprintf( '%1$s %2$s', $item->post_title, $this->row_actions( $actions ) );
	}

}
