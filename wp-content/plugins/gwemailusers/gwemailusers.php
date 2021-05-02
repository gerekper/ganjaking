<?php
/**
 * Plugin Name: GP Email Users
 * Description: Send a quick email to all users who have submitted a specific form.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-email-users/
 * Version: 1.3.11
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Text Domain: gp-email-users
 * Domain Path: /languages
 * Perk: True
 */

define( 'GP_EMAIL_USERS_VERSION', '1.3.11' );

require 'includes/class-gp-bootstrap.php';

$gp_email_users_bootstrap = new GP_Bootstrap( 'class-gp-email-users.php', __FILE__ );