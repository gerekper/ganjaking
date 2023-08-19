<?php

namespace ACA\YoastSeo\Column\Taxonomy;

use ACA\YoastSeo\Column;
use ACA\YoastSeo\Editing;
use ACP;

class FocusKeyword extends Column\TermMeta
	implements ACP\Editing\Editable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'wpseo-tax_focuskw' )
		     ->set_group( 'yoast-seo' )
		     ->set_label( __( 'Focus keyword', 'wordpress-seo' ) );
	}

	protected function get_meta_key() {
		return 'wpseo_focuskw';
	}

	public function editing() {
		return new Editing\Service\Taxonomy\SeoMeta( $this->get_taxonomy(), $this->get_meta_key() );
	}
}