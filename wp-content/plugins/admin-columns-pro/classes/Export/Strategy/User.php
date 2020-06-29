<?php

namespace ACP\Export\Strategy;

use AC;
use AC\ListTable;
use ACP\Export\Strategy;
use WP_User_Query;

/**
 * Exportability class for users list screen
 * @property AC\ListScreen\User $list_screen
 */
class User extends Strategy {

	/**
	 * @param AC\ListScreen\User $list_screen
	 */
	public function __construct( AC\ListScreen\User $list_screen ) {
		parent::__construct( $list_screen );
	}

	protected function get_list_table() {
		return new ListTable\User();
	}

	/**
	 * @since 1.0
	 * @see   ACP_Export_ExportableListScreen::ajax_export()
	 */
	protected function ajax_export() {
		add_filter( 'users_list_table_query_args', [ $this, 'catch_users_query' ], PHP_INT_MAX - 100 );
	}

	/**
	 * Modify the users query to use the correct pagination arguments, and epxort the resulting
	 * items. This should be attached to the users_list_table_query_args hook when an AJAX request
	 * is sent
	 *
	 * @param $args
	 *
	 * @see   filter:users_list_table_query_args
	 * @since 1.0
	 */
	public function catch_users_query( $args ) {
		$per_page = $this->get_num_items_per_iteration();

		$args['offset'] = $this->get_export_counter() * $per_page;
		$args['number'] = $per_page;
		$args['fields'] = 'ids';

		// Construct users query
		$query = new WP_User_Query( $args );

		// Export
		$this->export( $query->get_results() );
	}

}