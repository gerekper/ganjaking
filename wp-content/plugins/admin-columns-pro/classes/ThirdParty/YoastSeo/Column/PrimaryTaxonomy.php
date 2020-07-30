<?php

namespace ACP\ThirdParty\YoastSeo\Column;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\ThirdParty\YoastSeo;

class PrimaryTaxonomy extends AC\Column\Meta
	implements Editing\Editable, Filtering\Filterable, Export\Exportable {

	public function __construct() {
		$this->set_label( __( 'Primary Taxonomy', 'codepress-admin-columns' ) );
		$this->set_group( 'yoast-seo' );
		$this->set_type( 'column-wpseo_column_taxonomy' );
	}

	public function get_value( $id ) {
		$raw_value = $this->get_raw_value( $id );
		if ( ! $raw_value ) {
			return $this->get_empty_char();
		}

		$term = [ get_term( $raw_value, $this->get_taxonomy() ) ];
		$terms = ac_helper()->taxonomy->get_term_links( $term, $this->get_post_type() );

		if ( empty( $terms ) ) {
			return $this->get_empty_char();
		}

		return ac_helper()->string->enumeration_list( $terms, 'and' );
	}

	public function get_meta_key() {
		return '_yoast_wpseo_primary_' . $this->get_taxonomy();
	}

	protected function register_settings() {
		$this->add_setting( new YoastSeo\Settings\PrimaryTaxonomy( $this ) );
	}

	public function editing() {
		return new YoastSeo\Editing\PrimaryTaxonomy( $this );
	}

	public function filtering() {
		return new YoastSeo\Filtering\PrimaryTaxonomy( $this );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

	public function is_valid() {
		return AC\MetaType::POST === $this->get_meta_type();
	}

	/**
	 * @return string
	 */
	public function get_taxonomy() {
		$setting = $this->get_setting( 'primary_taxonomy' );

		if ( ! $setting instanceof YoastSeo\Settings\PrimaryTaxonomy ) {
			return '';
		}

		return $setting->get_primary_taxonomy();
	}

}