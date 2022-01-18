<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class FUE_Addon_WooCommerce_Order_Importer
 */
class FUE_Addon_WooCommerce_Order_Importer {

	/**
	 * @var array
	 */
	private $email_ids  = array();

	/**
	 * @var int
	 */
	private $limit      = 100;

	/**
	 * Class constructor
	 *
	 * @param array $email_ids
	 * @param int $limit
	 */
	public function __construct( $email_ids = null, $limit = null ) {
		if ( !is_null( $email_ids ) ) {
			$this->set_email_ids( $email_ids );
		}

		if ( !is_null( $limit ) ) {
			$this->set_limit( $limit );
		}
	}

	/**
	 * Set the IDs of the emails to match the orders against
	 *
	 * @param array $email_ids
	 */
	public function set_email_ids( Array $email_ids ) {
		$this->email_ids = $email_ids;
	}

	/**
	 * Set the maximum number of orders to import per run
	 *
	 * @param int $limit
	 */
	public function set_limit( $limit ) {
		$this->limit = $limit;
	}

	/**
	 * Return only the number of orders that match the conditions of $email
	 * @param FUE_Email $email
	 *
	 * @return int
	 */
	public function count_matching_orders_for_email( $email ) {

	}

	public function get_order_ids_matching_email( $email ) {
		$order_ids  = array();

		if ( $email->type == 'storewide' ) {
			$order_ids = $this->get_order_ids_for_storewide_email( $email );
		} elseif ( $email->type == 'customer' ) {
			$order_ids = $this->get_order_ids_for_customer_email( $email );
		}

		return apply_filters( 'fue_wc_get_orders_for_email', $order_ids, $email );
	}

	public function filter_orders( $data ) {
		$email = new FUE_Email( $data['email_id'] );

	    if ( $email->status != FUE_Email::STATUS_ACTIVE ) {
		    return array( $email->id => array() );
	    }

		if ( $email->type == 'storewide' ) {
			$data['orders'] = $this->filter_storewide_orders( $data['orders'], $email );
		} elseif ( $email->type == 'customer' ) {
			$data['orders'] = $this->filter_customer_orders( $data['orders'], $email );
		}

		$filtered = array( $email->id => $data['orders'] );

		return apply_filters( 'fue_wc_filter_orders_for_email', $filtered, $email );
	}

	/**
	 * Import orders in $orders for the provided email
	 * @param int   $email_id
	 * @param array $orders
	 * @param int $limit
	 *
	 * @return array
	 */
	public function import_orders( $email_id, $orders, $limit = 100 ) {
		$import_data = array();
		$imported    = array();
		$processed   = 0;
		$successes   = 0;
		$failures    = 0;
		$email       = new FUE_Email( $email_id );

		foreach ( $orders as $idx => $order_id ) {
			// break out of the loop if the $limit has been hit
			if ( $processed >= $limit ) {
				break;
			}
			$processed++;

			// remove from $orders so this doesn't get processed again in the next run
			unset( $orders[ $idx ] );

			$order = WC_FUE_Compatibility::wc_get_order( $order_id );
			if ( ! $order ) {
				$failures++;
				$import_data[] = array(
					'id'        => $order_id,
					'status'    => 'failed',
					'reason'    => sprintf( __('Importing failed. Invalid order (#%d)', 'follow_up_emails'), $order_id )
				);
				continue;
			}

			$start_date = null;

			if ( $email->trigger == 'completed' ) {
				$start_date = $order->get_date_completed() ? gmdate( 'Y-m-d H:i:s', $order->get_date_completed()->getOffsetTimestamp()  ) : '' ;

			} elseif (
				in_array( $email->trigger, Follow_Up_Emails::instance()->fue_wc->get_order_statuses() ) ||
				$email->trigger == 'first_purchase' ||
				$email->type == 'customer'
			) {
				$start_date = WC_FUE_Compatibility::get_order_prop( $order, 'post' )->post_date;
			}

			$insert = apply_filters( 'fue_wc_import_insert', array(
				'send_on'       => $email->get_send_timestamp( $start_date ),
				'email_id'      => $email->id,
				'order_id'      => $order_id,
				'user_id'       => WC_FUE_Compatibility::get_order_prop( $order, 'customer_user' ),
				'user_email'    => WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' )
			), $email );

			if ( ! $insert ) {
				$failures++;
				$imported[] = array(
					'id'        => $order_id,
					'status'    => 'failures',
					'reason'    => sprintf( __( 'There was an error importing the orders(#%d)', 'follow_up_emails' ), $order_id )
				);
				continue;
			}

			$item_id = FUE_Sending_Scheduler::queue_email( $insert, $email );

			if ( is_wp_error( $item_id ) ) {
				$failures++;
				$imported[] = array(
					'id'        => $order_id,
					'status'    => 'failed',
					'reason'    => sprintf( __('Importing failed. %s', 'follow_up_emails'), $item_id->get_error_message() )
				);
				continue;
			}

			$_order = WC_FUE_Compatibility::wc_get_order( $insert['order_id'] );

			$email_trigger  = apply_filters( 'fue_interval_str', $email->get_trigger_string(), $email );
			$send_date      = date( get_option('date_format') .' '. get_option('time_format'), $email->get_send_timestamp() );

			if ( $_order ) {
				$note = sprintf(
					__('Email queued: %s scheduled on %s<br/>Trigger: %s', 'follow_up_emails'),
					$email->name,
					$send_date,
					$email_trigger
				);

				$_order->add_order_note( $note );

				$successes++;
				$imported[] = array(
					'id'        => $insert['order_id'],
					'status'    => 'success'
				);
			}
		}

		delete_post_meta( $email_id, '_import_order_flag' );

		return array(
			'imported'          => $imported,
			'successes'         => $successes,
			'failures'          => $failures,
			'processed'         => $processed,
			'orders'            => $orders
		);
	}

	/**
	 * Import orders that match the given $email
	 * @param FUE_Email $email
	 * @param int       $limit The maximum number of orders to import per run
	 * @return array
	 */
	public function import_orders_for_email( $email, $limit = 100 ) {
		$orders     = get_transient( 'fue_orders_for_email_'. $email->id );
		$imported   = array();

		$processed = 0;
		foreach ( $orders as $idx => $order_id ) {
			$processed++;

			// break out of the loop if the $limit has been hit
			if ( $processed > $limit ) {
				break;
			}

			// remove from $orders so this doesn't get processed again in the next run
			unset( $orders[ $idx ] );

			if ( !$order_id ) {
				$imported[] = array(
					'id'        => $order_id,
					'status'    => 'failed',
					'reason'    => sprintf( __('Importing failed. Invalid order (#%d)', 'follow_up_emails'), $order_id )
				);
				continue;
			}

			$order      = WC_FUE_Compatibility::wc_get_order( $order_id );
			$start_date = null;

			if ( $email->trigger == 'first_purchase' ) {
				$start_date = WC_FUE_Compatibility::get_order_prop( $order, 'post' )->post_date;
			}

			$insert = apply_filters( 'fue_wc_import_insert', array(
				'send_on'       => $email->get_send_timestamp( $start_date ),
				'email_id'      => $email->id,
				'order_id'      => $order_id,
				'user_id'       => WC_FUE_Compatibility::get_order_prop( $order, 'customer_user' ),
				'user_email'    => WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' )
			), $email );

			if ( $insert ) {
				$item_id = FUE_Sending_Scheduler::queue_email( $insert, $email );

				if ( is_wp_error( $item_id ) ) {
					$imported[] = array(
						'id'        => $order_id,
						'status'    => 'failed',
						'reason'    => sprintf( __('Importing failed. %s', 'follow_up_emails'), $item_id->get_error_message() )
					);
					continue;
				}

				$_order = WC_FUE_Compatibility::wc_get_order( $order_id );

				$email_trigger  = apply_filters( 'fue_interval_str', $email->get_trigger_string(), $email );
				$send_date      = date( get_option('date_format') .' '. get_option('time_format'), $email->get_send_timestamp() );

				if ( $_order ) {
					$note = sprintf(
						__('Email queued: %s scheduled on %s<br/>Trigger: %s', 'follow_up_emails'),
						$email->name,
						$send_date,
						$email_trigger
					);

					$_order->add_order_note( $note );

					$imported[] = array(
						'id'        => $order_id,
						'status'    => 'success'
					);
				} else {
					$imported[] = array(
						'id'        => $order_id,
						'status'    => 'failed',
						'reason'    => sprintf( __('Importing failed. Could not load order (#%d)', 'follow_up_emails'), $order_id )
					);
				}
			}

		}

		if ( is_array( $orders ) && count( $orders ) > 0 ) {
			// store the new $orders array
			set_transient( 'fue_orders_for_email_'. $email->id, $orders, 600 );
		} else {
			// completed. delete the transient
			delete_transient( 'fue_orders_for_email_'. $email->id );
			$orders = array();
		}

		return array(
			'imported'          => $imported,
			'remaining_orders'  => count( $orders ),
			'status'            => ( count( $orders ) > 0 ) ? 'running' : 'completed'
		);
	}

	public function count_remaining_orders( $data ) {
		$count = 0;

		foreach ( $data as $email ) {
			$count += count($email);
		}

		return $count;
	}

	/**
	 * Get the order IDs matching the trigger of the storewide email.
	 *
	 * This method only matches the trigger and does not take into account
	 * the email's product and category ids or any of the email's conditions.
	 *
	 * @param FUE_Email $email
	 * @return array
	 */
	private function get_order_ids_for_storewide_email( $email ) {
		$wpdb       = Follow_Up_Emails::instance()->wpdb;
		$trigger    = $email->trigger;
		$orders     = array();

		if ( $trigger == 'cart' ) {
			// cart is an unsupported trigger
			return $orders;
		}

		if ( in_array( $trigger, Follow_Up_Emails::instance()->fue_wc->get_order_statuses() ) ) {
			// count the number of orders matching the email's order status trigger
			// and exclude those Order IDs that are in the email queue, sent or unsent
			$status = 'wc-'. $email->trigger;
			$orders = $wpdb->get_col( $wpdb->prepare(
				"SELECT ID
					FROM {$wpdb->posts} p
					WHERE p.post_status = %s
					AND p.post_type = 'shop_order'
					AND (
						SELECT COUNT(id)
						FROM {$wpdb->prefix}followup_email_orders
						WHERE order_id = p.ID
						AND email_id = %d
					) = 0",
				$status,
				$email->id
			) );
		} elseif ( $trigger == 'first_purchase' ) {
			// get the order IDs of customers with only 1 order
			$customer_orders = $wpdb->get_col(
				"SELECT order_id
					FROM {$wpdb->prefix}followup_customer_orders
					GROUP BY followup_customer_id
					HAVING COUNT(followup_customer_id) = 1"
			);

			if ( count( $customer_orders ) > 0 ) {
				$queue_orders = $wpdb->get_col( $wpdb->prepare(
					"SELECT order_id
					FROM {$wpdb->prefix}followup_email_orders
					WHERE order_id IN (". implode( ',', array_map( 'absint', $customer_orders ) ) .")
					AND email_id = %d",
					$email->id
				) );

				// exclude orders that are already in the queue
				foreach ( $customer_orders as $customer_order ) {
					if ( !in_array( $customer_order, $queue_orders ) ) {
						$orders[] = $customer_order;
					}
				}
			}

		} elseif ( $trigger == 'product_purchase_above_one' ) {
			// Get the orders of customers with more than 1 order
			$customers = $wpdb->get_col(
				"SELECT followup_customer_id
					FROM {$wpdb->prefix}followup_customer_orders
					GROUP BY followup_customer_id
					HAVING COUNT(followup_customer_id) > 1"
			);

			foreach ( $customers as $customer_id ) {
				$customer_orders = $wpdb->get_col( $wpdb->prepare(
					"SELECT order_id
						FROM {$wpdb->prefix}followup_customer_orders
						WHERE followup_customer_id = %d
						ORDER BY order_id ASC",
					$customer_id
				) );

				if ( count( $customer_orders ) > 0 ) {
					// drop the customer's first order
					$customer_orders = array_slice( $customer_orders, 1 );

					$queue_orders = $wpdb->get_col( $wpdb->prepare(
						"SELECT order_id
						FROM {$wpdb->prefix}followup_email_orders
						WHERE order_id IN (". implode( ',', array_map( 'absint', $customer_orders ) ) .")
						AND email_id = %d",
						$email->id
					) );

					// exclude orders that are already in the queue
					foreach ( $customer_orders as $customer_order ) {
						if ( !in_array( $customer_order, $queue_orders ) ) {
							$orders[] = $customer_order;
						}
					}
				}
			}

		}

		if ( empty( $orders ) ) {
			return array();
		}

		return array( $email->id => $orders );
	}

	/**
	 * Similar to FUE_Addon_WooCommerce_Order_Importer::get_order_ids_for_storewide_email() but for customer emails
	 * @param FUE_Email $email
	 * @return array
	 */
	private function get_order_ids_for_customer_email( $email ) {
		$wpdb       = Follow_Up_Emails::instance()->wpdb;
		$trigger    = $email->trigger;
		$orders     = array();

		if ( $trigger == 'after_last_purchase' ) {
			$now            = current_time( 'timestamp', true );
			$min_diff       = FUE_Sending_Scheduler::get_time_to_add( $email->interval, $email->duration );
			$customer_ids   = $wpdb->get_col(
				"SELECT DISTINCT followup_customer_id
				FROM {$wpdb->prefix}followup_customer_orders"
			);

			foreach ( $customer_ids as $customer_id ) {
				$order_id = $wpdb->get_var($wpdb->prepare(
					"SELECT order_id
					FROM {$wpdb->prefix}followup_customer_orders
					WHERE followup_customer_id = %d
					ORDER BY order_id DESC
					LIMIT 1",
					$customer_id
				));

				$order = WC_FUE_Compatibility::wc_get_order( $order_id );

				if ( !in_array( $order->get_status(), array('on-hold', 'processing', 'completed') ) ) {
					continue;
				}

				$diff = $now - strtotime( WC_FUE_Compatibility::get_order_prop( $order, 'post' )->post_date_gmt );

				if ( $diff < $min_diff ) {
					// the customer's last order doesn't meet the minimum date difference,
					// move on to the next customer.
					continue;
				}

				$orders[] = $order_id;
			}

		} elseif ( $trigger == 'order_total_above' ) {
			$order_ids = $wpdb->get_col($wpdb->prepare(
				"SELECT order_id
				FROM {$wpdb->prefix}followup_customer_orders
				WHERE price > %d",
				$email->meta['order_total_above']
			));

			foreach ( $order_ids as $order_id ) {
				$order = WC_FUE_Compatibility::wc_get_order( $order_id );

				if ( !$order ) {
					continue;
				}

				$orders[] = $order_id;
			}
		} elseif ( $trigger == 'order_total_below' ) {
			$order_ids = $wpdb->get_col($wpdb->prepare(
				"SELECT order_id
				FROM {$wpdb->prefix}followup_customer_orders
				WHERE price < %d",
				$email->meta['order_total_below']
			));

			foreach ( $order_ids as $order_id ) {
				$order = WC_FUE_Compatibility::wc_get_order( $order_id );

				if ( !$order ) {
					continue;
				}

				$orders[] = $order_id;
			}
		} elseif ( $trigger == 'purchase_above_one' ) {
			$customer_ids = $wpdb->get_col(
				"SELECT id
				FROM {$wpdb->prefix}followup_customers
				WHERE total_orders > 1"
			);

			foreach ( $customer_ids as $customer_id ) {
				$order_id = $wpdb->get_var($wpdb->prepare(
					"SELECT order_id
					FROM {$wpdb->prefix}followup_customer_orders
					WHERE followup_customer_id = %d
					ORDER BY order_id DESC
					LIMIT 1", $customer_id
				));

				$orders[] = $order_id;
			}
		} elseif ( $trigger == 'total_orders' ) {
			$mode           = $email->meta['total_orders_mode'];
			$requirement    = $email->meta['total_orders'];

			if ( $mode == 'equal to' ) {
				$sql = "SELECT id FROM {$wpdb->prefix}followup_customers WHERE total_orders = %d";
			} else {
				$sql = "SELECT id FROM {$wpdb->prefix}followup_customers WHERE total_orders > %d";
			}

			$customer_ids = $wpdb->get_col( $wpdb->prepare( $sql, $requirement ) );

			foreach ( $customer_ids as $customer_id ) {
				$order_id = $wpdb->get_var($wpdb->prepare(
					"SELECT order_id
					FROM {$wpdb->prefix}followup_customer_orders
					WHERE followup_customer_id = %d
					ORDER BY order_id DESC
					LIMIT 1", $customer_id
				));

				$orders[] = $order_id;
			}
		} elseif ( $trigger == 'total_purchases' ) {
			$mode           = $email->meta['total_purchases_mode'];
			$requirement    = $email->meta['total_purchases'];

			if ( $mode == 'equal to' ) {
				$sql = "SELECT id FROM {$wpdb->prefix}followup_customers WHERE total_purchase_price = %d";
			} else {
				$sql = "SELECT id FROM {$wpdb->prefix}followup_customers WHERE total_purchase_price > %d";
			}

			$customer_ids = $wpdb->get_col( $wpdb->prepare( $sql, $requirement ) );

			foreach ( $customer_ids as $customer_id ) {
				$order_id = $wpdb->get_var($wpdb->prepare(
					"SELECT order_id
					FROM {$wpdb->prefix}followup_customer_orders
					WHERE followup_customer_id = %d
					ORDER BY order_id DESC
					LIMIT 1", $customer_id
				));

				$orders[] = $order_id;
			}
		}

		foreach ( $orders as $i => $order_id ) {
			$queue = $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->prefix}followup_email_orders
				WHERE order_id = %d
				AND email_id = %d",
				$order_id,
				$email->id
			) );

			if ( $queue > 0 ) {
				unset( $orders[ $i ] );
			}
		}
		$orders = array_values( $orders );

		return array( $email->id => $orders );
	}

	/**
	 * @param array $orders
	 * @param FUE_Email $email
	 * @return array
	 */
	private function filter_storewide_orders( $orders, $email ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;
		$order_ids = implode( ',', array_map( 'absint', $orders ) );

		// filter out the orders that don't match the email's product or category filter
		if ( $email->product_id ) {
			$orders = $wpdb->get_col( $wpdb->prepare(
				"SELECT order_id
				FROM {$wpdb->prefix}followup_order_items
				WHERE order_id IN ($order_ids)
				AND (
					product_id = %d
					OR
					variation_id = %d
				)", $email->product_id, $email->product_id
			) );
		}

		if ( $email->category_id ) {
			$orders = $wpdb->get_col( $wpdb->prepare(
				"SELECT order_id
				FROM {$wpdb->prefix}followup_order_categories
				WHERE order_id IN ($order_ids)
				AND category_id = %d", $email->category_id
			) );
		}

		// remove orders with products/categories that are in the email exclusions list
		if ( !empty( $email->meta['excluded_categories'] ) ) {
			$excludes = $email->meta['excluded_categories'];

			if ( !is_array( $excludes ) ) {
				$excludes = array();
			}

			foreach ( $orders as $idx => $order_id ) {
				if ( in_array( $order_id, $excludes ) ) {
					unset( $orders[ $idx ] );
				}
			}

			// reset the indices
			$orders = array_values( $orders );
		}

		if ( !empty( $email->meta['excluded_customers_products'] ) ) {
			// exclude orders from customers who have purchased any one of these products
			$product_ids = implode( ',', array_map( 'absint', $email->meta['excluded_customers_products'] ) );

			foreach ( $orders as $idx => $order_id ) {
				$order      = WC_FUE_Compatibility::wc_get_order( $order_id );
				$user_id    = WC_FUE_Compatibility::get_order_user_id( $order );

				if ( $user_id ) {
					$customer = fue_get_customer( $user_id );
				} else {
					$customer = fue_get_customer( 0, WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ) );
				}

				if ( ! $customer ) {
					continue;
				}

				$sql = "SELECT COUNT(*)
						FROM {$wpdb->prefix}followup_order_items i
						WHERE o.followup_customer_id = %d
						AND o.order_id = i.order_id
						AND (
							i.product_id IN ( $product_ids )
							OR
							i.variation_id IN ( $product_ids )
						)";
				$found = $wpdb->get_var( $wpdb->prepare( $sql, $customer->id ) );

				if ( $found > 0 ) {
					unset( $orders[ $idx ] );
				}

			}

		}

		if ( !empty( $email->meta['excluded_customers_categories'] ) ) {
			$product_ids = implode( ',', array_map( 'absint', $email->meta['excluded_customers_products'] ) );

			foreach ( $orders as $idx => $order_id ) {
				$order      = WC_FUE_Compatibility::wc_get_order( $order_id );
				$user_id    = WC_FUE_Compatibility::get_order_user_id( $order );

				if ( $user_id ) {
					$customer = fue_get_customer( $user_id );
				} else {
					$customer = fue_get_customer( 0, WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ) );
				}

				if ( ! $customer ) {
					continue;
				}

				$sql = "SELECT COUNT(*)
						FROM {$wpdb->prefix}followup_order_items i, {$wpdb->prefix}followup_customer_orders o
						WHERE o.followup_customer_id = %d
						AND o.order_id = i.order_id
						AND (
							i.product_id IN ( $product_ids )
							OR
							i.variation_id IN ( $product_ids )
						)";
				$found = $wpdb->get_var( $wpdb->prepare( $sql, $customer->id ) );

				if ( $found > 0 ) {
					unset( $orders[ $idx ] );
				}
			}

		}

		return $orders;
	}

	/**
	 * @param array $orders
	 * @param FUE_Email $email
	 * @return array
	 */
	private function filter_customer_orders( $orders, $email ) {
		return $orders;
	}

}
