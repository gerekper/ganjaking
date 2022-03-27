<?php
/**
 * Plugin Name: GP Address Autocomplete
 * Description: Improve user experience and increase form conversion by adding address autocomplete.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-address-autocomplete
 * Version: 1.1.11
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Update URI: https://gravitywiz.com/updates/gp-address-autocomplete
 * Text Domain: gp-address-autocomplete
 * Domain Path: /languages
 */

define( 'GP_ADDRESS_AUTOCOMPLETE_VERSION', '1.1.11' );

require plugin_dir_path( __FILE__ ) . 'includes/class-gp-bootstrap.php';

$GP_Address_Autocomplete_Bootstrap = new GP_Bootstrap( 'class-gp-address-autocomplete.php', __FILE__ );
