<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Column;
use ACA\MetaBox\Editing;
use ACP;

class SelectAdvanced extends Column\Select {

	public function editing() {
		return $this->is_clonable()
			? false
			: new ACP\Editing\Service\Basic(
				( new ACP\Editing\View\AdvancedSelect( $this->get_field_options() ) )->set_clear_button( true )->set_multiple( $this->is_multiple() ),
				( new Editing\StorageFactory() )->create( $this, false )
			);
	}

}