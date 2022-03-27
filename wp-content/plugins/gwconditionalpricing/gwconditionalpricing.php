<?php
/**
 * Plugin Name: GP Conditional Pricing
 * Description: Create flexible, conditional pricing for your Gravity Form product fields.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-conditional-pricing/
 * Version: 1.3.9
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Update URI: https://gravitywiz.com/updates/gwconditionalpricing
 * Text Domain: gp-conditional-pricing
 * Domain Path: /languages
 */

define( 'GP_CONDITIONAL_PRICING_VERSION', '1.3.9' );

require 'includes/class-gp-bootstrap.php';

$gp_conditional_pricing_bootstrap = new GP_Bootstrap( 'class-gp-conditional-pricing.php', __FILE__ );