<?php

namespace ACP;

use AC;

abstract class Strategy {

	/**
	 * @return AC\Column
	 */
	public function get_column() {
		return $this->get_model()->get_column();
	}

	/**
	 * @return Model
	 */
	abstract public function get_model();

}