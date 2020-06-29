<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}


/**
 * YWPAR_Affiliates class to add compatibility with YITH WooCommerce Affiliates Premium
 *
 * @class   YWPAR_Affiliates
 * @package YITH WooCommerce Points and Rewards
 * @since   1.6.0
 * @author  YITH
 */
if ( ! class_exists( 'YWPAR_Affiliates' ) ) {
	/**
	 * Class YWPAR_Affiliates
	 */
	class YWPAR_Affiliates {
		/**
		 * Single instance of the class
		 *
		 * @var \YWPAR_Affiliates
		 * @since 1.6.0
		 */
		protected static $instance;

		/**
		 * Check if the
		 *
		 * @var bool
		 */
		protected $commission_assigned = false;

		/**
		 * List of status that allows referred user to receive commissions
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_unassigned_status = array(
			'not-confirmed',
			'cancelled',
			'refunded',
			'trash',
		);

		/**
		 * List of status that don't allows referred user to receive commissions
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_assigned_status = array(
			'pending',
			'pending-payment',
			'paid',
		);

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWPAR_Affiliates
		 * @since 1.6.0
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
		 * Initialize plugin and registers actions and filters to be used.
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {
			add_filter( 'ywpar_show_admin_tabs', array( $this, 'add_admin_tab' ) );
			if ( 'yes' == YITH_WC_Points_Rewards()->get_option( 'affiliates_enabled' ) ) {
				add_filter( 'yith_wcaf_create_order_commissions', array( $this, 'calculate_point_earned_by_affiliate' ), 10, 4 );
				add_action( 'yith_wcaf_refeal_totals_table', array( $this, 'add_point_earned_by_affiliate' ) );
				add_action( 'yith_wcaf_commission_status_pending', array( $this, 'add_points_to_affiliate' ) );
				add_action( 'yith_wcaf_commission_status_changed', array( $this, 'check_points_commission' ), 10, 3 );
				add_action( 'woocommerce_order_partially_refunded', array( $this, 'remove_order_points_refund' ), 11, 2 );
				add_action( 'ywpar_customer_removed_points', array( $this, 'customer_removed_points' ), 12, 2 );
			}
		}

		/**
		 * Remove points for partial refund when the calculation type id percentage.
		 *
		 * @param $points
		 * @param $order
		 */
		function customer_removed_points( $points, $order ) {
			$calculation_type = YITH_WC_Points_Rewards()->get_option( 'affiliates_earning_conversion_points' );
			if ( $calculation_type == 'percentage' ) {
				$percentage        = (int) YITH_WC_Points_Rewards()->get_option( 'affiliates_earning_percentage' );
				$commission_points = round( $points * $percentage / 100 );
				$affiliate_token   = yit_get_post_meta( $order, '_yith_wcaf_referral' );
				$affiliate         = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $affiliate_token );
				YITH_WC_Points_Rewards()->add_point_to_customer( $affiliate['user_id'], - $commission_points, 'affiliates', __( 'Removed commission for refunded order', 'yith-woocommerce-points-and-rewards' ), $order->get_id() );
			}
		}

		/**
		 * Check the status of the commission to add or remove point to the affiliate.
		 *
		 * @param $commission_id
		 * @param $new_status
		 * @param $old_status
		 */
		public function check_points_commission( $commission_id, $new_status, $old_status ) {
			if ( in_array( $old_status, $this->_assigned_status ) && in_array( $new_status, $this->_unassigned_status ) ) {
				// remove points
				$this->remove_points_to_affiliate( $commission_id );
			}

			if ( in_array( $new_status, $this->_assigned_status ) && in_array( $old_status, $this->_unassigned_status ) ) {
				// add points
				$this->add_points_to_affiliate( $commission_id );
			}
		}

		/**
		 * Add this row inside the metabox affiliate of the order.
		 *
		 * @param $order
		 */
		public function add_point_earned_by_affiliate( $order ) {
			$already_registered = yit_get_prop( $order, '_ywpar_affiliate_commission_registered', true );
			$tot_points         = $this->get_total_points( $order );
			if ( $already_registered && $tot_points ) {
				echo '<tr>
				<td class="label">' . esc_html( __( 'Points Earned:', 'yith-woocommerce-points-and-rewards' ) ) . '</td>
				<td class="total">' . esc_html( $tot_points ) . '</td>
			</tr>';
			}
		}

		/**
		 *  Return the current point earned by the affiliate
		 *
		 * @param $order
		 *
		 * @return int
		 */
		function get_total_points( $order ) {
			$commission_points     = (int) yit_get_prop( $order, '_ywpar_affiliate_commission_point' );
			$total_points_refunded = yit_get_prop( $order, '_ywpar_affiliate_total_points_refunded', true );
			$tot_point             = $total_points_refunded ? ( $commission_points - (int) $total_points_refunded ) : $commission_points;

			return $tot_point;
		}

		/**
		 * Add the tab Affiliates inside the admin panel.
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function add_admin_tab( $tabs ) {
			$tabs['affiliates'] = __( 'Affiliates', 'yith-woocommerce-points-and-rewards' );
			return $tabs;
		}

		/**
		 * Calculate the points to assign to affiliates.
		 *
		 * @param $result
		 * @param $order_id
		 * @param $token
		 * @param $token_origin
		 *
		 * @return mixed
		 */
		function calculate_point_earned_by_affiliate( $result, $order_id, $token, $token_origin ) {
			$affiliate         = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $token );
			$order             = wc_get_order( $order_id );
			$commission_points = 0;
			if ( $affiliate && $order instanceof WC_Order ) {
				$calculation_type = YITH_WC_Points_Rewards()->get_option( 'affiliates_earning_conversion_points' );
				if ( $order instanceof WC_Order ) {
					switch ( $calculation_type ) {
						case 'fixed':
							$commission_points = YITH_WC_Points_Rewards()->get_option( 'affiliates_earning_fixed' );
							break;
						case 'percentage':
							$points_earned_by_customer = (int) get_post_meta( $order_id, 'ywpar_points_from_cart', 1 );
							$percentage                = (int) YITH_WC_Points_Rewards()->get_option( 'affiliates_earning_percentage' );
							$commission_points         = round( $points_earned_by_customer * $percentage / 100 );
							break;
						case 'conversion':
							$conversion_rate = YITH_WC_Points_Rewards()->get_option( 'affiliates_earning_conversion' );
							if ( isset( $conversion_rate[ $order->get_currency() ] ) ) {
								$conversion_rate   = $conversion_rate[ $order->get_currency() ];
								$commission_points = round( $order->get_subtotal() / $conversion_rate['money'] * $conversion_rate['points'] );
							}
							break;
						default:
					}
				}
			}
			if ( $commission_points > 0 ) {
				yit_save_prop( $order, '_ywpar_affiliate_commission_point', $commission_points );
			}

			return $result;
		}

		/**
		 * Assign the points to affiliate.
		 *
		 * @param $commission_id
		 */
		public function add_points_to_affiliate( $commission_id ) {

			if ( $this->commission_assigned ) {
				return;
			}

			$commission = YITH_WCAF_Commission_Handler()->get_commission( $commission_id );

			if ( ! $commission ) {
				return;
			}

			$affiliate          = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $commission['affiliate_id'] );
			$order_id           = $commission['order_id'];
			$order              = wc_get_order( $order_id );
			$already_registered = yit_get_prop( $order, '_ywpar_affiliate_commission_registered', true );
			if ( $affiliate && $order instanceof WC_Order && ! $already_registered ) {
				$commission_points = yit_get_prop( $order, '_ywpar_affiliate_commission_point' );
				yit_save_prop( $order, '_ywpar_affiliate_commission_registered', true );
				YITH_WC_Points_Rewards()->add_point_to_customer( $affiliate['user_id'], $commission_points, 'affiliates', '', $order_id );
			}

			$this->commission_assigned = true;
		}

		/**
		 * Remove points to the affiliate
		 *
		 * @param $commission_id
		 */
		public function remove_points_to_affiliate( $commission_id ) {
			$commission = YITH_WCAF_Commission_Handler()->get_commission( $commission_id );

			if ( ! $commission ) {
				return;
			}

			$affiliate          = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $commission['affiliate_id'] );
			$order_id           = $commission['order_id'];
			$order              = wc_get_order( $order_id );
			$already_registered = yit_get_prop( $order, '_ywpar_affiliate_commission_registered', true );

			if ( $affiliate && $order instanceof WC_Order && $already_registered ) {
				$tot_points = $this->get_total_points( $order );
				yit_save_prop( $order, '_ywpar_affiliate_commission_registered', false );
				YITH_WC_Points_Rewards()->add_point_to_customer( $affiliate['user_id'], -$tot_points, 'affiliates', __( 'Removed commission', 'yith-woocommerce-points-and-rewards' ), $order_id );
			}

		}

		/**
		 * Remove points to the order if there's a partial refund
		 * when the
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 * @param $order_id
		 * @param $refund_id
		 *
		 * @return void
		 */
		public function remove_order_points_refund( $order_id, $refund_id ) {

			// only for conversion points
			$calculation_type = YITH_WC_Points_Rewards()->get_option( 'affiliates_earning_conversion_points' );
			if ( $calculation_type != 'conversion' ) {
				return;
			}

			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				return;
			}

			$affiliate_token = yit_get_prop( $order, '_yith_wcaf_referral' );
			$affiliate       = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $affiliate_token );
			$point_earned    = (float) yit_get_prop( $order, '_ywpar_affiliate_commission_point' );
			if ( ! $order || $point_earned == '' ) {
				return;
			}

			$refund_obj    = new WC_Order_Refund( $refund_id );
			$refund_amount = $refund_obj->get_amount();

			$order_total                   = $order->get_total();
			$order_subtotal                = $order->get_subtotal();
			$order_remaining_refund_amount = $order->get_remaining_refund_amount();
			$total_points_refunded         = (float) yit_get_prop( $order, '_ywpar_affiliate_total_points_refunded', true );

			if ( $refund_amount > 0 ) {
				$points = 0;
				if ( $order_remaining_refund_amount > 0 ) {
					if ( $refund_amount > $order_subtotal ) {
						// shipping must be removed from
						$order_shipping_total = $order->get_shipping_total();
						$refund_amount        = $refund_amount - $order_shipping_total;
					}

					$conversion_rate = YITH_WC_Points_Rewards()->get_option( 'affiliates_earning_conversion' );

					if ( isset( $conversion_rate[ $order->get_currency() ] ) ) {
						$conversion_rate = $conversion_rate[ $order->get_currency() ];
						if ( $refund_amount == abs( $order_total ) ) {
							$points = $point_earned;
						} else {
							$points = round( $refund_amount / $conversion_rate['money'] * $conversion_rate['points'] );
						}
					}
				}

				// fix the points to refund calculation if points are more of the gap
				$gap                    = $point_earned - $total_points_refunded;
				$points                 = ( $points > $gap ) ? $gap : $points;
				$action                 = 'affiliates';
				$total_points_refunded += $points;

				YITH_WC_Points_Rewards()->add_point_to_customer( $affiliate['user_id'], - $points, $action, __( 'Order refunded', 'yith-woocommerce-points-and-rewards' ), $order_id );
				yit_save_prop( $order, '_ywpar_affiliate_total_points_refunded', $total_points_refunded );
			}
		}
	}


	/**
	 * Unique access to instance of YWPAR_Affiliates class
	 *
	 * @return \YWPAR_Affiliates
	 */
	function YWPAR_Affiliates() {
		return YWPAR_Affiliates::get_instance();
	}

	YWPAR_Affiliates();
}
