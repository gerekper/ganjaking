<?php

/**
* Plugin Name: GP Auto Login
* Description: Automatically log users in after registration.
* Plugin URI: http://gravitywiz.com/
* Version: 1.3.5
* Author: Gravity Wiz
* Author URI: http://gravitywiz.com/
* License: GPL2
* Perk: True
*/

define( 'GP_AUTO_LOGIN_VERSION', '1.3.5' );

require 'includes/class-gp-bootstrap.php';

$gp_auto_login_bootstrap = new GP_Bootstrap( 'class-gp-auto-login.php', __FILE__ );