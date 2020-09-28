<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

global $wpdb;
$mepr_subscriptions = $wpdb->prefix.'mepr_subscriptions';

if((defined('MEPR_VERSION') && version_compare('1.2.9', MEPR_VERSION, '>=')) ||
   (!defined('MEPR_VERSION') && !MpdtBaseUtils::table_exists($mepr_subscriptions))) {

  class MpdtSubscriptionUtils extends MpdtBaseCptUtils {
    public $model_class = 'MeprSubscription';

    public function __construct() {
      $this->map  = array(
        'post_title'            => false,
        'post_content'          => false,
        'post_excerpt'          => false,
        'post_name'             => false,
        'post_status'           => false,
        'post_parent'           => false,
        'post_type'             => false,
        'post_author'           => false,
        'post_password'         => false,
        'post_content_filtered' => false,
        'post_mime_type'        => false,
        'guid'                  => false,
        'ID'                    => 'id',
        'coupon_id'             => 'coupon',
        'product_id'            => 'membership',
        'user_id'               => 'member'
      );

      parent::__construct();
    }

    protected function extend_obj(Array $sub) {
      $coupon_utils = MpdtUtilsFactory::fetch('coupon');
      $member_utils = MpdtUtilsFactory::fetch('member');
      $membership_utils = MpdtUtilsFactory::fetch('membership');

      if(isset($sub['member']) && is_numeric($sub['member']) && (int)$sub['member'] > 0) {
        $member = new MeprUser($sub['member']);
        if(!empty($member->ID)) {
          $member_data = $member_utils->map_vars((array)$member->rec);
          $member_data['address'] = (object)$member->full_address(false);
          $member_data['profile'] = (object)$member->custom_profile_values();
          $sub['member'] = (array)$member_utils->trim_obj((array)$member_data);
        }
      }

      if(isset($sub['coupon']) && is_numeric($sub['coupon']) && (int)$sub['coupon'] > 0) {
        $grp = new MeprCoupon($sub['coupon']);
        $sub['coupon'] = $coupon_utils->map_vars((array)$grp->rec);
        $sub['coupon'] = $coupon_utils->trim_obj($sub['coupon']);
      }
      else {
        $sub['coupon'] = false;
      }

      if(isset($sub['membership']) && is_numeric($sub['membership']) && (int)$sub['membership'] > 0) {
        $prd = new MeprProduct($sub['membership']);
        $sub['membership'] = $membership_utils->map_vars((array)$prd->rec);
        $sub['membership'] = $membership_utils->trim_obj($sub['membership']);
      }
      else {
        $sub['membership'] = false;
      }

      if(empty($sub['gateway'])) {
        $sub['gateway'] = 'manual';
      }

      return $sub;
    }

    // Used to implement custom search args
    protected function get_data_query_custom_clauses(Array $args) {
      global $wpdb;

      $clauses='';
      if(isset($args['member']) && is_numeric($args['member'])) {
        $clauses .= $wpdb->prepare(
          "
            AND (SELECT pm_member.meta_value
                   FROM {$wpdb->postmeta} AS pm_member
                  WHERE pm_member.post_id=p.ID
                    AND pm_member.meta_key=%s
                  LIMIT 1) = %d
          ",
          MeprSubscription::$user_id_str,
          $args['member']
        );
      }

      if(isset($args['membership']) && is_numeric($args['membership'])) {
        $clauses .= $wpdb->prepare(
          "
            AND (SELECT pm_membership.meta_value
                   FROM {$wpdb->postmeta} AS pm_membership
                  WHERE pm_membership.post_id=p.ID
                    AND pm_membership.meta_key=%s
                  LIMIT 1) = %d
          ",
          MeprSubscription::$product_id_str,
          $args['membership']
        );
      }

      if(isset($args['coupon']) && is_numeric($args['coupon'])) {
        $clauses .= $wpdb->prepare(
          "
            AND (SELECT pm_coupon.meta_value
                   FROM {$wpdb->postmeta} AS pm_coupon
                  WHERE pm_coupon.post_id=p.ID
                    AND pm_coupon.meta_key=%s
                  LIMIT 1) = %d
          ",
          MeprSubscription::$coupon_id_str,
          $args['coupon']
        );
      }

      if(isset($args['status']) && is_string($args['status'])) {
        $clauses .= $wpdb->prepare(
          "
            AND (SELECT pm_status.meta_value
                   FROM {$wpdb->postmeta} AS pm_status
                  WHERE pm_status.post_id=p.ID
                    AND pm_status.meta_key=%s
                  LIMIT 1) = %s
          ",
          MeprSubscription::$status_str,
          $args['status']
        );
      }

      if(isset($args['gateway']) && is_string($args['gateway'])) {
        $clauses .= $wpdb->prepare(
          "
            AND (SELECT pm_gateway.meta_value
                   FROM {$wpdb->postmeta} AS pm_gateway
                  WHERE pm_gateway.post_id=p.ID
                    AND pm_gateway.meta_key=%s
                  LIMIT 1) = %s
          ",
          MeprSubscription::$gateway_str,
          $args['gateway']
        );
      }

      return $clauses;
    }
  }

}
else {

  class MpdtSubscriptionUtils extends MpdtBaseUtils {
    public $model_class = 'MeprSubscription';

    public function __construct() {
      $this->map  = array(
        'coupon_id'  => 'coupon',
        'product_id' => 'membership',
        'user_id'    => 'member',
      );

      parent::__construct();
    }

    public function extend_obj(Array $sub) {
      $coupon_utils = MpdtUtilsFactory::fetch('coupon');
      $member_utils = MpdtUtilsFactory::fetch('member');
      $membership_utils = MpdtUtilsFactory::fetch('membership');

      if(isset($sub['member']) && is_numeric($sub['member']) && (int)$sub['member'] > 0) {
        $member = new MeprUser($sub['member']);
        if(!empty($member->ID)) {
          $member_data = $member_utils->map_vars((array)$member->rec);
          $sub['member'] = (array)$member_utils->trim_obj((array)$member_data);
        }
      }

      if(isset($sub['coupon']) && is_numeric($sub['coupon']) && (int)$sub['coupon'] > 0) {
        $grp = new MeprCoupon($sub['coupon']);
        $sub['coupon'] = $coupon_utils->map_vars((array)$grp->rec);
        $sub['coupon'] = $coupon_utils->trim_obj($sub['coupon']);
      }
      else {
        $sub['coupon'] = false;
      }

      if(isset($sub['membership']) && is_numeric($sub['membership']) && (int)$sub['membership'] > 0) {
        $prd = new MeprProduct($sub['membership']);
        $sub['membership'] = $membership_utils->map_vars((array)$prd->rec);
        $sub['membership'] = $membership_utils->trim_obj($sub['membership']);
      }
      else {
        $sub['membership'] = false;
      }

      if(empty($sub['gateway'])) {
        $sub['gateway'] = 'manual';
      }

      return $sub;
    }

    protected function get_data_query(Array $args, $count=false) {
      global $wpdb;

      $mpdt_db = new MeprDb();
      $tablename = $mpdt_db->subscriptions;

      $rc = new ReflectionClass($this->model_class);

      $clauses='';
      if(!empty($args['id'])) {
        $clauses .= $wpdb->prepare("
           WHERE sub.id = %d
        ",
        $args['id']);
      }

      if(!empty($args['search'])) {
        $where = $this->get_where_operator($clauses);
        $clauses .= $wpdb->prepare("
          {$where} sub.subscr_id LIKE %s
        ",
        '%'.$args['search'].'%');
      }

      if(isset($args['member']) && is_numeric($args['member'])) {
        $where = $this->get_where_operator($clauses);
        $clauses .= $wpdb->prepare(
          "
            {$where} sub.user_id = %d
          ",
          $args['member']
        );
      }

      if(isset($args['membership']) && is_numeric($args['membership'])) {
        $where = $this->get_where_operator($clauses);
        $clauses .= $wpdb->prepare(
          "
            {$where} sub.product_id = %d
          ",
          $args['membership']
        );
      }

      if(isset($args['coupon']) && is_numeric($args['coupon'])) {
        $where = $this->get_where_operator($clauses);
        $clauses .= $wpdb->prepare(
          "
            {$where} sub.coupon_id = %d
          ",
          $args['coupon']
        );
      }

      if(isset($args['status']) && is_string($args['status'])) {
        $where = $this->get_where_operator($clauses);
        $clauses .= $wpdb->prepare(
          "
            {$where} sub.status = %s
          ",
          $args['status']
        );
      }

      if(isset($args['gateway']) && is_string($args['gateway'])) {
        $where = $this->get_where_operator($clauses);
        $clauses .= $wpdb->prepare(
          "
            {$where} sub.gateway = %s
          ",
          $args['gateway']
        );
      }

      $limit_statement='';
      if(!$count && (int)$args['per_page'] !== -1) {
        $limit_statement = $wpdb->prepare("
          LIMIT %d OFFSET %d
        ",
        (int)$args['per_page'],
        (((int)$args['page']-1) * (int)$args['per_page']));
      }

      $args['order'] = strtolower($args['order']);

      $order_statement = ($count ? '' : "ORDER BY sub.{$args['order']} {$args['order_dir']}");

      $select_vars = ($count ? 'COUNT(*)' : 'sub.id');

      $q = "
        SELECT {$select_vars}
          FROM {$tablename} AS sub
        {$clauses}
        {$order_statement}
        {$limit_statement}
      ";

      return $q;
    }
  }

}
