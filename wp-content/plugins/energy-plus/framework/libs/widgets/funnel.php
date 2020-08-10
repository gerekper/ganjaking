<?php

/**
* WIDGET
*
* Funnel Chart
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


class Widgets__Funnel extends EnergyPlus_Widgets {

  public static $name = 'Funnel Chart';
  public static $multiple = false;

  public static function run ( $args = array(), $settings = array() ) {

    global $wpdb;

    wp_enqueue_script("energyplus-funnel-graph",  EnergyPlus_Public . "3rd/funnel-graph/js/funnel-graph.js");

		EnergyPlus::wc_engine();

    $data['results']      = EnergyPlus_Reports::energyplus_data(array('range'=>'daily'));

    $result_key           = EnergyPlus_Helpers::strtotime('now', 'Ymd');

    $funnel_order         = intval($data['results'][$result_key]['orders']);
    $funnel_visitors      = intval($data['results'][$result_key]['visitors']);
    $funnel_product_pages = intval($data['results'][$result_key]['product_pages']);
    $funnel_carts         = intval($data['results'][$result_key]['carts']);
    $funnel_checkout      = intval($data['results'][$result_key]['checkout']);

		if (0 === $funnel_visitors) {
			$funnel_visitors = '0.0001'; // Prevent graph error
		}

    $data['funnel']       = array($funnel_visitors, $funnel_product_pages, $funnel_carts, $funnel_checkout, $funnel_order);


    if (EnergyPlus_Helpers::is_ajax() OR isset( $args['ajax'] ))  {
      return EnergyPlus_View::run('widgets/funnel',  array('args' => $args, 'data' => $data));
    } else {
      echo EnergyPlus_View::run('widgets/funnel',  array('args' => $args, 'data' => $data));
    }
  }

  /**
   * Widget's settings
   *
   * @since  1.0.0
   * @param  array    $args
   * @return array
   */

  public static function settings ( $args ) {
    return array(
      'dimensions' => array(
        'type' => 'wh',
        'title' => esc_html__('Dimensions', 'energyplus'),
        'values' => array(
          array(
            'title' => 'W',
            'id' => 'w',
            'values'=> array(4,'4_5',5,'5_5',6,'6_5',7,'7_5',8,'8_5',9,'9_5',10)
          ),
          array(
            'title' => 'H',
            'id' => 'h',
            'values'=> array(3)
          ),
        )
      )
    );

  }
}

?>
