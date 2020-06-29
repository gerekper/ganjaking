<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Plans_Admin class.
 */
class WC_Deposits_Plans_Manager {

	/**
	 * Get a payment plan by ID.
	 *
	 * @param  int $plan_id
	 * @return WC_Deposits_Plan
	 */
	public static function get_plan( $plan_id ) {
		global $wpdb;
		return new WC_Deposits_Plan( $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->wc_deposits_payment_plans} WHERE ID = %d", absint( $plan_id ) ) ) );
	}

	/**
	 * Get All plans.
	 *
	 * @return array of WC_Deposits_Plan
	 */
	public static function get_plans() {
		global $wpdb;

		$plans = array();

		foreach ( $wpdb->get_results( "SELECT * FROM {$wpdb->wc_deposits_payment_plans}" ) as $result ) {
			$plans[] = new WC_Deposits_Plan( $result );
		}

		return $plans;
	}

	/**
	 * Get all plan ids and names.
	 *
	 * @return array of ID name value pairs
	 */
	public static function get_plan_ids() {
		$plans    = self::get_plans();
		$plan_ids = array();

		foreach ( $plans as $plan ) {
			$plan_ids[ $plan->get_id() ] = $plan->get_name();
		}

		return $plan_ids;
	}

	/**
	 * Get the default plan IDs.
	 */
	public static function get_default_plan_ids() {
		return get_option( 'wc_deposits_default_plans', array() );
	}

	/**
	 * Get plan ids assigned to a product.
	 *
	 * @param  int $product_id
	 * @return array of ids
	 */
	public static function get_plan_ids_for_product( $product_id ) {
		$map = array_map( 'absint', array_filter( (array) WC_Deposits_Product_Meta::get_meta( $product_id, '_wc_deposit_payment_plans' ) ) );
		if ( count( $map ) <= 0 ) {
			$map = self::get_default_plan_ids();
		}
		return $map;
	}

	/**
	 * Get payment plans for a product.
	 *
	 * @param  int $product_id
	 * @return array of WC_Deposits_Plan
	 */
	public static function get_plans_for_product( $product_id ) {
		global $wpdb;

		$plans    = array();
		$plan_ids = array_merge( array( 0 ), self::get_plan_ids_for_product( $product_id ) );

		foreach ( $wpdb->get_results( "SELECT * FROM {$wpdb->wc_deposits_payment_plans} WHERE ID IN (" . implode( ',', $plan_ids ) . ")" ) as $result ) {
			$plans[] = new WC_Deposits_Plan( $result );
		}
		return $plans;
	}

	/**
	 * Figure out a payment plan is fully paid.
	 *
	 * This function looks at the children order items. If they are fully
	 * paid and the parent order has a status of partially paid, processing or completed.
	 *
	 * @since  1.1.6
	 * @param  WC_Order $parent_order
	 * @return bool
	 */
	public static function is_order_plan_fully_paid( $parent_order  ) {
		if ( ! $parent_order->has_status( array( 'processing', 'completed', 'partial-payment' ) ) ) {
			return false;
		}

		$parent_order_id = is_callable( array( $parent_order, 'get_id' ) ) ? $parent_order->get_id() : $parent_order->id;
		$related_orders  = WC_Deposits_Scheduled_Order_Manager::get_related_orders( $parent_order_id );
		$fully_paid      = true;

		foreach ( $related_orders as $order_post ) {
			$order = wc_get_order(  $order_post );
			if ( ! $order->has_status( array( 'processing', 'completed' ) ) ) {
				$fully_paid = false;
				break;
			}
		}

		return $fully_paid;
	}
}
