<?php

namespace ACA\MetaBox\Column;

use AC\Settings;
use ACA\MetaBox\Column;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;
use ACA\MetaBox\Sorting;
use ACP;

class Post extends Column implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	protected function register_settings() {
		$this->add_setting( new Settings\Column\Post( $this ) );
	}

	public function editing() {
		if ( $this->is_clonable() ) {
			return false;
		}

		return new ACP\Editing\Service\Post(
			( new ACP\Editing\View\AjaxSelect() )->set_clear_button( true ),
			( new Editing\StorageFactory() )->create( $this ),
			new ACP\Editing\PaginatedOptions\Posts( (array) $this->get_field_setting( 'post_type' ), (array) $this->get_field_setting( 'query_args' ) )
		);
	}

	public function search() {
		return ( new Search\Factory\Post )->create( $this );
	}

	public function sorting() {
		return ( new Sorting\Factory\Post )->create( $this );
	}

}