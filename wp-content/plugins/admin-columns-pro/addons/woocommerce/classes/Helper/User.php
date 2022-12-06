<?php declare( strict_types=1 );

namespace ACA\WC\Helper;

final class User {

	/**
	 * @param int               $user_id
	 * @param string|array|null $status
	 *
	 * @return array
	 */
	public function get_totals_for_user( $user_id, $status = null ) {
		$totals = [];

		foreach ( $this->get_orders_by_user( (int) $user_id, $status ) as $order ) {
			if ( ! $order->get_total() ) {
				continue;
			}

			$currency = $order->get_currency();

			if ( ! isset( $totals[ $currency ] ) ) {
				$totals[ $currency ] = 0;
			}

			$totals[ $currency ] += $order->get_total();
		}

		return $totals;
	}

	/**
	 * @param int          $user_id
	 * @param string|array $status
	 *
	 * @return int[]
	 */
	public function get_order_ids_by_user( $user_id, $status ) {
		$args = [
			'fields'         => 'ids',
			'post_type'      => 'shop_order',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'meta_query'     => [
				[
					'key'   => '_customer_user',
					'value' => (int) $user_id,
				],
			],
		];

		if ( $status ) {
			$args['post_status'] = $status;
		}

		$order_ids = get_posts( $args );

		if ( ! $order_ids ) {
			return [];
		}

		return $order_ids;
	}

	/**
	 * @param int          $user_id
	 * @param string|array $status
	 *
	 * @return \WC_Order[]|array
	 */
	public function get_orders_by_user( $user_id, $status = [ 'wc-completed', 'wc-processing' ] ) {
		$orders = [];

		foreach ( $this->get_order_ids_by_user( (int) $user_id, $status ) as $order_id ) {
			$orders[] = wc_get_order( $order_id );
		}

		return $orders;
	}


}