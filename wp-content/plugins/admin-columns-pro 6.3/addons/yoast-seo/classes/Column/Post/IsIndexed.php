<?php

namespace ACA\YoastSeo\Column\Post;

use AC;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Search;
use ACP;
use WPSEO_Post_Type;

class IsIndexed extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_group( 'yoast-seo' )
		     ->set_label( 'Is Indexed' )
		     ->set_type( 'column-yoast_is_indexed' );
	}

	public function get_value( $id ) {
		$raw_value = (int) $this->get_raw_value( $id );

		switch ( $raw_value ) {
			case 0:
				return sprintf( '%s <span style="color: #ccc;">%s</span>',
					ac_helper()->icon->yes_or_no( $this->get_default_post_type_index() ),
					ac_helper()->icon->dashicon( [ 'icon' => 'info', 'class' => 'grey', 'tooltip' => __( 'Implicit', 'codepress-admin-columns' ) ] )
				);
			case 1:
				return ac_helper()->icon->no();
			case 2:
				return ac_helper()->icon->yes();
			default :
				return '';
		}
	}

	private function get_default_post_type_index() {
		if ( ! class_exists( 'WPSEO_Post_Type', false ) ) {
			return false;
		}

		return WPSEO_Post_Type::is_post_type_indexable( $this->get_post_type() );
	}

	public function get_meta_key() {
		return '_yoast_wpseo_meta-robots-noindex';
	}

	public function editing() {
		return new Editing\Service\Post\IsIndexed( $this->get_post_type(), $this->get_default_post_type_index() );
	}

	public function search() {
		$null_value = $this->get_default_post_type_index() ? 2 : 1;

		return new Search\Post\IsIndexed( $this->get_meta_key(), $null_value );
	}

}