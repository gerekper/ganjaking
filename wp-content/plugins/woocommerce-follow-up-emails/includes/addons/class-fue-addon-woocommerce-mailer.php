<?php

/**
 * Class FUE_Addon_Woocommerce_Mailer
 */
class FUE_Addon_Woocommerce_Mailer {

	/**
	 * @var FUE_Addon_Woocommerce
	 */
	private $fue_wc;

	public function __construct( FUE_Addon_Woocommerce $fue_wc ) {
		$this->fue_wc = $fue_wc;
		$this->register_hooks();
	}

	/**
	 * Register hooks
	 */
	private function register_hooks() {
		// custom email template
		add_filter( 'fue_installed_templates', array( $this, 'register_email_template' ) );

		// manual emails
		add_filter( 'fue_manual_email_recipients', array( $this, 'get_manual_email_recipients' ), 10, 2 );

		// send email
		add_filter( 'fue_send_email_data', array( $this, 'add_to_email_data' ), 10, 3 );

		// email variable replacements
		add_action( 'fue_before_variable_replacements', array( $this, 'register_email_variable_replacements' ), 10, 4 );

		// Apply wrapping to emails with WC as the selected template
		add_filter( 'fue_before_sending_email', array( $this, 'apply_email_wrapping' ), 10, 2 );

		// Reminders
		add_filter( 'fue_email_message', array( $this, 'parse_reminder_message' ), 10, 3 );

	}

	/**
	 * Add the default WC email style to the list of installed email templates
	 *
	 * @param array $templates
	 * @return array
	 */
	public function register_email_template( $templates ) {
		array_unshift( $templates, 'WooCommerce' );

		sort( $templates );

		return $templates;
	}

	/**
	 * Load all recipients matching the provided send type
	 *
	 * @param array $recipients
	 * @param array $post
	 *
	 * @return array
	 */
	public function get_manual_email_recipients( $recipients, $post ) {
		global $wpdb;

		$send_type  = $post['send_type'];

		if ( 'users' === $send_type ) {
			// Send to all users.
			$users = get_users();

			foreach ( $users as $user ) {
				$key   = $user->ID . '|' . $user->user_email . '|' . $user->display_name;
				$value = array( $user->ID, $user->user_email, $user->display_name );

				if ( ! isset( $recipients[ $key ] ) ) {
					$recipients[ $key ] = $value;
				}
			}
		} elseif ( 'storewide' === $send_type ) {
			// Send to all customers.
			$admin_users = new WP_User_Query(
				array(
					'role'   => 'administrator1',
					'fields' => 'ID',
				)
			);

			$manager_users = new WP_User_Query(
				array(
					'role'   => 'shop_manager',
					'fields' => 'ID',
				)
			);

			$users = get_users( array(
				'exclude' => array_merge( $admin_users->get_results(), $manager_users->get_results() )
			) );

			foreach ( $users as $user ) {
				$key    = $user->ID . '|' . $user->user_email . '|' . $user->display_name;
				$value  = array( $user->ID, $user->user_email, $user->display_name );

				if ( ! isset( $recipients[$key] ) ) {
					$recipients[ $key ] = $value;
				}
			}

		} elseif ( 'customer' === $send_type ) {
			// Individual email addresses.
			if ( count( $post['recipients'] ) > 0 ) {
				if ( ! is_array( $post['recipients'] ) ) {
					$post['recipients'] = explode( ',', $post['recipients'][0] );
				}
				foreach ( $post['recipients'] as $key ) {
					$data = explode( '|', $key );

					if ( 3 === count($data) ) {
						$value = array($data[0], $data[1], $data[2]);

						if ( ! isset( $recipients[ $key ] ) ) {
							$recipients[ $key ] = $value;
						}
					}
				}
			}
		} elseif ( 'product' === $send_type || 'not_product' === $send_type ) {
			// Customers who have or have not bought the selected products.
			$found_recipients = array();
			$found_user_ids   = array();

			if ( is_array( $post['product_ids'] ) ) {

				foreach ( $post['product_ids'] as $product_id ) {
					$order_ids = $wpdb->get_results( $wpdb->prepare(
						"SELECT DISTINCT order_id
						FROM {$wpdb->prefix}followup_order_items
						WHERE product_id = %d OR variation_id = %d",
						$product_id, $product_id
					) );

					foreach ( $order_ids as $row ) {
						$order = WC_FUE_Compatibility::wc_get_order( $row->order_id );

						if ( ! $order ) {
							continue;
						}

						// Only on processing and completed orders.
						$order_status = WC_FUE_Compatibility::get_order_status( $order );

						if ( $order_status != 'processing' && $order_status != 'completed' ) {
							continue;
						}

						$order_user_id  = (WC_FUE_Compatibility::get_order_user_id( $order ) > 0)
							? WC_FUE_Compatibility::get_order_user_id( $order )
							: 0;
						$key = $order_user_id
								.'|'. WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' )
								.'|'. WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' )
								.' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );
						$value = array(
							$order_user_id,
							WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ),
							WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ) .' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' )
						);

						$found_user_ids[] = $order_user_id;

						if (! isset($found_recipients[$key]) ) {
							$found_recipients[$key] = $value;
						}
					}
				}

			} // endif: is_array($post['product_ids'])

			if ( $send_type == 'not_product' ) {
				$found_user_ids = array_unique( $found_user_ids );

				$user_query = new WP_User_Query( array( 'exclude' => $found_user_ids ) );
				$users   = $user_query->get_results();

				foreach ( $users as $user ) {
					$name   = trim( $user->billing_first_name .' '. $user->billing_last_name );

					if ( empty( $name ) ) {
						$name = $user->display_name;
					}

					$key    = $user->ID .'|'. $user->user_email .'|'. $name;
					$value  = array( $user->ID, $user->user_email, $name );
					$recipients[ $key ] = $value;
				}
			} else {
				foreach ( $found_recipients as $key => $value ) {
					if ( !isset( $recipients[ $key ] ) ) {
						$recipients[ $key ] = $value;
					}
				}
			}
		} elseif ( $send_type == 'category' ) {
			// customers who bought products from the selected categories
			if ( is_array($post['category_ids']) ) {
				foreach ( $post['category_ids'] as $category_id ) {
					$order_ids = $wpdb->get_results( $wpdb->prepare("SELECT DISTINCT order_id FROM {$wpdb->prefix}followup_order_categories WHERE category_id = %d", $category_id) );

					foreach ( $order_ids as $order_id_row ) {
						// load the order and check the status
						$order_id = $order_id_row->order_id;
						$order = WC_FUE_Compatibility::wc_get_order( $order_id );

						if ( !$order )
							continue;

						// only on processing and completed orders
						$order_status = WC_FUE_Compatibility::get_order_status( $order );
						if ( $order_status != 'processing' && $order_status != 'completed' ) continue;

						$order_user_id  = (WC_FUE_Compatibility::get_order_user_id( $order ) > 0) ? WC_FUE_Compatibility::get_order_user_id( $order ) : 0;
						$key            = $order_user_id .'|'. WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ) .'|'. WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ) .' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );
						$value          = array( $order_user_id, WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ), WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ) .' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' ) );

						if (! isset($recipients[$key]) ) {
							$recipients[$key] = $value;
						}

					} // endforeach ( $order_items_result as $order_items )
				}

			} // endif: is_array($post['product_ids'])
		} elseif ( $send_type == 'timeframe' ) {
			$from_ts    = strtotime($post['timeframe_from']);
			$to_ts      = strtotime($post['timeframe_to']);

			$from       = date('Y-m-d', $from_ts) . ' 00:00:00';
			$to         = date('Y-m-d', $to_ts) .' 23:59:59';

			$valid_statuses = array('processing', 'completed');

			$order_ids  = $wpdb->get_results( $wpdb->prepare("SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND post_date BETWEEN %s AND %s", $from, $to) );

			foreach ( $order_ids as $order ) {
				$order_id   = $order->ID;
				$order      = WC_FUE_Compatibility::wc_get_order( $order_id );

				if ( !$order )
					continue;

				if ( !in_array( $order->get_status(), $valid_statuses ) ) {
					continue;
				}

				$order_user_id  = (WC_FUE_Compatibility::get_order_user_id( $order ) > 0) ? WC_FUE_Compatibility::get_order_user_id( $order ) : 0;
				$key            = $order_user_id .'|'. WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ) .'|'. WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ) .' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );
				$value          = array( $order_user_id, WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ), WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ) .' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' ) );

				if (! isset($recipients[$key]) ) {
					$recipients[$key] = $value;
				}
			}
		}

		return $recipients;
	}

	/**
	 * Add needed data to $email_data depending on the email type
	 *
	 * @param array                     $email_data
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @param FUE_Email                 $email
	 *
	 * @return array
	 */
	public function add_to_email_data( $email_data, $queue_item, $email ) {
		global $wpdb;

		if ( $email->trigger == 'cart' && !empty( $queue_item->user_email ) ) {
			$cart = FUE_Addon_Woocommerce_Cart::get_cart( $queue_item->user_id, $queue_item->user_email );
			$email_data['email_to'] = $queue_item->user_email;

			if ( !empty( $cart['first_name'] ) ) {
				$email_data['first_name']   = $cart['first_name'];
				$email_data['cname']        = $cart['first_name'] .' '. $cart['last_name'];
			}
		} else {
			if (! $queue_item->order_id ) {
				return $email_data;
			}

			// order
			$order = WC_FUE_Compatibility::wc_get_order($queue_item->order_id);

			if ( WC_FUE_Compatibility::get_order_user_id( $order ) > 0 ) {
				$email_data['user_id']  = WC_FUE_Compatibility::get_order_user_id( $order );

				if ( $email_data['user_id'] ) {
					$wp_user = new WP_User( $email_data['user_id'] );
					$email_data['username'] = $wp_user->user_login;
				}

			}

			$email_data['email_to']     = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
			$email_data['first_name']   = WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' );
			$email_data['last_name']    = WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );
			$email_data['order_id']     = $queue_item->order_id;

			$email_data['cname'] = $email_data['first_name'] .' '. $email_data['last_name'];
		}

		return $email_data;
	}

	/**
	 * Register additional variables to be replaced
	 *
	 * @param FUE_Sending_Email_Variables $var
	 * @param array                 $email_data
	 * @param FUE_Email             $email
	 * @param object                $queue_item
	 *
	 * @return void
	 */
	public function register_email_variable_replacements( $var, $email_data, $email, $queue_item = null ) {

		if ( ! in_array( $email->type, array( 'customer', 'storewide', 'reminder', 'subscription', 'wc_bookings', 'points_and_rewards' ) ) ) {
			return;
		}

		$variables = $this->get_variables_for_email_type( $email->type );

		if ( !empty( $variables ) ) {

			// use test data if the test flag is set
			if ( isset( $email_data['test'] ) && $email_data['test'] ) {
				$variables = $this->add_test_variable_replacements( $variables, $email_data, $email );
			} else {
				$variables = $this->add_variable_replacements( $variables, $email_data, $queue_item, $email );
			}

			$var->register( $variables );
		}
	}

	/**
	 * Get an array of email variables available for the given email type
	 * @param string $email_type
	 * @return array
	 */
	private function get_variables_for_email_type( $email_type ) {
		$variables = array();

		switch ( $email_type ) {
			case 'storewide':
				$variables = array(
					'order_number'              => '',
					'order_date'                => '',
					'order_datetime'            => '',
					'order_subtotal'            => '',
					'order_tax'                 => '',
					'order_pay_method'          => '',
					'order_pay_url'             => fue_replacement_url_var( '' ),
					'order_billing_address'     => '',
					'order_shipping_address'    => '',
					'order_billing_phone'       => '',
					'order_shipping_phone'      => '',
					'dollars_spent_order'       => '',
					'download_url'              => fue_replacement_url_var( '' ),
					'download_filename'         => '',
					'item_names'                => '',
					'item_names_list'           => '',
					'item_prices'               => '',
					'item_prices_image'         => '',
					'item_codes_prices'         => '',
					'item_prices_categories'    => '',
					'item_quantities'           => '',
					'item_categories'           => '',
					'item_name'                 => '',
					'item_code'                 => '',
					'item_url'                  => fue_replacement_url_var( '' ),
					'item_category'             => '',
					'refund_amount'             => '',
					'refund_reason'             => '',
					'item_price'                => '',
					'item_quantity'             => '',
					'cart_contents'             => '',
					'cart_content_image'        => '',
					'cart_total'                => '',
					'cart_url'                  => fue_replacement_url_var( '' ),
					//'customer_first_name'       => '',
					//'customer_name'             => '',
					//'customer_email'            => '',
					//'customer_username'         => ''
				);
				break;

			case 'customer':
				$variables = array(
					'order_number'          => '',
					'order_date'            => '',
					'order_datetime'        => '',
					'amount_spent_order'    => '',
					'amount_spent_total'    => '',
					'number_orders'         => '',
					'last_purchase_date'    => '',
					'item_name'             => '',
					'item_names'            => '',
					'item_names_list'       => '',
					'item_quantities'       => '',
					'item_categories'       => '',
					'order_subtotal'        => '',
					'order_tax'             => '',
					'order_pay_method'      => '',
					'order_pay_url'         => fue_replacement_url_var( '' ),
					'order_billing_address' => '',
					'order_shipping_address'=> '',
					'order_billing_phone'   => '',
					'order_shipping_phone'  => ''
				);
				break;

			default:
				$variables = array(
					'order_number'              => '',
					'order_date'                => '',
					'order_datetime'            => '',
					'order_subtotal'            => '',
					'order_tax'                 => '',
					'order_pay_method'          => '',
					'order_pay_url'             => fue_replacement_url_var( '' ),
					'order_billing_address'     => '',
					'order_shipping_address'    => '',
					'order_billing_phone'       => '',
					'order_shipping_phone'      => '',
					'dollars_spent_order'       => '',
					'item_name'                 => '',
					'item_code'                 => '',
					'item_url'                  => fue_replacement_url_var( '' ),
					'item_category'             => '',
					'item_price'                => '',
					'item_quantity'             => '',
					'item_names'                => '',
					'item_names_list'           => '',
					'item_prices'               => '',
					'item_prices_image'         => '',
					'item_codes_prices'         => '',
					'item_prices_categories'    => '',
					'item_quantities'           => '',
					'item_categories'           => ''
				);
				break;
		}

		return $variables;
	}

	/**
	 * Scan through the keys of $variables and apply the replacement if one is found
	 * @param array     $variables
	 * @param array     $email_data
	 * @param object    $queue_item
	 * @param FUE_Email $email
	 * @return array
	 */
	private function add_variable_replacements( $variables, $email_data, $queue_item, $email ) {

		$replacements = $this->get_replacement_data( $email_data, $queue_item, $email );

		foreach ( $variables as $key => $value ) {
			if ( isset( $replacements[ $key ] ) ) {
				$variables[ $key ] = $replacements[ $key ];
			}
		}

		return $variables;

	}

	/**
	 * Add variable replacements for test emails
	 *
	 * @param array     $variables
	 * @param array     $email_data
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	private function add_test_variable_replacements( $variables, $email_data, $email ) {
		$replacements = $this->get_test_replacement_data( $email_data, $email );

		foreach ( $variables as $key => $value ) {
			if ( isset( $replacements[ $key ] ) ) {
				$variables[ $key ] = $replacements[ $key ];
			}
		}

		return $variables;
	}

	/**
	 *
	 * @param array                     $email_data
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @param FUE_Email                 $email
	 * @return array
	 */
	private function get_replacement_data( $email_data, $queue_item, $email ) {
		$replacements = array();
		$order = null;
		$item_name = '';

		if ( $queue_item->is_cart ) {
			$cart = FUE_Addon_Woocommerce_Cart::get_cart( $queue_item->user_id, $queue_item->user_email );
			$items = $cart['cart_items'];
			$replacements['cart_contents']          = FUE_Addon_Woocommerce_Cart::get_cart_table( $queue_item->user_id, $queue_item->user_email );
			$replacements['cart_total']             = FUE_Addon_Woocommerce_Cart::get_cart_total( $queue_item->user_id, $queue_item->user_email );
			$replacements['cart_url']               = fue_replacement_url_var( FUE_Sending_Mailer::create_email_url(
				$queue_item->id,
				$email->id,
				$email_data['user_id'],
				$email_data['email_to'],
				add_query_arg( 'fue_cart_redirect', 1, get_permalink( wc_get_page_id( 'cart' ) ) )
			) );
		} elseif ( $queue_item->order_id > 0 ) {
			$order  = WC_FUE_Compatibility::wc_get_order( $queue_item->order_id );
			$meta   = maybe_unserialize( $queue_item->meta );

			if ( in_array( $email->interval_type, array( 'refund_manual', 'refund_successful' ), true ) ) {
				$refund     = WC_FUE_Compatibility::wc_get_order( $meta['refund_id'] );
				$items      = array();

				// check to make sure the $refund is valid because WC deletes
				// refund rows when an API refund request fails so there's
				// a possibility that the refund_id stored here doesn't exist anymore
				if ( $refund )
					$items = $refund->get_items();
			} else {
				$items = $order->get_items();
			}

			$order      = WC_FUE_Compatibility::wc_get_order( $queue_item->order_id );
			$order_id   = $order->get_order_number();

			$replacements['order_billing_phone']    = WC_FUE_Compatibility::get_order_prop( $order, 'billing_phone' );
			$replacements['order_shipping_phone']   = (isset($order->shipping_phone)) ? $order->shipping_phone : '';
			$replacements['order_date']             = date_i18n(get_option('date_format'), strtotime(WC_FUE_Compatibility::get_order_prop( $order, 'order_date' )));
			$replacements['order_datetime']         = date_i18n(get_option('date_format') .' '. get_option('time_format'), strtotime(WC_FUE_Compatibility::get_order_prop( $order, 'order_date' )));
			$replacements['order_billing_address']  = $order->get_formatted_billing_address();
			$replacements['order_shipping_address'] = $order->get_formatted_shipping_address();
			$replacements['order_number']           = $order_id;
			$replacements['dollars_spent_order']    = FUE_Addon_Woocommerce::format_price( $order->get_total() );
			$replacements['order_subtotal']         = FUE_Addon_Woocommerce::format_price( $order->get_subtotal() );
			$replacements['order_tax']              = FUE_Addon_Woocommerce::format_price( $order->get_total_tax() );
			$replacements['order_pay_method']       = WC_FUE_Compatibility::get_order_prop( $order, 'payment_method_title' );
			$replacements['order_pay_url']          = fue_replacement_url_var( $order->get_checkout_payment_url() );

		}

		$used_cats  = array();
		$lists      = array(
			'items'         => array(),
			'categories'    => array(),
		);
		$item_list  = '<ul>';
		$item_cats  = '<ul>';
		$items_array = array();

		if ( !empty( $items ) ) {
			foreach ( $items as $item ) {
				$item_id  = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				$_product = WC_FUE_Compatibility::wc_get_product( $item_id );
				if ( ! $_product ) {
					continue;
				}

				if ( method_exists( $_product, 'get_permalink' ) ) {
					$product_permalink = $_product->get_permalink();
				} else {
					$product_permalink = get_permalink($item_id);
				}

				$item_url = FUE_Sending_Mailer::create_email_url(
					$queue_item->id,
					$email->id,
					$email_data['user_id'],
					$email_data['email_to'],
					$product_permalink
				);
				$product_name  = FUE_Addon_Woocommerce::get_product_name( $item_id );
				$items_array[] = $product_name;

				$item_categories    = array();
				$cats               = get_the_terms( $item_id, 'product_cat' );

				if ( is_array( $cats ) && !empty( $cats ) ) {
					foreach ( $cats as $cat ) {
						if ( !in_array( $cat->term_id, $used_cats ) ) {
							$lists['categories'][] = $cat->name;
						}
						$item_categories[] = $cat->name;
					}
				}

				$item_name = $_product->get_title();

				$lists['items'][] = array(
					'id'            => $item_id,
					'sku'           => $_product->get_sku(),
					'link'          => $item_url,
					'name'          => $product_name,
					'price'         => $item['line_total'],
					'qty'           => !empty($item['qty']) ? $item['qty'] : $item['quantity'],
					'categories'    => $item_categories
				);
			}
		}

		ob_start();
		fue_get_template( 'item-names.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
		$item_list = ob_get_clean();

		ob_start();
		fue_get_template( 'item-prices.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
		$item_list_price = ob_get_clean();

		ob_start();
		fue_get_template( 'item-prices-image.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
		$item_list_prices_image = ob_get_clean();

		ob_start();
		fue_get_template( 'item-codes-prices.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
		$item_list_codes = ob_get_clean();

		ob_start();
		fue_get_template( 'item-prices-categories.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
		$item_list_cats = ob_get_clean();

		ob_start();
		fue_get_template( 'item-quantities.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
		$item_list_qty = ob_get_clean();

		ob_start();
		fue_get_template( 'item-categories.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
		$item_cats = ob_get_clean();

		$item_list_csv = implode( ', ', $items_array );

		$refund_amount = '';
		$refund_reason = '';


		if ( isset($meta['refund_amount']) )
			$refund_amount = strip_tags( wc_price($meta['refund_amount']) );

		if ( isset($meta['refund_reason']) )
			$refund_reason = $meta['refund_reason'];

		$replacements['item_name']              = $item_name;
		$replacements['item_names']             = $item_list;
		$replacements['item_names_list']        = $item_list_csv;
		$replacements['item_categories']        = $item_cats;
		$replacements['item_prices']            = $item_list_price;
		$replacements['item_prices_image']      = $item_list_prices_image;
		$replacements['item_codes_prices']      = $item_list_codes;
		$replacements['item_prices_categories'] = $item_list_cats;
		$replacements['item_quantities']        = $item_list_qty;
		$replacements['item_category']          = $item_cats;
		$replacements['refund_amount']          = $refund_amount;
		$replacements['refund_reason']          = $refund_reason;

		if ( $email->type == 'customer' ) {

			$replacements = $this->get_replacement_data_for_customer( $replacements, $email_data, $queue_item, $email );

		} elseif ( $email->type == 'storewide' || $email->type == 'reminder' ) {

			$replacements = $this->get_replacement_data_for_product( $replacements, $email_data, $queue_item, $email );

		}

		if ( $order && !empty( $email->product_id ) && !empty( $queue_item->meta['download_id'] ) ) {
			$product = WC_FUE_Compatibility::wc_get_product( $email->product_id );

			if ( $product ) {
				$file = $product->get_file( $queue_item->meta['download_id'] );

				$replacements['download_url']       = fue_replacement_url_var( add_query_arg( array(
					'download_file' => $email->product_id,
					'order'         => $order->get_order_key(),
					'email'         => urlencode( $order->get_billing_email() ),
					'key'           => $queue_item->meta['download_id'],
				), trailingslashit( home_url() ) ) );
				$replacements['download_filename']  = $file['name'];
			}
		}

		return $replacements;

	}

	/**
	 * Get replacement data for test emails
	 *
	 * @param array     $email_data
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	private function get_test_replacement_data( $email_data, $email ) {

		$email_type = $email->type;

		$item_url         = '';
		$item_sku         = '';
		$item_price       = '12.50';
		$item_qty         = 1;
		$order_date       = '';
		$order_datetime   = '';
		$order_subtotal   = '';
		$order_tax        = '';
		$order_pay_method = '';
		$order_pay_url    = '';
		$order_id         = $email_data['order_id'];
		$product_id       = $email_data['product_id'];
		$download_url     = admin_url();
		$download_name    = 'Test File';
		$categories       = '';
		$username         = 'jdoe007';

		$replacements     = array();

		$cart_items       = '';
		if ( ! empty( $product_id ) ) {
			$cart_items = array(
				'test' => array(
					'variation_id' => 0,
					'product_id'   => $product_id,
					'data'         => wc_get_product( $product_id ),
					'quantity'     => 1,
				),
			);
		}

		$replacements['cart_contents']  = FUE_Addon_Woocommerce_Cart::get_cart_contents_template( $cart_items );
		$replacements['cart_total']     = '';
		$replacements['cart_url']       = fue_replacement_url_var( add_query_arg( 'fue_cart_redirect', 1, get_permalink( wc_get_page_id( 'myaccount' ) ) ) );
		if ( $email_type == 'storewide' ) {
			// check if user wants to simulate email from a specific order
			if (! empty($order_id) ) {
				// make sure the order exist
				$order = WC_FUE_Compatibility::wc_get_order($order_id);

				if ( ! WC_FUE_Compatibility::get_order_prop( $order, 'id' )) {
					die( esc_html__('The Order ID does not exist. Please try again.'));
				}

				$order_date       = date_i18n(get_option('date_format'), strtotime(WC_FUE_Compatibility::get_order_prop( $order, 'order_date' )));
				$order_datetime   = date_i18n(get_option('date_format') .' '. get_option('time_format'), strtotime(WC_FUE_Compatibility::get_order_prop( $order, 'order_date' )));
				$order_id         = $order->get_order_number();
				$order_total      = FUE_Addon_Woocommerce::format_price( $order->get_total() );
				$order_subtotal   = FUE_Addon_Woocommerce::format_price( $order->get_subtotal() );
				$order_tax        = FUE_Addon_Woocommerce::format_price( $order->get_total_tax() );
				$order_pay_method = WC_FUE_Compatibility::get_order_prop( $order, 'payment_method_title' );

				$billing_address    = $order->get_formatted_billing_address();
				$shipping_address   = $order->get_formatted_shipping_address();
				$billing_phone      = WC_FUE_Compatibility::get_order_prop( $order, 'billing_phone' );
				$shipping_phone     = WC_FUE_Compatibility::get_order_prop( $order, 'shipping_phone' );

				$lists = FUE_Addon_Woocommerce::list_order_items( $order );

				$customer_first = WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' );
				$customer_last  = WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );
				$customer_email = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
				$user_id        = WC_FUE_Compatibility::get_order_prop( $order, 'customer_user' );

				if ( $user_id ) {
					$user       = new WP_User( $user_id );
					$username   = $user->user_login;
				} else {
					$username   = '';
				}
			} else {
				$order_id         = '798';
				$order_total      = FUE_Addon_Woocommerce::format_price( 121.40 );
				$order_subtotal   = FUE_Addon_Woocommerce::format_price( 120 );
				$order_tax        = FUE_Addon_Woocommerce::format_price( 1.40 );
				$order_pay_method = 'PayPal';

				// check if user wants to simulate email from a specific order
				if ( !empty($product_id) ) {
					$item               = WC_FUE_Compatibility::wc_get_product( $product_id );
					$item_categories    = array();
					$cats               = get_the_terms($item->get_id(), 'product_cat');

					if ( is_array($cats) && !empty($cats) ) {
						foreach ($cats as $cat) {
							$item_categories[] = $cat->name;
						}
					}

					$item_url   = get_permalink($item->get_id());
					$item_sku   = $item->get_sku();
					$item_sku   = (!empty($item_sku)) ? '('. $item_sku .')' : '';
					$product_name = FUE_Addon_Woocommerce::get_product_name( $product_id );
					$item_name  = $product_name;
					$item_price = strip_tags( $item->get_price_html() );
					$item_qty   = 1;
					$categories_array = array();

					if (is_array($cats) && !empty($cats)) {
						foreach ($cats as $cat) {
							$categories_array[ $cat->slug ] = $cat->name;
						}
					}

					$lists['items'][] = array(
						'id'    => $item->get_id(),
						'sku'   => $item_sku,
						'link'  => $item_url,
						'name'  => $product_name,
						'price' => $item_price,
						'qty'   => $item_qty,
						'categories'    => $categories_array
					);
				} else {
					$lists['items'][] = array(
						'id'    => 123021,
						'sku'   => 'TI012',
						'link'  => site_url(),
						'name'  => 'Test Item',
						'price' => 12.50,
						'qty'   => 1,
						'categories'    => array('Accessories')
					);
				}

				$billing_address    = '77 North Beach Dr., Miami, FL 35122';
				$shipping_address   = '77 North Beach Dr., Miami, FL 35122';
				$billing_phone      = '9176227215';
				$shipping_phone     = '9309813361';

				$customer_first = $email_data['first_name'];
				$customer_last  = $email_data['last_name'];
				$customer_email = $email_data['email_to'];

				$replacements['item_price']             = $item_price;
				$replacements['item_quantity']          = $item_qty;
			}

			ob_start();
			fue_get_template( 'item-names.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
			$item_list = ob_get_clean();

			ob_start();
			fue_get_template( 'item-prices.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
			$item_list_price = ob_get_clean();

			ob_start();
			fue_get_template( 'item-prices-image.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
			$item_list_prices_image = ob_get_clean();

			ob_start();
			fue_get_template( 'item-codes-prices.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
			$item_list_codes = ob_get_clean();

			ob_start();
			fue_get_template( 'item-prices-categories.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
			$item_list_cats = ob_get_clean();

			ob_start();
			fue_get_template( 'item-quantities.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
			$item_list_qty = ob_get_clean();

			ob_start();
			fue_get_template( 'item-categories.php', array('lists' => $lists), 'follow-up-emails/email-variables/', FUE_TEMPLATES_DIR .'/email-variables/' );
			$item_cats = ob_get_clean();

			$item_names         = array();
			foreach ( $lists['items'] as $item ) {
				$item_names[]   = $item['name'];
			}

			$item_list_csv = implode( ', ', $item_names );

			$replacements['order_number']   = $order_id;
			$replacements['order_date']     = $order_date;
			$replacements['order_datetime'] = $order_datetime;
			$replacements['order_billing_address']  = $billing_address;
			$replacements['order_shipping_address'] = $shipping_address;
			$replacements['order_billing_phone']    = $billing_phone;
			$replacements['order_shipping_phone']   = $shipping_phone;
			$replacements['dollars_spent_order']    = $order_total;
			$replacements['order_subtotal']         = $order_subtotal;
			$replacements['order_tax']              = $order_tax;
			$replacements['order_pay_method']       = $order_pay_method;
			$replacements['order_pay_url']          = fue_replacement_url_var( $order_pay_url );
			$replacements['customer_first_name']    = $customer_first;
			$replacements['customer_last_name']     = $customer_last;
			$replacements['customer_name']          = $customer_first .' '. $customer_last;
			$replacements['customer_email']         = $customer_email;
			$replacements['item_name']              = $item_list;
			$replacements['item_code']              = $item_sku;
			$replacements['item_url']               = fue_replacement_url_var( $item_url );
			$replacements['item_names']             = $item_list;
			$replacements['item_names_list']        = $item_list_csv;
			$replacements['item_prices']            = $item_list_price;
			$replacements['item_prices_image']      = $item_list_prices_image;
			$replacements['item_codes_prices']      = $item_list_codes;
			$replacements['item_prices_categories'] = $item_list_cats;
			$replacements['item_quantities']        = $item_list_qty;
			$replacements['item_categories']        = $item_cats;
			$replacements['item_category']          = $item_cats;
			$replacements['cart_total']             = FUE_Addon_Woocommerce_Cart::get_cart_total();
			$replacements['cart_url']               = fue_replacement_url_var( add_query_arg( 'fue_cart_redirect', 1, get_permalink( wc_get_page_id( 'myaccount' ) ) ) );

		} elseif ( $email_type == 'reminder' ) {
			$categories = '';

			$order_id           = (! empty($order_id) ) ? $order_id : '1100';
			$order_total        = FUE_Addon_Woocommerce::format_price( 121.4 );
			$order_subtotal     = FUE_Addon_Woocommerce::format_price( 120 );
			$order_tax          = FUE_Addon_Woocommerce::format_price( 1.40 );
			$order_pay_method   = 'PayPal';

			$order_date         = date_i18n(get_option('date_format'));
			$order_datetime     = date_i18n(get_option('date_format') .' '. get_option('time_format'));
			$billing_address    = '77 North Beach Dr., Miami, FL 35122';
			$shipping_address   = '77 North Beach Dr., Miami, FL 35122';
			$billing_phone      = '9176227215';
			$shipping_phone     = '9309813361';
			$customer_first     = 'John';
			$customer_last      = 'Doe';
			$customer_email     = 'john@example.org';

			if ( $order_id != '1100' && $order = WC_FUE_Compatibility::wc_get_order($order_id) ) {
				$order_id       = $order->get_order_number();
				$order_total    = FUE_Addon_Woocommerce::format_price( $order->get_total() );
				$order_subtotal = FUE_Addon_Woocommerce::format_price( $order->get_subtotal() );
				$order_tax      = FUE_Addon_Woocommerce::format_price( $order->get_total_tax() );
				$order_pay_method = 'PayPal';

				$used_cats  = array();
				$item_list  = '<ul>';
				$item_cats  = '<ul>';
				$items      = $order->get_items();
				$items_array = array();

				foreach ( $items as $item ) {
					$item_id  = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
					$item_name = FUE_Addon_Woocommerce::get_product_name( $item_id );
					$item_list .= '<li><a href="'. get_permalink($item_id) .'">'. $item_name .'</a></li>';

					$cats   = get_the_terms($item_id, 'product_cat');

					if ( is_array($cats) && !empty($cats) ) {
						foreach ($cats as $cat) {
							if (!in_array($cat->term_id, $used_cats)) {
								$item_cats .= '<li>'. $cat->name .'</li>';
							}
						}
					}
				}

				$item_list .= '</ul>';
				$item_cats .= '</ul>';
				$item_list_csv = implode( ', ', $items_array );
			}

			// check if user wants to simulate email from a specific order
			if (isset($email_data['product_id']) && !empty($email_data['product_id'])) {
				$item       = WC_FUE_Compatibility::wc_get_product( $email_data['product_id'] );
				$cats       = get_the_terms($item->get_id(), 'product_cat');

				if (is_array($cats) && !empty($cats)) {
					foreach ($cats as $cat) {
						$categories .= $cat->name .', ';
					}
					$categories = rtrim($categories, ', ');
				}
				$item_name  = $item->get_title();
				$item_url   = get_permalink($item->get_id());
			} else {
				$item_name  = 'Name of Product';
				$categories = 'Test Category';
				$item_url   = get_bloginfo('url');
			}

			$replacements['order_number']           = $order_id;
			$replacements['dollars_spent_order']    = $order_total;
			$replacements['order_date']             = $order_date;
			$replacements['order_datetime']         = $order_datetime;
			$replacements['order_billing_address']  = $billing_address;
			$replacements['order_shipping_address'] = $shipping_address;
			$replacements['order_billing_phone']    = $billing_phone;
			$replacements['order_shipping_phone']   = $shipping_phone;
			$replacements['customer_first_name']    = $customer_first;
			$replacements['customer_last_name']     = $customer_last;
			$replacements['customer_name']          = $customer_first .' '. $customer_last;
			$replacements['customer_email']         = $customer_email;
			$replacements['item_name']              = $item_name;
			$replacements['item_url']               = fue_replacement_url_var( $item_url );
			$replacements['item_names']             = $item_list;
			$replacements['item_names_list']        = $item_list_csv;
			$replacements['item_categories']        = $categories;
			$replacements['item_category']          = $categories;

		} else {
			$order_number   = '1100';
			$order_date     = date_i18n(get_option('date_format'));
			$order_datetime = date_i18n(get_option('date_format') .' '. get_option('time_format'));
			$customer_first = 'John';
			$customer_last  = 'John Doe';
			$customer_email = 'john@example.org';
			$billing_address    = '77 North Beach Dr., Miami, FL 35122';
			$shipping_address   = '77 North Beach Dr., Miami, FL 35122';
			$billing_phone      = '9176227215';
			$shipping_phone     = '9309813361';

			$item_list = '<ul><li><a href="#">Item 1</a></li><li><a href="#">Item 2</a></li></ul>';
			$item_list_csv = 'Item 1, Item 2';
			$item_cats = '<ul><li>Category 1</li><li>Category 2</li></ul>';

			$spent_order    = strip_tags( wc_price(19.99) );
			$spent_total    = strip_tags( wc_price(3250) );
			$total_orders   = 12;
			$last_order_date= $order_date;

			$item_name  = 'Name of Product';
			$item_url   = '#';
			$item_cat   = 'Test Category';
			$item_price = '$29.95';
			$item_qty   = 1;

			$replacements['order_number']           = $order_number;
			$replacements['order_date']             = $order_date;
			$replacements['order_datetime']         = $order_datetime;
			$replacements['customer_first_name']    = $customer_first;
			$replacements['customer_last_name']     = $customer_last;
			$replacements['customer_name']          = $customer_first .' '. $customer_last;
			$replacements['customer_email']         = $customer_email;
			$replacements['item_name']              = $item_name;
			$replacements['item_url']               = fue_replacement_url_var( $item_url );
			$replacements['item_names']             = $item_list;
			$replacements['item_names_list']        = $item_list_csv;
			$replacements['item_category']          = $item_cat;
			$replacements['item_categories']        = $item_cat;
			$replacements['item_price']             = $item_price;
			$replacements['item_quantity']          = $item_qty;
			$replacements['amount_spent_order']    = $spent_order;
			$replacements['amount_spent_total']    = $spent_total;
			$replacements['number_orders']          = $total_orders;
			$replacements['last_purchase_date']     = $last_order_date;
			$replacements['order_billing_address']  = $billing_address;
			$replacements['order_shipping_address'] = $shipping_address;
			$replacements['order_billing_phone']    = $billing_phone;
			$replacements['order_shipping_phone']   = $shipping_phone;
		}

		$replacements['customer_username']  = $username;
		$replacements['download_url']       = fue_replacement_url_var( $download_url );
		$replacements['download_filename']  = $download_name;

		return $replacements;
	}

	/**
	 * Get replacement data for customer emails
	 *
	 * @param array     $replacements
	 * @param array     $email_data
	 * @param object    $queue_item
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	private function get_replacement_data_for_customer( $replacements, $email_data, $queue_item, $email ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;
		$order  = WC_FUE_Compatibility::wc_get_order( $queue_item->order_id );

		if ( $email_data['user_id'] > 0 ) {
			$customer = $wpdb->get_row( $wpdb->prepare(
				"SELECT *
				FROM {$wpdb->prefix}followup_customers
				WHERE user_id = %d",
				$email_data['user_id']
			) );

			$last_order_date = $wpdb->get_var( $wpdb->prepare(
				"SELECT p.post_date
				FROM {$wpdb->posts} p, {$wpdb->prefix}followup_customer_orders co
				WHERE co.followup_customer_id = %d
				AND co.order_id = p.ID
				AND p.post_status = 'publish'
				ORDER BY p.ID
				DESC LIMIT 1",
				$email_data['user_id']
			) );

			$spent_total    = FUE_Addon_Woocommerce::format_price( $customer->total_purchase_price );
			$num_orders     = $customer->total_orders;

			$last_purchase  = date_i18n( get_option('date_format'), strtotime($last_order_date) );
		} else {
			$customer       = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}followup_customers WHERE email_address = %s", $email_data['email_to']) );
			$spent_total    = FUE_Addon_Woocommerce::format_price( $customer->total_purchase_price );
			$num_orders     = $customer->total_orders;
			$last_order_date= $wpdb->get_var( $wpdb->prepare("SELECT p.post_date FROM {$wpdb->posts} p, {$wpdb->postmeta} pm WHERE pm.meta_key = '_billing_email' AND pm.meta_value = %d AND pm.post_id = p.ID AND p.post_status = 'publish' ORDER BY p.ID DESC LIMIT 1", $email_data['email_to']) );
			$last_purchase  = date( get_option('date_format'), strtotime($last_order_date) );
		}

		$spent_order = 0;
		if ( $order ) {
			$spent_order = FUE_Addon_Woocommerce::format_price( WC_FUE_Compatibility::get_order_prop( $order, 'order_total' ) );

			$replacements['dollar_spent_order'] = $spent_order;
			$replacements['order_subtotal']     = FUE_Addon_Woocommerce::format_price( $order->get_subtotal() );
			$replacements['order_tax']          = FUE_Addon_Woocommerce::format_price( $order->get_total_tax() );
			$replacements['order_pay_method']   = WC_FUE_Compatibility::get_order_prop( $order, 'payment_method_title' );
			$replacements['order_pay_url']      = fue_replacement_url_var( $order->get_checkout_payment_url() );
		}

		$replacements['amount_spent_order'] = $spent_order;
		$replacements['amount_spent_total'] = $spent_total;
		$replacements['number_orders']      = $num_orders;
		$replacements['last_purchase_date'] = $last_purchase;

		return $replacements;
	}

	/**
	 * Get replacement data for product and reminder emails
	 *
	 * @param array     $replacements
	 * @param array     $email_data
	 * @param object    $queue_item
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	private function get_replacement_data_for_product( $replacements, $email_data, $queue_item, $email ) {
		$categories = '';
		$item_name  = '';
		$item_price = '';
		$item_sku   = '';
		$item_url   = '';
		$variation_ids = array();

		if ( !empty($queue_item->product_id) ) {
			$_product   = WC_FUE_Compatibility::wc_get_product( $queue_item->product_id );

			if ( $_product ) {
				if ( $_product->has_child() ) {
					$variation_ids = $_product->get_children();
				}

				$item_sku   = $_product->get_sku();
				$cats       = get_the_terms( $_product->get_id(), 'product_cat' );

				if (is_array($cats) && !empty($cats)) {
					foreach ($cats as $cat) {
						$categories .= $cat->name .', ';
					}
					$categories = rtrim($categories, ', ');
				}

				if ( method_exists( $_product, 'get_permalink' ) ) {
					$product_permalink = $_product->get_permalink();
				} else {
					$product_permalink = get_permalink($queue_item->product_id);
				}

				$item_url = FUE_Sending_Mailer::create_email_url(
					$queue_item->id,
					$queue_item->email_id,
					$email_data['user_id'],
					$email_data['email_to'],
					$product_permalink
				);
				$item_name = FUE_Addon_Woocommerce::get_product_name( $queue_item->product_id );
			}

			$order = WC_FUE_Compatibility::wc_get_order( $queue_item->order_id );

			if ( $order instanceof WC_Order ) {
				foreach ( $order->get_items() as $order_item ) {
					if ( $order_item['product_id'] == $queue_item->product_id || $order_item['variation_id'] == $queue_item->product_id ) {
						if ( empty( $replacements['item_quantity'] ) ) {
							$replacements['item_quantity'] = $order_item['qty'];
						}
						if ( empty( $item_price ) && in_array( $order_item['product_id'], $variation_ids ) || $order_item['product_id'] == $queue_item->product_id ) {
							$item_price = strip_tags( wc_price( $order_item['line_subtotal'] / $order_item['qty'] ) );
						}
					}
				}
				if ( $order->needs_payment() ) {
					$replacements['order_pay_url'] = fue_replacement_url_var( $order->get_checkout_payment_url() );
				}
			}
		}

		$replacements['item_name']      = $item_name;
		$replacements['item_url']       = fue_replacement_url_var( $item_url );
		$replacements['item_code']      = $item_sku;
		$replacements['item_price']     = $item_price;

		return $replacements;
	}

	/**
	 * Apply WC-style email wrapping to the email message
	 *
	 * @param array $email_data
	 * @param FUE_Email $email
	 * @return array
	 */
	public function apply_email_wrapping( $email_data, $email ) {
		if ( strtolower( $email->template ) != 'woocommerce' ) {
			return $email_data;
		}

		$mailer         = WC()->mailer();
		$disable_wrap   = get_option('fue_disable_wrapping', 0);

		// set WC_Email to send the email
		add_filter( 'fue_mail_method', array( $this, 'set_wc_email_to_send' ) );

		if (! $disable_wrap ) {
			// Only use <body> content if previously converted to DOMDocument
			$email_data['message'] = $this->get_email_body_content( $email_data['message']);
			$email_data['message'] = $mailer->wrap_message( $email_data['subject'], $email_data['message'] );
		} else {
			$email_data['message'] = wpautop( wptexturize( $email_data['message'] ) );
		}

		// WC inline styles
		$wc_email = new FUE_Addon_WooCommerce_Email();
		if ( method_exists( $wc_email, 'style_inline' ) ) {
			$email_data['message'] = $wc_email->style_inline( $email_data['message'] );
		}

		return $email_data;
	}

	/**
	 * Set WC_Email to send
	 *
     * @return array
     * @since 4.7.5
	 */
	public function set_wc_email_to_send() {
		return array( WC()->mailer(), 'send' );
	}

	/**
	 * Determine the part to send for reminder emails
	 *
	 * @param string    $message
	 * @param FUE_Email $email
	 * @param object    $queue_item
	 *
	 * @return string
	 */
	public function parse_reminder_message($message, $email, $queue_item) {
		global $wpdb;
		if ( $queue_item->order_id > 0 && $email->type == 'reminder' ) {
			// count the total emails and the number of sent emails
			$total_emails   = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}followup_email_orders WHERE order_id = %d AND email_id = %d", $queue_item->order_id, $email->id) );
			$sent_emails    = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}followup_email_orders WHERE order_id = %d AND email_id = %d AND is_sent = 1", $queue_item->order_id, $email->id) );

			if ( $total_emails == 1 ) {
				$messages = fue_str_search('{first_email}', '{/first_email}', $message);

				$message = (isset($messages[0])) ? $messages[0] : '';
			} elseif ( $total_emails == 2 ) {
				if ( $sent_emails == 0 ) {
					$messages = fue_str_search('{first_email}', '{/first_email}', $message);

					$message = (isset($messages[0])) ? $messages[0] : '';
				} else {
					$messages = fue_str_search('{final_email}', '{/final_email}', $message);
					$message = (isset($messages[0])) ? $messages[0] : '';
				}
			} else {
				if ( $sent_emails == 0 ) {
					$messages = fue_str_search('{first_email}', '{/first_email}', $message);
					$message = (isset($messages[0])) ? $messages[0] : '';
				} elseif ( $sent_emails == ($total_emails - 1) ) {
					$messages = fue_str_search('{final_email}', '{/final_email}', $message);
					$message = (isset($messages[0])) ? $messages[0] : '';
				} else {
					$messages = fue_str_search('{quantity_email}', '{/quantity_email}', $message);
					$message = (isset($messages[0])) ? $messages[0] : '';
				}
			}
		}

		return $message;
	}

	/**
	 * Get the order total without any HTML included
	 *
	 * @param WC_Order $order
	 * @return string
	 */
	private function get_plain_order_total( $order ) {
		$total = function_exists('wc_price') ? wc_price( $order->get_total() ) : wc_price( $order->get_total() );
		$total = strip_tags( $total );
		return $total;
	}

	/**
	 * Get the actual content of an email message, extracting it from the <body> tag if
	 * a full HTML document is provided.
	 *
	 * @since 4.9.11
	 *
	 * @param string $message The email message to parse.
	 *
	 * @return string The actual content of the document.
	 */
	private function get_email_body_content( $message  ) {
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$message = mb_convert_encoding( $message, 'HTML-ENTITIES', 'UTF-8' );
		}

		$dom = new DOMDocument();
		$dom->loadHTML( $message );
		$domBody = $dom->getElementsByTagName('body');

		if($domBody->length > 0) {
			$message = '';
			/** @var DomNode $child */
			foreach($domBody->item(0)->childNodes as $child) {
				$message .= $child->ownerDocument->saveHTML($child);
			}
		}

		return $message;
	}
}
