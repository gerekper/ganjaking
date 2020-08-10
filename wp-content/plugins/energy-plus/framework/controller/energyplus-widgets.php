<?php

/**
* EnergyPlus Widgets
*
* Widgets for Dashboard
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class EnergyPlus_Widgets extends EnergyPlus {

  /**
  * List of available widgets
  *
  * @since  1.0.0
  */

  public static function available_widgets() {
    return array(
      'onlineusers'  => array('id'=>'onlineusers', 'title'=>esc_html__('Online Users', 'energyplus'), 'image'=>'', 'description'=>esc_html__('Count of online users on your store', 'energyplus'), 'multiple'=>false, 'w'=>5, 'h'=>4),
      'hourly'       => array('id'=>'hourly', 'title'=>esc_html__('Visitors', 'energyplus'), 'image'=>'', 'description'=>esc_html__('Hourly/Daily/Monthly visitors count', 'energyplus'), 'multiple'=>false, 'w'=>5, 'h'=>4),
      'overview'     => array('id'=>'overview', 'title'=>esc_html__('Overview', 'energyplus'), 'image'=>'', 'description'=>esc_html__('Display info about sales, customers etc.', 'energyplus'), 'multiple'=>true, 'w'=>10, 'h'=>1),
      'productviews' => array('id'=>'productviews', 'title'=>esc_html__('Product Views', 'energyplus'),'image'=>'', 'description'=>esc_html__('Which products viewed today?', 'energyplus'),  'multiple'=>false, 'w'=>5, 'h'=>4),
      'lastactivity' => array('id'=>'lastactivity', 'title'=>esc_html__('Last Activities', 'energyplus'),'image'=>'', 'description'=>esc_html__('Live info about your visitors activities', 'energyplus'),  'multiple'=>false, 'w'=>10, 'h'=>4),
      'funnel'       => array('id'=>'funnel', 'title'=>esc_html__('Funnel Graph', 'energyplus'), 'image'=>'', 'description'=>esc_html__('Graph of conversions on your store', 'energyplus'), 'multiple'=>false, 'w'=>10, 'h'=>3),
      'links'        => array('id'=>'links', 'title'=>esc_html__('Shortcuts', 'energyplus'), 'image'=>'', 'description'=>esc_html__('Create custom shortcuts', 'energyplus'), 'multiple'=>true, 'w'=>3, 'h'=>1)
    );
  }


  /**
  * Ajax router
  *
  * @since  1.0.0
  */

  public static function ajax() {

    if ('remap' === EnergyPlus_Helpers::post('a', ''))  {
      return self::remap();
    }

    if ('settings' === EnergyPlus_Helpers::post('a', '')) {
      return self::widget_settings();
    }

    $output = array();

    $map      = EnergyPlus::option('dashboard_widgets', array());
    $settings = EnergyPlus::option('dashboard_widgets_settings', array());

    foreach ($map AS $id => $widget) {
      $class= "Widgets__" . sanitize_key($widget['type']);

      if (isset($settings[$id])) {
        $_settings = $settings[$id];
      } else {
        $_settings = array();
      }

      $output[$id]['type']   = sanitize_key($widget['type']);
      $output[$id]['result'] = $class::run( array( 'id' => $id, 'ajax' => 1, 'counter' => intval( EnergyPlus_Helpers::post( 'c' ) ) , 'lasttime' => intval( EnergyPlus_Helpers::post( 't' ) ) ), $_settings);
    }

    $output[0] = array('type'=>'system', 'lasttime' => time() );

    echo json_encode( $output );
    wp_die();
  }


  /**
  * Rebuid of widget's positions and dimensions on dashboard
  *
  * @since  1.0.0
  */

  public static function remap() {

    $page      = EnergyPlus_Helpers::post( 'p', '' );
    $_widgets  = $_POST['widgets'];

    $new       = array();
    $map       = EnergyPlus::option('dashboard_widgets', array());

    $widgets   = array();
    $__widgets = array();

    foreach ($_widgets as $_widget)  {
      if (isset( $map[$_widget['id']] )) {
        $new[$_widget['id']] = array('type' => sanitize_key($map[$_widget['id']]['type']), 'id'=> sanitize_key($_widget['id']), 'w'=> sanitize_key($_widget['w']),'h'=> sanitize_key($_widget['h']));
      }
    }
    EnergyPlus::option("dashboard_widgets", $new, 'set');
    wp_die();

  }

  public static function widget_settings() {

    $id         = sanitize_key(EnergyPlus_Helpers::post('id', 0));
    $setting_id = sanitize_key(EnergyPlus_Helpers::post('set_id'));
    $value      = sanitize_key(EnergyPlus_Helpers::post('s'));

    $map        = EnergyPlus::option('dashboard_widgets', array());

    if (isset( $map[$id] ))  {
      $all = EnergyPlus::option('dashboard_widgets_settings', array());

      $all[$id][$setting_id] = $value;

      EnergyPlus::option('dashboard_widgets_settings', $all, 'set');
      $a = EnergyPlus::option('dashboard_widgets_settings', array());
    }

    wp_die();

  }

}

?>
