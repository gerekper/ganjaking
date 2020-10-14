<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtRulesApi extends MpdtBaseApi {
  public function prepare_vars(Array $rule) {
    // Because we don't have access to Javascript here in the API we never auto-gen the title ... for now
    $rule['auto_gen_title'] = false;
    return $rule;
  }

  protected function after_store($request, $response) {
    $rule_data = (object) $response->get_data();
    $args = $request->get_params();

    if(!empty($rule_data->id) && isset($args['authorized_memberships']) && is_array($args['authorized_memberships'])) {
      // Delete rules first then add them back below
      MeprRuleAccessCondition::delete_all_by_rule($rule_data->id);

      $memberships = array_filter(array_map('intval', $args['authorized_memberships']));

      foreach($memberships as $membership) {
        $rule_access_condition = new MeprRuleAccessCondition();
        $rule_access_condition->rule_id = $rule_data->id;
        $rule_access_condition->access_type = 'membership';
        $rule_access_condition->access_operator = 'is';
        $rule_access_condition->access_condition  = $membership;
        $rule_access_condition->store();
      }
    }

    return $response;
  }
}

