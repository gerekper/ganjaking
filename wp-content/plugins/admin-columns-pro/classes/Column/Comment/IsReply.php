<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Search;

class IsReply extends AC\Column
	implements Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-is_reply' );
		$this->set_label( __( 'Is Reply', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		return $this->get_raw_value( $id ) ? ac_helper()->icon->yes() : ac_helper()->icon->no();
	}

	/**
	 * @param int $id
	 *
	 * @return int
	 */
	public function get_raw_value( $id ) {
		return get_comment( $id )->comment_parent;
	}

	public function search() {
		return new Search\Comparison\Comment\IsReply();
	}

}