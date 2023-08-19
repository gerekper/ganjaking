<?php

namespace ACP\Search\Comparison\Comment;

use AC;
use ACP\Helper\Select;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Post extends Field
	implements SearchableValues {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function get_field() {
		return 'comment_post_ID';
	}

	public function get_values( $s, $paged ) {
		$entities = new Select\Entities\Post( [
			's'         => $s,
			'paged'     => $paged,
			'post_type' => get_post_types_by_support( 'comments' ),
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Group\PostType(
				new Select\Formatter\PostTitle( $entities )
			)
		);
	}

}