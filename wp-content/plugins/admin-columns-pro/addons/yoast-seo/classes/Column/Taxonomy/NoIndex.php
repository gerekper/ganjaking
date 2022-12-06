<?php

namespace ACA\YoastSeo\Column\Taxonomy;

use ACA\YoastSeo\Column;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Export;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\ConditionalFormat\Formattable;

class NoIndex extends Column\TermMeta implements Formattable {

	use ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'wpseo-tax_noindex' )
		     ->set_group( 'yoast-seo' )
		     ->set_label( __( 'Noindex', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$rawValue = $this->get_raw_value( $id );

		switch ( $rawValue ) {
			case 'index':
				return __( 'Always index', 'wordpress-seo' );

			case 'noindex':
				return __( 'Always noindex', 'wordpress-seo' );

			default:
				return __( 'Use default', 'codepress-admin-columns' );
		}
	}

	protected function get_meta_key() {
		return 'wpseo_noindex';
	}
}