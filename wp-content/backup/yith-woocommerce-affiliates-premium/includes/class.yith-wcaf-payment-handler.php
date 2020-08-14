<?php
/**
 * Payment Handler class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Payment_Handler' ) ) {
	/**
	 * WooCommerce Payment Handler
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Payment_Handler {

		/**
		 * Available payment status labels
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_status_labels_map = array();

		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Payment_Handler
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Payment_Handler
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->_status_labels_map = apply_filters( 'yith_wcaf_payment_status_labels_map', array(
				'pending'   => __( 'Pending', 'yith-woocommerce-affiliates' ),
				'completed' => __( 'Completed', 'yith-woocommerce-affiliates' ),
				'cancelled' => __( 'Cancelled', 'yith-woocommerce-affiliates' ),
				'on-hold'   => __( 'On Hold', 'yith-woocommerce-affiliates' )
			) );

			// add commissions panel handling
			add_action( 'yith_wcaf_payment_panel', array( $this, 'print_payment_panel' ) );
			add_action( 'current_screen', array( $this, 'add_screen_option' ) );
			add_filter( 'manage_yith-plugins_page_yith_wcaf_panel_columns', array( $this, 'add_screen_columns' ) );
			add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
			add_action( 'load-yith-plugins_page_yith_wcaf_panel', array( $this, 'process_bulk_actions' ) );

			// update payments when affiliate changes
			add_action( 'yith_wcaf_affiliate_payment_email_updated', array(
				$this,
				'update_payment_email_on_affiliate_change'
			), 10, 2 );

			// handles ajax actions
			add_action( 'wp_ajax_yith_wcaf_add_payment_note', array( $this, 'ajax_add_note' ) );
			add_action( 'wp_ajax_yith_wcaf_delete_payment_note', array( $this, 'ajax_delete_note' ) );
		}

		/* === PAYMENT HANDLING METHODS === */

		/**
		 * Add a payment, registering related commissions
		 *
		 * @param $args        mixed Params for new payment<br />
		 *                     [<br />
		 *                     'affiliate_id' => 0,                     // Affiliate id (int)<br />
		 *                     'payment_email' => '',                   // Payment email (string)<br />
		 *                     'status' => 'pending',                   // Status (valid payment status on-hold/pending/completed/cancelled)<br />
		 *                     'amount' => 0,                           // Amount (double)<br />
		 *                     'created_at' => current_time( 'mysql' ), // Date of creationg (mysql date format; default to current server time)<br />
		 *                     'completed_at' => '',                    // Date of complete (mysql date format; default to null)<br />
		 *                     'transaction_key' => ''                  // Payment transaction key (string; default null)<br />
		 *                     ]
		 * @param $commissions mixed Array of commission to register within payment
		 *
		 * @return int|bool New payment id; false on failure
		 * @since 1.0.0
		 */
		public function add( $args = array(), $commissions = array() ) {
			global $wpdb;

			$defaults = array(
				'affiliate_id'    => 0,
				'payment_email'   => '',
				'gateway'         => '',
				'status'          => 'on-hold',
				'amount'          => 0,
				'created_at'      => current_time( 'mysql' ),
				'completed_at'    => '',
				'transaction_key' => ''
			);

			$args = wp_parse_args( $args, $defaults );

			if ( empty( $args['completed_at'] ) ) {
				unset( $args['completed_at'] );
			}

			if ( empty( $args['transaction_key'] ) ) {
				unset( $args['transaction_key'] );
			}

			$res = $wpdb->insert( $wpdb->yith_payments, $args );

			if ( ! $res ) {
				return false;
			}

			$insert_id = $wpdb->insert_id;

			if ( ! empty( $commissions ) ) {
				foreach ( $commissions as $commission ) {
					$payment_commission_args = array(
						'payment_id'    => $insert_id,
						'commission_id' => $commission['ID']
					);

					$wpdb->insert( $wpdb->yith_payment_commission, $payment_commission_args );
				}
			}

			return $insert_id;
		}

		/**
		 * Update a payment
		 *
		 * @param $payment_id int Payment id
		 * @param $args       mixed Params for new payment<br />
		 *                    [<br />
		 *                    'affiliate_id' => 0,                     // Affiliate id (int)<br />
		 *                    'payment_email' => '',                   // Payment email (string)<br />
		 *                    'status' => 'pending',                   // Status (valid payment status on-hold/pending/completed)<br />
		 *                    'amount' => 0,                           // Amount (double)<br />
		 *                    'created_at' => current_time( 'mysql' ), // Date of creationg (mysql date format; default to current server time)<br />
		 *                    'completed_at' => '',                    // Date of complete (mysql date format; default to null)<br />
		 *                    'transaction_key' => ''                  // Payment transaction key (string; default null)<br />
		 *                    ]
		 *
		 * @return int|bool Updated rows; false on failure
		 * @since 1.0.0
		 */
		public function update( $payment_id, $args = array() ) {
			global $wpdb;

			return $wpdb->update( $wpdb->yith_payments, $args, array( 'ID' => $payment_id ) );
		}

		/**
		 * Delete a payment id and all associated commissions relationship
		 *
		 * @param $payment_id int Payment id
		 *
		 * @return int|bool Number of deleted rows on payment table; false on failure
		 * @since 1.0.0
		 */
		public function delete( $payment_id ) {
			global $wpdb;

			$wpdb->delete( $wpdb->yith_payment_commission, array( 'payment_id' => $payment_id ) );

			return $wpdb->delete( $wpdb->yith_payments, array( 'ID' => $payment_id ) );
		}

		/**
		 * Retrieve last id registered by DB
		 *
		 * @return int|bool Last id entered in payments table or false on failure
		 * @since 1.3.0
		 */
		public function last_id() {
			global $wpdb;

			return $wpdb->get_var( "SELECT ID FROM {$wpdb->yith_payments} ORDER BY ID DESC LIMIT 1" );
		}

		/* === PAYMENT NOTES METHODS === */

		/**
		 * Get existing notes for a given payment
		 *
		 * @param $payment_id int Payment id
		 *
		 * @return mixed Array with registered notes, or false if no note was registered yet
		 * @since 1.0.0
		 */
		public function get_payment_notes( $payment_id ) {
			global $wpdb;

			$res = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->yith_payment_notes} WHERE payment_id = %d ORDER BY note_date DESC", $payment_id ), ARRAY_A );

			return $res;
		}

		/**
		 * Add note to a payment
		 *
		 * @param $payment_note mixed Array of payment note arguments<br/>
		 *                      [<br/>
		 *                      'payment_id' => 0,                       // Payment id (int)<br/>
		 *                      'note_content' => '',                    // Note content (string)<br/>
		 *                      'note_date' => current_time( 'mysql' )   // Note date (mysql date format; default to current server time)<br/>
		 *                      ]
		 *
		 * @return int Added note id; 0 on failure
		 * @since 1.0.0
		 */
		public function add_note( $payment_note ) {
			global $wpdb;

			$defaults = array(
				'payment_id'   => 0,
				'note_content' => '',
				'note_date'    => current_time( 'mysql' )
			);

			$query_args = wp_parse_args( $payment_note, $defaults );

			$res = $wpdb->insert( $wpdb->yith_payment_notes, $query_args );

			if ( ! $res ) {
				return 0;
			}

			return $wpdb->insert_id;
		}

		/**
		 * Delete a given note
		 *
		 * @param $payment_note_id int Payment note id
		 *
		 * @return int|bool Number of rows deleted, or false on failure
		 * @since 1.0.0
		 */
		public function delete_note( $payment_note_id ) {
			global $wpdb;

			$res = $wpdb->delete( $wpdb->yith_payment_notes, array( 'ID' => $payment_note_id ) );

			return $res;
		}

		/**
		 * Handle ajax request to add note; excepts payment_id and note_content params in the request
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ajax_add_note() {
			if ( empty( $_REQUEST['payment_id'] ) || empty( $_REQUEST['note_content'] ) ) {
				wp_send_json( false );
			}

			$payment_id   = intval( $_REQUEST['payment_id'] );
			$note_content = trim( esc_html( $_REQUEST['note_content'] ) );
			$note_date    = current_time( 'mysql' );
			$template     = '';
			$res          = $this->add_note( array(
				'payment_id'   => $payment_id,
				'note_content' => $note_content,
				'note_date'    => $note_date
			) );

			if ( $res ) {
				$template = sprintf( '<li rel="%s" class="note">
								<div class="note_content">
									<p>%s</p>
								</div>
								<p class="meta">
									<abbr class="exact-date" title="%s">%s</abbr>
									<a href="#" class="delete_note">%s</a>
								</p>
							 </li>',
					$res,
					$note_content,
					$note_date,
					sprintf( __( 'added on %1$s at %2$s', 'yith-woocommerce-affiliates' ), date_i18n( wc_date_format(), strtotime( $note_date ) ), date_i18n( wc_time_format(), strtotime( $note_date ) ) ),
					__( 'Delete note', 'yith-woocommerce-affiliates' )
				);
			}

			wp_send_json( array( 'res' => $res, 'template' => $template ) );
		}

		/**
		 * Handle ajax requests to delete note; excepts note_id in the request
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ajax_delete_note() {
			if ( empty( $_REQUEST['note_id'] ) ) {
				wp_send_json( false );
			}

			$note_id = intval( $_REQUEST['note_id'] );

			wp_send_json( $this->delete_note( $note_id ) );
		}

		/* === HELPER METHODS === */

		/**
		 * Count payments matching filtering params
		 *
		 * @param $args mixed Filtering params<br/>
		 *              [<br/>
		 *              'ID' => false,                   // Payment ID (int)<br/>
		 *              'user_id' => false,              // Affiliate user ID (int)<br/>
		 *              'affiliate_id' => false,         // Affiliate ID (int)<br/>
		 *              'user_login' => false,           // Affiliate user login, or part of it (string)<br/>
		 *              'user_email' => false,           // Affiliate user email, or part of it (string)<br/>
		 *              'payment_email' => false,        // Payment email, or part of it - the one registered within the payment record (string)<br/>
		 *              'status' => false,               // Payment status (string; on-hold/pending/completed/cancelled)<br/>
		 *              'amount' => false,               // Payment amount (double)<br/>
		 *              'interval' => false,             // Payment creation date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/><br/>
		 *              'completed_between' => false,    // Payment completion  date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/><br/>
		 *              'transaction_key' => false,      // Payment transaction key, or part of it (string)<br/>
		 *              's' => false                     // Search string; will search between user_login, user_email, payment_email, transaction_key
		 *              ]
		 *
		 * @return int Payments count
		 * @see   \YITH_WCAF_Payment_Handler::get_payments
		 * @since 1.0.0
		 */
		public function count_payments( $args = array() ) {
			$defaults = array(
				'ID'                => false,
				'user_id'           => false,
				'affiliate_id'      => false,
				'user_login'        => false,
				'user_email'        => false,
				'payment_email'     => false,
				'status'            => false,
				'amount'            => false,
				'interval'          => false,
				'completed_between' => false,
				'transaction_key'   => false,
				's'                 => false
			);

			$args = wp_parse_args( $args, $defaults );

			return count( $this->get_payments( $args ) );
		}

		/**
		 * Retrieve payments matching filtering params
		 *
		 * @param $args mixed Filtering params<br/>
		 *              [<br/>
		 *              'ID' => false,                   // Payment ID (int)<br/>
		 *              'user_id' => false,              // Affiliate user ID (int)<br/>
		 *              'affiliate_id' => false,         // Affiliate ID (int)<br/>
		 *              'user_login' => false,           // Affiliate user login, or part of it (string)<br/>
		 *              'user_email' => false,           // Affiliate user email, or part of it (string)<br/>
		 *              'payment_email' => false,        // Payment email, or part of it - the one registered within the payment record (string)<br/>
		 *              'status' => false,               // Payment status (string/array of strings; on-hold/pending/completed/cancelled)<br/>
		 *              'amount' => false,               // Payment amount (double)<br/>
		 *              'interval' => false,             // Payment creation date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/><br/>
		 *              'completed_between' => false,    // Payment completion  date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/><br/>
		 *              'transaction_key' => false,      // Payment transaction key, or part of it (string)<br/>
		 *              's' => false                     // Search string; will search between user_login, user_email, payment_email, transaction_key<br/>
		 *              'orderby' => 'ID',               // sorting direction (ASC/DESC)<br/>
		 *              'order' => 'ASC',                // sorting column (any table valid column)<br/>
		 *              'limit' => 0,                    // limit (int)<br/>
		 *              'offset' => 0                    // offset (int)<br/>
		 *              ]
		 *
		 * @return mixed Array with found commissions, or false on failure
		 * @since 1.0.0
		 */
		public function get_payments( $args = array() ) {
			global $wpdb;

			$defaults = array(
				'ID'                => false,
				'user_id'           => false,
				'affiliate_id'      => false,
				'user_login'        => false,
				'user_email'        => false,
				'payment_email'     => false,
				'status'            => false,
				'amount'            => false,
				'interval'          => false,
				'completed_between' => false,
				'transaction_key'   => false,
				's'                 => false,
				'orderby'           => 'ID',
				'order'             => 'ASC',
				'limit'             => 0,
				'offset'            => 0
			);

			$args = wp_parse_args( $args, $defaults );

			$query      = '';
			$query_args = array();

			$query .= "SELECT
			           yp.*,
			           ya.ID AS affiliate_id,
			           ya.token AS affiliate_token,
			           ya.earnings AS affiliate_earnings,
			           ya.paid AS affiliate_paid,
			           ya.refunds AS affiliate_refunds,
			           u.ID AS user_id,
			           u.user_login AS user_login,
			           u.user_email AS user_email
			          FROM {$wpdb->yith_payments} AS yp
			          LEFT JOIN {$wpdb->yith_affiliates} AS ya ON ya.ID = yp.affiliate_id
			          LEFT JOIN {$wpdb->users} AS u ON u.ID = ya.user_id
			          WHERE 1 = 1";

			if ( ! empty( $args['ID'] ) ) {
				$query        .= ' AND yp.ID = %d';
				$query_args[] = $args['ID'];
			}

			if ( ! empty( $args['user_id'] ) ) {
				$query        .= ' AND u.ID = %d';
				$query_args[] = $args['user_id'];
			}

			if ( ! empty( $args['affiliate_id'] ) ) {
				$query        .= ' AND ya.ID = %d';
				$query_args[] = $args['affiliate_id'];
			}

			if ( ! empty( $args['user_login'] ) ) {
				$query        .= ' AND u.user_login LIKE %s';
				$query_args[] = '%' . $args['user_login'] . '%';
			}

			if ( ! empty( $args['user_email'] ) ) {
				$query        .= ' AND u.user_email LIKE %s';
				$query_args[] = '%' . $args['user_email'] . '%';
			}

			if ( ! empty( $args['payment_email'] ) ) {
				$query        .= ' AND ya.payment_email LIKE %s';
				$query_args[] = '%' . $args['payment_email'] . '%';
			}

			if ( ! empty( $args['status'] ) ) {
				if ( ! is_array( $args['status'] ) && in_array( $args['status'], array_keys( $this->_status_labels_map ) ) ) {
					$query        .= ' AND yp.status = %s';
					$query_args[] = $args['status'];
				} elseif ( is_array( $args['status'] ) && $filtered_status = array_intersect( $args['status'], array_keys( $this->_status_labels_map ) ) ) {
					$query .= ' AND yp.status IN ( "' . implode( '","', $filtered_status ) . '" )';
				}
			}

			if ( ! empty( $args['amount'] ) && is_array( $args['amount'] ) && ( isset( $args['amount']['min'] ) || isset( $args['amount']['max'] ) ) ) {
				if ( ! empty( $args['amount']['min'] ) ) {
					$query        .= ' AND yp.amount >= %f';
					$query_args[] = $args['amount']['min'];
				}

				if ( ! empty( $args['amount']['max'] ) ) {
					$query        .= ' AND yp.amount <= %f';
					$query_args[] = $args['amount']['max'];
				}
			}

			if ( ! empty( $args['interval'] ) && is_array( $args['interval'] ) && ( isset( $args['interval']['start_date'] ) || isset( $args['interval']['end_date'] ) ) ) {
				if ( ! empty( $args['interval']['start_date'] ) ) {
					$query        .= ' AND yp.created_at >= %s';
					$query_args[] = $args['interval']['start_date'];
				}

				if ( ! empty( $args['interval']['end_date'] ) ) {
					$query        .= ' AND yp.created_at <= %s';
					$query_args[] = $args['interval']['end_date'];
				}
			}

			if ( ! empty( $args['completed_between'] ) && is_array( $args['completed_between'] ) && ( isset( $args['completed_between']['start_date'] ) || isset( $args['completed_between']['end_date'] ) ) ) {
				if ( ! empty( $args['completed_between']['start_date'] ) ) {
					$query        .= ' AND yp.completed_at >= %s';
					$query_args[] = $args['completed_between']['start_date'];
				}

				if ( ! empty( $args['completed_between']['end_date'] ) ) {
					$query        .= ' AND yp.completed_at <= %s';
					$query_args[] = $args['completed_between']['end_date'];
				}
			}

			if ( ! empty( $args['transaction_key'] ) ) {
				$query        .= ' AND yp.transaction_key LIKE %s';
				$query_args[] = '%' . $args['transaction_key'] . '%';
			}

			if ( ! empty( $args['s'] ) ) {
				$query        .= ' AND ( u.user_login LIKE %s OR u.user_email LIKE %s OR ya.payment_email LIKE %s OR yp.transaction_key LIKE %s )';
				$query_args[] = '%' . $args['s'] . '%';
				$query_args[] = '%' . $args['s'] . '%';
				$query_args[] = '%' . $args['s'] . '%';
				$query_args[] = '%' . $args['s'] . '%';
			}

			if ( ! empty( $args['orderby'] ) ) {
				$query .= sprintf( ' ORDER BY %s %s', $args['orderby'], $args['order'] );
			}

			if ( ! empty( $args['limit'] ) ) {
				$query .= sprintf( ' LIMIT %d, %d', ! empty( $args['offset'] ) ? $args['offset'] : 0, $args['limit'] );
			}

			if ( ! empty( $query_args ) ) {
				$query = $wpdb->prepare( $query, $query_args );
			}

			$res = $wpdb->get_results( $query, ARRAY_A );

			return $res;
		}

		/**
		 * Retrieve a payment with given id
		 *
		 * @param $payment_id int Payment id
		 *
		 * @return mixed Retrieved payment, or false on failure
		 * @since 1.0.0
		 */
		public function get_payment( $payment_id ) {
			global $wpdb;

			$query = "SELECT
			           yp.*,
			           ya.ID AS affiliate_id,
			           ya.token AS affiliate_token,
			           ya.earnings AS affiliate_earnings,
			           ya.paid AS affiliate_paid,
			           ya.refunds AS affiliate_refunds,
			           u.ID AS user_id,
			           u.user_login AS user_login,
			           u.user_email AS user_email
			          FROM {$wpdb->yith_payments} AS yp
			          LEFT JOIN {$wpdb->yith_affiliates} AS ya ON ya.ID = yp.affiliate_id
			          LEFT JOIN {$wpdb->users} AS u ON u.ID = ya.user_id
			          WHERE yp.ID = %d";

			$query_args = array(
				$payment_id
			);

			$res = $wpdb->get_row( $wpdb->prepare( $query, $query_args ), ARRAY_A );

			return $res;
		}

		/**
		 * Returns an array of commission related to a given payment
		 *
		 * @param $payment_id int Payment id
		 *
		 * @return array Array of retrieved commissions; empty set on failure
		 * @since 1.0.0
		 */
		public function get_payment_commissions( $payment_id ) {
			global $wpdb;

			$query      = "SELECT commission_id FROM {$wpdb->yith_payment_commission} WHERE payment_id = %d";
			$query_args = array( $payment_id );

			$commissions_ids = $wpdb->get_col( $wpdb->prepare( $query, $query_args ) );
			$commissions     = array();

			if ( ! empty( $commissions_ids ) ) {
				foreach ( $commissions_ids as $id ) {
					$commissions[] = YITH_WCAF_Commission_Handler()->get_commission( $id );
				}
			}

			return $commissions;
		}

		/**
		 * Return an array of payments related to a given commission
		 *
		 * @param $commission_id int Commission id
		 * @param $status        string Searched payment status; valid values are 'all' (all payments), 'active' (on-hold, pending and completed payments) and 'inactive' (cancelled payments)
		 *
		 * @return mixed Array of payments, or false on failure
		 * @since 1.0.0
		 */
		public function get_commission_payments( $commission_id, $status = 'all' ) {
			global $wpdb;

			$query      = "SELECT payment_id FROM {$wpdb->yith_payment_commission} WHERE commission_id = %d";
			$query_args = array( $commission_id );

			$payments_ids = $wpdb->get_col( $wpdb->prepare( $query, $query_args ) );
			$payments     = array();

			if ( ! empty( $payments_ids ) ) {
				foreach ( $payments_ids as $id ) {
					$res = $this->get_payment( $id );

					if ( ! empty( $res ) ) {
						if ( $status == 'active' && ! in_array( $res['status'], array(
								'on-hold',
								'pending',
								'completed'
							) ) ) {
							continue;
						}

						if ( $status == 'inactive' && ! in_array( $res['status'], array( 'cancelled' ) ) ) {
							continue;
						}

						$payments[] = $res;
					}
				}
			}

			return $payments;
		}

		/**
		 * Register payments for a bunch of commissions; will create different mass pay foreach affiliate referred by commissions
		 *
		 * @param $commissions_id       array|int Array of commissions to pay IDs, or single commission id
		 * @param $proceed_with_payment bool Whether to call gateways to pay, or just register payments
		 *
		 * @return mixed Array with payment status, when \$proceed_with_payment is enabled; false otherwise
		 */
		public function register_payment( $commissions_id, $proceed_with_payment = true, $gateway = false ) {
			// if no commission passed, return
			if ( empty( $commissions_id ) ) {
				return array(
					'status'   => false,
					'messages' => __( 'You have to select at least one commission', 'yith-woocommerce-affiliates' )
				);
			}

			// if single commission id provided, convert it to array
			if ( ! is_array( $commissions_id ) ) {
				$commissions_id = (array) $commissions_id;
			}

			$payments = array();

			foreach ( $commissions_id as $id ) {
				$commission = YITH_WCAF_Commission_Handler()->get_commission( $id );

				// if can't find commission, continue
				if ( ! $commission ) {
					continue;
				}

				$affiliate_id = $commission['affiliate_id'];
				$affiliate    = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id, true );

				// if can't find affiliate, continue
				if ( ! $affiliate ) {
					continue;
				}

				$payment_email = $affiliate['payment_email'];

				// if there is no payment registered for the affiliate, set one
				if ( ! isset( $payments[ $affiliate_id ] ) ) {
					$payments[ $affiliate_id ]                  = array();
					$payments[ $affiliate_id ]['affiliate_id']  = $affiliate_id;
					$payments[ $affiliate_id ]['payment_email'] = $payment_email;
					$payments[ $affiliate_id ]['gateway']       = $gateway ? $gateway : '';
					$payments[ $affiliate_id ]['amount']        = 0;
					$payments[ $affiliate_id ]['commissions']   = array();
				}

				$payments[ $affiliate_id ]['commissions'][] = $commission;
				$payments[ $affiliate_id ]['amount']        += floatval( $commission['amount'] );
			}

			// register payments
			if ( ! empty( $payments ) ) {
				foreach ( $payments as $payment ) {
					$commissions  = $payment['commissions'];
					$payment_args = $payment;
					unset( $payment_args['commissions'] );

					$payment_id = $this->add( $payment_args, $commissions );
				}
			}

			do_action( 'yith_wcaf_after_register_payment', $payments, $gateway );

			return array(
				'status'   => true,
				'messages' => __( 'Payment correctly registered', 'yith-woocommerce-affiliates' )
			);
		}

		/**
		 * Returns status that should be applied to commissions after payment
		 * This changes if gateway requires IPN (default) or if it will instantly confirm payment
		 *
		 * @param $gateway              string Unique gateway identifier
		 * @param $proceed_with_payment bool Whether payment should be processed or just registered
		 *
		 * @return mixed Array with status for the commission, and note to apply
		 * @since 1.0.10
		 */
		public function get_commission_status_after_payment( $gateway, $proceed_with_payment = true ) {
			if ( $gateway && apply_filters( 'yith_wcaf_ipn_gateway', true, $gateway ) ) {
				$status  = 'pending-payment';
				$message = __( 'Payment registered; awaiting for IPN confirmation', 'yith-woocommerce-affiliates' );
			} else {
				$status  = $proceed_with_payment ? 'paid' : 'pending-payment';
				$message = __( 'Payment registered', 'yith-woocommerce-affiliates' );
			}

			return array(
				'status'  => $status,
				'message' => $message
			);
		}

		/**
		 * Change payment status
		 *
		 * @param $payment_id int Payment id
		 * @param $new_status string New status for the payment
		 *
		 * @return int|bool Number of updated lines; false on failure
		 * @since 1.0.0
		 */
		public function change_payment_status( $payment_id, $new_status ) {
			$payment = $this->get_payment( $payment_id );

			if ( ! $payment ) {
				return false;
			}

			$old_status  = $payment['status'];
			$update_args = array( 'status' => $new_status );

			if ( $new_status == 'cancelled' ) {
				$commissions = $this->get_payment_commissions( $payment_id );

				if ( ! empty( $commissions ) ) {
					foreach ( $commissions as $commission ) {
						YITH_WCAF_Commission_Handler()->change_commission_status( $commission['ID'], 'pending' );
					}
				}
			} elseif ( $new_status == 'completed' ) {
				$update_args['completed_at'] = current_time( 'mysql' );
				$commissions                 = $this->get_payment_commissions( $payment_id );

				if ( ! empty( $commissions ) ) {
					foreach ( $commissions as $commission ) {
						YITH_WCAF_Commission_Handler()->change_commission_status( $commission['ID'], 'paid' );
					}
				}
			} else {
				$commissions = $this->get_payment_commissions( $payment_id );

				if ( ! empty( $commissions ) ) {
					foreach ( $commissions as $commission ) {
						YITH_WCAF_Commission_Handler()->change_commission_status( $commission['ID'], 'pending-payment' );
					}
				}
			}

			do_action( 'yith_wcaf_payment_status_' . $new_status, $payment_id );
			do_action( 'yith_wcaf_payment_status_' . $old_status . '_to_' . $new_status, $payment_id );
			do_action( 'yith_wcaf_payment_status_changed', $payment_id, $new_status, $old_status );

			return $this->update( $payment_id, $update_args );
		}

		/**
		 * Return a human friendly version of a payment status
		 *
		 * @param $status string Status to convert to human friendly form
		 *
		 * @return string Human friendly status
		 * @since 1.0.0
		 */
		public function get_readable_status( $status ) {
			if ( isset( $this->_status_labels_map[ $status ] ) ) {
				$label = $this->_status_labels_map[ $status ];
			} else {
				$label = ucfirst( str_replace( '-', ' ', $status ) );
			}

			return apply_filters( "yith_wcaf_{$status}_payment_status_name", $label );
		}

		/**
		 * Returns count of payments, grouped by status
		 *
		 * @param $status string Specific status to count, or all to obtain a global statistic
		 * @param $args   mixed Array of arguments to filter status query<br/>
		 *                [<br/>
		 *                'affiliate_id' => false,         // Affiliate ID (int)<br/>
		 *                'interval' => false,             // Payment creation date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/><br/>
		 *                ]
		 *
		 * @return int|mixed Count per state, or array indexed by status, with status count
		 * @since 1.0.0
		 */
		public function per_status_count( $status = 'all', $args = array() ) {
			global $wpdb;

			$query      = "SELECT yp.status, COUNT( yp.status ) AS status_count 
                      FROM {$wpdb->yith_payments} AS yp 
                      LEFT JOIN {$wpdb->yith_affiliates} AS ya ON ya.ID = yp.affiliate_id
                      WHERE 1 = 1";
			$query_args = array();

			if ( ! empty( $args['affiliate_id'] ) ) {
				$query        .= ' AND ya.ID = %d';
				$query_args[] = $args['affiliate_id'];
			}

			if ( ! empty( $args['interval'] ) && is_array( $args['interval'] ) && ( isset( $args['interval']['start_date'] ) || isset( $args['interval']['end_date'] ) ) ) {
				if ( ! empty( $args['interval']['start_date'] ) ) {
					$query        .= ' AND yp.created_at >= %s';
					$query_args[] = $args['interval']['start_date'];
				}

				if ( ! empty( $args['interval']['end_date'] ) ) {
					$query        .= ' AND yp.created_at <= %s';
					$query_args[] = $args['interval']['end_date'];
				}
			}

			$query .= " GROUP BY status";

			if ( ! empty( $query_args ) ) {
				$query = $wpdb->prepare( $query, $query_args );
			}

			$res = $wpdb->get_results( $query, ARRAY_A );

			$statuses = yith_wcaf_array_column( $res, 'status' );
			$counts   = yith_wcaf_array_column( $res, 'status_count' );

			if ( $status == 'all' ) {
				return array_sum( $counts );
			} elseif ( in_array( $status, $statuses ) ) {
				$index = array_search( $status, $statuses );

				if ( $index === false ) {
					return 0;
				} else {
					return $counts[ $index ];
				}
			} else {
				return 0;
			}
		}

		/* === WITHDRAW METHODS */

		/**
		 * Check whether payment has invoice
		 *
		 * @param $payment_id int Payment ID
		 *
		 * @return bool Whether payment has invoice or not
		 */
		public function has_invoice( $payment_id ) {
			return false;
		}

		/**
		 * Get path to invoice
		 *
		 * @param $payment_id int Payment ID
		 *
		 * @return string Path to invoice, or empty if there is no invoice
		 */
		public function get_invoice_path( $payment_id ) {
			return '';
		}

		/**
		 * Get url to invoice
		 *
		 * @param $payment_id int Payment ID
		 *
		 * @return string Url to invoice, or empty if there is no invoice
		 */
		public function get_invoice_url( $payment_id ) {
			return '';
		}

		/**
		 * Get url to let user download invoice
		 *
		 * @param $payment_id int Payment ID
		 *
		 * @return string Url to download invoice, or empty if there is no invoice
		 */
		public function get_invoice_publishable_url( $payment_id ) {
			return '';
		}

		/* === UPDATE PAYMENT ON EXTERNAL CHANGES === */

		/**
		 * Update payment email when affiliates updates it
		 * Applies only to on-hold payments (not sent yet) with empty "payment_email" field
		 *
		 * @return void
		 * @since 1.1.1
		 */
		public function update_payment_email_on_affiliate_change( $affiliate_id, $new_payment_email ) {
			$payments = $this->get_payments( array(
				'affiliate_id' => $affiliate_id,
				'status'       => 'on-hold'
			) );

			if ( ! empty( $payments ) ) {
				foreach ( $payments as $payment ) {
					if ( $payment['payment_email'] != $new_payment_email ) {
						$this->update( $payment['ID'], array(
							'payment_email' => $new_payment_email
						) );
					}
				}
			}
		}

		/* === PANEL PAYMENTS METHODS === */

		/**
		 * Print payment panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_payment_panel() {
			// define variables to use in template
			$payments_table = new YITH_WCAF_Payments_Table();
			$payments_table->prepare_items();

			// require rate panel template
			include( YITH_WCAF_DIR . 'templates/admin/payment-panel-table.php' );
		}

		/**
		 * Process bulk action for current view
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function process_bulk_actions() {
			if ( ! empty( $_REQUEST['payments'] ) ) {
				$current_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
				$current_action = ( empty( $current_action ) && isset( $_REQUEST['action2'] ) ) ? $_REQUEST['action2'] : $current_action;
				$redirect       = esc_url_raw( add_query_arg( array(
					'page' => 'yith_wcaf_panel',
					'tab'  => 'payments'
				), admin_url( 'admin.php' ) ) );

				// handles payment actions
				switch ( $current_action ) {
					case 'switch-to-completed':
						foreach ( $_REQUEST['payments'] as $payment_id ) {
							$res      = $this->change_payment_status( $payment_id, 'completed' );
							$redirect = esc_url_raw( add_query_arg( 'commission_status_change', $res, $redirect ) );
						}
						break;
					case 'switch-to-on-hold':
						foreach ( $_REQUEST['payments'] as $payment_id ) {
							$res      = $this->change_payment_status( $payment_id, 'on-hold' );
							$redirect = esc_url_raw( add_query_arg( 'commission_status_change', $res, $redirect ) );
						}
						break;
					case 'switch-to-cancelled':
						foreach ( $_REQUEST['payments'] as $payment_id ) {
							$res      = $this->change_payment_status( $payment_id, 'cancelled' );
							$redirect = esc_url_raw( add_query_arg( 'commission_status_change', $res, $redirect ) );
						}
						break;
					case 'delete':
						foreach ( $_REQUEST['payments'] as $payment_id ) {
							$res      = $this->delete( $payment_id );
							$redirect = esc_url_raw( add_query_arg( 'commission_deleted', $res, $redirect ) );
						}
						break;
					default:
						break;
				}

				if ( isset( $_GET['payment_id'] ) ) {
					$redirect = add_query_arg( 'payment_id', intval( $_GET['payment_id'] ), $redirect );
				}

				wp_redirect( esc_url_raw( $redirect ) );
				die();
			}
		}

		/**
		 * Add Screen option
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_screen_option() {
			if ( 'yith-plugins_page_yith_wcaf_panel' == get_current_screen()->id && isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' ) {
				add_screen_option( 'per_page', array(
					'label'   => __( 'Payments', 'yith-woocommerce-affiliates' ),
					'default' => 20,
					'option'  => 'edit_payments_per_page'
				) );

			}
		}

		/**
		 * Save custom screen options
		 *
		 * @param $set    bool Value to filter (default to false)
		 * @param $option string Custom screen option key
		 * @param $value  mixed Custom screen option value
		 *
		 * @return mixed Value to be saved as user meta; false if no value should be saved
		 */
		public function set_screen_option( $set, $option, $value ) {
			return ( isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' && 'edit_payments_per_page' == $option ) ? $value : $set;
		}

		/**
		 * Add columns filters to commissions page
		 *
		 * @param $columns mixed Available columns
		 *
		 * @return mixed The columns array to print
		 * @since 1.0.0
		 */
		public function add_screen_columns( $columns ) {
			if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' ) {
				$columns = array_merge(
					$columns,
					array(
						'status'        => __( 'Status', 'yith-woocommerce-affiliates' ),
						'id'            => __( 'ID', 'yith-woocommerce-affiliates' ),
						'affiliate'     => __( 'Affiliate', 'yith-woocommerce-affiliates' ),
						'payment_email' => __( 'Payment email', 'yith-woocommerce-affiliates' ),
						'amount'        => __( 'Amount', 'yith-woocommerce-affiliates' ),
						'created_at'    => __( 'Created at', 'yith-woocommerce-affiliates' ),
						'completed_at'  => __( 'Completed at', 'yith-woocommerce-affiliates' ),
					)
				);
			}

			return $columns;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Payment_Handler
		 * @since 1.0.2
		 */
		public static function get_instance() {
			if ( class_exists( 'YITH_WCAF_Payment_Handler_Premium' ) ) {
				return YITH_WCAF_Payment_Handler_Premium::get_instance();
			} else {
				if ( is_null( YITH_WCAF_Payment_Handler::$instance ) ) {
					YITH_WCAF_Payment_Handler::$instance = new YITH_WCAF_Payment_Handler;
				}

				return YITH_WCAF_Payment_Handler::$instance;
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Payment_Handler class
 *
 * @return \YITH_WCAF_Payment_Handler
 * @since 1.0.0
 */
function YITH_WCAF_Payment_Handler() {
	return YITH_WCAF_Payment_Handler::get_instance();
}