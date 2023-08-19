<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class FlexibleContent extends Field {

	/**
	 * @return array
	 */
	public function get_layouts() {
		return isset( $this->settings['layouts'] ) && is_array( $this->settings['layouts'] )
			? $this->settings['layouts']
			: [];
	}

}