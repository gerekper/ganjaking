<?php
/**
 * Plugin Name: GP Nested Forms
 * Description: Create forms within forms for better management of complex forms. Formception!
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-nested-forms/
 * Version: 1.0-beta-8.63
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com
 * License: GPL2
 * Perk: True
 * Text Domain: gp-nested-forms
 * Domain Path: /languages
 */

define( 'GP_NESTED_FORMS_VERSION', '1.0-beta-8.63' );

require 'includes/class-gp-bootstrap.php';

$gp_nested_forms_bootstrap = new GP_Bootstrap( 'class-gp-nested-forms.php', __FILE__ );
