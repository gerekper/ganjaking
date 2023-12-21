<?php

/**
 * Plugin Name: Happy Elementor Addons
 * Plugin URI: https://happyaddons.com/
 * Description: <a href="https://happyaddons.com/">Happy Addons for Elementor</a> Is the Best Elementor Addons Comes With 44+ Free Elementor Widgets Including Table Builder, Testimonial, Event Calendar,Slider,News Ticker, Image Grid, etc & Features Like Elementor Equal Height, Text Stroke, Shape Dividers, Floating Effect, Grid Layout, 500+ Elementor Icons, 450+ Template Packs & More.
 * Version: 3.10.0
 * Author: Leevio
 * Author URI: https://happyaddons.com/
 * Elementor tested up to: 3.18
 * Elementor Pro tested up to: 3.18
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: happy-elementor-addons
 * Domain Path: /i18n/
 *
 * @package Happy_Addons
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2019 HappyMonster <http://happymonster.me>
*/

defined('ABSPATH') || die();


if ( defined( 'HAPPY_ADDONS_DEV' ) && true == HAPPY_ADDONS_DEV ) {
	define('HAPPY_ADDONS_VERSION', time() );
} else {
	define('HAPPY_ADDONS_VERSION', '3.10.0');
}
define('HAPPY_ADDONS__FILE__', __FILE__);
define('HAPPY_ADDONS_DIR_PATH', plugin_dir_path(HAPPY_ADDONS__FILE__));
define('HAPPY_ADDONS_DIR_URL', plugin_dir_url(HAPPY_ADDONS__FILE__));
define('HAPPY_ADDONS_ASSETS', trailingslashit(HAPPY_ADDONS_DIR_URL . 'assets'));
define('HAPPY_ADDONS_REDIRECTION_FLAG', 'happyaddons_do_activation_direct');
define('HAPPY_ADDONS_WIZARD_REDIRECTION_FLAG', 'happyaddons_do_wizard_direct');

define('HAPPY_ADDONS_MINIMUM_ELEMENTOR_VERSION', '3.5.0');
define('HAPPY_ADDONS_MINIMUM_PHP_VERSION', '5.6');

/**
 * The journey of a thousand miles starts here.
 *
 * @return void Some voids are not really void, you have to explore to figure out why not!
 */
function ha_let_the_journey_begin() {
    require(HAPPY_ADDONS_DIR_PATH . 'inc/functions.php');

    // Check for required PHP version
    if (version_compare(PHP_VERSION, HAPPY_ADDONS_MINIMUM_PHP_VERSION, '<')) {
        add_action('admin_notices', 'ha_required_php_version_missing_notice');
        return;
    }

    // Check if Elementor installed and activated
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', 'ha_elementor_missing_notice');
        return;
    }

    // Check for required Elementor version
    if (!version_compare(ELEMENTOR_VERSION, HAPPY_ADDONS_MINIMUM_ELEMENTOR_VERSION, '>=')) {
        add_action('admin_notices', 'ha_required_elementor_version_missing_notice');
        return;
    }

    require HAPPY_ADDONS_DIR_PATH . 'base.php';
    \Happy_Addons\Elementor\Base::instance();
}

add_action('plugins_loaded', 'ha_let_the_journey_begin');

/**
 * Admin notice for required php version
 *
 * @return void
 */
function ha_required_php_version_missing_notice() {
    $notice = sprintf(
        /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
        esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'happy-elementor-addons'),
        '<strong>' . esc_html__('Happy Elementor Addons', 'happy-elementor-addons') . '</strong>',
        '<strong>' . esc_html__('PHP', 'happy-elementor-addons') . '</strong>',
        HAPPY_ADDONS_MINIMUM_PHP_VERSION
    );

    printf('<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice);
}

/**
 * Admin notice for elementor if missing
 *
 * @return void
 */
function ha_elementor_missing_notice() {

    if (file_exists(WP_PLUGIN_DIR . '/elementor/elementor.php')) {
        $notice_title = __('Activate Elementor', 'happy-elementor-addons');
        $notice_url = wp_nonce_url('plugins.php?action=activate&plugin=elementor/elementor.php&plugin_status=all&paged=1', 'activate-plugin_elementor/elementor.php');
    } else {
        $notice_title = __('Install Elementor', 'happy-elementor-addons');
        $notice_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
    }

    $notice = ha_kses_intermediate(sprintf(
        /* translators: 1: Plugin name 2: Elementor 3: Elementor installation link */
        __('%1$s requires %2$s to be installed and activated to function properly. %3$s', 'happy-elementor-addons'),
        '<strong>' . __('Happy Elementor Addons', 'happy-elementor-addons') . '</strong>',
        '<strong>' . __('Elementor', 'happy-elementor-addons') . '</strong>',
        '<a href="' . esc_url($notice_url) . '">' . $notice_title . '</a>'
    ));

    printf('<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice);
}

/**
 * Admin notice for required elementor version
 *
 * @return void
 */
function ha_required_elementor_version_missing_notice() {

    $notice_title = __('Update Elementor', 'happy-elementor-addons');
    $notice_url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=elementor/elementor.php'), 'upgrade-plugin_elementor/elementor.php');

    $notice = sprintf(
        /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
        esc_html__('"%1$s" requires "%2$s" version %4$s or greater. %3$s', 'happy-elementor-addons'),
        '<strong>' . esc_html__('Happy Elementor Addons', 'happy-elementor-addons') . '</strong>',
        '<strong>' . esc_html__('Elementor', 'happy-elementor-addons') . '</strong>',
        '<a href="' . esc_url($notice_url) . '">' . $notice_title . '</a>',
        HAPPY_ADDONS_MINIMUM_ELEMENTOR_VERSION
    );

    printf('<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice);
}

/**
 * Register actions that should run on activation
 *
 * @return void
 */
function ha_register_activation_hook() {
    add_option(HAPPY_ADDONS_REDIRECTION_FLAG, true);

    // add plugin activation time
    $get_activation_time = strtotime("now");
    add_option('happy_addons_activation_time', $get_activation_time);

    if (!get_option(HAPPY_ADDONS_WIZARD_REDIRECTION_FLAG, false)) {
        add_option(HAPPY_ADDONS_WIZARD_REDIRECTION_FLAG, 'initiate');
    }
}

register_activation_hook(HAPPY_ADDONS__FILE__, 'ha_register_activation_hook');
