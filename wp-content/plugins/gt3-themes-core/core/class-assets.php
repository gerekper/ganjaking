<?php

namespace GT3\ThemesCore;

use Elementor\Plugin as Elementor_Plugin;

final class Assets {
	private static $instance  = null;
	private static $dist_url  = '';
	private static $dist_path = '';
	private static $js_url    = '';
	private static $js_path   = '';
	private static $css_url   = '';
	private static $css_path  = '';

	private static $theme_css_url   = '';
	private static $theme_css_path  = '';
	private static $parent_css_url  = '';
	private static $parent_css_path = '';

	private static $theme_js_url   = '';
	private static $theme_js_path  = '';
	private static $parent_js_url  = '';
	private static $parent_js_path = '';

	private static $widgets_assets = array(
		'gt3-core/core' => array(),
		'gt3-core/isotope'   => array(),
	);

	/** @return \GT3\ThemesCore\Assets */
	public static function instance(){
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct(){
		self::$dist_url = plugin_dir_url(GT3_THEMES_CORE_PLUGIN_FILE).'dist/';
		self::$js_url   = self::$dist_url.'js/';
		self::$css_url  = self::$dist_url.'css/';

		self::$dist_path = plugin_dir_path(GT3_THEMES_CORE_PLUGIN_FILE).'dist/';
		self::$js_path   = self::$dist_path.'js/';
		self::$css_path  = self::$dist_path.'css/';

		$theme_dist_path      = get_stylesheet_directory().'/dist/';
		self::$theme_js_path  = $theme_dist_path.'js/';
		self::$theme_css_path = $theme_dist_path.'css/';
		$theme_dist_url       = get_stylesheet_directory_uri().'/dist/';
		self::$theme_js_url   = $theme_dist_url.'js/';
		self::$theme_css_url  = $theme_dist_url.'css/';

		$theme_dist_path       = get_template_directory().'/dist/';
		self::$parent_js_path  = $theme_dist_path.'js/';
		self::$parent_css_path = $theme_dist_path.'css/';
		$theme_dist_url        = get_template_directory_uri().'/dist/';
		self::$parent_js_url   = $theme_dist_url.'js/';
		self::$parent_css_url  = $theme_dist_url.'css/';

		add_action('wp_head', array( $this, 'register_widgets_assets' ), -1);
		add_action('elementor/editor/wp_head', array( $this, 'register_widgets_assets' ), -1);
		add_action('enqueue_block_assets', array( $this, 'frontend_gutenberg' ));
		add_action('dynamic_sidebar', array( $this, 'dynamic_sidebar' ));

//		add_action('elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ));
		add_action('wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ));
		add_action('elementor/editor/before_enqueue_scripts', array( $this, 'elementor_before_enqueue_scripts' ), -1);
	}

	public function wp_enqueue_scripts(){
		if(is_single()) {
			global $post;
			$post_type = $post->post_type;
			Assets::register_style("gt3-core/cpt/${post_type}");
			wp_enqueue_style("gt3-core/cpt/${post_type}");
			Assets::register_theme_style("gt3-theme/cpt/${post_type}");
			wp_enqueue_style("gt3-theme/cpt/${post_type}");


			Assets::register_style("gt3-core/cpt/single");
			wp_enqueue_style("gt3-core/cpt/single");
			Assets::register_theme_style("gt3-theme/cpt/single");
			wp_enqueue_style("gt3-theme/cpt/single");
		}
	}

	public static function enqueue_style($handle, $src = '', $deps = array(), $ver = false, $media = 'all'){
		if(!file_exists(self::$css_path.$src)) {

			return;
		}
		wp_enqueue_style(
			$handle,
			self::$css_url.$src,
			$deps,
			filemtime(self::$css_path.$src),
			$media
		);
	}

	public static function enqueue_script($handle, $src = '', $deps = array(), $ver = false, $in_footer = true){
		if(!file_exists(self::$js_path.$src)) {

			return;
		}
		wp_enqueue_script(
			$handle,
			self::$js_url.$src,
			$deps,
			filemtime(self::$js_path.$src),
			$in_footer
		);
	}

	public static function get_dist_url(){
		return self::$dist_url;
	}

	public static function get_dist_path(){
		return self::$dist_path;
	}

	public function print_elementor_styles(){
		if(is_singular()) {
			global $post;
			$post_id  = $post->ID;
			$document = Elementor_Plugin::$instance->documents->get_doc_for_frontend($post_id);
			// Change the current post, so widgets can use `documents->get_current`.
			Elementor_Plugin::$instance->documents->switch_to_document($document);
			$data = $document->get_elements_data();
			if(is_array($data) && count($data)) {
				foreach($data as $modules) {
					$this->elementor_recursive_style($modules);
				}
			}
			Elementor_Plugin::$instance->documents->restore_document();

			self::register_widget('column', array( 'gt3-core/core' ));
			wp_enqueue_script('gt3-core/widgets/column');
		}
	}

	protected function elementor_recursive_style($data){
		if(key_exists('elType', $data)) {
			switch($data['elType']) {
				case 'section':
				case 'column':
					foreach($data['elements'] as $modules) {
						$this->elementor_recursive_style($modules);
					}
					break;
				case 'widget':
					$widget = Elementor_Plugin::instance()->widgets_manager->get_widget_types($data['widgetType']);
					if(!is_null($widget)) {
						$styles = $widget->get_style_depends();
						if(is_array($styles) && count($styles)) {
							foreach($styles as $style) {
								wp_enqueue_style($style);
							}
						}
					}
					break;
			}
		}
	}

	public static function register_style($handle, $deps = array(), $media = 'all'){
		$file = strtolower(str_replace('gt3-core/', '', $handle).'.css');
		if(!file_exists(self::$css_path.$file)) {
			return;
		}
		wp_register_style(
			$handle,
			self::$css_url.$file,
			$deps,
			filemtime(self::$css_path.$file),
			$media
		);
	}

	public static function register_script($handle, $deps = array(), $in_footer = true){
		$file = strtolower(str_replace('gt3-core/', '', $handle).'.js');
		if(!file_exists(self::$js_path.$file)) {
			return;
		}
		wp_register_script(
			$handle,
			self::$js_url.$file,
			$deps,
			filemtime(self::$js_path.$file),
			$in_footer
		);
	}

	public static function register_theme_script($handle, $deps = array(), $in_footer = true){
		$file = strtolower(str_replace('gt3-theme/', '', $handle).'.js');
		$url  = $path = null;
		if(stream_resolve_include_path(self::$theme_js_path.$file)) {
			$url  = self::$theme_js_url.$file;
			$path = self::$theme_js_path.$file;
		} else if(stream_resolve_include_path(self::$parent_js_path.$file)) {
			$url  = self::$parent_js_url.$file;
			$path = self::$parent_js_path.$file;
		}

		if(is_null($path)) {
			return;
		}
		wp_register_script(
			$handle,
			$url,
			$deps,
			filemtime($path),
			$in_footer
		);
	}

	public static function register_theme_style($handle, $deps = array(), $media = 'all'){
		$file = strtolower(str_replace('gt3-theme/', '', $handle).'.css');
		$url  = $path = null;
		if(stream_resolve_include_path(self::$theme_css_path.$file)) {
			$url  = self::$theme_css_url.$file;
			$path = self::$theme_css_path.$file;
		} else if(stream_resolve_include_path(self::$parent_css_path.$file)) {
			$url  = self::$parent_css_url.$file;
			$path = self::$parent_css_path.$file;
		}

		if(is_null($path)) {
			return;
		}

		wp_register_style(
			$handle,
			$url,
			$deps,
			filemtime($path),
			$media
		);
	}

	public static function register_widget($widget_name, $depts = array()){
		Assets::register_script('gt3-core/widgets/'.$widget_name, $depts);
		Assets::register_style('gt3-core/widgets/'.$widget_name, $depts);
		Assets::register_theme_script('gt3-theme/widgets/'.$widget_name, $depts);
		Assets::register_theme_style('gt3-theme/widgets/'.$widget_name, $depts);
	}


	public function register_widgets_assets(){
		static $loaded = false;
		if($loaded) {
			return;
		}
		$loaded               = true;
		self::$widgets_assets = apply_filters('gt3/core/assets/widgets_assets', self::$widgets_assets);

		if(is_array(self::$widgets_assets) && count(self::$widgets_assets)) {
			foreach(self::$widgets_assets as $name => $deps) {
				$this->register_script($name, $deps);
				$this->register_style($name, $deps);

				if(class_exists('Elementor\Plugin') && Elementor_Plugin::instance()->preview->is_preview()) {
					wp_enqueue_script($name);
					wp_enqueue_style($name);
				}
			}
		}
		$content = ';window.resturl = window.resturl || "'.get_rest_url().'";';

		wp_script_add_data('gt3pg_pro--core', 'data', $content);

		if(is_singular()) {
			global $post;
			if(class_exists('Elementor\Plugin') && Elementor_Plugin::$instance->db->is_built_with_elementor($post->ID)) {
				add_action('wp_enqueue_scripts', array( $this, 'print_elementor_styles' ), 7);
			}
		}

		if(class_exists('\Elementor\Plugin')) {
			$widget_manager = Elementor_Plugin::instance()->widgets_manager;
			$this->widgets_registered($widget_manager);
		}

	}

	public function elementor_before_enqueue_scripts(){
		$this->register_widgets_assets();
		if(is_array(self::$widgets_assets) && count(self::$widgets_assets)) {
			foreach(self::$widgets_assets as $name => $deps) {
				wp_enqueue_script($name);
				wp_enqueue_style($name);
			}
		}
	}

	/**
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public function widgets_registered($widgets_manager){

		$widgets = array();

		$widgets = array_unique(apply_filters('gt3/core/assets/elementor_widgets_assets', $widgets));

		foreach($widgets as $widget_name) {
			$widget = Elementor_Plugin::instance()->widgets_manager->get_widget_types($widget_name);

			if(!is_null($widget)) {
				$this->register_widget($widget_name);
				$widget->add_style_depends("gt3-core/widgets/${widget_name}");
				$widget->add_style_depends("gt3-theme/widgets/${widget_name}");
				$widget->add_script_depends("gt3-core/widgets/${widget_name}");
				$widget->add_script_depends("gt3-theme/widgets/${widget_name}");
				if(Elementor_Plugin::instance()->preview->is_preview()) {
					wp_enqueue_script("gt3-core/widgets/${widget_name}");
					wp_enqueue_script("gt3-theme/widgets/${widget_name}");
					wp_enqueue_style("gt3-core/widgets/${widget_name}");
					wp_enqueue_style("gt3-theme/widgets/${widget_name}");
				}
			}
		}
	}

	public function frontend_gutenberg(){

	}

	public function dynamic_sidebar($widget){
		$widget_name = _get_widget_id_base($widget['id']);
		self::register_style('gt3-core/wp-widgets/'.$widget_name);
		self::register_theme_style('gt3-theme/wp-widgets/'.$widget_name);

		wp_enqueue_style('gt3-core/wp-widgets/'.$widget_name);
		wp_enqueue_style('gt3-theme/wp-widgets/'.$widget_name);
	}
}
