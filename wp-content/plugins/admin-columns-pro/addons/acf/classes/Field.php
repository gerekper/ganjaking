<?php

namespace ACA\ACF;

use http\Exception\InvalidArgumentException;

class Field {

	protected $settings;

	public function __construct( array $settings ) {
		$this->settings = $settings;

		$this->validate();
	}

	public function validate(): void {
		if ( ! isset( $this->settings['label'], $this->settings['type'], $this->settings['name'], $this->settings['key'] ) ) {
			throw new InvalidArgumentException( 'Missing field argument.' );
		}
	}

	public function is_required(): bool {
		return isset( $this->settings['required'] ) && $this->settings['required'];
	}

	public function get_settings(): array {
		return $this->settings;
	}

	public function get_label() {
		return $this->settings['label'];
	}

	public function get_type() {
		return $this->settings['type'];
	}

	public function get_meta_key() {
		return $this->settings['name'];
	}

	public function get_hash() {
		return $this->settings['key'];
	}

}