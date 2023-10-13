<?php

namespace WCML\Attributes;

class LookupTableFactory implements \IWPML_Backend_Action_Loader, \IWPML_Frontend_Action_Loader {

	const MIN_WC_VERSION = '6.3';

	/**
	 * @return \IWPML_Action|null
	 */
	public function create() {
		/** @var \SitePress $sitepress */
		global $sitepress;

		if ( self::hasFeature() ) {
			return new LookupTable( $sitepress );
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public static function hasFeature() {
		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MIN_WC_VERSION, '>=' );
	}

}
