<?php

namespace ACA\WC\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory\ListSettingsTrait;
use AC\ListScreenFactoryInterface;
use ACA\WC\ListScreen\ProductCategory;
use ACP\ListScreen\Taxonomy;
use LogicException;
use WP_Screen;

class ProductCategoryFactory implements ListScreenFactoryInterface {

	use ListSettingsTrait;

	public function can_create( string $key ): bool {
		return Taxonomy::KEY_PREFIX . 'product_cat' === $key;
	}

	public function create( string $key, array $settings = [] ): ListScreen {
		if ( ! $this->can_create( $key ) ) {
			throw new LogicException( 'Invalid Listscreen key' );
		}

		return $this->add_settings( new ProductCategory(), $settings );
	}

	public function can_create_by_wp_screen( WP_Screen $screen ): bool {
		return 'edit-tags' === $screen->base && 'product_cat' === $screen->taxonomy;
	}

	public function create_by_wp_screen( WP_Screen $screen, array $settings = [] ): ListScreen {
		if ( ! $this->can_create_by_wp_screen( $screen ) ) {
			throw new LogicException( 'Invalid Screen' );
		}

		return $this->add_settings( new ProductCategory(), $settings );
	}

}