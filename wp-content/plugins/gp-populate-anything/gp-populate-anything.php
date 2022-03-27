<?php
/**
 * Plugin Name: GP Populate Anything
 * Description: Populate fields from posts, users, entries, or databases.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-populate-anything/
 * Version: 1.2.6
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Update URI: https://gravitywiz.com/updates/gp-populate-anything
 * Text Domain: gp-populate-anything
 * Domain Path: /languages
 */

define( 'GPPA_VERSION', '1.2.6' );

require plugin_dir_path( __FILE__ ) . 'includes/class-gp-bootstrap.php';
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-object-type.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-object-type-post.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-object-type-term.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-object-type-user.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-object-type-gf-entry.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-object-type-database.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-compatibility-gravityview.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-compatibility-gravityflow.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-compatibility-gravitypdf.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-compatibility-jetsloth-image-choices.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-compatibility-wc-product-addons.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-live-merge-tags.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-gppa-export.php';

$gp_populate_anything_bootstrap = new GP_Bootstrap( 'class-gp-populate-anything.php', __FILE__ );
