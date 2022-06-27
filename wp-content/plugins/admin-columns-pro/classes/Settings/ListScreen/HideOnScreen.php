<?php

namespace ACP\Settings\ListScreen;

use AC\ListScreen;

class HideOnScreen {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $label;

	public function __construct( $name, $label ) {
		$this->name = (string) $name;
		$this->label = (string) $label;
	}

	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return bool
	 */
	public function is_hidden( ListScreen $list_screen ) {
		return 'on' === $list_screen->get_preference( $this->name );
	}

	/**
	 * @return array
	 */
	public function get_dependent_on() {
		return [];
	}

}