<?php

namespace ACA\BP\ListScreenFactory;

use AC;
use AC\ListScreenFactory\ListSettingsTrait;
use AC\ListScreenFactoryInterface;
use ACA\BP\ListScreen;
use LogicException;
use WP_Screen;

class Group implements ListScreenFactoryInterface {

	use ListSettingsTrait;

	public function can_create( string $key ): bool {
		return 'bp-groups' === $key;
	}

	public function create( string $key, array $settings = [] ): AC\ListScreen {
		if ( ! $this->can_create( $key ) ) {
			throw new LogicException( 'Invalid Listscreen key' );
		}

		return $this->add_settings( new ListScreen\Group(), $settings );
	}

	public function can_create_by_wp_screen( WP_Screen $screen ): bool {
		return $screen->id === 'toplevel_page_bp-groups' && 'edit' !== filter_input( INPUT_GET, 'action' );
	}

	public function create_by_wp_screen( WP_Screen $screen, array $settings = [] ): AC\ListScreen {
		if ( ! $this->can_create_by_wp_screen( $screen ) ) {
			throw new LogicException( 'Invalid Screen' );
		}

		return $this->add_settings( new ListScreen\Group(), $settings );
	}

}