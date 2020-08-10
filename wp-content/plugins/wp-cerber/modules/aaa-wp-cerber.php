<?php
/*
	Plugin URI: https://wpcerber.com
	Description: This is a standard boot module for WP Cerber Security & Antispam plugin. It was installed when you set the plugin initialization mode to Standard. Know more: <a href="https://wpcerber.com">wpcerber.com</a>.
	Author: Cerber Tech Inc.
	Author URI: https://wpcerber.com
	Version: 1.0
	Text Domain: wp-cerber
	Network: true

	Copyright (C) 2015-20 CERBER TECH INC., https://cerber.tech
	Copyright (C) 2015-20 CERBER TECH INC., https://wpcerber.com

	Licenced under the GNU GPL.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

// If this file is called directly, abort executing.
if ( ! defined( 'WPINC' ) ) {
	exit;
}

function cerber_mode(){
	return 1;
}

if ( ( @include_once WP_PLUGIN_DIR . '/wp-cerber/wp-cerber.php' ) == true ) {
	define( 'CERBER_MODE', 1 );
}