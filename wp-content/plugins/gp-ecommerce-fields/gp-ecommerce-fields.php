<?php
/**
 * Plugin Name: GP eCommerce Fields
 * Description: Make Gravity Forms more eCommerce-friendly with support for Tax, Discounts, and Subtotal fields.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-ecommerce-fields/
 * Version: 1.0.29
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Text Domain: gp-ecommerce-fields
 * Domain Path: /languages
 */

define( 'GP_ECOMMERCE_FIELDS_VERSION', '1.0.29' );

require 'includes/class-gp-bootstrap.php';

$gp_ecommerce_fields_bootstrap = new GP_Bootstrap( 'class-gp-ecommerce-fields.php', __FILE__ );