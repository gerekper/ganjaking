<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpCommission
{
  /** STATIC CRUD METHODS **/
  public static function create( $affiliate_id, $transaction_id, $commission_level, $commission_percentage, $commission_amount, $payment_id=0, $correction_amount=0.00, $commission_type='' )
  {
    global $wafp_db, $wafp_options;
    if(empty($commission_type) and !empty($affiliate_id) and is_numeric($affiliate_id) ) {
      $aff = new WafpUser($affiliate_id);
      $commission_type = $aff->get_commission_type();
    }
    else if(empty($commission_type)) { $commission_type = $wafp_options->commission_type; }

    $commission_percentage = WafpUtils::format_float($commission_percentage);
    $commission_amount = WafpUtils::format_float($commission_amount);
    $correction_amount = WafpUtils::format_float($correction_amount);
    $args = compact( 'affiliate_id', 'transaction_id', 'commission_level', 'commission_percentage', 'commission_amount', 'payment_id', 'correction_amount', 'commission_type' );

    return $wafp_db->create_record($wafp_db->commissions, $args);
  }

  public static function update( $id, $affiliate_id, $transaction_id, $commission_level, $commission_percentage, $commission_amount, $payment_id=0, $correction_amount=0.00, $commission_type='' )
  {
    global $wafp_db;
    $commission_percentage = WafpUtils::format_float($commission_percentage);
    $commission_amount = WafpUtils::format_float($commission_amount);
    $correction_amount = WafpUtils::format_float($correction_amount);
    $args = compact( 'affiliate_id', 'transaction_id', 'commission_level', 'commission_percentage', 'commission_amount', 'payment_id', 'correction_amount' );
    if(!empty($commission_type)) { $args['commission_type']=$commission_type; }
    return $wafp_db->update_record($wafp_db->commissions, $id, $args);
  }

  public static function update_refund( $id, $refund_amount, $correction_amount="" )
  {
    global $wafp_db;

    if(!isset($correction_amount) or empty($correction_amount))
    {
      $record = self::get_one($id);

      $correction_amount=0.00;

      if($record) {
        if($record->commission_type=='percentage')
          $correction_amount = WafpUtils::format_float( (float)$refund_amount * ( (float)$record->commission_percentage / 100.0 ) );
        else if($record->commission_type=='fixed' and $refund_amount > 0)
          $correction_amount = WafpUtils::format_float($record->commission_percentage); // Just void full commission
      }
    }

    $refund_amount = WafpUtils::format_float($refund_amount);

    $args = compact( 'correction_amount' );
    return $wafp_db->update_record($wafp_db->commissions, $id, $args);
  }

  public static function delete( $id )
  {
    global $wafp_db;

    $args = compact( 'id' );
    return $wafp_db->delete_records($wafp_db->commissions, $args);
  }

  public static function get_one($id)
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->get_one_record($wafp_db->commissions, $args);
  }

  public static function get_all_by_affiliate_id($affiliate_id, $order_by='', $limit='')
  {
    global $wafp_db;
    $args = compact( 'affiliate_id' );
    return $wafp_db->get_records($wafp_db->commissions, $args, $order_by, $limit);
  }

  public static function get_all_by_transaction_id($transaction_id, $order_by='', $limit='')
  {
    global $wafp_db;
    $args = compact( 'transaction_id' );
    return $wafp_db->get_records($wafp_db->commissions, $args, $order_by, $limit);
  }

  public static function get_count()
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->commissions);
  }

  public static function get_all($order_by='', $limit='')
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->commissions, array(), $order_by, $limit);
  }

  public static function get_all_objects_by_affiliate_id( $affiliate_id, $order_by='', $limit='')
  {
    $all_records = self::get_all_by_affiliate_id($affiliate_id, $order_by, $limit);

    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = self::get_stored_object($record->id);

    return $my_objects;
  }

  public static function get_all_objects($order_by='', $limit='')
  {
    $all_records = self::get_all($order_by, $limit);

    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = self::get_stored_object($record->id);

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
      $my_objects[$id] = new WafpCommission($id);

    return $my_objects[$id];
  }

  /** INSTANCE VARIABLES & METHODS **/
  public $rec;

  public function __construct($id)
  {
    $this->rec = self::get_one($id);
  }
}
