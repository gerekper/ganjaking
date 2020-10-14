<?php
/**
 * Plugin Name: GP Easy Passthrough
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-easy-passthrough/
 * Description: Easily transfer entry values from one Gravity Forms form to another.
 * Version: 1.4.17
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * Text Domain: gp-easy-passthrough
 * Domain Path: /languages
 * Perk: true
 **/

define( 'GPEP_VERSION', '1.4.17' );

require plugin_dir_path( __FILE__ ) . 'includes/class-gp-bootstrap.php';

$gp_easy_passthrough_bootstrap = new GP_Bootstrap( 'class-gp-easy-passthrough.php', __FILE__ );
