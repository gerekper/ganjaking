<?php

namespace ACP\Export\Strategy;

use AC\ListTable;
use ACP;
use ACP\Export\Strategy;
use WP_Term_Query;

/**
 * Exportability class for terms list screen
 * @property ACP\ListScreen\Taxonomy $list_screen
 */
class Taxonomy extends Strategy {

	/**
	 * @param ACP\ListScreen\Taxonomy $list_screen
	 */
	public function __construct( ACP\ListScreen\Taxonomy $list_screen ) {
		parent::__construct( $list_screen );
	}

	protected function get_list_table() {
		return new ListTable\Taxonomy( $this->list_screen->get_taxonomy() );
	}

	/**
	 * @see ACP_Export_ExportableListScreen::ajax_export()
	 */
	protected function ajax_export() {
		add_action( 'parse_term_query', [ $this, 'terms_query' ], PHP_INT_MAX - 100 );
	}

	/**
	 * Catch the terms query and run it with altered parameters for pagination. This should be
	 * attached to the parse_term_query hook when an AJAX request is sent
	 *
	 * @param $query
	 *
	 * @see   action:parse_term_query
	 * @since 1.0
	 */
	public function terms_query( $query ) {
		if ( $query->query_vars['fields'] === 'count' ) {
			$per_page = $this->get_num_items_per_iteration();

			$query->query_vars['offset'] = $this->get_export_counter() * $per_page;
			$query->query_vars['number'] = $per_page;
			$query->query_vars['fields'] = 'ids';

			remove_action( 'parse_term_query', [ $this, __FUNCTION__ ], PHP_INT_MAX - 100 );

			$modified_query = new WP_Term_Query( $query->query_vars );
			$terms = $modified_query->get_terms();
			$this->export( $terms );
		}
	}

}