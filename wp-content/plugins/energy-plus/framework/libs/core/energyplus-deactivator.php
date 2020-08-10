<?php

/**
* EnergyPlus Activator
*
* Fired during plugin deactivation
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class EnergyPlus_Deactivator {

  /**
  * Deactivate
  *
  * @since    1.0.0
  */

  public static function deactivate() {

    // Remove scheduled actions
    wp_clear_scheduled_hook('energyplus_cron_daily');

  }

}
