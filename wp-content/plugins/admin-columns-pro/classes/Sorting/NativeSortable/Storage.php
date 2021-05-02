<?php

namespace ACP\Sorting\NativeSortable;

class Storage {

	const OPTIONS_KEY = 'ac_sorting';

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @param string $list_screen_key
	 */
	public function __construct( $list_screen_key ) {
		$this->key = sprintf( "%s_%s_default", self::OPTIONS_KEY, $list_screen_key );
	}

	/**
	 * @param array $columns
	 *
	 * @return void
	 */
	public function update( array $columns ) {
		update_option( $this->key, $columns, false );
	}

	/**
	 * @return bool
	 */
	public function exists() {
		return false !== get_option( $this->key );
	}

	/**
	 * @return array
	 */
	public function get() {
		return get_option( $this->key, [] );
	}

	/**
	 * @return void
	 */
	public function delete() {
		delete_option( $this->key );
	}

}