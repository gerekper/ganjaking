<?php

/**
* WIDGET
*
* Information about store's sales, customers etc.
*
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework/libs/widgets
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Widgets__Overview extends EnergyPlus_Widgets {
  public static $name = 'Overview';
  public static $multiple = true;




  public static function run ( $args = array(), $settings = array()) {

    global $wpdb;

    $time = isset( $args['lasttime'] ) ? $args['lasttime'] : time() - (24 * 60 * 60);
    $time = time() - (24*60*60);

    if (empty($settings)) {
      $settings['today_total_visitors'] = 'true';
      $settings['today_orders']         = 'true';
      $settings['today_revenue']        = 'true';
      $settings['week_revenue']         = 'true';
      $settings['month_revenue']        = 'true';
    }

    $result = array(
      'online'               => array('title' => esc_html__('Online', 'energyplus'), 'count' => 0, 'active' => (isset($settings['online']) && 'true' === $settings['online']) ? 'true' : 'false'  ),
      'today_total_visitors' => array('title' => esc_html__('Today - Visitors', 'energyplus'), 'count' => 0, 'active' => (isset($settings['today_total_visitors']) && 'true' === $settings['today_total_visitors']) ?  'true' : 'false'),
      'today_orders'         => array('title' => esc_html__('Today - Orders', 'energyplus'), 'count' => 0, 'active' =>(isset($settings['today_orders']) && 'true' === $settings['today_orders']) ?  'true' : 'false'  ),
      'pending_orders'       => array('title' => esc_html__('Orders in progress', 'energyplus'), 'count' => 0, 'active' => (isset($settings['pending_orders']) && 'true' === $settings['pending_orders']) ?  'true' : 'false'  ),
    //  'today_members'        => array('title' => esc_html__('Today - New Customers', 'energyplus'), 'count' => 0, 'active' => (isset($settings['today_members']) && 'true' === $settings['today_members']) ?  'true' : 'false'  ),
      'today_revenue'        => array('title' => esc_html__('Today - Sales', 'energyplus'), 'count' => 0, 'is_price' => true,  'active' => (isset($settings['today_revenue']) && 'true' === $settings['today_revenue']) ?  'true' : 'false'  ),
      'week_revenue'         => array('title' => esc_html__('Week - Sales', 'energyplus'), 'count' => 0,  'is_price' => true, 'active' => (isset($settings['week_revenue']) && 'true' === $settings['week_revenue']) ?  'true' : 'false'  ),
      'month_revenue'        => array('title' => esc_html__('Month - Sales', 'energyplus'), 'count' => 0,  'is_price' => true, 'active' => (isset($settings['month_revenue']) && 'true' === $settings['month_revenue']) ?  'true' : 'false' ),

    );

    // Online visitors
    if ('true' === $result['online']['active']) {
      $result['online']['count'] = Widgets__Onlineusers::run(array('ajax'=>1));
    }

    // Todays total visitors
    if ('true' === $result['today_total_visitors']['active']) {

      $_today_total_visitors = $wpdb->get_var(
        $wpdb->prepare("
        SELECT COUNT(DISTINCT session_id)
        FROM {$wpdb->prefix}energyplus_requests
        WHERE week = %d AND date >= %s",
        EnergyPlus_Helpers::strtotime('now', 'W'), EnergyPlus_Helpers::strtotime('today')
        )
      );

      if ($_today_total_visitors) {
        $result['today_total_visitors']['count'] = absint($_today_total_visitors);
      }
    }

    EnergyPlus::wc_engine();

    $date =   EnergyPlus_Helpers::strtotime('now', 'Y-m-d');

    $_today =  WC()->api->WC_API_Reports->get_sales_report(null, array('date_min' => $date, 'date_max' => $date));


    if ('true' === $result['today_revenue']['active']) {
      $result['today_revenue']['count'] = intval($_today['sales']['totals'][$date]['sales']);
    }

    if ('true' === $result['today_orders']['active']) {
      $result['today_orders']['count'] = intval($_today['sales']['totals'][$date]['orders']);
    }
    /*
    if ('true' === $result['today_members']['active']) {
    $result['today_members']['count'] = intval($_today['sales']['totals'][$date]['customers']);
  }
  */

  $_week =  WC()->api->WC_API_Reports->get_sales_report(null, array('date_min' => date("Y-m-d", strtotime('this week')), 'date_max' => $date));

  if ('true' === $result['week_revenue']['active']) {
    $result['week_revenue']['count'] = intval($_week['sales']['total_sales']);
  }

  $_month =  WC()->api->WC_API_Reports->get_sales_report(null, array('date_min' => date("Y-m-d", strtotime('first day of this month')), 'date_max' => $date));

  if ('true' === $result['month_revenue']['active']) {
    $result['month_revenue']['count'] = intval($_month['sales']['total_sales']);
  }

  if ('true' === $result['pending_orders']['active']) {
    $result['pending_orders']['count'] = wc_orders_count('on-hold') + wc_orders_count('processing') + wc_orders_count('pending');
  }

  if (EnergyPlus_Helpers::is_ajax() OR isset( $args['ajax'] ))  {
    return EnergyPlus_View::run('widgets/overview',  array( 'args' => $args, 'results' => $result ));
  } else {
    echo EnergyPlus_View::run('widgets/overview',  array( 'args' => $args, 'results' => $result ));
  }
}

/**
* Widget's settings
*
* @since  1.0.0
* @param  array    $args
* @return array
*/

public static function settings ( $settings ) {

  if (empty($settings)) {
    $settings['today_total_visitors'] = 'true';
    $settings['today_orders']         = 'true';
    $settings['today_revenue']        = 'true';
    $settings['week_revenue']         = 'true';
    $settings['month_revenue']        = 'true';
  }

  return array(
    'dimensions' => array(
      'type' => 'wh',
      'title' => esc_html__('Dimensions', 'energyplus'),
      'values' => array(
        array(
          'title' => 'W',
          'id' => 'w',
          'values'=> array(3,4,5,6,7,8,9,10)
        ),
        array(
          'title' => 'H',
          'id' => 'h',
          'values'=> array(1,2,3,4,5,6,7,8,9,10)
        ),
      )
    ),

    'infos' => array(
      'type' => 'checkbox',
      'title' => esc_html__('Show', 'energyplus'),
      'values' => array(
        'online'               => array('title' => esc_html__('Online', 'energyplus'), 'id' => 'online', 'selected' => (isset($settings['online']) && 'true' === $settings['online']) ? 'true' : 'false'  ),
        'today_total_visitors' => array('title' => esc_html__('Today - Visitors', 'energyplus'), 'id' => 'today_total_visitors', 'selected' => (isset($settings['today_total_visitors']) && 'true' === $settings['today_total_visitors']) ?  'true' : 'false'),
        'today_orders'         => array('title' => esc_html__('Today - Orders', 'energyplus'), 'id' => 'today_orders', 'selected' =>(isset($settings['today_orders']) && 'true' === $settings['today_orders']) ?  'true' : 'false'  ),
        'pending_orders'       => array('title' => esc_html__('Orders in progress', 'energyplus'), 'id' => 'pending_orders', 'selected' => (isset($settings['pending_orders']) && 'true' === $settings['pending_orders']) ?  'true' : 'false'  ),
    //    'today_members'        => array('title' => esc_html__('Today - New Customers', 'energyplus'), 'id' => 'today_members', 'selected' => (isset($settings['today_members']) && 'true' === $settings['today_members']) ?  'true' : 'false'  ),
        'today_revenue'        => array('title' => esc_html__('Today - Sales', 'energyplus') , 'id' => 'today_revenue', 'selected' => (isset($settings['today_revenue']) && 'true' === $settings['today_revenue']) ?  'true' : 'false'  ),
        'week_revenue'         => array('title' => esc_html__('Week - Sales', 'energyplus'), 'id' => 'week_revenue', 'selected' => (isset($settings['week_revenue']) && 'true' === $settings['week_revenue']) ?  'true' : 'false'  ),
        'month_revenue'        => array('title' => esc_html__('Month - Sales', 'energyplus'), 'id' => 'month_revenue', 'selected' => (isset($settings['month_revenue']) && 'true' === $settings['month_revenue']) ?  'true' : 'false' ),


      )
    )

  );

}
}

?>
