<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Member Class
 *
 * @class   YITH_WCMBS_Membership
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 */
class YITH_WCMBS_Membership_Helper {

	/**
	 * Single instance of the class
	 *
	 * @var \YITH_WCMBS_Membership_Helper
	 */
	private static $_instance;

	/**
	 * the membership post type name
	 *
	 * @var string
	 * @since      1.0.0
	 * @deprecated 1.4.0 | use YITH_WCMBS_Post_Types::$membership instead.
	 */
	public $post_type_name;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YITH_WCMBS_Membership_Helper
	 * @since 1.0.0
	 */
	public static function get_instance() {
		return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
	}

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	private function __construct() {
		// Kept for backward compatibility.
		$this->post_type_name = YITH_WCMBS_Post_Types::$membership;
	}

	/**
	 * Get all memberships of a user
	 *
	 * @param int $user_id the id of the user
	 *
	 * @return YITH_WCMBS_Membership[]|bool
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.0.0
	 */
	public function get_memberships_by_user( $user_id ) {
		if ( ! $user_id ) {
			return false;
		}
		$memberships = get_posts( array(
									  'post_type'                  => YITH_WCMBS_Post_Types::$membership,
									  'posts_per_page'             => - 1,
									  'meta_key'                   => '_user_id',
									  'meta_value'                 => $user_id,
									  'suppress_filter'            => true,
									  'yith_wcmbs_suppress_filter' => true,
								  ) );

		return $this->parse_memberships_from_posts( $memberships );
	}

	/**
	 * Get all memberships by meta_query
	 *
	 * @param array  $meta_query the meta query
	 * @param string $return     the object type returned
	 *
	 * @return YITH_WCMBS_Membership[]|bool
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.0.0
	 */
	public function get_memberships_by_meta( $meta_query, $return = 'memberships' ) {
		$memberships = get_posts( array(
									  'post_type'                  => YITH_WCMBS_Post_Types::$membership,
									  'posts_per_page'             => - 1,
									  'meta_query'                 => $meta_query,
									  'suppress_filter'            => true,
									  'yith_wcmbs_suppress_filter' => true,
								  ) );

		switch ( $return ) {
			case 'posts':
				return $memberships;
				break;
			case 'memberships':
			default:
				return $this->parse_memberships_from_posts( $memberships );
		}
	}

	/**
	 * Get all memberships by meta_query
	 *
	 * @param array  $args   argument for get_posts
	 * @param string $return the object type returned
	 *
	 * @return YITH_WCMBS_Membership[]|bool
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.0.0
	 */
	public function get_memberships_by_args( $args, $return = 'memberships' ) {
		$default_args = array(
			'post_type'                  => YITH_WCMBS_Post_Types::$membership,
			'posts_per_page'             => - 1,
			'suppress_filter'            => true,
			'yith_wcmbs_suppress_filter' => true,
		);

		$args = wp_parse_args( $args, $default_args );

		$memberships = get_posts( $args );

		switch ( $return ) {
			case 'posts':
				return $memberships;
				break;
			case 'memberships':
			default:
				return $this->parse_memberships_from_posts( $memberships );
		}
	}

	/**
	 * return count of memberships with specific status
	 *
	 * @param string|array $status
	 * @param array        $args
	 *
	 * @return null|string
	 */
	public function get_count_membership_with_status( $status, $args = array() ) {
		global $wpdb;

		$only_subscription = isset( $args['only_subscription'] ) && $args['only_subscription'];

		$inner_join = '';
		if ( $only_subscription ) {
			$inner_join = " INNER JOIN " . $wpdb->prefix . "postmeta as yith_wcmbs_pm2 ON ( yith_wcmbs_p.ID = yith_wcmbs_pm2.post_id ) ";
		}

		$query = "SELECT count(*) as counter FROM $wpdb->posts as yith_wcmbs_p INNER JOIN " . $wpdb->prefix . "postmeta as yith_wcmbs_pm ON ( yith_wcmbs_p.ID = yith_wcmbs_pm.post_id ) $inner_join
                  WHERE yith_wcmbs_p.post_status = 'publish' AND yith_wcmbs_p.post_type = '%s' AND yith_wcmbs_pm.meta_key = '_status'";

		if ( is_string( $status ) ) {
			$query .= "  AND yith_wcmbs_pm.meta_value = '$status'";
		} elseif ( is_array( $status ) ) {
			$statuses_string = '';
			$first           = true;
			foreach ( $status as $s ) {
				if ( $first ) {
					$statuses_string .= "'$s'";
					$first           = false;
				} else {
					$statuses_string .= ",'$s'";
				}
			}

			$query .= "  AND yith_wcmbs_pm.meta_value IN ($statuses_string)";
		}

		if ( $only_subscription ) {
			$query .= " AND yith_wcmbs_pm2.meta_key = '_subscription_id' AND yith_wcmbs_pm2.meta_value != '0'";
		}

		$count = $wpdb->get_var( $wpdb->prepare( $query, YITH_WCMBS_Post_Types::$membership ) );

		return $count;
	}

	/**
	 * return count of active memberships
	 *
	 * @param array $args
	 *
	 * @return null|string
	 */
	public function get_count_active_membership( $args = array() ) {
		$count = $this->get_count_membership_with_status( array( 'active', 'resumed', 'expiring' ), $args );

		return $count;
	}

	public function get_count_actived_membership( $range = 'today' ) {
		global $wpdb;

		$start = 0;
		$end   = 0;
		switch ( $range ) {
			case 'today':
				$start = strtotime( 'now midnight' );
				$end   = strtotime( 'tomorrow midnight' );
				break;
			case 'yesterday':
				$start = strtotime( 'yesterday midnight' );
				$end   = strtotime( 'now midnight' );
				break;
			case 'month':
				$start = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$end   = strtotime( 'tomorrow midnight' );
				break;
			case 'last_month' :
				$first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$start                   = strtotime( date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ) );
				$end                     = strtotime( date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ) );
				break;
			case 'year' :
				$start = strtotime( date( 'Y-01-01', current_time( 'timestamp' ) ) );
				$end   = strtotime( 'tomorrow midnight' );
				break;
			case '7day' :
				$start = strtotime( '-6 days midnight', current_time( 'timestamp' ) );
				$end   = strtotime( 'tomorrow midnight' );
				break;
			case 'ever':
				$start = 0;
				$end   = strtotime( 'tomorrow midnight' );
				break;
		}

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as counter FROM $wpdb->posts as yith_wcmbs_p INNER JOIN " . $wpdb->prefix . "postmeta as yith_wcmbs_pm ON ( yith_wcmbs_p.ID = yith_wcmbs_pm.post_id )
                  WHERE yith_wcmbs_p.post_status = 'publish' AND yith_wcmbs_p.post_type = '%s' AND yith_wcmbs_pm.meta_key = '_start_date' AND yith_wcmbs_pm.meta_value >= '%s' AND yith_wcmbs_pm.meta_value < '%s'", YITH_WCMBS_Post_Types::$membership, $start, $end ) );

		return $count;
	}

	/**
	 * get membership by subscription id
	 *
	 * @param int $subscription_id the id of the subscription
	 *
	 * @access public
	 * @return bool|YITH_WCMBS_Membership
	 * @since  1.0.0
	 */
	public function get_memberships_by_subscription( $subscription_id ) {
		$meta_query = array(
			array(
				'key'   => '_subscription_id',
				'value' => $subscription_id,
			),
		);

		return $this->get_memberships_by_meta( $meta_query );
	}

	/**
	 * get memberships by order
	 *
	 * @param int $order_id      the id of the order
	 * @param int $order_item_id the id of the order item
	 *
	 * @access public
	 * @return bool|YITH_WCMBS_Membership
	 * @since  1.0.0
	 */
	public function get_memberships_by_order( $order_id, $order_item_id = '' ) {
		$meta_query = array(
			'relation' => 'AND',
			array(
				'key'   => '_order_id',
				'value' => $order_id,
			),
		);

		if ( ! empty( $order_item_id ) ) {
			$meta_query[] = array(
				'key'   => '_order_item_id',
				'value' => $order_item_id,
			);
		}

		return $this->get_memberships_by_meta( $meta_query );
	}

	/**
	 * Get all memberships
	 *
	 * @return YITH_WCMBS_Membership[]|bool
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.0.0
	 */
	public function get_all_memberships() {
		$memberships = get_posts( array(
									  'post_type'                  => YITH_WCMBS_Post_Types::$membership,
									  'posts_per_page'             => - 1,
									  'suppress_filter'            => true,
									  'yith_wcmbs_suppress_filter' => true,
								  ) );

		return $this->parse_memberships_from_posts( $memberships );
	}

	/**
	 * Get all memberships by plan_id
	 *
	 * @param int $plan_id the id of the plan
	 *
	 * @return YITH_WCMBS_Membership[]|bool
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.0.0
	 */
	public function get_all_memberships_by_plan_id( $plan_id ) {
		$memberships = get_posts( array(
									  'post_type'                  => YITH_WCMBS_Post_Types::$membership,
									  'posts_per_page'             => - 1,
									  'meta_key'                   => '_plan_id',
									  'meta_value'                 => $plan_id,
									  'suppress_filter'            => true,
									  'yith_wcmbs_suppress_filter' => true,
								  ) );

		return $this->parse_memberships_from_posts( $memberships );
	}

	/**
	 * parse posts and return array of YITH_WC
	 *
	 * @param WP_Post|WP_Post[] $membership_posts the posts
	 *
	 * @return YITH_WCMBS_Membership[]|bool
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.0.0
	 */
	public function parse_memberships_from_posts( $membership_posts ) {
		if ( ! empty( $membership_posts ) ) {
			$membership_posts = (array) $membership_posts;
			$memberships      = array();
			foreach ( $membership_posts as $post ) {
				$membership    = yith_wcmbs_get_membership( $post->ID );
				$memberships[] = $membership;
			}

			return $memberships;
		}

		return false;
	}

	/**
	 * get the linked plans ids by $plan_id
	 * return false if the plan don't have linked plans
	 *
	 * @access     public
	 * @return array
	 * @deprecated 1.4.0 | use YITH_WCMBS_Plan::get_linked_plans
	 * @since      1.0.0
	 */
	public function get_linked_plans( $plan_id ) {
		$plan = yith_wcmbs_get_plan( $plan_id );

		return $plan ? $plan->get_linked_plans() : array();
	}

	/**
	 * Get all members for a plan
	 *
	 * @param int $plan_id the id of the plan
	 *
	 * @return array
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.0.0
	 */
	public function get_members( $plan_id, $args = array() ) {

		$default_args = array(
			'return' => 'ids',
		);

		$args = wp_parse_args( $args, $default_args );

		$memberships = $this->get_all_memberships_by_plan_id( $plan_id );
		$r           = array();

		if ( ! empty( $memberships ) ) {
			foreach ( $memberships as $membership ) {
				if ( $membership instanceof YITH_WCMBS_Membership ) {
					if ( $membership->is_active() ) {
						if ( $args['return'] == 'ids' ) {
							$r[] = $membership->user_id;
						}
					}
				}
			}
		}

		return array_unique( $r );

	}


	/**
	 * Retrieve memberships
	 *
	 * @param array $args
	 *
	 * @return array|int[]|YITH_WCMBS_Membership[]
	 * @since 1.4.0
	 */
	public function get_memberships( $args = array() ) {
		$default_args = array(
			'number'      => - 1,
			'active_only' => false,
			'status'      => false,
			'user'        => false,
			'return'      => 'ids',
		);

		$args       = wp_parse_args( $args, $default_args );
		$meta_query = array( 'relation' => 'AND' );

		if ( $args['active_only'] ) {
			$args['status'] = array( 'active', 'resumed', 'expiring' );
		}

		if ( $args['status'] ) {
			$meta_query[] = array(
				'key'     => '_status',
				'value'   => (array) $args['status'],
				'compare' => 'IN',
			);
		}

		if ( $args['user'] ) {
			$meta_query[] = array(
				'key'   => '_user_id',
				'value' => absint( $args['user'] ),
			);
		}

		$post_args = array(
			'post_type'                  => YITH_WCMBS_Post_Types::$membership,
			'posts_per_page'             => $args['number'],
			'meta_query'                 => $meta_query,
			'suppress_filter'            => true,
			'yith_wcmbs_suppress_filter' => true,
			'fields'                     => 'ids',
		);

		$memberships = get_posts( $post_args );

		if ( 'memberships' === $args['return'] ) {
			$memberships = array_filter( array_map( 'yith_wcmbs_get_membership', $memberships ) );
		}

		return $memberships;
	}

}

/**
 * Unique access to instance of YITH_WCMBS_Membership_Helper class
 *
 * @return \YITH_WCMBS_Membership_Helper
 */
function YITH_WCMBS_Membership_Helper() {
	return YITH_WCMBS_Membership_Helper::get_instance();
}
