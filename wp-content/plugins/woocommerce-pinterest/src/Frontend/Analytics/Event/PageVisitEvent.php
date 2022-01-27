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
		return is_product();
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

		if ($this->data) {
			return $this->data;
		}

		$data = array();

		if ( is_product() ) {
			global $post;

			$data['product_id'] = $post->ID;
		}

		return $data;
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
