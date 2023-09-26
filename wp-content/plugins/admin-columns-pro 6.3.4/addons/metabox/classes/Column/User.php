<?php

namespace ACA\MetaBox\Column;

use AC\Settings;
use ACA\MetaBox\Column;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;
use ACA\MetaBox\Sorting;
use ACP;

class User extends Column implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	protected function register_settings() {
		$this->add_setting( new Settings\Column\User( $this ) );
	}

	public function search() {
		return ( new Search\Factory\User() )->create( $this );
	}

	public function editing() {
		return $this->is_clonable()
			? false
			: new ACP\Editing\Service\User(
				( new ACP\Editing\View\AjaxSelect() )->set_clear_button( true ),
				( new Editing\StorageFactory() )->create( $this ),
				new ACP\Editing\PaginatedOptions\Users( $this->get_field_setting( 'query_args' ) )
			);
	}

	public function sorting() {
		return ( new Sorting\Factory\User )->create( $this );
	}

}