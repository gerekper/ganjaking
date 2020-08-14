<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Implements the YWRAQ_Multivendor class.
 *
 * @class   YWRAQ_Multivendor
 * @package YITH
 * @since   1.3.0
 * @author  YITH
 */
if ( ! class_exists( 'YWRAQ_Multivendor' ) ) {

	/**
	 * Class YWRAQ_Multivendor
	 */
	class YWRAQ_Multivendor {

		/**
		 * Single instance of the class
		 *
		 * @var \YWRAQ_Multivendor
		 */
		protected static $instance;


		/**
		 * @var string
		 */
		protected $current_order = '';


		/**
		 * Returns single instance of the class
		 *
		 * @return \YWRAQ_Multivendor
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.3.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			//Send request quote compaibility
			add_filter( 'ywraq_multivendor_email', array( $this, 'trigger_email_send_request' ), 15, 3 );
			add_filter( 'woocommerce_email_recipient_ywraq_quote_status',  array( $this, 'check_recipients' ) );

            add_action( 'ywraq_after_create_order', array( $this, 'create_suborder' ), 10, 3 );
			add_filter( 'woocommerce_new_order_data' , array( $this, 'change_status_to_suborder'));

			//Send request quote compatibility
			add_filter( 'woocommerce_order_get_items', array( $this, 'filter_order_items'), 10, 2);
			add_filter( 'woocommerce_admin_order_data_after_order_details', array( $this, 'update_order_totals'), 10);

			//quote button to checkout
			add_action('woocommerce_checkout_update_order_meta', array( $this, 'add_filter_before_send_email_vendor'), 10 );
			add_action('yith_wcmv_checkout_order_processed', array( $this, 'add_add_meta_to_suborder'), 10 );

		}

		public function check_recipients( $recipients  ) {


			return $recipients;
		}
		public function add_add_meta_to_suborder( $suborder_id ){

			if ( isset( $_REQUEST['ywraq_checkout_quote'] ) && $_REQUEST['ywraq_checkout_quote'] == 'true' ) {

				$order = wc_get_order( $suborder_id );

				$raq = array(
					'user_name' => trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
					'user_email'=> $order->get_billing_email(),
					'user_message' =>$order->get_customer_note(),
					'from_checkout' => 'yes'
				);

				$order->update_meta_data( 'ywraq_customer_name', $raq['user_name'] );
				$order->update_meta_data( 'ywraq_customer_email', $raq['user_email'] );
				$order->update_meta_data( 'ywraq_customer_message', $raq['user_message'] );
				$order->update_meta_data( '_ywraq_from_checkout', 1 );
				$order->update_meta_data( '_ywraq_pay_quote_now', 1 );

				$order->set_status( 'ywraq-new' );

				YITH_YWRAQ_Order_Request()->add_order_meta( $order, array() );

				$order->save();
				$order->add_order_note(__("This quote has been submitted from the checkout page.", 'yith-woocommerce-request-a-quote') );

			}
		}

		/**
		 * Disable the email of new order to Vendor
		 * @param $order WC_Order
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_filter_before_send_email_vendor( $order ) {
			if ( isset( $_REQUEST['ywraq_checkout_quote'] ) && $_REQUEST['ywraq_checkout_quote'] == 'true' ) {
				add_filter( 'yith_wcmv_skip_new_order_email_to_vendor', '__return_true');
			}
		}
		/**
		 * Change the items inside the parent order removing the items od vendors.
		 *
		 * @param $items
		 * @param $order WC_Order
		 *
		 * @return array
		 */
		public function filter_order_items( $items, $order ) {

			$is_quote = YITH_YWRAQ_Order_Request()->is_quote( $order->get_id() );
			$parent_order        = get_post_field( 'post_parent', yit_get_prop( $order, 'id' ) );
			$is_parent_order     = $parent_order == 0;
			$is_create_raq_order = defined('DOING_CREATE_RAQ_ORDER' ) &&  DOING_CREATE_RAQ_ORDER;

			if( $is_quote && $is_parent_order && ! $is_create_raq_order ){
				$new_items = array();

				if( ! empty( $items ) ){
					foreach ( $items as $key => $item ) {
						if( isset( $item['product_id'] ) ){
							$vendor = yith_get_vendor( $item['product_id'], 'product' );
							if ( ! $vendor->is_valid() ) {
								$new_items[ $key ] = $item;
							}
						}else{
							$new_items[ $key ] = $item;
						}
					}
				}
                $items = $new_items;
            }
            return $items;
        }

		/**
		 * @param $order WC_Order
		 */
		public function update_order_totals( $order ) {
			$order->calculate_totals();
		}

		/**
		 * Create suborders for vendors
		 *
		 * @param $order_id
		 * @param $posted
         * @param $raq
		 *
		 * @since  1.3.0
		 * @author Emanuela Castorina
		 */
        public function create_suborder( $order_id, $posted, $raq ) {
            $this->current_order = $order_id;

            $suborder_ids        = YITH_Vendors()->orders->check_suborder( $this->current_order, $posted, true );

	        if ( ! empty( $suborder_ids ) ) {
		        foreach ( $suborder_ids as $suborder_id ) {
			        $suborder = wc_get_order( $suborder_id );

			        if ( $suborder instanceof WC_Order ) {
				        $suborder->update_meta_data( 'ywraq_customer_name', $raq['user_name'] );
				        $suborder->update_meta_data( 'ywraq_customer_email', $raq['user_email'] );
				        $suborder->update_meta_data( 'ywraq_customer_message', $raq['user_message'] );
				        $suborder->save();
				        YITH_YWRAQ_Order_Request()->add_order_meta( $suborder, $raq );
				        YITH_Commissions()->register_commissions( $suborder_id );
			        }
		        }
	        }

        }

		/**
		 * Set the status "New quote Request" to suborders
         *
		 * @param array $args
		 *
		 * @return mixed
		 */
		public function change_status_to_suborder( $args ) {
			if ( $this->current_order && isset( $args['post_parent'] ) && $this->current_order == $args['post_parent'] ) {
				$args['post_status'] = 'wc-ywraq-new';
			}

			return $args;
		}


		/**
		 * Switch the products of the request to each vendors that are owner, or to administrator
		 *
		 * @param $return
		 * @param $args
		 * @param $email_class YITH_YWRAQ_Send_Email_Request_Quote
		 *
		 * @return mixed
		 *
		 * @since  1.3.0
		 * @author Emanuela Castorina
		 */
		public function trigger_email_send_request( $return, $args, $email_class ) {

			$vendors_list  = array();
			$admin_list    = array();
			$parent_raq_id = $email_class->raq['order_id'];
			$order         = wc_get_order( $parent_raq_id );
			$sub_raqs      = YITH_Vendors()->orders->get_suborder( $parent_raq_id );
			$type          = '';
			$vendor_raqs   = array();

			if ( isset( $args['from_checkout'] ) ) {
				$type  = 'order_items';
				$items = $order->get_items();
				if ( ! empty( $items ) ) {
					foreach ( $items as $key => $item ) {
						$admin_list[ $key ] = $item;
					}
				}

				if ( ! empty( $sub_raqs ) ) {
					foreach ( $sub_raqs as $suborder_id ) {
						$suborder   = wc_get_order( $suborder_id );
						$tmp_vendor = yith_get_vendor( get_post_field( 'post_author', $suborder_id ), 'user' );
						if ( $suborder ) {
							$items = $suborder->get_items();
							if ( ! empty( $items ) ) {
								foreach ( $items as $key => $item ) {
									$vendors_list[ $tmp_vendor->id ][ $key ] = $item;
								}
							}
						}
					}
				}


			} else{
				if ( ! empty( $email_class->raq['raq_content'] ) ) {
					$type = 'raq_content';
					foreach ( $email_class->raq['raq_content'] as $raq_item => $item ) {
						$vendor = yith_get_vendor( $item['product_id'], 'product' );
						if ( $vendor->is_valid() ) {
							$vendors_list[ $vendor->id ][ $raq_item ] = $email_class->raq['raq_content'][ $raq_item ];
						} else {
							$admin_list[ $raq_item ] = $email_class->raq['raq_content'][ $raq_item ];
						}
					}
				}
			}

			/**
			 * Check for vendor raq
			 */
			foreach ( $sub_raqs as $sub_raq ) {
				$raq = wc_get_order( $sub_raq );
				if ( $raq ) {
					$tmp_vendor = yith_get_vendor( get_post_field( 'post_author', $raq->get_id() ), 'user' );
					if ( $tmp_vendor->is_valid() ) {
						$vendor_raqs[ $tmp_vendor->id ] = $sub_raq;
					}
				}
			}


			if ( ! empty( $admin_list ) ) {
				$email_class->raq['order_id']     = $parent_raq_id;
				$email_class->raq['raq_content']  = $admin_list;
				$email_class->raq['content_type'] = $type;
				$attachment                       = isset( $args['attachment'] ) ? $args['attachment'] : '';
				$return                           = $email_class->send( $email_class->get_recipient(), $email_class->get_subject(), $email_class->get_content(), $email_class->get_headers(), $email_class->get_attachments( $attachment ) );
			}


			if ( ! empty( $vendors_list ) ) {

				foreach ( $vendors_list as $vendor_id => $raq_vendor ) {

					$email_class->raq['order_id']     = $vendor_raqs[ $vendor_id ];
					$email_class->raq['raq_content']  = $raq_vendor;
					$email_class->raq['content_type'] = $type;
					$vendor                           = yith_get_vendor( $vendor_id, 'vendor' );

					if ( isset( $vendor->store_email ) && $vendor->store_email ) {
						$email_class->recipient = $vendor->store_email;
					} else {
						$owner_id = $vendor->get_owner();
						if ( ! empty( $owner_id ) ) {
							$owner                  = get_user_by( 'id', $owner_id );
							$email_class->recipient = $owner->user_email;
						}
					}

					$email_class->recipient = apply_filters('ywraq_request_a_quote_send_email_to_vendor_recipient', $email_class->recipient, $vendor, $email_class );

					$attachment = isset( $args['attachment'] ) ? $args['attachment'] : '';

					$return     = $email_class->send( $email_class->get_recipient(), $email_class->get_subject(), $email_class->get_content(), $email_class->get_headers(), $email_class->get_attachments( $attachment ) );
				}
			}


			return $return;
		}

		/**
		 * Add hidden order itemmeta from Quote Email
		 * @param $itemmeta
		 *
		 * @return array
		 */
		public function add_hidden_order_itemmeta( $itemmeta  ) {
			$itemmeta[] = '_parent_line_item_id';
			return $itemmeta;
		}

		
	}

}

/**
 * Unique access to instance of YWRAQ_Multivendor class
 *
 * @return \YWRAQ_Multivendor
 */
function YWRAQ_Multivendor() {
	return YWRAQ_Multivendor::get_instance();
}

YWRAQ_Multivendor();