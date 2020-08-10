<?php

/**
* EnergyPlus Ajax
*
* Ajax router
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class EnergyPlus_Ajax extends EnergyPlus {

  /**
  * Main function
  *
  * @since  1.0.0
  */

  public static function run() {

    $segment = EnergyPlus_Helpers::post('segment', false);

    if (!$segment) {
      $segment = EnergyPlus_Helpers::get('segment', false);
    }

    check_admin_referer( 'energyplus-segment--' . $segment, '_asnonce' );

    if ( ! wp_verify_nonce( $_REQUEST['_asnonce'],  'energyplus-segment--' . $segment ) ) {
      die( esc_html__('Failed on security check', 'energyplus') );
    }

    switch ($segment) {
      case 'search':
      EnergyPlus_Events::search();
      break;

      case 'lists':
      EnergyPlus_Events::lists();
      break;

      case 'orders':
      EnergyPlus_Orders::ajax();
      break;

      case 'customers':
      EnergyPlus_Customers::ajax();
      break;

      case 'coupons':
      EnergyPlus_Coupons::ajax();
      break;

      case 'products':
      EnergyPlus_Products::ajax();
      break;

      case 'comments':
      EnergyPlus_Comments::ajax();
      break;

      case 'settings':
      EnergyPlus_Settings::ajax();
      break;

      case 'reports':
      EnergyPlus_Reports::ajax();
      break;

      case 'dashboard':
      EnergyPlus_Dashboard::ajax();
      break;

      case 'notifications':
      EnergyPlus_Events::notifications();
      break;
      }
    }

    /**
    * Print error message on failure
    *
    * @since  1.0.0
    * @param  string    $error
    */

    public static function error($error) {
      echo json_encode(array('status'=>0, 'error'=> esc_html($error)));
      wp_die();
    }


    /**
    * Print success message
    *
    * @since  1.0.0
    * @param  string    $message
    * @param  array     $details
    */

    public static function success($message = '', $details = array(), $raw = false){
      echo json_encode(array_merge(array('status'=>1, 'message'=>$message), $details));
      wp_die();
    }
  }
