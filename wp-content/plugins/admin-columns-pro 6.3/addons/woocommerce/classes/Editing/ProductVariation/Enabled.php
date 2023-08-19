<?php

namespace ACA\WC\Editing\ProductVariation;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACA\WC\Editing\Storage;
use ACP;
use ACP\Editing\View;
use WC_Product_Variation;

class Enabled implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Toggle(
			new ToggleOptions( new Option( 'private' ), new Option( 'publish' ) )
		);
	}

	public function get_value( $id ) {
		return ( new WC_Product_Variation( $id ) )->get_status();
	}

	public function update( int $id, $data ): void {
		$variation = new WC_Product_Variation( $id );
		$variation->set_status( $data );
		$variation->save();
	}

}