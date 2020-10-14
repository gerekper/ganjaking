<?php
namespace memberpress\downloads\lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

abstract class BaseCtaxCtrl extends BaseCtrl {
  public $ctax, $cpts;

  public function __construct() {
    add_action('init', array( $this, 'register_taxonomy' ), 2);
    //add_filter('post_updated_messages', array($this,'post_updated_messages'));
    //add_filter('bulk_post_updated_messages', array($this,'bulk_post_updated_messages'), 10, 2);
    parent::__construct();
  }

  abstract public function register_taxonomy();
}
