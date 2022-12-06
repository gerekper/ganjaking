<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class GoogleMap extends Field {

	public function get_zoom() {
		return isset( $this->settings['zoom'] ) && is_numeric( $this->settings['zoom'] )
			? (int) $this->settings['zoom']
			: null;
	}

}