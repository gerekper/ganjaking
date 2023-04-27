<?php

namespace ACA\BeaverBuilder\ListScreen;

use ACP;

class Template extends ACP\ListScreen\Post {

	public const POST_TYPE = 'fl-builder-template';

	private $template_page;

	private $custom_label;

	public function __construct( string $template_page, string $label ) {
		parent::__construct( 'fl-builder-template' );

		$this->template_page = $template_page;
		$this->custom_label = $label;

		$this->set_key( self::POST_TYPE . $template_page )
		     ->set_group( 'beaver_builder' )
		     ->set_label( $label )
		     ->set_screen_id( $this->get_screen_base() . '-fl-builder-template' );
	}

	public function get_label() {
		return $this->custom_label;
	}

	public function get_screen_link() {
		return add_query_arg( [ 'fl-builder-template-type' => $this->template_page ], parent::get_screen_link() );
	}

}