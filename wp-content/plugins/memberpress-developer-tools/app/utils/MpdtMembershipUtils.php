<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtMembershipUtils extends MpdtBaseCptUtils {
  public $model_class = 'MeprProduct';

  public function __construct() {
    $this->map  = array(
      'post_name'             => false,
      'post_parent'           => false,
      'post_type'             => false,
      'post_password'         => false,
      'post_content_filtered' => false,
      'post_mime_type'        => false,
      'guid'                  => false,
      'group_id'              => 'group',
      'who_can_purchase'      => false
    );

    parent::__construct();
  }

  protected function extend_obj(Array $memb) {
    $group_utils = MpdtUtilsFactory::fetch('group');

    if(isset($memb['group']) && is_numeric($memb['group']) && (int)$memb['group'] > 0) {
      $grp = new MeprGroup($memb['group']);
      $mgrp = $group_utils->map_vars((array)$grp->rec);
      $memb['group'] = $group_utils->trim_obj($mgrp);
    }

    return $memb;
  }

  /** Eliminate fields we don't want to show in the results */
  public function trim_obj(Array $memb) {
    // We don't want to display this ... it's unnecessary for now
    if(isset($memb['emails'])) {
      unset($memb['emails']);
    }

    return $memb;
  }

  // Used to implement custom search args
  protected function get_data_query_custom_clauses(Array $args) {
    global $wpdb;

    $clauses='';
    if(isset($args['group']) && is_numeric($args['group'])) {
      $clauses .= $wpdb->prepare(
        "
          AND (SELECT pm_group.meta_value
                 FROM {$wpdb->postmeta} AS pm_group
                WHERE pm_group.post_id=p.ID
                  AND pm_group.meta_key=%s
                LIMIT 1) = %d
        ",
        MeprProduct::$group_id_str,
        $args['group']
      );
    }

    return $clauses;
  }

}

