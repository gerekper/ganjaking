<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_App_Controller {
  public function __construct() {
    add_action('admin_init',     array($this,'upgrade_db'));
    add_action('mepr-txn-store', array($this, 'sync_sub_account_transactions'));
    add_action('mepr-txn-status-refunded', array($this, 'disable_on_refund'));

    // Enqueue admin scripts
    add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts') );

    // List table modification
    add_filter('mepr_user_subscriptions_query_cols',             array($this,'customize_subscription_query_cols'));
    add_filter('mepr_recurring_subscriptions_table_cols',        array($this,'list_table_cols'));
    add_filter('mepr_recurring_subscriptions_table_joins',       array($this,'list_table_sub_joins'));
    add_filter('mepr_nonrecurring_subscriptions_table_cols',     array($this,'list_table_txn_cols'));
    add_filter('mepr_nonrecurring_subscriptions_table_joins',    array($this,'list_table_txn_joins'));
    add_filter('mepr_user_subscriptions_customize_subscription', array($this,'customize_subscription_objects'), 10, 3);

    add_filter( 'mepr_view_paths', array( $this, 'add_view_path' ) );

    // Import hooks
    add_filter( 'mepr_import_subscription_post_store', array($this, 'import_subscription') );

    // Load language - must be done after plugins are loaded to work with PolyLang/WPML
    add_action('plugins_loaded', array($this, 'load_language'));
  }

  public function enqueue_admin_scripts() {
    wp_enqueue_style( 'mpca-fontello-mp-corporate',
                      MPCA_FONTS_URL.'/fontello/css/mp-corporate.css',
                      array(), MEPR_VERSION );
    wp_enqueue_style('mpca-admin-list-table', MPCA_CSS_URL . '/mpca-admin-list-table.css');
  }

  public function upgrade_db() {
    $mpca_db = MPCA_Db::fetch();

    if($mpca_db->do_upgrade()) {
      @ignore_user_abort(true);
      @set_time_limit(0);

      if(is_multisite() && is_super_admin()) {
        global $blog_id;
        // If we're on the root blog then let's upgrade every site on the network
        if($blog_id==1) {
          $mpca_db->upgrade_multisite();
        }
        else {
          $mpca_db->upgrade();
        }
      }
      else {
        $mpca_db->upgrade();

        // Assign UUID to any accounts that don't have one
        $corporate_account_users = MPCA_Corporate_Account::get_all();

        foreach( $corporate_account_users as $user ) {
          if( empty($user->uuid) ) {
            $ca = new MPCA_Corporate_Account();
            $ca->load_from_array($user);
            $ca->store();
          }
        }
      }
    }
  }

  public function sync_sub_account_transactions($transaction) {
    // Make sure it's a completed or confirmed transaction, otherwise we don't do the sync
    if($transaction->status != MeprTransaction::$complete_str && $transaction->status != MeprTransaction::$confirmed_str) {
      return;
    }

    static $already_here;

    if(isset($already_here[$transaction->id]) && $already_here[$transaction->id]) { return; }

    if(empty($transaction->corporate_account_id) && ($sub = $transaction->subscription())) {
      $ca = MPCA_Corporate_Account::find_corporate_account_by_obj($sub);

      if($ca !== false && isset($ca->id) && $ca->id) {
        $transaction->corporate_account_id = $ca->id;
        $transaction->store();
        $already_here[$transaction->id] = true;
      }
    }

    // Bail if not a corporate account transaction or if it's a sub_account transaction
    if((empty($transaction->corporate_account_id)) || $transaction->txn_type == 'sub_account') {
      return;
    }

    // Don't want to use 'sync_sub_account_transactions' in the corporate account
    // model because it won't necessarily use this transaction. We'll use the one
    // in the sync utility so we're assuredly syncing the correct transaction.
    //$ca = new MPCA_Corporate_Account($transaction->corporate_account_id);
    //$ca->sync_sub_account_transactions();

    MPCA_Sync_Transactions::sync_sub_account_transactions($transaction);
  }

  public function disable_on_refund($txn) {
    if(($sub = $txn->subscription())) {
      $ca = MPCA_Corporate_Account::find_corporate_account_by_obj($sub);
    }
    else {
      $ca = MPCA_Corporate_Account::find_corporate_account_by_obj($txn);
    }

    if($ca !== false && isset($ca->id) && $ca->id) {
      $ca->disable(); //Disable will expire sub-accounts also
    }
  }

  public function list_table_cols($cols) {
    $cols['corporate_account_id'] = 'IFNULL(ca.id,0)';
    $cols['num_sub_accounts'] = 'IFNULL(ca.num_sub_accounts,0)';

    return $cols;
  }

  public function list_table_txn_cols($cols) {
    $cols = $this->list_table_cols($cols);
    // $cols['parent'] = 'IFNULL(p.user_login,\'\')';

    return $cols;
  }

  public function list_table_sub_joins($joins) {
    return $this->list_table_joins($joins, 'sub', 'subscriptions');
  }

  public function list_table_txn_joins($joins) {
    global $wpdb;
    $mp_db = MeprDB::fetch();

    $joins = $this->list_table_joins($joins, 'txn', 'transactions');

    $joins[] = "LEFT JOIN {$mp_db->transactions} AS ptxn ON ptxn.id = txn.parent_transaction_id";
    $joins[] = "LEFT JOIN {$wpdb->users} AS p ON p.ID = ptxn.user_id";

    return $joins;
  }

  private function list_table_joins($joins, $from='sub', $sub_type='subscriptions') {
    global $wpdb;
    $mpca_db = MPCA_Db::fetch();

    $joins[] = $wpdb->prepare("
        LEFT JOIN {$mpca_db->corporate_accounts} AS ca
          ON ca.obj_type=%s
         AND {$from}.id=ca.obj_id
      ",
      $sub_type
    );

    return $joins;
  }

  public function customize_subscription_query_cols($cols) {
    $cols[] = 'corporate_account_id';
    $cols[] = 'num_sub_accounts';

    return $cols;
  }

  public function customize_subscription_objects($sub, $row, $user) {
    $sub->corporate_account_id = $row->corporate_account_id;
    $sub->num_sub_accounts = $row->num_sub_accounts;

    if(!empty($row->corporate_account_id)) {
      $ca = new MPCA_Corporate_Account($row->corporate_account_id);
      $sub->is_corporate_account = $ca->is_enabled();
      $sub->corporate_account = $ca;
    }
    else {
      $sub->is_corporate_account = false;
    }

    return $sub;
  }

  /**
   * Create a corporate account from an imported subscription id
   *
   * Hook: mepr_import_subscription_post_store
   *
   * @param int $id The ID of the subscription being imported
   */
  public function import_subscription($id) {

    $sub = new MeprSubscription($id);
    $product = $sub->product();

    // meta attributes
    $is_corporate_product = get_post_meta($product->ID, 'mpca_is_corporate_product', true);
    $num_sub_accounts = get_post_meta($product->ID, 'mpca_num_sub_accounts', true);

    // Only create corporate user record if we have a corporate membership
    if($is_corporate_product) {
      $attributes = array(
        'user_id'          => $sub->user_id,
        'obj_id'           => $id,
        'obj_type'         => 'subscriptions',
        'is_corporate'     => true,
        'num_sub_accounts' => $num_sub_accounts
      );

      $ca = new MPCA_Corporate_Account();
      $ca->load_from_array($attributes);
      $ca->store();
    }
  }

  public function load_language() {
    load_plugin_textdomain('memberpress-corporate', false, dirname(plugin_basename(__FILE__)) . '/i18n');
  }

  /**
   * Add plugin path to memberpress view path
   *
   * @param  mixed $paths MemberPress paths
   *
   * @return mixed
   */
  function add_view_path( $paths ) {
    array_splice( $paths, 1, 0, MPCA_VIEWS_PATH );
    return $paths;
  }
}
