<?php

namespace GT3\PhotoVideoGallery\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Widget_Base as Elementor_Widget_Base;
use Elementor\Modules;
use GT3\PhotoVideoGallery\Elementor\Core;

abstract class Widget_Base extends Elementor_Widget_Base {
	protected static $render_index = 0;
	protected $style = array();
	protected $WRAP = '';

	public function get_categories(){
		return array( 'gt3-photo-video-gallery' );
	}

	public function get_keywords(){
		return array(
			'gt3pg',
			'layout',
			'gallery',
			'photo',
			'image',
			'album',
			'portfolio',
			'slider',
		);
	}

	public function _get_settings($setting = null) {
		$settings = $this->get_settings($setting);
		return array_merge($settings, array(
			'_uid'       => $this->get_id(),
			'_blockName' => $this->get_slug(),
			'fromElementor'  => true,
			'className'  => '',
			'blockAlignment' => '',
		));
	}

	protected function get_slug() {
		return strtolower(substr(strrchr($this::get_class_name(), "\\"), 1));
	}

	public function start_controls_section($section_id, array $args = []){
		parent::start_controls_section($section_id.'_section', $args);
	}

	public function __construct(array $data = array(), $args = null){
		parent::__construct($data, $args);

		add_action('elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ));

		$this->construct();
	}

	protected function camelToUnderscore($string, $us = "-"){
		$patterns = array(
			'/([a-z]+)([0-9]+)/i',
			'/([a-z]+)([A-Z]+)/',
			'/([0-9]+)([a-z]+)/i'
		);
		$string   = preg_replace($patterns, '$1'.$us.'$2', $string);

		// Lowercase
		$string = strtolower($string);

		return $string;
	}

	protected function get_styles($with_tags = true){
		$style = '';
		if(is_array($this->style) && count($this->style)) {
			foreach($this->style as $selector => $_styles) {
				if(is_array($_styles) && count($_styles)) {
					$_style = '';
					foreach($_styles as $styleName => $value) {
						if(!empty($value) || (is_numeric($value))) {
							if(!is_array($value)) {
								$value = array( $value );
							}
							if(substr($styleName, -1, 1) !== ';') {
								$styleName .= ';';
							}
							$_style .= "\t".sprintf($this->camelToUnderscore($styleName), ...$value).PHP_EOL;
						}
					}
					if(!empty($_style)) {
						$style .= $selector.' {'.PHP_EOL.$_style.'}'.PHP_EOL;
					}
				}
			}
		}
		if(!empty($style) && $with_tags) {
			return '<style>'.$style.'</style>';
		}

		return $style;
	}

	/**
	 * @param array|string $selector
	 * @param array|null   $value
	 */
	protected function add_style($selector, $value = null){
		$oldStyle = array();
		if(is_array($selector) && count($selector)) {
			foreach($selector as $_selector => $_value) {
				if(is_numeric($_selector)) {
					$_selector = $_value;
					$_value    = $value;
				}
				if(isset($this->style[$this->WRAP.' '.$_selector])) {
					$oldStyle = $this->style[$this->WRAP.' '.$_selector];
				} else {
					$oldStyle = array();
				}
				$this->style[$this->WRAP.' '.$_selector] = array_merge($oldStyle, $_value);
			}
		} else {
			if(isset($this->style[$this->WRAP.' '.$selector])) {
				$oldStyle = $this->style[$this->WRAP.' '.$selector];
			} else {
				$oldStyle = array();
			}
			$this->style[$this->WRAP.' '.$selector] = array_merge($oldStyle, $value);
		}
	}

	/**
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public function widgets_registered($widgets_manager){
		$widgets_manager->register_widget_type($this);
	}

	protected function prepareSettings(&$settings, $keys = array()){
		$int  = 'int';
		$bool = 'bool';
		$keys = array_merge(array(
			'borderPadding'        => $int,
			'borderSize'           => $int,
			'borderType'           => $int,
			'columns'              => $int,
			'isMargin'             => $bool,
			'margin'               => $int,
			'random'               => $bool,
			'rightClick'           => $bool,
			'showTitle'            => $bool,
			'showCaption'          => $bool,
			'ytWidth'              => $bool,
			'lightboxAutoplay'     => $bool,
			'lightboxAutoplayTime' => $int,
			'lightboxThumbnails'   => $bool,
			'lightboxCover'        => $bool,
			'lightboxDeeplink'     => $bool,
			'sliderAutoplay'       => $bool,
			'sliderAutoplayTime'   => $int,
			'sliderThumbnails'     => $bool,
			'sliderCover'          => $bool,
			'packery'              => $int,
			'socials'              => $bool,
			'allowDownload'        => $bool,
			'thumbnails_Controls'  => $bool,
			'thumbnails_lightbox'  => $bool,
			'fsSliderAutoplay'     => $bool,
			'lightboxAllowZoom'    => $bool,
		), $keys);
		foreach($keys as $key => $type) {
			if(!key_exists($key, $settings)) {
				continue;
			}
			switch($type) {
				case $bool:
					$settings[$key] = in_array($settings[$key], array( '1', 1, 'on', 'yes', true ), true) ? true : $settings[$key];
					$settings[$key] = in_array($settings[$key], array( '0', 0, 'off', 'no', false ), true) ? false : $settings[$key];
					$settings[$key] = (bool) $settings[$key];
					break;
				case $int:
					$settings[$key] = (int) $settings[$key];
					break;
			}
		}
	}

	protected function construct(){
	}

	public function get_repeater_key($setting_key, $repeater_key, $repeater_item_index){
		return $this->get_repeater_setting_key($setting_key, $repeater_key, $repeater_item_index);
	}

	protected function get_controls_template(){
		$name       = explode('_', get_class($this));
		$name_lower = strtolower(end($name));

		$template = locate_template(array( 'widgets/'.$name_lower.'/controls.php', 'elementor/widgets/'.$name_lower.'/controls.php' ));
		if(empty($template) && file_exists(Core::get_path().'widgets/'.$name_lower.'/controls.php')) {
			$template = Core::get_path().'widgets/'.$name_lower.'/controls.php';
		}

		if(!empty($template) && file_exists($template)) {
			$widget = $this;
			require_once $template;
		} else {
			$this->_controls();
		}
	}

	protected function _controls(){
	}

	protected function get_render_template(){
		$name       = explode('_', get_class($this));
		$name       = end($name);
		$name_lower = strtolower($name);

		$template = locate_template(array( 'widgets/'.$name_lower.'/render.php', 'elementor/widgets/'.$name_lower.'/render.php' ));
		if(empty($template) && file_exists(Core::get_path().'widgets/'.$name_lower.'/render.php')) {
			$template = Core::get_path().'widgets/'.$name_lower.'/render.php';
		}
		self::$render_index++;

		if(!empty($template) && file_exists($template)) {
			$widget = $this;
			require $template;
		} else {
			$this->_render();
		}
	}

	protected function _render(){

	}

	protected function _register_controls(){
		do_action('gt3/elementor/register_control/before/'.$this->get_name(), $this);
		$this->get_controls_template();
		do_action('gt3/elementor/register_control/after/'.$this->get_name(), $this);
	}

	// php
	protected function render(){
		do_action('gt3/elementor/render/before/'.$this->get_name(), $this);
		$this->get_render_template();
		do_action('gt3/elementor/render/after/'.$this->get_name(), $this);
	}
}
