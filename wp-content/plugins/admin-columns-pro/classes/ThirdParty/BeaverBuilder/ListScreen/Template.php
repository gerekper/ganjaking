<?php

namespace ACP\ThirdParty\BeaverBuilder\ListScreen;

use ACP;

class Template extends ACP\ListScreen\Post {

	/*
	 * @var string
	 */
	private $template_page;

	/*
	 * @var string
	 */
	private $page_label;

	public function __construct( $page, $label ) {
		parent::__construct( 'fl-builder-template' );

		$this->template_page = (string) $page;
		$this->page_label = (string) $label;

		$this->set_key( 'fl-builder-template' . $page )
		     ->set_group( 'beaver_builder' )
		     ->set_screen_id( $this->get_screen_base() . '-fl-builder-template' );
	}

	public function is_current_screen( $wp_screen ) {
		return parent::is_current_screen( $wp_screen ) && filter_input( INPUT_GET, 'fl-builder-template-type' ) === $this->template_page;
	}

	public function get_label() {
		return $this->page_label;
	}

	public function get_screen_link() {
		return add_query_arg( [ 'fl-builder-template-type' => $this->template_page ], parent::get_screen_link() );
	}

}