<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class PageTemplate extends BasicStorage {

	/**
	 * @var string
	 */
	private $post_type;

	public function __construct( string $post_type ) {
		parent::__construct( new Storage\Post\MetaWithModifiedDate( '_wp_page_template' ) );

		$this->post_type = $post_type;
	}

	private function get_options() {
		$templates = get_page_templates( null, $this->post_type );

		$options = array_merge(
			[
				'' => apply_filters( 'default_page_template_title', __( 'Default Template' ), 'acp-editing' ),
			],
			array_flip( $templates )
		);

		natcasesort( $options );

		return $options;
	}

	public function get_view( string $context ): ?View {
		return new View\Select( $this->get_options() );
	}

}