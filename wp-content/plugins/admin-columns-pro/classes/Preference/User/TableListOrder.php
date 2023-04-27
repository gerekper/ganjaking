<?php
declare( strict_types=1 );

namespace ACP\Preference\User;

use AC\Preferences\Site;

class TableListOrder extends Site {

	public function __construct( int $user_id ) {
		parent::__construct( 'list_order', $user_id );
	}
}