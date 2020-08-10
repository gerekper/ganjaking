<?php

/**
* EnergyPlus Events
*
* Notifications and hooks about internal usages
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class EnergyPlus_Events extends EnergyPlus {

  /**
  * Notifications
  *
  * @since  1.0.0
  */

  public static function notifications(){

    global $wpdb;

    $lastid                 = 0;
    $now                    = time();
    $last                   = intval(EnergyPlus_Helpers::post('lasttime', 0));
    $notifications_lasttime = intval ( EnergyPlus::option('notifications_lasttime_' . absint(get_current_user_id()) , 0) );
    $last_timestamp         = EnergyPlus_Helpers::timestamp(intval(EnergyPlus_Helpers::post('lasttime', 0)));


    $query = $wpdb->prepare(
      "SELECT count(*) count FROM {$wpdb->prefix}energyplus_events WHERE type IN (1,2,3,4,5,11,12,14,15) AND event_id > %d",
      $notifications_lasttime
    );

    $count = $wpdb->get_var($query);

    if ( 0 === $notifications_lasttime OR -1 === $last) {

      $query = $wpdb->prepare("SELECT event_id FROM {$wpdb->prefix}energyplus_events WHERE type IN (1,2,3,4,5,11,12,14,15)  ORDER BY event_id DESC LIMIT %d", 1);
      $_last = $wpdb->get_var($query);

      EnergyPlus::option('notifications_lasttime_' . absint(get_current_user_id()) , absint($_last), 'set');
    }

    if (-2 === $last)
    {
      $where = '1=1';
    }  else {
      $where = ' event_id > ' . esc_sql(intval(sanitize_text_field(EnergyPlus_Helpers::post('lastid', 0))));
    }


    $limit = max(intval($count)+10, 10);
    $query          = $wpdb->prepare("SELECT event_id, user, type, id, extra, time FROM {$wpdb->prefix}energyplus_events WHERE type IN (1,2,3,4,5,11,12,14,15) AND $where ORDER BY event_id DESC LIMIT 0, %d", $limit);
    $_notifications = $wpdb->get_results($query);

    $notifications = array();
    $alerts        = array();
    $sounds        = array();

    foreach ($_notifications AS $notify) {

      if (0 === $lastid) {
        $lastid = $notify->event_id;
      }

      switch ($notify->type)
      {

        // New Order
        case 2:

        try {
          $order =  new WC_Order( $notify->id );
        } catch(Exception $e){
          continue 2;
        }


        $order_data = $order->get_data() ;

        if ('failed' !== $order->get_status()) {
          $notifications[$notify->event_id] = array(
            'time'    => $notify->time,
            'type'    => $notify->type,
            'title'   => sprintf (esc_html__('New order', 'energyplus')),
            'details' => array(
              'order_id'             => $notify->id,
              'status'               => $order->get_status(),
              'total'                => $order->get_formatted_order_total(),
              'payment_method_title' => $order_data['payment_method_title'],
              'customer'             => esc_html(sprintf("%s %s", $order_data['billing']['first_name'], $order_data['billing']['last_name'])),
              'city'                 => esc_html(sprintf("%s, %s", $order_data['billing']['city'], @WC()->countries->states[$order_data['billing']['country']][$order_data['billing']['state']])),
            )
          );
        }

        if (intval(EnergyPlus_Helpers::post('lasttime', time())) >0 && strtotime($notify->time) > intval(EnergyPlus_Helpers::post('lasttime', time()))) {
          $notifications[$notify->event_id]['status'] = 'new';
          $alerts[] = array(
            'title' => esc_html__('New order', 'energyplus'),
            'body'  => esc_html(sprintf ("%s - %s %s", $order_data['currency']. ' ' . $order_data['total'], $order_data['billing']['first_name'], $order_data['billing']['last_name'])),
            'link'  => EnergyPlus_Helpers::admin_page('orders', array( ))
          );

          $sounds[0] = EnergyPlus_Public.'sounds/notification.mp3';

        }
        break;


        // Comments
        case 4:

        $comment = get_comment( $notify->id );

        if (!$comment) {
          continue 2;
        }

        $post = get_post($comment->comment_post_ID);

        $notifications[$notify->event_id] = array(
          'time'    => $notify->time,
          'type'    => $notify->type,
          'title'   => sprintf (esc_html__("%s has commented to %s", 'energyplus'), $comment->comment_author, $post->post_title),
          'details' => array(
            'comment_content' => $comment->comment_content,
            'post_id'         => $post->ID,
            'comment_id'      => $comment->comment_ID,
            'star'            => intval(get_comment_meta( $comment->comment_ID, 'rating', true ))
          )
        );

        if (intval(EnergyPlus_Helpers::post('lasttime', time())) >0 && strtotime($notify->time) > intval(EnergyPlus_Helpers::post('lasttime', time()))) {
          $notifications[$notify->event_id]['status'] = 'new';
          $alerts[] = array(
            'title' => esc_html__('New comment', 'energyplus'),
            'body'  => sprintf (esc_html__("%s has commented to %s", 'energyplus'), $comment->comment_author, $post->post_title),
            'link'  => EnergyPlus_Helpers::admin_page('comments', array()) . "#" . $comment->comment_ID
          );

          $sounds[0] = EnergyPlus_Public.'sounds/notification.mp3';

        }
        break;

        // Coupon usage limit has been reached

        case 11:

        $extra = maybe_unserialize($notify->extra);

        if (!is_array($extra)) {
          continue 2;
        }

        $notifications[$notify->event_id] = array(
          'time'    => $notify->time,
          'type'    => $notify->type,
          'title'   => sprintf (esc_html__("%s &mdash; Coupon usage limit has been reached", 'energyplus'), strtoupper($extra[0])),
          'details' => array(
            'coupon_code' => $extra[0],
            'coupon_id'   => $notify->id,
            'usage'       => $extra[1] . '/' . $extra[2],

          )
        );

        break;

        // Info messages
        case 12:

        $extra = maybe_unserialize($notify->extra);

        $notifications[$notify->event_id] = array(
          'time'    => $notify->time,
          'type'    => $notify->type,
          'title'   => $extra['title'],
          'details' => $extra
        );

        break;

        // Stock
        case 14:

        $product = wc_get_product( $notify->id );

        if (!$product) {
          continue 2;
        }

        if (intval($notify->extra) < 1) {
          $title = sprintf (esc_html__("%s - Out of stock", 'energyplus'), $product->get_name());
          $title2 =  esc_html__('Out of stock', 'energyplus');
        } else {
          $title = sprintf (esc_html__("%s - Low stock", 'energyplus'), $product->get_name());
          $title2 =  esc_html__('Low stock', 'energyplus');
        }


        $notifications[$notify->event_id] = array(
          'time'    => $notify->time,
          'type'    => $notify->type,
          'title'   => $title,
          'details' => array(
            'product_name' => $product->get_name(),
            'product_id'   => $product->get_id(),
            'qty'   => $notify->extra
          )
        );

        if (intval(EnergyPlus_Helpers::post('lasttime', time())) >0 && strtotime($notify->time) > intval(EnergyPlus_Helpers::post('lasttime', time()))) {
          $notifications[$notify->event_id]['status'] = 'new';
          $alerts[] = array(
            'title' => $title2,
            'body'  => sprintf (esc_html__("Low stock for %s - %d", 'energyplus'), $product->get_name(),$notify->extra),
            'link'  => EnergyPlus_Helpers::admin_page('products', array())
          );

          $sounds[0] = EnergyPlus_Public.'sounds/notification.mp3';

        }
        break;

        // Announcements
        case 15:

        $extra = maybe_unserialize($notify->extra);

        $notifications[$notify->event_id] = array(
          'time'    => $notify->time,
          'type'    => $notify->type,
          'title'   => $extra['title'],
          'details' => $extra
        );

        if (intval(EnergyPlus_Helpers::post('lasttime', time())) >0 && strtotime($notify->time) > intval(EnergyPlus_Helpers::post('lasttime', time()))) {
          $notifications[$notify->event_id]['status'] = 'new';
          $alerts[] = array(
            'title' => esc_html($extra['title']),
            'body'  => substr(wp_kses_data(strip_tags($extra['content'])), 0, 50) . '...',
            'link'  => EnergyPlus_Helpers::admin_page('dashboard', array( ))
          );

          $sounds[0] = EnergyPlus_Public.'sounds/notification.mp3';

        }

        break;

      }
    }
    if (0 === $last) {
      $notifications = array();
    }
    $title = self::get_title($count);

    $today_sales = number_format(floatval(get_transient( 'today_sales' )),0);
    echo json_encode( array( 'background'=>true, 'status'=>1, 'result'=> EnergyPlus_View::run('core/notifications',  array( 'notifications' => $notifications ) ), 'count' => $count, 'title' => $title, 'lastid' => $lastid, 'time'=> $now, 'alerts' => $alerts, 'today_sales'=>$today_sales, 'sounds'=>$sounds));

    wp_die();
  }

  public static function notification_count() {

    global $wpdb;

    $now = time();

    $notifications_lasttime = intval ( EnergyPlus::option('notifications_lasttime_' . absint(get_current_user_id()) , 0) );

    $query = $wpdb->prepare("SELECT count(*) count FROM {$wpdb->prefix}energyplus_events WHERE type IN (1,2,3,4,5,11,12,14,15) AND event_id > %d", $notifications_lasttime) ;
    $count = $wpdb->get_var($query);

    return intval($count);

  }


  /**
  * Searching whole store
  *
  * @since  1.0.0
  */


  public static function search() {

    $term = EnergyPlus_Helpers::post('q', '');

    if ($term) {
      wp_die( EnergyPlus_View::run('core/search',  array( 'term' =>  $term)) );
    } else {
      wp_die();
    }

  }

  /**
  * Get first name a customer
  *
  * @since  1.0.0
  * @param  int    $userid
  * @return string
  */

  private static function get_username($userid) {
    $user_info = get_userdata(absint($userid));
    return  $user_info->first_name;
  }


  /**
  * Set title for giving specific information
  * @since  1.0.0
  * @param  int    $notification_count
  */

  public static function get_title($notification_count) {

    $option             = EnergyPlus::option('feature-badge', 0);
    $notification_count = absint($notification_count);

    $title = "";

    switch ($option) {

      // Notification counts
      case 0:
      if (0 < $notification_count) {
        $title = "($notification_count)";
      }
      break;

      // Online Users
      case 1:
      $notification_count =   Widgets__Onlineusers::run(array('ajax' => 1));
      if (0 < $notification_count) {
        $title = "($notification_count)";
      }
      break;

      // Orders not completed
      case 2:

      EnergyPlus::wc_engine();

      $notification_count = absint(WC()->api->WC_API_Orders->get_orders_count( 'pending' ) ['count'] ) +
      absint(WC()->api->WC_API_Orders->get_orders_count( 'processing' ) ['count'] ) +
      absint(WC()->api->WC_API_Orders->get_orders_count( 'on-hold' ) ['count'] );;

      if (0 < $notification_count) {
        $title = "($notification_count)";
      }

      break;

      // Today's visitors
      case 3:

      global $wpdb;

      $_today_total_visitors = $wpdb->get_var(
        $wpdb->prepare("
        SELECT COUNT(DISTINCT session_id)
        FROM {$wpdb->prefix}energyplus_requests
        WHERE week = %d AND date >= %s",
        EnergyPlus_Helpers::strtotime('now', 'W'), EnergyPlus_Helpers::strtotime('today')
        )
      );

      if ($_today_total_visitors) {
        $title = "($_today_total_visitors)";
      }

      break;

      // Today's sales

      case 4:

      $today_sales = number_format(floatval(get_transient( 'today_sales' )),0);

      if ($today_sales) {
        $title = "($today_sales)";
      }


      break;

      // Today's total revenue

      case 5:
      break;

    }

    return $title . ' ' . get_bloginfo( 'name' );
  }

  /**
  * Add to events table
  *
  * @since 1.0.0
  * @param array     $data
  */


  public static function add($data = array()) {

    global $wpdb;

    if (!isset($data['user'])) {
      $user         = get_current_user_id();
      $data['user'] = absint ( $user );
    }

    $insert = $wpdb->insert( $wpdb->prefix."energyplus_events",
    array(
      'user'   => $data['user'],
      'time'   => current_time('mysql'),
      'id'     => $data['id'],
      'type'   => $data['type'],
      'extra'  => $data['extra'],
    ),
    array('%s', '%s','%s', '%s','%s', '%s')
  );


}

/**
* Hook for new orders
*
* @since  1.0.0
* @param  int    $order_id
*/

public static function new_order( $order_id ) {
  global $wpdb;

  $_query = $wpdb->get_var(
    $wpdb->prepare("
    SELECT event_id
    FROM {$wpdb->prefix}energyplus_events
    WHERE type = 2 AND id = %d",
    $order_id
    )
  );

  if ($_query) {
    return false;
  }

  $notify = array('wc-cancelled','wc-refunded','wc-failed');

  $order = wc_get_order ($order_id);

  $order_status  = $order->get_status();

  if (!in_array($order_status, $notify) && !in_array('wc-' . $order_status, $notify)) {
    $data = array(
      'type'  => 2,
      'id'    => $order_id,
      'extra' => ''
    );

    self::add( $data );
  }



  /*foreach( $order->get_used_coupons() as $coupon ){

    // Retrieving the coupon ID
    $coupon_post_obj = get_page_by_title($coupon, OBJECT, 'shop_coupon');

    if (!$coupon_post_obj) {
      continue;
    }

    $coupon_id = $coupon_post_obj->ID;

    // Get an instance of WC_Coupon object in an array(necesary to use WC_Coupon methods)
    $coupons_obj = new WC_Coupon($coupon_id);
    if ( $coupons_obj->get_usage_limit() > 0 && ($coupons_obj->get_usage_count() >= ($coupons_obj->get_usage_limit()-1)) ) {

      $data = array(
        'type'  => 11,
        'id'    => $coupon_id,
        'extra' => serialize(array($coupon, $coupons_obj->get_usage_limit(), $coupons_obj->get_usage_count()+1))
      );
      self::add( $data );

    }
  }*/

  self::save_post_shop_order( $order_id );

}

/**
* Save new today's sales when an order is updated
*
* @since  1.0.0
*/

public static function post_updated_messages( ) {
  self::save_post_shop_order(0);
}

/**
* Save new today's sales when an order is updated
*
* @since  1.0.0
* @param  int    $order_id
*/

public static function save_post_shop_order( $order_id ) {

  EnergyPlus::wc_engine();

  wc_delete_shop_order_transients();

  include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
  include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-report-sales-by-date.php' );

  $report = new WC_Report_Sales_By_Date();

  $_GET['start_date'] = EnergyPlus_Helpers::strtotime('now', "Y-m-d");
  $_GET['end_date']   = EnergyPlus_Helpers::strtotime('now', "Y-m-d");

  $report_data     = $report->calculate_current_range( 'custom' );
  $report_data     = $report->get_report_data();

  if (is_wp_error($report_data)) {
    return;
  }
  set_transient( 'today_sales', floatval(floor($report_data->total_sales)) , EnergyPlus_Helpers::strtotime('tomorrow 00:00:00', 'U')-EnergyPlus_Helpers::strtotime('now', 'U') );

}

/**
* Hook for order updates
*
* @since  1.0.0
*/

public static function transition_post_status( $new_status, $old_status, $post ) {
  $data = array(
    'type'  => 6,
    'id'    => absint ( $post->ID ),
    'extra' => serialize ( array( 'name' => $post->post_title, 'type' => $post->post_type, 'new' => $new_status, 'old' => $old_status ) )
  );

  self::add( $data );
}


/**
* Hook for new comment
*
* @since  1.0.0
*/

public static function comment_post( $comment_ID, $comment_approved ) {

  if ('spam' !== $comment_approved && 'trash' !== $comment_approved) {
    $data = array(
      'type'  => 4,
      'id'    => absint ( $comment_ID ),
      'extra' => ''
    );

    self::add( $data );
  }
}


/**
* Low stock
*
* @since  1.2.2
*/

public static function low_stock( $product ) {

  if (is_object($product)) {
    $data = array(
      'type'  => 14,
      'id'    => absint ( $product->get_id() ),
      'extra' => $product->get_stock_quantity()
    );

    self::add( $data );

  }
}

}

?>
