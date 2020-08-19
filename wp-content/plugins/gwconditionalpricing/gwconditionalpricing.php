<?php
/**
 * Plugin Name: GP Conditional Pricing
 * Description: Create flexible, conditional pricing for your Gravity Form product fields.
 * Plugin URI: http://gravitywiz/documentation/gravity-forms-conditional-pricing/
 * Version: 1.2.37
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Text Domain: gp-conditional-pricing
 * Domain Path: /languages
 */

define( 'GP_CONDITIONAL_PRICING_VERSION', '1.2.37' );

require 'includes/class-gp-bootstrap.php';

$gp_conditional_pricing_bootstrap = new GP_Bootstrap( 'class-gp-conditional-pricing.php', __FILE__ );