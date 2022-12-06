<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use AC;
use ACP;
use ACP\Search\Value;

class Post extends TableStorage implements ACP\Search\Comparison\SearchableValues {

	/**
	 * @var mixed
	 */
	private $post_type;

	/**
	 * @var array
	 */
	private $query_args;

	public function __construct( $operators, $table, $column, $post_type, $query_args = [] ) {
		$this->post_type = $post_type;
		$this->query_args = $query_args;

		parent::__construct( $operators, $table, $column, Value::INT );
	}

	public function get_values( $search, $page ) {
		$args = wp_parse_args( $this->query_args, [
			's'         => $search,
			'paged'     => $page,
			'post_type' => $this->post_type,
		] );

		$entities = new ACP\Helper\Select\Entities\Post( $args );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Group\PostType(
				new ACP\Helper\Select\Formatter\PostTitle( $entities )
			)
		);
	}

}