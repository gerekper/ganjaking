<?php

namespace GT3\PhotoVideoGalleryPro\Help;
defined('ABSPATH') OR exit;

final class Types {
	const TYPE_INT    = 1;
	const TYPE_FLOAT  = 2;
	const TYPE_BOOL   = 3;
	const TYPE_ARRAY  = 4;
	const TYPE_OBJECT = 5;
	const TYPE_STRING = 6;
	private static $instance = null;

	public static function instance(){
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	protected function __construct(){

	}
}

