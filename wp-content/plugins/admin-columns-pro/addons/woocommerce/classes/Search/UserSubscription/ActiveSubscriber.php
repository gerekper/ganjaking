<?php

namespace ACA\WC\Search\UserSubscription;

use ACP;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class ActiveSubscriber extends ACP\Search\Comparison {

	public function __construct() {
		$operators = new Operators( [
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );
		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;
		$ids = implode( ',', $this->get_active_subscribers_ids() );

		if ( Operators::NOT_IS_EMPTY === $operator ) {
			$where = $wpdb->users . '.ID IN( ' . $ids . ' )';
		} else {
			$where = $wpdb->users . '.ID NOT IN( ' . $ids . ' )';
		}

		$bindings = new Bindings();
		$bindings->where( $where );

		return $bindings;
	}

	private function get_active_subscribers_ids() {
		global $wpdb;

		return $wpdb->get_col( "
        SELECT DISTINCT pm.meta_value
        FROM {$wpdb->prefix}posts as p
        JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
        WHERE p.post_type = 'shop_subscription'
        AND p.post_status = 'wc-active'
        AND pm.meta_key = '_customer_user'
    " );
	}

}