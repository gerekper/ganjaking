<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtRuleUtils extends MpdtBaseCptUtils {
  public $model_class = 'MeprRule';

  public function __construct() {
    $this->map  = array(
      'post_title'             => 'title',
      'post_content'           => false,
      'post_excerpt'           => false,
      'post_name'              => false,
      'post_parent'            => false,
      'post_type'              => false,
      'post_password'          => false,
      'post_content_filtered'  => false,
      'post_mime_type'         => false,
      'mepr_access'            => 'authorized_memberships',
      'mepr_type'              => 'rule_type',
      'mepr_content'           => 'rule_content',
      'is_mepr_content_regexp' => 'is_rule_content_regex',
      'guid'                   => false
    );

    parent::__construct();
  }

  protected function extend_obj(Array $rule) {
    $membership_utils = MpdtUtilsFactory::fetch('membership');

    if(isset($rule['authorized_memberships']) && is_array($rule['authorized_memberships'])) {
      foreach($rule['authorized_memberships'] as $k => $v) {
        if(is_numeric($v) && (int)$v > 0) {
          $prd = new MeprProduct($v);
          $mprd = $membership_utils->map_vars((array)$prd->rec);
          $rule['authorized_memberships'][$k] = $membership_utils->trim_obj($mprd);
        }
      }
    }

    return $rule;
  }
}

