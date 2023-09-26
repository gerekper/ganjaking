<?php

namespace ACA\JetEngine\Editing\Service\Relation;

use ACA\JetEngine\Editing;
use ACP;

class Post extends Editing\Service\Relationship {

	/**
	 * @var string
	 */
	private $related_post_type;

	public function __construct( ACP\Editing\Storage $storage, $multiple, $related_post_type ) {
		$this->related_post_type = (string) $related_post_type;

		parent::__construct( $storage, $multiple );
	}

	public function get_value( $id ) {
		$value = [];
		$post_ids = parent::get_value( $id );

		foreach ( $post_ids as $post_id ) {
			$value[ $post_id ] = get_the_title( $post_id );
		}

		return $value;
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		$args = [
			'post_type' => $this->related_post_type,
		];

		return new ACP\Helper\Select\Paginated\Posts( $search, $page, $args );
	}

}