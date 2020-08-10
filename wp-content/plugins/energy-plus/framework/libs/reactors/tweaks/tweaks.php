<?php

/**
* Tweaks
*
* @since      1.1.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework/libs/widgets
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class Reactors__tweaks__tweaks  {

  public static function settings() {

    wp_enqueue_script("energyplus-fontselect",     EnergyPlus_Public . "3rd/jquery.fontselect.js", array(), EnergyPlus_Version);


    $reactor = EnergyPlus_Reactors::reactors_list('tweaks');

    $saved = 0;

    if ($_POST) {

      if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'energyplus_reactors' ) ) {
        exit;
      }

      /* Detail window size */

      $window_size = intval(EnergyPlus_Helpers::post('reactors-tweaks-window-size', '1090'));
      if ('px' === EnergyPlus_Helpers::post('reactors-tweaks-window-size-dimension', 'px') && $window_size < 700) {
        $window_size = 900;
      }
      if ('%' === EnergyPlus_Helpers::post('reactors-tweaks-window-size-dimension', 'px') && ($window_size < 20 || $window_size > 100)) {
        $window_size = 60;
      }
      EnergyPlus::option('reactors-tweaks-window-size', intval($window_size).EnergyPlus_Helpers::post('reactors-tweaks-window-size-dimension', 'px'), 'set');

      // reactors-tweaks-order-cond

      delete_option('energyplus_reactors-tweaks-order-statuses');
      EnergyPlus::option('reactors-tweaks-order-cond', EnergyPlus_Helpers::sanitize_array($_POST['reactors-tweaks-order-cond']), 'set');

      // reactors-tweaks-adminbar-hotkey

      EnergyPlus::option('reactors-tweaks-adminbar-hotkey', EnergyPlus_Helpers::post('reactors-tweaks-adminbar-hotkey', 0), 'set');

      // Landing page

      EnergyPlus::option('reactors-tweaks-landing', strtolower(EnergyPlus_Helpers::post('reactors-tweaks-landing', 'dashboard')), 'set');

      // reactors-tweaks-settings-woocommerce

      EnergyPlus::option('reactors-tweaks-settings-woocommerce', EnergyPlus_Helpers::post('reactors-tweaks-settings-woocommerce', 0), 'set');

      // reactors-tweaks-screenoptions

      EnergyPlus::option('reactors-tweaks-screenoptions', EnergyPlus_Helpers::post('reactors-tweaks-screenoptions', 0), 'set');

      // reactors-tweaks-pg-comment

     EnergyPlus::option('reactors-tweaks-pg-orders', absint(EnergyPlus_Helpers::post('reactors-tweaks-pg-orders', 10)), 'set');
     EnergyPlus::option('reactors-tweaks-pg-products', absint(EnergyPlus_Helpers::post('reactors-tweaks-pg-products', 10)), 'set');
     EnergyPlus::option('reactors-tweaks-pg-customers', absint(EnergyPlus_Helpers::post('reactors-tweaks-pg-customers', 10)), 'set');
     EnergyPlus::option('reactors-tweaks-pg-coupons', absint(EnergyPlus_Helpers::post('reactors-tweaks-pg-coupons', 10)), 'set');
     EnergyPlus::option('reactors-tweaks-pg-comments', absint(EnergyPlus_Helpers::post('reactors-tweaks-pg-comments', 10)), 'set');

      //reactors-tweaks-icon-text

      EnergyPlus::option('reactors-tweaks-icon-text', EnergyPlus_Helpers::post('reactors-tweaks-icon-text', 0), 'set');

      //reactors-tweaks-font
      $redirect = 0;

      if (EnergyPlus::option('reactors-tweaks-font') !== EnergyPlus_Helpers::post('reactors-tweaks-font', 'Open+Sans:400')) {
        $redirect = 1;
      }

      EnergyPlus::option('reactors-tweaks-font', EnergyPlus_Helpers::post('reactors-tweaks-font', 'Open+Sans:400'), 'set');

      if ('Theme+Default' === EnergyPlus_Helpers::post('reactors-tweaks-font', 'Open+Sans:400') ) {
        delete_option('energyplus_reactors-tweaks-font');
      }

      if (1 === $redirect) {
        wp_redirect(EnergyPlus_Helpers::admin_page('reactors', array('action'=>'detail', 'id'=>'tweaks')));
      }

  
    }

    echo   EnergyPlus_View::reactor('tweaks/views/settings', array('reactor' => $reactor, 'saved' => $saved));
  }

  public static function init() {
  }


  public static function deactivate() {
    // Remove options
    $options = array(
      'reactors-tweaks-window-size',
      'reactors-tweaks-window-size-dimension',
      'reactors-tweaks-order-statuses',
      'reactors-tweaks-order-cond',
      'reactors-tweaks-adminbar-hotkey',
      'reactors-tweaks-landing',
      'reactors-tweaks-settings-woocommerce',
      'reactors-tweaks-icon-text',
      'reactors-tweaks-font',
      'reactors-tweaks-screenoptions',
      'reactors-tweaks-pg-orders',
      'reactors-tweaks-pg-products',
      'reactors-tweaks-pg-customers',
      'reactors-tweaks-pg-coupons',
      'reactors-tweaks-pg-comments'
    );

    foreach ($options AS $option) {
      delete_option('energyplus_' . $option);
    }
  }
}
