<?php

namespace ACP\Search\Comparison\Post;

use AC;
use ACP\Helper\Select;
use ACP\Search\Comparison\RemoteValues;
use ACP\Search\Operators;

class Status extends PostField
	implements RemoteValues {

	/** @var string */
	private $post_type;

	public function __construct( $post_type ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
		] );

		$this->post_type = $post_type;

		parent::__construct( $operators );
	}

	/**
	 * @return string
	 */
	protected function get_field() {
		return 'post_status';
	}

	/**
	 * @return AC\Helper\Select\Options
	 */
	public function get_values() {
		$entities = new Select\Entities\PostStatus( [
			'post_type' => $this->post_type,
		] );

		$results = [];
		foreach ( $entities as $value => $entity ) {
			$results[] = new AC\Helper\Select\Option( $value, $entity->label );
		}

		return new AC\Helper\Select\Options( $results );
	}

}