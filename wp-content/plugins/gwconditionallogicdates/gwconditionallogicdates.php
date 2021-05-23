<?php
/**
* Plugin Name: GP Conditional Logic Dates
* Description: Allows Date fields to be used in Gravity Forms conditional logic.
* Plugin URI: https://gravitywiz.com/documentation/gravity-forms-conditional-logic-dates/
* Version: 1.1
* Author: Gravity Wiz
* Author URI: http://gravitywiz.com/
* License: GPL2
* Perk: True
*/

define( 'GP_CONDITIONAL_LOGIC_DATES_VERSION', '1.1' );

require 'includes/class-gp-bootstrap.php';

$gp_conditional_logic_dates_bootstrap = new GP_Bootstrap( 'class-gp-conditional-logic-dates.php', __FILE__ );
