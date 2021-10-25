<?php

namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Widget_Base as Elementor_Widget_Base;
use Elementor\Modules;
use GT3\PhotoVideoGalleryPro\Elementor\Core;

abstract class Widget_Base extends Elementor_Widget_Base {
	protected static $render_index = 0;
	protected        $style        = array();
	protected        $WRAP         = '';

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

	public function _get_settings($setting = null){
		$settings = $this->get_settings($setting);

		return array_merge(
			$settings, array(
				'_uid'           => $this->get_id(),
				'_blockName'     => $this->get_slug(),
				'fromElementor'  => true,
				'className'      => '',
				'blockAlignment' => '',
			)
		);
	}

	protected function get_slug(){
		$strtolower_function = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

		return call_user_func($strtolower_function, substr(strrchr($this::get_class_name(), "\\"), 1));
	}

	public function start_controls_section($section_id, array $args = []){
		parent::start_controls_section($section_id.'_section', $args);
	}

	public function __construct(array $data = array(), $args = null){
		parent::__construct($data, $args);

		add_action('elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ));

		$this->construct();
	}

	/**
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public function widgets_registered($widgets_manager){
		$widgets_manager->register_widget_type($this);
	}

	protected function construct(){
	}


	protected function get_controls_template(){
		$strtolower_function = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

		$name       = explode('_', get_class($this));
		$name_lower = call_user_func($strtolower_function, end($name));

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
		$name                = explode('_', get_class($this));
		$name                = end($name);
		$strtolower_function = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';
		$name_lower          = call_user_func($strtolower_function, $name);

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
