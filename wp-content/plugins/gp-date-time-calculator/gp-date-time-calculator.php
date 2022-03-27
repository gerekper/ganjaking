<?php
/**
 * Plugin Name: GP Date Time Calculator
 * Description: Use Date and Time fields in your Gravity Forms calculation formulas to calculate the time between different dates and times.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-date-time-calculator/
 * Version: 1.0-beta-4.10
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com/
 * License: GPL2
 * Perk: True
 */

define( 'GP_DATE_TIME_CALCULATOR_VERSION', '1.0-beta-4.10' );

require 'includes/class-gp-bootstrap.php';
require 'includes/functions.php';

$gp_date_time_calculator_bootstrap = new GP_Bootstrap( 'class-gp-date-time-calculator.php', __FILE__ );
