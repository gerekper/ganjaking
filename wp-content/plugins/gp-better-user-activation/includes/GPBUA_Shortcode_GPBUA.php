<?php

class GPBUA_Shortcode_GPBUA {

  public function __construct() {

    add_shortcode( 'gpbua', array( $this, 'shortcodeHandler') );

  }

  public function shortcodeHandler( $atts ) {
    return $this->get_current_view_content();
  }

  public function get_current_view_content() {

    // get the activate instance
    $activate = GPBUA_Activate::get_instance();

    // init template class
    $template = new GPBUA_Template( $activate->get_view(), $activate->get_result() );

    return $template->render_view();
  }


}
