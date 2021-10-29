<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

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

