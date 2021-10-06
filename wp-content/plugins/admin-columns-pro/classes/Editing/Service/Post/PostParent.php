<?php

namespace ACP\Editing\Service\Post;

use AC;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper\Select;

class PostParent extends BasicStorage implements PaginatedOptions {

	/**
	 * @var string
	 */
	private $post_type;

	public function __construct( $post_type ) {
		parent::__construct( new Storage\Post\Field( 'post_parent' ) );

		$this->post_type = (string) $post_type;
	}

	public function get_view( $context ) {
		$view = new View\AjaxSelect();
		$view->set_clear_button( true );

		return $view;
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
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

	public function get_value( $id ) {
		$parent = get_post( parent::get_value( $id ) );

		if ( ! $parent ) {
			return false;
		}

		return [
			$parent->ID => $parent->post_title,
		];
	}

}