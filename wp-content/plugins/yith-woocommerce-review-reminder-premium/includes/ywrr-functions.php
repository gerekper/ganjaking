<?php
/**
 * Plugin functions
 *
 * @package YITH\ReviewReminder
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * GENERIC USE FUNCTIONS
 */
if ( ! function_exists( 'ywrr_user_has_commented' ) ) {

	/**
	 * Check if has reviewed the product
	 *
	 * @param integer $product_id The product ID.
	 * @param string  $user_email The user email.
	 *
	 * @return  boolean
	 * @since   1.6.0
	 */
	function ywrr_user_has_commented( $product_id, $user_email ) {

		if ( defined( 'YITH_YWAR_PREMIUM' ) && YITH_YWAR_PREMIUM ) {

			$args = array(
				'posts_per_page' => -1,
				'post_type'      => YITH_YWAR_POST_TYPE,
				'post_parent'    => 0,
				'post_status'    => 'publish',
				//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => YITH_YWAR_META_APPROVED,
						'value'   => 1,
						'compare' => '=',
					),
					array(
						'key'     => YITH_YWAR_META_KEY_PRODUCT_ID,
						'value'   => $product_id,
						'compare' => '=',
					),
					array(
						'key'     => YITH_YWAR_META_REVIEW_AUTHOR_EMAIL,
						'value'   => $user_email,
						'compare' => '=',
					),
				),
			);

			$reviews = get_posts( $args );
			$count   = count( $reviews );
		} else {

			global $wpdb;
			$sql = "
					SELECT  COUNT(comment_ID)
					FROM    {$wpdb->prefix}comments
					WHERE   comment_post_ID = $product_id
					AND     comment_author_email = '$user_email'
					";

			//phpcs:ignore
			$count = $wpdb->get_var( $sql );
		}

		return $count > 0 ? true : false;

	}
}

if ( ! function_exists( 'ywrr_items_has_comments_opened' ) ) {

	/**
	 * Check if product has reviews enabled
	 *
	 * @param integer $product_id The product ID.
	 *
	 * @return  boolean
	 * @since   1.6.0
	 */
	function ywrr_items_has_comments_opened( $product_id ) {
		/**
		 * APPLY_FILTERS: ywrr_comment_status
		 *
		 * Check if comments are opened for a specific product.
		 *
		 * @param boolean $comment_status Value to check if comments are opened for that product.
		 *
		 * @return boolean
		 */
		return apply_filters( 'ywrr_comment_status', comments_open( $product_id ) );

	}
}

if ( ! function_exists( 'ywrr_check_reviewable_items' ) ) {

	/**
	 * Check if order has reviewable items
	 *
	 * @param integer $post_id The post ID.
	 *
	 * @return  integer
	 * @since   1.6.0
	 */
	function ywrr_check_reviewable_items( $post_id ) {

		$order            = wc_get_order( $post_id );
		$order_items      = $order->get_items();
		$reviewable_items = 0;

		foreach ( $order_items as $item ) {

			if ( ! ywrr_skip_product( $item['product_id'], $order->get_billing_email() ) ) {

				$reviewable_items++;

			}
		}

		return $reviewable_items;

	}
}

if ( ! function_exists( 'ywrr_format_date' ) ) {

	/**
	 * Format email date
	 *
	 * @param string $date The date to format.
	 *
	 * @return  string
	 * @since   1.2.3
	 */
	function ywrr_format_date( $date ) {

		/**
		 * Y = YEAR
		 * m = month
		 * d = day
		 * H = hour
		 * i = minutes
		 * s = seconds
		 */
		/**
		 * APPLY_FILTERS: ywrr_custom_date_format
		 *
		 * Sets date format.
		 *
		 * @param string $date_format The date format.
		 *
		 * @return string
		 */
		$date_format = apply_filters( 'ywrr_custom_date_format', 'Y-m-d H:i:s' );

		try {
			$date_time      = new DateTime( $date );
			$formatted_date = $date_time->format( $date_format );
		} catch ( Exception $e ) {
			$formatted_date = $date;
		}

		return $formatted_date;

	}
}

if ( ! function_exists( 'ywrr_update_1_6_0' ) ) {

	/**
	 * Add add_new column to schedule table
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_update_1_6_0() {

		$ywrr_db_option = get_option( 'ywrr_db_version_ext' );

		if ( empty( $ywrr_db_option ) || version_compare( $ywrr_db_option, YWRR_DB_VERSION_EXT, '<' ) ) {

			global $wpdb;

			$sql = "ALTER TABLE {$wpdb->prefix}ywrr_email_schedule ADD mail_type varchar(100) NOT NULL DEFAULT 'order'";
			//phpcs:ignore
			$wpdb->query( $sql );
		}
	}
}

if ( ! function_exists( 'ywrr_mail_options' ) ) {

	/**
	 * Check if current user is a vendor
	 *
	 * @param boolean $wc_settings Check if WC settings are rendered.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_mail_options( $wc_settings = false ) {

		$types = array( 'plain' => esc_html__( 'Plain text', 'woocommerce' ) );

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html']      = esc_html__( 'HTML', 'woocommerce' );
			$types['multipart'] = esc_html__( 'Multipart', 'woocommerce' );
		}

		$form_fields ['ywrr_mail_subject'] = array(
			'title'       => esc_html__( 'Email subject', 'yith-woocommerce-review-reminder' ),
			'type'        => 'text',
			/* translators: %s placeholders */
			'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-review-reminder' ), '<code>{site_title}</code>' ),
			'id'          => 'ywrr_mail_subject',
			'default'     => esc_html__( '[{site_title}] Review recently purchased products', 'yith-woocommerce-review-reminder' ),
			'desc_tip'    => true,
		);
		$form_fields ['ywrr_mail_body']    = array(
			'title'       => esc_html__( 'Email body', 'yith-woocommerce-review-reminder' ),
			'type'        => 'textarea',
			/* translators: %s placeholders */
			'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-review-reminder' ), '<code>{site_title}, {customer_name}, {customer_email}, {order_id}, {order_date}, {order_date_completed}, {order_list}, {days_ago}, {unsubscribe_link}</code>' ),
			'id'          => 'ywrr_mail_body',
			'default'     => esc_html__(
				'Hello {customer_name},
Thank you for purchasing items from the {site_title} shop!
We would love if you could help us and other customers by reviewing the products you recently purchased.
It only takes a minute and it would really help others by giving them an idea of your experience.
Click the link below for each product and review the product under the \'Reviews\' tab.

{order_list}

Much appreciated,

{site_title}.


{unsubscribe_link}',
				'yith-woocommerce-review-reminder'
			),
			'css'         => 'resize: vertical; width: 100%; min-height: 40px; height:200px',
			'desc_tip'    => true,
		);

		if ( defined( 'YITH_WCBK_PREMIUM' ) && YITH_WCBK_PREMIUM && ( defined( 'YITH_WCBK_VERSION' ) && version_compare( YITH_WCBK_VERSION, '2.1.9', '>=' ) ) ) {
			$form_fields ['ywrr_mail_body_booking'] = array(
				'title'       => esc_html__( 'Email body (for booking products)', 'yith-woocommerce-review-reminder' ),
				'type'        => 'textarea',
				/* translators: %s placeholders */
				'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-review-reminder' ), '<code>{site_title}, {customer_name}, {customer_email}, {order_id}, {order_date}, {order_date_completed}, {order_list}, {days_ago}, {unsubscribe_link}</code>' ),
				'id'          => 'ywrr_mail_body_booking',
				'default'     => esc_html__(
					'Hello {customer_name},
Thank you for booking on {site_title}!
We hope you had a great time and would appreciate your help by reviewing the booking you recently purchased.
It only takes a minute and you would really help other customers through your experience.
Click the link below and leave your review under the \'Reviews\' tab.

{order_list}

Much appreciated,


{site_title}.


{unsubscribe_link}',
					'yith-woocommerce-review-reminder'
				),
				'css'         => 'resize: vertical; width: 100%; min-height: 40px; height:200px',
				'desc_tip'    => true,
			);
		}

		$form_fields ['ywrr_mail_type']             = array(
			'title'       => esc_html__( 'Email type', 'yith-woocommerce-review-reminder' ),
			'type'        => 'select',
			'default'     => 'html',
			'id'          => 'ywrr_mail_type',
			'description' => esc_html__( 'Choose which format of email to send.', 'yith-woocommerce-review-reminder' ),
			'class'       => 'email_type wc-enhanced-select',
			'options'     => $types,
			'desc_tip'    => true,
		);
		$form_fields ['ywrr_mail_unsubscribe_text'] = array(
			'title'       => esc_html__( 'Review unsubscription text', 'yith-woocommerce-review-reminder' ),
			'type'        => 'text',
			'default'     => esc_html__( 'Unsubscribe from review emails', 'yith-woocommerce-review-reminder' ),
			'id'          => 'ywrr_mail_unsubscribe_text',
			'description' => esc_html__( 'The text of the unsubscribe link.', 'yith-woocommerce-review-reminder' ),
			'desc_tip'    => true,
		);

		if ( ! $wc_settings ) {

			foreach ( $form_fields as $key => $field ) {

				$form_fields[ $key ]['yith-type'] = $field['type'];
				$form_fields[ $key ]['type']      = 'yith-field';
				$form_fields[ $key ]['desc']      = $field['description'];
				unset( $form_fields[ $key ]['description'] );
				unset( $form_fields[ $key ]['desc_tip'] );

			}
		}

		return $form_fields;

	}
}

if ( ! function_exists( 'ywrr_log_unscheduled_email' ) ) {

	/**
	 * Write in the schedule list an unscheduled mail when it is sent
	 *
	 * @param WC_Order $order           The Order.
	 * @param integer  $booking_id      The booking ID.
	 * @param array    $items_to_review The list of items to review.
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_log_unscheduled_email( $order, $booking_id = null, $items_to_review = array() ) {

		$was_quote = false;

		if ( function_exists( 'YITH_YWRAQ_Order_Request' ) ) {
			$was_quote = YITH_YWRAQ_Order_Request()->is_quote( $order->get_id() );
		}

		if ( wp_get_post_parent_id( $order->get_id() ) && ! $was_quote ) {
			return;
		}

		global $wpdb;

		$order_date = $order->get_date_modified();

		if ( ! $order_date ) {
			$order_date = $order->get_date_created();
		}

		//phpcs:ignore
		$wpdb->insert(
			$wpdb->prefix . 'ywrr_email_schedule',
			array(
				'order_id'       => $order->get_id(),
				'mail_status'    => 'sent',
				'scheduled_date' => gmdate( 'Y-m-d', strtotime( current_time( 'mysql' ) ) ),
				'order_date'     => gmdate( 'Y-m-d', yit_datetime_to_timestamp( $order_date ) ),
				'request_items'  => ! empty( $items_to_review ) ? maybe_serialize( $items_to_review ) : '',
				'mail_type'      => ( $booking_id ? 'booking-' . $booking_id : 'order' ),
			),
			array( '%d', '%s', '%s', '%s', '%s', '%s' )
		);

	}
}

if ( ! function_exists( 'ywrr_check_ywcet_active' ) ) {

	/**
	 * Check if YITH WooCommerce Email Templates
	 *
	 * @return boolean
	 * @since   1.6.0
	 */
	function ywrr_check_ywcet_active() {
		return defined( 'YITH_WCET_PREMIUM' ) && YITH_WCET_PREMIUM;
	}
}

/**
 * MULTIVENDOR 4.0 COMPATIBILITY
 */

if ( ! function_exists( 'ywrr_multivendor_enabled' ) ) {

	/**
	 * Check if Multi Vendor plugin is enabled
	 *
	 * @return boolean
	 * @since  1.18.0
	 */
	function ywrr_multivendor_enabled() {
		return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;
	}
}

if ( ! function_exists( 'ywrr_vendor_check' ) ) {

	/**
	 * Check if current user is a vendor
	 *
	 * @return  boolean
	 * @since   1.6.0
	 */
	function ywrr_vendor_check() {

		$is_vendor = false;

		if ( ywrr_multivendor_enabled() ) {
			$vendor    = yith_wcmv_get_vendor( 'current', 'user' );
			$is_vendor = ( 0 !== $vendor->get_id() );
		}

		return $is_vendor;

	}
}

if ( ! function_exists( 'ywrr_get_suborders' ) ) {

	/**
	 * Get Suborders
	 *
	 * @param integer $order_id The Order ID.
	 *
	 * @return array
	 * @since  1.18.0
	 */
	function ywrr_get_suborders( $order_id ) {
		return YITH_Vendors_Orders::get_suborders( $order_id );
	}
}

/**
 * EMAIL RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_send_email' ) ) {

	/**
	 * Prepares and send the review request mail
	 *
	 * @param integer $order_id        The order ID.
	 * @param integer $days            Days passed.
	 * @param array   $items_to_review The list of items to review.
	 * @param array   $stored_items    Stored items.
	 * @param string  $type            Email type.
	 *
	 * @return  boolean
	 * @since   1.0.0
	 */
	function ywrr_send_email( $order_id, $days, $items_to_review = array(), $stored_items = array(), $type = 'order' ) {

		$list  = array();
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return false;
		}

		$is_funds    = $order->get_meta( '_order_has_deposit' ) === 'yes';
		$is_deposits = $order->get_created_via() === 'yith_wcdp_balance_order';

		$customer_id    = $order->get_user_id();
		$customer_email = $order->get_billing_email();

		if ( ! ywrr_check_blocklist( $customer_id, $customer_email ) ) {
			ywrr_unschedule_mail( $order_id );

			return false;
		}

		/**
		 * APPLY_FILTERS: ywrr_skip_renewal_orders
		 *
		 * Check if plugin should skip subscription renewal orders.
		 *
		 * @param boolean $value Value to check if renewals should be skipped.
		 *
		 * @return boolean
		 */
		$is_renew = $order->get_meta( 'is_a_renew' ) === 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );

		if ( ! $is_funds && ! $is_deposits && ! $is_renew ) {

			if ( ! empty( $stored_items ) ) {
				$list = $stored_items;
			} else {
				if ( ! empty( $items_to_review ) ) {
					$list = ywrr_get_review_list_forced( $items_to_review, $order_id );
				}
			}

			if ( empty( $list ) ) {
				$list = ywrr_get_review_list( $order_id );
			}
		}

		if ( empty( $list ) ) {
			return esc_html__( 'There are no reviewable items in this order', 'yith-woocommerce-review-reminder' );
		}

		$mail_args = array(
			'order_id'   => $order_id,
			'item_list'  => $list,
			'days_ago'   => $days,
			'test_email' => '',
			'template'   => false,
			'type'       => $type,
		);

		return apply_filters( 'send_ywrr_mail', $mail_args );

	}
}

if ( ! function_exists( 'ywrr_get_review_list' ) ) {

	/**
	 * Prepares the list of items to review from stored options
	 *
	 * @param integer $order_id The order ID.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_get_review_list( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return array();
		}

		$user_email = $order->get_billing_email();
		$criteria   = ( get_option( 'ywrr_request_type', 'all' ) ) !== 'all' ? get_option( 'ywrr_request_criteria' ) : 'default';
		$items      = call_user_func( 'ywrr_criteria_' . $criteria, $order, $user_email );

		return $items;

	}
}

if ( ! function_exists( 'ywrr_criteria_default' ) ) {

	/**
	 * Get all products in the order that can be reviewed
	 *
	 * @param WC_Order $order      The Order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_default( $order, $user_email ) {

		$items = array();

		foreach ( $order->get_items() as $item ) {

			$product_id = $item->get_data()['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item->get_data()['name'];
			$items[ $product_id ]['id']   = $product_id;

		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_skip_product' ) ) {

	/**
	 * Check if product can be reviewed
	 *
	 * @param integer $product_id The product ID.
	 * @param string  $user_email The user email.
	 *
	 * @return  boolean
	 * @since   1.6.0
	 */
	function ywrr_skip_product( $product_id, $user_email ) {
		/**
		 * APPLY_FILTERS: ywrr_excluded_items
		 *
		 * Get list of excluded items.
		 *
		 * @param array   $excluded_items The list of excluded items.
		 * @param integer $product_id     The product ID.
		 *
		 * @return array
		 */
		$excluded_items = apply_filters( 'ywrr_excluded_items', array(), $product_id );

		return ( ! ywrr_items_has_comments_opened( $product_id ) || ywrr_user_has_commented( $product_id, $user_email ) || in_array( $product_id, $excluded_items, true ) );
	}
}

if ( ! function_exists( 'ywrr_email_styles' ) ) {

	/**
	 * Get email styles
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	function ywrr_email_styles() {

		?>

		.ywrr-table {
		border: none;
		}

		.ywrr-table td {
		border: none;
		border-bottom: 1px solid #e0e7f0;
		text-align: left;
		vertical-align: top;
		padding: 10px 0!important;
		}

		.ywrr-table td.picture-column {
		width: 135px;
		padding: 10px 20px 10px 0 !important;
		}

		.ywrr-table td.picture-column a {
		display: block;
		}

		.ywrr-table td.picture-column a img {
		margin: 0!important;
		max-width: 135px;
		}

		.ywrr-table td.title-column a {
		font-size: 16px;
		font-weight: bold!important;
		text-decoration: none;
		display: block:
		}

		.ywrr-table td.title-column a .stars{
		display: block;
		font-size: 11px;
		color: #6e6e6e;
		text-transform: uppercase:
		}

		<?php
	}
}

if ( ! function_exists( 'ywrr_email_items_list' ) ) {

	/**
	 * Set the list item for the selected template.
	 *
	 * @param array   $item_list   The list of items to review.
	 * @param integer $customer_id The customer ID.
	 *
	 * @return  string
	 * @since   1.6.0
	 */
	function ywrr_email_items_list( $item_list, $customer_id ) {

		$style = wc_get_template_html(
			'emails/email-items-list.php',
			array(
				'item_list'   => $item_list,
				'customer_id' => $customer_id,
			),
			false,
			YWRR_TEMPLATE_PATH
		);

		return $style;

	}
}

if ( ! function_exists( 'ywrr_get_templates' ) ) {

	/**
	 * Gets the email templates available
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_get_templates() {
		/**
		 * APPLY_FILTERS: ywrr_email_templates
		 *
		 * Get list of mail templates.
		 *
		 * @param array $date_format The list of mail templates.
		 *
		 * @return array
		 */
		return apply_filters( 'ywrr_email_templates', array() );
	}
}

if ( ! function_exists( 'ywrr_get_review_list_forced' ) ) {

	/**
	 * Prepares the list of items from selected items in order page
	 *
	 * @param array   $items_to_review The list of items to review.
	 * @param integer $order_id        The order ID.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_get_review_list_forced( $items_to_review, $order_id ) {

		$items       = array();
		$order       = wc_get_order( $order_id );
		$order_items = $order->get_items();

		foreach ( $items_to_review as $item ) {

			try {

				$product_id = wc_get_order_item_meta( $item, '_product_id' );

				if ( ywrr_items_has_comments_opened( $product_id ) ) {

					$items[ $product_id ]['name'] = $order_items[ $item ]['name'];
					$items[ $product_id ]['id']   = $product_id;

				}
			} catch ( Exception $e ) {
				return array();
			}
		}

		return $items;

	}
}

if ( ! function_exists( 'ywrr_criteria_first' ) ) {

	/**
	 * Get the first X items in the order that can be reviewed
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_first( $order, $user_email ) {

		$items  = array();
		$amount = get_option( 'ywrr_request_number', 1 );
		$count  = 0;

		foreach ( $order->get_items() as $item ) {

			$product_id = $item->get_data()['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item->get_data()['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_criteria_last' ) ) {

	/**
	 * Get the last X items in the order that can be reviewed
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_last( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array_reverse( $order->get_items() );

		foreach ( $order_items as $item ) {

			$product_id = $item->get_data()['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item->get_data()['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_criteria_random' ) ) {

	/**
	 * Get X random items in the order that can be reviewed
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_random( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = $order->get_items();
		shuffle( $order_items );

		foreach ( $order_items as $item ) {

			$product_id = $item->get_data()['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item->get_data()['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_criteria_highest_quantity' ) ) {

	/**
	 * Get the last X items in the order that can be reviewed ordered by quantity
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_highest_quantity( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort(
			$order_items,
			function ( $a, $b ) {
				return $b['quantity'] - $a['quantity'];
			}
		);

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_criteria_lowest_quantity' ) ) {

	/**
	 * Get the last X items in the order that can be reviewed ordered by quantity
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_lowest_quantity( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort(
			$order_items,
			function ( $a, $b ) {
				return $a['quantity'] - $b['quantity'];
			}
		);

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_criteria_highest_priced' ) ) {

	/**
	 * Get the first X items in the order that can be reviewed ordered by price
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_highest_priced( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort(
			$order_items,
			function ( $a, $b ) {
				return ( $b['subtotal'] / $b['quantity'] ) - ( $a['subtotal'] / $a['quantity'] );
			}
		);

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_criteria_lowest_priced' ) ) {

	/**
	 * Get the last X items in the order that can be reviewed ordered by price
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_lowest_priced( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort(
			$order_items,
			function ( $a, $b ) {
				return ( $a['subtotal'] / $a['quantity'] ) - ( $b['subtotal'] / $b['quantity'] );
			}
		);

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_criteria_highest_total_value' ) ) {

	/**
	 * Get the first X items in the order that can be reviewed ordered by subtotal
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_highest_total_value( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort(
			$order_items,
			function ( $a, $b ) {
				return $b['subtotal'] - $a['subtotal'];
			}
		);

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_criteria_lowest_total_value' ) ) {

	/**
	 * Get the last X items in the order that can be reviewed ordered by subtotal
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_lowest_total_value( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort(
			$order_items,
			function ( $a, $b ) {
				return $a['subtotal'] - $b['subtotal'];
			}
		);

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_criteria_most_reviewed' ) ) {

	/**
	 * Get the first X items in the order that can be reviewed ordered by number of reviews
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_most_reviewed( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {

			$item_data     = $item->get_data();
			$product       = wc_get_product( $item_data['product_id'] );
			$review_count  = array( 'reviews' => $product->get_review_count() );
			$item_data     = array_merge( $item_data, $review_count );
			$order_items[] = $item_data;
		}

		usort(
			$order_items,
			function ( $a, $b ) {
				return $b['reviews'] - $a['reviews'];
			}
		);

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_criteria_least_reviewed' ) ) {

	/**
	 * Get the last X items in the order that can be reviewed ordered by number of reviews
	 *
	 * @param WC_Order $order      The order.
	 * @param string   $user_email The user email.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_criteria_least_reviewed( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {

			$item_data     = $item->get_data();
			$product       = wc_get_product( $item_data['product_id'] );
			$review_count  = array( 'reviews' => $product->get_review_count() );
			$item_data     = array_merge( $item_data, $review_count );
			$order_items[] = $item_data;
		}

		usort(
			$order_items,
			function ( $a, $b ) {
				return $a['reviews'] - $b['reviews'];
			}
		);

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count++;
			if ( $count === $amount ) {
				break;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'ywrr_mandrill_send' ) ) {

	/**
	 * Send the email using Mandrill
	 *
	 * @param string $to          The receiving address.
	 * @param string $subject     The email subject.
	 * @param string $message     The message body.
	 * @param string $headers     The email headers.
	 * @param array  $attachments The attachments.
	 *
	 * @return  boolean
	 * @throws  Mandrill_Exception The exception coming from Mandrill.
	 * @since   1.6.0
	 */
	function ywrr_mandrill_send( $to, $subject, $message, $headers = '', $attachments = array() ) {

		if ( ! class_exists( 'Mandrill' ) ) {
			require_once YWRR_DIR . 'includes/third-party/Mandrill.php';
		}

		$from_name = wp_specialchars_decode( esc_html( get_option( 'woocommerce_email_from_name' ) ), ENT_QUOTES );

		if ( ! isset( $from_name ) ) {
			$from_name = 'WordPress';
		}

		$from_email = sanitize_email( get_option( 'woocommerce_email_from_address' ) );

		if ( ! isset( $from_email ) ) {
			// Get the site domain and get rid of www.
			$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
			if ( 'www.' === substr( $sitename, 0, 4 ) ) {
				$sitename = substr( $sitename, 4 );
			}

			$from_email = 'wordpress@' . $sitename;
		}

		$api_key = get_option( 'ywrr_mandrill_apikey' );

		$headers_array = explode( '\r\n', $headers );

		$headers = array();

		foreach ( $headers_array as $item ) {

			$headers_row = explode( ': ', $item );

			$headers[ $headers_row[0] ] = $headers_row[1];

		}

		try {
			$mandrill = new Mandrill( $api_key );
			$message  = apply_filters(
				'ywrr_mandrill_send_mail_message',
				array(
					'html'        => $message,
					'subject'     => $subject,
					'from_email'  => apply_filters( 'wp_mail_from', $from_email ),
					'from_name'   => apply_filters( 'wp_mail_from_name', $from_name ),
					'to'          => array(
						array(
							'email' => $to,
							'type'  => 'to',
						),
					),
					'headers'     => $headers,
					'attachments' => $attachments,
				)
			);

			$async   = apply_filters( 'ywrr_mandrill_send_mail_async', false );
			$ip_pool = apply_filters( 'ywrr_mandrill_send_mail_ip_pool', null );
			$send_at = apply_filters( 'ywrr_mandrill_send_mail_send_at', null );

			$results = $mandrill->messages->send( $message, $async, $ip_pool, $send_at );
			$return  = true;

			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {
					if ( ! isset( $result['status'] ) || in_array( $result['status'], array( 'rejected', 'invalid' ), true ) ) {
						$return = false;
					}
				}
			}

			return $return;
		} catch ( Mandrill_Error $e ) {
			return false;
		}

	}
}

if ( ! function_exists( 'ywrr_get_premium_templates' ) ) {

	/**
	 * Get premium email templates
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_get_premium_templates() {

		return array(
			'premium-1',
			'premium-2',
			'premium-3',
		);
	}

	add_filter( 'ywrr_email_templates', 'ywrr_get_premium_templates' );

}

if ( ! function_exists( 'ywrr_check_hash' ) ) {

	/**
	 * Get the custom hash for email links
	 *
	 * @param string $link_hash The link hash.
	 *
	 * @return  string
	 * @since   1.6.0
	 */
	function ywrr_check_hash( $link_hash ) {

		if ( '' !== $link_hash && substr( $link_hash, 0, 1 ) !== '#' ) {
			$link_hash = '#' . $link_hash;
		}

		return $link_hash;

	}
}

/**
 * SCHEDULE RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_schedule_mail' ) ) {

	/**
	 * Create a schedule record
	 *
	 * @param integer      $order_id    Tte order ID.
	 * @param string|array $forced_list The list of items.
	 *
	 * @return  string
	 * @since   1.6.0
	 */
	function ywrr_schedule_mail( $order_id, $forced_list = '' ) {

		$was_quote = false;

		if ( function_exists( 'YITH_YWRAQ_Order_Request' ) ) {
			$was_quote = YITH_YWRAQ_Order_Request()->is_quote( $order_id );
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return esc_html__( 'This email cannot be scheduled', 'yith-woocommerce-review-reminder' );
		}

		$customer_id    = $order->get_user_id();
		$customer_email = $order->get_billing_email();

		if ( ywrr_check_blocklist( $customer_id, $customer_email ) !== true ) {
			return esc_html__( 'This email cannot be scheduled', 'yith-woocommerce-review-reminder' );
		}

		if ( ( ! wp_get_post_parent_id( $order_id ) || ( wp_get_post_parent_id( $order_id ) && $was_quote ) ) && (int) ywrr_check_exists_schedule( $order_id ) === 0 ) {

			$forced_list = maybe_serialize( $forced_list );

			if ( '' === $forced_list ) {

				$list        = array();
				$is_funds    = $order->get_meta( '_order_has_deposit' ) === 'yes';
				$is_deposits = $order->get_created_via() === 'yith_wcdp_balance_order';
				/**
				 * APPLY_FILTERS: ywrr_skip_renewal_orders
				 *
				 * Check if plugin should skip subscription renewal orders.
				 *
				 * @param boolean $value Value to check if renewals should be skipped.
				 *
				 * @return boolean
				 */
				$is_renew = $order->get_meta( 'is_a_renew' ) === 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );
				/**
				 * APPLY_FILTERS: ywrr_can_ask_for_review
				 *
				 * Check if plugin can ask for a review.
				 *
				 * @param boolean  $value Value to check if the review can be asked.
				 * @param WC_Order $order The order to check.
				 *
				 * @return boolean
				 */
				$can_ask_review = apply_filters( 'ywrr_can_ask_for_review', true, $order );

				if ( ! $is_funds && ! $is_deposits && ! $is_renew && $can_ask_review ) {
					$list = ywrr_get_review_list( $order_id );
				}

				if ( empty( $list ) ) {
					return esc_html__( 'There are no reviewable items in this order', 'yith-woocommerce-review-reminder' );
				}

				$forced_list = maybe_serialize( $list );

			}

			if ( (int) ywrr_check_reviewable_items( $order_id ) === 0 && $was_quote ) {
				return esc_html__( 'There are no reviewable items in this order', 'yith-woocommerce-review-reminder' );
			}

			global $wpdb;

			$scheduled_date = gmdate( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + ' . get_option( 'ywrr_mail_schedule_day' ) . ' days' ) );
			$order_date     = $order->get_date_modified();

			if ( ! $order_date ) {
				$order_date = $order->get_date_created();
			}

			//phpcs:ignore
			$wpdb->insert(
				$wpdb->prefix . 'ywrr_email_schedule',
				array(
					'order_id'       => $order_id,
					'mail_status'    => 'pending',
					'scheduled_date' => $scheduled_date,
					'order_date'     => gmdate( 'Y-m-d', yit_datetime_to_timestamp( $order_date ) ),
					'request_items'  => $forced_list,
				),
				array( '%d', '%s', '%s', '%s', '%s' )
			);

			return '';
		}

		return esc_html__( 'This email cannot be scheduled', 'yith-woocommerce-review-reminder' );

	}

	add_action( 'woocommerce_order_status_completed', 'ywrr_schedule_mail' );

}

if ( ! function_exists( 'ywrr_check_exists_schedule' ) ) {

	/**
	 * Checks if order has a scheduled email
	 *
	 * @param integer $order_id   The order ID.
	 * @param integer $booking_id The booking ID.
	 *
	 * @return  integer
	 * @since   1.6.0
	 */
	function ywrr_check_exists_schedule( $order_id, $booking_id = null ) {

		$was_quote = false;

		if ( function_exists( 'YITH_YWRAQ_Order_Request' ) ) {
			$was_quote = YITH_YWRAQ_Order_Request()->is_quote( $order_id );
		}

		if ( wp_get_post_parent_id( $order_id ) && ! $was_quote ) {
			return 0;
		}

		global $wpdb;

		$mail_type = ( $booking_id ? 'booking-' . $booking_id : 'order' );

		$query = $wpdb->prepare(
			"
                    SELECT    COUNT(*)
                    FROM      {$wpdb->prefix}ywrr_email_schedule
                    WHERE     order_id = %d
                    AND 	  mail_type = %s
                    ",
			array( $order_id, $mail_type )
		);

		//phpcs:ignore
		$count = $wpdb->get_var( $query );

		return $count;
	}
}

if ( ! function_exists( 'ywrr_change_schedule_status' ) ) {

	/**
	 * Changes email schedule status
	 *
	 * @param integer $order_id   The order ID.
	 * @param string  $status     The schedule status.
	 * @param integer $booking_id The booking ID.
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_change_schedule_status( $order_id, $status = 'cancelled', $booking_id = null ) {

		$was_quote = false;

		if ( function_exists( 'YITH_YWRAQ_Order_Request' ) ) {
			$was_quote = YITH_YWRAQ_Order_Request()->is_quote( $order_id );
		}

		if ( wp_get_post_parent_id( $order_id ) && ! $was_quote ) {
			return;
		}

		global $wpdb;

		$mail_type = ( $booking_id ? 'booking-' . $booking_id : 'order' );
		//phpcs:ignore
		$wpdb->update(
			$wpdb->prefix . 'ywrr_email_schedule',
			array(
				'mail_status'    => $status,
				'scheduled_date' => gmdate( 'Y-m-d', strtotime( current_time( 'mysql' ) ) ),
			),
			array(
				'order_id'  => $order_id,
				'mail_type' => $mail_type,
			),
			array( '%s' ),
			array( '%d', '%s' )
		);

	}
}

if ( ! function_exists( 'ywrr_daily_schedule' ) ) {

	/**
	 * Handles the daily mail sending
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_daily_schedule() {
		global $wpdb;

		//phpcs:ignore
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"
                    SELECT    COUNT(*)
                    FROM      {$wpdb->prefix}ywrr_email_schedule
                    WHERE     mail_status = 'pending' AND scheduled_date <= %s
                    ",
				current_time( 'mysql' )
			)
		);

		$number = ceil( $count / 24 );

		update_option( 'ywrr_hourly_send_number', $number );

	}

	add_action( 'ywrr_daily_send_mail_job', 'ywrr_daily_schedule' );

}

if ( ! function_exists( 'ywrr_hourly_schedule' ) ) {

	/**
	 * Handles the hourly mail sending
	 *
	 * @return  void
	 * @throws  Exception The exception.
	 * @since   1.6.0
	 */
	function ywrr_hourly_schedule() {
		global $wpdb;

		$number = get_option( 'ywrr_hourly_send_number', 10 );

		//phpcs:ignore
		$orders = $wpdb->get_results(
			$wpdb->prepare(
				"
							SELECT  	order_id,
										order_date,
										request_items,
										mail_type
							FROM    	{$wpdb->prefix}ywrr_email_schedule
							WHERE		mail_status = 'pending' AND scheduled_date <= %s
							ORDER BY    id DESC
							LIMIT		%d
							",
				array( current_time( 'mysql' ), $number )
			)
		);

		foreach ( $orders as $item ) {
			$list         = maybe_unserialize( $item->request_items );
			$today        = new DateTime( current_time( 'mysql' ) );
			$pay_date     = new DateTime( $item->order_date );
			$days         = $pay_date->diff( $today );
			$type         = 'order' === $item->mail_type ? 'order' : 'booking';
			$email_result = ywrr_send_email( $item->order_id, $days->days, array(), $list, $type );

			if ( $email_result ) {
				$booking_id = 'order' === $item->mail_type ? '' : str_replace( 'booking-', '', $item->mail_type );
				ywrr_change_schedule_status( $item->order_id, 'sent', $booking_id );
			}
		}

	}

	add_action( 'ywrr_hourly_send_mail_job', 'ywrr_hourly_schedule' );

}

if ( ! function_exists( 'ywrr_on_order_deletion' ) ) {

	/**
	 * Removes from schedule list if order is deleted
	 *
	 * @param integer $post_id The post ID.
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_on_order_deletion( $post_id ) {

		global $wpdb;

		//phpcs:ignore
		$wpdb->delete(
			$wpdb->prefix . 'ywrr_email_schedule',
			array( 'order_id' => $post_id ),
			array( '%d' )
		);

	}

	add_action( 'trashed_post', 'ywrr_on_order_deletion' );
	add_action( 'after_delete_post', 'ywrr_on_order_deletion' );

}

if ( ! function_exists( 'ywrr_reschedule' ) ) {

	/**
	 * Reschedule the mail sending
	 *
	 * @param integer      $order_id       The order ID.
	 * @param string       $scheduled_date The schedule date.
	 * @param string|array $forced_list    The list of items.
	 *
	 * @return  string
	 * @since   1.6.0
	 */
	function ywrr_reschedule( $order_id, $scheduled_date, $forced_list = '' ) {
		$was_quote = false;

		if ( function_exists( 'YITH_YWRAQ_Order_Request' ) ) {
			$was_quote = YITH_YWRAQ_Order_Request()->is_quote( $order_id );
		}

		if ( ! wp_get_post_parent_id( $order_id ) || ( wp_get_post_parent_id( $order_id ) && $was_quote ) ) {

			$forced_list = maybe_serialize( $forced_list );

			if ( '' === $forced_list ) {

				$list        = array();
				$order       = wc_get_order( $order_id );
				$is_funds    = $order->get_meta( '_order_has_deposit' ) === 'yes';
				$is_deposits = $order->get_created_via() === 'yith_wcdp_balance_order';
				/**
				 * APPLY_FILTERS: ywrr_skip_renewal_orders
				 *
				 * Check if plugin should skip subscription renewal orders.
				 *
				 * @param boolean $value Value to check if renewals should be skipped.
				 *
				 * @return boolean
				 */
				$is_renew = $order->get_meta( 'is_a_renew' ) === 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );

				if ( ! $is_funds && ! $is_deposits && ! $is_renew ) {
					$list = ywrr_get_review_list( $order_id );
				}

				if ( empty( $list ) ) {
					return esc_html__( 'There are no reviewable items in this order', 'yith-woocommerce-review-reminder' );
				}
			}

			global $wpdb;

			//phpcs:ignore
			$wpdb->update(
				$wpdb->prefix . 'ywrr_email_schedule',
				array(
					'scheduled_date' => $scheduled_date,
					'mail_status'    => 'pending',
					'request_items'  => $forced_list,
				),
				array( 'order_id' => $order_id ),
				array( '%s' ),
				array( '%d' )
			);

			return '';

		}

		return esc_html__( 'This email cannot be rescheduled', 'yith-woocommerce-review-reminder' );

	}
}

if ( ! function_exists( 'ywrr_set_mass_reschedule' ) ) {

	/**
	 * Check if scheduled emails should be rescheduled
	 *
	 * @param array $option The option array.
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_set_mass_reschedule( $option ) {

		$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( 'ywrr_mail_reschedule' === $option['id'] && isset( $posted[ $option['id'] ] ) && ( '1' === $posted[ $option['id'] ] || 'yes' === $posted[ $option['id'] ] ) ) {

			if ( get_option( 'ywrr_mail_schedule_day' ) !== $posted['ywrr_mail_schedule_day'] || get_option( 'ywrr_request_type' ) !== $posted['ywrr_request_type'] || get_option( 'ywrr_request_number' ) !== $posted['ywrr_request_number'] || get_option( 'ywrr_request_criteria' ) !== $posted['ywrr_request_criteria'] ) {

				update_option( 'ywrr_must_reschedule', 'yes' );

			}
		}

	}

	add_action( 'woocommerce_update_option', 'ywrr_set_mass_reschedule', 10, 1 );

}

if ( ! function_exists( 'ywrr_mass_reschedule' ) ) {
	/**
	 * Handles mass reschedule of email after options changes
	 *
	 * @return  void
	 * @throws  Exception The exception.
	 * @since   1.6.0
	 */
	function ywrr_mass_reschedule() {

		if ( get_option( 'ywrr_must_reschedule' ) === 'yes' ) {

			global $wpdb;
			$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing

			$new_interval = $posted['ywrr_mail_schedule_day'];
			$query        = "
							SELECT  order_id,
									order_date,
									request_items,
									mail_type
							FROM	{$wpdb->prefix}ywrr_email_schedule
							WHERE	mail_status = 'pending'
							";
			//phpcs:ignore
			$orders = $wpdb->get_results( $query );

			foreach ( $orders as $item ) {
				$new_scheduled_date = gmdate( 'Y-m-d', strtotime( $item->order_date . ' + ' . $new_interval . ' days' ) );
				$list               = 'order' === $item->mail_type ? maybe_serialize( ywrr_get_review_list( $item->order_id ) ) : $item->request_items;

				//phpcs:ignore
				$wpdb->update(
					$wpdb->prefix . 'ywrr_email_schedule',
					array(
						'scheduled_date' => $new_scheduled_date,
						'request_items'  => $list,
					),
					array( 'order_id' => $item->order_id ),
					array( '%s' ),
					array( '%d' )
				);

				if ( isset( $posted['ywrr_mail_send_rescheduled'] ) && '1' === $posted['ywrr_mail_send_rescheduled'] ) {

					$list = maybe_unserialize( $item->request_items );

					$today     = new DateTime( current_time( 'mysql' ) );
					$send_date = new DateTime( $new_scheduled_date );
					$pay_date  = new DateTime( $item->order_date );
					$days      = $pay_date->diff( $today );

					if ( $send_date <= $today ) {
						$booking_id   = 'order' === $item->mail_type ? '' : str_replace( 'booking-', '', $item->mail_type );
						$type         = 'order' === $item->mail_type ? 'order' : 'booking';
						$email_result = ywrr_send_email( $item->order_id, $days->days, array(), $list, $type );

						if ( $email_result ) {

							ywrr_change_schedule_status( $item->order_id, 'sent', $booking_id );

						}
					}
				}
			}

			delete_option( 'ywrr_must_reschedule' );

		}

	}

	add_action( 'yit_panel_wc_after_update', 'ywrr_mass_reschedule' );
}

if ( ! function_exists( 'ywrr_unschedule_mail' ) ) {

	/**
	 * Cancel schedule mail when order is cancelled
	 *
	 * @param integer $order_id The order ID.
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_unschedule_mail( $order_id ) {

		if ( (int) ywrr_check_exists_schedule( $order_id ) !== 0 ) {

			ywrr_change_schedule_status( $order_id );

		}

	}

	add_action( 'woocommerce_order_status_cancelled', 'ywrr_unschedule_mail' );
	add_action( 'woocommerce_order_status_refunded', 'ywrr_unschedule_mail' );

}

/**
 * GDPR RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_register_scheduled_reminders_exporter' ) ) {

	/**
	 * Registers the personal data exporter for scheduled reminders.
	 *
	 * @param array $exporters The exporters.
	 *
	 * @return  array
	 * @since   1.3.8
	 */
	function ywrr_register_scheduled_reminders_exporter( $exporters ) {
		$exporters['ywrr-reminders'] = array(
			'exporter_friendly_name' => esc_html__( 'Review Reminders', 'yith-woocommerce-review-reminder' ),
			'callback'               => 'ywrr_scheduled_reminders_exporter',
		);

		return $exporters;
	}

	add_filter( 'wp_privacy_personal_data_exporters', 'ywrr_register_scheduled_reminders_exporter' );

}

if ( ! function_exists( 'ywrr_register_blocklist_exporter' ) ) {

	/**
	 * Registers the personal data exporter for blocklist elements.
	 *
	 * @param array $exporters The exporters.
	 *
	 * @return  array
	 * @since   1.3.8
	 */
	function ywrr_register_blocklist_exporter( $exporters ) {
		$exporters['ywrr-blocklist'] = array(
			'exporter_friendly_name' => esc_html__( 'Review Reminder Blocklist', 'yith-woocommerce-review-reminder' ),
			'callback'               => 'ywrr_blocklist_exporter',
		);

		return $exporters;
	}

	add_filter( 'wp_privacy_personal_data_exporters', 'ywrr_register_blocklist_exporter' );

}

if ( ! function_exists( 'ywrr_scheduled_reminders_exporter' ) ) {

	/**
	 * Finds and exports personal data associated with an email address from the scheduled reminders table.
	 *
	 * @param string  $email_address The user email address.
	 * @param integer $page          The current page.
	 *
	 * @return  array
	 * @since   1.3.8
	 */
	function ywrr_scheduled_reminders_exporter( $email_address, $page = 1 ) {
		// Limit us to 500 comments at a time to avoid timing out.
		global $wpdb;

		$number                  = 500;
		$page                    = (int) $page;
		$offset                  = $number * ( $page - 1 );
		$data_to_export          = array();
		$args                    = array(
			'customer' => $email_address,
			'limit'    => -1,
			'return'   => 'ids',
		);
		$orders                  = implode( ', ', wc_get_orders( $args ) );
		$sql                     = "
				SELECT      *
				FROM        {$wpdb->prefix}ywrr_email_schedule
				WHERE       order_id IN ({$orders}) 
				ORDER BY    order_id ASC
				LIMIT       {$offset} ,{$number}
				";
		$reminders               = $wpdb->get_results( $sql );//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$reminder_prop_to_export = array(
			'order_id'       => esc_html__( 'Order number', 'yith-woocommerce-review-reminder' ),
			'request_items'  => esc_html__( 'Items to review', 'yith-woocommerce-review-reminder' ),
			'order_date'     => esc_html__( 'Completed order date', 'yith-woocommerce-review-reminder' ),
			'scheduled_date' => esc_html__( 'Scheduled email date', 'yith-woocommerce-review-reminder' ),
			'mail_status'    => esc_html__( 'Status', 'yith-woocommerce-review-reminder' ),
		);

		foreach ( (array) $reminders as $reminder ) {
			$reminder_data_to_export = array();

			foreach ( $reminder_prop_to_export as $key => $name ) {

				switch ( $key ) {
					case 'order_date':
					case 'scheduled_date':
						$value = date_i18n( get_option( 'date_format' ), strtotime( $reminder->{$key} ) );
						break;
					case 'request_items':
						$items       = maybe_unserialize( $reminder->request_items );
						$items_names = array();
						if ( ! empty( $items ) ) {
							foreach ( $items as $item ) {
								$items_names[] = $item['name'];
							}
						}
						$value = implode( ', ', $items_names );
						break;
					default:
						$value = $reminder->{$key};
				}

				if ( ! empty( $value ) ) {
					$reminder_data_to_export[] = array(
						'name'  => $name,
						'value' => $value,
					);
				}
			}
			$data_to_export[] = array(
				'group_id'    => 'ywrr_reminders',
				'group_label' => esc_html__( 'Scheduled Review Reminders', 'yith-woocommerce-review-reminder' ),
				'item_id'     => "reminder-$reminder->id",
				'data'        => $reminder_data_to_export,
			);

		}

		$done = count( $reminders ) < $number;

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}
}

if ( ! function_exists( 'ywrr_blocklist_exporter' ) ) {

	/**
	 * Finds and exports personal data associated with an email address from the blocklist table.
	 *
	 * @param string $email_address The user email address.
	 *
	 * @return  array
	 * @since   1.3.8
	 */
	function ywrr_blocklist_exporter( $email_address ) {
		global $wpdb;

		$data_to_export = array();

		//phpcs:ignore
		$is_blocklist = $wpdb->get_var(
			$wpdb->prepare(
				"
                    SELECT    COUNT(*)
                    FROM      {$wpdb->prefix}ywrr_email_blocklist 
                    WHERE   customer_email = %s 
                    ",
				$email_address
			)
		);

		if ( $is_blocklist ) {

			$data_to_export[] = array(
				'group_id'    => 'ywrr_blocklist',
				'group_label' => esc_html__( 'Review Reminder Status', 'yith-woocommerce-review-reminder' ),
				'item_id'     => 'blocklist-0',
				'data'        => array(
					array(
						'name'  => esc_html__( 'Blocklist', 'yith-woocommerce-review-reminder' ),
						'value' => esc_html__( 'This customer doesn\'t want to receive review requests anymore', 'yith-woocommerce-review-reminder' ),
					),
				),
			);

		}

		return array(
			'data' => $data_to_export,
			'done' => true,
		);
	}
}

if ( ! function_exists( 'ywrr_register_scheduled_reminders_eraser' ) ) {

	/**
	 * Registers the personal data eraser for scheduled reminders.
	 *
	 * @param array $erasers The erasers.
	 *
	 * @return  array
	 * @since   1.3.8
	 */
	function ywrr_register_scheduled_reminders_eraser( $erasers ) {
		$erasers['ywrr-reminders'] = array(
			'eraser_friendly_name' => esc_html__( 'Review Reminders', 'yith-woocommerce-review-reminder' ),
			'callback'             => 'ywrr_scheduled_reminders_eraser',
		);

		return $erasers;
	}

	add_filter( 'wp_privacy_personal_data_erasers', 'ywrr_register_scheduled_reminders_eraser' );

}

if ( ! function_exists( 'ywrr_scheduled_reminders_eraser' ) ) {

	/**
	 * Erases personal data associated with an email address from the scheduled reminders table.
	 *
	 * @param string  $email_address The user email address.
	 * @param integer $page          The current page.
	 *
	 * @return array
	 * @since  1.3.8
	 */
	function ywrr_scheduled_reminders_eraser( $email_address, $page = 1 ) {
		global $wpdb;

		if ( empty( $email_address ) ) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}

		// Limit us to 500 comments at a time to avoid timing out.
		$number        = 500;
		$page          = (int) $page;
		$offset        = $number * ( $page - 1 );
		$items_removed = false;
		$args          = array(
			'customer' => $email_address,
			'limit'    => -1,
			'return'   => 'ids',
		);
		$orders        = implode( ', ', wc_get_orders( $args ) );
		$sql           = "
				SELECT      id
				FROM        {$wpdb->prefix}ywrr_email_schedule
				WHERE       order_id IN ({$orders}) 
				ORDER BY    order_id ASC
				LIMIT       {$offset} ,{$number}
				";
		$reminders     = $wpdb->get_col( $sql );//phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		if ( ! empty( $reminders ) ) {

			$items_removed = true;

			foreach ( $reminders as $reminder ) {
				//phpcs:ignore
				$wpdb->delete(
					$wpdb->prefix . 'ywrr_email_schedule',
					array( 'id' => $reminder ),
					array( '%d' )
				);
			}
		}

		$done = count( $reminders ) < $number;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => $done,
		);
	}
}

if ( ! function_exists( 'ywrr_register_blocklist_eraser' ) ) {

	/**
	 * Registers the personal data eraser for scheduled reminders.
	 *
	 * @param array $erasers The erasers.
	 *
	 * @return  array
	 * @since   1.3.8
	 */
	function ywrr_register_blocklist_eraser( $erasers ) {
		$erasers['ywrr-blocklist'] = array(
			'eraser_friendly_name' => esc_html__( 'Review Reminder Blocklist', 'yith-woocommerce-review-reminder' ),
			'callback'             => 'ywrr_blocklist_eraser',
		);

		return $erasers;
	}

	add_filter( 'wp_privacy_personal_data_erasers', 'ywrr_register_blocklist_eraser' );

}

if ( ! function_exists( 'ywrr_blocklist_eraser' ) ) {

	/**
	 * Erases personal data associated with an email address from the blocklist table.
	 *
	 * @param string $email_address The user email address.
	 *
	 * @return array
	 * @since  1.3.8
	 */
	function ywrr_blocklist_eraser( $email_address ) {
		global $wpdb;

		if ( empty( $email_address ) ) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}

		// Limit us to 500 comments at a time to avoid timing out.
		$items_removed = false;

		//phpcs:ignore
		$deleted = $wpdb->delete(
			$wpdb->prefix . 'ywrr_email_blocklist',
			array( 'customer_email' => $email_address ),
			array( '%s' )
		);

		if ( $deleted > 0 ) {

			$items_removed = true;

		}

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}
}

/**
 * BLOCKLIST RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_check_blocklist' ) ) {

	/**
	 * Check if the customer is in blocklist table
	 *
	 * @param integer $customer_id    The customer ID.
	 * @param string  $customer_email The customer Email.
	 *
	 * @return  boolean
	 * @since   1.6.0
	 */
	function ywrr_check_blocklist( $customer_id, $customer_email ) {
		global $wpdb;

		if ( 0 === (int) $customer_id ) {
			//phpcs:ignore
			$count = $wpdb->get_var(
				$wpdb->prepare(
					"
                    SELECT    COUNT(*)
                    FROM      {$wpdb->prefix}ywrr_email_blocklist
                    WHERE     customer_email = %s
                    ",
					$customer_email
				)
			);
		} else {
			//phpcs:ignore
			$count = $wpdb->get_var(
				$wpdb->prepare(
					"
                    SELECT    COUNT(*)
                    FROM      {$wpdb->prefix}ywrr_email_blocklist
                    WHERE     customer_id = %d
                    ",
					$customer_id
				)
			);
		}

		return ( $count >= 1 ? false : true );
	}
}

if ( ! function_exists( 'ywrr_add_to_blocklist' ) ) {

	/**
	 * Add customer to blocklist table
	 *
	 * @param integer $customer_id    The customer ID.
	 * @param string  $customer_email The customer Email.
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_add_to_blocklist( $customer_id, $customer_email ) {
		global $wpdb;

		//phpcs:ignore
		$wpdb->insert(
			$wpdb->prefix . 'ywrr_email_blocklist',
			array(
				'customer_email' => $customer_email,
				'customer_id'    => $customer_id,
			),
			array(
				'%s',
				'%d',
			)
		);
	}
}

if ( ! function_exists( 'ywrr_remove_from_blocklist' ) ) {

	/**
	 * Remove customer from blocklist table
	 *
	 * @param integer $customer_id The customer ID.
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_remove_from_blocklist( $customer_id ) {

		global $wpdb;

		//phpcs:ignore
		$wpdb->delete(
			$wpdb->prefix . 'ywrr_email_blocklist',
			array(
				'customer_id' => $customer_id,
			),
			array(
				'%d',
			)
		);

	}
}

/**
 * BOOKING RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_schedule_booking_mail' ) && ( defined( 'YITH_WCBK_VERSION' ) && version_compare( YITH_WCBK_VERSION, '2.1.9', '>=' ) ) ) {

	/**
	 * Schedule booking wen is set as completed
	 *
	 * @param integer $booking_id     The booking ID.
	 * @param string  $scheduled_date The scheduled date.
	 *
	 * @return  string
	 * @since   1.6.0
	 */
	function ywrr_schedule_booking_mail( $booking_id, $scheduled_date = '' ) {

		$booking = yith_get_booking( $booking_id );
		if ( ! $booking ) {
			return esc_html__( 'This email cannot be scheduled', 'yith-woocommerce-review-reminder' );
		}

		$order = $booking->get_order();

		if ( ! $order ) {
			return esc_html__( 'This email cannot be scheduled', 'yith-woocommerce-review-reminder' );
		}

		$was_quote = false;

		if ( function_exists( 'YITH_YWRAQ_Order_Request' ) ) {
			$was_quote = YITH_YWRAQ_Order_Request()->is_quote( $order->get_id() );
		}

		$customer_id    = $order->get_user_id();
		$customer_email = $order->get_billing_email();

		if ( ywrr_check_blocklist( $customer_id, $customer_email ) !== true ) {
			return esc_html__( 'This email cannot be scheduled', 'yith-woocommerce-review-reminder' );
		}

		if ( ( ! wp_get_post_parent_id( $order->get_id() ) || ( wp_get_post_parent_id( $order->get_id() ) && $was_quote ) ) && (int) ywrr_check_exists_schedule( $order->get_id(), $booking_id ) === 0 ) {

			$forced_list = maybe_serialize(
				array(
					$booking->get_product_id() => array(
						'name' => $booking->get_product()->get_name(),
						'id'   => $booking->get_product_id(),
					),
				)
			);

			global $wpdb;

			if ( '' === $scheduled_date ) {
				$scheduled_date = gmdate( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + ' . get_option( 'ywrr_mail_schedule_day' ) . ' days' ) );
			}
			$order_date = $order->get_date_modified();

			if ( ! $order_date ) {
				$order_date = $order->get_date_created();
			}

			//phpcs:ignore
			$wpdb->insert(
				$wpdb->prefix . 'ywrr_email_schedule',
				array(
					'order_id'       => $order->get_id(),
					'mail_status'    => 'pending',
					'scheduled_date' => $scheduled_date,
					'order_date'     => gmdate( 'Y-m-d', yit_datetime_to_timestamp( $order_date ) ),
					'request_items'  => $forced_list,
					'mail_type'      => 'booking-' . $booking_id,
				),
				array( '%d', '%s', '%s', '%s', '%s', '%s' )
			);

			return '';
		}

		return esc_html__( 'This email cannot be scheduled', 'yith-woocommerce-review-reminder' );
	}

	add_action( 'yith_wcbk_booking_status_completed', 'ywrr_schedule_booking_mail' );

}

if ( ! function_exists( 'ywrr_exclude_booking' ) ) {

	/**
	 * Check if product is a booking and add it to excluded items
	 *
	 * @param array   $items      The excluded items list.
	 * @param integer $product_id The product ID.
	 *
	 * @return  array
	 * @since   1.6.0
	 */
	function ywrr_exclude_booking( $items, $product_id ) {

		$product = wc_get_product( $product_id );

		if ( defined( 'YITH_WCBK_PREMIUM' ) && YITH_WCBK_PREMIUM && ( defined( 'YITH_WCBK_VERSION' ) && version_compare( YITH_WCBK_VERSION, '2.1.9', '>=' ) ) && $product && $product->is_type( 'booking' ) ) {
			$items[] = $product_id;
		}

		return $items;

	}

	add_filter( 'ywrr_excluded_items', 'ywrr_exclude_booking', 10, 2 );

}

/**
 * ADMIN TEMPLATES RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_get_send_box' ) ) {

	/**
	 * Get send box content
	 *
	 * @param integer  $row_id        The row ID.
	 * @param WC_Order $order         The order object.
	 * @param integer  $booking_id    The booking ID.
	 * @param integer  $order_item_id The order item ID.
	 *
	 * @return void
	 * @since   1.6.0
	 */
	function ywrr_get_send_box( $row_id, $order, $booking_id = 0, $order_item_id = 0 ) {

		global $wpdb;

		$query = $wpdb->prepare(
			"
					SELECT	scheduled_date,
					       	mail_status
					FROM	{$wpdb->prefix}ywrr_email_schedule
					WHERE	order_id = %d
					  AND	mail_status <> 'cancelled'
					  ",
			$order->get_id()
		);
		//phpcs:ignore
		$schedule = $wpdb->get_row( $query );

		$background = '';
		$title      = '';
		$date       = '';

		if ( $schedule ) {
			$date = date_i18n( get_option( 'date_format' ), strtotime( $schedule->scheduled_date ) );
			switch ( $schedule->mail_status ) {
				case 'sent':
					$title      = esc_html__( 'The request was sent on', 'yith-woocommerce-review-reminder' );
					$background = YWRR_ASSETS_URL . 'images/email-sent.svg';
					break;

				case 'pending':
					$title      = esc_html__( 'The request will be sent on', 'yith-woocommerce-review-reminder' );
					$background = YWRR_ASSETS_URL . 'images/email-pending.svg';

					break;
			}
		}

		$order_date = $order->get_date_modified();
		if ( ! $order_date ) {
			$order_date = $order->get_date_created();
		}

		?>
		<div class="ywrr-send-box" id="ywrr-<?php echo esc_attr( $row_id ); ?>">
			<?php if ( $schedule ) : ?>
				<img src="<?php echo esc_url( $background ); ?>" />
				<strong><?php echo esc_html( $title ); ?>:</strong>
				<br />
				<?php if ( 'pending' === $schedule->mail_status ) : ?>
					<?php
					/* translators: %s send date */
					$label = '<br /><small>' . sprintf( esc_html__( 'By default, the plugin will send the reminder on %s.', 'yith-woocommerce-review-reminder' ), $date ) . '<br/>' . esc_html__( 'Pick a new date to overwrite this setting.', 'yith-woocommerce-review-reminder' ) . '</small>';
					ywrr_actions_button( $order->get_id(), $booking_id, $order_item_id, $order_date, $date, '', 'pending', $date, $label );
					?>
					<br />
					<a href="#" class="ywrr-schedule-delete" data-order-id="<?php echo esc_attr( $order->get_id() ); ?>" data-booking-id="<?php echo esc_attr( $booking_id ); ?>" data-order-item-id="<?php echo esc_attr( $order_item_id ); ?>" data-order-date="<?php echo esc_attr( yit_datetime_to_timestamp( $order_date ) ); ?>"><?php esc_html_e( 'Delete', 'yith-woocommerce-review-reminder' ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $date ); ?>
					<br />
					<?php
					$scheduled_date = gmdate( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + 1 days' ) );
					/* translators: %s send date */
					$label = '<br /><small>' . sprintf( esc_html__( 'An email was sent on %s.', 'yith-woocommerce-review-reminder' ), $date ) . '<br/>' . esc_html__( 'Pick a new date to reschedule it.', 'yith-woocommerce-review-reminder' ) . '</small>';
					ywrr_actions_button( $order->get_id(), $booking_id, $order_item_id, $order_date, esc_html__( 'Send a new reminder', 'yith-woocommerce-review-reminder' ), '', 'sent', $scheduled_date, $label );
					?>
				<?php endif; ?>
			<?php else : ?>
				<?php
				$scheduled_date = gmdate( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + 1 days' ) );
				ywrr_actions_button( $order->get_id(), $booking_id, $order_item_id, $order_date, esc_html__( 'Schedule Reminder', 'yith-woocommerce-review-reminder' ), 'ywrr-button-type', 'new', $scheduled_date );
				?>
			<?php endif; ?>
		</div>
		<?php

	}
}

if ( ! function_exists( 'ywrr_get_noreview_message' ) ) {

	/**
	 * Get message if reminder cannot be sent for specified item
	 *
	 * @param string $type The message type.
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_get_noreview_message( $type = '' ) {

		switch ( $type ) {
			case 'no-items':
				$message = esc_html__( 'There are no reviewable items in this order', 'yith-woocommerce-review-reminder' );
				break;
			case 'no-booking':
				$message = esc_html__( 'This booking cannot be reviewed', 'yith-woocommerce-review-reminder' );
				break;
			default:
				$message = esc_html__( 'This customer doesn\'t want to receive any more review requests', 'yith-woocommerce-review-reminder' );
		}

		?>
		<div class="ywrr-no-review-box">
			<?php echo esc_html( $message ); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'ywrr_actions_button' ) ) {

	/**
	 * Outputs action buttons
	 *
	 * @param integer $order_id         The order ID.
	 * @param integer $booking_id       The booking ID.
	 * @param integer $order_item_id    The order item ID.
	 * @param string  $order_date       The order date.
	 * @param string  $label            The label.
	 * @param string  $class            The CSS class.
	 * @param string  $button_type      The button type.
	 * @param string  $scheduled_date   The scheduled date.
	 * @param string  $additional_label Additional label.
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_actions_button( $order_id, $booking_id, $order_item_id, $order_date, $label, $class, $button_type, $scheduled_date = '', $additional_label = '' ) {

		?>
		<a href="#" class="ywrr-schedule-actions <?php echo esc_attr( $class ); ?>" data-button-type="<?php echo esc_attr( $button_type ); ?>" data-order-id="<?php echo esc_attr( $order_id ); ?>" data-booking-id="<?php echo esc_attr( $booking_id ); ?>" data-order-item-id="<?php echo esc_attr( $order_item_id ); ?>" data-order-date="<?php echo esc_attr( yit_datetime_to_timestamp( $order_date ) ); ?>" data-additional-label="<?php echo esc_attr( $additional_label ); ?>" data-scheduled-date="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $scheduled_date ) ) ); ?>"><?php echo esc_html( $label ); ?></a>
		<?php

	}
}

if ( ! function_exists( 'ywrr_compact_list' ) ) {

	/**
	 * Print a compact list
	 *
	 * @param array $items Order Items.
	 * @param array $args  Arguments.
	 *
	 * @return  void
	 * @since   1.6.0
	 */
	function ywrr_compact_list( $items, $args = array() ) {

		$defaults          = array(
			'limit'             => 5,
			'class'             => '',
			/* translators: %s number of hidden items */
			'show_more_message' => esc_html__( 'and other %s...', 'yith-woocommerce-review-reminder' ),
			'hide_more_message' => esc_html__( 'hide', 'yith-woocommerce-review-reminder' ),
		);
		$args              = wp_parse_args( $args, $defaults );
		$total             = count( $items );
		$limit             = absint( $args['limit'] );
		$hidden            = max( 0, $total - $limit );
		$class             = $args['class'];
		$show_more_message = sprintf( $args['show_more_message'], $hidden );
		$hide_more_message = $args['hide_more_message'];

		echo '<div class="ywrr-compact-list ' . esc_attr( $class ) . '" data-total="' . esc_attr( $total ) . '" data-limit="' . esc_attr( $limit ) . '" data-show-more-message="' . esc_attr( $show_more_message ) . '" data-hide-more-message="' . esc_attr( $hide_more_message ) . '">';
		$index = 1;

		foreach ( $items as $item ) {

			$product    = wc_get_product( $item['id'] );
			$item_class = 'ywrr-compact-list__item';
			if ( ( $limit + 1 ) === $index ) {
				echo "<div class='ywrr-compact-list__hidden-items'>";
			}

			if ( $product ) {
				$url = admin_url( 'post.php?post=' . $item['id'] . '&action=edit' );
				echo '<a target="_blank" href="' . esc_url( $url ) . '" class="' . esc_attr( $item_class ) . '" data-index="' . esc_attr( $index ) . '">' . esc_html( $item['name'] ) . '</a>';
			} else {
				echo '<div class="' . esc_attr( $item_class ) . '" data-index="' . esc_attr( $index ) . '">' . esc_html( $item['name'] ) . '</div>';
			}
			$index++;
		}
		if ( $hidden ) {
			echo '</div>';
			echo '<div class="clear"></div>';
			echo '<span class="ywrr-compact-list__show-more">';
			echo esc_html( $show_more_message );
			echo '<span class="yith-icon yith-icon-arrow_down"></span></span>';
			echo '<span class="ywrr-compact-list__hide-more">';
			echo esc_html( $hide_more_message );
			echo '<span class="yith-icon yith-icon-arrow_up"></span></span>';
		}
		echo '</div>';
	}
}
