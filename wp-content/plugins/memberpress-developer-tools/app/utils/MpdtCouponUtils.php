<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtCouponUtils extends MpdtBaseCptUtils {
  public $model_class = 'MeprCoupon';

  public function __construct() {
    $this->map = array(
      'post_title'            => 'coupon_code',
      'post_content'          => 'description',
      'post_excerpt'          => false,
      'post_name'             => false,
      'post_parent'           => false,
      'post_type'             => false,
      'post_password'         => false,
      'post_content_filtered' => false,
      'post_mime_type'        => false,
      'guid'                  => false,
      'valid_products'        => 'valid_memberships'
    );

    parent::__construct();
  }

  protected function extend_obj(Array $coupon) {
    $membership_utils = MpdtUtilsFactory::fetch('membership');

    if(isset($coupon['valid_memberships']) && is_array($coupon['valid_memberships'])) {
      foreach($coupon['valid_memberships'] as $k => $v) {
        if(is_numeric($v) && (int)$v > 0) {
          $prd = new MeprProduct($v);
          $membership = $membership_utils->map_vars((array)$prd->rec);
          $coupon['valid_memberships'][$k] = $membership_utils->trim_obj($membership);
        }
      }
    }

    return $coupon;
  }

  // Used to implement custom search args
  protected function get_data_query_custom_clauses(Array $args) {
    global $wpdb;

    $clauses='';
    if(isset($args['code'])) {
      $clauses .= $wpdb->prepare(
        "
          AND p.post_title = %s
        ",
        $args['code']
      );
    }

    return $clauses;
  }

}

