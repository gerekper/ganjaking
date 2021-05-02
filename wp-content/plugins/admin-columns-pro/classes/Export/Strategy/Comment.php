<?php

namespace ACP\Export\Strategy;

use AC\ListScreen;
use ACP\Export\Strategy;
use WP_Comment_Query;

/**
 * Exportability class for comments list screen
 * @property ListScreen\Comment $list_screen
 */
class Comment extends Strategy {

	/**
	 * @param ListScreen\Comment $list_screen
	 */
	public function __construct( ListScreen\Comment $list_screen ) {
		parent::__construct( $list_screen );
	}

	/**
	 * @since 1.0
	 * @see   ACP_Export_ExportableListScreen::ajax_export()
	 */
	protected function ajax_export() {
		add_action( 'parse_comment_query', [ $this, 'comments_query' ], PHP_INT_MAX - 100 );
	}

	/**
	 * Catch the comments query and run it with altered parameters for pagination. This should be
	 * attached to the parse_comment_query hook when an AJAX request is sent
	 *
	 * @param $query
	 *
	 * @see   action:pre_get_posts
	 * @since 1.0
	 */
	public function comments_query( $query ) {
		if ( ! $query->query_vars['count'] ) {
			$per_page = $this->get_num_items_per_iteration();

			$query->query_vars['offset'] = $this->get_export_counter() * $per_page;
			$query->query_vars['number'] = $per_page;
			$query->query_vars['fields'] = 'ids';

			remove_action( 'parse_comment_query', [ $this, __FUNCTION__ ], PHP_INT_MAX - 100 );

			$modified_query = new WP_Comment_Query( $query->query_vars );
			$comments = $modified_query->get_comments();
			$this->export( $comments );
		}
	}

}