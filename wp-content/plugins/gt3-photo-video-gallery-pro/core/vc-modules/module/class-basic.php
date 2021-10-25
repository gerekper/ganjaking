<?php

namespace GT3\PhotoVideoGalleryPro\VC_modules\Module;
defined('ABSPATH') OR exit;


class Basic {
	protected $SHORTCODE = 'gt3pg_gallery__';
	protected $name = 'gt3pg_gallery__';

	final public static function instance(){
		static $instance = null;

		if(is_null($instance)) {
			$instance = new static();
		}

		return $instance;
	}

	protected function __construct(){
		if(function_exists('vc_lean_map')) {
			add_shortcode($this->SHORTCODE, array( $this, 'output' ));
			vc_lean_map($this->SHORTCODE, array( $this, 'lean_map' ));
		}
	}

	public function output($atts, $content = null){
//			$atts = vc_map_get_attributes($this->SHORTCODE, $atts);
		$atts = $this->fixKeys($atts);

		// Return output
		ob_start();
		$content = $this->render($atts);
		if(ob_get_length()) {
			$content .= ob_get_contents();
			ob_end_clean();
		}

		return $content;
	}

	protected function render($atts){
		return '';
	}

	protected function fixKeys($atts){
		if(!is_array($atts) || !count($atts)) {
			return array();
		}
		foreach($atts as $key => $value) {
			unset($atts[$key]);
			$atts[$this->camelize($key)] = $value;
		}

		return $atts;
	}

	public function camelToUnderscore($string, $us = "-"){
		$patterns = array(
//				'/([a-z]+)([0-9]+)/i',
			'/([A-Z]{1})/',
//				'/([0-9]+)([a-z]+)/i',
		);

		$strtolower_function = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

		return call_user_func($strtolower_function, preg_replace($patterns, '-$1', $string));
	}

	public function camelize($input, $separator = '-'){
		return lcfirst(str_replace($separator, '', ucwords($input, $separator)));
	}

	public function lean_map(){
		$map    = $this->map();
		$params = $map['params'];
		foreach($params as $key => $param) {
			if(key_exists('param_name', $param)) {
				$param['param_name'] = static::camelToUnderscore($param['param_name']);
				if(key_exists('dependency', $param) && is_array($param['dependency'])) {
					if(key_exists('element', $param['dependency'])) {
						$param['dependency']['element'] = static::camelToUnderscore($param['dependency']['element']);
					}
				}
			}
			$params[$key] = $param;
		}
		$map['params'] = $params;

		return $map;
	}

	public function map(){
		return array();
	}
}
