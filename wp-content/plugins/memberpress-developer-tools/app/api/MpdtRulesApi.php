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

    if ( ! empty( $args['authorized_memberships'] ) || ! empty( $args['authorized_members'] ) || ! empty( $args['authorized_roles'] ) || ! empty( $args['authorized_capabilities'] ) ) {
      // Delete rules first then add them back below
      MeprRuleAccessCondition::delete_all_by_rule($rule_data->id);
    }

    // Authorized Memberships
    if(!empty($rule_data->id) && !empty($args['authorized_memberships']) && is_array($args['authorized_memberships'])) {

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

    // Authorized Members
    if(!empty($rule_data->id) && !empty($args['authorized_members']) && is_array($args['authorized_members'])) {

      $members = array_filter(array_map('sanitize_text_field', $args['authorized_members']));

      foreach($members as $member) {
        $rule_access_condition = new MeprRuleAccessCondition();
        $rule_access_condition->rule_id = $rule_data->id;
        $rule_access_condition->access_type = 'member';
        $rule_access_condition->access_operator = 'is';
        $rule_access_condition->access_condition  = $member;
        $rule_access_condition->store();
      }
    }

    // Authorized Roles
    if(!empty($rule_data->id) && !empty($args['authorized_roles']) && is_array($args['authorized_roles'])) {

      $roles = array_filter(array_map('sanitize_text_field', $args['authorized_roles']));

      foreach($roles as $role) {
        $rule_access_condition = new MeprRuleAccessCondition();
        $rule_access_condition->rule_id = $rule_data->id;
        $rule_access_condition->access_type = 'role';
        $rule_access_condition->access_operator = 'is';
        $rule_access_condition->access_condition  = $role;
        $rule_access_condition->store();
      }
    }

    // Authorized Capabilities
    if(!empty($rule_data->id) && !empty($args['authorized_capabilities']) && is_array($args['authorized_capabilities'])) {

      $capabilities = array_filter(array_map('sanitize_text_field', $args['authorized_capabilities']));

      foreach($capabilities as $capability) {
        $rule_access_condition = new MeprRuleAccessCondition();
        $rule_access_condition->rule_id = $rule_data->id;
        $rule_access_condition->access_type = 'capability';
        $rule_access_condition->access_operator = 'is';
        $rule_access_condition->access_condition  = $capability;
        $rule_access_condition->store();
      }
    }

    return $response;
  }
}

