<?php


namespace Premmerce\WooCommercePinterest\Frontend\Analytics\Event;

class PageVisitEvent extends AbstractEvent implements EventInterface {

	/**
	 * Return event status
	 *
	 * @return bool
	 */
	public function enabled() {
		return $this->isEnabledInOptions();
	}

	/**
	 * Return if event was fired
	 *
	 * @return bool
	 */
	public function fired() {
		return is_page() && ! is_checkout() && ! is_shop();
	}

	/**
	 * Return event name
	 *
	 * @return string
	 */
	public function getName() {
		return 'PageVisit';
	}

	/**
	 * Return data to be sent with analytics event
	 *
	 * @return array
	 */
	public function getData() {
		return array();
	}

	/**
	 * Return deferred status
	 * If event is deferred, it will be saved to transients and fired on next request handling
	 *
	 * @return bool
	 */
	public function isDeferred() {
		return false;
	}
}
