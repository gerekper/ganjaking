<?php
/**
 * WooCommerce Google Analytics Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Contracts;

defined( 'ABSPATH' ) or exit;

/**
 * Deferrable AJAX event interface.
 *
 * Indicates that an event has a deferred frontend trigger that makes an AJAX call to track the event.
 *
 * @since 2.0.0
 */
interface Deferred_AJAX_Event extends Deferred_Event {


	/**
	 * Triggers the event via AJAX.
	 *
	 * Sends an AJAX request on the trigger (provided by `get_trigger_js()`) with the data provided by the trigger.
	 *
	 * @since 2.0.0
	 *
	 * @internal
	 *
	 * @return void
	 */
	public function trigger_via_ajax(): void;


}
