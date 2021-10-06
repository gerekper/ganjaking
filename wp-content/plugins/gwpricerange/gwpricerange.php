<?php
/**
* Plugin Name: GP Price Range
* Description: Specify a minimum/maximum price for "User Defined Price" product fields.
* Plugin URI: https://gravitywiz.com/documentation/gravity-forms-price-range/
* Version: 1.2.1
* Author: Gravity Wiz
* Author URI: http://gravitywiz.com/
* License: GPL2
* Perk: True
*/

require 'includes/class-gp-bootstrap.php';

$gp_price_range_bootstrap = new GP_Bootstrap( 'class-gp-price-range.php', __FILE__ );
