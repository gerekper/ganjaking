<?php
/**
 * WooCommerce Order Status Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Order Status Manager Order Statuses class
 *
 * @since 1.0.0
 */
class WC_Order_Status_Manager_Order_Statuses {


	/** @var array Core/built-in/manually registered order statuses **/
	private $core_order_statuses = array();

	/** @var string Previous order status slug. Used when changing an order status's slug. **/
	private $_previous_order_status_slug;


	/**
	 * Set up custom order statuses
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// include the Order Status class
		require_once( wc_order_status_manager()->get_plugin_path() . '/src/class-wc-order-status-manager-order-status.php' );

		// store core order statuses before introducing custom order statuses
		$this->core_order_statuses = wc_get_order_statuses();

		// add in custom order statuses
		add_filter( 'wc_order_statuses', array( $this, 'order_statuses' ) );

		// add custom paid statuses
		add_filter( 'woocommerce_order_is_paid_statuses', array( $this, 'order_paid_statuses' ) );

		// grant download permissions on custom paid statuses
		add_action( 'woocommerce_order_status_changed', array( $this, 'regenerate_download_permissions' ), 10, 3 );

		// add statuses that require payment (and thus can be cancelled)
		add_filter( 'woocommerce_valid_order_statuses_for_payment', [ $this, 'order_needs_payment_statuses' ] );
		add_filter( 'woocommerce_valid_order_statuses_for_cancel',  [ $this, 'order_needs_payment_statuses' ] );
		// filtering this will help skipping held stock checks when paying for orders with custom unpaid statuses
		add_filter( 'woocommerce_order_is_pending_statuses',        [ $this, 'order_needs_payment_statuses' ] );

		// add statuses set to require a payment to statuses valid
		// to receive payment complete status (complete or processing)
		add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', array( $this, 'order_statuses_valid_for_payment_complete' ) );

		// disallow editing order items for custom order statuses marked as paid
		add_filter( 'wc_order_is_editable', array( $this, 'is_order_editable' ), 10, 2 );

		// order statuses that can show purchase notes
		add_filter( 'woocommerce_purchase_note_order_statuses', array( $this, 'show_purchase_note' ) );

		// custom sorting set by ajax via menu order
		// leave this priority at > 10 for FacetWP compat {BR 2017-04-19}
		add_action( 'pre_get_posts', array( $this, 'order_statuses_custom_sorting' ), 11 );

		// truncate slugs to 17 chars
		add_filter( 'wp_unique_post_slug', array( $this, 'truncate_order_status_slug' ), 10, 4 );

		// handle order status changes
		add_action( 'pre_post_update', array( $this, 'queue_orders_update' ), 10, 2 );
		add_action( 'delete_post',     array( $this, 'handle_order_status_delete' ) );

		// include custom statuses in order reports
		add_filter( 'woocommerce_reports_order_statuses', array( $this, 'reports_order_statuses' ) );

		// include custom statuses in the partial refund calculations of the reports
		add_filter( 'woocommerce_reports_get_order_report_data_args', array( $this, 'order_report_data_args' ) );
	}


	/**
	 * Get core order statuses
	 *
	 * @since 1.0.0
	 * @return array of order status slug to name, ie 'wc-pending' => 'Pending Payment'
	 */
	public function get_core_order_statuses() {
		return $this->core_order_statuses;
	}


	/**
	 * Check if a status is a core status
	 *
	 * @since 1.0.0
	 * @param string $status status slug (with or without prefix)
	 * @return boolean true if this is a core status
	 */
	public function is_core_status( $status ) {

		$status              = str_replace( 'wc-', '', $status );
		$core_order_statuses = $this->get_core_order_statuses();

		return isset( $core_order_statuses[ 'wc-' . $status ] );
	}


	/**
	 * Get order status posts
	 *
	 * @since 1.0.0
	 * @param array $args Optional. List of get_post args
	 * @return \WP_Post[] Array of WP_Post objects
	 */
	public function get_order_status_posts( $args = array() ) {
		return wc_order_status_manager_get_order_status_posts( $args );
	}


	/**
	 * Sort order statuses by custom order
	 *
	 * Uses menu_order to change sorting of statuses
	 *
	 * @since 1.3.0
	 * @param \WP_Query $query
	 */
	public function order_statuses_custom_sorting( $query ) {

		$post_type = $query->get( 'post_type' );

		if ( 'wc_order_status' === $post_type ) {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order' , 'ASC' );
		}
	}


	/**
	 * Ensure that all wc order statuses have posts associated with them
	 *
	 * This way, all statuses are customizable
	 *
	 * @since 1.0.0
	 */
	public function ensure_statuses_have_posts() {

		$status_posts = $this->get_order_status_posts();

		foreach ( wc_get_order_statuses() as $slug => $name ) {

			// truncate slugs to 17 chars to handle plugins doing_it_wrong() by using register_post_status() with a slug >20 chars ಠ_ಠ
			$slug_without_prefix = substr( str_replace( 'wc-', '', $slug ), 0, 17 );
			$has_post = false;

			foreach ( $status_posts as $status_post ) {

				if ( $slug_without_prefix === $status_post->post_name ) {
					$has_post = true;
					break;
				}
			}

			if ( ! $has_post ) {
				$this->create_post_for_status( $slug_without_prefix, $name );
			}
		}
	}


	/**
	 * Creates a custom post type (wc_order_status) for an order status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug slug, for example: `processing`
	 * @param string $name name, for example: `Processing`
	 * @return int the created post id
	 */
	public function create_post_for_status( $slug, $name ) {

		$post_id = wp_insert_post( array(
			'post_name'   => $slug,
			'post_title'  => $name,
			'post_type'   => 'wc_order_status',
			'post_status' => 'publish'
		) );

		$core_order_statuses = $this->get_core_order_statuses();

		// Create default settings for core statuses. These are set here
		// manually based on WC core statuses implementation
		if ( isset( $core_order_statuses[ 'wc-' . $slug ] ) ) {

			$paid_statuses = wc_get_is_paid_statuses();

			update_post_meta( $post_id, '_color', $this->get_core_order_status_color( $slug ) );

			switch ( $slug ) {

				case 'pending':

					update_post_meta( $post_id, '_icon',          'wcicon-status-pending' );
					update_post_meta( $post_id, '_next_statuses', $paid_statuses );
					update_post_meta( $post_id, '_is_paid',       'needs_payment' );

				break;

				case 'processing':

					$completed_paid_statuses = $paid_statuses;
					unset( $completed_paid_statuses['processing'] );

					update_post_meta( $post_id, '_icon',               'wcicon-status-processing' );
					update_post_meta( $post_id, '_action_icon',        'wcicon-processing' );
					update_post_meta( $post_id, '_next_statuses',      $completed_paid_statuses );
					update_post_meta( $post_id, '_bulk_action',        'yes' );
					update_post_meta( $post_id, '_include_in_reports', 'yes' );
					update_post_meta( $post_id, '_is_paid',            'yes' );

				break;

				case 'on-hold':

					update_post_meta( $post_id, '_icon',               'wcicon-on-hold' );
					update_post_meta( $post_id, '_next_statuses',      $paid_statuses );
					update_post_meta( $post_id, '_bulk_action',        'yes' );
					update_post_meta( $post_id, '_include_in_reports', 'yes' );
					update_post_meta( $post_id, '_is_paid',            'no' );

				break;

				case 'completed':

					update_post_meta( $post_id, '_icon',               'wcicon-status-completed' );
					update_post_meta( $post_id, '_action_icon',        'dashicons dashicons-yes' );
					update_post_meta( $post_id, '_bulk_action',        'yes' );
					update_post_meta( $post_id, '_include_in_reports', 'yes' );
					update_post_meta( $post_id, '_is_paid',            'yes' );

				break;

				case 'cancelled':

					update_post_meta( $post_id, '_icon',    'wcicon-status-cancelled' );
					update_post_meta( $post_id, '_is_paid', 'no' );

				break;

				case 'refunded':

					update_post_meta( $post_id, '_icon',               'wcicon-status-refunded' );
					update_post_meta( $post_id, '_include_in_reports', 'yes' );
					update_post_meta( $post_id, '_is_paid',            'no' );

				break;

				case 'failed':

					update_post_meta( $post_id, '_icon',    'wcicon-status-failed' );
					update_post_meta( $post_id, '_is_paid', 'needs_payment' );

				break;

				case 'pending-deposit':

					update_post_meta( $post_id, '_icon',   'wcicon-status-pending' );
					update_post_meta( $post_id, '_is_paid', 'needs_payment' );

				break;

			}
		}

		return $post_id;
	}


	/**
	 * Returns the badge color for a core order status.
	 *
	 * @since 1.9.0
	 *
	 * @param string $status the status slug
	 * @return string the badge color for the status
	 */
	public function get_core_order_status_color( $status ) {

		switch ( $status ) {

			case 'pending':
				return '#c6e1c6';
			case 'processing':
				return '#c6e1c6';
			case 'on-hold':
				return '#f8dda7';
			case 'completed':
				return '#c8d7e1';
			case 'cancelled':
				return '#e5e5e5';
			case 'refunded':
				return '#e5e5e5';
			case 'failed':
				return '#eba3a3';
			default:
				return '#e5e5e5';
		}
	}


	/**
	 * Add custom order statuses to WooCommerce order statuses
	 *
	 * @since 1.0.0
	 * @param array $order_statuses
	 * @return array
	 */
	public function order_statuses( array $order_statuses ) {

		$filtered_statuses = array();

		foreach ( $this->get_order_status_posts() as $status ) {

			$filtered_statuses[ 'wc-' . $status->post_name ] = $status->post_title;
		}

		// to catch any status for which a post hasn't been created we need to merge,
		// however we need to be careful that:
		// 1. the sort order of the statuses must match in dropdowns
		// 2. any customization of core statuses names must be retained in dropdowns
		return $filtered_statuses + $order_statuses;
	}


	/**
	 * Add custom order paid statuses to WooCommerce paid order statuses
	 *
	 * @since 1.4.3
	 * @return array
	 */
	public function order_paid_statuses() {

		$custom_paid_statuses = array();
		$custom_statuses      = $this->get_order_status_posts();

		foreach ( $custom_statuses as $order_status_post ) {

			$custom_status = new WC_Order_Status_Manager_Order_Status( $order_status_post );

			if ( $custom_status->is_paid() ) {
				$custom_paid_statuses[] = $custom_status->get_slug();
			}
		}

		return $custom_paid_statuses;
	}


	/**
	 * Add custom order paid statuses to WooCommerce needs payment order statuses
	 *
	 * @since 1.6.0
	 * @return array
	 */
	public function order_needs_payment_statuses() {

		$custom_needs_payment_statuses = array();
		$custom_statuses               = $this->get_order_status_posts();

		foreach ( $custom_statuses as $order_status_post ) {

			$custom_status = new WC_Order_Status_Manager_Order_Status( $order_status_post );

			if ( $custom_status->needs_payment() ) {
				$custom_needs_payment_statuses[] = $custom_status->get_slug();
			}
		}

		return $custom_needs_payment_statuses;
	}


	/**
	 * Filter order status valid for payment complete
	 *
	 * @see \WC_Order::payment_complete()
	 *
	 * @since 1.6.2
	 * @param array $valid_order_statuses An array of status slugs
	 * @return array
	 */
	public function order_statuses_valid_for_payment_complete( $valid_order_statuses = array() ) {

		$custom_statuses = $this->get_order_status_posts();

		foreach ( $custom_statuses as $custom_status_post ) {

			$custom_status = new WC_Order_Status_Manager_Order_Status( $custom_status_post );

			if ( ! $custom_status->is_paid() ) {

				$valid_order_statuses[] = $custom_status->get_slug();

			} else {

				// core statuses marked as paid which are in the default WC array
				// should then be removed from the valid order statuses array
				if ( $custom_status->is_core_status() && in_array( $custom_status->get_slug(), $valid_order_statuses, true ) ) {

					unset( $valid_order_statuses[ $custom_status->get_slug() ] );
				}
			}
		}

		return array_unique( $valid_order_statuses );
	}


	/**
	 * Grant download permissions on custom paid order statuses
	 *
	 * @since 1.6.1
	 *
	 * @param int $order_id WC_Order id
	 * @param string $old_order_status Old order status
	 * @param string $new_order_status New order status
	 */
	public function regenerate_download_permissions( $order_id, $old_order_status, $new_order_status ) {
		global $wpdb;

		$paid_order_statuses = $this->order_paid_statuses();

		if ( ! empty( $paid_order_statuses ) && in_array( $new_order_status, $paid_order_statuses, true ) ) {

			delete_post_meta( $order_id, '_download_permissions_granted' );

			$wpdb->delete(
				$wpdb->prefix . 'woocommerce_downloadable_product_permissions',
				array( 'order_id' => $order_id ),
				array( '%d' )
			);

			wc_downloadable_product_permissions( $order_id );
		}
	}


	/**
	 * Disallow editing order items if the status has been marked as paid
	 *
	 * @since 1.3.0
	 * @param bool $maybe_editable
	 * @param \WC_Order $order
	 * @return bool
	 */
	public function is_order_editable( $maybe_editable, $order ) {

		$order_status = new WC_Order_Status_Manager_Order_Status( $order->get_status() );

		if ( ! $order_status->is_core_status() ) {
			return false === $order_status->is_paid();
		}

		return $maybe_editable;
	}


	/**
	 * Allow showing purchase notes for statuses marked as paid
	 *
	 * @since 1.3.0
	 * @param array $statuses
	 * @return array
	 */
	public function show_purchase_note( $statuses ) {

		$custom_statuses             = $this->get_order_status_posts();
		$custom_statuses_marked_paid = array();

		foreach ( $custom_statuses as $order_status_post ) {

			$custom_status = new WC_Order_Status_Manager_Order_Status( $order_status_post );

			if ( ! $custom_status->is_core_status() ) {
				$custom_statuses_marked_paid[] = $custom_status->is_paid();
			}
		}

		return array_merge( $statuses, $custom_statuses_marked_paid );
	}


	/**
	 * Truncate the order status slug to a maximum of 17 characters
	 * Remove any leading numbers as these are also used as CSS classes
	 *
	 * @since 1.1.1
	 * @param string $slug          The post slug.
	 * @param int    $post_ID       Post ID.
	 * @param string $post_status   The post status.
	 * @param string $post_type     Post type.
	 * @return string $slug
	 */
	public function truncate_order_status_slug( $slug, $post_ID, $post_status, $post_type ) {

		$max_slug_length = 17;

		if ( 'wc_order_status' !== $post_type ) {
			return $slug;
		}

		// trim leading digits
		$trimmed_slug = ltrim( $slug, '0..9' );

		if ( strlen( $trimmed_slug ) > 0 ) {

			// do not trim if this would make the slug blank (name only contains numbers)
			$slug = $trimmed_slug;
		}

		if ( strlen( $slug ) <= $max_slug_length ) {
			return $slug;
		}

		$slug = _truncate_post_slug( $slug, $max_slug_length );

		// ensure we only have valid characters before checking if it's unique
		$slug = sanitize_html_class( $slug, 'custom-status' );

		// The following was borrowed from WP core function wp_unique_post_slug()
		global $wpdb;

		// Post slugs must be unique across all posts.
		$check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = %s AND ID != %d LIMIT 1";
		$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $post_type, $post_ID ) );

		if ( $post_name_check ) {
			$suffix = 2;
			do {
				$alt_post_name = _truncate_post_slug( $slug, $max_slug_length - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
				$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name, $post_type, $post_ID ) );
				$suffix++;
			} while ( $post_name_check );
			$slug = $alt_post_name;
		}

		return $slug;
	}


	/**
	 * Queue orders to be updated when status slug is changed
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @param array $data
	 */
	public function queue_orders_update( $post_id, $data ) {

		// Skip if doing autosave
		if ( defined( 'DOING_AUTOSAVE' ) ) {
			return;
		}

		// Sanity check
		if ( ! $post_id || ! isset( $data['post_name'] ) || ! $data['post_name'] ) {
			return;
		}

		// Bail out if not an order status
		if ( 'wc_order_status' !== get_post_type( $post_id ) ) {
			return;
		}

		$order_status = new WC_Order_Status_Manager_Order_Status( $post_id );

		// Bail out if order status does not exist
		if ( ! $order_status->get_id() ) {
			return;
		}

		// If the slug has changed, queue orders to be updated
		if ( $data['post_name'] !== $order_status->get_slug() ) {

			$this->_previous_order_status_slug = $order_status->get_slug();

			add_action( 'save_post_wc_order_status', array( $this, 'handle_slug_change' ), 10, 2 );
		}
	}


	/**
	 * Handle order status slug change
	 *
	 * This function will find any orders with the previous slug
	 * and update them with the new slug. It also updates the slug
	 * in any "next statuses".
	 *
	 * @since 1.0.0
	 * @param int $post_id the order status post id
	 * @param \WP_Post $post the order status post object
	 */
	public function handle_slug_change( $post_id, \WP_Post $post ) {

		// Check if the previous slug was stored
		if ( ! $this->_previous_order_status_slug ) {
			return;
		}

		$order_status = new WC_Order_Status_Manager_Order_Status( $post_id );

		global $wpdb;

		$wpdb->query( $wpdb->prepare( "
			UPDATE {$wpdb->posts}
			SET post_status = %s
			WHERE post_type = 'shop_order'
			AND post_status = %s
		", $order_status->get_slug( true ), 'wc-' . $this->_previous_order_status_slug ) );

		// If any other order statuses have specified this status
		// as a 'next status', update the slug in their meta, so that
		// the 'next status' keeps functioning
		$rows = $wpdb->get_results( $wpdb->prepare( "
			SELECT pm.post_id
			FROM {$wpdb->postmeta} pm
			RIGHT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE post_type = 'wc_order_status'
			AND meta_key = '_next_statuses'
			AND meta_value LIKE %s
		", '%' . $wpdb->esc_like( $this->_previous_order_status_slug ) . '%' ) );

		if ( $rows ) {
			foreach ( $rows as $row ) {

				$next_statuses = get_post_meta( $row->post_id, '_next_statuses', true );

				// Update the next status slug
				if ( ( $key = array_search( $this->_previous_order_status_slug, $next_statuses ) ) !== false ) {
					$next_statuses[ $key ] = $order_status->get_slug();
				}

				update_post_meta( $row->post_id, '_next_statuses', $next_statuses );
			}
		}
	}


	/**
	 * Handle deleting an order status
	 *
	 * Will assign all orders that have the to-be deleted status
	 * a replacement status, which defaults to `wc-on-hold`.
	 * Also removes the status form any next statuses.
	 *
	 * @since 1.0.0
	 * @param int $post_id the order status post id
	 * @param string $replacement_status the new status to assign (optional, default: on hold)
	 */
	public function handle_order_status_delete( $post_id, $replacement_status = 'on-hold' ) {

		global $wpdb;

		// Bail out if not an order status or not published
		if ( 'wc_order_status' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
			return;
		}

		$order_status = new WC_Order_Status_Manager_Order_Status( $post_id );

		if ( ! $order_status->get_id() ) {
			return;
		}

		/**
		 * Filter the replacement status when an order status is deleted
		 *
		 * This filter is applied just before the order status is deleted,
		 * but after the order status meta has already been deleted.
		 *
		 * @since 1.0.0
		 * @param string $replacement Replacement order status slug.
		 * @param string $original Original order status slug.
		 */
		$replacement_status = apply_filters( 'wc_order_status_manager_deleted_status_replacement', $replacement_status, $order_status->get_slug() );
		$replacement_status = str_replace( 'wc-', '', $replacement_status );

		$new_status = new WC_Order_Status_Manager_Order_Status( $replacement_status );

		$new_status_name = $new_status->get_name();
		$old_status_name = $order_status->get_name();

		$order_rows = $wpdb->get_results( $wpdb->prepare( "
			SELECT ID FROM {$wpdb->posts}
			WHERE post_type = 'shop_order' AND post_status = %s
		", $order_status->get_slug( true ) ), ARRAY_A );

		$num_updated = 0;

		if ( ! empty( $order_rows ) ) {

			add_action( 'woocommerce_email', [ $this, 'disable_email_notifications' ] );

			foreach ( $order_rows as $order_row ) {

				$order = wc_get_order( $order_row['ID'] );
				/* translators: Order status updated from %1$s = old status to %2$s = new status */
				$order->update_status( $replacement_status, sprintf( __( 'Order status updated from %1$s to %2$s because the former status was deleted.', 'woocommerce-order-status-manager' ), $old_status_name, $new_status_name ) );

				$num_updated++;
			}
		}

		// If any other order statuses have specified this status
		// as a 'next status', remove it from there
		$rows = $wpdb->get_results( $wpdb->prepare( "
			SELECT pm.post_id
			FROM {$wpdb->postmeta} pm
			RIGHT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE post_type = 'wc_order_status'
			AND meta_key = '_next_statuses'
			AND meta_value LIKE %s
		", '%' . $wpdb->esc_like( $order_status->get_slug() ) . '%' ) );

		if ( $rows ) {
			foreach ( $rows as $row ) {

				$next_statuses = get_post_meta( $row->post_id, '_next_statuses', true );

				// Remove the next status slug
				if ( ( $key = array_search( $order_status->get_slug(), $next_statuses ) ) !== false ) {
					unset( $next_statuses[ $key ] );
				}

				update_post_meta( $row->post_id, '_next_statuses', $next_statuses );
			}
		}

		// Add admin notice
		if ( $num_updated && is_admin() && ! is_ajax() ) {

			/* translators: Placeholders: %d is the number of orders changed, %1$s is the old order status, %2$s is the new order status  */
			$message = sprintf( _n(
					'%d order that was previously %1$s is now %2$s.',
					'%d orders that were previously %1$s are now %2$s.',
					$num_updated,
					'woocommerce-order-status-manager'
				), $num_updated, esc_html( $old_status_name ), esc_html( $new_status_name ) );

			wc_order_status_manager()->get_message_handler()->add_message( $message );
		}
	}


	/**
	 * Add custom statuses to order reports
	 *
	 * @since 1.1.0
	 * @param array $report_statuses
	 * @return array $report_statuses
	 */
	public function reports_order_statuses( $report_statuses ) {

		// don't alter the order statuses if it's not an array or if 'refunded' is the only status
		if ( ! is_array( $report_statuses ) || ( 1 === count( $report_statuses ) && 'refunded' === $report_statuses[0] ) ) {
			return $report_statuses;
		}

		$status_posts = $this->get_order_status_posts( array( 'fields' => 'ids' ) );

		foreach ( $status_posts as $post_id ) {

			$status = new WC_Order_Status_Manager_Order_Status( $post_id );

			if ( $status->include_in_reports() ) {

				$report_statuses[] = $status->get_slug();

			} else {

				if ( ( $key = array_search( $status->get_slug(), $report_statuses ) ) !== false ) {
					unset( $report_statuses[ $key ] );
				}
			}
		}

		// ensure report statuses are unique
		return array_unique( $report_statuses );
	}


	/**
	 * Ensure orders with custom statuses are included in partial refund report calculations
	 *
	 * @since 1.1.0
	 * @param array $args
	 * @return array $args
	 */
	public function order_report_data_args( $args ) {

		// don't alter the order statuses if it's not an array or if 'refunded' is the only status
		if ( ! isset( $args['parent_order_status'] ) || ! is_array( $args['parent_order_status'] ) || ( 1 === count( $args['parent_order_status'] ) && 'refunded' === $args['parent_order_status'][0] ) ) {
			return $args;
		}

		$status_posts = $this->get_order_status_posts( array( 'fields' => 'ids' ) );

		foreach ( $status_posts as $post_id ) {

			$status = new WC_Order_Status_Manager_Order_Status( $post_id );

			if ( $status->include_in_reports() ) {

				// We don't want to modify the partial refund query to include the "refunded" status, lest we end up doubling refund amounts
				// This code is really only intended to ensure that our custom statuses are checked when searching for refunded line items,
				// so this condition ensures that we skip the partial refund query to only modify the refunded line item query {BR 2017-08-01}
				if ( 'refunded' === $status->get_slug() && isset( $args['data']['_refund_amount'] ) && ! in_array( 'refunded', $args['parent_order_status'], true ) ) {
					continue;
				}

				$args['parent_order_status'][] = $status->get_slug();

			} else {

				if ( ( $key = array_search( $status->get_slug(), $args['parent_order_status'] ) ) !== false ) {
					unset( $args['parent_order_status'][ $key ] );
				}
			}
		}

		// ensure parent order statuses are unique
		$args['parent_order_status'] = array_unique( $args['parent_order_status'] );

		return $args;
	}


	/**
	 * Remove Order Status Manager actions from Order actions
	 *
	 * @see WC_Order_Status_Manager_Admin_Orders::custom_order_actions()
	 *
	 * @since 1.4.3
	 *
	 * @param array $order_actions
	 * @return array
	 */
	public function trim_order_actions( $order_actions ) {

		if ( $order_statuses = $this->get_order_status_posts() ) {

			foreach ( $order_statuses as $post ) {

				if ( $status = new WC_Order_Status_Manager_Order_Status( $post ) ) {

					$slug = $status->get_slug();

					if ( isset( $order_statuses[ $slug ] ) ) {
						unset( $order_actions[ $slug ] );
					} elseif ( 'completed' === $slug ) {
						unset( $order_actions['complete'] );
					}
				}
			}
		}

		return $order_actions;
	}


	/**
	 * Returns a list of customized order actions.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Order $order the order instance
	 * @return array an array of customized order actions
	 */
	public function get_custom_order_actions( $order ) {

		$custom_actions = array();
		$status         = new WC_Order_Status_Manager_Order_Status( $order->get_status() );

		// sanity check: bail if status is not found
		// this can happen if some statuses are registered late
		if ( ! $status || ! $status->get_id() ) {
			return $custom_actions;
		}

		$next_statuses  = $status->get_next_statuses();

		if ( ! empty( $next_statuses ) ) {

			$order_statuses = wc_get_order_statuses();

			// add next statuses as actions
			foreach ( $next_statuses as $next_status ) {

				if ( isset( $order_statuses[ 'wc-' . $next_status ] ) ) {

					$custom_actions[ $next_status ] = array(
						'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $next_status . '&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
						'name'   => $order_statuses[ 'wc-' . $next_status ],
						'action' => $next_status,
					);
				}
			}
		}

		return $custom_actions;
	}


	/**
	 * Removes WooCommerce email actions to prevent notifications on manual order status updates.
	 *
	 * @internal
	 *
	 * @since 1.12.1
	 *
	 * @param WC_Emails $email_class the WooCommerce emails class instance
	 */
	public function disable_email_notifications( $email_class ) {

		remove_action( 'woocommerce_order_status_completed_notification', [ $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ] );
	}


	/**
	 * Determines whether at least one custom status is being used by any order.
	 *
	 * @since 1.12.1-dev.1
	 *
	 * @return bool true if at least one custom status is being used by any order
	 */
	public function is_any_custom_status_in_use() {

		$order_with_custom_status_found = false;

		foreach ( wc_get_order_statuses() as $slug => $name ) {

			// core statuses are not considered in this method
			if ( wc_order_status_manager()->get_order_statuses_instance()->is_core_status( $slug ) ) {
				continue;
			}

			$status = new \WC_Order_Status_Manager_Order_Status( $slug );

			// at least one custom status must be associated with an order to interrupt this method iteration and return true
			if ( $existing_orders = $status->has_orders( [ 'posts_per_page' => 1 ] ) ) {
				$order_with_custom_status_found = true;
				break;
			}
		}

		return $order_with_custom_status_found;
	}


}
