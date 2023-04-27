<?php
declare( strict_types=1 );

namespace ACP\ListScreenFactory;

use AC;
use AC\ListScreen;
use AC\ListScreenFactory\ListSettingsTrait;
use ACP\ListScreen\Taxonomy;
use LogicException;
use WP_Screen;

class TaxonomyFactory implements AC\ListScreenFactoryInterface {

	use ListSettingsTrait;

	public function can_create( string $key ): bool {
		return null !== $this->get_taxonomy( $key );
	}

	private function get_taxonomy( string $key ): ?string {
		if ( ! ac_helper()->string->starts_with( $key, 'wp-taxonomy_' ) ) {
			return null;
		}

		$taxonomy = substr( $key, 12 );

		return taxonomy_exists( $taxonomy )
			? $taxonomy
			: null;
	}

	public function can_create_by_wp_screen( WP_Screen $screen ): bool {
		return 'edit-tags' === $screen->base && $screen->taxonomy && $screen->taxonomy === filter_input( INPUT_GET, 'taxonomy' );
	}

	public function create( string $key, array $settings = [] ): ListScreen {
		if ( ! $this->can_create( $key ) ) {
			throw new LogicException( 'Invalid key' );
		}

		$taxonomy = $this->get_taxonomy( $key );

		if ( ! $taxonomy ) {
			throw new LogicException( 'Invalid taxonomy' );
		}

		return $this->add_settings( new Taxonomy( $taxonomy ), $settings );
	}

	public function create_by_wp_screen( WP_Screen $screen, array $settings = [] ): ListScreen {
		if ( ! $this->can_create_by_wp_screen( $screen ) ) {
			throw new LogicException( 'Invalid screen' );
		}

		return $this->add_settings( new Taxonomy( $screen->taxonomy ), $settings );
	}

}