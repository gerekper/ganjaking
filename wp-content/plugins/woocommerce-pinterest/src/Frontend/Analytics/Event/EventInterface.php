<?php
namespace Premmerce\WooCommercePinterest\Frontend\Analytics\Event;

interface EventInterface {
	/**
	 * Return event status
	 *
	 * @return bool
	 */
	public function enabled();

	/**
	 * Return event was fired
	 *
	 * @return bool
	 */
	public function fired();

	/**
	 * Return event name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Return data to be sent with analytics event
	 *
	 * @return array
	 */
	public function getData();

	/**
	 * Set event data, should be used to restore deferred events.
	 *
	 * @param array $data
	 *
	 */
	public function setData( array $data);

	/**
	 * Return deferred status
	 * If event is deferred, it will be saved to transients and fired on next request handling
	 *
	 * @return bool
	 */
	public function isDeferred();

	/**
	 * Trigger
	 *
	 * @return void
	 */
	public function trigger();
}
