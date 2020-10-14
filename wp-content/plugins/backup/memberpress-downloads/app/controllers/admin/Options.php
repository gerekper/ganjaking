<?php
namespace memberpress\downloads\controllers\admin;

if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

use memberpress\downloads as base,
    memberpress\downloads\lib as lib;

class Options extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('mpdl_admin_general_options', array($this, 'general'));
  }

  public function route() {
    require_once(base\VIEWS_PATH . '/admin/options/form.php');
  }

  public function general() {
    require_once(base\VIEWS_PATH . '/admin/options/general.php');
  }
}
