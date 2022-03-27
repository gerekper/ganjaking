<?php
/**
 * Plugin Name: GP Reload Form
 * Description: Reload the form following an AJAX submission. Useful in situations where you would like to allow multiple form submission with refreshing the page.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-reload-form/
 * Version: 2.0.7
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Update URI: https://gravitywiz.com/updates/gwreloadform
 */

define( 'GP_RELOAD_FORM_VERSION', '2.0.7' );

require 'includes/class-gp-bootstrap.php';

$gp_reload_form_bootstrap = new GP_Bootstrap( 'class-gp-reload-form.php', __FILE__ );
