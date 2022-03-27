<?php
/**
 * Plugin Name: GP Inventory
 * Description: Easy, flexible inventory management for Gravity Forms.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-inventory/
 * Version: 1.0-beta-2.7
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Update URI: https://gravitywiz.com/updates/gp-inventory
 * Text Domain: gp-inventory
 * Domain Path: /languages
 */

define( 'GP_INVENTORY_VERSION', '1.0-beta-2.7' );

require plugin_dir_path( __FILE__ ) . 'includes/class-gp-bootstrap.php';

$GP_Inventory_bootstrap = new GP_Bootstrap( 'class-gp-inventory.php', __FILE__ );
