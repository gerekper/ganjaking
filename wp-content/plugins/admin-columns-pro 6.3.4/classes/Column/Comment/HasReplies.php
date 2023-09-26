<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Search;

class HasReplies extends AC\Column
	implements Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-has_replies' )
		     ->set_label( __( 'Has Replies', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$comments = $this->get_raw_value( $id );

		return $comments
			? ac_helper()->icon->yes()
			: ac_helper()->icon->no();
	}

	/**
	 * @param int $id
	 *
	 * @return int
	 */
	public function get_raw_value( $id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "
			SELECT count(*)
			FROM $wpdb->comments
			WHERE comment_parent = %d
		",
			(int) $id
		);

		return (int) $wpdb->get_var( $sql );
	}

	public function search() {
		return new Search\Comparison\Comment\HasReplies();
	}

}