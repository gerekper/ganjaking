<?php

if ( ! defined( 'YITH_WCARS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Refund_Request
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Refund_Request' ) ) {
	/**
	 * Class YITH_Refund_Request
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Refund_Request {

        /**
         * Refund request ID
         *
         * @var int
         * @since 1.0
         */
        public $ID = 0;

        /**
         * @var string Request status
         */
        public $status = null;

        /**
         * Refund title
         *
         * @var string
         * @since 1.0
         */
        public $title = null;

        /**
         * Order ID
         *
         * @var string
         * @since 1.0
         */
        public $order_id = null;

        /**
         * Whether the refund is for entire order or not
         *
         * @var bool
         * @since 1.0
         */
        public $whole_order = null;

        /**
         * Requested product ID.
         *
         * @var string
         * @since 1.0
         */
        public $product_id = null;

        /**
         * Item id
         *
         * @var string
         * @since 1.0
         */
        public $item_id = null;

		/**
		 * Item unity value
		 *
		 * @var string
		 * @since 1.0
		 */
		public $item_value = null;

		/**
		 * Total of all items ordered.
		 *
		 * @var string
		 * @since 1.0
		 */
		public $item_total = null;

		/**
		 * Item Tax Data
		 *
		 * @var array
		 * @since 1.0
		 */
		public $item_tax_data = null;

		/**
		 * Tax unity value
		 *
		 * @var string
		 * @since 1.0
		 */
		public $tax_value = null;

		/**
		 * Total of all taxes
		 *
		 * @var string
		 * @since 1.0
		 */
		public $tax_total = null;

        /**
         * Quantity. How many items to be refunded
         *
         * @var string
         * @since 1.0
         */
        public $qty = null;

		/**
		 * Ordered quantity. How many items has been ordered.
		 *
		 * @var string
		 * @since 1.0
		 */
		public $qty_total = null;

        /**
         * Total refund amount of item without taxes
         *
         * @var string
         * @since 1.0
         */
        public $item_refund_total = null;

		/**
		 * Total tax amount of refunded items
		 *
		 * @var string
		 * @since 1.0
		 */
		public $tax_refund_total = null;

		/**
		 * Total refund amount of item/order with taxes
		 *
		 * @var string
		 * @since 1.0
		 */
		public $refund_total = null;

		/**
		 * WC_Order_Refund id
		 *
		 * @var string
		 * @since 1.0
		 */
		public $refund_id = null;

		/**
		 * Total refunded amount after approval
		 *
		 * @var string
		 * @since 1.0
		 */
		public $refunded_amount = null;

        /**
         * Requester customer ID
         *
         * @var string
         * @since 1.0
         */
        public $customer_id = null;

		/**
		 * Coupon ID (if any)
		 *
		 * @var string
		 * @since 1.0
		 */
		public $coupon_id = null;

		/**
		 * Whether the request is closed or not
		 *
		 * @var boolean
		 * @since 1.0
		 */
		public $is_closed = null;


        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0
         */
        public function __construct( $id = 0 ) {
            $post = $id ? get_post( $id ) : null;
            if ( $post && $post->post_type == YITH_WCARS_CUSTOM_POST_TYPE ) {
	            $order_id = get_post_meta( $post->ID, '_ywcars_order_id', true );
	            $order = wc_get_order( $order_id );
	            if ( $order ) {
		            $this->ID                = $post->ID;
		            $this->status            = $post->post_status;
		            $this->title             = $post->post_title;
		            $this->order_id          = $order_id;
		            $this->whole_order       = get_post_meta( $post->ID, '_ywcars_whole_order', true );
		            $this->product_id        = get_post_meta( $post->ID, '_ywcars_product_id', true );
		            $this->item_id           = get_post_meta( $post->ID, '_ywcars_item_id', true );
		            $this->item_value        = get_post_meta( $post->ID, '_ywcars_item_value', true );
		            $this->item_total        = get_post_meta( $post->ID, '_ywcars_item_total', true );
		            $this->item_tax_data     = get_post_meta( $post->ID, '_ywcars_item_tax_data', true );
		            $this->tax_value         = get_post_meta( $post->ID, '_ywcars_tax_value', true );
		            $this->tax_total         = get_post_meta( $post->ID, '_ywcars_tax_total', true );
		            $this->qty               = get_post_meta( $post->ID, '_ywcars_qty', true );
		            $this->qty_total         = get_post_meta( $post->ID, '_ywcars_qty_total', true );
		            $this->item_refund_total = get_post_meta( $post->ID, '_ywcars_item_refund_total', true );
		            $this->tax_refund_total  = get_post_meta( $post->ID, '_ywcars_tax_refund_total', true );
		            $this->refund_total      = get_post_meta( $post->ID, '_ywcars_refund_total', true );
		            $this->refund_id         = get_post_meta( $post->ID, '_ywcars_refund_id', true );
		            $this->refunded_amount   = get_post_meta( $post->ID, '_ywcars_refunded', true );
		            $this->coupon_id         = get_post_meta( $post->ID, '_ywcars_coupon_id', true );
		            $this->is_closed         = get_post_meta( $post->ID, '_ywcars_is_closed', true );
		            $this->customer_id       = get_post_meta( $post->ID, '_ywcars_customer_id', true );
	            }
            }
        }

        /**
         * Save the current object
         */
        public function save() {

            // Create post object args
            $args = array(
                'post_title'   => 'Refund Request',
                'post_type'    => YITH_WCARS_CUSTOM_POST_TYPE,
            );

            if ( $this->ID == 0 ) {
                $args['post_status'] = 'ywcars-new';
                // Insert the post into the database
                $this->ID = wp_insert_post( $args );
            } else {
                $args['ID'] = $this->ID;
                $args['post_status'] = $this->status;
                $this->ID   = wp_update_post( $args );
            }

	        wp_update_post( array(
			        'ID' => $this->ID,
			        'post_title' => sprintf( esc_html__( 'Refund request #%d', 'yith-advanced-refund-system-for-woocommerce' ), $this->ID )
		        )
	        );


            $order = wc_get_order( $this->order_id );
            $this->customer_id = $order->get_user_id();

            update_post_meta( $this->ID, '_ywcars_order_id', $this->order_id );
            update_post_meta( $this->ID, '_ywcars_whole_order', $this->whole_order );
            update_post_meta( $this->ID, '_ywcars_product_id', $this->product_id );
            update_post_meta( $this->ID, '_ywcars_item_id', $this->item_id );
            update_post_meta( $this->ID, '_ywcars_item_value', $this->item_value );
	        update_post_meta( $this->ID, '_ywcars_item_total', $this->item_total );
	        update_post_meta( $this->ID, '_ywcars_item_tax_data', $this->item_tax_data );
	        update_post_meta( $this->ID, '_ywcars_tax_value', $this->tax_value );
	        update_post_meta( $this->ID, '_ywcars_tax_total', $this->tax_total );
            update_post_meta( $this->ID, '_ywcars_qty', $this->qty );
	        update_post_meta( $this->ID, '_ywcars_qty_total', $this->qty_total );
	        update_post_meta( $this->ID, '_ywcars_item_refund_total', $this->item_refund_total );
	        update_post_meta( $this->ID, '_ywcars_tax_refund_total', $this->tax_refund_total );
            update_post_meta( $this->ID, '_ywcars_refund_total', $this->refund_total );
	        update_post_meta( $this->ID, '_ywcars_refund_id', $this->refund_id );
	        update_post_meta( $this->ID, '_ywcars_refunded', $this->refunded_amount );
            update_post_meta( $this->ID, '_ywcars_customer_id', $this->customer_id );
	        update_post_meta( $this->ID, '_ywcars_is_closed', $this->is_closed );
	        update_post_meta( $this->ID, '_ywcars_coupon_id', $this->coupon_id );

            $requests = yit_get_prop( $order, '_ywcars_requests', true );
            if ( $requests && is_array( $requests ) ) {
                if ( ! in_array( $this->ID, $requests ) ) {
                    $requests[] = $this->ID;
	                yit_save_prop( $order, '_ywcars_requests', $requests );
                }
            } else if ( empty( $requests ) ) {
                $requests = array( $this->ID );
                yit_save_prop( $order, '_ywcars_requests', $requests );
            }
            return $this->ID;
        }

        public function get_id() {
        	return $this->ID;
        }

        public function get_messages() {
            global $wpdb;

            $table_name =  $wpdb->prefix . YITH_ARS_DB::$ywcars_messages_table;

            $query   = $wpdb->prepare( "SELECT * FROM $table_name WHERE request = %d", $this->ID );
            $results = $wpdb->get_results( $query, ARRAY_A );


            if ( ! $results ) {
                return false;
            }
            $messages = array();
            foreach ( $results as $result ) {
                $message = new YITH_Request_Message();
                $message->ID = $result['ID'];
                $message->request = $result['request'];
                $message->date = $result['date'];
                $message->message = $result['message'];
                $message->author = $result['author'];
                $messages[] = $message;
            }
            return $messages;
        }

        public function get_view_request_url() {

            $view_request_url = wc_get_endpoint_url( 'view-request', $this->ID, wc_get_page_permalink( 'myaccount' ) );

            return apply_filters( 'ywcars_get_view_request_url', $view_request_url, $this );
        }

        public function get_date() {
            $post = get_post( $this->ID );
            return $post->post_date;
        }

        public function get_customer_link_legacy() {
            $customer = get_userdata( $this->customer_id );
            $order = wc_get_order( $this->order_id );
            if ( ! empty( $customer ) ) {
	            $url      = admin_url( 'user-edit.php?user_id=' . absint( $customer->ID ) );
	            $username = '<a href="' . $url . '">';

                if ( $customer->first_name || $customer->last_name ) {
                    $username .= esc_html( sprintf( esc_html_x( '%1$s %2$s', 'full name', 'yith-advanced-refund-system-for-woocommerce' ), ucfirst( $customer->first_name ), ucfirst( $customer->last_name ) ) );
                } else {
                    $username .= esc_html( ucfirst( $customer->display_name ) );
                }

                $username .= '</a>';

            } else {
                if ( $order->billing_first_name || $order->billing_last_name ) {
                    $username = trim( sprintf( esc_html_x( '%1$s %2$s', 'full name', 'yith-advanced-refund-system-for-woocommerce' ), $order->billing_first_name, $order->billing_last_name ) );
                } else if ( $order->billing_company ) {
                    $username = trim( $order->billing_company );
                } else {
                    $username = esc_html__( 'Guest', 'yith-advanced-refund-system-for-woocommerce' );
                }
            }
            return $username;
        }

        public function get_customer_link() {
	        $customer = get_userdata( $this->customer_id );
	        $order = wc_get_order( $this->order_id );
	        if ( ! $customer ) {
	        	return false;
	        }
	        if ( $order->get_customer_id() ) {
		        $user      = get_user_by( 'id', $order->get_customer_id() );
		        $url       = admin_url( 'user-edit.php?user_id=' . absint( $order->get_customer_id() ) );
		        $username  = '<a href="' . $url . '">';
		        $username .= esc_html( ucwords( $user->display_name ) );
		        $username .= '</a>';
	        } elseif ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
		        $username = trim( sprintf( esc_html_x( '%1$s %2$s', 'full name', 'yith-advanced-refund-system-for-woocommerce' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
	        } elseif ( $order->get_billing_company() ) {
		        $username = trim( $order->get_billing_company() );
	        } else {
		        $username = esc_html__( 'Guest', 'yith-advanced-refund-system-for-woocommerce' );
	        }

	        return $username;
        }

        public function exists() {
            return $this->ID;
        }

        public function set_approved() {
	        $this->status = 'ywcars-approved';
	        $this->save();
	        WC()->mailer();
	        do_action( 'ywcars_send_approved_user', $this->ID );
        }

		public function set_rejected() {
			$this->status = 'ywcars-rejected';
			$this->save();
			WC()->mailer();
			do_action( 'ywcars_send_rejected_user', $this->ID );
		}

		public function set_coupon_offered() {
			$this->status = 'ywcars-coupon';
			$this->save();
			WC()->mailer();
			do_action( 'ywcars_send_coupon_user', $this->ID );
		}

		public function set_processing() {
			$this->status = 'ywcars-processing';
			$this->save();
			WC()->mailer();
			do_action( 'ywcars_send_processing_user', $this->ID );
		}

		public function set_on_hold() {
			$this->status = 'ywcars-on-hold';
			$this->save();
			WC()->mailer();
			do_action( 'ywcars_send_on_hold_user', $this->ID );
		}

		public function close_request() {
			$this->is_closed = true;
			update_post_meta( $this->ID, '_ywcars_is_closed', $this->is_closed );
		}
		
    }
}