<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpResponse
{
  public static function create( $response, $type, $status='pending' )
  {
    global $wafp_db;
    $args = compact( 'response', 'type', 'status' );
    return $wafp_db->create_record($wafp_db->responses, $args, false, false, true);
  }

  public static function update( $id, $response, $type, $status='pending' )
  {
    global $wafp_db;
    $args = compact( 'response', 'type', 'status' );
    return $wafp_db->update_record($wafp_db->responses, $id, $args);
  }

  public static function update_status( $id, $status )
  {
    global $wafp_db;
    $args = compact( 'status' );
    return $wafp_db->update_record($wafp_db->responses, $id, $args);
  }

  public static function delete( $id )
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->delete_records($wafp_db->responses, $args);
  }

  public static function get_one($id)
  {
    global $wafp_db;
    $args = compact( 'id' );
    return $wafp_db->get_one_record($wafp_db->responses, $args);
  }

  public static function get_count($id)
  {
    global $wafp_db;
    return $wafp_db->get_count($wafp_db->responses);
  }

  public static function get_all($order_by='', $limit='')
  {
    global $wafp_db;
    return $wafp_db->get_records($wafp_db->responses, array(), $order_by, $limit);
  }

  public static function get_all_by_status_and_ts( $status, $ts )
  {
    global $wpdb, $wafp_db;
    $query = "SELECT r.* FROM {$wafp_db->responses} r WHERE r.status=%s AND r.created_ts <= %d ORDER BY r.created_ts DESC";
    $query = $wpdb->prepare($query, $status, $ts);
    return $wpdb->get_results($query);
  }
}
