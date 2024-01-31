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
 * @copyright   Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Contracts;

defined( 'ABSPATH' ) or exit;

/**
 * Tracking event contract.
 *
 * Indicates that this is an event that can be tracked.
 *
 * @since 2.0.0
 */
interface Tracking_Event {


	/**
	 * Sets the event name.
	 *
	 * @since 2.0.0
	 *
	 * @param string $name
	 * @return $this
	 */
	public function set_name( string $name = '' ) : Tracking_Event;


	/**
	 * Gets the event name.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_name(): string;


	/**
	 * Gets the default name for the event.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_default_name() : string;


	/**
	 * Checks whether the event is enabled or not.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_enabled(): bool;


	/**
	 * Checks whether this event is an admin event.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_admin_event() : bool;


	/**
	 * Registers event callbacks with action hooks.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_hooks() : void;


	/**
	 * Tracks the event.
	 *
	 * @internal
	 *
	 * @return void
	 */
	public function track() : void;


	/**
	 * Gets the settings form field for the event.
	 *
	 * @see Integration::get_event_name_fields()
	 *
	 * @internal
	 * @since 2.0.0
	 *
	 * @return string[]
	 */
	public function get_form_field() : array;


	/**
	 * Gets the settings form field description for the event.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_form_field_type() : string;


	/**
	 * Gets the settings form field title for the event.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_form_field_title() : string;


	/**
	 * Gets the settings form field description for the event.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_form_field_description() : string;


}
