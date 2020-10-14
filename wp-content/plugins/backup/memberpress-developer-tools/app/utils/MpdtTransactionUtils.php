<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtTransactionUtils extends MpdtBaseUtils {
  public $model_class = 'MeprTransaction';

  public function __construct() {
    $this->map  = array(
      'product_id'      => 'membership',
      'user_id'         => 'member',
      'coupon_id'       => 'coupon',
      'subscription_id' => 'subscription',
    );

    parent::__construct();
  }

  protected function extend_obj(Array $txn) {
    $cpn_utils = MpdtUtilsFactory::fetch('coupon');
    $mbr_utils = MpdtUtilsFactory::fetch('member');
    $msp_utils = MPdtUtilsFactory::fetch('membership');
    $sub_utils = MPdtUtilsFactory::fetch('subscription');

    if(isset($txn['coupon']) && (int)$txn['coupon'] > 0) {
      $cpn = new MeprCoupon($txn['coupon']);
      if(!empty($cpn->ID)) {
        $cpn_data = $cpn_utils->map_vars((array)$cpn->rec);
        $txn['coupon'] = (array)$cpn_utils->trim_obj((array)$cpn_data);
      }
    }

    if(isset($txn['member']) && (int)$txn['member'] > 0) {
      $mbr = new MeprUser($txn['member']);
      if(!empty($mbr->ID)) {
        $mbr_data = $mbr_utils->map_vars((array)$mbr->rec);
        $mbr_data['address'] = (object)$mbr->full_address(false);
        $mbr_data['profile'] = (object)$mbr->custom_profile_values();
        $txn['member'] = (array)$mbr_utils->trim_obj((array)$mbr_data);
      }
    }

    if(isset($txn['parent_transaction_id']) && (int) $txn['parent_transaction_id'] > 0) {
      $parent_txn = new MeprTransaction($txn['parent_transaction_id']);
      $parent = new MeprUser($parent_txn->user_id);
      if(!empty($parent->ID)) {
        $parent_data = $mbr_utils->map_vars((array) $parent->rec);
        $parent_data['address'] = (object)$parent->full_address(false);
        $parent_data['profile'] = (object)$parent->custom_profile_values();
        $txn['parent'] = (array) $mbr_utils->trim_obj((array) $parent_data);
      }
    }

    if(isset($txn['membership']) && (int)$txn['membership'] > 0) {
      $prd = new MeprProduct($txn['membership']);
      if(!empty($prd->ID)) {
        $prd_data = $msp_utils->map_vars((array)$prd->rec);
        $txn['membership'] = (array)$msp_utils->trim_obj((array)$prd_data);
      }
    }

    if(isset($txn['subscription']) && (int)$txn['subscription'] > 0) {
      $sub = new MeprSubscription($txn['subscription']);
      if(!empty($sub->ID)) {
        $sub_data = $sub_utils->map_vars((array)$sub->rec);
        $txn['subscription'] = (array)$sub_utils->trim_obj((array)$sub_data);
      }
    }

    $txn_obj = new MeprTransaction($txn['id']);

    if(method_exists($txn_obj,'is_rebill')) {
      $txn['rebill'] = $txn_obj->is_rebill();
    }

    if(method_exists($txn_obj,'subscription_payment_index')) {
      $txn['subscription_payment_index'] = $txn_obj->subscription_payment_index();
    }

    return $txn;
  }

  protected function get_data_query(Array $args, $count=false) {
    global $wpdb;

    $mpdt_db = new MeprDb();
    $tablename = $mpdt_db->transactions;

    $rc = new ReflectionClass($this->model_class);

    $clauses='';
    if(!empty($args['id'])) {
      $clauses .= $wpdb->prepare("
         WHERE t.id = %d
      ",
      $args['id']);
    }

    if(!empty($args['search'])) {
      $where = $this->get_where_operator($clauses);
      $clauses .= $wpdb->prepare("
        {$where} t.trans_num LIKE %s
      ",
      '%'.$args['search'].'%');
    }

    if(isset($args['member']) && is_numeric($args['member'])) {
      $where = $this->get_where_operator($clauses);
      $clauses .= $wpdb->prepare(
        "
          {$where} t.user_id = %d
        ",
        $args['member']
      );
    }

    if(isset($args['membership']) && is_numeric($args['membership'])) {
      $where = $this->get_where_operator($clauses);
      $clauses .= $wpdb->prepare(
        "
          {$where} t.product_id = %d
        ",
        $args['membership']
      );
    }

    if(isset($args['coupon']) && is_numeric($args['coupon'])) {
      $where = $this->get_where_operator($clauses);
      $clauses .= $wpdb->prepare(
        "
          {$where} t.coupon_id = %d
        ",
        $args['coupon']
      );
    }

    if(isset($args['status']) && is_string($args['status'])) {
      $where = $this->get_where_operator($clauses);
      $clauses .= $wpdb->prepare(
        "
          {$where} t.status = %s
        ",
        $args['status']
      );
    }

    if(isset($args['gateway']) && is_string($args['gateway'])) {
      $where = $this->get_where_operator($clauses);
      $clauses .= $wpdb->prepare(
        "
          {$where} t.gateway = %s
        ",
        $args['gateway']
      );
    }

    if(isset($args['subscription']) && is_numeric($args['subscription'])) {
      $where = $this->get_where_operator($clauses);
      $clauses .= $wpdb->prepare(
        "
          {$where} t.subscription_id = %d
        ",
        $args['subscription']
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

    $order_statement = ($count ? '' : "ORDER BY t.{$args['order']} {$args['order_dir']}");

    $select_vars = ($count ? 'COUNT(*)' : 't.id');

    $q = "
      SELECT {$select_vars}
        FROM {$tablename} AS t
      {$clauses}
      {$order_statement}
      {$limit_statement}
    ";

    return $q;
  }

}
