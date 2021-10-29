<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpPaymentsController {
  public static function route() {
    if(isset($_REQUEST['page']) and $_REQUEST['page'] == 'affiliate-royale-pay-affiliates') {
      if(isset($_POST['wafp-update-payments']) and $_POST['wafp-update-payments'] == 'Y')
        self::process_update_payments();
      else
        self::admin_affiliates_owed();
    }
    else if(isset($_REQUEST['page']) and $_REQUEST['page'] == 'affiliate-royale-payments')
      self::display_list();
  }

  public static function admin_affiliates_owed($period='current')
  {
    global $wafp_options;

    if( $period=='current' or empty($period) )
      $period = mktime(0, 0, 0, date('n'), 1, date('Y'));

    $payments = WafpPayment::affiliates_owed( $period );

    extract($payments);

    require( WAFP_VIEWS_PATH . "/payments/owed.php" );
  }


  public static function process_update_payments()
  {
    if (!empty($_POST['wafp-payment-paid'])) //Paul added this fix
    {
      $payment_ids = array();
      foreach( $_POST['wafp-payment-paid'] as $affiliate_id => $value )
      {
        $payment_id = WafpPayment::create( $affiliate_id, $_POST['wafp-payment-amount'][$affiliate_id] );
        WafpPayment::update_transactions( $payment_id, $affiliate_id, $_POST['wafp-period'] );

        if( !empty($payment_id) and $payment_id )
          $payment_ids[] = $payment_id;
      }

      $payment_ids = implode(',', $payment_ids);

      self::admin_affiliate_payment_receipt($payment_ids);
    }
    else
      self::admin_affiliates_owed();
  }

  public static function admin_affiliate_payment_receipt($payment_ids=null)
  {
    global $wafp_options;

    require( WAFP_VIEWS_PATH . "/payments/receipt.php" );
  }

  public static function admin_paypal_bulk_file($payment_id)
  {
    global $wafp_options, $wafp_blogname;

    $bulk_totals = WafpReport::affiliate_paypal_bulk_file_totals($payment_id);

    require( WAFP_VIEWS_PATH . "/payments/paypal_bulk_file.php" );
  }

  public static function display_list() {
    $list_table = new WafpPaymentsTable();
    $list_table->prepare_items();

    require WAFP_VIEWS_PATH . '/payments/list.php';
  }
}
