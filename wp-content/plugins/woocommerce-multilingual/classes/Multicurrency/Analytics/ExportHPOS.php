<?php

namespace WCML\Multicurrency\Analytics;

use WCML\COT\Helper as COTHelper;

class ExportHPOS extends Export {

	/**
	 * @param string[] $clauses
	 *
	 * @return string[]
	 */
	public function addJoinClauses( $clauses ) {
		$ordersMetaTable = COTHelper::getMetaTableName();
		$clauses[]       = PHP_EOL . "LEFT JOIN {$ordersMetaTable} AS wcmllang ON wcmllang.order_id = {$this->wpdb->prefix}wc_order_stats.order_id AND wcmllang.meta_key = '" . \WCML_Orders::KEY_LANGUAGE . "'";

		if ( wcml_is_multi_currency_on() ) {
			$ordersTable = COTHelper::getTableName();
			$clauses[]   = PHP_EOL . "LEFT JOIN {$ordersTable} AS wcmlcurr ON wcmlcurr.id = {$this->wpdb->prefix}wc_order_stats.order_id";
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
			$clauses[] = sprintf( ', wcmlcurr.currency AS %s', self::COL_CURRENCY );
		}

		return $clauses;
	}
}
