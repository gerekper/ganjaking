<?php

namespace ACP\Export\Strategy;

use AC;
use AC\ListTable;
use ACP\Export\Strategy;
use WP_Query;

/**
 * Exportability class for posts list screen
 * @property AC\ListScreenPost $list_screen
 */
class Post extends Strategy {

	/**
	 * @param AC\ListScreenPost $list_screen
	 */
	public function __construct( AC\ListScreenPost $list_screen ) {
		parent::__construct( $list_screen );
	}

	protected function get_list_table() {
		return new ListTable\Post( $this->list_table_factory->create_post_table( $this->list_screen->get_screen_id() ) );
	}

	/**
	 * @since 1.0
	 * @see   ACP_Export_ExportableListScreen::ajax_export()
	 */
	protected function ajax_export() {
		add_action( 'pre_get_posts', [ $this, 'modify_posts_query' ], 16 );
		add_filter( 'the_posts', [ $this, 'catch_posts' ], 10, 2 );
	}

	/**
	 * Modify the main posts query to use the correct pagination arguments. This should be attached
	 * to the pre_get_posts hook when an AJAX request is sent
	 *
	 * @param WP_Query $query
	 *
	 * @since 1.0
	 * @see   action:pre_get_posts
	 */
	public function modify_posts_query( $query ) {
		if ( $query->is_main_query() ) {
			$per_page = $this->get_num_items_per_iteration();
			$query->set( 'nopaging', false );
			$query->set( 'offset', $this->get_export_counter() * $per_page );
			$query->set( 'posts_per_page', $per_page );
			$query->set( 'posts_per_archive_page', $per_page );
			$query->set( 'fields', 'all' );
		}
	}

	/**
	 * Run the actual export when the posts query is finalized. This should be attached to the
	 * the_posts filter when an AJAX request is run
	 *
	 * @param array    $posts
	 * @param WP_Query $query
	 *
	 * @return array
	 * @see   action:the_posts
	 * @since 1.0
	 */
	public function catch_posts( $posts, $query ) {
		if ( $query->is_main_query() ) {
			$this->export( wp_list_pluck( $posts, 'ID' ) );
		}

		return $posts;
	}

}