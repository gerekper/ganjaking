<?php
declare( strict_types=1 );

namespace ACA\BeaverBuilder;

use AC\ListScreen;
use AC\ListScreenFactory\ListSettingsTrait;
use AC\ListScreenFactoryInterface;
use ACA\BeaverBuilder\ListScreen\Template;
use LogicException;
use WP_Screen;

abstract class ListScreenFactory implements ListScreenFactoryInterface {

	use ListSettingsTrait;

	private function create_list_screen(): Template {
		return new Template( $this->get_page(), $this->get_label() );
	}

	abstract protected function get_label(): string;

	abstract protected function get_page(): string;

	public function can_create( string $key ): bool {
		return $key === Template::POST_TYPE . $this->get_page() && post_type_exists( Template::POST_TYPE );
	}

	public function create( string $key, array $settings = [] ): ListScreen {
		if ( ! $this->can_create( $key ) ) {
			throw new LogicException( 'Invalid key' );
		}

		return $this->add_settings( $this->create_list_screen(), $settings );
	}

	public function can_create_by_wp_screen( WP_Screen $screen ): bool {
		return 'edit' === $screen->base
		       && $screen->post_type
		       && 'edit-' . $screen->post_type === $screen->id
		       && $this->get_page() === filter_input( INPUT_GET, 'fl-builder-template-type' );
	}

	public function create_by_wp_screen( WP_Screen $screen, array $settings = [] ): ListScreen {
		if ( ! $this->can_create_by_wp_screen( $screen ) ) {
			throw new LogicException( 'Invalid screen' );
		}

		return $this->add_settings( $this->create_list_screen(), $settings );
	}

}