<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpPayment
{
  /** STATIC CRUD METHODS **/
  public static function create( $affiliate_id, $amount )
  {
    global $wafp_db;
    $amount = WafpUtils::format_float($amount);
    $args = compact( 'affiliate_id', 'amount' );
    return $wafp_db->create_record($wafp_db->payments, $args);
  }

  public static function update( $id, $affiliate_id, $amount )
  {
    global $wafp_db;
    $amount = WafpUtils::format_float($amount);
    $args = compact( 'affiliate_id', 'amount' );
    return $wafp_db->update_record($wafp_db->payments, $id, $args);
  }

  public static function update_transactions($payment_id, $affiliate_id, $period)
  {
    global $wpdb, $wafp_db;

    $num_days_in_month = (int)(date( 't', $period )) - 1;
    $seconds_in_month  = 60*60*24*(int)$num_days_in_month;

    $day_start = date( 'Y-m-d 00:00:00', $period );
    $day_end   = date( 'Y-m-d 23:59:59', ( $period + $seconds_in_month ) );

    $query_str = "UPDATE {$wafp_db->commissions} SET payment_id=%d WHERE affiliate_id=%d AND payment_id=0 AND created_at <= %s";
    $query = $wpdb->prepare( $query_str, $payment_id, $affiliate_id, $day_end );

    return $wpdb->query($query);
  }

  public static function delete( $id )
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->delete_records($wafp_db->payments, $args);
  }

  public static function delete_by_affiliate_id($affiliate_id)
  {
    global $wafp_db;
    $args = compact( 'affiliate_id' );
    return $wafp_db->delete_records($wafp_db->payments, $args);
  }

  public static function get_one($id)
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->get_one_record($wafp_db->payments, $args);
  }

  public static function get_count($id)
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->payments);
  }

  public static function get_count_by_affiliate_id($affiliate_id)
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->payments, compact('affiliate_id'));
  }

  public static function get_all($order_by='', $limit='')
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->payments, array(), $order_by, $limit);
  }

  public static function get_all_by_affiliate_id( $affiliate_id, $order_by='', $limit='' )
  {
    global $wafp_db;
    $args = compact('affiliate_id');
    return $wafp_db->get_records($wafp_db->payments, $args, $order_by, $limit);
  }

  public static function get_all_ids_by_affiliate_id( $affiliate_id, $order_by='', $limit='' )
  {
    global $wpdb;
    $query = "SELECT id FROM {$wafp_db->payments} WHERE affiliate_id=%d {$order_by}{$limit}";
    $query = $wpdb->prepare($query, $affiliate_id);
    return $wpdb->get_col($query);
  }

  public static function get_all_objects_by_affiliate_id( $affiliate_id, $order_by='', $limit='')
  {
    $all_records = WafpPayment::get_all_by_affiliate_id($affiliate_id, $order_by, $limit);

    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = WafpPayment::get_stored_object($record->id);

    return $my_objects;
  }

  public static function get_all_objects($order_by='', $limit='')
  {
    $all_records = WafpPayment::get_all($order_by, $limit);

    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = WafpPayment::get_stored_object($record->id);

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
      $my_objects[$id] = new WafpPayment($id);

    return $my_objects[$id];
  }

  public static function affiliates_owed( $period )
  {
    global $wafp_db, $wpdb;

    $num_days_in_month = (int)(date( 't', $period )) - 1;
    $seconds_in_month  = 60*60*24*(int)$num_days_in_month;

    $day_start = date( 'Y-m-d 00:00:00', $period );
    $day_end   = date( 'Y-m-d 23:59:59', ( $period + $seconds_in_month ) );

    $query_str = "SELECT aff.ID AS aff_id,
                    aff.user_login AS aff_login,
                    (SELECT COUNT( * ) FROM {$wafp_db->commissions} WHERE affiliate_id=aff.ID AND created_at <= %s) AS transaction_count,
                    (SELECT SUM( commission_amount ) FROM {$wafp_db->commissions} WHERE affiliate_id=aff.ID AND created_at <= %s) AS commission_amount,
                    (SELECT SUM( correction_amount ) FROM {$wafp_db->commissions} WHERE affiliate_id=aff.ID AND created_at <= %s) AS correction_amount,
                    (SELECT SUM( amount ) FROM {$wafp_db->payments} WHERE affiliate_id=aff.ID) AS payment_amount
                    FROM {$wpdb->users} aff
                    ORDER BY commission_amount DESC, aff_login";

    $query = $wpdb->prepare( $query_str, $day_end, $day_end, $day_end );

    $results_array = $wpdb->get_results($query);

    $totals = array();
    $results = array();
    foreach( $results_array as $result )
    {
      $total_amount = ($result->commission_amount - $result->correction_amount - $result->payment_amount);

      // if it doesn't round up to one cent then it's zero
      if((float)$total_amount >= 0.01)
      {
        $totals{"{$result->aff_id}"} = $total_amount;
        $results[$result->aff_id] = $result;
      }
    }

    arsort($totals); //changed from $totals_hash
    return compact( 'totals', 'results' );
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
      'id' => 'pm.id',
      'affiliate_id' => 'pm.affiliate_id',
      'affiliate' => 'u.user_login',
      'created_at' => 'pm.created_at',
      'amount' => 'pm.amount'
    );
    $from = "{$wafp_db->payments} AS pm";
    $args = array();
    $joins = array("JOIN {$wpdb->users} AS u ON u.ID=pm.affiliate_id");

    return WafpDb::list_table($cols, $from, $joins, $args, $order_by, $order, $paged, $search, $perpage);
  }

  /** INSTANCE VARIABLES & METHODS **/
  public $rec;

  public function __construct($id) {
    $this->rec = WafpPayment::get_one($id);
  }
}
