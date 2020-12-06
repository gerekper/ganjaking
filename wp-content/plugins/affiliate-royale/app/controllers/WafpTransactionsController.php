<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpTransactionsController {
  public static function route() {
    if(strtolower($_SERVER['REQUEST_METHOD'])=='post') {
      if(isset($_POST['id']) and is_numeric($_POST['id']))
        self::update($_POST['id']);
      else
        self::create();
    }
    else {
      if(isset($_GET['action']) and strtolower($_GET['action'])=='new')
        self::display_new();
      elseif(isset($_GET['action']) and strtolower($_GET['action'])=='edit')
        self::display_edit($_GET['id']);
      else
        self::display_list();
    }
  }

  public static function track( $amount, $order_id, $product_id='', $user_id='', $subscription_id='', $response='', $timeout='', $delete_cookie='false', $subscription_type = 'generic' )
  {
    WafpTransaction::track($amount, $order_id, $product_id, $user_id, $subscription_id, $response, $timeout, $delete_cookie, $subscription_type );
    exit;
  }

  public static function display_list() {
    $list_table = new WafpTransactionsTable();
    $list_table->prepare_items();

    require WAFP_VIEWS_PATH . '/transactions/list.php';
  }

  public static function display_new($message='',$errors=array()) {
    global $wafp_options;
    $txn = (object)array( 'item_name' => (isset($_REQUEST['item_name'])?$_REQUEST['item_name']:''),
                          'trans_num' => ((isset($_REQUEST['trans_num']) and !empty($_REQUEST['trans_num']))?$_REQUEST['trans_num']:WafpUtils::random_string()),
                          'sale_amount' => (isset($_REQUEST['sale_amount'])?$_REQUEST['sale_amount']:0.00),
                          'refund_amount' => (isset($_REQUEST['refund_amount'])?$_REQUEST['refund_amount']:0.00) );
    $referrer = (isset($_REQUEST['referrer'])?$_REQUEST['referrer']:'');
    $customer_name = '';
    $customer_email = '';
    require WAFP_VIEWS_PATH . '/transactions/new.php';
  }

  public static function display_edit($id,$message='',$errors=array()) {
    global $wafp_options;
    $txn = WafpTransaction::get_one($id);
    $robj = new WafpUser($txn->affiliate_id);
    $referrer = $robj->get_field('user_login');
    $customer_name = $txn->cust_name;
    $customer_email = $txn->cust_email;
    $commissions = WafpCommission::get_all_by_transaction_id( $id, 'commission_level' );
    require WAFP_VIEWS_PATH . '/transactions/edit.php';
  }

  public static function validate($errors=array()) {
    if(!isset($_REQUEST['referrer']) or empty($_REQUEST['referrer']))
      $errors[] = __('Affiliate referrer must not be empty','affiliate-royale', 'easy-affiliate');

    $ex = username_exists($_REQUEST['referrer']);

    if(empty($ex))
      $errors[] = __('Affiliate referrer must be a valid, wordpress user','affiliate-royale', 'easy-affiliate');

    if(!isset($_REQUEST['item_name']) or empty($_REQUEST['item_name']) )
      $errors[] = __('Product name must not be empty','affiliate-royale', 'easy-affiliate');

    if(!isset($_REQUEST['trans_num']) or empty($_REQUEST['trans_num']))
      $errors[] = __('Unique Order ID must not be empty','affiliate-royale', 'easy-affiliate');

    if(!isset($_REQUEST['sale_amount']) or empty($_REQUEST['sale_amount']))
      $errors[] = __('Amount must not be empty','affiliate-royale', 'easy-affiliate');

    if(!preg_match('!^\d+(\.\d{2})?$!', trim($_REQUEST['sale_amount'])))
      $errors[] = __('Amount must be formatted #.##','affiliate-royale', 'easy-affiliate');

    if(!isset($_REQUEST['refund_amount']) or empty($_REQUEST['refund_amount']))
      $errors[] = __('Refund Amount must not be empty','affiliate-royale', 'easy-affiliate');

    if(!preg_match('!^\d+(\.\d{2})?$!', trim($_REQUEST['refund_amount'])))
      $errors[] = __('Refund amount must be formatted #.##','affiliate-royale', 'easy-affiliate');

    $levels = array();
    if(isset($_REQUEST['commissions']) and is_array($_REQUEST['commissions']) and !empty($_REQUEST['commissions']))
      $commissions = array_values($_REQUEST['commissions']);
    else
      $commissions = array();

    if(isset($_REQUEST['new_commissions'])) {
      $new_commissions = WafpUtils::array_invert($_REQUEST['new_commissions']);
      $commissions = array_merge( $commissions, $new_commissions );
    }

    foreach($commissions as $crec) {
      if(in_array($crec['commission_level'],$levels))
        $errors[] = __('Commission Levels within a transaction must be unique','affiliate-royale', 'easy-affiliate');

      if(isset($crec['commission_level']) and !is_numeric($crec['commission_level']))
        $errors[] = __('Commission levels must be numbers','affiliate-royale', 'easy-affiliate');

      if(isset($crec['commission_level']) and is_numeric($crec['commission_level']) and $crec['commission_level']<1)
        $errors[] = __('Commission level must be a number greater than zero','affiliate-royale', 'easy-affiliate');

      if(!isset($crec['commission_level']) or $crec['commission_level']=='')
        $errors[] = __('Commission level cannot be empty','affiliate-royale', 'easy-affiliate');

      if(!isset($crec['commission_percentage']) or empty($crec['commission_percentage']))
        $errors[] = __('Commission must not be empty','affiliate-royale', 'easy-affiliate');

      if(!preg_match('!^\d+(\.\d{2})?$!', trim($crec['commission_percentage'])))
        $errors[] = __('Commission must be formatted #.##','affiliate-royale', 'easy-affiliate');

      $levels[] = $crec['commission_level'];
    }

    return $errors;
  }

  public static function create() {
    $errors = self::validate();

    if( empty($errors) ) {
      $aff = new WafpUser();
      $aff->load_user_data_by_login( $_POST['referrer'] );

      // Force the cookie here
      $_COOKIE['wafp_click'] = $aff->get_id();

      if(!($id = WafpTransaction::track($_POST['sale_amount'], $_POST['trans_num'], $_POST['item_name'], 0, '', '', '', 'false', 'generic', $_POST['customer_name'], $_POST['customer_email'])))
        $errors[] = __('There was an error creating your affiliate transaction.','affiliate-royale', 'easy-affiliate');

      WafpTransaction::update_refund( $id, $_POST['refund_amount'] );
    }

    if(empty($errors)) {
      self::display_edit($id, __('Your transaction was created successfully','affiliate-royale', 'easy-affiliate'));
    }
    else {
      self::display_new('', $errors);
    }
  }

  public static function update($id) {
    $txn = WafpTransaction::get_one($id);
    $errors = self::validate();

    if( empty($errors) ) {
      $aff = new WafpUser();
      $aff->load_user_data_by_login( $_POST['referrer'] );

      // Force the cookie here
      $_COOKIE['wafp_click'] = $aff->get_id();

      //Update customer name and email if set
      $cust_name = (isset($_POST['customer_name']))?$_POST['customer_name']:$txn->cust_name;
      $cust_email = (isset($_POST['customer_email']))?$_POST['customer_email']:$txn->cust_email;

      WafpTransaction::update($_POST['id'],
                              $aff->get_id(),
                              $_POST['item_name'],
                              $_POST['trans_num'],
                              $_POST['sale_amount'],
                              $cust_name,
                              $cust_email );

      // Update the commissions for this record
      if(isset($_POST['commissions']) and is_array($_POST['commissions']) and !empty($_POST['commissions'])) {
        foreach($_POST['commissions'] as $cid => $crec) {
          // Ensure that the transaction level affiliate_id is updated properly for the first commission
          if( $crec['commission_level'] <= 1 ) {
            $ref = $aff;
          }
          else {
            $ref = new WafpUser();
            $ref->load_user_data_by_login( $crec['referrer'] );
          }

          $commish = WafpCommission::get_one($cid);
          if($commish) {
            WafpCommission::update( $cid,
                                    $ref->get_id(),
                                    $_POST['id'],
                                    ($crec['commission_level']-1), // stored in the database as zero based but displayed as 1 based
                                    WafpUtils::format_float($crec['commission_percentage']),
                                    WafpUtils::format_float( self::calculate_custom_commission( WafpUtils::format_float($_POST['sale_amount']), WafpUtils::format_float($crec['commission_percentage']), $crec['commission_type'] ) ),
                                    $commish->payment_id,
                                    WafpUtils::format_float($commish->correction_amount),
                                    $crec['commission_type'] );
          }
        }
      }

      // If any new commissions have been added then create those here
      if(isset($_POST['new_commissions']) and is_array($_POST['new_commissions']) and !empty($_POST['new_commissions'])) {
        $new_commissions = WafpUtils::array_invert($_POST['new_commissions']);
        foreach($new_commissions as $crec) {
          $ref = new WafpUser();
          $ref->load_user_data_by_login( $crec['referrer'] );

          // Ensure that the transaction's affiliate id matches the first commission level's
          if( $crec['commission_level'] <= 1 ) {
            $aff = $ref;
            WafpTransaction::update( $_POST['id'],
                                     $ref->get_id(),
                                     $_POST['item_name'],
                                     $_POST['trans_num'],
                                     $_POST['sale_amount'] );
          }

          WafpCommission::create( $ref->get_id(),
                                  $_POST['id'],
                                  ($crec['commission_level']-1), // stored in the database as zero based but displayed as 1 based
                                  WafpUtils::format_float($crec['commission_percentage']),
                                  WafpUtils::format_float( self::calculate_custom_commission( WafpUtils::format_float($_POST['sale_amount']), WafpUtils::format_float($crec['commission_percentage']), $crec['commission_type'] ) ),
                                  0,
                                  WafpUtils::format_float(0.00),
                                  $crec['commission_type'] );
        }
      }

      WafpTransaction::update_refund( $_POST['id'], $_POST['refund_amount'] );

      self::display_edit($id, __('Your transaction was updated successfully','affiliate-royale', 'easy-affiliate'));
    }
    else
      self::display_edit($id, '', $errors);
  }

  public static function calculate_custom_commission($sale_amount, $commission_percentage, $commission_type) {
    if($commission_type=='percentage')
      return ($sale_amount * $commission_percentage / 100);
    else
      return $commission_percentage;
  }

  /** Deprecated **/
  public static function process_update_transactions() {
    global $wafp_options;

    if (!empty($_POST['wafp-refund'])) {
      foreach( $_POST['wafp-refund'] as $affiliate_id => $value ) {
        if( $wafp_options->number_format == '#.###,##' )
          $value = str_replace(',','.',$value);
        WafpTransaction::update_refund( $affiliate_id, $value );
      }

      self::display_list();
    }
  }

  public static function delete_transaction()
  {
    if(!is_super_admin())
      die(__('You do not have access.', 'affiliate-royale', 'easy-affiliate'));

    if(!isset($_POST['id']) || empty($_POST['id']) || !is_numeric($_POST['id']))
      die(__('Could not delete transaction', 'affiliate-royale', 'easy-affiliate'));

    WafpTransaction::destroy($_POST['id']);
    die('true'); //don't localize this string
  }

  public static function delete_commission()
  {
    if(!is_super_admin())
      die(__('You do not have access.', 'affiliate-royale', 'easy-affiliate'));

    if(!isset($_POST['id']) || empty($_POST['id']) || !is_numeric($_POST['id']))
      die(__('Could not delete commission', 'affiliate-royale', 'easy-affiliate'));

    WafpCommission::delete($_POST['id']);
    die('true'); //don't localize this string
  }
} //End class
