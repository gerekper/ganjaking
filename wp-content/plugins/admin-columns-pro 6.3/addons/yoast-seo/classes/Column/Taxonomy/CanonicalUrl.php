<?php

namespace ACA\YoastSeo\Column\Taxonomy;

use AC\Settings\Column\LinkLabel;
use ACA\YoastSeo\Column;
use ACA\YoastSeo\Editing;
use ACP;

class CanonicalUrl extends Column\TermMeta
	implements ACP\Editing\Editable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'wpseo-tax_canonical_url' )
		     ->set_group( 'yoast-seo' )
		     ->set_label( __( 'Canonical URL', 'wordpress-seo' ) );
	}

	protected function get_meta_key() {
		return 'wpseo_canonical';
	}

	protected function register_settings() {
		$this->add_setting( new LinkLabel( $this ) );
	}

	public function editing() {
		return new Editing\Service\Taxonomy\SeoMeta( $this->get_taxonomy(), $this->get_meta_key() );
	}

}