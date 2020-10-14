<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpAccessConditionsImporter extends MpimpBaseImporter {
  public function form() { }

  public function import($row,$args) {
    $required = array('rule_id','access_type','access_condition');
    $this->check_required('access_conditions', array_keys($row), $required);

    // Merge in default values where applicable
    $row = array_merge(array('access_operator' => 'is'), $row);

    $rule_access_condition = new MeprRuleAccessCondition();
    $rule_access_condition->access_operator = $row['access_operator'];

    // Check that the rule is valid
    $rule_id = $row['rule_id'];
    $this->fail_if_empty('rule_id', $rule_id);
    $this->fail_if_not_number('rule_id', $rule_id);
    $this->fail_if_not_valid_rule_id($rule_id);
    $rule_access_condition->rule_id = $rule_id;

    // Check that the access type is valid
    $access_type = $row['access_type'];
    $this->fail_if_empty('access_type', $access_type);
    $access_condition = $row['access_condition'];
    $this->fail_if_empty('access_condition', $access_condition);
    $valid_access_types = array_column(MeprRule::mepr_access_types(), 'value');
    $this->fail_if_not_in_enum('access_type', $access_type, $valid_access_types);
    $rule_access_condition->access_type = $access_type;
    switch($row['access_type']) {
      case 'membership':
        $this->fail_if_not_number('access_condition', $access_condition);
        $this->fail_if_not_valid_product_id($access_condition);
        break;
      case 'member':
        // Allow access condition import by user login, email, or id
        if(preg_match('/.+@.+\..{2,}/', $access_condition))
          $user = get_user_by('email', $access_condition);
        elseif(preg_match('/^\d+$/', $access_condition))
          $user = get_user_by('id', $access_condition);
        $access_condition = isset($user) ? $user->user_login : $access_condition;
        $this->fail_if_not_valid_username($access_condition);
        break;
    }
    $rule_access_condition->access_condition = $access_condition;

    if($rule_access_condition_id = $rule_access_condition->store())
      return sprintf(__('Access Condition (ID = %d) was created successfully.'), $rule_access_condition_id);
    else
      throw new Exception(__('Rule failed to be created'));
  }
}
