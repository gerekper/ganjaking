<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpTagsImporter extends MpimpTermsImporter {
  public function form() { }

  public function import($row,$args) {
    $row['taxonomy'] = 'post_tag';
    return parent::import($row,$args);
  }
}

