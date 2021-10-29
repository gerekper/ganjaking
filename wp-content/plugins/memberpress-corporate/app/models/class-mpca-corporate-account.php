<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Corporate_Account extends MeprBaseModel {

  public function __construct($obj = null) {
    $this->initialize(
      array(
        'id'                => 0,
        'user_id'           => 0,
        'obj_id'            => 0,
        'obj_type'          => null,
        'num_sub_accounts'  => 0,
        'status'            => 'enabled',
        'uuid'              => ''
      ),
      $obj
    );
  }

  public function validate() {
    $var = $this->num_sub_accounts;
    $this->validate_is_numeric($var, 0, null, __('Max sub-accounts', 'memberpress-corporate'));

    if($this->num_sub_accounts_used() >= $var) {
      return new WP_Error('validation', __('Exceeded number of allowed sub-accounts', 'memberpress-corporate'));
    }

    return true;
  }

  public static function get_one($id, $return_type = OBJECT) {
    $mepr_db = MeprDb::fetch();
    $mpca_db = MPCA_Db::fetch();
    $args = compact('id');
    return $mepr_db->get_one_record($mpca_db->corporate_accounts, $args, $return_type);
  }

  public static function get_count() {
    $mepr_db = new MeprDb();
    $mpca_db = MPCA_Db::fetch();
    return $mepr_db->get_count($mpca_db->corporate_accounts);
  }

  public static function get_all($order_by = '', $limit = '') {
    $mepr_db = MeprDb::fetch();
    $mpca_db = MPCA_Db::fetch();
    return $mepr_db->get_records($mpca_db->corporate_accounts, array(), $order_by, $limit);
  }

  public static function get_all_by_user_id($user_id, $order_by = '', $limit = '') {
    $mepr_db = MeprDb::fetch();
    $mpca_db = MPCA_Db::fetch();
    $args = array('user_id' => $user_id);
    return $mepr_db->get_records($mpca_db->corporate_accounts, $args, $order_by, $limit);
  }

  public static function find_corporate_account_by_obj_id($obj_id, $obj_type) {
    $mepr_db = MeprDb::fetch();
    $mpca_db = MPCA_Db::fetch();

    $args = array('obj_id' => $obj_id, 'obj_type' => $obj_type);

    $ca = $mepr_db->get_one_record($mpca_db->corporate_accounts, $args);

    if(empty($ca)) { return false; }

    $ca_obj = new MPCA_Corporate_Account();
    $ca_obj->load_from_array($ca);

    return $ca_obj;
  }

  /**
   * Checks if object is a sub account
   *
   * @param  mixed $obj_id
   * @param  string $obj_type
   *
   * @return mixed
   */
  public static function is_obj_sub_account($obj_id, $obj_type) {
    global $wpdb;

    $mepr_db = MeprDb::fetch();
    $id_col = 'subscriptions' == $obj_type ? 'subscription_id' : 'id';

    $q = $wpdb->prepare("
    SELECT id
      FROM {$mepr_db->transactions}
      WHERE {$id_col} = %d
        AND txn_type = %s
      LIMIT 1
      ",
      $obj_id,
      'sub_account'
    );
    return $wpdb->get_var($q);
  }

  public static function find_corporate_account_by_obj($obj) {
    if(!($obj instanceof MeprSubscription) &&
       !($obj instanceof MeprTransaction)) {
      return false;
    }

    $obj_id = $obj->id;
    $obj_type = self::get_obj_type($obj);

    return self::find_corporate_account_by_obj_id($obj_id,$obj_type);
  }

  // Utilitiy to determine the subscription type of an object
  public static function get_obj_type($obj) {
    if($obj instanceof MeprSubscription) {
      return 'subscriptions';
    } else if($obj instanceof MeprTransaction){
      return 'transactions';
    } else {
      return false;
    }
  }

  public function user() {
    return new MeprUser($this->user_id);
  }

  public function get_obj() {
    if($this->obj_type=='subscriptions') {
      return new MeprSubscription($this->obj_id);
    }
    else {
      return new MeprTransaction($this->obj_id);
    }
  }

  public function get_uuid() {
    return $this->uuid;
  }

  private function create_uuid() {
    return md5(base64_encode(uniqid()));
  }

  public static function find_by_uuid($uuid) {
    $mepr_db = MeprDb::fetch();
    $mpca_db = MPCA_Db::fetch();

    $args = apply_filters('mpca-find-by-uuid', array('uuid' => $uuid));

    $ca = $mepr_db->get_one_record($mpca_db->corporate_accounts, $args);

    if(empty($ca)) { return false; }

    $ca_obj = new MPCA_Corporate_Account();
    $ca_obj->load_from_array($ca);

    return $ca_obj;
  }

  public function store() {
    $mepr_db = MeprDb::fetch();
    $mpca_db = MPCA_Db::fetch();

    $vals = (array)$this->get_values();

    if( empty($this->uuid) ) {
      $vals['uuid'] = $this->create_uuid();
    }

    if(isset($this->id) and !is_null($this->id) and (int)$this->id > 0) {
      $mepr_db->update_record( $mpca_db->corporate_accounts, $this->id, $vals );
    }
    else {
      $this->id = $mepr_db->create_record( $mpca_db->corporate_accounts, $vals, false );
    }

    $this->maybe_sync_sub_account_transactions();

    return $this->id;
  }

  public function maybe_sync_sub_account_transactions() {
    if($this->is_enabled()) {
      $this->setup_parent_transaction();
      $this->sync_sub_account_transactions();
    }
    else {
      $this->expire_sub_account_transactions();
    }
  }

  public function destroy() {
    $mepr_db = new MeprDb();
    $mpca_db = MPCA_Db::fetch();

    $this->delete_sub_account_transactions();
    $this->reset_parent_transactions();

    $id = $this->id;
    $args = compact('id');
    return $mepr_db->delete_records($mpca_db->corporate_accounts, $args);
  }

  public function get_expiring_transaction() {
    global $wpdb;
    $mepr_db = MeprDb::fetch();
    $mpca_db = MPCA_Db::fetch();

    $obj = $this->get_obj();

    if($this->obj_type=='transactions') {
      $expiring_transaction = $obj;
    }
    else if($this->obj_type=='subscriptions') {
      $expiring_transaction = $obj->expiring_txn();

      //$q = $wpdb->prepare(
      //  "
      //    SELECT t.id
      //      FROM {$mepr_db->transactions} AS t
      //     WHERE t.subscription_id=%d
      //       AND t.txn_type IN (%s,%s)
      //       AND t.txn_status IN (%s,%s)
      //       AND t.expires_at <> NULL
      //       AND t.expires_at <> '0000-00-00 00:00:00'
      //       AND t.expires_at > %s
      //     ORDER BY t.expires_at DESC
      //     LIMIT 1
      //  ",
      //  $obj->id,
      //  MeprTransaction::$payment_str,
      //  MeprTransaction::$subscription_confirmation_str,
      //  MeprTransaction::$complete_str,
      //  MeprTransaction::$confirmed_str,
      //  $mpca_db->now()
      //);

      //$id = $wpdb->get_var($q);

      //$expiring_transaction = new MeprTransaction($id);
    }

    if($expiring_transaction !== false) {
      // TODO: Figure out why caching is making this necessary
      $expiring_transaction = new MeprTransaction($expiring_transaction->id);
    }

    return $expiring_transaction;
  }

  public function enable() {
    $this->status = 'enabled';
    $this->store();
  }

  public function disable() {
    $this->status = 'disabled';
    $this->store();
  }

  public function is_enabled() {
    return ($this->status=='enabled');
  }

  public function is_disabled() {
    return ($this->status=='disabled');
  }

  public function num_sub_accounts_used() {
    global $wpdb;
    $mepr_db = MeprDb::fetch();

    $q = $wpdb->prepare(
      "
        SELECT COUNT(DISTINCT user_id)
          FROM {$wpdb->usermeta}
         WHERE meta_key=%s
           AND meta_value=%s
      ",
      'mpca_corporate_account_id',
      $this->id
    );

    return $wpdb->get_var($q);
  }

  /**
   * Add a sub account user or return false
   *
   * @param int $id User ID of the sub-account to associate with
   * @return Transaction The sub account's transaction
   */
  public function add_sub_account_user($user_id) {
    $corporate_account_ids = get_user_meta($user_id,'mpca_corporate_account_id');

    $result = $this->validate();
    if( is_wp_error($result) ) {
      return $result;
    }

    if(!in_array($this->id, $corporate_account_ids)) {
      add_user_meta($user_id, 'mpca_corporate_account_id', $this->id);
    }

    // Get the parent_transaction and ensure it's setup properly
    $parent_transaction = $this->setup_parent_transaction();

    $transaction = self::get_user_sub_account_transaction($user_id);

    if(!$transaction) {
      $transaction_id = MPCA_Sync_Transactions::add_transaction($user_id, $parent_transaction->id);
    }
    else {
      $transaction_id = MPCA_Sync_Transactions::update_transaction($transaction->id, $parent_transaction->id);
    }

    MPCA_Event::record_event('sub-account-added', $transaction_id, MPCA_Event::$transactions_str);
    return new MeprTransaction($transaction_id);
  }

  public function get_user_sub_account_transaction($user_id) {
    $parent_transaction = $this->get_expiring_transaction();
    $transaction_id = MPCA_Sync_Transactions::get_sub_account_transaction_id($user_id, $parent_transaction->id);

    if($transaction_id) {
      return new MeprTransaction($transaction_id);
    }
    else {
      return false;
    }
  }

  public function remove_sub_account_user($user_id) {
    global $wpdb;
    $mepr_db = MeprDb::fetch();
    $transaction = self::get_user_sub_account_transaction($user_id);

    //Let's expire the txn instead of deleting it
    //That way our transaction-expired events will still trigger
    //And the sub-account user will be removed from marketing lists etc like they should be
    $q = $wpdb->prepare(
      " UPDATE {$mepr_db->transactions}
        SET expires_at=%s
        WHERE txn_type=%s
          AND corporate_account_id=%d
          AND user_id=%d
      ",
      gmdate('c', (time() - 3600)), //Expired 1 hour ago just to make sure they're not active
      'sub_account',
      $this->id,
      $user_id
    );

    $wpdb->query($q);

    delete_user_meta($user_id, 'mpca_corporate_account_id', $this->id);

    if($transaction !== false) {
      MPCA_Event::record_event('sub-account-removed', $transaction->id, MPCA_Event::$transactions_str);
    }
  }

  public function sub_account_management_url() {
    $mepr_options = MeprOptions::fetch();
    return $mepr_options->account_page_url('action=manage_sub_accounts&ca='.$this->uuid);
  }

  public function import_url() {
    $user = $this->user();
    return admin_url("admin.php?page=memberpress-import&importer=corporatesubaccounts&parent={$user->user_login}&ca={$this->id}");
  }

  public function export_url() {
    return admin_url("admin-ajax.php?action=mpca_export_csv&ca={$this->id}");
  }

  public function signup_url() {
    $sub = $this->get_obj();
    $product = $sub->product();
    return $product->url() . "?ca={$this->uuid}";
  }

  public function sub_id() {
    $obj = $this->get_obj();

    if($obj instanceof MeprSubscription) {
      return $obj->subscr_id;
    }
    else {
      return $obj->trans_num;
    }
  }

  public function sub_users() {
    $user_ids = MPCA_Sync_Transactions::get_sub_user_ids($this->id);

    $users = array();
    foreach($user_ids as $user_id) {
      $users[] = new MeprUser($user_id);
    }

    return $users;
  }

  public function current_user_has_access() {
    $current_user = MeprUtils::get_currentuserinfo();
    return (MeprUtils::is_mepr_admin() || $current_user->ID == $this->user_id);
  }

  public function copy_sub_accounts_from($ca) {
    $sub_user_ids = MPCA_Sync_Transactions::get_sub_user_ids($ca->id);

    foreach($sub_user_ids as $sub_user_id) {
      // Get all the corporate accounts for the sub user
      $corporate_accounts = get_user_meta($sub_user_id, 'mpca_corporate_account_id', false);

      // Ensure the user isn't already associated with the current corporate account ($this) already
      if(!(in_array($this->id, $corporate_accounts)) ) {
        add_user_meta($sub_user_id, 'mpca_corporate_account_id', $this->id);
      }
    }
  }

  public function sub_account_list_table( $order_by = 'last_name',
                                          $order = 'ASC',
                                          $paged = '',
                                          $perpage = 10,
                                          $search = '' ) {
    global $wpdb;
    $mepr_db = MeprDb::fetch();

    $cols = array(
      'ID' => 'u.ID',
      'user_login' => 'u.user_login',
      'user_email' => 'u.user_email',
      'first_name' => 'um_first_name.meta_value',
      'last_name' => 'um_last_name.meta_value'
    );

    $joins = array(
      "LEFT JOIN {$wpdb->usermeta} AS um_first_name ON um_first_name.user_id = u.ID AND um_first_name.meta_key='first_name'",
      "LEFT JOIN {$wpdb->usermeta} AS um_last_name ON um_last_name.user_id = u.ID AND um_last_name.meta_key='last_name'",
      "/* IMPORTANT */ JOIN (
           SELECT um1.*
             FROM {$wpdb->usermeta} AS um1
            WHERE um1.meta_key='mpca_corporate_account_id'
            GROUP BY um1.user_id, um1.meta_value
          ) AS um_corporate_account_id
          ON um_corporate_account_id.user_id = u.ID"
    );

    $args = array(
      $wpdb->prepare(
        'um_corporate_account_id.meta_value=%s',
        $this->id
      )
    );

    return MeprDb::list_table(
      $cols, "{$wpdb->users} AS u",
      $joins, $args, $order_by, $order,
      $paged, $search, 'any', $perpage
    );
  }

  // ******** Sync transaction convenience methods ********* /

  public function setup_parent_transaction() {
    $parent_transaction = $this->get_expiring_transaction();
    if($parent_transaction) {
      return MPCA_Sync_Transactions::setup_parent_transaction($parent_transaction, $this->id);
    }
  }

  public function reset_parent_transaction() {
    $parent_transaction = $this->get_expiring_transaction();
    if($parent_transaction) {
      return MPCA_Sync_Transactions::reset_parent_transaction($parent_transaction);
    }
  }

  public function sync_sub_account_transactions() {
    $parent_transaction = $this->get_expiring_transaction();
    if($parent_transaction) {
      return MPCA_Sync_Transactions::sync_sub_account_transactions($parent_transaction);
    }
  }

  public function delete_sub_account_transactions() {
    return MPCA_Sync_Transactions::delete_sub_account_transactions($this->id);
  }

  private function expire_sub_account_transactions() {
    return MPCA_Sync_Transactions::expire_sub_account_transactions($this->id);
  }

  public function reset_parent_transactions() {
    return MPCA_Sync_Transactions::reset_parent_transactions($this->id);
  }

}
