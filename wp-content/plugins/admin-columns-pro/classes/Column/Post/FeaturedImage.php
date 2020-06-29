<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Settings;
use ACP\Sorting;

/**
 * @since 2.0
 */
class FeaturedImage extends AC\Column\Post\FeaturedImage
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function sorting() {
		if ( 'filesize' === $this->get_display_value() ) {
			return new Sorting\Model\Post\FeaturedImageSize( $this->get_meta_key() );
		}

		return new Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function filtering() {
		if ( 'filesize' === $this->get_display_value() ) {
			return new Filtering\Model\Disabled( $this );
		}

		return new Filtering\Model\Post\FeaturedImage( $this );
	}

	public function editing() {
		return new Editing\Model\Post\FeaturedImage( $this );
	}

	public function export() {
		return new Export\Model\AttachmentURLFromAttachmentId( $this );
	}

	public function search() {
		return new Search\Comparison\Post\FeaturedImage( $this->get_post_type() );
	}

	public function register_settings() {
		$this->add_setting( new Settings\Column\FeaturedImage( $this ) );
	}

	/**
	 * @return bool|string
	 */
	private function get_display_value() {
		$setting = $this->get_setting( 'featured_image' );

		if ( ! $setting instanceof Settings\Column\FeaturedImage ) {
			return false;
		}

		return $setting->get_featured_image_display();
	}

}