<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FUE_Sending_Queue_List_Table class
 * Output a List Table that mirrors the followup_email_orders table
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FUE_Reports_Customers_Table extends WP_List_Table {
	/**
	 * Create and instance of this list table.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular'  => __('customer', 'follow_up_emails'),
			'plural'    => __('customers', 'follow_up_emails'),
			'ajax'      => false
		) );
	}

	/**
	 * Prepare items to be displayed and setup pagination data
	 */
	public function prepare_items() {
		$wpdb       = Follow_Up_Emails::instance()->wpdb;

		$columns    = $this->get_columns();
		$hidden     = array();

		$sortable   = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$per_page   = 50;
		$page       = empty( $_GET['paged'] ) ? 1 : absint( $_GET['paged'] ); // phpcs:ignore WordPress.Security.NonceVerification
		$start      = ( $per_page * $page ) - $per_page;

		$sql = "SELECT SQL_CALC_FOUND_ROWS c.*, u.*
				FROM {$wpdb->prefix}followup_customers c
				LEFT JOIN {$wpdb->users} u ON c.user_id = u.ID
				WHERE 1=1";

		if ( !empty($_GET['_customer_user']) ) { // phpcs:ignore WordPress.Security.NonceVerification
			// filter by user id/user email
			if ( is_email( sanitize_text_field( wp_unslash( $_GET['_customer_user'] ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$sql .= " AND c.email_address = '". esc_sql( sanitize_email( wp_unslash( $_GET['_customer_user'] ) ) ) ."'"; // phpcs:ignore WordPress.Security.NonceVerification
			} else {
			   $sql .= " AND u.ID = ". esc_sql( absint( $_GET['_customer_user'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			}
		}

		$order = 'desc';
		$order_column = 'u.ID';

		if ( !empty( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$order_column = sanitize_text_field( wp_unslash( $_GET['orderby'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			$order = strtolower( $order_column == 'asc' ) ? 'asc' : 'desc';

			if ( !isset( $sortable[ $order_column ] ) ) {
				$order_column = 'u.ID';
			}

			if ( $order_column == 'lifetime_value' ) {
				$order_column = 'total_purchase_price';
			}
		}

		$sql .= " ORDER BY {$order_column} {$order} LIMIT {$start},{$per_page}";

		$users          = $wpdb->get_results( $sql, ARRAY_A );
		$items          = array();

		$total_items    = $wpdb->get_var("SELECT FOUND_ROWS()");

		foreach ( $users as $user ) {
			if ( empty( $user['user_id'] ) ) {
				$user['user_email'] = $user['email_address'];
			}

			if ( empty( $user['user_id'] ) && empty( $user['email_address'] ) ) {
				continue;
			}

			$fue_customer = fue_get_customer( $user['user_id'], $user['user_email'] );

			$user['is_subscriber']      = Follow_Up_Emails::instance()->newsletter->subscriber_exists( $user['user_email'] ) ? 'Yes' : 'No';
			$user['is_customer']        = ( $fue_customer && $fue_customer->total_orders > 0 ) ? 'Yes' : 'No';
			$user['last_order_date']    = '';

			if ( $fue_customer ) {
				$last_order_id = $wpdb->get_var($wpdb->prepare(
					"SELECT order_id
					FROM {$wpdb->prefix}followup_customer_orders
					WHERE followup_customer_id = %d
					ORDER BY order_id DESC
					LIMIT 1",
					$fue_customer->id
				));

				if ( $last_order_id > 0 ) {
					$date = $wpdb->get_var($wpdb->prepare(
						"SELECT post_date
						FROM {$wpdb->posts}
						WHERE ID = %d",
						$last_order_id
					));

					if ( $date ) {
						$user['last_order_date'] = date( wc_date_format(), strtotime( $date ) );
					}
				}
			}

			$items[] = $user;
		}

		$this->items = $items;

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );

	}

	/**
	 * Return the value for the columns
	 * @param array     $user
	 * @param string    $column
	 *
	 * @return string
	 */
	public function column_default( $user, $column ) {
		$value = '';

		switch ( $column ) {

			case 'is_customer':
				$value = $user['is_customer'];
				break;

			case 'is_subscriber':
				$value = $user['is_subscriber'];
				break;

			case 'total_orders':
				$value =  (!$user['total_orders'] || $user['total_orders'] < 0) ? 0 : $user['total_orders'];
				break;

			case 'lifetime_value':
				$value = max(0, $user['total_purchase_price']);
				$value = (function_exists( 'wc_price' ) ) ? wc_price( $value ) : 'n/a';
				break;

			case 'last_order_date':
				$value = (empty($user['last_order_date'])) ? 'n/a' : $user['last_order_date'];
				break;

		}

		return $value;
	}

	public function column_user( $user ) {

		if ( $user['user_id'] ) {
			$user_id = $user['user_id'];
			$email   = $user['user_email'];
			$display = $user['display_name'] .' (#'. $user_id .')';
		} else {
			$user_id    = 0;
			$email      = $user['email_address'];
			$display    = $user['email_address'] .' (Guest)';
		}

		$url = admin_url('admin.php?page=followup-emails-reports&tab=reportuser_view&email='. urlencode( $email ). '&user_id='. $user_id);
		$value = '<a href="'. $url .'" data-cid="'. $user['id'] .'">'. $display .'</a><br/><span class="email">'. $email .'</span>';

		return $value;
	}

	public function column_stats( $user ) {
		$wpdb           = Follow_Up_Emails::instance()->wpdb;
		$user_email     = $user['user_email'];

		if ( $user['user_id'] > 0 ) {
			$billing_email  = get_user_meta( $user['user_id'], 'billing_email', true );

			if ( is_email( $billing_email ) ) {
				$user_email = $billing_email;
			}

			$sent = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(DISTINCT email_order_id)
				FROM {$wpdb->prefix}followup_email_logs
				WHERE user_id = %d OR email_address = %s",
				$user['user_id'],
				$user_email
			));

			$opens  = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->prefix}followup_email_tracking
				WHERE (user_id = %d OR user_email = %s)
				AND event_type = 'open'",
				$user['user_id'],
				$user_email
			));

			$clicks  = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->prefix}followup_email_tracking
				WHERE (user_id = %d OR user_email = %s)
				AND event_type = 'click'",
				$user['user_id'],
				$user_email
			));
		} else {
			$sent = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(DISTINCT email_order_id)
				FROM {$wpdb->prefix}followup_email_logs
				WHERE email_address = %s",
				$user_email
			));

			$opens  = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->prefix}followup_email_tracking
				WHERE user_email = %s
				AND event_type = 'open'",
				$user_email
			));

			$clicks  = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->prefix}followup_email_tracking
				WHERE user_email = %s
				AND event_type = 'click'",
				$user_email
			));
		}

		$stats = sprintf( 'Sent: %d<br/>Opens: %d<br/>Clicks: %d', $sent, $opens, $clicks );

		return $stats;
	}

	/**
	 * Add all the columns
	 *
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 */
	public function get_columns(){

		$columns = array(
			'user'            => __( 'Customer Name', 'follow_up_emails' ),
			'is_customer'     => __( 'Purchased', 'follow_up_emails' ),
			'is_subscriber'   => __( 'Mailing List', 'follow_up_emails' ),
			'stats'           => __( 'Email Stats', 'follow_up_emails' ),
			'total_orders'    => __( 'Total Orders', 'follow_up_emails' ),
			'last_order_date' => __( 'Last Order Date', 'follow_up_emails' ),
			'lifetime_value'  => __( 'Lifetime Value', 'follow_up_emails' ),
		);

		return $columns;
	}

	/**
	 * Make the table sortable by all columns and set the default sort field to be 'send_on'.
	 *
	 * @return array An associative array containing all the columns that should be sortable: 'slugs' => array( 'data_values', bool )
	 */
	public function get_sortable_columns() {

		$sortable_columns = array(
			'user'          => array( 'user_login', true ),
			'total_orders'  => array( 'total_orders', false ),
			'lifetime_value'=> array( 'lifetime_value', false )
		);

		return $sortable_columns;
	}

	/**
	 * Generate the table navigation above or below the table
	 */
	function display_tablenav( $which ) {
		if ( 'top' == $which ) { ?>
			<input type="hidden" name="page" value="<?php echo esc_attr( sanitize_text_field( isset( $_REQUEST['page'] ) ? wp_unslash( $_REQUEST['page'] ) : '' ) ); // phpcs:ignore WordPress.Security.NonceVerification ?>" />

		<?php }
		parent::display_tablenav( $which );
	}

	/**
	 * Display extra filter controls between bulk actions and pagination.
	 *
	 * @since 1.3.1
	 */
	function extra_tablenav( $which ) {
		if ( 'top' == $which ) {
			$user_id     = '';
			$user_string = '';
			if ( ! empty( $_GET['_customer_user'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				if ( is_email( wp_unslash( $_GET['_customer_user'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$user_id     = 0;
					$user_string = esc_html( sanitize_email( wp_unslash( $_GET['_customer_user'] ) ) .' (Guest)' ); // phpcs:ignore WordPress.Security.NonceVerification
				} else {
					$user_id     = absint( $_GET['_customer_user'] ); // phpcs:ignore WordPress.Security.NonceVerification
					$user        = get_user_by( 'id', $user_id );
					$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) .')';
				}
			}
		?>
			<div class="alignleft actions">
				<select
					name="_customer_user"
					class="user-search-select"
					data-placeholder="<?php esc_attr_e( 'Search for a customer&hellip;', 'follow_up_emails' ); ?>"
					data-nonce="<?php echo esc_attr( wp_create_nonce( 'customer_search' ) ); ?>"
					data-allow_clear="true"
					tabindex="-1"
					title=""
				></select>
				<?php submit_button( __( 'Search' ), 'button', false, false, array( 'id' => 'post-query-submit' ) ); ?>
			</div>
		<?php
		}
	}
}

$table = new FUE_Reports_Customers_Table();
$table->prepare_items();
?>
<style>
	td span.email {color: #999; font-size: 12px;}
	th#user {width: 30%;}
	th#last_order_date {width: 15%;}
</style>
<div class="wrap">
	<h2><?php esc_html_e( 'Customer Data Manager', 'follow_up_emails' ); ?></h2>

	<form action="" method="get">
		<?php $table->display(); ?>
	</form>
</div>
<script>
	jQuery( document ).ready( function( $ ) {
		jQuery( ':input.user-search-select' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = {
				allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
				placeholder: jQuery( this ).data( 'placeholder' ),
				width:       '100%',
				minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : 3,
				escapeMarkup: function( m ) {
					return m;
				},
				ajax: {
					url:         ajaxurl,
					dataType:    'json',
					quietMillis: 250,
					data: function( params ) {
						return {
							term:   params.term,
							action: jQuery( this ).data( 'action' ) || 'fue_user_search',
							nonce:  jQuery( this ).data( 'nonce' ) || FUE.nonce
						};
					},
					processResults: function( data ) {
						var terms = [];
						if ( data ) {
							jQuery.each( data, function( id, text ) {
								terms.push( { id: id, text: text } );
							} );
						}
						return { results: terms };
					},
					cache: true
				},
			};

			jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
		} );
	} );
</script>
