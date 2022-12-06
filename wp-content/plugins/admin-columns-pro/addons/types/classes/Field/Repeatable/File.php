<?php

namespace ACA\Types\Field\Repeatable;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACA\Types\Field;
use ACA\Types\Sorting\DisabledSortingTrait;
use ACP;

class File extends Field\File {

	use DisabledSortingTrait;

	public function get_value( $id ) {
		$urls = $this->get_raw_value( $id );

		if ( ! $urls ) {
			return false;
		}

		$values = [];

		foreach ( (array) $urls as $url ) {
			$label = $url;

			$upload_dir = wp_upload_dir();

			if ( $upload_dir ) {
				$label = str_replace( $upload_dir['baseurl'], '', $url );
			}

			$values[] = ac_helper()->html->link( $url, $label );
		}

		return ac_helper()->html->small_block( $values );
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\Media() )->set_multiple( true )->set_clear_button( true ),
			new Storage\RepeatableFile( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

}