<?php
declare( strict_types=1 );

namespace ACP\ListScreenFactory;

use AC;
use AC\ListScreen;
use AC\ListScreenFactory\ListSettingsTrait;
use ACP\ListScreen\MSSite;
use LogicException;
use WP_Screen;

class MSSiteFactory implements AC\ListScreenFactoryInterface {

	use ListSettingsTrait;

	public function can_create( string $key ): bool {
		return 'wp-ms_sites' === $key;
	}

	public function can_create_by_wp_screen( WP_Screen $screen ): bool {
		return 'sites-network' === $screen->base && 'sites-network' === $screen->id && $screen->in_admin( 'network' );
	}

	public function create( string $key, array $settings = [] ): ListScreen {
		if ( ! $this->can_create( $key ) ) {
			throw new LogicException( 'Invalid key' );
		}

		return $this->add_settings( new MSSite(), $settings );
	}

	public function create_by_wp_screen( WP_Screen $screen, array $settings = [] ): ListScreen {
		if ( ! $this->can_create_by_wp_screen( $screen ) ) {
			throw new LogicException( 'Invalid screen' );
		}

		return $this->add_settings( new MSSite(), $settings );
	}

}