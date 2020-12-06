<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Sync_Transactions {
  // Used to set a given transaction as a corporate_account parent transaction
  // We're using SQL here to avoid calling store() ... as there are some hooks
  // that will now fire on store that could cause an infinite recursion
  public static function setup_parent_transaction($parent_transaction, $corporate_account_id) {
    global $wpdb;
    $mepr_db = MeprDb::fetch();

    if($parent_transaction->corporate_account_id != $corporate_account_id) {
      $q = $wpdb->prepare("
          UPDATE {$mepr_db->transactions}
             SET corporate_account_id=%d,
                 parent_transaction_id=0
           WHERE id=%d
        ",
        $corporate_account_id,
        $parent_transaction->id
      );

      $wpdb->query($q);

      // Re-init object after update
      $parent_transaction = new MeprTransaction($parent_transaction->id);
    }

    return $parent_transaction;
  }

  // Used to reset a given transaction as a corporate_account parent transaction
  // We're using SQL here to avoid calling store() ... as there are some hooks
  // that will now fire on store that could cause an infinite recursion
  public static function reset_parent_transaction($parent_transaction) {
    global $wpdb;
    $mepr_db = MeprDb::fetch();

    $q = $wpdb->prepare("
        UPDATE {$mepr_db->transactions}
           SET corporate_account_id=0,
               parent_transaction_id=0
         WHERE id=%d
      ",
      $parent_transaction->id
    );

    $wpdb->query($q);

    // Re-init object after update
    $parent_transaction = new MeprTransaction($parent_transaction->id);

    return $parent_transaction;
  }

  // Resets all parent transactions for a given corporate_account
  public static function reset_parent_transactions($corporate_account_id) {
    global $wpdb;
    $mepr_db = MeprDb::fetch();

    $q = $wpdb->prepare(
      "
        UPDATE {$mepr_db->transactions}
           SET corporate_account_id=0,
               parent_transaction_id=0
         WHERE txn_type IN (%s,%s)
           AND corporate_account_id=%d
      ",
      MeprTransaction::$payment_str,
      MeprTransaction::$subscription_confirmation_str,
      $corporate_account_id
    );

    return $wpdb->query($q);
  }

  // Add a sub_account transaction
  public static function add_transaction($user_id, $parent_transaction_id) {
    $mpca_db = MPCA_Db::fetch();
    MeprUtils::debug_log("MPCA_Sync_Transactions: {$user_id} {$parent_transaction_id}");

    $parent_transaction = new MeprTransaction($parent_transaction_id);

    $sub_account_transaction = new MeprTransaction();

    $sub_account_transaction->user_id = $user_id;
    $sub_account_transaction->txn_type = 'sub_account';
    $sub_account_transaction->parent_transaction_id = $parent_transaction_id;
    $sub_account_transaction->subscription_id = 0; // Don't want to confuse expiring_txn
    $sub_account_transaction->created_at = $mpca_db->now();
    $sub_account_transaction->status = MeprTransaction::$complete_str;
    $sub_account_transaction->product_id = $parent_transaction->product_id;
    $sub_account_transaction->expires_at = $parent_transaction->expires_at;
    $sub_account_transaction->corporate_account_id = $parent_transaction->corporate_account_id;
    $sub_account_transaction->gateway = MeprTransaction::$manual_gateway_str;

    return $sub_account_transaction->store();

  }

  // Update a sub_account transaction
  public static function update_transaction($transaction_id, $parent_transaction_id) {
    $sub_account_transaction = new MeprTransaction($transaction_id);
    $parent_transaction = new MeprTransaction($parent_transaction_id);

    $sub_account_transaction->status = ($parent_transaction->status == MeprTransaction::$confirmed_str) ? MeprTransaction::$complete_str : $parent_transaction->status;
    $sub_account_transaction->product_id = $parent_transaction->product_id;
    $sub_account_transaction->expires_at = $parent_transaction->expires_at;
    $sub_account_transaction->parent_transaction_id = $parent_transaction->id;
    $sub_account_transaction->corporate_account_id = $parent_transaction->corporate_account_id;

    return $sub_account_transaction->store();
  }

  // Synchronizes all of the sub_account transactions for a given parent transaction.
  // This basically looks up all the sub_account users and then either generates or
  // updates a sub_account transaction for each sub_account user.
  public static function sync_sub_account_transactions($parent_transaction) {
    if(!self::is_valid_transaction($parent_transaction)) { return; }

    require_once(MPCA_JOBS_PATH . '/MeprSyncSubAccountJob.php');

    $sub_user_ids = self::get_sub_user_ids($parent_transaction->corporate_account_id);

    foreach($sub_user_ids as $user_id) {
      $job = new MeprSyncSubAccountJob();
      $job->user_id = $user_id;
      $job->parent_transaction_id = $parent_transaction->id;
      $job->enqueue();
    }
  }

  // Deletes all sub account trnasactions for a given corporate_account_id
  public static function delete_sub_account_transactions($corporate_account_id) {
    global $wpdb;
    $mepr_db = MeprDb::fetch();

    $q = $wpdb->prepare(
      "
        DELETE FROM {$mepr_db->transactions}
         WHERE txn_type=%s
           AND corporate_account_id=%d
      ",
      'sub_account',
      $corporate_account_id
    );

    $wpdb->query($q);
  }

  // Expire all sub account trnasactions for a given corporate_account_id
  public static function expire_sub_account_transactions($corporate_account_id) {
    global $wpdb;
    $mepr_db = MeprDb::fetch();

    $q = $wpdb->prepare(
      "
        UPDATE {$mepr_db->transactions}
           SET expires_at = DATE_SUB(NOW(), INTERVAL 1 DAY)
         WHERE txn_type=%s
           AND corporate_account_id=%d
           AND expires_at > NOW()
      ",
      'sub_account',
      $corporate_account_id
    );

    $wpdb->query($q);
  }

  // Checks to see if the transaction is valid as a parent transaction
  public static function is_valid_transaction($transaction) {
    return (
      (
        $transaction->txn_type == 'payment' ||
        $transaction->txn_type == 'subscription_confirmation'
      ) &&
      !empty($transaction->corporate_account_id)
    );
  }

  // Get all the sub_account user_ids currently set
  // TODO move this function to the corporate account model
  public static function get_sub_user_ids($corporate_account_id) {
    global $wpdb;

    $q = $wpdb->prepare("
        SELECT DISTINCT user_id
          FROM {$wpdb->usermeta}
          WHERE meta_key = %s
            AND meta_value = %s
      ",
      'mpca_corporate_account_id',
      $corporate_account_id
    );

    return $wpdb->get_col($q);
  }

  // Get a specific transaction_id for the given user and parent_transaction_id
  public static function get_sub_account_transaction_id($user_id, $parent_transaction_id) {
    global $wpdb;

    $mepr_db = MeprDb::fetch();

    $q = $wpdb->prepare("
        SELECT id
          FROM {$mepr_db->transactions}
          WHERE user_id = %d
            AND parent_transaction_id = %d
            AND txn_type = %s
          LIMIT 1
      ",
      $user_id,
      $parent_transaction_id,
      'sub_account'
    );

    return $wpdb->get_var($q);
  }
}
