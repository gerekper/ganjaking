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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Contracts\Tracking_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The base Event class.
 *
 * @since 2.0.0
 */
abstract class Event implements Tracking_Event {


	/** @var string the event name */
	protected string $name = '';

	/** @var bool whether this is an admin event */
	protected bool $admin_event = false;

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = '';


	/**
	 * @inheritdoc
	 *
	 * @since 2.0.0
	 */
	public function set_name( string $name = '' ) : Event {

		$this->name = $name;

		return $this;
	}


	/**
	 * @inheritdoc
	 *
	 * @since 2.0.0
	 */
	public function get_name(): string {

		return $this->name;
	}


	/**
	 * @inheritdoc
	 *
	 * @since 2.0.0
	 */
	public function is_admin_event() : bool {

		return $this->admin_event;
	}


	/**
	 * @inheritdoc
	 *
	 * @since 2.0.0
	 */
	public function is_enabled(): bool {

		return ! empty( $this->get_name() );
	}


	/**
	 * @inheritdoc
	 *
	 * @since 2.0.0
	 */
	public function get_form_field() : array {

		return [
			'title'       => $this->get_form_field_title(),
			'description' => $this->get_form_field_description(),
			'type'        => $this->get_form_field_type(),
			'default'     => $this->get_default_name(),
		];
	}


	/**
	 * @inheritdoc
	 *
	 * @since 2.0.0
	 */
	public function get_form_field_type() : string {

		return 'event_name';
	}


	/**
	 * @inheritdoc
	 *
	 * @since 2.0.0
	 */
	public function register_hooks() : void {

		if ( ! $this->trigger_hook ) {
			return;
		}

		add_action( $this->trigger_hook, [ $this, 'track' ] );
	}


}
