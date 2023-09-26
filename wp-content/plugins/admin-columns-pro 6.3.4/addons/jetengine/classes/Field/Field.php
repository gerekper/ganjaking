<?php

namespace ACA\JetEngine\Field;

class Field {

	/**
	 * @var array
	 */
	protected $settings;

	public function __construct( $settings ) {
		$this->settings = $settings;
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return $this->settings['type'];
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return (string) $this->settings['title'];
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return (string) $this->settings['name'];
	}

	/**
	 * @return bool
	 */
	public function is_required() {
		return isset( $this->settings['is_required'] ) && $this->settings['is_required'];
	}

}