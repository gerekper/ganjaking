<?php

namespace WCML\Multicurrency\Analytics;

use WCML\Orders\Helper as OrdersHelper;

class ExportLegacy extends Export {

	/**
	 * @param string[] $clauses
	 *
	 * @return string[]
	 */
	public function addJoinClauses( $clauses ) {
		$clauses[] = PHP_EOL . "LEFT JOIN {$this->wpdb->postmeta} AS wcmllang ON wcmllang.post_id = {$this->wpdb->prefix}wc_order_stats.order_id AND wcmllang.meta_key = '" . \WCML_Orders::KEY_LANGUAGE . "'";

		if ( wcml_is_multi_currency_on() ) {
			$clauses[] = PHP_EOL . "LEFT JOIN {$this->wpdb->postmeta} AS wcmlcurr ON wcmlcurr.post_id = {$this->wpdb->prefix}wc_order_stats.order_id AND wcmlcurr.meta_key = '" . OrdersHelper::KEY_LEGACY_CURRENCY . "'";
		}

		return $clauses;
	}

	/**
	 * @param string[] $clauses
	 *
	 * @return string[]
	 */
	public function addSelectClauses( $clauses ) {
		$clauses[] = sprintf( ', wcmllang.meta_value AS %s', self::COL_LANGUAGE );

		if ( wcml_is_multi_currency_on() ) {
			$clauses[] = sprintf( ', wcmlcurr.meta_value AS %s', self::COL_CURRENCY );
		}

		return $clauses;
	}
}
