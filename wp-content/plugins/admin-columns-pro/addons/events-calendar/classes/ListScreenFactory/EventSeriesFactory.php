<?php

namespace ACA\EC\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory\ListSettingsTrait;
use AC\ListScreenFactoryInterface;
use ACA\EC\ListScreen\EventSeries;
use LogicException;
use WP_Screen;

class EventSeriesFactory implements ListScreenFactoryInterface {

	use ListSettingsTrait;

	public function can_create( string $key ): bool {
		return 'tribe_event_series' === $key;
	}

	public function create( string $key, array $settings = [] ): ListScreen {
		if ( ! $this->can_create( $key ) ) {
			throw new LogicException( 'Invalid Listscreen key' );
		}

		return $this->add_settings( new EventSeries(), $settings );
	}

	public function can_create_by_wp_screen( WP_Screen $screen ): bool {
		return $screen->base === 'edit' && $screen->post_type === 'tribe_event_series';
	}

	public function create_by_wp_screen( WP_Screen $screen, array $settings = [] ): ListScreen {
		if ( ! $this->can_create_by_wp_screen( $screen ) ) {
			throw new LogicException( 'Invalid Screen' );
		}

		return $this->add_settings( new EventSeries(), $settings );
	}

}