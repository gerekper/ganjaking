<?php

namespace ACP\Column\Media;

use AC;
use ACP\Filtering;
use ACP\Search;
use ACP\Settings;

class UsedAsFeaturedImage extends AC\Column
	implements Filtering\Filterable, Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-used_as_featured_image' )
		     ->set_label( __( 'Featured Image', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $id ) {
		$ids = get_posts( [
			'posts_per_page' => -1,
			'post_type'      => 'any',
			'post_status'    => 'any',
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => [
				[
					'key'   => '_thumbnail_id',
					'value' => $id,
				],
			],
		] );

		return $ids;
	}

	protected function register_settings() {
		$this->add_setting( new Settings\Column\FeaturedImageDisplay( $this ) );
	}

	public function filtering() {
		return new Filtering\Model\Media\UsedAsFeaturedImage( $this );
	}

	public function search() {
		return new Search\Comparison\Media\UsedAsFeaturedImage();
	}

}