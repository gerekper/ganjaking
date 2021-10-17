<?php

namespace ACP\Search\Comparison\Post;

use AC;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class CommentStatus extends PostField
	implements Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'open'   => __( 'Open', 'codepress-admin-columns' ),
			'closed' => __( 'Closed', 'codepress-admin-columns' ),
		] );
	}

	protected function get_field() {
		return 'comment_status';
	}

}