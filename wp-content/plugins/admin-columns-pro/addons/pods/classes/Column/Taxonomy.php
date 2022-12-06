<?php

namespace ACA\Pods\Column;

use ACA\Pods\Column;

class Taxonomy extends Column {

	protected function get_pod_name() {
		if ( ! method_exists( $this->list_screen, 'get_taxonomy' ) ) {
			return false;
		}

		return $this->list_screen->get_taxonomy();
	}

}