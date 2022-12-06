<?php

namespace ACA\GravityForms\Search;

use ACP\Search;
use GFFormsModel;

final class Query extends Search\Query {

	/**
	 * @var int
	 */
	private $form_id;

	/**
	 * @var string
	 */
	private $status;

	public function register() {
		add_filter( 'gform_get_entries_args_entry_list', [ $this, 'catch_list_details' ], 10, 3 );
		add_filter( 'gform_gf_query_sql', [ $this, 'parse_search_query' ] );
	}

	public function catch_list_details( array $args ) {
		$this->form_id = (int) $args['form_id'];
		$this->status = (string) $args['search_criteria']['status'];

		return $args;
	}

	public function parse_search_query( array $query ) {
		global $wpdb;

		$entry_table = GFFormsModel::get_entry_table_name();

		$where = sprintf(
			'WHERE %s.form_id = %s AND %s.status = %s',
			$entry_table,
			$wpdb->prepare( '%d', $this->form_id ),
			$entry_table,
			$wpdb->prepare( '%s', $this->status )
		);

		$query['select'] = sprintf( 'SELECT SQL_CALC_FOUND_ROWS DISTINCT %s.id', $entry_table );
		$query['from'] = sprintf( 'FROM %s', $entry_table );
		$query['where'] = $where;
		$query['order'] = sprintf( 'ORDER BY %s.id DESC', $entry_table );

		foreach ( $this->bindings as $binding ) {
			if ( $binding->get_where() ) {
				$query['where'] .= "\nAND " . $binding->get_where();
			}

			if ( $binding->get_join() ) {
				$query['join'] .= "\n" . $binding->get_join();
			}
		}

		return $query;
	}

}