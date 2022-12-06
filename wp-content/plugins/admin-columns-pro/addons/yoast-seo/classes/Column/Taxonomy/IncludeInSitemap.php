<?php

namespace ACA\YoastSeo\Column\Taxonomy;

use ACA\YoastSeo\Column;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Export;

class IncludeInSitemap extends Column\TermMeta {

	public function __construct() {
		$this->set_type( 'wpseo-tax_sitemap_include' )
		     ->set_group( 'yoast-seo' )
		     ->set_label( __( 'Include in Sitemap', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$rawValue = $this->get_raw_value( $id );

		switch ( $rawValue ) {
			case 'always':
				return __( 'Always include', 'wordpress-seo' );

			case 'never':
				return __( 'Never include', 'wordpress-seo' );

			default:
				return __( 'Auto detect', 'wordpress-seo' );
		}
	}

	protected function get_meta_key() {
		return 'wpseo_sitemap_include';
	}
}