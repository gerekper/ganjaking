<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Abandoned Carts List Table
 *
 * @class   YITH_YWRAC_Carts_List_Table
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */

class YITH_YWRAC_Carts_List_Table extends WP_List_Table {

	private $post_type;

	public function __construct( $args = array() ) {
		parent::__construct( array() );

		$this->post_type = YITH_WC_Recover_Abandoned_Cart()->post_type_name;
	}

	function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'post_title'   => __( 'Info', 'yith-woocommerce-recover-abandoned-cart' ),
			'email'        => __( 'Email', 'yith-woocommerce-recover-abandoned-cart' ),
			'phone'        => __( 'Phone', 'yith-woocommerce-recover-abandoned-cart' ),
			'subtotal'     => __( 'Subtotal', 'yith-woocommerce-recover-abandoned-cart' ),
			'status'       => __( 'Status', 'yith-woocommerce-recover-abandoned-cart' ),
			'status_email' => __( 'Last email sent', 'yith-woocommerce-recover-abandoned-cart' ),
			'last_update'  => __( 'Last update', 'yith-woocommerce-recover-abandoned-cart' ),
			'action'       => __( 'Action', 'yith-woocommerce-recover-abandoned-cart' ),
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

		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : '';
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'DESC';

		$link         = '';
		$order_string = '';

		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$order_string = 'ORDER BY ywrac_pm.meta_value ' . $order;
			switch ( $orderby ) {
				case 'email':
					$link = " AND ( ywrac_pm.meta_key = '_user_email' ) ";
					break;
				case 'status':
					$link = " AND ( ywrac_pm.meta_key = '_cart_status' ) ";
					break;
				case 'subtotal':
					$link         = " AND ( ywrac_pm.meta_key = '_cart_subtotal' ) ";
					$order_string = 'ORDER BY ywrac_pm.meta_value_num ' . $order;
					break;
				case 'last_update':
					$order_string = ' ORDER BY ywrac_p.post_date ' . $order;
					break;
				default:
					$order_string = ' ORDER BY ' . $orderby . ' ' . $order;
			}
		}

		$join = 'INNER JOIN ' . $wpdb->prefix . 'postmeta as ywrac_pm ON ( ywrac_p.ID = ywrac_pm.post_id )
        INNER JOIN ' . $wpdb->prefix . 'postmeta as ywrac_pm2 ON ( ywrac_p.ID = ywrac_pm2.post_id ) 
        INNER JOIN ' . $wpdb->prefix . 'postmeta as ywrac_pm3 ON ( ywrac_p.ID = ywrac_pm3.post_id )
        INNER JOIN ' . $wpdb->prefix . 'postmeta as ywrac_pm4 ON ( ywrac_p.ID = ywrac_pm4.post_id ) ';

		$where = "  AND ( ywrac_pm2.meta_key='_cart_status' AND ywrac_pm2.meta_value='abandoned') ";
		/*
		 FILTERS */
		// by user
		if ( isset( $_REQUEST['_customer_user'] ) && ! empty( $_REQUEST['_customer_user'] ) ) {
			$join  .= 'INNER JOIN ' . $wpdb->prefix . 'postmeta as ywrac_pm5 ON ( ywrac_p.ID =  ywrac_pm5.post_id ) ';
			$where .= " AND ( ywrac_pm5.meta_key = '_user_id' AND ywrac_pm5.meta_value = '" . $_REQUEST['_customer_user'] . "' )";
		}

		if ( isset( $_REQUEST['s'] ) ) {
			$search = '%' . $_REQUEST['s'] . '%';
			$query  = $wpdb->prepare(
				"SELECT ywrac_p.* FROM $wpdb->posts as ywrac_p $join
        WHERE 1=1 $link 
        $where 
        AND ywrac_p.post_type = %s
        AND ( ywrac_pm4.meta_key='_cart_subtotal' AND ywrac_pm4.meta_value NOT LIKE '0')
        AND ( ( ywrac_pm3.meta_value LIKE %s ) OR ( ywrac_p.post_title LIKE %s  ) )
        AND (ywrac_p.post_status = 'publish' OR ywrac_p.post_status = 'future' OR ywrac_p.post_status = 'draft' OR ywrac_p.post_status = 'pending' OR ywrac_p.post_status = 'private')
        GROUP BY ywrac_p.ID $order_string",
				$this->post_type,
				$search,
				$search
			);

		} else {
				$query = $wpdb->prepare(
					"SELECT ywrac_p.* FROM $wpdb->posts as ywrac_p $join
            WHERE 1=1 $link $where 
            AND ( ywrac_pm4.meta_key='_cart_subtotal' AND ywrac_pm4.meta_value NOT LIKE '0')
            AND ywrac_p.post_type = %s
            AND (ywrac_p.post_status = 'publish' OR ywrac_p.post_status = 'future' OR ywrac_p.post_status = 'draft' OR ywrac_p.post_status = 'pending' OR ywrac_p.post_status = 'private')
            GROUP BY ywrac_p.ID $order_string",
					$this->post_type
				);
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

		$this->items = $wpdb->get_results( $query );

	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'post_title':
				return $item->$column_name;
				break;
			case 'email':
				$user_email = get_post_meta( $item->ID, '_user_email', true );
				return $user_email;
				break;
			case 'phone':
				$user_phone = get_post_meta( $item->ID, '_user_phone', true );
				return $user_phone ? $user_phone : '-';
				break;
			case 'status':
				$user_email = get_post_meta( $item->ID, '_cart_status', true );
				return $user_email;
				break;
			case 'status_email':
				$emails_sent = get_post_meta( $item->ID, '_emails_sent', true );
				if ( empty( $emails_sent ) ) {
					$email_status = __( 'Not sent', 'yith-woocommerce-recover-abandoned-cart' );
				} else {
					$last         = end( $emails_sent );
					$email_status = $last['email_name'] . '<br>' . $last['data_sent'];
				}
				return '<span class="email_status" data-id="' . $item->ID . '">' . $email_status . '</span>';
				break;
			case 'subtotal':
				$currency = get_post_meta( $item->ID, '_user_currency', true );
				$subtotal = get_post_meta( $item->ID, '_cart_subtotal', true );
				if ( class_exists( 'WOOCS' ) ) {
					global $WOOCS;
					$WOOCS->current_currency = $currency;
				}

				$cart_subtotal = wc_price( $subtotal, array( 'currency' => $currency ) );
				return $cart_subtotal;
				break;
			case 'last_update':
				$last_update = $item->post_date;
				return $last_update;
				break;
			default:
				return ''; // Show the whole array for troubleshooting purposes
		}
	}

	function get_bulk_actions() {

		$actions = $this->current_action();
		if ( ! empty( $actions ) && isset( $_POST['ywrac_cart_ids'] ) ) {

			$carts = (array) $_POST['ywrac_cart_ids'];
			if ( $actions == 'sendemail' ) {
				foreach ( $carts as $cart_id ) {
					YITH_WC_Recover_Abandoned_Cart_Admin()->email_send( $cart_id );
				}
			} elseif ( $actions == 'delete' ) {
				foreach ( $carts as $cart_id ) {
					wp_delete_post( $cart_id, true );
				}
			}

			$this->prepare_items();
		}

		$actions = array(
			'delete' => __( 'Delete', 'yith-woocommerce-recover-abandoned-cart' ),
		);

		return $actions;
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="ywrac_cart_ids[]" value="%s" />',
			$item->ID
		);
	}


	function get_sortable_columns() {
		$sortable_columns = array(
			'post_title'  => array( 'post_title', false ),
			'email'       => array( 'email', false ),
			'subtotal'    => array( 'email', false ),
			'status'      => array( 'status', false ),
			'last_update' => array( 'last_update', false ),
		);
		return $sortable_columns;
	}

	function column_post_title( $item ) {
		admin_url( 'post.php?post=' . $item->ID . 'action=edit' );
		$actions = array(
			'edit' => '<a href="' . admin_url( 'post.php?post=' . $item->ID . '&action=edit' ) . '">' . __( 'View', 'yith-woocommerce-recover-abandoned-cart' ) . '</a>',
		);
		return sprintf( '%1$s %2$s', $item->post_title, $this->row_actions( $actions ) );
	}

	function column_action( $item ) {
		$html            = '';
		$email_templates = YITH_WC_Recover_Abandoned_Cart_Email()->get_email_templates( 'cart', false );

		if ( ! empty( $email_templates ) ) {
			  $select = '<select name="ywrac_template_email">';
			foreach ( $email_templates as $em ) {
				$select .= '<option value="' . $em->ID . '">' . $em->post_title . '</option>';
			}
			  $select .= '</select>';
			  $html    = $select . '<input type="button" id="sendemail" class="ywrac_send_email button action"  value="' . __( 'Send email', 'yith-woocommerce-recover-abandoned-cart' ) . '" data-id="' . $item->ID . '" data-type="cart">';
		} else {
			 $html = __( 'Add a new email template', 'yith-woocommerce-recover-abandoned-cart' );
		}

		return $html;
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
			<input type="search" id="<?php echo $input_id; ?>" name="s" value="<?php _admin_search_query(); ?>" placeholder="<?php _e( 'Search', 'yith-woocommerce-recover-abandoned-cart' ); ?>"/>
			<?php submit_button( $text, 'button', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
		<?php
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

			echo '<div class="alignleft actions">';
			if ( version_compare( WC()->version, '2.7', '<' ) ) {
				$user_string = '';
				$customer_id = '';
				$user        = '';
				if ( ! empty( $_POST['_customer_user'] ) ) {
					$customer_id = absint( $_POST['_customer_user'] );
					$user        = get_user_by( 'id', $customer_id );
					$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email );
				}

				?>
				<input type="hidden" class="wc-customer-search" id="customer_user" name="_customer_user" data-placeholder="<?php _e( 'Show All Customers', 'yith-woocommerce-recover-abandoned-cart' ); ?>" data-selected="<?php echo esc_attr( $user_string ); ?>" value="<?php echo $customer_id; ?>" data-allow_clear="true" style="width:200px" />
				<?php
				submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );

			} else {
				$user_string = '';
				$user_id     = 0;
				$sel         = array();
				if ( ! empty( $_REQUEST['_customer_user'] ) ) {
					$user_id = absint( $_REQUEST['_customer_user'] );
					$user    = get_user_by( 'id', $user_id );
					/* translators: 1: user display name 2: user ID 3: user email */
					$user_string = sprintf(
						esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce' ),
						$user->display_name,
						absint( $user->ID ),
						$user->user_email
					);

					$sel[ $user_id ] = $user_string;
				}

				yit_add_select2_fields(
					array(
						'type'              => 'hidden',
						'class'             => 'wc-customer-search',
						'id'                => 'customer_user',
						'name'              => '_customer_user',
						'data-placeholder'  => __( 'Show All Customers', 'yith-woocommerce-recover-abandoned-cart' ),
						'data-allow_clear'  => true,
						'data-selected'     => $sel,
						'data-multiple'     => false,
						'data-action'       => '',
						'value'             => $user_id,
						'style'             => 'width:200px',
						'custom-attributes' => array(),
					)
				);
				submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
			}
			echo '</div>';
		}
	}

}
