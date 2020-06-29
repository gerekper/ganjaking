<?php


namespace Premmerce\WooCommercePinterest\Frontend\Analytics\Event;

use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Analytics;

abstract class AbstractEvent implements EventInterface {

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $integration;

	/**
	 * AbstractEvent constructor.
	 *
	 * @param PinterestIntegration $integration
	 */
	public function __construct( PinterestIntegration $integration) {

		$this->integration = $integration;
	}

	/**
	 * Return is event enabled in options
	 *
	 * @return bool
	 */
	public function isEnabledInOptions() {
		$eventName = $this->getName();

		return $this->integration->get_option('event_' . $eventName) === 'yes';
	}

	public function trigger() {

		$data   = wp_json_encode($this->getData());
		$script = "pintrk('track', '{$this->getName()}', {$data});";

		wp_add_inline_script(Analytics::PINTEREST_ANALYTICS_INIT_SCRIPT_HANDLE, $script);
	}
}
