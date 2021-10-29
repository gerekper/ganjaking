<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpClick
{
  /** STATIC CRUD METHODS **/
  public static function create( $ip, $browser, $referrer, $uri, $link_id, $affiliate_id, $first_click=0, $robot=0 )
  {
    global $wafp_db;
    $args = compact( 'ip', 'browser', 'referrer', 'uri', 'link_id', 'affiliate_id', 'first_click', 'robot' );
    return $wafp_db->create_record($wafp_db->clicks, $args);
  }

  public static function update( $id, $name, $description, $affiliate_id )
  {
    global $wafp_db;
    $args = compact( 'ip', 'browser', 'referrer', 'uri', 'link_id', 'affiliate_id', 'first_click', 'robot' );
    return $wafp_db->update_record($wafp_db->clicks, $id, $args);
  }

  public static function delete( $id )
  {
    global $wafp_db;

    $args = compact( 'id' );
    return $wafp_db->delete_records($wafp_db->clicks, $args);
  }

  public static function delete_by_affiliate_id($affiliate_id)
  {
    global $wafp_db;
    $args = compact( 'affiliate_id' );
    return $wafp_db->delete_records($wafp_db->clicks, $args);
  }

  public static function get_one($id)
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->get_one_record($wafp_db->clicks, $args);
  }

  public static function get_count()
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->clicks);
  }

  public static function get_count_by_affiliate_id($affiliate_id)
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->clicks, compact('affiliate_id'));
  }

  public static function get_all($order_by='', $limit='')
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->clicks, array(), $order_by, $limit);
  }

  public static function get_all_by_affiliate_id( $affiliate_id, $order_by='', $limit='' )
  {
    global $wafp_db;
    $args = compact('affiliate_id');
    return $wafp_db->get_records($wafp_db->clicks, $args, $order_by, $limit);
  }

  public static function get_all_ids_by_affiliate_id( $affiliate_id, $order_by='', $limit='' )
  {
    global $wpdb;
    $query = "SELECT id FROM {$wafp_db->clicks} WHERE affiliate_id=%d {$order_by}{$limit}";
    $query = $wpdb->prepare($query, $affiliate_id);
    return $wpdb->get_col($query);
  }

  public static function get_first_click()
  {
    global $wpdb, $wafp_db;
    $query = "SELECT *, UNIX_TIMESTAMP(created_at) created_at_ts FROM {$wafp_db->clicks} ORDER BY created_at LIMIT 1";
    return $wpdb->get_row($query);
  }

  public static function get_all_objects_by_affiliate_id( $affiliate_id, $order_by='', $limit='')
  {
    $all_records = WafpClick::get_all_by_affiliate_id($affiliate_id, $order_by, $limit);

    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = WafpClick::get_stored_object($record->id);

    return $my_objects;
  }

  public static function get_all_objects($order_by='', $limit='')
  {
    $all_records = WafpClick::get_all($order_by, $limit);

    $my_objects = array();
    foreach ($all_records as $record)
      $my_objects[] = WafpClick::get_stored_object($record->id);

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
      $my_objects[$id] = new WafpClick($id);

    return $my_objects[$id];
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
      'id' => 'cl.id',
      'ip' => 'cl.ip',
      'referrer' => 'cl.referrer',
      'created_at' => 'cl.created_at',
      'target_url' => 'COALESCE(li.target_url,cl.uri,"'.__('Unknown','affiliate-royale', 'easy-affiliate').'")',
      'affiliate_id' => 'usr.ID',
      'user_login' => 'usr.user_login'
    );

    $from = "{$wafp_db->clicks} AS cl";
    $args = array();

    //We're not filtering by affiliate yet, maybe later
    // if( is_numeric($affiliate_id) and (int)$affiliate_id > 0 )
      // $args[] = $wpdb->prepare( "cl.affiliate_id=%d", $affiliate_id );

    $joins = array( "JOIN {$wpdb->users} as usr ON cl.affiliate_id=usr.ID",
                    "LEFT JOIN {$wafp_db->links} AS li ON cl.link_id=li.id" );

    return WafpDb::list_table($cols, $from, $joins, $args, $order_by, $order, $paged, $search, $perpage);
  }

  /** INSTANCE VARIABLES & METHODS **/
  var $rec;

  public function __construct($id) {
    $this->rec = WafpClick::get_one($id);
  }
}
