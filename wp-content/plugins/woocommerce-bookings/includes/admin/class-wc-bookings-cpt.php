<?php

/**
 * WC_Admin_CPT_Product Class.
 */
class WC_Bookings_CPT {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->type = 'wc_booking';

		// Post title fields
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );

		// Admin Columns
		add_filter( 'manage_edit-' . $this->type . '_columns', array( $this, 'edit_columns' ) );
		add_action( 'manage_' . $this->type . '_posts_custom_column', array( $this, 'custom_columns' ), 2 );
		add_filter( 'manage_edit-' . $this->type . '_sortable_columns', array( $this, 'custom_columns_sort' ) );
		add_filter( 'request', array( $this, 'custom_columns_orderby' ) );

		// Filtering
		add_action( 'restrict_manage_posts', array( $this, 'booking_filters' ) );
		add_filter( 'parse_query', array( $this, 'booking_filters_query' ) );
		add_filter( 'get_search_query', array( $this, 'search_label' ) );

		// Search
		add_filter( 'parse_query', array( $this, 'search_custom_fields' ) );

		// Actions
		add_filter( 'bulk_actions-edit-' . $this->type, array( $this, 'bulk_actions' ) );
		add_action( 'load-edit.php', array( $this, 'bulk_action' ) );
		add_action( 'admin_footer', array( $this, 'bulk_admin_footer' ), 10 );
		add_action( 'admin_notices', array( $this, 'bulk_admin_notices' ) );
	}

	/**
	 * Remove edit from the bulk actions.
	 *
	 * @param mixed $actions
	 * @return array
	 */
	public function bulk_actions( $actions ) {
		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}
		return $actions;
	}

	/**
	 * Add extra bulk action options to mark orders as complete or processing.
	 *
	 * Using Javascript until WordPress core fixes: http://core.trac.wordpress.org/ticket/16031
	 */
	public function bulk_admin_footer() {
		global $post_type;

		if ( $this->type === $post_type ) {
			?>
			<script type="text/javascript">
				jQuery( document ).ready( function ( $ ) {
					$( '<option value="confirm_bookings"><?php esc_html_e( 'Confirm bookings', 'woocommerce-bookings' ); ?></option>' ).appendTo( 'select[name="action"], select[name="action2"]' );
					$( '<option value="unconfirm_bookings"><?php esc_html_e( 'Unconfirm bookings', 'woocommerce-bookings' ); ?></option>' ).appendTo( 'select[name="action"], select[name="action2"]' );
					$( '<option value="cancel_bookings"><?php esc_html_e( 'Cancel bookings', 'woocommerce-bookings' ); ?></option>' ).appendTo( 'select[name="action"], select[name="action2"]' );
					$( '<option value="mark_paid_bookings"><?php esc_html_e( 'Mark bookings as paid', 'woocommerce-bookings' ); ?></option>' ).appendTo( 'select[name="action"], select[name="action2"]' );
					$( '<option value="mark_unpaid_bookings"><?php esc_html_e( 'Mark bookings as unpaid', 'woocommerce-bookings' ); ?></option>' ).appendTo( 'select[name="action"], select[name="action2"]' );
				});
			</script>
			<?php
		}
	}

	/**
	 * Process the new bulk actions for changing order status.
	 */
	public function bulk_action() {
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action = $wp_list_table->current_action();

		switch ( $action ) {
			case 'confirm_bookings':
				$new_status = 'confirmed';
				$report_action = 'bookings_confirmed';
				break;
			case 'unconfirm_bookings':
				$new_status = 'pending-confirmation';
				$report_action = 'bookings_unconfirmed';
				break;
			case 'mark_paid_bookings':
				$new_status = 'paid';
				$report_action = 'bookings_marked_paid';
				break;
			case 'mark_unpaid_bookings':
				$new_status = 'unpaid';
				$report_action = 'bookings_marked_unpaid';
				break;
			case 'cancel_bookings':
				$new_status = 'cancelled';
				$report_action = 'bookings_cancelled';
				break;
			default:
				return;
		}

		$changed = 0;

		$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );

		foreach ( $post_ids as $post_id ) {
			$booking = get_wc_booking( $post_id );
			if ( $booking->get_status() !== $new_status ) {
				$booking->update_status( $new_status );
			}
			$changed++;
		}

		$sendback = add_query_arg( array(
			'post_type' => $this->type,
			$report_action => true,
			'changed' => $changed,
			'ids' => join( ',', $post_ids ),
		), '' );
		wp_redirect( $sendback );
		exit();
	}

	/**
	 * Show confirmation message that order status changed for number of orders.
	 */
	public function bulk_admin_notices() {
		global $post_type, $pagenow;

		if ( isset( $_REQUEST['bookings_confirmed'] ) || isset( $_REQUEST['bookings_marked_paid'] ) || isset( $_REQUEST['bookings_marked_unpaid'] ) || isset( $_REQUEST['bookings_unconfirmed'] ) || isset( $_REQUEST['bookings_cancelled'] ) ) {
			$number = isset( $_REQUEST['changed'] ) ? absint( $_REQUEST['changed'] ) : 0;

			if ( 'edit.php' == $pagenow && $this->type == $post_type ) {
				/* translators: 1: number of booking statuses change */
				$message = sprintf( _n( '%1$s booking status changed.', '%1$s booking statuses changed.', $number, 'woocommerce-bookings' ), number_format_i18n( $number ) );
				echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
			}
		}
	}

	/**
	 * Change title boxes in admin.
	 *
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		if ( 'wc_booking' === $post->post_type ) {
			return __( 'Booking Title', 'woocommerce-bookings' );
		}
		return $text;
	}

	/**
	 * Change the columns shown in admin.
	 */
	public function edit_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}

		unset( $existing_columns['comments'], $existing_columns['title'], $existing_columns['date'] );

		$columns                    = array();
		$columns['booking_status']  = '<span class="status_head tips" data-tip="' . wc_sanitize_tooltip( __( 'Status', 'woocommerce-bookings' ) ) . '">' . esc_attr__( 'Status', 'woocommerce-bookings' ) . '</span>';
		$columns['booking_id']      = __( 'ID', 'woocommerce-bookings' );
		$columns['booked_product']  = __( 'Booked Product', 'woocommerce-bookings' );
		$columns['num_of_persons']  = __( '# of Persons', 'woocommerce-bookings' );
		$columns['customer']        = __( 'Booked By', 'woocommerce-bookings' );
		$columns['order']           = __( 'Order', 'woocommerce-bookings' );
		$columns['start_date']      = __( 'Start Date', 'woocommerce-bookings' );
		$columns['end_date']        = __( 'End Date', 'woocommerce-bookings' );
		$columns['booking_actions'] = __( 'Actions', 'woocommerce-bookings' );

		return array_merge( $existing_columns, $columns );
	}

	/**
	 * Make product columns sortable.
	 *
	 * https://gist.github.com/906872
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function custom_columns_sort( $columns ) {
		$custom = array(
			'booking_id'     => 'booking_id',
			'booked_product' => 'booked_product',
			'booking_status' => 'status',
			'start_date'     => 'start_date',
			'end_date'       => 'end_date',
		);
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Define our custom columns shown in admin.
	 *
	 * @param  string $column
	 * @global WC_Booking $booking
	 */
	public function custom_columns( $column ) {
		global $post, $booking;

		if ( ! is_a( $booking, 'WC_Booking' ) || $booking->get_id() !== $post->ID ) {
			$booking = new WC_Booking( $post->ID );
		}

		$product = $booking->get_product();

		switch ( $column ) {
			case 'booking_status':
				echo '<span class="status-' . esc_attr( $booking->get_status() ) . ' tips" data-tip="' . wc_sanitize_tooltip( wc_bookings_get_status_label( $booking->get_status() ) ) . '">' . esc_html( wc_bookings_get_status_label( $booking->get_status() ) ) . '</span>';
				break;
			case 'booking_id':
				/* translators: 1: a href to booking id */
				printf( '<a href="%s">' . esc_html__( 'Booking #%d', 'woocommerce-bookings' ) . '</a>', esc_url( admin_url( 'post.php?post=' . esc_attr( $post->ID ) . '&action=edit' ) ), esc_html( $post->ID ) );
				break;
			case 'num_of_persons':
				if ( ! is_object( $product ) || ! $product->has_persons() ) {
					esc_html_e( 'N/A', 'woocommerce-bookings' );
					break;
				}
				echo esc_html( array_sum( $booking->get_person_counts() ) );
				break;
			case 'customer':
				$customer      = $booking->get_customer();
				$customer_name = esc_html( $customer->name ?: '-' );

				if ( $customer->email ) {
					$customer_name = '<a href="mailto:' . esc_attr( $customer->email ) . '">' . esc_html( $customer_name ) . '</a>';
				}

				echo $customer_name; // phpcs:ignore WordPress.Security.EscapeOutput
				break;
			case 'booked_product':
				$resource = $booking->get_resource();

				if ( $product ) {
					echo '<a href="' . esc_url( admin_url( 'post.php?post=' . ( is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id ) . '&action=edit' ) ) . '">' . esc_html( $product->get_title() ) . '</a>';

					if ( $resource ) {
						echo ' (<a href="' . esc_url( admin_url( 'post.php?post=' . $resource->get_id() . '&action=edit' ) ) . '">' . esc_html( $resource->get_name() ) . '</a>)';
					}
				} else {
					echo '-';
				}
				break;
			case 'order':
				$order = $booking->get_order();
				if ( $order ) {
					echo '<a href="' . esc_url( admin_url( 'post.php?post=' . ( is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id ) . '&action=edit' ) ) . '">#' . esc_html( $order->get_order_number() ) . '</a> - ' . esc_html( wc_get_order_status_name( $order->get_status() ) );
				} else {
					echo '-';
				}
				break;
			case 'start_date':
				echo esc_html( $booking->get_start_date() );
				break;
			case 'end_date':
				echo esc_html( $booking->get_end_date() );
				break;
			case 'booking_actions':
				echo '<p>';
				$actions = array(
					'view' => array(
						'url'    => admin_url( 'post.php?post=' . $post->ID . '&action=edit' ),
						'name'   => __( 'View', 'woocommerce-bookings' ),
						'action' => 'view',
					),
				);

				if ( in_array( $booking->get_status(), array( 'pending-confirmation' ) ) ) {
					$actions['confirm'] = array(
						'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=wc-booking-confirm&booking_id=' . $post->ID ), 'wc-booking-confirm' ),
						'name'   => __( 'Confirm', 'woocommerce-bookings' ),
						'action' => 'confirm',
					);
				}

				$actions = apply_filters( 'woocommerce_admin_booking_actions', $actions, $booking );

				foreach ( $actions as $action ) {
					printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), wc_sanitize_tooltip( $action['name'] ), esc_attr( $action['name'] ) );
				}
				echo '</p>';
				break;
		}
	}

	/**
	 * Product column orderby.
	 *
	 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
	 *
	 * @access public
	 * @param mixed $vars
	 * @return array
	 */
	public function custom_columns_orderby( $vars ) {
		if ( isset( $vars['orderby'] ) ) {
			if ( 'booking_id' === $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'orderby' => 'ID',
				) );
			}

			if ( 'booked_product' === $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_booking_product_id',
					'orderby'  => 'meta_value_num',
				) );
			}

			if ( 'status' === $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'orderby' => 'post_status',
				) );
			}

			if ( 'start_date' === $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_booking_start',
					'orderby'  => 'meta_value_num',
				) );
			}

			if ( 'end_date' === $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_booking_end',
					'orderby'  => 'meta_value_num',
				) );
			}
		}

		return $vars;
	}

	/**
	 * Show a filter box.
	 */
	public function booking_filters() {
		global $typenow, $wp_query;

		if ( $typenow !== $this->type ) {
			return;
		}

		$filters = array();

		$products = WC_Bookings_Admin::get_booking_products();

		foreach ( $products as $product ) {
			$filters[ $product->get_id() ] = $product->get_name();

			$resources = $product->get_resources();

			foreach ( $resources as $resource ) {
				$filters[ $resource->get_id() ] = '&nbsp;&nbsp;&nbsp;' . $resource->get_name();
			}
		}

		$output = '';

		if ( $filters ) {
			$output .= '<select name="filter_bookings">';
			$output .= '<option value="">' . __( 'All Bookable Products', 'woocommerce-bookings' ) . '</option>';

			foreach ( $filters as $filter_id => $filter ) {
				$output .= '<option value="' . absint( $filter_id ) . '" ';

				if ( isset( $_REQUEST['filter_bookings'] ) ) {
					$output .= selected( $filter_id, $_REQUEST['filter_bookings'], false );
				}

				$output .= '>' . esc_html( $filter ) . '</option>';
			}

			$output .= '</select>';
		}

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Filter the products in admin based on options.
	 *
	 * @param mixed $query
	 */
	public function booking_filters_query( $query ) {
		global $typenow, $wp_query;

		if ( $typenow === $this->type ) {
			if ( ! empty( $_REQUEST['filter_bookings'] ) && empty( $query->query_vars['suppress_filters'] ) ) {
				$query->query_vars['meta_query'] = array(
					array(
						'key'   => get_post_type( $_REQUEST['filter_bookings'] ) === 'bookable_resource' ? '_booking_resource_id' : '_booking_product_id',
						'value' => absint( $_REQUEST['filter_bookings'] ),
					),
				);
			}
		}
	}

	/**
	 * Search custom fields.
	 *
	 * @param mixed $wp
	 */
	public function search_custom_fields( $wp ) {
		global $pagenow, $wpdb;

		if ( 'edit.php' != $pagenow || empty( $wp->query_vars['s'] ) || $wp->query_vars['post_type'] !== $this->type ) {
			return $wp;
		}

		$booking_ids = array();
		$term        = wc_clean( $_GET['s'] );

		if ( is_numeric( $term ) ) {
			$booking_ids[] = $term;
		}

		$order_ids   = wc_order_search( wc_clean( $_GET['s'] ) );
		$booking_ids = array_merge(
			$booking_ids,
			$order_ids ? WC_Booking_Data_Store::get_booking_ids_from_order_id( $order_ids ) : array( 0 ),
			wc_booking_search( wc_clean( $_GET['s'] ) )
		);

		$wp->query_vars['s']              = false;
		$wp->query_vars['post__in']       = $booking_ids;
		$wp->query_vars['booking_search'] = true;
	}

	/**
	 * Change the label when searching orders.
	 *
	 * @param mixed $query
	 * @return string
	 */
	public function search_label( $query ) {
		global $pagenow, $typenow;

		if ( 'edit.php' !== $pagenow ) {
			return $query;
		}

		if ( $typenow != $this->type ) {
			return $query;
		}

		if ( ! get_query_var( 'booking_search' ) ) {
			return $query;
		}

		return wc_clean( $_GET['s'] );
	}
}
