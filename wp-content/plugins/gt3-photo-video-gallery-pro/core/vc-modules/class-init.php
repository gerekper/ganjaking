<?php

namespace GT3\PhotoVideoGalleryPro\VC_modules;
defined('ABSPATH') OR exit;

use GT3\PhotoVideoGalleryPro\Settings;

class Init {

	private $modules = array();

	private static $instance = null;

	public static function instance(){
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct(){
		add_action('init', array($this,'initHandler'));
	}

	public function initHandler() {
		$this->modules = Settings::instance()->getBlocks();

		foreach($this->modules as $module) {
			$module = __NAMESPACE__.'\\Module\\'.$module;
			if(class_exists($module)) {
				/** @var Module\Basic $module */
				$module::instance();
			}
		}
	}
}
