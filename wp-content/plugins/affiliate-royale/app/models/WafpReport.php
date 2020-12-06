<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpReport
{
  public static function affiliate_stats($period, $affiliate_id=0)
  {
    global $wafp_db, $wpdb;

    $num_days_in_month = date( 't', $period );
    $year = date('Y',$period);
    $month = date('n',$period);

    $stats = array();

    if( (int)$affiliate_id <= 0 )
      $aff_where = "";
    else
      $aff_where = "AND affiliate_id=%d ";

    $query_str = "SELECT %s as date,
                         %s as tsdate,
                    ( SELECT COUNT(*)
                        FROM {$wafp_db->clicks}
                       WHERE created_at >= %s AND
                             created_at <= %s {$aff_where}) as clicks,
                    ( SELECT COUNT(*)
                        FROM {$wafp_db->clicks}
                       WHERE first_click <> 0 AND
                             created_at >= %s AND
                             created_at <= %s {$aff_where}) as uniques,
                    ( SELECT COUNT(*)
                        FROM {$wafp_db->transactions}
                       WHERE type='commission' AND created_at >= %s AND
                             created_at <= %s {$aff_where}) as transactions,
                    ( SELECT SUM(commission_amount)
                        FROM {$wafp_db->commissions}
                       WHERE created_at >= %s AND created_at <= %s {$aff_where}) as commissions,
                    ( SELECT SUM(correction_amount)
                        FROM {$wafp_db->commissions}
                       WHERE created_at >= %s AND created_at <= %s {$aff_where}) as corrections";

    for($i = 1; $i <= $num_days_in_month; $i++ )
    {
      $day = mktime(0,0,0,$month,$i,$year);
      $day_start = sprintf("%4d-%02d-%02d 00:00:00", $year, $month, $i);
      $day_end   = sprintf("%4d-%02d-%02d 23:59:59", $year, $month, $i);

      if( (int)$affiliate_id <= 0 )
        $query = $wpdb->prepare( $query_str, date('m/d/Y', $day), $day, $day_start, $day_end, $day_start, $day_end, $day_start, $day_end, $day_start, $day_end, $day_start, $day_end );
      else
        $query = $wpdb->prepare( $query_str, date('m/d/Y', $day), $day, $day_start, $day_end, $affiliate_id, $day_start, $day_end, $affiliate_id, $day_start, $day_end, $affiliate_id, $day_start, $day_end, $affiliate_id, $day_start, $day_end, $affiliate_id );

      $stats[$i] = $wpdb->get_row($query);
    }

    return $stats;
  }

  public static function last_n_days_stats($n=7)
  {
    global $wafp_db, $wpdb;

    $stats = array();

    for($i=($n-1); $i>=0; $i--)
    {
      // Take the time out of the equation
      $start_time = mktime(  0,  0,  0 ) - 60*60*24*$i;
      $end_time   = mktime( 23, 59, 59 ) - 60*60*24*$i;

      $start_time_str = date('Y-m-d H:i:s', $start_time);
      $end_time_str   = date('Y-m-d H:i:s', $end_time);

      $query_str = "SELECT %s as rdate,
                           ( SELECT COUNT(*)
                               FROM {$wafp_db->clicks}
                              WHERE created_at >= '{$start_time_str}' AND
                                    created_at <= '{$end_time_str}' ) as clicks,
                           ( SELECT COUNT(*)
                               FROM {$wafp_db->clicks}
                              WHERE first_click <> 0 AND
                                    created_at >= '{$start_time_str}' AND
                                    created_at <= '{$end_time_str}' ) as uniques,
                           ( SELECT COUNT(*)
                               FROM {$wafp_db->transactions}
                              WHERE type='commission' AND
                                    created_at >= '{$start_time_str}' AND
                                    created_at <= '{$end_time_str}' ) as transactions";

      $query = $wpdb->prepare($query_str, date('Y/m/d', $start_time ));
      $stats[] = $wpdb->get_row($query);
    }

    return $stats;
  }

  public static function affiliate_clicks( $page_num=1, $page_size=25, $affiliate_id=0 )
  {
    global $wafp_db, $wpdb;

    $limit  = (int)$page_size;
    $offset = ((int)$page_num - 1) * $limit;

    if( (int)$affiliate_id <= 0 ) {
      $query_str = "SELECT cl.*, li.target_url, usr.user_login FROM {$wafp_db->clicks} cl, {$wafp_db->links} li, {$wpdb->users} usr WHERE cl.affiliate_id=usr.ID AND cl.link_id=li.id ORDER BY id DESC LIMIT %d,%d";
      $query = $wpdb->prepare( $query_str, $offset, $limit );
    }
    else {
      $query_str = "SELECT cl.*, li.target_url, usr.user_login FROM {$wafp_db->clicks} cl, {$wafp_db->links} li, {$wpdb->users} usr WHERE cl.affiliate_id=usr.ID AND cl.link_id=li.id AND cl.affiliate_id=%d ORDER BY id DESC LIMIT %d,%d";
      $query = $wpdb->prepare( $query_str, $affiliate_id, $offset, $limit );
    }

    return $wpdb->get_results($query);
  }

  public static function top_referring_affiliates( $period, $page_num=1, $page_size=50 )
  {
    global $wafp_db, $wpdb;

    $num_days_in_month = (int)(date( 't', $period )) - 1;
    $seconds_in_month  = 60*60*24*(int)$num_days_in_month;

    $day_start = date( 'Y-m-d 00:00:00', $period );
    $day_end   = date( 'Y-m-d 23:59:59', ( $period + $seconds_in_month ) );

    $limit  = (int)$page_size;
    $offset = ((int)$page_num - 1) * $limit;

    $query_str = "SELECT aff.user_login AS aff_login,
                    (SELECT COUNT( * ) FROM {$wafp_db->clicks} WHERE affiliate_id=aff.ID AND created_at >= %s AND created_at <= %s) AS click_count,
                    (SELECT COUNT( * ) FROM {$wafp_db->transactions} WHERE type='commission' AND affiliate_id=aff.ID AND created_at >= %s AND created_at <= %s) AS transaction_count,
                    (SELECT SUM( sale_amount ) FROM {$wafp_db->transactions} WHERE type='commission' AND affiliate_id=aff.ID AND created_at >= %s AND created_at <= %s) AS sales_amount,
                    (SELECT SUM( refund_amount ) FROM {$wafp_db->transactions} WHERE type='commission' AND affiliate_id=aff.ID AND created_at >= %s AND created_at <= %s) AS refund_amount,
                    (SELECT SUM( commission_amount ) FROM {$wafp_db->commissions} WHERE affiliate_id=aff.ID AND created_at >= %s AND created_at <= %s) AS commission_amount,
                    (SELECT SUM( amount ) FROM {$wafp_db->payments} WHERE affiliate_id=aff.ID AND created_at >= %s AND created_at <= %s) AS payment_amount
                    FROM {$wpdb->users} aff
                    GROUP BY aff.user_login
                    ORDER BY click_count DESC, transaction_count DESC, sales_amount DESC, commission_amount DESC, aff.user_login
                    LIMIT %d,%d";

    $query = $wpdb->prepare( $query_str, $day_start, $day_end, $day_start, $day_end, $day_start, $day_end, $day_start, $day_end, $day_start, $day_end, $day_start, $day_end, $offset, $limit );

    return $wpdb->get_results($query);
  }


  public static function affiliate_frontend_payments($affiliate_id)
  {
    global $wpdb, $wafp_db;
    $query_str = "( SELECT 'transaction' AS trans_type,
                           tr.sale_amount AS sale_amount,
                           co.commission_amount AS commission_amount,
                           co.correction_amount AS correction_amount,
                           co.commission_level AS commission_level,
                           (co.commission_amount - co.correction_amount) AS total_amount,
                           NULL AS payment_amount,
                           co.payment_id AS payment_id,
                           co.created_at AS timestamp
                      FROM {$wafp_db->commissions} co
                      JOIN {$wafp_db->transactions} tr ON co.transaction_id = tr.id
                     WHERE co.affiliate_id=%d )
                  UNION (
                    SELECT 'payment' AS trans_type,
                           NULL AS sale_amount,
                           NULL AS commission_amount,
                           NULL AS correction_amount,
                           NULL AS commission_level,
                           NULL AS total_amount,
                           pmt.amount AS payment_amount,
                           NULL AS payment_id,
                           pmt.created_at AS timestamp
                      FROM {$wafp_db->payments} pmt
                     WHERE pmt.affiliate_id=%d )
                  ORDER BY timestamp DESC";
    $query = $wpdb->prepare( $query_str, $affiliate_id, $affiliate_id );
    return $wpdb->get_results($query);
  }

  public static function affiliate_payment_totals($affiliate_id)
  {
    global $wpdb, $wafp_db;
    $query_str = "SELECT (SUM(co.commission_amount) - SUM(co.correction_amount)) AS owed FROM {$wafp_db->commissions} co WHERE co.payment_id = 0 AND co.affiliate_id=%d";
    $query = $wpdb->prepare( $query_str, $affiliate_id );
    $owed = $wpdb->get_var($query);

    $query_str = "SELECT SUM(pmt.amount) AS paid FROM {$wafp_db->payments} pmt WHERE pmt.affiliate_id=%d";
    $query = $wpdb->prepare( $query_str, $affiliate_id );
    $paid = $wpdb->get_var($query);

    $owed = ((!$owed)?0.00:$owed);
    $paid = ((!$paid)?0.00:$paid);

    return compact('owed','paid');
  }

  public static function affiliate_paypal_bulk_file_totals($payment_ids=null)
  {
    global $wpdb, $wafp_db;

    if(empty($payment_ids) or is_null($payment_ids))
      return false;

    $query = "SELECT (SUM(co.commission_amount) - SUM(co.correction_amount)) as paid, co.affiliate_id FROM {$wafp_db->commissions} co WHERE co.payment_id IN ({$payment_ids}) GROUP BY co.affiliate_id";

    return $wpdb->get_results($query);
  }

  public static function get_user_count()
  {
    global $wafp_db, $wpdb;
    return $wafp_db->get_count( $wpdb->users );
  }
}
