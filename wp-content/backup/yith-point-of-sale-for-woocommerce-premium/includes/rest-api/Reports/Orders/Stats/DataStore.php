<?php


namespace YITH\POS\RestApi\Reports\Orders\Stats;

defined( 'ABSPATH' ) || exit;

use function YITH\POS\RestApi\get_sql_clauses_for_filters;

class DataStore extends \Automattic\WooCommerce\Admin\API\Reports\Orders\Stats\DataStore {

    /**
     * Updates the totals and intervals database queries with parameters used for Orders report: categories, coupons and order status.
     *
     * @param array $query_args Query arguments supplied by the user.
     */
    protected function orders_stats_sql_filter( $query_args ) {
        $stats_table_name = self::get_db_table_name();

        $clauses = get_sql_clauses_for_filters( $query_args, $stats_table_name, true );

        if ( $clauses->from && $clauses->where ) {
            $this->total_query->add_sql_clause( 'join', $clauses->from );
            $this->total_query->add_sql_clause( 'where', "AND ( $clauses->where )" );
            $this->interval_query->add_sql_clause( 'join', $clauses->from );
            $this->interval_query->add_sql_clause( 'where', "AND ( $clauses->where )" );
        }

        parent::orders_stats_sql_filter( $query_args );

    }
}
