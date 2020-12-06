<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpTransaction
{
  /** STATIC CRUD METHODS **/
  public static function create( $item_name, $sale_amount, $commission_amount, $trans_num, $type, $status, $response, $affiliate_id, $cust_name, $cust_email, $ip_addr, $commission_percentage, $subscr_id=0, $subscr_paynum=0, $commission_type='' )
  {
    global $wafp_db, $wafp_options;
    if(empty($commission_type) and !empty($affiliate_id) and is_numeric($affiliate_id) ) {
      $aff = new WafpUser($affiliate_id);
      $commission_type = $aff->get_commission_type();
    }
    else if(empty($commission_type)) { $commission_type = $wafp_options->commission_type; }

    $sale_amount = WafpUtils::format_float($sale_amount);
    $commission_amount = WafpUtils::format_float($commission_amount);
    $commission_percentage = WafpUtils::format_float($commission_percentage);
    $args = compact( 'item_name', 'sale_amount', 'commission_amount', 'trans_num', 'type', 'status', 'response', 'affiliate_id', 'ip_addr', 'commission_percentage', 'cust_name', 'cust_email', 'subscr_id', 'subscr_paynum', 'commission_type' );
    $transaction_id = $wafp_db->create_record($wafp_db->transactions, $args);

    //Something broke
    if(!$transaction_id) { return; }

    if(!empty($affiliate_id) and is_numeric($affiliate_id) and number_format($commission_amount, 2) != '0.00') {
      $affiliate = new WafpUser($affiliate_id);
      $affiliates = $affiliate->get_ancestors(true);

      // Record commission for each affiliate who's getting some
      foreach($affiliates as $level => $aff)
      {
        $curr_percentage = ( $aff->is_affiliate() ? WafpUtils::format_float($aff->get_commission_percentage($level)) : 0.00 );
        $curr_amount = ( $aff->is_affiliate() ? WafpUtils::format_float($aff->calculate_commission($sale_amount, $level, $item_name)) : 0.00 );

        if((float)$curr_percentage > 0.00) {
          WafpCommission::create( $aff->get_id(), $transaction_id, $level, $curr_percentage, $curr_amount );
        }
      }

      $trans_type = $type;
      $transaction_type = (empty($subscr_id)?'Payment':'Subscription Payment');
      $payment_status = $status;
      $remote_ip_addr = $ip_addr;
      $payment_amount = $sale_amount;
      $customer_name = $cust_name;
      $customer_email = $cust_email;

      $params = compact('item_name', 'trans_num', 'trans_type', 'payment_status',
                        'remote_ip_addr', 'response', 'payment_amount', 'customer_name',
                        'customer_email', 'transaction_type');

      WafpUtils::send_admin_sale_notification( $params, $affiliates ); //Make sure item_name is in params
      WafpUtils::send_affiliate_sale_notifications( $params, $affiliates ); //Make sure item_name is in params
    }

    return $transaction_id;
  }

  public static function update($id, $affiliate_id, $item_name,
                                $trans_num, $sale_amount, $cust_name = '',
                                $cust_email = '', $commission_amount = null,
                                $commission_percentage = null,
                                $subscr_id = null, $type = 'commission',
                                $status = 'complete'
                         ) {
    global $wafp_db, $wafp_options;
    $sale_amount = WafpUtils::format_float($sale_amount);
    $commission_type = '';

    if(!empty($affiliate_id) && is_numeric($affiliate_id) ) {
      $aff = new WafpUser($affiliate_id);
      $commission_type = $aff->get_commission_type();
    }

    if(empty($commission_type)) {
      $commission_type = $wafp_options->commission_type;
    }

    $args = compact('affiliate_id', 'item_name', 'trans_num', 'sale_amount', 'type', 'status', 'commission_type');

    if(!empty($cust_name)) {
      $args['cust_name'] = $cust_name;
    }
    if(!empty($cust_email)) {
      $args['cust_email'] = $cust_email;
    }

    if(!empty($affiliate_id) && is_numeric($affiliate_id) && number_format($commission_amount, 2) != '0.00') {
      $affiliate = new WafpUser($affiliate_id);
      $affiliates = $affiliate->get_ancestors(true);

      // Record commission for each affiliate who's getting some
      foreach($affiliates as $level => $aff) {
        $curr_percentage = ( $aff->is_affiliate() ? WafpUtils::format_float($aff->get_commission_percentage($level)) : 0.00 );
        $curr_amount = ( $aff->is_affiliate() ? WafpUtils::format_float($aff->calculate_commission($sale_amount, $level, $item_name)) : 0.00 );

        if((float)$curr_percentage > 0.00) {
          WafpCommission::create( $aff->get_id(), $id, $level, $curr_percentage, $curr_amount );
        }
      }

      $trans_type = $type;
      $transaction_type = (empty($subscr_id)?'Payment':'Subscription Payment');
      $payment_status = $status;
      $remote_ip_addr = $_SERVER['REMOTE_ADDR'];
      $payment_amount = $sale_amount;
      $customer_name = $cust_name;
      $customer_email = $cust_email;

      $params = compact('item_name', 'trans_num', 'trans_type', 'payment_status',
                        'remote_ip_addr', 'response', 'payment_amount', 'customer_name',
                        'customer_email', 'transaction_type');

      WafpUtils::send_admin_sale_notification( $params, $affiliates ); //Make sure item_name is in params
      WafpUtils::send_affiliate_sale_notifications( $params, $affiliates ); //Make sure item_name is in params
    }

    return $wafp_db->update_record($wafp_db->transactions, $id, $args);
  }

  public static function update_refund( $id, $refund_amount, $correction_amount="" )
  {
    global $wafp_db;

    //make sure we can't enter a negative or non-numeric value
    if($refund_amount <= 0 || !is_numeric($refund_amount))
      $refund_amount = 0;

    //$correction_amount var doesn't seem to actually get used here ???
    if(!isset($correction_amount) or empty($correction_amount))
    {
      $record = WafpTransaction::get_one($id);

      $correction_amount = 0.00;

      if($record) {
        if($record->commission_type=='percentage')
          $correction_amount = WafpUtils::format_float( (float)$refund_amount * ( (float)$record->commission_percentage / 100.0 ) );
        else if($record->commission_type=='fixed' and $refund_amount > 0) {
          // Just void full commission
          $correction_amount = WafpUtils::format_float($record->commission_percentage);
        }
      }
    }

    $commissions = WafpCommission::get_all_by_transaction_id($id);
    $refund_amount = WafpUtils::format_float($refund_amount);

    foreach($commissions as $commission)
      WafpCommission::update_refund( $commission->id, $refund_amount );

    $args = compact( 'refund_amount', 'correction_amount' );
    return $wafp_db->update_record($wafp_db->transactions, $id, $args);
  }

  //Deletes this txn and it's associated commissions
  public static function destroy($id)
  {
    $commissions = WafpCommission::get_all_by_transaction_id($id);

    if(!empty($commissions))
      foreach($commissions as $commission)
        WafpCommission::delete($commission->id);

    self::delete($id);
  }

  public static function delete( $id )
  {
    global $wafp_db;

    $args = compact( 'id' );
    return $wafp_db->delete_records($wafp_db->transactions, $args);
  }

  public static function delete_by_affiliate_id($affiliate_id)
  {
    global $wafp_db;
    $args = compact( 'affiliate_id' );
    return $wafp_db->delete_records($wafp_db->transactions, $args);
  }

  public static function get_one($id)
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->get_one_record($wafp_db->transactions, $args);
  }

  public static function get_one_by_trans_num($trans_num)
  {
    global $wafp_db, $wpdb;
    $q = "SELECT *
            FROM {$wafp_db->transactions}
            WHERE `trans_num` = %s
            LIMIT 1";

    return $wpdb->get_row($wpdb->prepare($q, $trans_num));
  }

  public static function get_one_by_subscription_id($subscr_id)
  {
    global $wafp_db;

    if(is_null($subscr_id) or empty($subscr_id) or !$subscr_id)
      return false;

    $args = compact( 'subscr_id' );
    return $wafp_db->get_one_record($wafp_db->transactions, $args);
  }

  public static function get_all_by_subscription_id($subscr_id)
  {
    global $wafp_db;

    if(is_null($subscr_id) or empty($subscr_id) or !$subscr_id)
      return false;

    $args = compact('subscr_id');
    return $wafp_db->get_records($wafp_db->transactions, $args);
  }

  public static function get_count($search='')
  {
    global $wafp_db, $wpdb;
    $join = '';
    $where = '';

    if(!empty($search)) {
      $join  = " INNER JOIN {$wpdb->users} aff ON tr.affiliate_id=aff.id";
      $where = " AND ( aff.user_login LIKE '%{$search}%'" .
                      " OR tr.trans_num LIKE '%{$search}%'" .
                      " OR tr.sale_amount LIKE '%{$search}%'" .
                      " OR tr.refund_amount LIKE '%{$search}%'" .
                      " OR tr.status LIKE '%{$search}%'" .
                      " OR tr.created_at LIKE '%{$search}%' )";
    }

    $query = "SELECT COUNT(*) FROM {$wafp_db->transactions} tr{$join} WHERE tr.type='commission'{$where}";
    return $wpdb->get_var($query);
  }

  public static function get_count_by_affiliate_id($affiliate_id)
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->transactions, array('affiliate_id' => $affiliate_id, 'type' => 'commission'));
  }

  public static function get_all($order_by='', $limit='')
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->transactions, array('type' => 'commission'), $order_by, $limit);
  }

  public static function get_all_by_affiliate_id( $affiliate_id, $order_by='', $limit='' )
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->transactions, array('affiliate_id' => $affiliate_id, 'type' => 'commission'), $order_by, $limit);
  }

  public static function get_all_ids_by_affiliate_id( $affiliate_id, $order_by='', $limit='' )
  {
    global $wpdb;
    $query = "SELECT id FROM {$wafp_db->transactions} WHERE type='commission' AND affiliate_id=%d {$order_by}{$limit}";
    $query = $wpdb->prepare($query, $affiliate_id);
    return $wpdb->get_col($query);
  }

  public static function get_all_objects_by_affiliate_id( $affiliate_id, $order_by='', $limit='')
  {
    $all_records = WafpTransaction::get_all_by_affiliate_id($affiliate_id, $order_by, $limit);

    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = WafpTransaction::get_stored_object($record->id);

    return $my_objects;
  }

  public static function get_all_objects($order_by='', $limit='')
  {
    $all_records = WafpTransaction::get_all($order_by, $limit);

    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = WafpTransaction::get_stored_object($record->id);

    return $my_objects;
  }

  public static function get_stored_object($id)
  {
    static $my_objects;

    if( !isset($my_objects) )
      $my_objects = array();

    if( !isset($my_objects[$id]) or
        empty($my_objects[$id]) or
        !is_object($my_objects[$id]) )
      $my_objects[$id] = new WafpTransaction($id);

    return $my_objects[$id];
  }

  public static function get_num_trans_by_subscr_id($subscr_id) {
    global $wpdb, $wafp_db;

    $sql = "SELECT COUNT(*) {$wafp_db->transactions} WHERE subscr_id=%d";
    $sql = $wpdb->prepare($sql, $subscr_id);

    return $wpdb->get_var($sql);
  }

  public static function track( $amount,
                                $order_id,
                                $product_id='',
                                $user_id=0,
                                $subscription_id='',
                                $response='',
                                $timeout='',
                                $delete_cookie='false',
                                $subscription_type = 'generic',
                                $customer_name = '',
                                $customer_email = '' ) {
    global $wafp_options, $user_ID;

    $transaction_id = false; // by default this is false
    $recurring_purchase = false;

    $affiliate_id = isset($_COOKIE['wafp_click'])?$_COOKIE['wafp_click']:false;
    $wafp_subscr_id = 0;

    // Create a subscription if it's set
    if(!empty($affiliate_id) && $affiliate_id && !empty($subscription_id) && $subscription_id) {
      if(!($wafp_subscr = WafpSubscription::subscription_exists($subscription_id))) {
        $wafp_subscr_id = WafpSubscription::create( $subscription_id, $subscription_type, $affiliate_id, $product_id, $_SERVER['REMOTE_ADDR'] );
        $recurring_purchase = false; // This is the first purchase of a subscription
      }
      else {
        $wafp_subscr_id = $wafp_subscr->subscription->ID;
        $recurring_purchase = true; // This not is the first purchase of a subscription
      }
    }
    else if( !empty( $subscription_id ) && $subscription_id &&
             ( $wafp_subscr = WafpSubscription::subscription_exists( $subscription_id ) ) ) {
      // If we don't have the affiliate id yet, let's try
      // to determine it from the subscription object
      if($wafp_subscr->affiliate_id && is_numeric($wafp_subscr->affiliate_id)) {
        $affiliate_id = $wafp_subscr->affiliate_id;
      }

      $wafp_subscr_id = $wafp_subscr->subscription->ID;
      $recurring_purchase = true;
    }

    //need an amount
    if(is_null($amount) or empty($amount)) {
      return;
    }

    //need an order_id/trans_num
    if(is_null($order_id) or empty($order_id)) {
      return;
    }

    //If an admin is trying to manually enter a sale but it's been recorded as a no_commission already - we should just update it
    $update_existing = false;
    $existing_transaction = WafpTransaction::get_one_by_trans_num($order_id);
    if($existing_transaction && $existing_transaction->type == 'no_commission') {
      $update_existing = true;
    }
    elseif($existing_transaction) {
      return; //Don't update a transaction that already has commission
    }

    // Override affiliate id with stored affiliate id or store the
    // affiliate_id with the usermeta if no stored meta is found
    if($user_id) {
      $wuser = new WafpUser($user_id);

      //TODO: Move this get_user_meta to the WafpUser object
      $stored_aff_id = $wuser->get_referrer();

      // if get_usermeta returned something -- if not attempt to store it from cookie
      if($stored_aff_id) {
        $affiliate_id = $stored_aff_id;
      }
      else {
        if($affiliate_id && is_numeric($affiliate_id)) {
          $wuser->set_referrer($affiliate_id);
          $wuser->store();
        }
      }
    }

    // Short circuit this and don't pay commissions if the user who purchased is also the affilaite
    WafpUtils::get_currentuserinfo(); //Load the $user_ID WP global if it's not already set
    if(($user_ID && $user_ID == $affiliate_id && !is_super_admin()) || ($user_id && $user_id == $affiliate_id)) {
      unset($affiliate_id);
    }

    // Try this again now that we've got an affiliate_id
    if(!$wafp_subscr_id && !empty( $subscription_id ) && $subscription_id && !empty($affiliate_id) && $affiliate_id) {
      // let's create the subscription and say this is the first transaction?
      $wafp_subscr_id = WafpSubscription::create($subscription_id, $subscription_type, $affiliate_id, $product_id, $_SERVER['REMOTE_ADDR']);
      $recurring_purchase = false;
    }

    if(isset($affiliate_id) && !empty($affiliate_id) && is_numeric($affiliate_id)) {
      $affiliate = new WafpUser($affiliate_id);
    }

    // If there's a timeout value set then make sure we know its set
    // Timeouts are here to prevent users from refreshing the page and having commissions tracked
    $timeout_active = false;
    if(!empty($timeout) && is_numeric($timeout)) {
      $timeout_active = (isset($_COOKIE['wafp_timeout']));
    }

    if($user_id && is_numeric($user_id)) {
      $customer = new WafpUser($user_id);
    }

    if(isset($customer)) {
      $customer_name = $customer->get_full_name();
      $customer_email = $customer->get_field('user_email');
    }

    $wafp_subscr_paynum = ((!$wafp_subscr_id)?0:1);

    if(isset($affiliate) && is_a($affiliate, 'WafpUser')) {
      $commission_percentage = WafpUtils::format_float($affiliate->get_commission_percentages_total(true));
      $commission_amount = WafpUtils::format_float($affiliate->calculate_commissions_total($amount, true, false, $product_id));
    }

    if(!$update_existing && isset($affiliate) && is_a($affiliate, 'WafpUser') && $affiliate->pay_commission($recurring_purchase) && !$timeout_active) {
      if(number_format($commission_amount,2) != '0.00') { // Accept positive or negative amount
        $transaction_id = WafpTransaction::create($product_id,
                                                  $amount,
                                                  $commission_amount,
                                                  $order_id,
                                                  'commission',
                                                  'complete',
                                                  $response,
                                                  $affiliate_id,
                                                  $customer_name,
                                                  $customer_email,
                                                  $_SERVER['REMOTE_ADDR'],
                                                  $commission_percentage,
                                                  $wafp_subscr_id,
                                                  $wafp_subscr_paynum,
                                                  $affiliate->get_commission_type());

        if($delete_cookie == 'true') {
          setcookie('wafp_click', '', time()-60*60, '/');
        }
        elseif(!empty($timeout) and is_numeric($timeout)) {
          setcookie('wafp_timeout', '1', (time() + $timeout), '/');
        }
      }
      else {
        $transaction_id = WafpTransaction::create($product_id,
                                                  $amount,
                                                  '0.00',
                                                  $order_id,
                                                  'no_commission',
                                                  'complete',
                                                  $response,
                                                  $affiliate_id,
                                                  $customer_name,
                                                  $customer_email,
                                                  $_SERVER['REMOTE_ADDR'],
                                                  '0.0',
                                                  $wafp_subscr_id,
                                                  $wafp_subscr_paynum,
                                                  $wafp_options->commission_type);
      }
    }
    elseif($update_existing && isset($affiliate) && is_a($affiliate, 'WafpUser')) {
      WafpTransaction::update($existing_transaction->id, $affiliate_id, $product_id, $order_id, $amount, $customer_name, $customer_email, $commission_amount, $commission_percentage, $wafp_subscr_id);
      $transaction_id = $existing_transaction->id;
    }
    elseif(!$update_existing) {
      $transaction_id = WafpTransaction::create($product_id,
                                                $amount,
                                                '0.00',
                                                $order_id,
                                                'no_commission',
                                                'complete',
                                                $response,
                                                '', '', '',
                                                $_SERVER['REMOTE_ADDR'],
                                                '0.0',
                                                $wafp_subscr_id,
                                                $wafp_subscr_paynum,
                                                $wafp_options->commission_type);
    }

    return $transaction_id;
  }

  //$type not supported yet, but can be count or sum
  public static function get_leaderboard_data($limit, $days, $type = 'count')
  {
    global $wpdb, $wafp_db;

    $limit_date = '';
    if($days) {
      $limit_date = date('c', (time() - ($days * 24 * 60 * 60)));
      $limit_date = "AND created_at >= '{$limit_date}'";
    }

    if($limit)
      $limit = "LIMIT {$limit}";
    else
      $limit = '';

    $q = "SELECT affiliate_id, COUNT(id) AS total
            FROM {$wafp_db->transactions}
            WHERE type = 'commission'
              AND status IN ('complete', 'Completed')
              AND correction_amount <= 0.00
              {$limit_date}
          GROUP BY affiliate_id
          ORDER BY COUNT(id) DESC
          {$limit}";

    return $wpdb->get_results($q);
  }

  public static function list_table( $order_by='',
                                     $order='',
                                     $paged='',
                                     $search='',
                                     $perpage=10 ) {
    global $wafp_db, $wpdb, $wafp_options;

    $year = date('Y');
    $month = date('m');

    $cols = array(
      'id' => 'tr.id',
      'created_at' => 'tr.created_at',
      'user_login' => 'aff.user_login',
      'affiliate_id' => 'aff.ID',
      'trans_num' => 'tr.trans_num',
      'item_name' => 'tr.item_name',
      'sale_amount' => 'tr.sale_amount',
      'refund_amount' => 'tr.refund_amount',
      'commission_amount' => "(SELECT SUM(cm.commission_amount) - SUM(cm.correction_amount) FROM {$wafp_db->commissions} AS cm WHERE cm.transaction_id=tr.id)",
      'total_amount' => '(tr.sale_amount - tr.refund_amount)',
      'referring_page' => "(SELECT cl.referrer FROM {$wafp_db->clicks} cl WHERE cl.affiliate_id=tr.affiliate_id AND cl.ip=tr.ip_addr ORDER BY cl.referrer DESC, cl.created_at DESC LIMIT 1)"
    );

    $args = array( "tr.type='commission'" );

    $joins = array( "INNER JOIN {$wpdb->users} aff ON tr.affiliate_id=aff.id" );

    return WafpDb::list_table($cols, "{$wafp_db->transactions} AS tr", $joins, $args, $order_by, $order, $paged, $search, $perpage);
  }

  /** INSTANCE VARIABLES & METHODS **/
  public $rec;

  public function __construct($id) {
    $this->rec = WafpTransaction::get_one($id);
  }
}
