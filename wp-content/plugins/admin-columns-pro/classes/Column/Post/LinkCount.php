<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;
use ACP\Settings;
use ACP\Sorting;

class LinkCount extends AC\Column
	implements Export\Exportable, Sorting\Sortable {

	public function __construct() {
		$this->set_type( 'column-linkcount' )
		     ->set_label( __( 'Link Count', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $id ) {
		return ac_helper()->html->get_internal_external_links(
			get_post_field( 'post_content', $id ),
			$this->get_internal_domains()
		);
	}

	private function get_internal_domains() {
		return apply_filters( 'ac/column/linkcount/domains', [ home_url() ] );
	}

	public function register_settings() {
		$this->add_setting( new Settings\Column\LinkCount( $this ) );
	}

	public function is_valid() {
		return class_exists( 'DOMDocument', false );
	}

	public function export() {
		return new Export\Model\Post\LinkCount( $this );
	}

	private function get_link_count_type() {
		$setting = $this->get_setting( 'link_count_type' );

		return $setting instanceof Settings\Column\LinkCount
			? $setting->get_link_count_type()
			: null;
	}

	public function sorting() {
		switch ( $this->get_link_count_type() ) {
			case 'internal' :
				return new Sorting\Model\Post\LinkCount( $this->get_internal_domains() );
			case 'external' :
				return new Sorting\Model\Disabled();
			default :
				return new Sorting\Model\Post\LinkCount( [ 'http' ] );
		}
	}

}