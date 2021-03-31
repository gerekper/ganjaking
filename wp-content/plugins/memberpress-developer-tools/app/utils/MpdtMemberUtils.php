<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtMemberUtils extends MpdtBaseUtils {
  public $model_class = 'MeprUser';

  public function __construct() {
    $this->map  = array(
      'ID'                  => 'id',
      'user_email'          => 'email',
      'user_login'          => 'username',
      'user_nicename'       => 'nicename',
      'user_url'            => 'url',
      'user_pass'           => 'password',
      'user_message'        => 'message',
      'user_registered'     => 'registered_at',
      'user_activation_key' => false,
      'user_status'         => false,
      'signup_notice_sent'  => false,
      'uuid'                => false
    );

    parent::__construct();
  }

  protected function extend_obj(Array $mbr) {
    $u = new MeprUser($mbr['id']);
    $mbr['active_memberships'] = array();

    // Add counts
    $count_fields = array('active_txn_count', 'expired_txn_count', 'trial_txn_count', 'sub_count', 'login_count');
    foreach($count_fields as $field) {
      $mbr[$field] = $u->$field;
    }
    $txn_utils = MpdtUtilsFactory::fetch('transaction');
    $first_txn = $u->first_txn;
    if($first_txn) {
      $first_txn = $txn_utils->map_vars((array)$first_txn->rec);
      $mbr['first_txn'] = $txn_utils->trim_obj($first_txn);
    }
    $latest_txn = $u->latest_txn;
    if($latest_txn) {
      $latest_txn = $txn_utils->map_vars((array)$latest_txn->rec);
      $mbr['latest_txn'] = $txn_utils->trim_obj($latest_txn);
    }
    $membership_utils = MpdtUtilsFactory::fetch('membership');
    $memberships = $u->active_product_subscriptions('products');
    foreach($memberships as $membership) {
      $data = $membership_utils->map_vars((array)$membership->rec);
      $mbr['active_memberships'][] = (array)$membership_utils->trim_obj((array)$data);
    }

    // List out address & custom profile values
    $mbr['address'] = (object)$u->full_address(false);
    $mbr['profile'] = (object)$u->custom_profile_values(true);

    $rt = (object)$u->recent_transactions();
    $mbr['recent_transactions'] = array();
    foreach($rt as $i => $t) {
      $mbr['recent_transactions'][$i] = $txn_utils->map_vars((array)$t->rec);
      $mbr['recent_transactions'][$i] = $txn_utils->trim_obj($mbr['recent_transactions'][$i]);
    }

    $rs = (object)$u->recent_subscriptions();
    $utils = MpdtUtilsFactory::fetch('subscription');
    $mbr['recent_subscriptions'] = array();
    foreach($rs as $i => $s) {
      $mbr['recent_subscriptions'][$i] = $utils->map_vars((array)$s->rec);
      $mbr['recent_subscriptions'][$i] = $utils->trim_obj($mbr['recent_subscriptions'][$i]);
    }

    return $mbr;
  }

  protected function get_data_query(Array $args, $count=false) {
    global $wpdb;

    $rc = new ReflectionClass($this->model_class);

    $id_clause='';
    if(!empty($args['id'])) {
      $id_clause = $wpdb->prepare("
         WHERE u.ID = %d
      ",
      $args['id']);
    }

    $search_clause='';
    if(!empty($args['search'])) {
      $where = (empty($id_clause) ? 'WHERE' : 'AND');
      $search_string = '%'.$args['search'].'%';
      $search_clause = $wpdb->prepare("
          {$where} (
            u.user_login LIKE %s
            OR u.user_email LIKE %s
          )
        ",
        $search_string, $search_string,
        $search_string, $search_string
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

    $order_statement = ($count ? '' : "ORDER BY u.{$args['order']} {$args['order_dir']}");

    $select_vars = ($count ? 'COUNT(*)' : 'u.ID');

    $q = "
      SELECT {$select_vars}
        FROM {$wpdb->users} AS u
      {$id_clause}
      {$search_clause}
      {$order_statement}
      {$limit_statement}
    ";

    return $q;
  }
}
