<?php
/**
* Plugin Name: WooSlider
* Plugin URI: http://woothemes.com/products/wooslider/
* Description: Add responsive slideshows to your website using shortcodes, template tags or widgets. Showcase custom slides, blog posts or other content in a responsive animated slideshow.
* Version: 2.5.0
* Author: WooThemes
* Author URI: http://woothemes.com/
* WC tested up to: 5.0
* Tested up to: 5.6
* License: GPL version 3 or later - http://www.gnu.org/licenses/old-licenses/gpl-3.0.html
* Woo: 46506:209d98f3ccde6cc3de7e8732a2b20b6a
*/
/*  Copyright 2012  WooThemes  (email : info@woothemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	require_once( 'classes/class-wooslider.php' );
	if ( ! is_admin() ) require_once( 'inc/wooslider-template.php' );

    if ( ! function_exists( 'woothemes_queue_update' ) )
        require_once( 'inc/woo-functions.php' );

    /* Integrate with the WooThemes Updater plugin for plugin updates. */
    woothemes_queue_update( plugin_basename( __FILE__ ), '209d98f3ccde6cc3de7e8732a2b20b6a', '46506' );

	global $wooslider;
	$wooslider = new WooSlider( __FILE__ );
	$wooslider->version = '2.4.3';
?>