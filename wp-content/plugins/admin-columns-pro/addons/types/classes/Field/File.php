<?php

namespace ACA\Types\Field;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACA\Types\Field;
use ACA\Types\Filtering;
use ACA\Types\Search;
use ACP\Editing;
use ACP\Sorting;

class File extends Field {

	public function get_value( $id ) {
		$value = $this->get_raw_value( $id );

		if ( ! $value ) {
			return false;
		}

		$label = $value;

		$upload_dir = wp_upload_dir();

		if ( $upload_dir ) {
			$label = str_replace( $upload_dir['baseurl'], '', $value );
		}

		return ac_helper()->html->link( $value, $label );
	}

	public function sorting() {
		return ( new Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() );
	}

	public function filtering() {
		return new Filtering\File( $this->column );
	}

	public function editing() {
		return new Editing\Service\Basic(
			( new Editing\View\Media() )->set_clear_button( true ),
			new Storage\File( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

	public function search() {
		return new Search\File( $this->column->get_meta_key(), $this->column->get_meta_type() );
	}

	/**
	 * @param string $image_url
	 *
	 * @return int|null
	 */
	public function get_attachment_id_by_url( $image_url ) {
		if ( ! $image_url ) {
			return false;
		}

		$upload_dir = wp_get_upload_dir();

		$image = get_posts( [
			'post_type'      => 'attachment',
			'fields'         => 'ids',
			'meta_query'     => [
				[
					'key'   => '_wp_attached_file',
					'value' => ltrim( str_replace( $upload_dir['baseurl'], '', $image_url ), '/' ),
				],
			],
			'posts_per_page' => 1,
		] );

		if ( ! $image ) {
			return false;
		}

		return $image[0];
	}

}