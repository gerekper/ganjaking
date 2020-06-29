<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Subscription_Controller {
  public function __construct() {
    add_action('mepr-event-subscription-changed', array($this,'subscription_changed'));
    add_action('mepr-txn-status-complete', array($this,'transaction_completed'));
    add_filter('mepr-admin-subscriptions-cols', array($this, 'customize_admin_subscriptions_cols'), 10, 3);
    add_filter('mepr-admin-subscriptions-cell', array($this, 'customize_admin_subscriptions_table_content'), 10, 4);
  }

  public function subscription_changed($e) {
    $old_txn = $e->get_data();
    $new_txn_id = $e->get_args();

    if(empty($new_txn_id)) { return; }

    $new_txn = new MeprTransaction($new_txn_id);

    if(!($old_sub = $old_txn->subscription())) {
      $old_sub = $old_txn;
    }

    if(!($new_sub = $new_txn->subscription())) {
      $new_sub = $new_txn;
    }

    if(($old_ca = MPCA_Corporate_Account::find_corporate_account_by_obj($old_sub)) &&
       ($new_ca = MPCA_Corporate_Account::find_corporate_account_by_obj($new_sub)) &&
       $old_ca->is_enabled() && $new_ca->is_enabled()) {
      $new_ca->copy_sub_accounts_from($old_ca);
      $new_ca->maybe_sync_sub_account_transactions();
      // Disable old corp account and expire the sub account transactions
      $old_ca->disable();
    }
  }

  /**
  * Handle one-time renewals of a previously purchased CA product
  */
  public function transaction_completed($new_txn) {
    global $wpdb;
    $product = $new_txn->product();
    $is_corporate_product = get_post_meta($product->ID, 'mpca_is_corporate_product', true);
    // Return if product is not a corp product or not renewable
    if(!$is_corporate_product || !$product->is_renewable()) { return; }
    $user = $new_txn->user();
    // Query: By product and status, excludes $new_txn
    $transactions_where = $wpdb->prepare('
      id!=%d
      AND product_id=%d
      AND status IN(%s, %s)
      ',
      $new_txn->id,
      $new_txn->product_id,
      MeprTransaction::$complete_str,
      MeprTransaction::$refunded_str
    );
    $user_product_prev_txns = $user->transactions($transactions_where);
    // Ensure the user has purchased the product in the past
    if(!empty($user_product_prev_txns)) {
      $latest_txn_id = $user_product_prev_txns[0]->id;
      $latest_txn = new MeprTransaction($latest_txn_id);
      $latest_ca = MPCA_Corporate_Account::find_corporate_account_by_obj($latest_txn);
      if($latest_ca && $latest_ca->is_enabled()) {
        $new_ca = MPCA_Corporate_Account::find_corporate_account_by_obj($new_txn);
        if($new_ca && $new_ca->is_enabled()) {
          // Copy over sub accounts
          $new_ca->copy_sub_accounts_from($latest_ca);
          // Enqueues job to create or update sub account transactions
          MPCA_Sync_Transactions::sync_sub_account_transactions($new_txn);
        }
      }
    }
  }

  /**
  * Add a parent column to subscriptions
  */
  public function customize_admin_subscriptions_cols($columns, $prefix, $lifetime) {
    if($prefix === 'col_txn_') {
      $columns['col_txn_parent'] = __('Parent', 'memberpress-corporate');
    }

    return $columns;
  }

  /**
  * Render the parent profile link for sub accounts
  */
  public function customize_admin_subscriptions_table_content($column_name, $subscription, $table, $attributes) {
    if($column_name === 'col_txn_parent') {
      ?><td <?php echo $attributes; ?>><?php
      if($subscription->sub_type === 'transaction' && $subscription->id > 0) {
        $txn = new MeprTransaction($subscription->id);
        if($txn->corporate_account_id > 0) {
          $corporate_account = new MPCA_Corporate_Account($txn->corporate_account_id);
          if($corporate_account->id > 0) {
            $user = $txn->user();
            $parent = $corporate_account->user();
            if($parent != $user) {
              ?>
                <a href="<?php echo get_edit_user_link($parent->ID); ?>" title="<?php _e("View parent's profile", 'memberpress-corporate'); ?>">
                  <?php echo stripslashes($parent->user_login); ?>
                </a>
              <?php
            }
          }
        }
      }
      ?></td><?php
    }
  }
}
