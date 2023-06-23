<?php

namespace WCML\DisplayAsTranslated;

use function WPML\Container\make;

class FrontendHooksFactory implements \IWPML_Frontend_Action_Loader {

	public function create() {
		/** @var \SitePress $sitepress */
		global $sitepress;

		$hooks           = [];
		$isSecondaryLang = $sitepress->get_current_language() !== $sitepress->get_default_language();

		if ( $isSecondaryLang ) {

			if ( $sitepress->is_display_as_translated_taxonomy( 'product_cat' ) ) {
				$hooks[] = make( ProductCatHooks::class );
			}
		}

		return $hooks;
	}
}
