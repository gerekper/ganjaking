<?php

namespace ACA\WC\Editing\ProductVariation;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACA\WC\Editing\Storage;
use ACP;
use ACP\Editing\View;
use WC_Product_Variation;

class Downloadable implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Toggle(
			new ToggleOptions(
				new Option( 'yes' ), new Option( 'no' )
			)
		);
	}

	public function get_value( $id ) {
		$variation = new WC_Product_Variation( $id );

		return $variation->get_downloadable() ? 'yes' : 'no';
	}

	public function update( int $id, $data ): void {
		$variation = new WC_Product_Variation( $id );
		$variation->set_downloadable( $data );
		$variation->save() > 0;
	}

}
