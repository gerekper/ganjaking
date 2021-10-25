<?php

namespace GT3\PhotoVideoGalleryPro;

use DiviExtension;

defined('ABSPATH') OR exit;


class Divi extends DiviExtension {

	private static $instance = null;

	public $gettext_domain = 'gt3pg_pro';

	public $name = 'gt3-gallery-divi-extension';

	public $version = '1.0.0';

	public static function instance(){
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct($name = 'gt3-gallery-divi-extension', $args = array()){
		$this->plugin_dir     = plugin_dir_path(__FILE__);
		$this->plugin_dir_url = plugin_dir_url($this->plugin_dir);

		parent::__construct($this->name, $args);
	}

	public static function loader(){
		return self::instance();
	}

	protected function _enqueue_bundles(){

	}

	protected function _enqueue_debug_bundles(){

	}

	protected function _enqueue_backend_styles(){

	}

	public function wp_hook_enqueue_scripts(){

	}
}

