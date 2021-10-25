<?php
namespace GT3\Elementor\Templates;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

require_once __DIR__.'/class-basic.php';

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Core\DocumentTypes\Post;
use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\Utils;

class Tabs extends Basic {

	const post_type = 'elementor_library';
	public static $name = 'gt3-tabs';

	public function __construct(array $data = []){
		if($data) {
			$template = get_post_meta($data['post_id'], '_wp_page_template', true);

			if(empty($template)) {
				$template = 'default';
			}

			$data['settings']['template'] = $template;
		}

		parent::__construct($data);
	}

	public function filter_admin_row_actions($actions){
		if($this->is_built_with_elementor() && $this->is_editable_by_current_user()) {
			$actions['edit_with_elementor'] = sprintf(
				'<a href="%1$s">%2$s</a>',
				$this->get_edit_url(),
				__('Edit Tab', 'gt3_themes_core')
			);

			unset($actions['edit_vc']);
		}

		return $actions;
	}

	public static function static_get_edit_url($post_id){
		$url = add_query_arg(
			[
				'post'   => $post_id,
				'action' => 'elementor',
			],
			admin_url('post.php')
		);

		return $url;
	}

	public static function get_properties(){
		$properties = parent::get_properties();

		$properties['support_wp_page_templates'] = false;
		$properties['admin_tab_group']           = 'library';
		$properties['show_in_library']           = true;
		$properties['register_type']             = true;

		return $properties;
	}

	/**
	 * @access public
	 */
	public function get_name(){
		return self::$name;
	}

	protected static function get_editor_panel_categories(){
		return Utils::array_inject(
			parent::get_editor_panel_categories(),
			'theme-elements',
			[
				'theme-elements-single' => [
					'title'  => __('Single', 'gt3_themes_core'),
					'active' => false,
				],
			]
		);
	}

	public function get_css_wrapper_selector(){
		return '.gt3-template-'.$this->get_main_id();
	}

	protected function _register_controls(){
		parent::_register_controls();

		Post::register_style_controls($this);
	}

	/**
	 * @access public
	 * @static
	 */
	public static function get_title(){
		return __('GT3 Tabs', 'gt3_themes_core');
	}

	protected function get_remote_library_config(){
		$config = parent::get_remote_library_config();

		$config['category'] = '';
		$config['type']     = self::$name;

		return $config;
	}

	public function _get_initial_config(){
		$config = parent::get_initial_config();

		return $config;
	}

	public static function load_canvas_template($single_template){
		global $post;
		$_elementor_template_type = get_metadata('post', $post->ID, '_elementor_template_type', true);

		if($post->post_type === 'elementor_library' && $_elementor_template_type === self::$name) {
			$single_template = __DIR__.'/template.php';
		}

		return $single_template;
	}
}
