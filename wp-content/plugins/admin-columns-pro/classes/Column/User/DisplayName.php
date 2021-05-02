<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Sorting;

class DisplayName extends AC\Column\User\DisplayName
	implements Sorting\Sortable, Editing\Editable {

	public function sorting() {
		return new Sorting\Model\User\UserField( 'display_name' );
	}

	public function editing() {
		return new Editing\Model\User\DisplayName( $this );
	}

}