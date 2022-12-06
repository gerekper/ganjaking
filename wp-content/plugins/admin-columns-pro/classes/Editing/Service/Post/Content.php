<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Content extends BasicStorage {

	/**
	 * @var View
	 */
	private $view;

	public function __construct( View $view ) {
		parent::__construct( new Storage\Post\Field( 'post_content' ) );

		$this->view = $view;
	}

	public function get_view( string $context ): ?View {
		return $this->view;
	}

}