<?php

/**
* WIDGET
*
* Daily views count of products/categories
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

class Widgets__Productviews extends EnergyPlus_Widgets {

  public static $name = 'Product Views';
  public static $multiple = false;

  public static function run ( $args = array() ) {

    global $wpdb;

    $time = EnergyPlus_Helpers::strtotime('today', 'Y-m-d 00:00:00');

    $result = $wpdb->get_results(
      $wpdb->prepare("
      SELECT id, type, count(*) AS cnt
      FROM {$wpdb->prefix}energyplus_requests
      WHERE type IN (1,2) AND week = %d AND date > %s
      GROUP BY id
      ORDER BY cnt DESC, id DESC",
      EnergyPlus_Helpers::strtotime('now', 'W'), $time
      ) ,ARRAY_A);

      if (EnergyPlus_Helpers::is_ajax() OR isset( $args['ajax'] ))  {
        return EnergyPlus_View::run('widgets/productviews',  array( 'args' => $args, 'result' => $result ));
      } else {
        echo EnergyPlus_View::run('widgets/productviews',  array( 'args' => $args, 'result' => $result ));
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
              'values'=> array(3,4,5,6,7,8,9,10)
            ),
            array(
              'title' => 'H',
              'id' => 'h',
              'values'=> array(2,3,4,5,6,7,8,9,10)
            ),
          )
        )
      );
    }
  }

  ?>
