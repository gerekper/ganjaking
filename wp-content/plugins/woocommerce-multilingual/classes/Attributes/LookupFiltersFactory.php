<?php

namespace WCML\Attributes;

class LookupFiltersFactory implements \IWPML_Frontend_Action_Loader {
	
	/**
	 * @return \IWPML_Action|null
	 */
	public function create() {
		/**
		 * @var \SitePress $sitepress
		 * @var \wpdb      $wpdb
		 */
		global $sitepress, $wpdb;
		
		if ( self::isEnabled() ) {
			return new LookupFilters( $sitepress, $wpdb );
		}

		return null;
	}
	
	/**
	 * @return bool
	 */
	public static function isEnabled() {
		return 'yes' === get_option( 'woocommerce_attribute_lookup_enabled' );
	}

}
