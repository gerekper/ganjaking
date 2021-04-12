<?php
/**
 * Plugin Name: GP Limit Choices
 * Description: Limit how many times a choice may be selected for Radio Button, Drop Down and Checkbox fields.
 * Plugin URI: http://gravitywiz.com/
 * Version: 1.6.31
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Text Domain: gp-limit-choices
 * Domain Path: /languages
 * Perk: True
 */

define( 'GP_LIMIT_CHOICES_VERSION', '1.6.31' );

require 'includes/class-gp-bootstrap.php';

$gp_limit_choices_bootstrap = new GP_Bootstrap( 'class-gp-limit-choices.php', __FILE__ );
