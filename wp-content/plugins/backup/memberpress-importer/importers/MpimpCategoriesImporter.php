<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpCategoriesImporter extends MpimpTermsImporter {
  public function form() { }

  public function import($row,$args) {
    $row['taxonomy'] = 'category';
    return parent::import($row,$args);
  }
}

