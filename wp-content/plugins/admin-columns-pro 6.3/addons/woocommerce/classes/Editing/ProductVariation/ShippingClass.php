<?php

namespace ACA\WC\Editing\ProductVariation;

use ACA\WC\Editing;
use ACP\Editing\View;

class ShippingClass extends Editing\Product\ShippingClass {

	public function get_view( string $context ): ?View {
		$view = parent::get_view( $context );

		if ( $view instanceof View\Select ) {
			$options = $view->get_arg( 'options' );
			$options[''] = __( 'Use Product Shipping Class', 'codepress-admin-columns' );
			$view->set_options( $options );
		}

		return $view;
	}

}