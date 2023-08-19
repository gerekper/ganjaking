<?php

namespace ACA\EC\Search\Event;

use AC;
use ACP;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Relation extends Meta
	implements SearchableValues {

	/** @var Relation */
	private $relation;

	public function __construct( $meta_key, $meta_type, AC\Relation\Post $relation ) {
		$this->relation = $relation;
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators, $meta_key, $meta_type );
	}

	public function get_values( $search, $page ) {

		$entities = new ACP\Helper\Select\Entities\Post( [
			's'         => $search,
			'paged'     => $page,
			'post_type' => $this->relation->get_post_type_object()->name,
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Group\PostType(
				new ACP\Helper\Select\Formatter\PostTitle( $entities )
			)
		);
	}

}