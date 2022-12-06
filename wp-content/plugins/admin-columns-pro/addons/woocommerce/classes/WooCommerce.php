<?php

namespace ACA\WC;

use AC;
use AC\Registerable;
use ACA\WC\QuickAdd;
use ACP;

final class WooCommerce implements Registerable {

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return;
		}

		ACP\QuickAdd\Model\Factory::add_factory( new QuickAdd\Factory() );

		$services = [
			new TableScreen( $this->location, $this->use_product_variations() ),
			new Subscriptions(),
			new Rounding(),
			new Admin( $this->location ),
			new Service\Columns(),
			new Service\Editing(),
			new Service\QuickAdd(),
			new Service\Table(),
			new Service\ColumnGroups(),
			new Service\ListScreenGroups(),
			new Service\ListScreens( $this->use_product_variations() ),
			new ACP\Service\Templates( $this->location->get_path() ),
			new ACP\Service\IntegrationStatus( 'ac-addon-woocommerce' ),
		];

		if ( $this->use_product_variations() ) {
			$services[] = new PostType\ProductVariation( $this->location );
		}

		array_map(
			static function ( Registerable $service ) {
				$service->register();
			},
			$services
		);
	}

	/**
	 * @return bool
	 */
	private function use_product_variations() {
		return apply_filters( 'acp/wc/show_product_variations', true ) && $this->is_wc_version_gte( '3.3' );
	}

	/**
	 * @param string $version
	 *
	 * @return bool
	 */
	private function is_wc_version_gte( $version ) {
		return version_compare( WC()->version, (string) $version, '>=' );
	}

}