<?php

class GPBUA_Template {

	private $view;
	private $activation;

	public function __construct( $view, $activation ) {
		$this->view = $view;
		$this->activation = $activation;
	}

  public function get_view() {
    return $this->view;
  }

  public function render_view() {

    $meta_key = '_gpbua_activation_' . $this->view;
    $content = get_post_meta( gpbua_get_activation_page_id(), $meta_key, true );

    if( ! $content ) {
	    $content = gpbua()->get_default_content( $this->view );
    }

    return $content;

  }

  public function get_template_path() {
    return gpbua()->get_base_path() . '/templates/';
  }

}
