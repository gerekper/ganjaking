<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

// load helper functions
require_once( wc_memberships()->get_plugin_path() . '/src/functions/wc-memberships-functions-dates.php' );
require_once( wc_memberships()->get_plugin_path() . '/src/functions/wc-memberships-functions-misc.php' );
require_once( wc_memberships()->get_plugin_path() . '/src/functions/wc-memberships-functions-orders.php' );
require_once( wc_memberships()->get_plugin_path() . '/src/functions/wc-memberships-functions-membership-plans.php' );
require_once( wc_memberships()->get_plugin_path() . '/src/functions/wc-memberships-functions-user-memberships.php' );
require_once( wc_memberships()->get_plugin_path() . '/src/functions/wc-memberships-functions-profile-fields.php' );

// load template functions (loaded everywhere as we may need to use them in AJAX or Email contexts)
require_once( wc_memberships()->get_plugin_path() . '/src/functions/wc-memberships-functions-restrictions.php' );
require_once( wc_memberships()->get_plugin_path() . '/src/functions/wc-memberships-functions-member-discounts.php' );
require_once( wc_memberships()->get_plugin_path() . '/src/functions/wc-memberships-functions-members-area.php' );
