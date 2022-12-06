<?php

namespace ACA\JetEngine\Editing\Service\Relation;

use ACA\JetEngine\Editing;
use ACP;

class Term extends Editing\Service\Relationship {

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( ACP\Editing\Storage $storage, $multiple, $taxonomy ) {
		$this->taxonomy = (string) $taxonomy;

		parent::__construct( $storage, $multiple );
	}

	public function get_value( $id ) {
		$value = [];
		$term_ids = parent::get_value( $id );

		foreach ( $term_ids as $term_id ) {
			$value[ $term_id ] = ac_helper()->taxonomy->get_term_display_name( get_term( $term_id ) );
		}

		return $value;
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		return new ACP\Helper\Select\Paginated\Terms( $search, $page, [ $this->taxonomy ] );
	}

}