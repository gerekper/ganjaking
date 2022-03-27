<?php

namespace ACP\Search\Comparison\Post;

use AC;
use ACP\Helper\Select;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class PostParent extends PostField
	implements SearchableValues {

	/** @var string */
	private $post_type;

	public function __construct( $post_type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		$this->post_type = $post_type;

		parent::__construct( $operators, null, new Labels([
			Operators::IS_EMPTY => __('Has No Parent', 'codepress-admin-columns'),
			Operators::NOT_IS_EMPTY => __('Has Parent', 'codepress-admin-columns'),
		]) );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		if ( Operators::IS_EMPTY === $operator ) {
			$operator = Operators::EQ;
			$value = new Value( 0, $value->get_type() );
		}

		if ( Operators::IS_EMPTY === $operator ) {
			$operator = Operators::NEQ;
			$value = new Value( 0, $value->get_type() );
		}

		return parent::create_query_bindings( $operator, $value );
	}

	protected function get_field() {
		return 'post_parent';
	}

	public function get_values( $s, $paged ) {
		$entities = new Select\Entities\Post( [
			's'         => $s,
			'paged'     => $paged,
			'post_type' => $this->post_type,
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\PostTitle( $entities )
		);
	}

}