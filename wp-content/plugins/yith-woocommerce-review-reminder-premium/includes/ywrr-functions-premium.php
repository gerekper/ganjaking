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
if ( ! function_exists( 'ywrr_log_unscheduled_email' ) ) {

	/**
	 * Write in the schedule list an unscheduled mail when it is sent
	 *
	 * @param        $order           WC_Order
	 * @param        $booking_id      integer
	 * @param        $items_to_review array
	 *
	 * @return  void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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

		$wpdb->insert(
			$wpdb->prefix . 'ywrr_email_schedule',
			array(
				'order_id'       => $order->get_id(),
				'mail_status'    => 'sent',
				'scheduled_date' => date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) ),
				'order_date'     => date( 'Y-m-d', yit_datetime_to_timestamp( $order_date ) ),
				'request_items'  => ! empty( $items_to_review ) ? maybe_serialize( $items_to_review ) : '',
				'mail_type'      => ( $booking_id ? 'booking-' . $booking_id : 'order' )
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
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_check_ywcet_active() {
		return defined( 'YITH_WCET_PREMIUM' ) && YITH_WCET_PREMIUM;
	}

}

if ( ! function_exists( 'ywrr_load_premium_options' ) ) {

	/**
	 * Loads premium options
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_load_premium_options() {

		$wcet_args              = array( 'page' => 'yith_wcet_panel', );
		$wcet_url               = esc_url( add_query_arg( $wcet_args, admin_url( 'admin.php' ) ) );
		$email_templates_enable = ( ywrr_check_ywcet_active() ) ? array(
			'name'      => sprintf( esc_html__( 'Use %s', 'yith-woocommerce-review-reminder' ), 'YITH WooCommerce Email Templates' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => sprintf( esc_html__( 'By enabling this option, you will have to assign a template from %s', 'yith-woocommerce-review-reminder' ), '<a href="' . $wcet_url . '" target="_blank">YITH WooCommerce Email Templates</a>' ),
			'id'        => 'ywrr_mail_template_enable',
			'default'   => 'no',
		) : '';
		$email_templates_deps   = ( ywrr_check_ywcet_active() ) ? array(
			'id'    => 'ywrr_mail_template_enable',
			'value' => 'no'
		) : '';

		return array(
			'review_reminder_mail_template_enable'  => $email_templates_enable,
			'review_reminder_mail_template'         => array(
				'name'      => esc_html__( 'Email template', 'yith-woocommerce-review-reminder' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'desc'      => '',
				'options'   => array(
					'base'      => esc_html__( 'Woocommerce Template', 'yith-woocommerce-review-reminder' ),
					'premium-1' => esc_html__( 'Template 1', 'yith-woocommerce-review-reminder' ),
					'premium-2' => esc_html__( 'Template 2', 'yith-woocommerce-review-reminder' ),
					'premium-3' => esc_html__( 'Template 3', 'yith-woocommerce-review-reminder' ),
				),
				'default'   => 'base',
				'id'        => 'ywrr_mail_template',
				'deps'      => $email_templates_deps
			),
			'review_reminder_mail_item_link'        => array(
				'name'      => esc_html__( 'Set links destination', 'yith-woocommerce-review-reminder' ),
				'type'      => 'yith-field',
				'yith-type' => 'radio',
				'desc'      => esc_html__( 'Set the destination you want to show in the email', 'yith-woocommerce-review-reminder' ),
				'options'   => array(
					'product' => esc_html__( 'Product page', 'yith-woocommerce-review-reminder' ),
					'review'  => esc_html__( 'Default WooCommerce Reviews Tab', 'yith-woocommerce-review-reminder' ),
					'custom'  => esc_html__( 'Custom Anchor', 'yith-woocommerce-review-reminder' ),
				),
				'default'   => 'product',
				'id'        => 'ywrr_mail_item_link'
			),
			'review_reminder_mail_item_link_hash'   => array(
				'name'      => esc_html__( 'Set Custom Anchor', 'yith-woocommerce-review-reminder' ),
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'desc'      => esc_html__( 'HTML ID of the comments tab if different from the standard one', 'yith-woocommerce-review-reminder' ),
				'id'        => 'ywrr_mail_item_link_hash',
				'deps'      => array(
					'id'    => 'ywrr_mail_item_link',
					'value' => 'custom',
					'type'  => 'hide-disable'
				)
			),
			'review_reminder_comment_form_id'       => array(
				'name'      => esc_html__( 'Comment Form ID', 'yith-woocommerce-review-reminder' ),
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'default'   => '#commentform',
				'desc'      => esc_html__( 'HTML ID of the comments form. Leave blank if you don\'t want the page to scroll to the form when the customer visits the email links', 'yith-woocommerce-review-reminder' ),
				'id'        => 'ywrr_comment_form_id',
				'deps'      => array(
					'id'    => 'ywrr_mail_item_link',
					'value' => 'review,custom',
					'type'  => 'hide-disable'
				)
			),
			'review_reminder_comment_form_offset'   => array(
				'name'      => esc_html__( 'Comment Form Offset', 'yith-woocommerce-review-reminder' ),
				'type'      => 'yith-field',
				'yith-type' => 'number',
				'default'   => 0,
				'desc'      => esc_html__( 'Set a positive or negative value to adjust the scrolling offset when the customer visits the email links', 'yith-woocommerce-review-reminder' ),
				'id'        => 'ywrr_comment_form_offset',
				'deps'      => array(
					'id'    => 'ywrr_mail_item_link',
					'value' => 'review,custom',
					'type'  => 'hide-disable'
				)
			),
			'review_reminder_login_from_link'       => array(
				'name'      => esc_html__( 'Login from email link', 'yith-woocommerce-review-reminder' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => '',
				'id'        => 'ywrr_login_from_link',
				'default'   => 'no',
			),
			'review_reminder_mail_enable_analytics' => array(
				'name'      => esc_html__( 'Add Google Analytics to email links', 'yith-woocommerce-review-reminder' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => '',
				'id'        => 'ywrr_enable_analytics',
				'default'   => 'no',
			),
			'review_reminder_mail_campaign_source'  => array(
				'name'              => esc_html__( 'Campaign Source', 'yith-woocommerce-review-reminder' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'desc'              => esc_html__( 'Referrer: google, citysearch, newsletter4', 'yith-woocommerce-review-reminder' ),
				'id'                => 'ywrr_campaign_source',
				'custom_attributes' => 'required',
				'deps'              => array(
					'id'    => 'ywrr_enable_analytics',
					'value' => 'yes',
					'type'  => 'hide-disable'
				)
			),
			'review_reminder_mail_campaign_medium'  => array(
				'name'              => esc_html__( 'Campaign Medium', 'yith-woocommerce-review-reminder' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'desc'              => esc_html__( 'Marketing medium: cpc, banner, email', 'yith-woocommerce-review-reminder' ),
				'id'                => 'ywrr_campaign_medium',
				'custom_attributes' => 'required',
				'deps'              => array(
					'id'    => 'ywrr_enable_analytics',
					'value' => 'yes',
					'type'  => 'hide-disable'
				)
			),
			'review_reminder_mail_campaign_term'    => array(
				'name'        => esc_html__( 'Campaign Term', 'yith-woocommerce-review-reminder' ),
				'type'        => 'yith-field',
				'yith-type'   => 'ywrr-custom-checklist',
				'desc'        => esc_html__( 'Identify the paid keywords. Enter values separated by commas (for example, term1, term2)', 'yith-woocommerce-review-reminder' ),
				'id'          => 'ywrr_campaign_term',
				'placeholder' => esc_html__( 'Insert a term&hellip;', 'yith-woocommerce-review-reminder' ),
				'deps'        => array(
					'id'    => 'ywrr_enable_analytics',
					'value' => 'yes',
					'type'  => 'hide-disable'
				)
			),
			'review_reminder_mail_campaign_content' => array(
				'name'      => esc_html__( 'Campaign Content', 'yith-woocommerce-review-reminder' ),
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'desc'      => esc_html__( 'Use to differentiate ads', 'yith-woocommerce-review-reminder' ),
				'id'        => 'ywrr_campaign_content',
				'deps'      => array(
					'id'    => 'ywrr_enable_analytics',
					'value' => 'yes',
					'type'  => 'hide-disable'
				)
			),
			'review_reminder_mail_campaign_name'    => array(
				'name'              => esc_html__( 'Campaign Name', 'yith-woocommerce-review-reminder' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'desc'              => esc_html__( 'Product, promo code, or slogan', 'yith-woocommerce-review-reminder' ),
				'id'                => 'ywrr_campaign_name',
				'custom_attributes' => 'required',
				'deps'              => array(
					'id'    => 'ywrr_enable_analytics',
					'value' => 'yes',
					'type'  => 'hide-disable'
				)
			),
			'review_reminder_mandrill_enable'       => array(
				'name'      => esc_html__( 'Enable Mandrill', 'yith-woocommerce-review-reminder' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html__( 'Use Mandrill to send emails', 'yith-woocommerce-review-reminder' ),
				'id'        => 'ywrr_mandrill_enable',
				'default'   => 'no',
			),
			'review_reminder_mandrill_apikey'       => array(
				'name'              => esc_html__( 'Mandrill API Key', 'yith-woocommerce-review-reminder' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'desc'              => '',
				'id'                => 'ywrr_mandrill_apikey',
				'default'           => '',
				'custom_attributes' => 'required',
				'deps'              => array(
					'id'    => 'ywrr_mandrill_enable',
					'value' => 'yes',
					'type'  => 'hide-disable'
				)
			),
		);

	}

	add_filter( 'ywrr_premium_options', 'ywrr_load_premium_options' );

}

if ( ! function_exists( 'ywrr_premium_panel_tabs' ) ) {

	/**
	 * Loads premium tabs
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_premium_panel_tabs() {

		return array(
			'mail'      => esc_html__( 'Email Settings', 'yith-woocommerce-review-reminder' ),
			'settings'  => esc_html__( 'Request Settings', 'yith-woocommerce-review-reminder' ),
			'schedule'  => esc_html__( 'Scheduled Emails List', 'yith-woocommerce-review-reminder' ),
			'blocklist' => esc_html__( 'Blocklist', 'yith-woocommerce-review-reminder' ),
		);

	}

	add_filter( 'ywrr_panel_tabs', 'ywrr_premium_panel_tabs' );

}

/**
 * EMAIL RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_get_review_list_forced' ) ) {

	/**
	 * Prepares the list of items from selected items in order page
	 *
	 * @param   $items_to_review array the list of items to request a review
	 * @param   $order_id        int the order id
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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

			}

		}

		return $items;

	}

}

if ( ! function_exists( 'ywrr_criteria_first' ) ) {

	/**
	 * Get the first X items in the order that can be reviewed
	 *
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_criteria_highest_quantity( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort( $order_items, function ( $a, $b ) {
			return $b['quantity'] - $a['quantity'];
		} );

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_criteria_lowest_quantity( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort( $order_items, function ( $a, $b ) {
			return $a['quantity'] - $b['quantity'];
		} );

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_criteria_highest_priced( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort( $order_items, function ( $a, $b ) {
			return ( $b['subtotal'] / $b['quantity'] ) - ( $a['subtotal'] / $a['quantity'] );
		} );

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_criteria_lowest_priced( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort( $order_items, function ( $a, $b ) {
			return ( $a['subtotal'] / $a['quantity'] ) - ( $b['subtotal'] / $b['quantity'] );
		} );

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_criteria_highest_total_value( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort( $order_items, function ( $a, $b ) {
			return $b['subtotal'] - $a['subtotal'];
		} );

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_criteria_lowest_total_value( $order, $user_email ) {

		$items       = array();
		$amount      = get_option( 'ywrr_request_number', 1 );
		$count       = 0;
		$order_items = array();
		foreach ( $order->get_items() as $item ) {
			$order_items[] = $item->get_data();
		}

		usort( $order_items, function ( $a, $b ) {
			return $a['subtotal'] - $b['subtotal'];
		} );

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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

		usort( $order_items, function ( $a, $b ) {
			return $b['reviews'] - $a['reviews'];
		} );

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $order          WC_Order
	 * @param   $user_email     string
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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

		usort( $order_items, function ( $a, $b ) {
			return $a['reviews'] - $b['reviews'];
		} );

		foreach ( $order_items as $item ) {

			$product_id = $item['product_id'];

			if ( ywrr_skip_product( $product_id, $user_email ) ) {
				continue;
			}

			$items[ $product_id ]['name'] = $item['name'];
			$items[ $product_id ]['id']   = $product_id;
			$count ++;
			if ( $count == $amount ) {
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
	 * @param   $to          string
	 * @param   $subject     string
	 * @param   $message     string
	 * @param   $headers     string
	 * @param   $attachments array
	 *
	 * @return  boolean
	 * @throws  Mandrill_Exception
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_mandrill_send( $to, $subject, $message, $headers = '', $attachments = array() ) {

		if ( ! class_exists( 'Mandrill' ) ) {
			require_once( YWRR_DIR . 'includes/third-party/Mandrill.php' );
		}

		$from_name = wp_specialchars_decode( esc_html( get_option( 'woocommerce_email_from_name' ) ), ENT_QUOTES );

		if ( ! isset( $from_name ) ) {
			$from_name = 'WordPress';
		}

		$from_email = sanitize_email( get_option( 'woocommerce_email_from_address' ) );

		if ( ! isset( $from_email ) ) {

			$sitename = strtolower( $_SERVER['SERVER_NAME'] );
			if ( substr( $sitename, 0, 4 ) == 'www.' ) {
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
			$message  = apply_filters( 'ywrr_mandrill_send_mail_message', array(
				'html'        => $message,
				'subject'     => $subject,
				'from_email'  => apply_filters( 'wp_mail_from', $from_email ),
				'from_name'   => apply_filters( 'wp_mail_from_name', $from_name ),
				'to'          => array(
					array(
						'email' => $to,
						'type'  => 'to'
					)
				),
				'headers'     => $headers,
				'attachments' => $attachments
			) );

			$async   = apply_filters( 'ywrr_mandrill_send_mail_async', false );
			$ip_pool = apply_filters( 'ywrr_mandrill_send_mail_ip_pool', null );
			$send_at = apply_filters( 'ywrr_mandrill_send_mail_send_at', null );

			$results = $mandrill->messages->send( $message, $async, $ip_pool, $send_at );
			$return  = true;

			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {
					if ( ! isset( $result['status'] ) || in_array( $result['status'], array( 'rejected', 'invalid' ) ) ) {
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
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_get_premium_templates() {

		return array(
			'premium-1',
			'premium-2',
			'premium-3'
		);
	}

	add_filter( 'ywrr_email_templates', 'ywrr_get_premium_templates' );

}

if ( ! function_exists( 'ywrr_check_hash' ) ) {

	/**
	 * Get the custom hash for email links
	 *
	 * @param   $link_hash string
	 *
	 * @return  string
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_check_hash( $link_hash ) {

		if ( $link_hash != '' && substr( $link_hash, 0, 1 ) !== '#' ) {
			$link_hash = '#' . $link_hash;
		}

		return $link_hash;

	}


}

/**
 * SCHEDULE RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywrr_reschedule' ) ) {

	/**
	 * Reschedule the mail sending
	 *
	 * @param   $order_id              integer
	 * @param   $scheduled_date        string
	 * @param   $forced_list           string|array
	 *
	 * @return  boolean
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_reschedule( $order_id, $scheduled_date, $forced_list = '' ) {
		$was_quote = false;

		if ( function_exists( 'YITH_YWRAQ_Order_Request' ) ) {
			$was_quote = YITH_YWRAQ_Order_Request()->is_quote( $order_id );
		}

		if ( ! wp_get_post_parent_id( $order_id ) || ( wp_get_post_parent_id( $order_id ) && $was_quote ) ) {

			$forced_list = maybe_serialize( $forced_list );

			if ( $forced_list == '' ) {

				$list        = array();
				$order       = wc_get_order( $order_id );
				$is_funds    = $order->get_meta( '_order_has_deposit' ) == 'yes';
				$is_deposits = $order->get_created_via() == 'yith_wcdp_balance_order';
				//APPLY_FILTER: ywrr_skip_renewal_orders: check if plugin should skip subscription renewal orders
				$is_renew = $order->get_meta( 'is_a_renew' ) == 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );

				if ( ! $is_funds && ! $is_deposits && ! $is_renew ) {

					$list = ywrr_get_review_list( $order_id );

				}

				if ( empty( $list ) ) {

					return esc_html__( 'There are no reviewable items in this order', 'yith-woocommerce-review-reminder' );

				}

			}

			global $wpdb;

			$wpdb->update(
				$wpdb->prefix . 'ywrr_email_schedule',
				array(
					'scheduled_date' => $scheduled_date,
					'mail_status'    => 'pending',
					'request_items'  => $forced_list
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
	 * @param   $option array
	 *
	 * @return  void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_set_mass_reschedule( $option ) {

		if ( 'ywrr_mail_reschedule' == $option['id'] && isset( $_POST[ $option['id'] ] ) && ( '1' == $_POST[ $option['id'] ] || 'yes' == $_POST[ $option['id'] ] ) ) {

			if ( $_POST['ywrr_mail_schedule_day'] != get_option( 'ywrr_mail_schedule_day' ) || $_POST['ywrr_request_type'] != get_option( 'ywrr_request_type' ) || $_POST['ywrr_request_number'] != get_option( 'ywrr_request_number' ) || $_POST['ywrr_request_criteria'] != get_option( 'ywrr_request_criteria' ) ) {

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
	 * @throws  Exception
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_mass_reschedule() {

		if ( get_option( 'ywrr_must_reschedule' ) == 'yes' ) {

			global $wpdb;

			$new_interval = $_POST['ywrr_mail_schedule_day'];
			$query        = "
							SELECT  order_id,
									order_date,
									request_items,
									mail_type
							FROM	{$wpdb->prefix}ywrr_email_schedule
							WHERE	mail_status = 'pending'
							";
			$orders       = $wpdb->get_results( $query );

			foreach ( $orders as $item ) {
				$new_scheduled_date = date( 'Y-m-d', strtotime( $item->order_date . ' + ' . $new_interval . ' days' ) );
				$list               = $item->mail_type == 'order' ? maybe_serialize( ywrr_get_review_list( $item->order_id ) ) : $item->request_items;

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

				if ( isset( $_POST['ywrr_mail_send_rescheduled'] ) && '1' == $_POST['ywrr_mail_send_rescheduled'] ) {

					$list = maybe_unserialize( $item->request_items );

					$today     = new DateTime( current_time( 'mysql' ) );
					$send_date = new DateTime( $new_scheduled_date );
					$pay_date  = new DateTime( $item->order_date );
					$days      = $pay_date->diff( $today );

					if ( $send_date <= $today ) {
						$booking_id   = $item->mail_type == 'order' ? '' : str_replace( 'booking-', '', $item->mail_type );
						$type         = $item->mail_type == 'order' ? 'order' : 'booking';
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
	 * @param   $order_id integer
	 *
	 * @return  void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_unschedule_mail( $order_id ) {

		if ( ywrr_check_exists_schedule( $order_id ) != 0 ) {

			ywrr_change_schedule_status( $order_id );

		}

	}

	add_action( 'woocommerce_order_status_cancelled', 'ywrr_unschedule_mail' );
	add_action( 'woocommerce_order_status_refunded', 'ywrr_unschedule_mail' );

}

/**
 * BOOKING RELATED FUNCTIONS
 */

if ( ! function_exists( 'ywrr_schedule_booking_mail' ) && ( defined( 'YITH_WCBK_VERSION' ) && version_compare( YITH_WCBK_VERSION, '2.1.9', '>=' ) ) ) {

	/**
	 * Schedule booking wen is set as completed
	 *
	 * @param   $booking_id     integer
	 * @param   $scheduled_date string
	 *
	 * @return  string
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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

		if ( ywrr_check_blocklist( $customer_id, $customer_email ) != true ) {
			return esc_html__( 'This email cannot be scheduled', 'yith-woocommerce-review-reminder' );
		}

		if ( ( ! wp_get_post_parent_id( $order->get_id() ) || ( wp_get_post_parent_id( $order->get_id() ) && $was_quote ) ) && ywrr_check_exists_schedule( $order->get_id(), $booking_id ) == 0 ) {

			$forced_list = maybe_serialize( array( $booking->get_product_id() => array( 'name' => $booking->get_product()->get_name(), 'id' => $booking->get_product_id() ) ) );

			global $wpdb;

			if ( $scheduled_date == '' ) {
				$scheduled_date = date( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + ' . get_option( 'ywrr_mail_schedule_day' ) . ' days' ) );
			}
			$order_date = $order->get_date_modified();

			if ( ! $order_date ) {
				$order_date = $order->get_date_created();
			}

			$wpdb->insert(
				$wpdb->prefix . 'ywrr_email_schedule',
				array(
					'order_id'       => $order->get_id(),
					'mail_status'    => 'pending',
					'scheduled_date' => $scheduled_date,
					'order_date'     => date( 'Y-m-d', yit_datetime_to_timestamp( $order_date ) ),
					'request_items'  => $forced_list,
					'mail_type'      => 'booking-' . $booking_id
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
	 * @param   $items      array
	 * @param   $product_id integer
	 *
	 * @return  array
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_exclude_booking( $items, $product_id ) {

		$product = wc_get_product( $product_id );

		if ( defined( 'YITH_WCBK_PREMIUM' ) && YITH_WCBK_PREMIUM && ( defined( 'YITH_WCBK_VERSION' ) && version_compare( YITH_WCBK_VERSION, '2.1.9', '>=' ) ) && $product->is_type( 'booking' ) ) {
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
	 * @param $row_id        integer
	 * @param $order         WC_Order
	 * @param $booking_id    integer
	 * @param $order_item_id integer
	 *
	 * @return void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_get_send_box( $row_id, $order, $booking_id = 0, $order_item_id = 0 ) {

		global $wpdb;

		$query    = $wpdb->prepare( "
                            SELECT  scheduled_date,
                                    mail_status
                            FROM    {$wpdb->prefix}ywrr_email_schedule 
                            WHERE   order_id = %d
                            AND     mail_status <> 'cancelled'
                            ", $order->get_id() );
		$schedule = $wpdb->get_row( $query );

		$background = $title = $date = '';

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
        <div class="ywrr-send-box" id="ywrr-<?php echo $row_id ?>">
			<?php if ( $schedule ): ?>
                <img src="<?php echo $background ?>" />
                <strong><?php echo $title ?>:</strong>
                <br />
				<?php if ( $schedule->mail_status == 'pending' ) : ?>
					<?php
					$label = '<br /><small>' . sprintf( esc_html__( 'By default, the plugin will send the reminder on %s.', 'yith-woocommerce-review-reminder' ), $date ) . '<br/>' . esc_html__( 'Pick a new date to overwrite this setting.', 'yith-woocommerce-review-reminder' ) . '</small>';
					ywrr_actions_button( $order->get_id(), $booking_id, $order_item_id, $order_date, $date, '', 'pending', $date, $label );
					?>
                    <br />
                    <a href="#" class="ywrr-schedule-delete" data-order-id="<?php echo $order->get_id() ?>" data-booking-id="<?php echo $booking_id ?>" data-order-item-id="<?php echo $order_item_id ?>" data-order-date="<?php echo yit_datetime_to_timestamp( $order_date ) ?>"><?php esc_html_e( 'Delete', 'yith-woocommerce-review-reminder' ) ?></a>
				<?php else: ?>
					<?php echo $date ?>
                    <br />
					<?php
					$scheduled_date = date( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + 1 days' ) );
					$label          = '<br /><small>' . sprintf( esc_html__( 'An email was sent on %s.', 'yith-woocommerce-review-reminder' ), $date ) . '<br/>' . esc_html__( 'Pick a new date to reschedule it.', 'yith-woocommerce-review-reminder' ) . '</small>';
					ywrr_actions_button( $order->get_id(), $booking_id, $order_item_id, $order_date, esc_html__( 'Send a new reminder', 'yith-woocommerce-review-reminder' ), '', 'sent', $scheduled_date, $label );
					?>
				<?php endif; ?>
			<?php else: ?>
				<?php
				$scheduled_date = date( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + 1 days' ) );
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
	 * @param   $type string
	 *
	 * @return  void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_get_noreview_message( $type = '' ) {

		switch ( $type ) {
			case 'no-items':
				$message = esc_html__( 'There are no reviewable items in this order', 'yith-woocommerce-review-reminder' );;
				break;
			case 'no-booking':
				$message = esc_html__( 'This booking cannot be reviewed', 'yith-woocommerce-review-reminder' );
				break;
			default:
				$message = esc_html__( 'This customer doesn\'t want to receive any more review requests', 'yith-woocommerce-review-reminder' );
		}

		?>
        <div class="ywrr-no-review-box">
			<?php echo $message ?>
        </div>
		<?php
	}
}

if ( ! function_exists( 'ywrr_actions_button' ) ) {

	/**
	 * Outputs action buttons
	 *
	 * @param   $order_id            integer
	 * @param   $booking_id          integer
	 * @param   $order_item_id       integer
	 * @param   $order_date          string
	 * @param   $label               string
	 * @param   $class               string
	 * @param   $button_type         string
	 * @param   $scheduled_date      string
	 * @param   $additional_label    string
	 *
	 * @return  void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_actions_button( $order_id, $booking_id, $order_item_id, $order_date, $label, $class, $button_type, $scheduled_date = '', $additional_label = '' ) {

		?>
        <a href="#" class="ywrr-schedule-actions <?php echo $class ?>" data-button-type="<?php echo $button_type ?>" data-order-id="<?php echo $order_id ?>" data-booking-id="<?php echo $booking_id ?>" data-order-item-id="<?php echo $order_item_id ?>" data-order-date="<?php echo yit_datetime_to_timestamp( $order_date ) ?>" data-additional-label="<?php echo $additional_label ?>" data-scheduled-date="<?php echo date( 'Y-m-d', strtotime( $scheduled_date ) ) ?>"><?php echo $label ?></a>
		<?php

	}

}

if ( ! function_exists( 'ywrr_compact_list' ) ) {

	/**
	 * Print a compact list
	 *
	 * @param $items array
	 * @param $args  $args
	 *
	 * @return  void
	 * @since   1.6.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywrr_compact_list( $items, $args = array() ) {

		$defaults          = array(
			'limit'             => 5,
			'class'             => '',
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

		echo "<div class='ywrr-compact-list {$class}' data-total='{$total}' data-limit='{$limit}' data-show-more-message='{$show_more_message}' data-hide-more-message='{$hide_more_message}'>";
		$index = 1;

		foreach ( $items as $item ) {

			$product    = wc_get_product( $item['id'] );
			$item_class = 'ywrr-compact-list__item';
			if ( $index === ( $limit + 1 ) ) {
				echo "<div class='ywrr-compact-list__hidden-items'>";
			}

			if ( $product ) {
				$url = admin_url( 'post.php?post=' . $item['id'] . '&action=edit' );
				echo "<a target='_blank' href='{$url}' class='{$item_class}' data-index='{$index}'>{$item['name']}</a>";
			} else {
				echo "<div class='{$item_class}' data-index='{$index}'>{$item['name']}</div>";
			}
			$index ++;
		}
		if ( $hidden ) {
			echo "</div>";
			echo "<div class='clear'></div>";
			echo "<span class='ywrr-compact-list__show-more'>{$show_more_message}<span class='yith-icon yith-icon-arrow_down'></span></span>";
			echo "<span class='ywrr-compact-list__hide-more'>{$hide_more_message}<span class='yith-icon yith-icon-arrow_up'></span></span>";
		}
		echo "</div>";
	}

}