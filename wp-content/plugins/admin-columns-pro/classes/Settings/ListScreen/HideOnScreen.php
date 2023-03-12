<?php

namespace ACP\Settings\ListScreen;

use AC\ListScreen;

class HideOnScreen {

	protected $name;

	protected $label;

	protected $dependent_on;

	public function __construct( string $name, string $label, string $dependent_on = null ) {
		$this->name = $name;
		$this->label = $label;
		$this->dependent_on = $dependent_on;
	}

	public function get_name(): string {
		return $this->name;
	}

	public function get_label(): string {
		return $this->label;
	}

	public function is_hidden( ListScreen $list_screen ): bool {
		return 'on' === $list_screen->get_preference( $this->name );
	}

	public function has_dependent_on(): bool {
		return null !== $this->dependent_on;
	}

	public function get_dependent_on(): string {
		return $this->dependent_on;
	}

}