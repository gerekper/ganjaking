<?php

namespace ACP\Search\Comparison\Comment;

use AC;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class Approved extends Field
	implements Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			__( 'Unapproved' ),
			_x( 'Approved', 'comment status' ),
		] );
	}

	protected function get_field() {
		return 'comment_approved';
	}

}