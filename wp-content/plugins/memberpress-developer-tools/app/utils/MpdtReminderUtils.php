<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtReminderUtils extends MpdtBaseCptUtils {
  public $model_class = 'MeprReminder';

  public function __construct() {
    $this->map  = array(
      'post_content'          => false,
      'post_excerpt'          => false,
      'post_name'             => false,
      'post_parent'           => false,
      'post_type'             => false,
      'post_password'         => false,
      'post_content_filtered' => false,
      'post_mime_type'        => false,
      'guid'                  => false
    );

    parent::__construct();
  }

  // In this case we're actually trimming stuff out of the object rather than 'extending' it
  public function trim_obj(Array $rmd) {
    if(isset($rmd['emails'])) {
      unset($rmd['emails']);
    }

    //$event = MeprUtils::camelcase($rmd['trigger_event'],'upper');
    //$emails  = array(
    //  "MeprUser{$event}ReminderEmail",
    //  "MeprAdmin{$event}ReminderEmail"
    //);

    //foreach($rmd['emails'] as $k => $v) {
    //  if(!in_array($k, $emails)) {
    //    unset($rmd['emails'][$k]);
    //  }
    //}

    return $rmd;
  }

  protected function extend_obj(Array $rmd) {
    $mepr_options = MeprOptions::fetch();

    $member_utils = MpdtUtilsFactory::fetch('member');

    if(isset($rmd['member']) && is_numeric($rmd['member']) && (int)$rmd['member'] > 0) {
      $member = new MeprUser($rmd['member']);
      if(!empty($member->ID)) {
        $member_data = $member_utils->map_vars((array)$member->rec);
        $member_data['address'] = (object)$member->full_address(false);
        $member_data['profile'] = (object)$member->custom_profile_values();
        $rmd['member'] = (array)$member_utils->trim_obj((array)$member_data);
      }
    }

    return $rmd;
  }

}

