<?php

namespace WCML\Importer;

use WPML\LIB\WP\Hooks;

use function WPML\FP\spreadArgs;
use function WCML\functions\getId;
use function WCML\functions\getWooCommerceWpml;

class Products implements \IWPML_Backend_Action {

	public function add_hooks() {
		Hooks::onAction( 'woocommerce_product_import_inserted_product_object' )
			->then( spreadArgs( [ $this, 'synchronizeProducts' ] ) );
	}

	/**
	 * @param \WC_Product $product 
	 */
	public function synchronizeProducts( $product ) {
		do_action( 'wpml_sync_all_custom_fields', getId( $product ) );

		getWooCommerceWpml()->sync_product_data->synchronize_products( getId( $product ), get_post( getId( $product ) ), true );
	}

}
