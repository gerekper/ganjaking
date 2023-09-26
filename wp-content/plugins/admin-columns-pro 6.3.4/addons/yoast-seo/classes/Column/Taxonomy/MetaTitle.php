<?php

namespace ACA\YoastSeo\Column\Taxonomy;

use ACA\YoastSeo\Column;
use ACA\YoastSeo\Editing;
use ACP;

class MetaTitle extends Column\TermMeta
	implements ACP\Editing\Editable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'wpseo-tax_seotitle' )
		     ->set_group( 'yoast-seo' )
		     ->set_label( __( 'SEO Title', 'wordpress-seo' ) );
	}

	protected function get_meta_key() {
		return 'wpseo_title';
	}

	public function editing() {
		return new Editing\Service\Taxonomy\SeoMeta( $this->get_taxonomy(), $this->get_meta_key() );
	}

}