<?php

namespace GFML\Container;

class Config {

	/**
	 * @return array
	 */
	public static function getSharedClasses() {
		return [
			\GFML_TM_API::class,
		];
	}
}
