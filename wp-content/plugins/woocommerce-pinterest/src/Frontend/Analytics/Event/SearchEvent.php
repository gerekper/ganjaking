<?php


namespace Premmerce\WooCommercePinterest\Frontend\Analytics\Event;

class SearchEvent extends AbstractEvent implements EventInterface {


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
		return is_search();
	}

	/**
	 * Return event name
	 *
	 * @return string
	 */
	public function getName() {
		return 'Search';
	}

	/**
	 * Return data to be sent with analytics event
	 *
	 * @return array
	 */
	public function getData() {
		return array('search_query' => get_search_query());
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
