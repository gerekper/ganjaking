<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * GENERIC USE FUNCTIONS
 */
if ( ! function_exists( 'ywrr_user_has_commented' ) ) {

	/**
	 * Check if has reviewed the product
	 *
	 * @param   $product_id integer
	 * @param   $user_email string
	 *
	 * @return  boolean
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_user_has_commented( $product_id, $user_email ) {

		global $wpdb;

		$count = $wpdb->get_var( "
									SELECT  COUNT(comment_ID)
									FROM    {$wpdb->prefix}comments
									WHERE   comment_post_ID = $product_id
									AND     comment_author_email = '{$user_email}'
									" );

		return $count > 0 ? true : false;

	}

}

if ( ! function_exists( 'ywrr_items_has_comments_opened' ) ) {

	/**
	 * Check if product has reviews enabled
	 *
	 * @param   $product_id integer
	 *
	 * @return  boolean
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_items_has_comments_opened( $product_id ) {
		//APPLY_FILTER: ywrr_comment_status: check if comments are opened for a specific product
		return apply_filters( 'ywrr_comment_status', comments_open( $product_id ) );

	}

}

if ( ! function_exists( 'ywrr_check_reviewable_items' ) ) {

	/**
	 * Check if order has reviewable items
	 *
	 * @param   $post_id integer
	 *
	 * @return  integer
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_check_reviewable_items( $post_id ) {

		$order            = wc_get_order( $post_id );
		$order_items      = $order->get_items();
		$reviewable_items = 0;

		foreach ( $order_items as $item ) {

			if ( ! ywrr_skip_product( $item['product_id'], $order->get_billing_email() ) ) {

				$reviewable_items ++;

			}

		}

		return $reviewable_items;

	}

}

if ( ! function_exists( 'ywrr_format_date' ) ) {

	/**
	 * Format email date
	 *
	 * @param   $date string
	 *
	 * @return  string
	 * @since   1.2.3
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
		//APPLY_FILTER: ywrr_custom_date_format: sets date format
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
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_update_1_6_0() {

		$ywrr_db_option = get_option( 'ywrr_db_version_ext' );

		if ( empty( $ywrr_db_option ) || version_compare( $ywrr_db_option, YWRR_DB_VERSION_EXT, '<' ) ) {

			global $wpdb;

			$sql = "ALTER TABLE {$wpdb->prefix}ywrr_email_schedule ADD mail_type varchar(100) NOT NULL DEFAULT 'order'";
			$wpdb->query( $sql );

			update_option( 'ywrr_db_version_ext', YWRR_DB_VERSION_EXT );

		}

	}

	add_action( 'admin_init', 'ywrr_update_1_6_0' );

}

if ( ! function_exists( 'ywrr_vendor_check' ) ) {

	/**
	 * Check if current user is a vendor
	 *
	 * @return  boolean
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_vendor_check() {

		$is_vendor = false;

		if ( defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM ) {

			$vendor = yith_get_vendor( 'current', 'user' );

			$is_vendor = ( $vendor->id != 0 );

		}

		return $is_vendor;

	}

}

if ( ! function_exists( 'ywrr_mail_options' ) ) {

	/**
	 * Check if current user is a vendor
	 *
	 * @param   $wc_settings boolean
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
			'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-review-reminder' ), '<code>{site_title}</code>' ),
			'id'          => 'ywrr_mail_subject',
			'default'     => esc_html__( '[{site_title}] Review recently purchased products', 'yith-woocommerce-review-reminder' ),
			'desc_tip'    => true,
		);
		$form_fields ['ywrr_mail_body']    = array(
			'title'       => esc_html__( 'Email body', 'yith-woocommerce-review-reminder' ),
			'type'        => 'textarea',
			'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-review-reminder' ), '<code>{site_title}, {customer_name}, {customer_email}, {order_id}, {order_date}, {order_date_completed}, {order_list}, {days_ago}, {unsubscribe_link}</code>' ),
			'id'          => 'ywrr_mail_body',
			'default'     => esc_html__( 'Hello {customer_name},
Thank you for purchasing items from the {site_title} shop!
We would love if you could help us and other customers by reviewing the products you recently purchased.
It only takes a minute and it would really help others by giving them an idea of your experience.
Click the link below for each product and review the product under the \'Reviews\' tab.

{order_list}

Much appreciated,

{site_title}.


{unsubscribe_link}', 'yith-woocommerce-review-reminder' ),
			'css'         => 'resize: vertical; width: 100%; min-height: 40px; height:200px',
			'desc_tip'    => true,
		);

		if ( defined( 'YITH_WCBK_PREMIUM' ) && YITH_WCBK_PREMIUM && ( defined( 'YITH_WCBK_VERSION' ) && version_compare( YITH_WCBK_VERSION, '2.1.9', '>=' ) ) && defined( 'YWRR_PREMIUM' ) && YWRR_PREMIUM ) {
			$form_fields ['ywrr_mail_body_booking'] = array(
				'title'       => esc_html__( 'Email body (for booking products)', 'yith-woocommerce-review-reminder' ),
				'type'        => 'textarea',
				'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-review-reminder' ), '<code>{site_title}, {customer_name}, {customer_email}, {order_id}, {order_date}, {order_date_completed}, {order_list}, {days_ago}, {unsubscribe_link}</code>' ),
				'id'          => 'ywrr_mail_body_booking',
				'default'     => esc_html__( 'Hello {customer_name},
Thank you for booking on {site_title}!
We hope you had a great time and would appreciate your help by reviewing the booking you recently purchased.
It only takes a minute and you would really help other customers through your experience.
Click the link below and leave your review under the \'Reviews\' tab.

{order_list}

Much appreciated,


{site_title}.


{unsubscribe_link}', 'yith-woocommerce-review-reminder' ),
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

/**
 * EMAIL RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_send_email' ) ) {

	/**
	 * Prepares and send the review request mail
	 *
	 * @param   $order_id        integer
	 * @param   $days            integer
	 * @param   $items_to_review array
	 * @param   $stored_items    array
	 * @param   $type            string
	 *
	 * @return  boolean
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_send_email( $order_id, $days, $items_to_review = array(), $stored_items = array(), $type = 'order' ) {

		$list  = array();
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return false;
		}

		$is_funds    = $order->get_meta( '_order_has_deposit' ) == 'yes';
		$is_deposits = $order->get_created_via() == 'yith_wcdp_balance_order';
		//APPLY_FILTER: ywrr_skip_renewal_orders: check if plugin should skip subscription renewal orders
		$is_renew = $order->get_meta( 'is_a_renew' ) == 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );

		if ( ! $is_funds && ! $is_deposits && ! $is_renew ) {

			if ( ! empty( $stored_items ) && defined( 'YWRR_PREMIUM' ) ) {
				$list = $stored_items;
			} else {
				if ( ! empty( $items_to_review ) && defined( 'YWRR_PREMIUM' ) ) {
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
			'type'       => $type
		);

		return apply_filters( 'send_ywrr_mail', $mail_args );

	}

}

if ( ! function_exists( 'ywrr_get_review_list' ) ) {

	/**
	 * Prepares the list of items to review from stored options
	 *
	 * @param   $order_id integer
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_get_review_list( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return array();
		}

		$user_email = $order->get_billing_email();
		$criteria   = ( get_option( 'ywrr_request_type', 'all' ) ) != 'all' ? get_option( 'ywrr_request_criteria' ) : 'default';
		$items      = call_user_func( 'ywrr_criteria_' . $criteria, $order, $user_email );

		return $items;

	}

}

if ( ! function_exists( 'ywrr_criteria_default' ) ) {

	/**
	 * Get all products in the order that can be reviewed
	 *
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
	 * @param   $product_id integer
	 * @param   $user_email string
	 *
	 * @return  boolean
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */

	function ywrr_skip_product( $product_id, $user_email ) {

		//APPLY_FILTER: ywrr_excluded_items: lists of excluded items
		$excluded_items = apply_filters( 'ywrr_excluded_items', array(), $product_id );

		return ( ! ywrr_items_has_comments_opened( $product_id ) || ywrr_user_has_commented( $product_id, $user_email ) || in_array( $product_id, $excluded_items ) );
	}

}

if ( ! function_exists( 'ywrr_email_styles' ) ) {

	/**
	 * Get email styles
	 *
	 * @return  void
	 * @since   1.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
	 * @param   $item_list   array
	 * @param   $customer_id integer
	 *
	 * @return  string
	 * @since   1.6.0
	 *
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_email_items_list( $item_list, $customer_id ) {

		$filename = defined( 'YWRR_PREMIUM' ) && YWRR_PREMIUM ? '-premium' : '';
		$style    = wc_get_template_html( 'emails/email-items-list' . $filename . '.php', array( 'item_list' => $item_list, 'customer_id' => $customer_id ), false, YWRR_TEMPLATE_PATH );

		return $style;

	}

}

if ( ! function_exists( 'ywrr_get_templates' ) ) {

	/**
	 * Gets the email templates available
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_get_templates() {
		//APPLY_FILTER: ywrr_email_templates: get list of mail templates
		return apply_filters( 'ywrr_email_templates', array() );
	}

}

/**
 * SCHEDULE RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_schedule_mail' ) ) {

	/**
	 * Create a schedule record
	 *
	 * @param   $order_id    integer
	 * @param   $forced_list string
	 *
	 * @return  boolean
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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

		if ( ywrr_check_blocklist( $customer_id, $customer_email ) != true ) {
			return esc_html__( 'This email cannot be scheduled', 'yith-woocommerce-review-reminder' );
		}

		if ( ( ! wp_get_post_parent_id( $order_id ) || ( wp_get_post_parent_id( $order_id ) && $was_quote ) ) && ywrr_check_exists_schedule( $order_id ) == 0 ) {

			$forced_list = maybe_serialize( $forced_list );

			if ( $forced_list == '' ) {

				$list        = array();
				$is_funds    = $order->get_meta( '_order_has_deposit' ) == 'yes';
				$is_deposits = $order->get_created_via() == 'yith_wcdp_balance_order';
				//APPLY_FILTER: ywrr_skip_renewal_orders: check if plugin should skip subscription renewal orders
				$is_renew = $order->get_meta( 'is_a_renew' ) == 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );
				//APPLY_FILTER: ywrr_can_ask_for_review: check if plugin can ask for a review
				$can_ask_review = apply_filters( 'ywrr_can_ask_for_review', true, $order );


				if ( ! $is_funds && ! $is_deposits && ! $is_renew && $can_ask_review ) {
					$list = ywrr_get_review_list( $order_id );
				}

				if ( empty( $list ) ) {
					return esc_html__( 'There are no reviewable items in this order', 'yith-woocommerce-review-reminder' );
				}

				$forced_list = maybe_serialize( $list );

			}

			if ( ywrr_check_reviewable_items( $order_id ) == 0 && $was_quote ) {
				return esc_html__( 'There are no reviewable items in this order', 'yith-woocommerce-review-reminder' );
			}

			global $wpdb;

			$scheduled_date = date( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + ' . get_option( 'ywrr_mail_schedule_day' ) . ' days' ) );
			$order_date     = $order->get_date_modified();

			if ( ! $order_date ) {
				$order_date = $order->get_date_created();
			}

			$wpdb->insert(
				$wpdb->prefix . 'ywrr_email_schedule',
				array(
					'order_id'       => $order_id,
					'mail_status'    => 'pending',
					'scheduled_date' => $scheduled_date,
					'order_date'     => date( 'Y-m-d', yit_datetime_to_timestamp( $order_date ) ),
					'request_items'  => $forced_list
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
	 * @param   $order_id   integer
	 * @param   $booking_id integer
	 *
	 * @return  integer
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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

		$query = $wpdb->prepare( "
                    SELECT    COUNT(*)
                    FROM      {$wpdb->prefix}ywrr_email_schedule
                    WHERE     order_id = %d
                    AND 	  mail_type = %s
                    ", array( $order_id, $mail_type ) );

		$count = $wpdb->get_var( $query );

		return $count;
	}

}

if ( ! function_exists( 'ywrr_change_schedule_status' ) ) {

	/**
	 * Changes email schedule status
	 *
	 * @param   $order_id   integer
	 * @param   $status     string
	 * @param   $booking_id integer
	 *
	 * @return  void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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

		$wpdb->update(
			$wpdb->prefix . 'ywrr_email_schedule',
			array(
				'mail_status'    => $status,
				'scheduled_date' => date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) )
			),
			array( 'order_id' => $order_id, 'mail_type' => $mail_type ),
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
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_daily_schedule() {
		global $wpdb;

		$count = $wpdb->get_var( $wpdb->prepare( "
                    SELECT    COUNT(*)
                    FROM      {$wpdb->prefix}ywrr_email_schedule
                    WHERE     mail_status = 'pending' AND scheduled_date <= %s
                    ", current_time( 'mysql' ) ) );

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
	 * @throws  Exception
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_hourly_schedule() {
		global $wpdb;

		$number = get_option( 'ywrr_hourly_send_number', 10 );
		$orders = $wpdb->get_results( $wpdb->prepare( "
							SELECT  	order_id,
										order_date,
										request_items,
										mail_type
							FROM    	{$wpdb->prefix}ywrr_email_schedule
							WHERE		mail_status = 'pending' AND scheduled_date <= %s
							ORDER BY    id DESC
							LIMIT		{$number}
							", current_time( 'mysql' ) ) );

		foreach ( $orders as $item ) {
			$list         = maybe_unserialize( $item->request_items );
			$today        = new DateTime( current_time( 'mysql' ) );
			$pay_date     = new DateTime( $item->order_date );
			$days         = $pay_date->diff( $today );
			$type         = $item->mail_type == 'order' ? 'order' : 'booking';
			$email_result = ywrr_send_email( $item->order_id, $days->days, array(), $list, $type );

			if ( $email_result ) {
				$booking_id = $item->mail_type == 'order' ? '' : str_replace( 'booking-', '', $item->mail_type );
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
	 * @param   $post_id integer
	 *
	 * @return  void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_on_order_deletion( $post_id ) {

		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . 'ywrr_email_schedule',
			array( 'order_id' => $post_id ),
			array( '%d' )
		);

	}

	add_action( 'trashed_post', 'ywrr_on_order_deletion' );
	add_action( 'after_delete_post', 'ywrr_on_order_deletion' );

}

/**
 * GDPR RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_register_scheduled_reminders_exporter' ) ) {

	/**
	 * Registers the personal data exporter for scheduled reminders.
	 *
	 * @param   $exporters array
	 *
	 * @return  array
	 * @since   1.3.8
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
	 * @param   $exporters array
	 *
	 * @return  array
	 * @since   1.3.8
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
	 * @param   $email_address string
	 * @param   $page          integer
	 *
	 * @return  array
	 * @since   1.3.8
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_scheduled_reminders_exporter( $email_address, $page = 1 ) {
		// Limit us to 500 comments at a time to avoid timing out.
		global $wpdb;

		$number         = 500;
		$page           = (int) $page;
		$offset         = $number * ( $page - 1 );
		$data_to_export = array();
		$reminders      = $wpdb->get_results( $wpdb->prepare( "
                    SELECT      a.*
                    FROM        {$wpdb->prefix}ywrr_email_schedule a 
                    INNER JOIN  {$wpdb->prefix}posts b ON a.order_id = b.ID 
                    INNER JOIN  {$wpdb->prefix}postmeta c ON  b.ID = c.post_id 
                    WHERE       c.meta_key='_billing_email' 
                    AND         c.meta_value = %s 
                    ORDER BY    a.order_id ASC
                    LIMIT       {$offset },{$number}
                    ", $email_address ) );

		$reminder_prop_to_export = array(
			'order_id'       => esc_html__( 'Order number', 'yith-woocommerce-review-reminder' ),
			'request_items'  => esc_html__( 'Items to review', 'yith-woocommerce-review-reminder' ),
			'order_date'     => esc_html__( 'Completed order date', 'yith-woocommerce-review-reminder' ),
			'scheduled_date' => esc_html__( 'Scheduled email date', 'yith-woocommerce-review-reminder' ),
			'mail_status'    => esc_html__( 'Status', 'yith-woocommerce-review-reminder' )
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
				'item_id'     => "reminder-{$reminder->id}",
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
	 * @param  $email_address string
	 *
	 * @return  array
	 * @since   1.3.8
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_blocklist_exporter( $email_address ) {
		global $wpdb;

		$data_to_export = array();
		$is_blocklist   = $wpdb->get_var( $wpdb->prepare( "
                    SELECT    COUNT(*)
                    FROM      {$wpdb->prefix}ywrr_email_blocklist 
                    WHERE   customer_email = %s 
                    ", $email_address ) );

		if ( $is_blocklist ) {

			$data_to_export[] = array(
				'group_id'    => 'ywrr_blocklist',
				'group_label' => esc_html__( 'Review Reminder Status', 'yith-woocommerce-review-reminder' ),
				'item_id'     => "blocklist-0",
				'data'        => array(
					array(
						'name'  => esc_html__( 'Blocklist', 'yith-woocommerce-review-reminder' ),
						'value' => esc_html__( 'This customer doesn\'t want to receive review requests anymore', 'yith-woocommerce-review-reminder' ),
					)
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
	 * @param   $erasers array
	 *
	 * @return  array
	 * @since   1.3.8
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
	 * @param  $email_address string
	 * @param  $page          integer
	 *
	 * @return array
	 * @since  1.3.8
	 * @author Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
		$reminders     = $wpdb->get_col( $wpdb->prepare( "
                    SELECT      a.id
                    FROM        {$wpdb->prefix}ywrr_email_schedule a 
                    INNER JOIN  {$wpdb->prefix}posts b ON a.order_id = b.ID 
                    INNER JOIN  {$wpdb->prefix}postmeta c ON  b.ID = c.post_id 
                    WHERE       c.meta_key='_billing_email' 
                    AND         c.meta_value = %s 
                    ORDER BY    a.order_id ASC
                    LIMIT       {$offset },{$number}
                    ", $email_address ) );

		if ( ! empty( $reminders ) ) {

			$items_removed = true;

			foreach ( $reminders as $reminder ) {
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
	 * @param   $erasers array
	 *
	 * @return  array
	 * @since   1.3.8
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
	 * @param  $email_address string
	 *
	 * @return array
	 * @since  1.3.8
	 * @author Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
	 * @param   $customer_id    integer
	 * @param   $customer_email string
	 *
	 * @return  boolean
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_check_blocklist( $customer_id, $customer_email ) {
		global $wpdb;

		if ( 0 == $customer_id ) {
			$count = $wpdb->get_var( $wpdb->prepare( "
                    SELECT    COUNT(*)
                    FROM      {$wpdb->prefix}ywrr_email_blocklist
                    WHERE     customer_email = %s
                    ", $customer_email ) );
		} else {
			$count = $wpdb->get_var( $wpdb->prepare( "
                    SELECT    COUNT(*)
                    FROM      {$wpdb->prefix}ywrr_email_blocklist
                    WHERE     customer_id = %d
                    ", $customer_id ) );
		}

		return ( $count >= 1 ? false : true );
	}

}

if ( ! function_exists( 'ywrr_add_to_blocklist' ) ) {

	/**
	 * Add customer to blocklist table
	 *
	 * @param   $customer_id    integer
	 * @param   $customer_email string
	 *
	 * @return  void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_add_to_blocklist( $customer_id, $customer_email ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'ywrr_email_blocklist',
			array(
				'customer_email' => $customer_email,
				'customer_id'    => $customer_id
			),
			array(
				'%s',
				'%d'
			)
		);
	}

}

if ( ! function_exists( 'ywrr_remove_from_blocklist' ) ) {

	/**
	 * Remove customer from blocklist table
	 *
	 * @param   $customer_id    integer
	 *
	 * @return  void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_remove_from_blocklist( $customer_id ) {

		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . 'ywrr_email_blocklist',
			array(
				'customer_id' => $customer_id
			),
			array(
				'%d'
			)
		);

	}

}
