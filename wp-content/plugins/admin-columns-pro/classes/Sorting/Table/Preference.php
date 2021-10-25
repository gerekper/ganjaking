<?php

namespace ACP\Sorting\Table;

use AC;

class Preference {

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var string 'asc' or 'desc'
	 */
	private $order;

	/**
	 * @var string
	 */
	private $order_by;

	/**
	 * @var AC\Preferences\Site
	 */
	private $preferences;

	/**
	 * @param string              $key
	 * @param AC\Preferences\Site $preferences
	 */
	public function __construct( $key, AC\Preferences\Site $preferences ) {
		$this->key = $key;
		$this->preferences = $preferences;

		$this->set_order( $this->get_value( 'order' ) );
		$this->set_order_by( $this->get_value( 'orderby' ) );
	}

	/**
	 * @return string
	 */
	public function get_order() {
		return $this->order;
	}

	/**
	 * @return string
	 */
	public function get_order_by() {
		return $this->order_by;
	}

	/**
	 * @param string $order
	 *
	 * @return $this
	 */
	public function set_order( $order ) {
		$this->order = $order === 'desc' ? 'desc' : 'asc';

		return $this;
	}

	/**
	 * @param string $order_by
	 *
	 * @return $this
	 */
	public function set_order_by( $order_by ) {
		$this->order_by = $order_by;

		return $this;
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	private function get_value( $key ) {
		$data = $this->get();

		if ( empty( $data ) || ! array_key_exists( $key, $data ) ) {
			return false;
		}

		return $data[ $key ];
	}

	/**
	 * @return array
	 */
	private function get() {
		return $this->preferences->get( $this->key );
	}

	/**
	 * @return bool
	 */
	public function delete() {
		return $this->preferences->delete( $this->key );
	}

	/**
	 * @return bool
	 */
	public function save() {
		$this->preferences->set( $this->key, [
			'order'   => $this->get_order(),
			'orderby' => $this->get_order_by(),
		] );

		return $this->preferences->save();
	}

}