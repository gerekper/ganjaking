<?php

namespace ACP\Column\Post;

use AC;
use ACP\Search;
use ACP\Settings;

class HasTerm extends AC\Column implements Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-has_term' )
		     ->set_label( __( 'Has Term', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		return ac_helper()->icon->yes_or_no( $this->get_raw_value( $id ) );
	}

	public function get_raw_value( $id ) {
		$setting = $this->get_taxonomy_setting();

		return $setting && has_term( $setting->get_term_id(), $setting->get_taxonomy(), $id );
	}

	/**
	 * @return bool True when post type has associated taxonomies
	 */
	public function is_valid() {
		return get_object_taxonomies( $this->get_post_type() ) ? true : false;
	}

	/**
	 * @return Settings\Column\Post\TaxonomyTerm|null
	 */
	public function get_taxonomy_setting() {
		$setting = $this->get_setting( Settings\Column\Post\TaxonomyTerm::NAME );

		return $setting instanceof Settings\Column\Post\TaxonomyTerm
			? $setting
			: null;
	}

	protected function register_settings() {
		parent::register_settings();

		$this->add_setting( new Settings\Column\Post\TaxonomyTerm( $this, $this->get_post_type() ) );
	}

	public function search() {
		$setting = $this->get_taxonomy_setting();

		return $setting
			? new Search\Comparison\Post\HasTerm( $setting->get_taxonomy(), $setting->get_term_id() )
			: false;
	}

}