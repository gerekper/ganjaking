<?php

namespace ElementorModal\Widgets;

if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Modules;
use Elementor\GT3_Core_Elementor_Plugin;
use GT3\ThemesCore\Assets;

abstract class GT3_Core_Widget_Base extends Widget_Base {
	public $is_rest             = false;
	public $is_editor           = false;
	public $is_elementor_editor = false;

	public function get_keywords(){
		return array(
			'gt3',
		);
	}

	public function get_clear_name(){
		return str_replace('gt3-core-', '', $this->get_name());
	}

	public function get_categories(){
		return array( 'gt3-core-elements' );
	}

	public function start_controls_section($section_id, array $args = []){
		$default_args = array(
			'condition' => apply_filters(
				'gt3/core/start_controls_section/'.$section_id.'_section', null
			)
		);
		$args         = array_merge($default_args, $args);
		parent::start_controls_section($section_id.'_section', $args);
	}

	/**
	 * GT3_Core_Widget_Base constructor.
	 *
	 * @param array $data
	 * @param null  $args
	 *
	 * @throws \Exception
	 */
	public function __construct(array $data = array(), $args = null){
		parent::__construct($data, $args);

		add_action('elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ));
		$widget_name = $this->get_name();

		$this->construct();

		do_action("gt3/elementor/widget/register", $this);
		do_action("gt3/elementor/widget/register/{$widget_name}", $this);

		$this->register_widgets_assets();


		add_action('elementor/frontend/before_register_scripts', array( $this, 'before_register_scripts' ), 5);
	}

	public function before_register_scripts() {
		if (Plugin::instance()->preview->is_preview()) {
			$this->enqueue_scripts();
			$this->enqueue_styles();
		}
	}

	public function register_widgets_assets(){
		$widget_name = $this->get_name();


		$global_scripts = $this->get_main_script_depends();
		if(is_array($global_scripts) && count($global_scripts)) {
			$global_scripts[] = "gt3-core/widgets/${widget_name}";
			$global_scripts[] = "gt3-theme/widgets/${widget_name}";
			foreach($global_scripts as $script) {
				$this->add_style_depends($script);
				$this->add_script_depends($script);
			}
		}
		Assets::register_widget($widget_name);
	}

	/**
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public function widgets_registered($widgets_manager){
		$widgets_manager->register_widget_type($this);
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
		if(empty($template) && file_exists(GT3_Core_Elementor_Plugin::$PATH.'widgets/'.$name_lower.'/controls.php')) {
			$template = GT3_Core_Elementor_Plugin::$PATH.'widgets/'.$name_lower.'/controls.php';
		}

		if(!empty($template)) {
			$widget = $this;
			require_once $template;
		}
	}

	protected function get_render_template(){
		$name       = explode('_', get_class($this));
		$name       = end($name);
		$name_lower = strtolower($name);

		$template = locate_template(array( 'widgets/'.$name_lower.'/render.php', 'elementor/widgets/'.$name_lower.'/render.php' ));
		if(empty($template) && file_exists(GT3_Core_Elementor_Plugin::$PATH.'widgets/'.$name_lower.'/render.php')) {
			$template = GT3_Core_Elementor_Plugin::$PATH.'widgets/'.$name_lower.'/render.php';
		}

		if(!empty($template)) {
			$widget = $this;
			require $template;
		}
	}

	protected function _register_controls(){
		do_action('gt3/elementor/register_control/before/'.$this->get_name(), $this);
		$this->get_controls_template();
		do_action('gt3/elementor/register_control/after/'.$this->get_name(), $this);
	}

	// php
	protected function render(){
		$this->is_rest             = defined('REST_REQUEST');
		$this->is_elementor_editor = class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode();
		$this->is_editor           = $this->is_rest || $this->is_elementor_editor;
		do_action('gt3/elementor/render/before/'.$this->get_name(), $this);
//		echo '<div class="gt3-themes-core" data-gt3-widget="'.$this->get_clear_name().'" data-id="'.$this->get_id().'">';
		$this->get_render_template();
//		echo '</div>';
		do_action('gt3/elementor/render/after/'.$this->get_name(), $this);
	}

	protected function get_main_script_depends(){
		return array( 'gt3-core/core' );
	}

	public function print_data_settings($data){
		if(!is_array($data)) {
			$data = array();
		}
		echo '<script type="application/json" id="settings--'.$this->get_id().'">'.wp_json_encode($data).'</script>';
	}
}

