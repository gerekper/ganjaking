<?php

namespace WCML\Exporter;

use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\LIB\WP\Hooks;
use function WPML\FP\spreadArgs;

class AttributeHeadersHooks implements \IWPML_Backend_Action {

	const EXPORT_ACTION = 'woocommerce_do_ajax_product_export';

	public function add_hooks() {
		if ( self::isExporting() ) {
			Hooks::onFilter( 'wcml_sanitize_name_for_translated_attribute_label' )
				->then( spreadArgs( Fns::always( false ) ) );
		}
	}

	/**
	 * @return bool
	 */
	private static function isExporting() {
		if (
			wp_doing_ajax()
			&& self::EXPORT_ACTION === Obj::prop( 'action', $_POST )
		) {
			return true;
		}

		return false;
	}
}
