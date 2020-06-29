<?php
/**
 * AV8_Cart_Action
 */

class AV8_Cart_Receipt {

	public $post_author;
	public $post_content;
	public $post_name;
	public $post_title;
	public $post_status;
	public $post_date;
	public $products;
	public $sid;
	public $post_id;
	public $order;
	public $is_new;
	public $ip_address;
	public $user_agent;
	public $created;
	public $last_updated;
	public $last_connected;

	/**
	 * Set the $offset variable
	 *
	 * Should be refactored for better math. This temporary solution comes from
	 * https://wordpress.stackexchange.com/a/283094
	 */
	public function get_timezone_offset() {
		$timezone_string = get_option( 'timezone_string' );
		$seconds_in_hour = 3600;

		if ( ! empty( $timezone_string ) ) {
			$today    = new DateTime();
			$timezone = new DateTimeZone( $timezone_string );

			return $timezone->getOffset( $today ) / $seconds_in_hour;
		}

		return get_option( 'gmt_offset' );
	}

	public function __construct( $sid = '' ) {
		global $offset, $woocommerce_cart_reports_options;

		$offset    = $this->get_timezone_offset();
		$this->sid = $sid;

		if ( isset( $woocommerce_cart_reports_options['logip'] ) ) {
			$this->ip_address = $this->get_user_ip();
			$this->user_agent = ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		} else {
			$this->ip_address = '';
			$this->user_agent = '';
		}
		if ( $this->sid != '' ) {
			$this->post_id = $this->get_id_from_session( $this->sid );
		} else {
			$this->post_id = '';
		}
		// check for an associated order and grab it if exists
		$post_author = '';
		if ( $this->post_id == 0 || $this->post_id == '' ) {
			$this->is_new      = true;
			$this->post_status = 'publish';
		}
	}

	public function get_user_ip() {
		if ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$ipaddress = getenv( 'HTTP_CLIENT_IP' );
		} elseif ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$ipaddress = getenv( 'HTTP_X_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_X_FORWARDED' ) ) {
			$ipaddress = getenv( 'HTTP_X_FORWARDED' );
		} elseif ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
			$ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_FORWARDED' ) ) {
			$ipaddress = getenv( 'HTTP_FORWARDED' );
		} elseif ( getenv( 'REMOTE_ADDR' ) ) {
			$ipaddress = getenv( 'REMOTE_ADDR' );
		} else {
			$ipaddress = 'UNKNOWN';
		}

		return $ipaddress;
	}

	public function get_id_from_session( $s ) {
		$args = array(
			'numberposts' => 1,
			'offset'      => 0,
			'orderby'     => 'post_date',
			'order'       => 'DESC',
			'post_type'   => 'carts',
			'post_status' => 'publish',
			'meta_query'  => array(
				array(
					'key'   => 'cart_session_id',
					'value' => $s,
				),
			),

			'tax_query' => array(
				array(
					'taxonomy' => 'shop_cart_status',
					'terms'    => 'open',
					'field'    => 'slug',
					'operator' => 'IN',
				),
			),
		);

		$carts   = get_posts( $args );
		$post_id = '';

		foreach ( $carts as $cart ) {
			$post_id = $cart->ID;
		}

		return $post_id;
	}

	public function set_products( $woocommerce ) {
		$products = array();

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters(
				'woocommerce_cart_item_product',
				$cart_item['data'],
				$cart_item,
				$cart_item_key
			);
			if ( isset( $cart_item['variation_id'] ) ) {
				$products[] = array(
					'type'         => 'variation',
					'product_id'   => $cart_item['product_id'],
					'variation_id' => $cart_item['variation_id'],
					'quantity'     => $cart_item['quantity'],
					'variation'    => $cart_item['variation'],
					'price'        => apply_filters(
						'woocommerce_cart_item_price',
						WC()->cart->get_product_price( $_product ),
						$cart_item,
						$cart_item_key
					),
				);
			} elseif ( isset( $cart_item['product_id'] ) ) {
				$products[] = array(
					'type'         => 'product',
					'product_id'   => $cart_item['product_id'],
					'variation_id' => false,
					'variation'    => array(),
					'quantity'     => $cart_item['quantity'],
					'price'        => apply_filters(
						'woocommerce_cart_item_price',
						WC()->cart->get_product_price( $_product ),
						$cart_item,
						$cart_item_key
					),
				);
			}
		}

		if ( WP_DEBUG == true ) {
			assert( is_array( $products ) );
		}

		$this->products = $products;
	}

	public function set_owner( $owner ) {
		// We still want to set the title to person x's cart, to keep searching working for guest carts
		$this->post_author = $owner;
		if ( WP_DEBUG == true ) {
			assert( $this->post_author > 0 || $this->post_author == '' );
		}
		if ( $this->post_author > 0 ) {
			$user_info = get_userdata( $this->post_author );
			if ( $user_info->first_name != '' && $user_info->last_name != '' ) {
				$this->post_title = $user_info->first_name . ' ' . $user_info->last_name . "'s Cart";
			} else {
				$this->post_title = ucwords( $user_info->user_login ) . "'s Cart";
			}
		} else {
			$this->post_author = 0;
			$this->post_title  = "Guest's Cart";
		}
	}

	public function is_guest_order() {
		if ( WP_DEBUG == true ) {
			assert( $this->post_author > - 1 );
		} //Is 0 or greater
		if ( $this->post_author == 0 ) {
			return true;
		} else {
			return false;
		}
	}

	public function load_receipt( $post_id ) {
		global $offset;
		if ( WP_DEBUG == true ) {
			assert( is_numeric( $offset ) );
		}
		// Precond: post_id > 0 and not ""
		$this->post_id = $post_id;
		if ( WP_DEBUG == true ) {
			assert( $this->post_id > 0 && $this->post_id != '' );
		}
		$this->is_new = false;
		$post         = get_post( $this->post_id );

		// $products = get_post_meta($post_id,'av8_cartitems',true);

		$this->post_title  = $post->post_title;
		$this->post_author = $post->post_author;
		$this->post_date   = $post->post_date;

		$this->ip_address = get_post_meta( $this->post_id, 'av8_ip_address', true );
		// $this->user_agent = get_post_meta($this->post_id,'av8_user_agent',true);

		$this->last_updated = absint( get_post_meta( $this->post_id, 'av8_last_updated', true ) ) + ( $offset * 3600 );

		$this->last_connected = $post->post_date;
		$this->created        = get_post_meta( $this->post_id, 'av8_cart_created', true );

		$this->sid = get_post_meta( $this->post_id, 'cart_session_id', true );
	}

	/**
	 * Save the Receipt
	 *
	 * Precond: $products are loaded into the instance.
	 *
	 * Use the '$is_new' flag to determine whether we need to insert_post or update_post.
	 */
	public function save_receipt() {

		// We need to make sure there aren't any conversions from this session in the last 8 seconds
		$args = array(
			'numberposts'      => 1,
			'offset'           => 0,
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => 'carts',
			'post_status'      => 'publish',
			'suppress_filters' => false,

			'meta_query' => array(
				array(
					'key'   => 'cart_session_id',
					'value' => $this->sid,
				),
			),

			'tax_query' => array(
				array(
					'taxonomy' => 'shop_cart_status',
					'terms'    => 'converted',
					'field'    => 'slug',
					'operator' => 'IN',
				),
			),
		);

		add_filter( 'posts_where', array( $this, 'very_recent' ) );

		$carts = get_posts( $args );

		remove_filter( 'posts_where', array( $this, 'very_recent' ) );

		if ( count( $carts ) > 0 ) {
			return;
		}

		if ( $this->sid != '' && $this->is_new && $this->products ) { // Only insert a cart if there are products
			$this->insert_receipt();
		} elseif ( $this->sid != '' && $this->post_id != '' && $this->products ) { // Update the cart if there are new products.
			$this->update_receipt();
		} elseif ( $this->post_id ) { // If there aren't any products in an existing cart, delete the cart.
			$this->delete_receipt();
		}
	}

	public function delete_receipt() {
		$post_id = $this->post_id;
		wp_delete_post( $post_id, true );
	}

	public function very_recent( $where ) {
		global $offset;

		$where .= " AND post_date > '" . date( 'Y-m-d G:i:s', time() + ( $offset * 3600 ) - 8 ) . "' ";

		return $where;
	}

	public function status() {
		global $woocommerce_cart_reports_options;
		global $offset;

		if ( WP_DEBUG == true ) {
			assert( $offset != '' && is_numeric( $offset ) );
		}

		// check the status - either CONVERTED, OPEN, or ABANDONED

		$stored_status_obj = wp_get_object_terms( $this->post_id, 'shop_cart_status', false );

		$stored_status = '';

		foreach ( $stored_status_obj as $s ) :
			$stored_status = ucwords( $s->name );
		endforeach;

		// Timeout option in milliseconds
		$timeout = $woocommerce_cart_reports_options['timeout'];
		if ( WP_DEBUG == true ) {
			assert( $timeout != '' && is_int( $timeout ) );
		}

		$since_connected = time() - $this->get_last_connected();
		if ( $since_connected > $timeout && $this->get_last_connected() != '' && $stored_status != 'Converted' && $timeout > 0 ) {
			return ABANDONED;
		} elseif ( ! is_wp_error( $stored_status ) && $stored_status != '' ) {
			return $stored_status;
		} else {
			return 'Abandoned';
		}
	}

	public function update_receipt() {
		if ( WP_DEBUG == true ) {
			assert( $this->sid != '' );
		}

		global $wpdb;
		global $offset;

		if ( WP_DEBUG == true ) {
			assert( $offset != '' && is_numeric( $offset ) );
			assert( $this->post_id > 0 ); // Make sure we're working with a valid post id.
		}

		update_post_meta( $this->post_id, 'av8_cartitems', $this->add_titles_to_cart_items( $this->products ) );
		$post_updated = array(
			'ID'          => $this->post_id,
			'post_title'  => $this->post_title,
			'post_author' => $this->post_author,
			'post_date'   => $this->post_date,
			'post_type'   => 'carts',

		);

		wp_update_post( $post_updated );
		update_post_meta( $this->post_id, 'av8_last_updated', time() );
		update_post_meta( $this->post_id, 'av8_last_updated_date', date( 'Y-m-d H:i:s' ) );
	}

	public function set_guest_details( $meta_name = '_customer_data' ) {
		$cust_data           = get_post_meta( $this->post_id, $meta_name, true );
		$this->guest_details = $cust_data;
	}


	public function has_guest_details() {
		if ( ! empty( $this->guest_details ) ) {
			$cust_data = $this->guest_details;
			if ( $cust_data['billing_first_name'] == '' && $cust_data['billing_last_name'] == '' && $cust_data['billing_email'] == '' ) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	public function get_guest_details( $field ) {
		if ( $this->has_guest_details() ) {
			$detail_arr = $this->guest_details;

			return $detail_arr[ $field ];
		} else {
			return false;
		}
	}


	public function full_name() {
		$this->set_guest_details();

		global $current_user;
		if ( $this->post_author > 0 ) {
			$user_info = get_userdata( $this->post_author );
			if ( $user_info->first_name != '' && $user_info->last_name != '' ) {
				return $user_info->first_name . ' ' . $user_info->last_name;
			} else {
				return ucwords( $user_info->user_login );
			}
		} elseif ( $this->has_guest_details() && $this->get_guest_details( 'billing_first_name' ) != '' && $this->get_guest_details( 'billing_last_name' ) ) {
			return ucwords( $this->get_guest_details( 'billing_first_name' ) ) . ' ' . ucwords( $this->get_guest_details( 'billing_last_name' ) );
		}

		return false;
	}

	public function email() {
		$cust_info = get_post_meta( $this->post_id, '_customer_data', true );
		global $current_user;
		if ( $this->post_author > 0 ) {
			$user_info = get_userdata( $this->post_author );

			return $user_info->user_email;
		} elseif ( isset( $cust_info['billing_email'] ) && $cust_info['billing_email'] != '' ) {
			return $cust_info['billing_email'];
		} else {
			return false;
		}
	}


	/**
	 * Get when user's last connected timestamp.
	 *
	 * @return false|int|string Unix Timestamp
	 */
	public function get_last_connected() {
		$post = get_post( $this->post_id );

		global $offset;
		if ( WP_DEBUG == true ) {
			assert( $offset != '' && is_numeric( $offset ) );
		}
		if ( isset( $post->post_modified_gmt ) && $post->post_modified_gmt != '' ) {
			return strtotime( $post->post_modified_gmt );
		} else {
			return '';
		}
	}

	public function set_last_connected() {
		update_post_meta( $this->post_id, 'av8_last_connected', time() );
	}

	public function last_updated() {
		// return last updated timestamp
		global $offset;
		if ( WP_DEBUG == true ) {
			assert( $offset != '' && is_numeric( $offset ) );
		}

		return $this->last_updated + ( $offset * 3600 );
	}


	public function created() {
		// return last updated timestamp
		global $offset;
		if ( WP_DEBUG == true ) {
			assert( $offset != '' && is_numeric( $offset ) );
		}

		return $this->created + ( $offset * 3600 );
	}

	public function get_age_text( $time = '' ) {
		// Precon: created and last_connected are populated
		$disp    = '';
		$created = $this->created();

		if ( $time == '' ) {
			$connected = $this->get_last_connected();
			if ( WP_DEBUG == true ) {
				assert( is_int( $connected ) );
			}
		} else {
			$connected = $time;
			if ( WP_DEBUG == true ) {
				assert( is_int( $connected ) );
			}
		}

		if ( $created == '' || $connected == '' ) {
			return '<span style="color:lightgray;">' . __( 'Not Available', 'woocommerce_cart_reports' ) . '</span>';
		} else {
			$time_diff = abs( $connected - $created );
			if ( WP_DEBUG == true ) {
				assert( $time_diff != '' && $time_diff > 0 );
			}
			$number_days = intval( $time_diff / 86400 );  // 86400 seconds in one day

			// If it's older than six months let's not show days just say > 6 months

			if ( $number_days > 180 ) {
				return __( '> 6 Months', 'woocommerce_cart_reports' );
			}

			$number_hours   = intval( ( $time_diff - ( $number_days * 86400 ) ) / 3600 );
			$number_minutes = intval( ( $time_diff - ( $number_days * 86400 ) - ( $number_hours * 3600 ) ) / 60 );
			$number_seconds = intval( ( $time_diff - ( $number_days * 86400 ) - ( $number_hours * 3600 ) - ( $number_minutes * 60 ) ) );

			if ( $number_days > 1 ) {
				$days_label = __( 'Days', 'woocommerce_cart_reports' );
			} else {
				$days_label = __( 'Day', 'woocommerce_cart_reports' );
			}

			if ( $number_hours > 1 ) {
				$hours_label = __( 'Hours', 'woocommerce_cart_reports' );
			} else {
				$hours_label = __( 'Hour', 'woocommerce_cart_reports' );
			}

			if ( $number_minutes > 1 ) {
				$minutes_label = __( 'Minutes', 'woocommerce_cart_reports' );
			} else {
				$minutes_label = __( 'Minute', 'woocommerce_cart_reports' );
			}

			if ( $number_seconds > 1 ) {
				$seconds_label = __( 'Seconds', 'woocommerce_cart_reports' );
			} else {
				$seconds_label = __( 'Second', 'woocommerce_cart_reports' );
			}

			if ( $number_days > 0 ) {
				$disp .= "<strong>$number_days</strong> " . $days_label;
			}
			if ( ( $number_days > 0 ) && ( $number_hours > 0 ) ) {
				$disp .= ', ';
			}
			if ( $number_hours > 0 ) {
				$disp .= "<strong>$number_hours</strong> " . $hours_label;
			}
			if ( ( $number_hours > 0 ) && ( $number_minutes > 0 ) ) {
				$disp .= ', ';
			}
			if ( $number_minutes > 0 ) {
				$disp .= "<strong>$number_minutes</strong> " . $minutes_label;
			}
			if ( ( $number_minutes > 0 ) && ( $number_seconds > 0 ) ) {
				$disp .= ', ';
			}
			if ( $number_seconds > 0 ) {
				$disp .= "<strong>$number_seconds</strong> " . $seconds_label;
			}
		}

		return $disp;
	}

	public function get_order_id() {
		$order_id = get_post_meta( $this->post_id, 'av8_order_id', true );
		if ( $order_id != '' ) {
			return get_post_meta( $this->post_id, 'av8_order_id', true );
		} else {
			return false;
		}
	}

	public function save_user_id( $user_id, $post_id ) {
		if ( WP_DEBUG == true ) {
			assert( $user_id != 0 && is_int( $user_id ) && $user_id > 0 );
		}
		$post_updated = array(
			'ID'          => $post_id,
			'post_author' => $user_id,
			'post_type'   => 'carts',
		);

		wp_update_post( $post_updated );
	}

	public function save_order_id( $order_id ) {
		if ( WP_DEBUG === true ) {
			assert( 0 < $order_id && is_int( $order_id ) && '' !== $order_id );
		}

		update_post_meta( $this->post_id, 'av8_order_id', $order_id );

		$order      = wc_get_order( $order_id );
		$post_title = version_compare(
			WC_VERSION,
			'3.0',
			'<'
		) ? ( $order->billing_first_name . ' ' . $order->billing_last_name ) : ( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );

		$post_updated = array(
			'ID'         => $this->post_id,
			'post_title' => $post_title,
			'post_type'  => 'carts',
		);

		wp_update_post( $post_updated );
	}

	/**
	 * Generates a unique slug so we don't have to rely on WordPress to do it
	 * which can be slow as a DB query is executed in order to make sure each
	 * slug is unique.
	 *
	 * @param string $post_title The post's title.
	 * @param string $session_id The current session ID.
	 *
	 * @return string
	 */
	public function generate_cart_slug( $post_title, $session_id = '' ) {
		$slug = sanitize_title( $post_title );

		if ( $session_id ) {
			return $slug . '-' . $session_id;
		}

		if ( "Guest's Cart" === $post_title ) {
			return uniqid( $slug . '-', false );
		}

		return $slug;
	}


	/**
	 * Inserts the cart session post into the DB
	 */
	public function insert_receipt() {
		global $offset, $woocommerce_cart_reports_options;

		/**
		 * The user ID number of the person who created this cart.
		 * If it's guest, this value will be empty. WordPress doesn't complain,
		 * so at this point neither do I.
		 */
		$post = array(
			'post_author'  => $this->post_author,
			'post_content' => '',
			'post_status'  => 'publish',
			'post_title'   => $this->post_title,
			'post_type'    => 'carts',
			'post_name'    => $this->generate_cart_slug( $this->post_title, $this->sid ),
		);

		$post_id = wp_insert_post( $post );

		if ( is_wp_error( $post_id ) ) {
			return;
		}

		$this->post_id = $post_id;

		// Save the session id for this cart.
		update_post_meta( $this->post_id, 'cart_session_id', $this->sid );

		// Only save if the user has checked "Log IP" in the setting page.
		if ( isset( $woocommerce_cart_reports_options['logip'] ) ) {
			update_post_meta( $this->post_id, 'av8_ip_address', $this->ip_address );
			update_post_meta( $this->post_id, 'av8_user_agent', $this->user_agent );
		}

		// Saving the open/converted as meta - much faster than post meta!
		wp_set_object_terms(
			$this->post_id,
			'open',
			'shop_cart_status'
		);

		// Save the Cart products from the WooCommerce object.
		update_post_meta(
			$this->post_id,
			'av8_cartitems',
			$this->add_titles_to_cart_items( $this->products )
		);

		// Save created time.
		update_post_meta( $this->post_id, 'av8_cart_created', time() );
		update_post_meta( $this->post_id, 'av8_cart_created_date', date( 'Y-m-d H:i:s' ) );

		// Initialize the updated time.
		update_post_meta( $this->post_id, 'av8_last_updated', time() );
		update_post_meta( $this->post_id, 'av8_last_updated_date', date( 'Y-m-d H:i:s' ) );
	}

	public function add_titles_to_cart_items( $products ) {

		// Add titles to the attributes in the product array for searching on the index page
		$newProducts = array();
		foreach ( $products as $product ) {
			if ( isset( $product['product_id'] ) ) {
				$product['title'] = get_the_title( $product['product_id'] );
			} elseif ( isset( $product['variation_id'] ) ) {
				$product['title'] = get_the_title( $product['variation_id'] );
			}
			$newProducts[] = $product;
		}

		return $newProducts;
	}

	public function save_conversion() {
		// Check if this is a guest account - because we want to save this cart if they registered.
		if ( $this->post_id > 0 && is_int( $this->post_id ) ) { // we have already done the guest check at the higher layer.
			if ( WP_DEBUG == true ) {
				assert( $this->post_id > 0 && is_numeric( $this->post_id ) );
			}

			wp_set_object_terms( $this->post_id, 'converted', 'shop_cart_status' );
			// Make sure the cart has been emptied.
		}
	}

	public function record_page_view() {
		global $woocommerce_cart_reports_options;
		global $woocommerce;
		if ( $this->status() != 'Converted' && $this->post_id != 0 && $this->post_id > 0 ) { // We don't want to save any online info if the cart is converted - because the carts effectively done. This comes into play if the customer browses the site after converting a cart - it'll mess with the reports.
			if ( isset( $woocommerce_cart_reports_options['logip'] ) ) { // Again, only log if this setting is enabled
				update_post_meta( $this->post_id, 'av8_ip_address', $this->ip_address );
				update_post_meta( $this->post_id, 'av8_user_agent', $this->user_agent );
			}

			if ( WP_DEBUG == true ) {
				assert( $this->post_date != false && $this->post_id != false && $this->post_date != '' && $this->post_id != '' );
			}

			$post_updated = array(
				'ID'        => $this->post_id,
				'post_date' => $this->post_date,
				'post_type' => 'carts',
			);

			$this->set_products( $woocommerce );
			update_post_meta( $this->post_id, 'av8_cartitems', $this->add_titles_to_cart_items( $this->products ) );

			wp_update_post( $post_updated ); // We're saving the "Last Online" time in the main post date - this is much faster to access, and this is the date queried most often.
		}
	}

	public function print_cart_actions() {
		global $post;
		global $user_info;
		$cart_receipt = new AV8_Cart_Receipt();
		$cart_receipt->load_receipt( $post->ID );

		$actions = array();
		$status  = $cart_receipt->status();

		switch ( $status ) {

			case 'Converted':
				// Click to see created order form
				$order_id = '';
				$order_id = get_post_meta( $post->ID, 'av8_order_id', true );

				if ( $order_id > 0 && $order_id != '' ) {
					$order_post = get_post( $order_id );
					$order_link = get_admin_url( '', 'post.php?post=' . $order_id . '&action=edit' );
					$actions[]  = new AV8_Cart_Action( $order_link, 'View Order' );
				}
				do_action( 'av8_carts_action_converted', $actions );

				break;

			case 'Open':
				// Make sure this isn't a guest-owned cart
				if ( isset( $cart_receipt ) && $cart_receipt->full_name() != '' ) {
					$full_name = $cart_receipt->full_name();
				} else {
					$full_name = $cart_receipt->email();
				}

				if ( $cart_receipt->email() != false && $cart_receipt->email() != '' ) {
					$email_link = 'mailto:' . $cart_receipt->email();
					$actions[]  = new AV8_Cart_Action( $email_link, "Email $full_name" );
				}

				do_action( 'av8_carts_action_open', $actions );

				break;

			case 'Abandoned':
				// Allow the site admin to email the cart abandoner

				// Make sure this isn't a guest-owned cart

				if ( $cart_receipt->full_name() != '' && $cart_receipt->full_name() != false ) {
					$full_name = $cart_receipt->full_name();
				} else {
					$full_name = $cart_receipt->email();
				}

				if ( $cart_receipt->email() != false && $cart_receipt->email() != '' ) {
					$email_link = 'mailto:' . $cart_receipt->email();
					$actions[]  = new AV8_Cart_Action( $email_link, "Email $full_name" );
				}

				do_action( 'av8_carts_action_abandoned' );

				break;

			default:
				break;
		}

		if ( empty( $actions ) ) {
			echo "<span style='color:lightgray;'>" . __(
					'No Actions Available',
					'woocommerce_cart_reports'
				) . '</span>';
		} else {
			// Print the actions
			foreach ( $actions as $action ) :
				echo $action->display();
			endforeach;
		}
	}
} // END CLASS
