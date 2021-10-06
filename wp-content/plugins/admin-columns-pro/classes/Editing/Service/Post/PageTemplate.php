<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View\Select;

class PageTemplate extends Service\BasicStorage {

	/**
	 * @var string
	 */
	private $post_type;

	public function __construct( $post_type ) {
		parent::__construct( new Storage\Post\MetaWithModifiedDate( '_wp_page_template' ) );

		$this->post_type = $post_type;
	}

	private function get_templates() {
		return (array) get_page_templates( null, $this->post_type );
	}

	private function get_options() {
		$options = array_merge(
			[
				'' => apply_filters( 'default_page_template_title', __( 'Default Template' ), 'acp-editing' ),
			],
			array_flip( $this->get_templates() )
		);

		natcasesort( $options );

		return $options;
	}

	public function get_view( $context ) {
		return new Select( $this->get_options() );
	}

}