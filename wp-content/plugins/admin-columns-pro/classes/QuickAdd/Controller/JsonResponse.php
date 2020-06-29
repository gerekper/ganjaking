<?php

namespace ACP\QuickAdd\Controller;

use AC\ListScreen;
use AC\ListScreenWP;
use AC\Response\Json;

class JsonResponse extends Json {

	public function create_from_list_screen( ListScreen $list_screen, $id ) {
		$this->set_parameter( 'id', $id );

		if ( $list_screen instanceof ListScreenWP ) {
			$this->set_parameter( 'row', $list_screen->get_single_row( $id ) );
		}

		return $this;
	}

}