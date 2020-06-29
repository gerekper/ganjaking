<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtRulesApi extends MpdtBaseApi {
  public function prepare_vars(Array $rule) {
    // Because we don't have access to Javascript here in the API we never auto-gen the title ... for now
    $rule['auto_gen_title'] = false;
    return $rule;
  }
}

