<?php

/**
 * Class FUE_Addon_Woocommerce_Scheduler
 */
class FUE_Addon_Woocommerce_Scheduler {

	/**
	 * @var FUE_Addon_Woocommerce
	 */
	private $fue_wc;

	/**
	 * Class constructor
	 *
	 * @param FUE_Addon_Woocommerce $wc
	 */
	public function __construct( $wc ) {
		$this->fue_wc = $wc;

		$this->register_hooks();
	}

	/**
	 * Register hooks
	 */
	private function register_hooks() {
		// refunds
		add_action( 'woocommerce_refund_created', array( $this, 'refund_created' ) );

		// @since 2.2.1 support custom order statuses
		add_action( 'init', array( $this, 'hook_statuses' ), 100 );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'order_status_updated' ) );
		add_action( 'woocommerce_order_status_changed', array( $this, 'unqueue_status_emails' ), 10, 3 );

		// downloads
		add_action( 'woocommerce_order_status_completed', array( $this, 'queue_download_reminders' ) );
		add_action( 'woocommerce_download_product', array( $this, 'remove_queued_download_reminders' ), 10, 6 );
		add_action( 'woocommerce_download_product', array( $this, 'file_downloaded' ), 10, 6 );
		add_action( 'woocommerce_process_product_file_download_paths_grant_access_to_new_file', array( $this, 'downloadable_file_added' ), 100, 4 );

		// coupons
		add_action( 'woocommerce_new_order_item', array( $this, 'queue_coupon_emails' ), 10, 4 );

		// subscriptions
		if ( FUE_Addon_Subscriptions::is_wcs_2() ) {
			add_action( 'wcs_renewal_order_created', array( $this, 'reschedule_last_purchase_emails' ), 11, 3 );
		} else {
			add_action( 'woocommerce_subscriptions_renewal_order_created', array( $this, 'reschedule_last_purchase_emails' ), 11, 3 );
		}

		add_filter( 'fue_insert_email_order', array( $this, 'get_correct_email' ) );

		add_filter( 'fue_queue_item_filter_conditions', array( $this, 'check_item_conditions' ), 10, 2 );
		add_filter( 'fue_queue_item_filter_conditions_before_sending', array( $this, 'check_signup_conditions' ), 10, 2 );
	}

	/**
	 * Delete all unsent cart emails for the given customer
	 * @param int $customer_id
	 * @param string $user_email
	 */
	public function delete_unsent_cart_emails( $customer_id, $user_email = '' ) {

		// Do not delete cart emails if we don't have a valid customer
		if ( empty( $customer_id ) && empty( $user_email ) ) {
			return;
		}

		$args = array(
			'is_cart'   => 1,
			'is_sent'   => 0,
		);

		if ( $customer_id ) {
			$args['user_id'] = $customer_id;
		}

		if ( $user_email ) {
			$args['user_email'] = $user_email;
		}

		$cart_queue = Follow_Up_Emails::instance()->scheduler->get_items( $args );

		foreach ( $cart_queue as $queue_item ) {
			Follow_Up_Emails::instance()->scheduler->delete_item( $queue_item->id );
		}

	}

	/**
	 * Delete records from the database matching the ID of the deleted order
	 * @param int $order_id
	 */
	public static function order_deleted( $order_id ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$row = $wpdb->get_row($wpdb->prepare(
			"SELECT followup_customer_id, price
			FROM {$wpdb->prefix}followup_customer_orders
			WHERE order_id = %d",
			$order_id
		));

		if ( $row && $row->price > 0 ) {
			// deduct the price from the customer's total order amount
			$wpdb->query($wpdb->prepare(
				"UPDATE {$wpdb->prefix}followup_customers
				SET total_purchase_price = total_purchase_price - %2f,
				total_orders = total_orders - 1
				WHERE id = %d",
				$row->price,
				$row->followup_customer_id
			));
		}

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}followup_email_orders WHERE order_id = %d", $order_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}followup_customer_orders WHERE order_id = %d", $order_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}followup_email_excludes WHERE order_id = %d", $order_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}followup_email_logs WHERE order_id = %d", $order_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}followup_order_categories WHERE order_id = %d", $order_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}followup_order_items WHERE order_id = %d", $order_id ) );

	}


    /**
     * Schedule emails after a refund has been processed
     * @param int $refund_id The refund ID
     */
    public function refund_created($refund_id) {

        /**
         * @var WC_Order_Refund $refund
         */
        $refund = WC_FUE_Compatibility::wc_get_order( $refund_id );

        $triggers = array( 'refund_successful' );

        /*
         Checking if refund has been manually processed.
         */
        if ( ! $refund->get_refunded_payment() ) {
            $triggers[] = 'refund_manual';
        }


        // enqueue the emails
        $emails =  fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, array(
            'meta_query' => array(
                array(
                    'key'      => '_interval_type',
                    'value'    => $triggers,
                    'compare'  => 'IN',
                ),
            ),
        ) );

        foreach ( $emails as $email ) {
            $insert = array(
                'order_id' => $refund->get_parent_id(),
                'meta'     => array(
                    'refund_id'     => $refund_id,
                    'refund_amount' => $refund->get_amount(),
                    'refund_reason' => $refund->get_reason(),
                ),
            );
            FUE_Sending_Scheduler::queue_email( $insert, $email );
        }
    }

	/**
	 * Register order statuses to trigger follow-up emails
	 */
	public function hook_statuses() {
		$statuses = $this->fue_wc->get_order_statuses();

		foreach ( $statuses as $status ) {
			add_action( 'woocommerce_order_status_' . $status, array( $this, 'order_status_updated' ), 20 );
		}

	}

	/**
	 * When an order gets updated, queue emails that match the new status
	 *
	 * @param int $order_id
	 */
	public function order_status_updated( $order_id ) {

		$order = WC_FUE_Compatibility::wc_get_order( $order_id );

		FUE_Addon_Woocommerce::record_order( $order );

		$queued         = array();
		$triggers       = $this->get_order_triggers( $order, Follow_Up_Emails::get_email_type( 'storewide' ) );

		$product_emails = $this->get_matching_product_emails( $order, $triggers, false );
		$queued         = array_merge( $queued, $this->queue_product_emails( $product_emails, $order ) );

		$product_always_send_emails = $this->get_matching_product_emails( $order, $triggers, true );
		$queued         = array_merge( $queued, $this->queue_always_send_product_emails( $product_always_send_emails, $order ) );

		$category_emails    = $this->get_matching_category_emails( $order, $triggers, false );
		$queued             = array_merge( $queued, $this->queue_category_emails( $category_emails, $order ) );

		$category_always_send_emails    = $this->get_matching_category_emails( $order, $triggers, true );
		$queued         = array_merge( $queued, $this->queue_always_send_category_emails( $category_always_send_emails, $order ) );

		$storewide_always_send_emails   = $this->get_matching_storewide_emails( $order, $triggers, true );
		$queued         = array_merge( $queued, $this->queue_storewide_emails( $storewide_always_send_emails, $order ) );

		if ( count( $queued ) == 0 ) {
			$storewide_emails = $this->get_matching_storewide_emails( $order, $triggers );
			$queued = array_merge( $queued, $this->queue_storewide_emails( $storewide_emails, $order ) );
		}

		$order_status = WC_FUE_Compatibility::get_order_status( $order );

		if ( $order_status == 'processing' || $order_status == 'completed' ) {
			// only queue date and customer emails once per order
			if ( get_post_meta( $order_id, '_order_status_emails_queued', true ) != true ) {
				update_post_meta( $order_id, '_order_status_emails_queued', true );
				$queued = array_merge( $queued, $this->queue_date_emails( $order ) );
				$queued = array_merge( $queued, $this->queue_reminder_emails( $order ) );
				$queued = array_merge( $queued, $this->queue_customer_emails( $order ) );

			}
		}

		$this->add_order_notes_to_queued_emails( $queued );

		// remove signup emails that have the 'remove_signup_emails_on_purchase' option enabled
		$this->remove_signup_emails_on_purchase( $order );

	}

	/**
	 * Remove unsent emails with triggers matching the old order status from
	 * the queue.
	 *
	 * @since 1.0.0
	 * @version 4.5.2
	 *
	 * @param int $order_id Order ID.
	 * @param string $old_status Old order status.
	 * @param string $new_status New order status.
	 */
	public function unqueue_status_emails( $order_id, $old_status, $new_status ) {
		$order      = WC_FUE_Compatibility::wc_get_order( $order_id );
		$scheduler  = Follow_Up_Emails::instance()->scheduler;
		$filter     = array(
			'meta_query'    => array(
				array(
					'key'       => '_interval_type',
					'value'     => $old_status,
				),
			),
		);

		/**
		 * Filter the fue_get_emails' filter when the order status chnaged and returned
		 * emails will be unqueued because **Remove on status change** option
		 * is enabled.
		 *
		 * @since 4.5.2
		 * @version 4.5.2
		 *
		 * @see https://github.com/woocommerce/woocommerce-follow-up-emails/issues/344
		 *
		 * @param array  $filter     Filter for `fue_get_emails`.
		 * @param int    $order_id   Order ID.
		 * @param string $old_status Old Status.
		 * @param string $new_status New Status.
		 */
		$filter = apply_filters(
			'fue_unqueue_emails_filter_on_order_status_change',
			$filter,
			$order_id,
			$old_status,
			$new_status
		);

		$emails     = fue_get_emails( 'any', '', $filter );
		$email_ids  = array();

		foreach ( $emails as $email ) {
			if ( ! empty( $email->meta['remove_email_status_change'] ) && $email->meta['remove_email_status_change'] == 'yes' ) {
				$email_ids[] = $email->id;
			}
		}

		$queue = $scheduler->get_items( array(
			'is_sent'   => 0,
			'order_id'  => $order_id,
			'email_id'  => $email_ids,
		) );

		foreach ( $queue as $item ) {
			$email_name = get_the_title( $item->email_id );
			$order->add_order_note( sprintf( __( 'The email &quot;%s&quot; has been removed due to an order status change', 'follow_up_emails' ), $email_name ) );
			$scheduler->delete_item( $item->id );
		}
	}

	/**
	 * Schedule 'not_downloaded' emails
	 * @param $order_id
	 */
	public function queue_download_reminders( $order_id ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;
		$args = array(
			'meta_query'    => array(
				array(
					'key'       => '_interval_type',
					'value'     => 'not_downloaded',
				),
			),
		);

		$emails = fue_get_emails( 'storewide', FUE_Email::STATUS_ACTIVE, $args );

		foreach ( $emails as $email ) {
			$downloads = $wpdb->get_results( $wpdb->prepare(
				"SELECT *
				FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
				WHERE order_id = %d",
				$order_id
			));

			foreach ( $downloads as $download ) {
				if ( empty( $email->meta['downloadable_file'] ) || $email->meta['downloadable_file'] != $download->download_id ) {
					continue;
				}

				$insert = array(
					'user_email'    => $download->user_email,
					'product_id'    => $download->product_id,
					'order_id'      => $order_id,
					'meta'          => array(
						'download_id'   => $download->download_id,
					),
				);
				FUE_Sending_Scheduler::queue_email( $insert, $email, true );
			}
}
	}

	/**
	 * Remove unsent 'not_downloaded' emails matching the download_id of this downloaded file
	 *
	 * @param string $user_email
	 * @param string $order_key
	 * @param string $product_id
	 * @param string $user_id
	 * @param string $download_id
	 * @param string $order_id
	 */
	public function remove_queued_download_reminders( $user_email, $order_key, $product_id, $user_id, $download_id, $order_id ) {
		$scheduler = Follow_Up_Emails::instance()->scheduler;

		$args = array(
			'meta_query'    => array(
				'relation'  => 'AND',
				array(
					'key'       => '_interval_type',
					'value'     => 'not_downloaded',
				),
				array(
					'key'       => '_product_id',
					'value'     => $product_id,
				)
			),
		);

		$emails = fue_get_emails( 'any', '', $args );

		foreach ( $emails as $email ) {
			$items = $scheduler->get_items(array(
				'email_id'  => $email->id,
				'order_id'  => $order_id,
				'is_sent'   => 0,
			));

			foreach ( $items as $item ) {
				$scheduler->delete_item( $item->id );
			}
		}

	}

	/**
	 * Schedule emails after a downloadable product's file has been downloaded
	 * @param string $user_email
	 * @param string $order_key
	 * @param string $product_id
	 * @param string $user_id
	 * @param string $download_id
	 * @param string $order_id
	 */
	public function file_downloaded( $user_email, $order_key, $product_id, $user_id, $download_id, $order_id ) {
		$args = array(
			'meta_query'    => array(
				'relation'  => 'AND',
				array(
					'key'       => '_interval_type',
					'value'     => 'downloaded',
				),
				array(
					'key'       => '_product_id',
					'value'     => $product_id,
				)
			),
		);

		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, $args );

		foreach ( $emails as $email ) {
			if ( empty( $email->meta['downloadable_file'] ) || $email->meta['downloadable_file'] != $download_id ) {
				continue;
			}

			$insert = array(
				'user_id'       => $user_id,
				'user_email'    => $user_email,
				'product_id'    => $product_id,
				'order_id'      => $order_id,
				'meta'          => array(
					'download_id'   => $download_id,
				),
			);
			FUE_Sending_Scheduler::queue_email( $insert, $email, true );
		}
	}

	/**
	 * Queue drip emails after files have been added to a product
	 *
	 * @param bool $grant_access
	 * @param string $download_id
	 * @param int $product_id
	 * @param WC_Order $order
	 * @return bool
	 */
	public static function downloadable_file_added( $grant_access, $download_id, $product_id, $order ) {

		if ( $grant_access ) {
			$categories = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

			$emails = fue_get_emails( 'storewide', FUE_Email::STATUS_ACTIVE, array(
				'meta_query' => array(
					array(
						'key'       => '_interval_type',
						'value'     => 'downloadable_file_added',
					),
				),
			) );

			foreach ( $emails as $email ) {

				if ( $email->product_id > 0 && $email->product_id != $product_id ) {
					continue;
				} elseif ( $email->category_id > 0 && ! in_array( $email->category_id, $categories ) ) {
					continue;
				}

				$insert = array(
					'user_id'       => WC_FUE_Compatibility::get_order_prop( $order, 'customer_user' ),
					'user_email'    => WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ),
					'product_id'    => $product_id,
					'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
					'meta'          => array(
						'download_id'   => $download_id,
					),
				);
				FUE_Sending_Scheduler::queue_email( $insert, $email, true );

			}
}

		return $grant_access;
	}

	/**
	 * Schedule emails after a coupon have been used.
	 *
	 * @since 1.0.0
	 * @version 4.5.2
	 *
	 * @param int           $item_id  Order item ID.
	 * @param WC_Order_Item $item     Order item object.
	 * @param int           $order_id Order ID.
	 */
	public function queue_coupon_emails( $item_id, $item, $order_id ) {
		if ( ! is_a( $item, 'WC_Order_Item_Coupon' ) ) {
			return;
		}

		$code     = $item->get_code();
		$discount = $item->get_discount();

		$queued = array();
		$coupon = new WC_Coupon( $code );

		$exists = ( $coupon->get_id() > 0 );

		if ( ! $exists ) {
			return;
		}

		$args = array(
			'meta_query'   => array(
				'relation' => 'AND',
				array(
					'key'   => '_interval_type',
					'value' => 'coupon',
				)
			),
		);

		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, $args );
		$order  = WC_FUE_Compatibility::wc_get_order( $order_id );

		foreach ( $emails as $email ) {
			if ( ! isset( $email->meta['coupon'] ) ) {
				continue;
			}

			if ( ! empty( $email->meta['coupon'] ) && $email->meta['coupon'] != $coupon->get_id() ) {
				continue;
			}

			$insert = array(
				'user_email' => WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ),
				'order_id'   => $order_id,
				'meta'       => array(
					'coupon_code'     => $code,
					'discount_amount' => $discount,
				),
			);

			if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email, true ) ) ) {
				$queued[] = $insert;
			}
		}

		if ( ! empty( $queued ) ) {
			self::add_order_notes_to_queued_emails( $queued );
		}
	}

	/**
	 * Adjust last_purchase emails after a renewal order has been created
	 *
	 * @param WC_Order $renewal_order
	 * @param WC_Order $original_order
	 * @return WC_Order
	 */
	public function reschedule_last_purchase_emails( $renewal_order, $original_order ) {
		$this->queue_customer_last_purchased_emails( $renewal_order );

		return $renewal_order;
	}

	/**
	 * Add cart emails to the queue
	 * @param array $cart
	 * @param int $user_id
	 * @param string $user_email
	 * @param int $added_product
	 */
	public function queue_cart_emails( $cart, $user_id = 0, $user_email = '', $added_product = null ) {
		$cart_emails    = array();
		$always_prods   = array();
		$always_cats    = array();
		$email_created  = false;

		$cart_session = FUE_Addon_Woocommerce_Cart::get_user_cart_session( $user_id );

		if ( ! $user_email ) {
			$user_email = '';
		}

		$customer = fue_get_customer( $user_id, $user_email );

		$search_params = array(
			'is_sent'       => 0,
			'order_id'      => 0,
			'product_id'    => 0,
			'email_id'      => 0,
			'user_id'       => $user_id,
			'is_cart'       => 1,
		);

		if ( ! empty( $user_email ) ) {
			$search_params['user_email'] = $user_email;
		}

		// Processing cart emails that target specific product and/or product
		// category. Emails without product set won't be processed in this loop.
		//
		// TODO: Move this out into its own method for readability.
		foreach ( $cart as $item_key => $item ) {
			// Look for cart emails matching the current cart item.
			$emails = $this->get_cart_emails( FUE_Email::STATUS_ACTIVE, array(
				'product_id' => ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'],
			) );

			if ( count( $emails ) == 0 ) {
				continue;
			}

			$email = current( $emails );

			if ( $email ) {
				$args = $search_params;
				$args['product_id'] = $item['product_id'];
				$args['email_id']   = $email->id;
				$queue_check = Follow_Up_Emails::instance()->scheduler->get_items( $args );

				if ( count( $queue_check ) > 0 ) {
					$email_created = true;
				} elseif ( ! in_array( $email->id . '_' . $item['product_id'], $cart_session ) ) {

					if ( $this->is_product_or_category_excluded( $item['product_id'], null, null, $email ) ) {
						continue;
					}

					if ( $this->exclude_customer_based_on_purchase_history( $customer, $email ) ) {
						continue;
					}

					$cart_session[] = $email->id . '_' . $item['product_id'];
					$cart_emails[]  = array(
						'id'        => $email->id,
						'item'      => $item['product_id'],
						'priority'  => $email->priority,
					);
				}
			}

			// always_send product matches
			$emails = $this->get_cart_emails( FUE_Email::STATUS_ACTIVE, array(
				'product_id' => $item['product_id'],
				'always_send'   => 1,
			) );

			foreach ( $emails as $email ) {
				$args = $search_params;
				$args['product_id'] = $item['product_id'];
				$args['email_id']   = $email->id;
				$check = Follow_Up_Emails::instance()->scheduler->get_items( $args );

				if ( count( $check ) > 0 ) {
					$email_created = true;
				} elseif ( ! in_array( $email->id . '_' . $item['product_id'], $cart_session ) ) {
					if ( $this->is_product_or_category_excluded( $item['product_id'], null, null, $email ) ) {
						continue;
					}

					if ( $this->exclude_customer_based_on_purchase_history( $customer, $email ) ) {
						continue;
					}

					$cart_session[] = $email->id . '_' . $item['product_id'];
					$always_prods[] = array(
						'id'    => $email->id,
						'item'  => $item['product_id'],
					);
				}
			}

			// always_send category matches
			$cat_ids  = wp_get_object_terms( $item['product_id'], 'product_cat', array( 'fields' => 'ids' ) );

			$emails = $this->get_cart_emails( FUE_Email::STATUS_ACTIVE, array(
				'always_send'   => 1,
				'category_id'   => $cat_ids,
			) );

			foreach ( $emails as $email ) {
				$args = $search_params;
				$args['product_id'] = $item['product_id'];
				$args['email_id']   = $email->id;
				$check = Follow_Up_Emails::instance()->scheduler->get_items( $args );

				if ( count( $check ) > 0 ) {
					$email_created = true;
				} elseif ( ! in_array( $email->id . '_' . $item['product_id'], $cart_session ) ) {
					if ( $this->is_product_or_category_excluded( $item['product_id'], null, null, $email ) ) {
						continue;
					}

					if ( $this->exclude_customer_based_on_purchase_history( $customer, $email ) ) {
						continue;
					}

					$cart_session[] = $email->id . '_' . $item['product_id'];
					$always_cats[] = array(
						'id'    => $email->id,
						'item'  => $item['product_id'],
					);
				}
			}
		}

		if ( ! empty( $always_prods ) ) {
			foreach ( $always_prods as $row ) {
				$email = new FUE_Email( $row['id'] );

				$insert = array(
					'product_id' => $row['item'],
					'is_cart'   => 1,
					'user_id'   => $user_id,
					'user_email' => $user_email,
				);

				if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$email_created = true;
				}
			}
		}

		if ( ! empty( $always_cats ) ) {
			foreach ( $always_cats as $row ) {
				$email = new FUE_Email( $row['id'] );

				$insert = array(
					'product_id' => $row['item'],
					'is_cart'   => 1,
					'user_id'   => $user_id,
					'user_email' => $user_email,
				);

				if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$email_created = true;
				}
			}
		}

		// Product matches.
		$email_created = $this->process_cart_emails_by_priority( $cart_emails, array(
			'user_id'    => $user_id,
			'user_email' => $user_email,
		) );

		// Find a category match.
		if ( ! $email_created ) {
			$emails = array();
			foreach ( $cart as $item_key => $item ) {
				$cat_ids = wp_get_object_terms( $item['product_id'], 'product_cat', array( 'fields' => 'ids' ) );
				if ( empty( $cat_ids ) ) {
					continue;
				}

				if ( apply_filters( 'fue_storewide_category_trigger_parent_emails', true ) ) {
					foreach ( $cat_ids as $id ) {
						$parent_categories = get_ancestors( $id, 'product_cat' );
						$cat_ids = array_merge( $cat_ids, $parent_categories );
					}
				}

				$rows = $this->get_cart_emails( FUE_Email::STATUS_ACTIVE, array(
					'category_id' => $cat_ids,
				) );

				foreach ( $rows as $email ) {
					$args = $search_params;
					$args['product_id'] = $item['product_id'];
					$args['email_id']   = $email->id;
					$check = Follow_Up_Emails::instance()->scheduler->get_items( $args );

					if ( count( $check ) == 0 && ! in_array( $email->id . '_' . $item['product_id'], $cart_session ) ) {
						if ( $this->is_product_or_category_excluded( 0, $cat_ids, null, $email ) ) {
							continue;
						}

						if ( $this->exclude_customer_based_on_purchase_history( $customer, $email ) ) {
							continue;
						}

						$cart_session[] = $email->id . '_' . $item['product_id'];
						$emails[] = array( 'id' => $email->id, 'item' => $item['product_id'], 'priority' => $email->priority );
					}
				}
			}

			$email_created = $this->process_cart_emails_by_priority( $emails, array(
				'user_id'    => $user_id,
				'user_email' => $user_email,
			) );
		}

		if ( ! $email_created ) {
			// Find a storewide mailer.
			$emails = $this->get_cart_emails( FUE_Email::STATUS_ACTIVE, array(
				'product_id'    => 0,
				'category_id'   => 0,
			) );

			foreach ( $emails as $email ) {
				$args             = $search_params;
				$args['email_id'] = $email->id;
				$check            = Follow_Up_Emails::instance()->scheduler->get_items( $args );

				if ( count( $check ) > 0 || in_array( $email->id . '_0', $cart_session ) ) {
					continue;
				}

				if ( $added_product ) {
					if ( $this->is_product_or_category_excluded( $added_product, null, null, $email ) ) {
						continue;
					}
				} else {
					// If no $added_product is specified, make sure there are no
					// excluded products in the cart.
					foreach ( $cart as $item ) {
						if ( $this->is_product_or_category_excluded( $item['product_id'], null, null, $email ) ) {
							continue 2;
						}
					}
				}

				if ( $this->exclude_customer_based_on_purchase_history( $customer, $email ) ) {
					continue;
				}

				$cart_session[] = $email->id . '_0';

				$insert = array(
					'is_cart'    => 1,
					'product_id' => ( $added_product ) ? $added_product : 0,
					'user_id'    => $user_id,
					'user_email' => $user_email,
				);

				FUE_Sending_Scheduler::queue_email( $insert, $email );
			}
		}

		FUE_Addon_Woocommerce_Cart::set_user_cart_session( $user_id, $cart_session );
	}

	/**
	 * Process a list of cart emails w.r.t. priority.
	 *
	 * @param array $cart_emails Emails to process
	 * @param array $args        Args to apply to processed emails in DB
	 * @return bool Email is created
	 */
	protected function process_cart_emails_by_priority( $cart_emails = array(), $args = array() ) {
		// Sort by priority.
		$sorted_cart_emails = array();
		foreach ( $cart_emails as $id => $data ) {
			$sorted_cart_emails[ $data['priority'] ][ $id ] = $data;
		}

		ksort( $sorted_cart_emails );

		$email_created = false;

		foreach ( $sorted_cart_emails as $priority => $emails ) {
			foreach ( $emails as $data ) {
				$email = new FUE_Email( $data['id'] );

				$insert = wp_parse_args( array(
					'product_id' => $data['item'],
					'is_cart'    => 1,
				), $args );

				if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$email_created = true;
				}
			}
		}

		return $email_created;
	}

	/**
	 * Queue product emails that match the $order's status
	 * @param array $emails Array of FUE_Emails to queue
	 * @param WC_Order $order
	 * @return array Array of emails added to the queue
	 */
	protected function queue_product_emails( $emails, $order ) {
		$queued     = array();

		if ( empty( $emails ) ) {
			return $queued;
		}

		foreach ( $emails as $email ) {
			$meta = array();

			if ( isset( $email->meta['downloadable_file'] ) ) {
				$meta['download_id'] = $email->meta['downloadable_file'];
			}

			$insert = array(
				'send_on'       => $email->get_send_timestamp(),
				'email_id'      => $email->id,
				'product_id'    => $email->product_id,
				'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
				'meta'          => $meta,
			);

			if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
				$queued[] = $insert;
			}
		}

		return $queued;
	}

	/**
	 * Queue always_send product emails that match the $order's status
	 * @param array $emails Array of FUE_Emails to queue
	 * @param WC_Order $order
	 * @return array Array of emails added to the queue
	 */
	protected function queue_always_send_product_emails( $emails, $order ) {
		$queued = array();

		foreach ( $emails as $email ) {
			$skip = apply_filters( 'fue_create_order_always_send', false, $email, $order );

			if ( ! $skip ) {

				$insert = array(
					'send_on'       => $email->get_send_timestamp(),
					'email_id'      => $email->id,
					'product_id'    => $email->product_id,
					'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
				);
				if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}
			}
		}

		return $queued;

	}

	/**
	 * Queue category emails that match the $order's status
	 * @param array $emails Array of FUE_Emails to queue
	 * @param WC_Order $order
	 * @return array Array of emails added to the queue
	 */
	protected function queue_category_emails( $emails, $order ) {
		$queued = array();

		foreach ( $emails as $email ) {
			$insert = array(
				'send_on'    => $email->get_send_timestamp(),
				'email_id'   => $email->id,
				'product_id' => $email->product_id,
				'order_id'   => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
			);

			if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
				$queued[] = $insert;
			}
		}

		return $queued;
	}

	/**
	 * Queue always_send category emails that match the $order's status
	 * @param array $emails Array of FUE_Emails to queue
	 * @param WC_Order $order
	 * @return array Array of emails added to the queue
	 */
	protected function queue_always_send_category_emails( $emails, $order ) {
		$queued = array();

		foreach ( $emails as $email ) {
			$interval   = (int) $email->interval;

			$skip = apply_filters( 'fue_create_order_always_send', false, $email, $order );

			if ( ! $skip ) {

				$insert = array(
					'send_on'       => $email->get_send_timestamp(),
					'email_id'      => $email->id,
					'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
					'product_id'    => $email->product_id,
				);
				if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}
			}
		}

		return $queued;
	}

	/**
	 * Add storewide emails to the queue
	 *
	 * @param array     $emails
	 * @param WC_Order  $order
	 * @return array
	 */
	protected function queue_storewide_emails( $emails, $order ) {
		$queued = array();

		foreach ( $emails as $email ) {
			$insert = array(
				'send_on'       => $email->get_send_timestamp(),
				'email_id'      => $email->id,
				'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
			);
			if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
				$queued[] = $insert;
			}
}

		return $queued;

	}

	/**
	 * Queue customer emails
	 * @param WC_Order $order
	 * @return array
	 */
	protected function queue_customer_emails( $order ) {
		$wpdb       = Follow_Up_Emails::instance()->wpdb;
		$queued     = array();
		$user_id    = WC_FUE_Compatibility::get_order_user_id( $order );

		if ( $user_id > 0 ) {
			$fue_customer = fue_get_customer( $user_id );

			if ( ! $fue_customer ) {
				FUE_Addon_Woocommerce::record_order( $order );
				$fue_customer = fue_get_customer( $user_id );
			}

			$fue_customer_id    = $fue_customer->id;
		} else {
			$fue_customer       = fue_get_customer( 0, WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ) );
			$fue_customer_id    = $fue_customer->id;
		}

		if ( $fue_customer_id ) {
			/**
			 * Look for and queue first_purchase and product_purchase_above_one emails
			 * for the 'storewide' email type
			 */
			$product_ids = $this->get_product_ids_from_order( $order );

			foreach ( $product_ids as $product_id ) {

				// number of time this customer have purchased the current item
				$num_product_purchases = $this->fue_wc->count_customer_purchases( $fue_customer_id, $product_id['product_id'] );

				if ( $num_product_purchases == 1 ) {
					// First Purchase emails
					$queued = array_merge( $queued, $this->queue_first_purchase_emails( $product_id, 0, $order ) );
				} elseif ( $num_product_purchases > 1 ) {
					// Purchase Above One emails
					$queued = array_merge( $queued, $this->queue_purchase_above_one_emails( $product_id['product_id'], 0, $order ) );
				}

				// category match
				$cat_ids = wp_get_post_terms( $product_id['product_id'], 'product_cat', array( 'fields' => 'ids' ) );

				if ( $cat_ids ) {
					foreach ( $cat_ids as $cat_id ) {

						$num_category_purchases = $this->fue_wc->count_customer_purchases( $fue_customer_id, 0, $cat_id );

						if ( $num_category_purchases == 1 ) {
							// first time purchasing from this category
							$queued = array_merge( $queued, $this->queue_first_purchase_emails( 0, $cat_id, $order ) );

						} elseif ( $num_category_purchases > 1 ) {
							// purchased from this category more than once
							$queued = array_merge( $queued, $this->queue_purchase_above_one_emails( 0, $cat_id, $order ) );
						}
}
				}
				// end category match
			}

			if ( count( $queued ) == 0 ) {
				// storewide first purchase
				$num_storewide_purchases = $this->fue_wc->count_customer_purchases( $fue_customer_id );

				if ( $num_storewide_purchases == 1 ) {
					// first time ordering
					$queued = array_merge( $queued, $this->queue_first_purchase_emails( 0, 0, $order ) );
				} else {
					$queued = array_merge( $queued, $this->queue_purchase_above_one_emails( 0, 0, $order ) );
				}
			}
}

		// look for customer emails
		// check for order_total
		$triggers = array( 'order_total_above', 'order_total_below', 'total_orders', 'total_purchases' );
		$emails = fue_get_emails( 'customer', FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				'relation'  => 'AND',
				array(
					'key'       => '_interval_type',
					'value'     => $triggers,
					'compare'   => 'IN',
				)
			),
		) );

		foreach ( $emails as $email ) {
			if ( ! $this->customer_email_matches_order( $email, $order ) ) {
				continue;
			}

			$insert = array(
				'send_on'       => $email->get_send_timestamp(),
				'email_id'      => $email->id,
				'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
			);
			if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
				$queued[] = $insert;
			}
		}

		// special trigger: last purchased
		$queued = array_merge( $queued, $this->queue_customer_last_purchased_emails( $order ) );

		return $queued;
	}

	/**
	 * Run the $email through several checks and return true if it passes the conditional validation
	 *
	 * @param FUE_Email $email
	 * @param WC_Order  $order
	 * @return bool
	 */
	protected function customer_email_matches_order( $email, $order ) {
		$wpdb       = Follow_Up_Emails::instance()->wpdb;
		$meta       = maybe_unserialize( $email->meta );

		// check for order total triggers first and
		// filter out emails that doesn't match the trigger conditions
		if ( $email->trigger == 'order_total_above' ) {

			if (
				! isset( $meta['order_total_above'] ) ||
				WC_FUE_Compatibility::get_order_prop( $order, 'order_total' ) < $meta['order_total_above']
			) {
				return false;
			}
} elseif ( $email->trigger == 'order_total_below' ) {

			if (
				! isset( $meta['order_total_below'] ) ||
				WC_FUE_Compatibility::get_order_prop( $order, 'order_total' ) > $meta['order_total_below']
			) {
				return false;
					}
} elseif ( $email->trigger == 'total_orders' ) {
			$mode           = $meta['total_orders_mode'];
			$requirement    = $meta['total_orders'];

			if ( isset( $meta['one_time'] ) && $meta['one_time'] == 'yes' ) {
				// get the correct email address
				if ( WC_FUE_Compatibility::get_order_user_id( $order ) > 0 ) {
					$user = new WP_User( WC_FUE_Compatibility::get_order_user_id( $order ) );
					$user_email = $user->user_email;
				} else {
					$user_email = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
				}

				$search = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*)
					FROM {$wpdb->prefix}followup_email_orders
					WHERE email_id = %d
					AND user_email = %s",
					$email->id,
					$user_email
				) );

				if ( $search > 0 ) {
					return false;
				}
					}

			// get user's total number of orders
			$customer   = fue_get_customer( WC_FUE_Compatibility::get_order_user_id( $order ), WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ) );
			$num_orders = 0;

			if ( $customer ) {
				$num_orders = $customer->total_orders;
					}

			if ( $mode == 'less than' && $num_orders >= $requirement ) {
				return false;
					} elseif ( $mode == 'equal to' && $num_orders != $requirement ) {
				return false;
					} elseif ( $mode == 'greater than' && $num_orders <= $requirement ) {
				return false;
					}
		} elseif ( $email->trigger == 'total_purchases' ) {
			$mode           = $meta['total_purchases_mode'];
			$requirement    = $meta['total_purchases'];

			if ( isset( $meta['one_time'] ) && $meta['one_time'] == 'yes' ) {
				// get the correct email address
				if ( WC_FUE_Compatibility::get_order_user_id( $order ) > 0 ) {
					$user = new WP_User( WC_FUE_Compatibility::get_order_user_id( $order ) );
					$user_email = $user->user_email;
				} else {
					$user_email = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
				}

				$search = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*)
							FROM {$wpdb->prefix}followup_email_orders
							WHERE email_id = %d
							AND user_email = %s",
					$email->id,
					$user_email
				) );

				if ( $search > 0 ) {
					return false;
				}
			}

			// get user's total amount of purchases
			if ( WC_FUE_Compatibility::get_order_user_id( $order ) > 0 ) {
				$purchases = $wpdb->get_var( $wpdb->prepare( "SELECT total_purchase_price FROM {$wpdb->prefix}followup_customers WHERE user_id = %d", WC_FUE_Compatibility::get_order_user_id( $order ) ) );
			} else {
				$purchases = $wpdb->get_var( $wpdb->prepare( "SELECT total_purchase_price FROM {$wpdb->prefix}followup_customers WHERE email_address = %s", WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ) ) );
			}

			if ( $mode == 'less than' && $purchases >= $requirement ) {
				return false;
			} elseif ( $mode == 'equal to' && $purchases != $requirement ) {
				return false;
			} elseif ( $mode == 'greater than' && $purchases <= $requirement ) {
				return false;
			}
		} elseif ( $email->interval_type == 'purchase_above_one' ) {
			// look for duplicate emails
			if ( WC_FUE_Compatibility::get_order_user_id( $order ) > 0 ) {
				$wp_user = new WP_User( WC_FUE_Compatibility::get_order_user_id( $order ) );
				$user_email = $wp_user->user_email;
			} else {
				$user_email = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
			}

			$num = $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->prefix}followup_email_orders
				WHERE email_id = %d
				AND user_email = %s",
				$email->id,
				$user_email
			) );

			if ( $num > 0 ) {
				return false;
			}
		}

		return true;

	}

	/**
	 * Get and queue emails matching the product and category
	 *
	 * @param array $product_id Potentially a combination of parent product ID and variation ID.
	 * @param int $category_id
	 * @param WC_Order $order
	 * @return array
	 */
	protected function queue_first_purchase_emails( $product_id, $category_id, $order ) {
		$queued = array();

		$args = array(
			'meta_query' => array(
				'relation'  => 'AND',
				array(
					'key'   => '_interval_type',
					'value' => 'first_purchase',
				)
			),
		);

		if ( $category_id == 0 ) {
			$args['meta_query'][] = array(
				'key'   => '_category_id',
				'value' => array( '', '0' ),
				'compare' => 'IN',
			);
		} else {
			$args['meta_query'][] = array(
				'key'   => '_category_id',
				'value' => $category_id,
			);
		}

		if ( $product_id == 0 ) {
			$args['meta_query'][] = array(
				'key'   => '_product_id',
				'value' => array( '', '0' ),
				'compare' => 'IN',
			);
		} else {
			if ( ! empty( $product_id['variation_id'] ) ) {
				$args['meta_query'][] = array(
					'relation'  => 'OR',
					array(
						'key'   => '_product_id',
						'value' => $product_id['product_id'],
					),
					array(
						'key'   => '_product_id',
						'value' => $product_id['variation_id'],
					),
				);
			} else {
				$args['meta_query'][] = array(
					'key'   => '_product_id',
					'value' => $product_id['product_id'],
				);
			}
		}

		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, $args );

		if ( $emails ) {
			foreach ( $emails as $email ) {
				$queue_items = Follow_Up_Emails::instance()->scheduler->get_items( array(
					'order_id'  => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
					'email_id'  => $email->id,
				) );

				// only queue reminders once per order and email
				if ( count( $queue_items ) > 0 ) {
					continue;
				}

				// first time purchasing this item
				$insert = array(
					'send_on'       => $email->get_send_timestamp(),
					'email_id'      => $email->id,
					'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
				);

				if ( $product_id ) {
					$insert['product_id'] = $product_id;
				}

				if ( $product_id ) {
					$categories = wp_get_object_terms( $product_id, 'product_cat' );
				} elseif ( $category_id ) {
					$categories = array( $category_id );
				} else {
					$categories = $this->get_category_ids_from_order( $order );
				}

				if ( ! empty( $categories ) ) {
					// excluded categories
					$meta = maybe_unserialize( $email->meta );
					$excludes = (isset( $meta['excluded_categories'] )) ? $meta['excluded_categories'] : array();

					if ( ! is_array( $excludes ) ) {
						$excludes = array();
					}

					if ( count( $excludes ) > 0 ) {
						foreach ( $categories as $category ) {
							if ( in_array( $category, $excludes ) ) {
								continue 2;
							}
						}
					}
}

				if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}
			}
		}

		return $queued;
	}

	/**
	 * Get and queue emails that have the 'purchase_above_one' trigger
	 *
	 * @param int $product_id
	 * @param int $category_id
	 * @param WC_Order $order
	 * @return array
	 */
	protected function queue_purchase_above_one_emails( $product_id, $category_id, $order ) {
		$queued = array();

		$args = array(
			'meta_query' => array(
				'relation'  => 'AND',
				array(
					'key'       => '_interval_type',
					'value'     => array( 'product_purchase_above_one', 'purchase_above_one' ),
					'compare'   => 'IN',
				)
			),
		);

		$args['meta_query'][] = array(
			'key'   => '_category_id',
			'value' => $category_id,
		);

		$args['meta_query'][] = array(
			'key'   => '_product_id',
			'value' => $product_id,
		);

		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, $args );

		if ( $emails ) {
			foreach ( $emails as $email ) {
				$queue_items = Follow_Up_Emails::instance()->scheduler->get_items( array(
					'order_id'  => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
					'email_id'  => $email->id,
				) );

				// only queue reminders once per order and email
				if ( count( $queue_items ) > 0 ) {
					continue;
				}

				$insert = array(
					'send_on'       => $email->get_send_timestamp(),
					'email_id'      => $email->id,
					'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
				);

				if ( $product_id ) {
					$insert['product_id'] = $product_id;
				}

				if ( $product_id ) {
					$categories = wp_get_object_terms( $product_id, 'product_cat' );
				} elseif ( $category_id ) {
					$categories = array( $category_id );
				} else {
					$categories = $this->get_category_ids_from_order( $order );
				}

				if ( ! empty( $categories ) ) {
					// excluded categories
					$meta = maybe_unserialize( $email->meta );
					$excludes = (isset( $meta['excluded_categories'] )) ? $meta['excluded_categories'] : array();

					if ( ! is_array( $excludes ) ) {
						$excludes = array();
					}

					if ( count( $excludes ) > 0 ) {
						foreach ( $categories as $category ) {
							if ( in_array( $category, $excludes ) ) {
								continue 2;
							}
						}
					}
}

				if ( $this->exclude_customer_based_on_purchase_history( fue_get_customer_from_order( $order ), $email ) ) {
					continue;
				}

				if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}
			}
		}

		return $queued;
	}

	/**
	 * Get all matching product emails against the provided $order and $triggers and sort by priority
	 *
	 * @param WC_Order  $order
	 * @param array     $triggers
	 * @param bool      $always_send
	 * @return array    Array of matched FUE_Email
	 */
	protected function get_matching_product_emails( $order, $triggers, $always_send = false ) {
		$item_ids       = $this->get_product_ids_from_order( $order );
		$product_ids    = array();
		$variation_ids  = array();

		foreach ( $item_ids as $item_id ) {
			$product_ids[] = $item_id['product_id'];

			if ( $item_id['variation_id'] ) {
				$variation_ids[] = $item_id['variation_id'];
			}
		}

		$product_ids    = array_unique( $product_ids );
		$variation_ids  = array_unique( $variation_ids );

		// product match
		$always_send_value = ( $always_send ) ? array( 1 ) : array( 0, '' );
		$args = array(
			'meta_query'    => array(
				'relation'  => 'AND',
				array(
					'key'       => '_interval_type',
					'value'     => $triggers,
					'compare'   => 'IN',
				),
				array(
					'key'       => '_product_id',
					'value'     => 0,
					'compare'   => '!=',
				),
				array(
					'key'       => '_product_id',
					'value'     => array_merge( $product_ids, $variation_ids ),
					'compare'   => 'IN',
				),
				array(
					'key'       => '_always_send',
					'value'     => $always_send_value,
					'compare'   => 'IN',
				)
			),
		);

		$product_emails = fue_get_emails( 'storewide', FUE_Email::STATUS_ACTIVE, $args );

		// Loop through the product matches and queue the top result
		$matched_product_emails = array();
		foreach ( $product_emails as $email ) {

			$meta               = maybe_unserialize( $email->meta );
			$include_variations = isset( $meta['include_variations'] ) && $meta['include_variations'] == 'yes';

			if ( $this->exclude_customer_based_on_purchase_history( fue_get_customer_from_order( $order ), $email ) ) {
				continue;
			}

			// exact product match
			if ( in_array( $email->product_id, $product_ids ) || in_array( $email->product_id, $variation_ids ) ) {
				$matched_product_emails[] = $email;
			} elseif ( $include_variations && in_array( $email->product_id, $variation_ids ) ) {
				$matched_product_emails[] = $email;
			}
}

		return $matched_product_emails;
	}

	/**
	 * Get all matching category emails against the provided $order and $triggers and sort by priority
	 *
	 * @param WC_Order  $order
	 * @param array     $triggers
	 * @param bool      $always_send
	 * @return array    Array of matched FUE_Email
	 */
	protected function get_matching_category_emails( $order, $triggers, $always_send = false ) {
		$matching_emails    = array();
		$category_ids       = $this->get_category_ids_from_order( $order );

		if ( empty( $category_ids ) ) {
			return $matching_emails;
		}

		$always_send_value = ( $always_send ) ? array( 1 ) : array( 0, '' );
		$args = array(
			'meta_query'    => array(
				'relation'  => 'AND',
				array(
					'key'       => '_interval_type',
					'value'     => $triggers,
					'compare'   => 'IN',
				),
				array(
					'key'       => '_category_id',
					'value'     => 0,
					'compare'   => '!=',
				),
				array(
					'key'       => '_category_id',
					'value'     => $category_ids,
					'compare'   => 'IN',
				),
				array(
					'key'       => '_always_send',
					'value'     => $always_send_value,
					'compare'   => 'IN',
				)
			),
		);

		$category_emails = fue_get_emails( 'storewide', FUE_Email::STATUS_ACTIVE, $args );

		foreach ( $category_emails as $email ) {

			if ( $this->exclude_customer_based_on_purchase_history( fue_get_customer_from_order( $order ), $email ) ) {
				continue;
			}

			$matching_emails[] = $email;

		}

		return $matching_emails;
	}

	/**
	 * Add to queue all 'last purchased' emails
	 *
	 * @param WC_Order $order
	 * @return array
	 */
	protected function queue_customer_last_purchased_emails( $order ) {
		$wpdb           = Follow_Up_Emails::instance()->wpdb;
		$scheduler      = Follow_Up_Emails::instance()->scheduler;
		$order_status   = WC_FUE_Compatibility::get_order_status( $order );
		$queued         = array();

		// If the order is a renewal order, switch to the parent order
		if ( $this->is_subscription_renewal_order( $order ) ) {
			$order = WC_FUE_Compatibility::wc_get_order( WC_FUE_Compatibility::get_order_prop( $order, 'post' )->post_parent );
		}

		if ( $order && ( $order_status == 'processing' || $order_status == 'completed' ) ) {
			$order_user_id = WC_FUE_Compatibility::get_order_user_id( $order );
			$recipient = ($order_user_id > 0) ? $order_user_id : WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );

			// if there are any "last purchased" emails, automatically add this order to the queue
			$emails = fue_get_emails( 'customer', FUE_Email::STATUS_ACTIVE, array(
				'meta_query' => array(
					array(
						'key'   => '_interval_type',
						'value' => 'after_last_purchase',
					),
				),
			) );

			foreach ( $emails as $email ) {
				// only schedule 'last_purchase' emails once per order
				$order_emails = $scheduler->get_items( array(
					'email_id'  => $email->id,
					'order_id'  => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
				) );

				if ( count( $order_emails ) > 0 ) {
					continue;
				}

				// look for unsent emails in the queue with the same email ID
				$queued_emails = $scheduler->get_items( array(
					'is_sent'   => 0,
					'email_id'  => $email->id,
				) );

				// loop through the queue and delete unsent entries with identical customers
				foreach ( $queued_emails as $queue ) {
					if ( $queue->user_id > 0 && $order_user_id > 0 && $queue->user_id == $order_user_id ) {
						$scheduler->delete_item( $queue->id );
					} elseif ( $order_user_id > 0 && $queue->order_id > 0 ) {
						$queue_order_id = get_post_meta( $queue->order_id, '_customer_user', true );

						if ( $queue_order_id == $order_user_id ) {
							$scheduler->delete_item( $queue->id );
						}
					} else {
						// try to match the email address
						$email_address = get_post_meta( $queue->order_id, '_billing_email', true );

						if ( $email_address == WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ) ) {
							$scheduler->delete_item( $queue->id );
						}
					}
				}

				// add this email to the queue
				$insert = array(
					'send_on'       => $email->get_send_timestamp(),
					'email_id'      => $email->id,
					'product_id'    => 0,
					'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
				);
				if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}
			}
		}

		return $queued;
	}

	/**
	 * Check if the given order is a renewal order
	 * @param WC_Order $order
	 * @return bool
	 */
	protected function is_subscription_renewal_order( $order ) {
		if ( WC_FUE_Compatibility::get_order_prop( $order, 'post' )->post_parent > 0 && $order->original_order == WC_FUE_Compatibility::get_order_prop( $order, 'post' )->post_parent ) {
			return true;
		}

		return false;
	}

	/**
	 * Get all matching storewide emails against the provided $order and $triggers and sort by priority
	 *
	 * @param WC_Order  $order
	 * @param array     $triggers
	 * @param bool      $always_send
	 * @return array    Array of matched FUE_Email
	 */
	protected function get_matching_storewide_emails( $order, $triggers, $always_send = false ) {

		$matched_emails = array();
		$category_ids   = $this->get_category_ids_from_order( $order );

		$emails = fue_get_emails( 'storewide', FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				'relation'  => 'AND',
				array(
					'key'       => '_interval_type',
					'value'     => $triggers,
					'compare'   => 'IN',
				),
				array(
					'key'       => '_product_id',
					'value'     => 0,
				),
				array(
					'key'       => '_category_id',
					'value'     => 0,
				)
			),
		) );

		foreach ( $emails as $email ) {
			// excluded categories
			$meta = maybe_unserialize( $email->meta );
			$excludes = (isset( $meta['excluded_categories'] )) ? $meta['excluded_categories'] : array();

			if ( ! is_array( $excludes ) ) {
				$excludes = array();
			}

			if ( count( $excludes ) > 0 ) {
				foreach ( $category_ids as $cat_id ) {
					if ( in_array( $cat_id, $excludes ) )
						continue 2;
				}
			}

			if ( $this->exclude_customer_based_on_purchase_history( fue_get_customer_from_order( $order ), $email ) ) {
				continue;
			}

			$matched_emails[] = $email;

		}

		return $matched_emails;
	}

	/**
	 * Queue date-based emails
	 * @param WC_Order $order
	 * @return array
	 */
	public function queue_date_emails( $order ) {
		$queued     = array();
		$triggers   = $this->get_order_triggers( $order );

		$item_ids       = $this->get_product_ids_from_order( $order );
		$product_ids    = array();
		$variation_ids  = array();

		foreach ( $item_ids as $item_id ) {
			$product_ids[] = $item_id['product_id'];

			if ( $item_id['variation_id'] ) {
				$variation_ids[] = $item_id['variation_id'];
			}
		}

		$product_ids    = array_unique( $product_ids );
		$variation_ids  = array_unique( $variation_ids );

		// product match
		$args = array(
			'meta_query'    => array(
				'relation'  => 'AND',
				array(
					'key'       => '_interval_type',
					'value'     => 'date',
				),
				array(
					'key'       => '_product_id',
					'value'     => 0,
					'compare'   => '!=',
				),
				array(
					'key'       => '_product_id',
					'value'     => array_merge( $product_ids, $variation_ids ),
					'compare'   => 'IN',
				)
			),
		);

		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, $args );

		foreach ( $emails as $email ) {
			// skip date emails that have passed
			if ( FUE_Sending_Scheduler::send_date_passed( $email->id ) ) {
				continue;
			}

			$insert = array(
				'send_on'       => $email->get_send_timestamp(),
				'email_id'      => $email->id,
				'product_id'    => $email->product_id,
				'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
			);
			if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
				$queued[] = $insert;
			}
}

		return $queued;
	}

	/**
	 * Queue reminder emails
	 *
	 * @param WC_Order $order
	 * @return array
	 */
	public function queue_reminder_emails( $order ) {
		$queued         = array();
		$item_ids       = $this->get_product_ids_from_order( $order );
		$triggers       = $this->get_order_triggers( $order, Follow_Up_Emails::get_email_type( 'reminder' ) );
		$product_ids    = array();
		$variation_ids  = array();

		foreach ( $item_ids as $item_id ) {
			$product_ids[] = $item_id['product_id'];

			if ( $item_id['variation_id'] ) {
				$variation_ids[] = $item_id['variation_id'];
			}
		}

		$product_ids    = array_merge( array_unique( $product_ids ), array_unique( $variation_ids ) );

		$args = array(
			'meta_query'    => array(
				'relation'  => 'AND',
				array(
					'key'       => '_interval_type',
					'value'     => $triggers,
					'compare'   => 'IN',
				)
			),
		);

		$emails = fue_get_emails( 'reminder', FUE_Email::STATUS_ACTIVE, $args );

		foreach ( $emails as $email ) {

			if ( $email->product_id > 0 && ! in_array( $email->product_id, $product_ids ) ) {
				// Product ID does not match
				continue;
			}

			$queue_items = Follow_Up_Emails::instance()->scheduler->get_items( array(
				'order_id'  => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
				'email_id'  => $email->id,
			) );

			// only queue reminders once per order and email
			if ( count( $queue_items ) == 0 ) {
				$interval           = $email->interval;
				$interval_duration  = $email->interval_duration;

				// get the item's quantity
				$qty            = 0;
				$num_products   = false;

				foreach ( $order->get_items() as $item ) {
					$variation_id   = $item['variation_id'];
					$item_id        = $item['product_id'];

					if ( $email->product_id == 0 || ( $item_id == $email->product_id || $variation_id == $email->product_id ) ) {
						$qty = $item['qty'];

						if ( isset( $item['item_meta'] ) && ! empty( $item['item_meta'] ) ) {
							foreach ( $item['item_meta'] as $meta_key => $meta_value ) {

								if ( $meta_key == 'Filters/Case' ) {
									$num_products = $meta_value[0];
									break;
								}
}
						}
}
				}

				// look for a lifespan product variable
				$lifespan = get_post_meta( $email->product_id, 'filter_lifespan', true );

				if ( $lifespan && $lifespan > 0 ) {
					$interval = (int) $lifespan;
					$interval_duration = 'months';
				}

				if ( $num_products !== false && $num_products > 0 ) {
					$qty = $qty * $num_products;
				}

				if ( $qty == 1 ) {
					// only send the first email
					$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $interval_duration );
					$send_on    = current_time( 'timestamp' ) + $add;

					$insert = array(
						'send_on'       => $send_on,
						'email_id'      => $email->id,
						'product_id'    => $email->product_id,
						'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
					);
					if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
						$queued[] = $insert;
					}
				} elseif ( $qty == 2 ) {
					// only send the first and last emails
					$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $interval_duration );
					$send_on    = current_time( 'timestamp' ) + $add;

					$insert = array(
						'send_on'       => $send_on,
						'email_id'      => $email->id,
						'product_id'    => $email->product_id,
						'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
					);
					if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
						$queued[] = $insert;
					}

					$last       = FUE_Sending_Scheduler::get_time_to_add( $interval, $interval_duration );
					$send_on    = current_time( 'timestamp' ) + $add + $last;

					$insert = array(
						'send_on'       => $send_on,
						'email_id'      => $email->id,
						'product_id'    => $email->product_id,
						'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
					);
					if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
						$queued[] = $insert;
					}
				} else {
					// send all emails
					$add    = FUE_Sending_Scheduler::get_time_to_add( $interval, $interval_duration );
					$last   = 0;
					for ( $x = 1; $x <= $qty; $x++ ) {
						$send_on    = current_time( 'timestamp' ) + $add + $last;
						$last       += $add;

						$insert = array(
							'send_on'       => $send_on,
							'email_id'      => $email->id,
							'product_id'    => $email->product_id,
							'order_id'      => WC_FUE_Compatibility::get_order_prop( $order, 'id' ),
						);
						if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
							$queued[] = $insert;
						}
					}
				}
}
}

		return $queued;

	}

	/**
	 * Check if the email must be skipped from sending to a customer based on the
	 * customer's purchase history
	 *
	 * @param Object    $fue_customer Use fue_get_customer() or fue_get_customer_from_order() to get the customer object
	 * @param FUE_Email $email
	 * @return bool
	 */
	public function exclude_customer_based_on_purchase_history( $fue_customer, $email ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;
		$skip = false;
		$meta = maybe_unserialize( $email->meta );

		if ( ! $fue_customer ) {
			return false;
		}

		if ( ! empty( $meta['excluded_customers_products'] ) ) {
			if ( ! is_array( $meta['excluded_customers_products'] ) ) {
				$meta['excluded_customers_products'] = array( $meta['excluded_customers_products'] );
			}

			$product_ids = implode( ',', array_map( 'absint', $meta['excluded_customers_products'] ) );
			$sql = "SELECT COUNT(*)
					FROM {$wpdb->prefix}followup_order_items i, {$wpdb->prefix}followup_customer_orders o
					WHERE o.followup_customer_id = %d
					AND o.order_id = i.order_id
					AND (
						i.product_id IN ( $product_ids )
						OR
						i.variation_id IN ( $product_ids )
					)";
			$found = $wpdb->get_var( $wpdb->prepare( $sql, $fue_customer->id ) );

			if ( $found > 0 ) {
				$skip = true;
			}
		}

		if ( ! $skip && ! empty( $meta['excluded_customers_categories'] ) ) {
			if ( ! is_array( $meta['excluded_customers_categories'] ) ) {
				$meta['excluded_customers_categories'] = array( $meta['excluded_customers_categories'] );
			}

			$category_ids = implode( ',', array_map( 'absint', $meta['excluded_customers_categories'] ) );
			$found = 0;

			if ( ! empty( $category_ids ) ) {
				$sql = "SELECT COUNT(*)
					FROM {$wpdb->prefix}followup_order_categories c, {$wpdb->prefix}followup_customer_orders o
					WHERE o.followup_customer_id = %d
					AND o.order_id = c.order_id
					AND c.category_id IN ( $category_ids )";
				$found = $wpdb->get_var( $wpdb->prepare( $sql, $fue_customer->id ) );
			}

			if ( $found > 0 ) {
				$skip = true;
			}
		}

		return apply_filters( 'fue_exclude_customer_on_purchase_history', $skip, $fue_customer, $email );
	}

	/**
	 * @param int       $product_id
	 * @param array     $category_ids
	 * @param WC_Order  $order
	 * @param FUE_Email $email
	 * @return bool
	 */
	public function is_product_or_category_excluded( $product_id, $category_ids, $order, $email ) {
		$excluded = false;

		if ( $product_id ) {
			$categories = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
		} elseif ( $category_ids ) {
			$categories = $category_ids;
		} else {
			$categories = $this->get_category_ids_from_order( $order );
		}

		if ( ! empty( $categories ) ) {
			// excluded categories
			$meta       = maybe_unserialize( $email->meta );
			$excludes   = (isset( $meta['excluded_categories'] )) ? $meta['excluded_categories'] : array();

			if ( ! is_array( $excludes ) || empty( $excludes ) ) {
				return $excluded;
			}

			foreach ( $categories as $category ) {
				if ( in_array( $category, $excludes ) ) {
					$excluded = true;
					break;
				}
			}
		}

		return $excluded;
	}

	/**
	 * Look for orders that match the email trigger and other conditions
	 * @param FUE_Email $email
	 * @return array
	 */
	protected function get_matching_orders_for_email( $email ) {
		$wpdb       = Follow_Up_Emails::instance()->wpdb;
		$trigger    = $email->trigger;
		$orders     = array();

		if ( $email->type == 'storewide' ) {
			// $orders = $this->get_matching_storewide_orders( $email );
		} elseif ( $email->type == 'customer' ) {

		}

		$orders = apply_filters( 'fue_get_matching_orders_for_email', $orders, $email );

		return $orders;
	}

	/**
	 * Override the default email address to use the order's billing address
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function get_correct_email( $data ) {
		if ( ! empty( $data['order_id'] ) ) {
			$order = WC_FUE_Compatibility::wc_get_order( $data['order_id'] );
			$data['user_email'] = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
		}

		return $data;
	}

	/**
	 * Run WC-related conditions on the $item and see if it passes
	 *
	 * @param bool|WP_Error $passed
	 * @param FUE_Sending_Queue_Item $item
	 * @return bool|WP_Error
	 */
	public function check_item_conditions( $passed, $item ) {

		// only storewide for now
		if ( fue_get_email_type( $item->email_id ) != 'storewide' ) {
			return $passed;
		}

		if ( is_wp_error( $passed ) ) {
			return $passed;
		}

		$conditions         = $this->fue_wc->wc_conditions->get_store_conditions();
		$email              = new FUE_Email( $item->email_id );
		$email_conditions   = ! empty( $email->conditions ) ? $email->conditions : array();

		foreach ( $email_conditions as $email_condition ) {

			if ( array_key_exists( $email_condition['condition'], $conditions ) ) {
				// this is a WC condition
				$passed = $this->fue_wc->wc_conditions->test_store_condition( $email_condition, $item );

				if ( is_wp_error( $passed ) ) {
					// immediately return errors
					return $passed;
				}
			}
}

		return true;
	}

	/**
	 * Run signup condition tests on the $item and see if it passes
	 *
	 * @param bool|WP_Error $passed
	 * @param FUE_Sending_Queue_Item $item
	 * @return bool|WP_Error
	 */
	public function check_signup_conditions( $passed, $item ) {

		if ( fue_get_email_type( $item->email_id ) != 'signup' ) {
			return $passed;
		}

		if ( is_wp_error( $passed ) ) {
			return $passed;
		}

		$conditions         = $this->fue_wc->wc_conditions->get_signup_conditions();
		$email              = new FUE_Email( $item->email_id );
		$email_conditions   = ! empty( $email->conditions ) ? $email->conditions : array();

		foreach ( $email_conditions as $email_condition ) {

			if ( array_key_exists( $email_condition['condition'], $conditions ) ) {
				// this is a signup condition
				$passed = $this->fue_wc->wc_conditions->test_signup_condition( $email_condition, $item );

				if ( is_wp_error( $passed ) ) {
					// immediately return errors
					return $passed;
				}
			}
}

		return true;
	}

	/**
	 * If a queued email is linked to an order, add an order note that contains
	 * the email name, trigger and schedule
	 *
	 * @param array $queued
	 */
	public function add_order_notes_to_queued_emails( $queued ) {
		if ( ! is_array( $queued ) ) {
			return;
		}

		foreach ( $queued as $row ) {
			if ( isset( $row['order_id'] ) && $row['order_id'] > 0 ) {
				$_order = WC_FUE_Compatibility::wc_get_order( $row['order_id'] );
				$email  = new FUE_Email( $row['email_id'] );

				if ( empty( $row['send_on'] ) ) {
					$row['send_on'] = $email->get_send_timestamp();
				}

				$email_trigger  = apply_filters( 'fue_interval_str', $email->get_trigger_string(), $email );
				$send_date      = date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $row['send_on'] );

				$note = sprintf(
					__( 'Email queued: %1$s, scheduled on %2$s<br/>Trigger: %3$s', 'follow_up_emails' ),
					$email->name,
					$send_date,
					$email_trigger
				);

				$_order->add_order_note( $note );
			}
		}
	}

	/**
	 * Unsubscribe customers from signup emails after they have made their first purchase
	 * @param WC_Order $order
	 */
	protected function remove_signup_emails_on_purchase( $order ) {
		$user_id        = WC_FUE_Compatibility::get_order_prop( $order, 'customer_user' );
		$signup_emails  = fue_get_emails( 'signup', FUE_Email::STATUS_ACTIVE );
		$email_ids      = array();

		if ( empty( $user_id ) ) {
			return;
		}

		foreach ( $signup_emails as $signup_email ) {
			if (
				! empty( $signup_email->meta['remove_signup_emails_on_purchase'] ) &&
				$signup_email->meta['remove_signup_emails_on_purchase'] == 'yes'
			) {
				$email_ids[] = $signup_email->id;
			}
		}

		if ( ! empty( $email_ids ) ) {
			$wpdb           = Follow_Up_Emails::instance()->wpdb;
			$email_ids_csv  = implode( ',', array_map( 'absint', $email_ids ) );

			$wpdb->query( $wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}followup_email_orders
				WHERE user_id = %d
				AND email_id IN ( $email_ids_csv )",
				$user_id
			) );
		}
	}

	/**
	 * Get the triggers available for the given order based
	 * on its status and the email type
	 *
	 * @param int|WC_Order      $order
	 * @param FUE_Email_Type    $email_type
	 * @return array
	 */
	protected function get_order_triggers( $order, $email_type = null ) {
		if ( is_numeric( $order ) ) {
			$order = WC_FUE_Compatibility::wc_get_order( $order );
		}

		$order_status   = WC_FUE_Compatibility::get_order_status( $order );
		$triggers       = array( $order_status );

		$triggers = apply_filters( 'fue_order_triggers', $triggers, WC_FUE_Compatibility::get_order_prop( $order, 'id' ), $email_type );

		return $triggers;
	}

	/**
	 * Get an array of Product IDs and Variation IDs included in the given $order
	 * @param int|WC_Order  $order
	 * @return array
	 */
	protected function get_product_ids_from_order( $order ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( is_numeric( $order ) ) {
			$order = WC_FUE_Compatibility::wc_get_order( $order );
		}

		if ( 1 != get_post_meta( WC_FUE_Compatibility::get_order_prop( $order, 'id' ), '_fue_recorded', true ) ) {
			FUE_Addon_Woocommerce::record_order( $order );
		}

		$product_ids = $wpdb->get_results( $wpdb->prepare(
			"SELECT product_id, variation_id
			FROM {$wpdb->prefix}followup_order_items
			WHERE order_id = %d",
			WC_FUE_Compatibility::get_order_prop( $order, 'id' )
		), ARRAY_A );

		return $product_ids;
	}

	/**
	 * Get an array of Category IDs included in the given $order
	 * @param int|WC_Order  $order
	 * @return array
	 */
	protected function get_category_ids_from_order( $order ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( is_numeric( $order ) ) {
			$order = WC_FUE_Compatibility::wc_get_order( $order );
		}

		if ( 1 != get_post_meta( WC_FUE_Compatibility::get_order_prop( $order, 'id' ), '_fue_recorded', true ) ) {
			FUE_Addon_Woocommerce::record_order( $order );
		}

		$category_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT category_id
			FROM {$wpdb->prefix}followup_order_categories
			WHERE order_id = %d",
			WC_FUE_Compatibility::get_order_prop( $order, 'id' )
		) );

		return array_unique( $category_ids );

	}

	/**
	 * @param string    $status Filter emails by status (e.g. FUE_Email::STATUS_ACTIVE
	 * @param array     $args
	 * @return array
	 */
	protected function get_cart_emails( $status = '', $args = array() ) {
		$query = array(
			'meta_query' => array(
				array(
					'key'   => '_interval_type',
					'value' => 'cart',
				),
			),
		);

		if ( isset( $args['product_id'] ) ) {
			$query['meta_query'][] = array(
					'key'   => '_product_id',
					'value' => absint( $args['product_id'] ),
			);
		}

		if ( isset( $args['category_id'] ) ) {
			$query['meta_query'][] = array(
				'key'       => '_category_id',
				'value'     => $args['category_id'],
				'compare'   => 'IN',
			);
		}

		if ( isset( $args['always_send'] ) ) {
			$query['meta_query'][] = array(
				'key'   => '_always_send',
				'value' => $args['always_send'],
			);
		}

		// $args = array_merge( $query, $args );
		return fue_get_emails( 'any', $status, $query );
	}

}
