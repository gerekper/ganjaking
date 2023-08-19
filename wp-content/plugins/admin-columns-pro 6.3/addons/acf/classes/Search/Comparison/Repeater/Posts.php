<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use AC;
use ACA\ACF\Search\Comparison;
use ACP\Helper\Select;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Posts extends Comparison\Repeater
	implements SearchableValues {

	/**
	 * @var array
	 */
	private $post_type;

	public function __construct( $meta_type, $parent_key, $sub_key, $post_type, $multiple = false ) {
		if ( null === $post_type ) {
			$post_type = [ 'any' ];
		}

		$this->post_type = (array) $post_type;

		$operators = new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $meta_type, $parent_key, $sub_key, $operators, null, $multiple );
	}

	public function get_values( $search, $page ) {
		$entities = new Select\Entities\Post( [
			's'             => $search,
			'paged'         => $page,
			'post_type'     => $this->post_type,
			'search_fields' => [ 'post_title', 'ID' ],
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Group\PostType(
				new Select\Formatter\PostTitle( $entities )
			)
		);
	}

}