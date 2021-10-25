<?php

namespace GT3\PhotoVideoGallery\Elementor;

defined('ABSPATH') OR exit;

use Elementor\Plugin;
use GT3\PhotoVideoGallery\Settings;
use GT3\PhotoVideoGallery\Assets;

class Core {
	private static $PATH = false;

	private $widgets = array();

	private $controls = array(
		'Gallery',
	);

	private $group_controls = array();

	const version = '1.3.2';

	private static $instance = null;

	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct(){
		$this->actions();
	}

	public static function get_path(){
		if(!self::$PATH) {
			self::$PATH = plugin_dir_path(__FILE__);
		}

		return self::$PATH;
	}

	private function actions(){
		add_action('elementor/init', array( $this, 'elementor_init' ));
	}

	/** @var \Elementor\Elements_Manager $elements_manager */
	public function categories_registered($elements_manager){
		$categories = $elements_manager->get_categories();
		if(!key_exists('gt3-photo-video-gallery', $categories)) {
			$elements_manager->add_category(
				'gt3-photo-video-gallery',
				array(
					'title' => esc_html__('GT3 Photo & Video Gallery', 'gt3pg_lite'),
					'icon'  => 'fa fa-plug'
				)
			);
		}
	}


	public function elementor_init(){
		if(Assets::instance()->pluginEnable('pro')) {
			return;
		}
		add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ));
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts' ));

		add_action('elementor/elements/categories_registered', array( $this, 'categories_registered' ));
		add_action('elementor/controls/controls_registered', array( $this, 'controls_registered' ));

		add_action('elementor/editor/after_enqueue_scripts', array( $this, 'editor_enqueue_scripts' ));
		add_action('elementor/editor/after_enqueue_styles', array( $this, 'editor_enqueue_styles' ));
		add_action('elementor/frontend/after_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ));
		add_action('elementor/frontend/after_enqueue_styles', array( $this, 'frontend_enqueue_styles' ));

		$this->widgets = Settings::instance()->getBlocks();

		/** @var \Elementor\Elements_Manager $elements_manager */
		$elements_manager = Plugin::instance()->elements_manager;
		$categories       = $elements_manager->get_categories();
		if(!key_exists('gt3-photo-video-gallery', $categories)) {
			$elements_manager->add_category(
				'gt3-photo-video-gallery',
				array(
					'title' => esc_html__('GT3 Photo & Video Gallery PRO', 'gt3pg_lite'),
					'icon'  => 'fa fa-plug'
				)
			);
		}

		$this->include_files();
	}

	/**
	 * @param \Elementor\Controls_Manager $controls_manager
	 */
	public function controls_registered($controls_manager){
		if(Assets::instance()->pluginEnable('pro')) {
			return;
		}
		if(is_array($this->controls) && !empty($this->controls)) {
			foreach($this->controls as $module) {
				$module = sprintf('%s\\Controls\\%s', __NAMESPACE__, $module);

				if(class_exists($module)) {
					if($controls_manager->get_control($module::TYPE) === false) {
						$controls_manager->register_control($module::TYPE, new $module);
					}
				}
			}
		}

		if(is_array($this->group_controls) && !empty($this->group_controls)) {
			foreach($this->group_controls as $module) {
				$module = sprintf('%s\\Controls\\%s', __NAMESPACE__, $module);

				if(class_exists($module)) {
					if($controls_manager->get_control($module::TYPE) === false) {
						$controls_manager->add_group_control($module::TYPE, new $module);
					}
				}
			}
		}
	}

	/**
	 * @param string $control
	 * @param bool   $include
	 *
	 * @return void
	 */

	private function include_files(){
		if(Assets::instance()->pluginEnable('pro')) {
			return;
		}
		$this->controls = apply_filters('gt3pg/elementor/controls/register', $this->controls);

		$this->widgets = apply_filters('gt3pg/elementor/widgets/register', $this->widgets);

		if(is_array($this->widgets) && !empty($this->widgets)) {
			foreach($this->widgets as $module) {
				$module = str_replace('/', '\\', $module);
				$module = sprintf('%s\\Widgets\\%s', __NAMESPACE__, $module);
				if(class_exists($module)) {
					new $module();
				}
			}
		}
	}

	public function wp_footer(){

	}

	public function frontend_enqueue_styles(){

	}

	public function frontend_enqueue_scripts(){

	}

	public function editor_enqueue_scripts(){
		if(Assets::instance()->pluginEnable('pro')) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script('media-grid');
		wp_enqueue_script('media');

		wp_enqueue_script('gt3pg_lite-gt3-elementor-core-editor-core',
			GT3PG_LITE_JS_URL.'admin/elementor.js',
			array(
				'imagesloaded',
				'wp-i18n',
				'jquery-ui-sortable',
			),
			filemtime(GT3PG_LITE_JS_PATH.'admin/elementor.js'),
			true
		);
	}

	public function editor_enqueue_styles(){
		if(Assets::instance()->pluginEnable('pro')) {
			return;
		}
		wp_enqueue_style(
			'gt3pg_lite-gt3-elementor-core-editor-core',
			GT3PG_LITE_CSS_URL.'admin/elementor.css',
			array(),
			filemtime(GT3PG_LITE_CSS_PATH.'admin/elementor.css')
		);
	}

	public function enqueue_scripts(){
		/* CSS */

	}
}


