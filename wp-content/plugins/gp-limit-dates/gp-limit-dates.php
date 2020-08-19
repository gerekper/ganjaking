<?php
/**
 * Plugin Name: GP Limit Dates
 * Description: Limit which days are selectable for your Gravity Forms Date Picker fields.
 * Plugin URI: http://gravitywiz.com/documentation/gp-limit-dates/
 * Version: 1.0.20
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Text Domain: gp-limit-dates
 * Domain Path: /languages
 */

define( 'GP_LIMIT_DATES_VERSION', '1.0.20' );

require 'includes/class-gp-bootstrap.php';

$gp_limit_dates_bootstrap = new GP_Bootstrap( 'class-gp-limit-dates.php', __FILE__ );