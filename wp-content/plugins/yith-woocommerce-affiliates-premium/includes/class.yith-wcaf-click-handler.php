<?php
/**
 * Click Handler class
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

if ( ! class_exists( 'YITH_WCAF_Click_Handler' ) ) {
	/**
	 * WooCommerce Click Handler
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Click_Handler {

		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Click_Handler
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Minimum number of seconds to pass before count two different click
		 *
		 * @var int
		 * @since 1.0.0
		 */
		protected $_hit_resolution = 60;

		/**
		 * Whether or not to enable click registering
		 *
		 * @var bool
		 * @since 1.5.2
		 */
		protected $_enabled = true;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Click_Handler
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->_retrieve_options();

			if ( $this->are_hits_registered() ) {
				// register click
				add_action( 'init', array( $this, 'register_hit' ), 20 );

				// register conversion handling
				add_action( 'woocommerce_order_status_processing', array( $this, 'register_conversion' ), 10, 1 );
				add_action( 'woocommerce_order_status_completed', array( $this, 'register_conversion' ), 10, 1 );
				add_action( 'woocommerce_order_status_on-hold', array( $this, 'unregister_conversion' ), 10, 1 );
				add_action( 'woocommerce_order_status_pending-payment', array(
					$this,
					'unregister_conversion'
				), 10, 1 );
				add_action( 'woocommerce_order_status_failed', array( $this, 'unregister_conversion' ), 10, 1 );
				add_action( 'woocommerce_order_status_cancelled', array( $this, 'unregister_conversion' ), 10, 1 );
				add_action( 'woocommerce_order_status_refunded', array( $this, 'unregister_conversion' ), 10, 1 );
			}
		}

		/**
		 * Init class attributes for admin options
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _retrieve_options() {
			$this->_enabled        = 'no' != get_option( 'yith_wcaf_click_enabled', 'yes' );
			$this->_hit_resolution = get_option( 'yith_wcaf_click_resolution', $this->_hit_resolution );
		}

		/* === CLICK/CONVERSION HANDLER === */

		/**
		 * Registers an hit in clicks table
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_hit() {
			global $wpdb;

			$token      = YITH_WCAF_Affiliate()->get_token();
			$token_name = YITH_WCAF_Affiliate()->get_ref_name();
			$affiliate  = YITH_WCAF_Affiliate()->get_affiliate();

			if ( $affiliate && $token !== false && isset( $_REQUEST[ $token_name ] ) ) {

				$requester_link = apply_filters( 'yith_wcaf_requester_link', $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] );

				$requester_ip          = $_SERVER['REMOTE_ADDR'];
				$requester_origin      = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
				$requester_base_origin = ! empty( $requester_origin ) ? @ parse_url( $requester_origin, PHP_URL_HOST ) : '';

				$already_inserted = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( yc.ID ) FROM {$wpdb->yith_clicks} AS yc WHERE yc.IP = %s AND yc.affiliate_id = %d AND yc.click_date > %s", $requester_ip, $affiliate['ID'], date( 'Y-m-d H:i:s', time() - $this->_hit_resolution ) ) );

				if ( intval( $already_inserted ) == 0 ) {
					$wpdb->insert(
						$wpdb->yith_clicks,
						array_merge(
							array(
								'affiliate_id' => $affiliate['ID'],
								'link'         => $requester_link,
								'IP'           => $requester_ip,
								'click_date'   => date( 'Y-m-d H:i:s', time() )
							),
							! empty( $requester_origin ) ? array( 'origin' => $requester_origin ) : array(),
							! empty( $requester_base_origin ) ? array( 'origin_base' => $requester_base_origin ) : array()
						)
					);

					// update referrer click count
					$click_count = $affiliate['click'];
					YITH_WCAF_Affiliate_Handler()->update( $affiliate['ID'], array( 'click' => ++ $click_count ) );
				}
			}
		}

		/**
		 * Register conversion when an order changes to complete or processing status
		 *
		 * @param $order_id int Order id
		 *
		 * @return bool Status of the operation
		 * @since 1.0.0
		 */
		public function register_conversion( $order_id ) {
			global $wpdb;

			$order = wc_get_order( $order_id );

			$row_to_change      = yit_get_prop( $order, '_yith_wcaf_click_id', true );
			$already_registered = yit_get_prop( $order, '_yith_wcaf_conversion_registered', true );

			if ( ! $row_to_change || $already_registered ) {
				return false;
			}

			$row = $this->get_hit( $row_to_change );

			if ( ! $row ) {
				return false;
			}

			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $row['affiliate_id'] );

			if ( ! $affiliate ) {
				return false;
			}

			// calculate conv time
			$actual_time = time();
			$click_time  = strtotime( $row['click_date'] );
			$conv_time   = $actual_time - $click_time;

			$res = $wpdb->update(
				$wpdb->yith_clicks,
				array(
					'order_id'  => $order_id,
					'conv_date' => current_time( 'mysql' ),
					'conv_time' => $conv_time
				),
				array(
					'ID' => $row_to_change
				)
			);

			if ( $res ) {
				// update referrer conversion count
				$conversion_count = $affiliate['conversion'];
				YITH_WCAF_Affiliate_Handler()->update( $affiliate['ID'], array( 'conversion' => ++ $conversion_count ) );

				// update order meta
				yit_save_prop( $order, '_yith_wcaf_conversion_registered', true );
			}

			return $res;
		}

		/**
		 * Deregister conversion when order switch to on-hold/cancelled/refunded
		 *
		 * @param $order_id int Order int
		 *
		 * @return boolean Operation status
		 * @since 1.0.0
		 */
		public function unregister_conversion( $order_id ) {
			global $wpdb;

			$order = wc_get_order( $order_id );

			$row_to_change      = yit_get_prop( $order, '_yith_wcaf_click_id', true );
			$already_registered = yit_get_prop( $order, '_yith_wcaf_conversion_registered', true );

			if ( ! $row_to_change || ! $already_registered ) {
				return false;
			}

			$row = $this->get_hit( $row_to_change );

			if ( ! $row ) {
				return false;
			}

			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $row['affiliate_id'] );

			if ( ! $affiliate ) {
				return false;
			}

			// calculate conv time
			$actual_time = time();
			$click_time  = strtotime( $row['click_date'] );
			$conv_time   = $actual_time - $click_time;

			$res = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->yith_clicks} SET order_id = NULL, conv_date = NULL, conv_time = NULL WHERE ID = %d", $row_to_change ) );

			if ( $res ) {
				// update referrer conversion count
				$conversion_count = $affiliate['conversion'];
				YITH_WCAF_Affiliate_Handler()->update( $affiliate['ID'], array( 'conversion' => -- $conversion_count ) );

				// update order meta
				yit_save_prop( $order, '_yith_wcaf_conversion_registered', false );
			}

			return $res;
		}

		/* === HELPER METHODS === */

		/**
		 * Checks whether click system is enabled or not
		 *
		 * @return bool Whether click system is enabled
		 */
		public function are_hits_registered() {
			return $this->_enabled;
		}

		/**
		 * Return number of clicks matching filtering criteria
		 *
		 * @param $args mixed Filtering criteria<br/>
		 *              [<br/>
		 *              'user_id' => false,              // click related user id (int)<br/>
		 *              'affiliate_id' => false,         // click affiliate id (int)<br/>
		 *              'referrer_login' => false,       // click related user login, or part of it (string)<br/>
		 *              'link' => false,                 // click visited link, or part of it (string)<br/>
		 *              'origin' => false,               // click origin link, or part of it (string)<br/>
		 *              'origin_base' => false,          // click origin link base, or part of it (string)<br/>
		 *              'ip' => false,                   // click user IP, or part of it (string)<br/>
		 *              'interval' => false,             // click date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/>
		 *              'converted' => false,            // whether click converted or not (yes/no)<br/>
		 *              'order_id' => false,             // click related order id (int)<br/>
		 *              ]
		 *
		 * @return int Number of counted clicks
		 * @use   YITH_WCAF_Click_Handler::get_clicks()
		 * @since 1.0.0
		 */
		public function count_hits( $args = array() ) {
			$defaults = array(
				'user_id'        => false,
				'affiliate_id'   => false,
				'referrer_login' => false,
				'referrer_email' => false,
				'link'           => false,
				'origin'         => false,
				'origin_base'    => false,
				'ip'             => false,
				'interval'       => false,
				'converted'      => false,
				'order_id'       => false,
			);

			$args = wp_parse_args( $args, $defaults );

			return count( $this->get_hits( $args ) );
		}

		/**
		 * Return clicks matching filtering criteria
		 *
		 * @param $args mixed Filtering criteria<br/>
		 *              [<br/>
		 *              'user_id' => false,              // click related user id (int)<br/>
		 *              'affiliate_id' => false,         // click affiliate id (int)<br/>
		 *              'referrer_login' => false,       // click related user login, or part of it (string)<br/>
		 *              'link' => false,                 // click visited link, or part of it (string)<br/>
		 *              'origin' => false,               // click origin link, or part of it (string)<br/>
		 *              'origin_base' => false,          // click origin link base, or part of it (string)<br/>
		 *              'ip' => false,                   // click user IP, or part of it (string)<br/>
		 *              'interval' => false,             // click date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/>
		 *              'converted' => false,            // whether click converted or not (yes/no)<br/>
		 *              'order_id' => false,             // click related order id (int)<br/>
		 *              'order' => 'DESC',               // sorting direction (ASC/DESC)<br/>
		 *              'orderby' => 'ID',               // sorting column (any table valid column)<br/>
		 *              'limit' => 0,                    // limit (int)<br/>
		 *              'offset' => 0                    // offset (int)<br/>
		 *              ]
		 *
		 * @return mixed Matching clicks
		 * @since 1.0.0
		 */
		public function get_hits( $args = array() ) {
			global $wpdb;

			$defaults = array(
				'user_id'        => false,
				'affiliate_id'   => false,
				'referrer_login' => false,
				'referrer_email' => false,
				'link'           => false,
				'origin'         => false,
				'origin_base'    => false,
				'ip'             => false,
				'interval'       => false,
				'converted'      => false,
				'order_id'       => false,
				'orderby'        => 'click_date',
				'order'          => 'DESC',
				'limit'          => 0,
				'offset'         => 0
			);

			$args = wp_parse_args( $args, $defaults );

			$query     = '';
			$query_arg = array();

			$query .= "SELECT
					    yc.*,
					    ya.user_id AS user_id,
					    u.user_login AS user_login,
					    u.user_email AS user_email
					   FROM {$wpdb->yith_clicks} AS yc
					   LEFT JOIN {$wpdb->yith_affiliates} AS ya ON ya.ID = yc.affiliate_id
					   LEFT JOIN {$wpdb->users} AS u ON u.ID = ya.user_id
					   WHERE 1 = 1";

			if ( ! empty( $args['affiliate_id'] ) ) {
				$query       .= ' AND yc.affiliate_id = %d';
				$query_arg[] = $args['affiliate_id'];
			}

			if ( ! empty( $args['user_id'] ) ) {
				$query       .= ' AND ya.user_id = %d';
				$query_arg[] = $args['user_id'];
			}

			if ( ! empty( $args['referrer_login'] ) ) {
				$query       .= ' AND u.user_login LIKE %s';
				$query_arg[] = '%' . $args['referrer_login'] . '%';
			}

			if ( ! empty( $args['referrer_email'] ) ) {
				$query       .= ' AND u.user_email LIKE %s';
				$query_arg[] = '%' . $args['referrer_email'] . '%';
			}

			if ( ! empty( $args['link'] ) ) {
				$query       .= ' AND yc.link LIKE %s';
				$query_arg[] = '%' . $args['link'] . '%';
			}

			if ( ! empty( $args['origin'] ) ) {
				$query       .= ' AND yc.origin LIKE %s';
				$query_arg[] = '%' . $args['origin'] . '%';
			}

			if ( ! empty( $args['origin_base'] ) ) {
				$query       .= ' AND yc.origin_base LIKE %s';
				$query_arg[] = '%' . $args['origin_base'] . '%';
			}

			if ( ! empty( $args['ip'] ) ) {
				$query       .= ' AND yc.IP LIKE %s';
				$query_arg[] = '%' . $args['ip'] . '%';
			}

			if ( ! empty( $args['interval'] ) && is_array( $args['interval'] ) && ( isset( $args['interval']['start_date'] ) || isset( $args['interval']['end_date'] ) ) ) {
				if ( ! empty( $args['interval']['start_date'] ) ) {
					$query       .= ' AND yc.click_date >= %s';
					$query_arg[] = $args['interval']['start_date'];
				}

				if ( ! empty( $args['interval']['end_date'] ) ) {
					$query       .= ' AND yc.click_date <= %s';
					$query_arg[] = $args['interval']['end_date'];
				}
			}

			if ( ! empty( $args['converted'] ) && $args['converted'] == 'yes' ) {
				$query .= ' AND order_id IS NOT NULL';
			} elseif ( ! empty( $args['converted'] ) && $args['converted'] == 'no' ) {
				$query .= ' AND order_id IS NULL';
			}

			if ( ! empty( $args['order_id'] ) ) {
				$query       .= ' AND yc.order_id = %d';
				$query_arg[] = $args['order_id'];
			}

			if ( ! empty( $args['orderby'] ) ) {
				$query .= sprintf( ' ORDER BY %s %s', $args['orderby'], $args['order'] );
			}

			if ( ! empty( $args['limit'] ) ) {
				$query .= sprintf( ' LIMIT %d, %d', ! empty( $args['offset'] ) ? $args['offset'] : 0, $args['limit'] );
			}

			if ( ! empty( $query_arg ) ) {
				$query = $wpdb->prepare( $query, $query_arg );
			}

			$res = $wpdb->get_results( $query, ARRAY_A );

			return $res;
		}

		/**
		 * Return specific hit by ID
		 *
		 * @param $hit_id int Hit ID
		 *
		 * @return mixed Database row, or false on failure
		 * @since 1.0.0
		 */
		public function get_hit( $hit_id ) {
			global $wpdb;

			$query      = "SELECT
					   yc.*,
					   ya.user_id AS user_id,
					   u.user_login AS user_login,
					   u.user_email AS user_email
					  FROM {$wpdb->yith_clicks} AS yc
					  LEFT JOIN {$wpdb->yith_affiliates} AS ya ON ya.ID = yc.affiliate_id
					  LEFT JOIN {$wpdb->users} AS u ON u.ID = ya.user_id
					  WHERE yc.ID = %d";
			$query_args = array(
				$hit_id
			);

			$res = $wpdb->get_row( $wpdb->prepare( $query, $query_args ), ARRAY_A );

			return $res;
		}

		/**
		 * Return last hit with current requester IP and affiliate token
		 *
		 * @return mixed Database row, or false on failure
		 * @since 1.0.0
		 */
		public function get_last_hit() {
			$user = YITH_WCAF_Affiliate()->get_user();

			if ( ! $user ) {
				return false;
			}

			$requester_ip = $_SERVER['REMOTE_ADDR'];
			$user_id      = $user->ID;

			$rows = $this->get_hits( array(
				'user_id' => $user_id,
				'IP'      => $requester_ip,
				'limit'   => 1,
				'order'   => 'DESC'
			) );

			if ( empty( $rows ) ) {
				return false;
			}

			$row_to_update    = $rows[0];
			$row_to_update_id = $row_to_update['ID'];

			return $row_to_update_id;
		}

		/**
		 * Get hits stats
		 *
		 * @param $stat string Id of the stat to retrieve [total_amount/total_refunds]
		 * @param $args mixed Filtering params<br/>
		 *              [<br/>
		 *              'converted' => false,     // whether clicks should be converted or not (yes/no)<br/>
		 *              'affiliate_id' => false,  // click related affiliate ID<br/>
		 *              'user_id' => false,       // click related user ID<br/>
		 *              'interval' => false       // click date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/>
		 *              ]
		 *
		 * @return mixed Stat value, or false if stat does not match acceptable values
		 * @since 1.0.0
		 */
		public function get_hit_stats( $stat, $args = array() ) {
			global $wpdb;

			$available_stats = array(
				'total_clicks'      => 'ID',
				'total_conversions' => 'order_id',
				'avg_conv_time'     => 'conv_time'
			);

			if ( ! in_array( $stat, array_keys( $available_stats ) ) ) {
				return false;
			}

			$defaults = array(
				'converted'    => false,
				'affiliate_id' => false,
				'user_id'      => false,
				'interval'     => false
			);

			$args = wp_parse_args( $args, $defaults );

			$query     = '';
			$query_arg = array();

			$query = "SELECT" .
					 ( ( $stat == 'avg_conv_time' ) ? " AVG( yc.{$available_stats[$stat]} ) " : " COUNT( yc.{$available_stats[$stat]} ) " ) .
					 "FROM {$wpdb->yith_clicks} AS yc
			          LEFT JOIN {$wpdb->yith_affiliates} AS ya ON yc.affiliate_id = ya.ID " .
					 ( ( $stat == 'total_conversions' ) ? " LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS i ON yc.order_id = i.order_id " : " " ) .
					 "LEFT JOIN {$wpdb->users} AS u ON ya.user_id = u.ID
			          WHERE 1=1";

			if ( $stat == 'total_conversions' ) {
				$query       .= " AND i.order_item_type = %s";
				$query_arg[] = 'line_item';
			}

			if ( ! empty( $args['converted'] ) && $args['converted'] == 'yes' ) {
				$query .= ' AND order_id IS NOT NULL';
			} elseif ( ! empty( $args['converted'] ) && $args['converted'] == 'no' ) {
				$query .= ' AND order_id IS NULL';
			}

			if ( ! empty( $args['affiliate_id'] ) ) {
				$query       .= ' AND yc.affiliate_id = %d';
				$query_arg[] = $args['affiliate_id'];
			}

			if ( ! empty( $args['user_id'] ) ) {
				$query       .= ' AND yc.user_id = %d';
				$query_arg[] = $args['user_id'];
			}

			if ( ! empty( $args['interval'] ) && is_array( $args['interval'] ) && ( isset( $args['interval']['start_date'] ) || isset( $args['interval']['end_date'] ) ) ) {
				if ( ! empty( $args['interval']['start_date'] ) ) {
					$query       .= ' AND yc.click_date >= %s';
					$query_arg[] = $args['interval']['start_date'];
				}

				if ( ! empty( $args['interval']['end_date'] ) ) {
					$query       .= ' AND yc.click_date <= %s';
					$query_arg[] = $args['interval']['end_date'];
				}
			}

			if ( ! empty( $query_arg ) ) {
				$query = $wpdb->prepare( $query, $query_arg );
			}

			$res = $wpdb->get_var( $query );

			return $res;
		}

		/**
		 * Delete hit form clicks table
		 *
		 * @param $args mixed Array of parameters<br/>
		 *              [<br/>
		 *              'time' => '-1 week' // valid strtotme() input, used to calculate a delimiter date; clicks before that delimiter, will be deleted<br/>
		 *              'affiliate_id' => false // a valid affiliate id, or false if none
		 *              ]
		 *
		 * @return bool Status of the operation
		 * @use   strtotime()
		 * @since 1.0.0
		 */
		public function delete_hits( $args = array() ) {
			global $wpdb;

			$defaults = array(
				'time'         => '-1 week',
				'affiliate_id' => false
			);

			$args = wp_parse_args( $args, $defaults );

			$query = "DELETE FROM {$wpdb->yith_clicks} WHERE 1";

			if ( $args['time'] ) {
				$query        .= " AND click_date <= %s";
				$query_args[] = date( 'Y-m-d H:i:s', strtotime( $args['time'] ) );
			}

			if ( $args['affiliate_id'] ) {
				$query        .= " AND affiliate_id = %d";
				$query_args[] = $args['affiliate_id'];
			}

			if ( ! empty( $query_args ) ) {
				$query = $wpdb->prepare( $query, $query_args );
			}

			return $wpdb->query( $query );
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Click_Handler
		 * @since 1.0.2
		 */
		public static function get_instance() {
			if ( class_exists( 'YITH_WCAF_Click_Handler_Premium' ) ) {
				return YITH_WCAF_Click_Handler_Premium::get_instance();
			} else {
				if ( is_null( YITH_WCAF_Click_Handler::$instance ) ) {
					YITH_WCAF_Click_Handler::$instance = new YITH_WCAF_Click_Handler;
				}

				return YITH_WCAF_Click_Handler::$instance;
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Click_Handler class
 *
 * @return \YITH_WCAF_Click_Handler
 * @since 1.0.0
 */
function YITH_WCAF_Click_Handler() {
	return YITH_WCAF_Click_Handler::get_instance();
}