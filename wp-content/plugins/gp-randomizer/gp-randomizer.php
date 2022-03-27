<?php
/**
 * Plugin Name: GP Randomizer
 * Description: Randomize choice order in Gravity Forms choice-based fields.
 * Plugin URI: https://github.com/gravitywiz/gp-randomizer
 * Version: 1.0.2
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Text Domain: gp-randomizer
 * Domain Path: /languages
 */

define( 'GP_RANDOMIZER_VERSION', '1.0.2' );

require plugin_dir_path( __FILE__ ) . 'includes/class-gp-bootstrap.php';

$GP_Perk_Scaffold_bootstrap = new GP_Bootstrap( 'class-gp-randomizer.php', __FILE__ );
