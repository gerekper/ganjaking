<?php

namespace GT3\PhotoVideoGalleryPro;
defined('ABSPATH') OR exit;

use Elementor\Core\Documents_Manager;
use Elementor\Frontend;
use Elementor\Plugin as Elementor_Plugin;
use GT3\PhotoVideoGalleryPro\Block\Basic as Basic_Block;
use GT3_Post_Type_Gallery;
use WP_Block_Type_Registry;

class Assets {
	private static $dist_url  = '';
	private static $dist_path = '';
	private static $js_url    = '';
	private static $js_path   = '';
	private static $css_url   = '';
	private static $css_path  = '';

	private static $instance = null;

	/** @return Assets */
	public static function instance(){
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	protected $assets = array(
		'gt3pg_pro--core'            => array(),
		'gt3pg_pro--lightbox'        => array( 'gt3pg_pro--core' ),
		'gt3pg_pro--isotope'         => array(),
		'gt3pg_pro--gallery-isotope' => array( 'gt3pg_pro--core', 'gt3pg_pro--isotope', 'gt3pg_pro--lightbox' ),
		'gt3pg_pro--slider'          => array( 'gt3pg_pro--core', 'gt3pg_pro--lightbox' ),
		'gt3pg_pro--fsslider'        => array( 'gt3pg_pro--core', 'gt3pg_pro--lightbox' ),
		'gt3pg_pro--thumbnails'      => array( 'gt3pg_pro--core', 'gt3pg_pro--lightbox' ),
		'gt3pg_pro--zoom'            => array( 'imagesloaded' ),
	);

	protected static $assets_map = array(
		'lightbox' => 'gt3pg_pro--lightbox',
	);

	/**
	 * @param string $asset
	 */
	public static function enqueue_script($asset){
		if(key_exists($asset, static::$assets_map)) {
			wp_enqueue_script(static::$assets_map[$asset]);
		}
	}

	protected $styles = array();

	public static $block_style_depends = array(
		'grid'         => array( 'gt3pg_pro--gallery-isotope', 'gt3pg-pro-blocks-frontend' ),
		'masonry'      => array( 'gt3pg_pro--gallery-isotope', 'gt3pg-pro-blocks-frontend' ),
		'packery'      => array( 'gt3pg_pro--gallery-isotope', 'gt3pg-pro-blocks-frontend' ),
		'thumbnails'   => array( 'gt3pg-pro-blocks-frontend', 'gt3pg_pro--lightbox', 'gt3pg_pro--thumbnails' ),
		'before-after' => array( 'gt3pg-pro-blocks-frontend' ),
		'stripe'       => array( 'gt3pg-pro-blocks-frontend' ),
		'albums'       => array( 'gt3pg-pro-blocks-frontend' ),
		'shift'        => array( 'gt3pg-pro-blocks-frontend' ),
		'flow'         => array( 'gt3pg-pro-blocks-frontend' ),
		'ribbon'       => array( 'gt3pg-pro-blocks-frontend' ),
		'justified'    => array( 'gt3pg-pro-blocks-frontend' ),
		'instagram'    => array( 'gt3pg_pro--gallery-isotope', 'gt3pg-pro-blocks-frontend' ),
		'fsslider'     => array( 'gt3pg-pro-blocks-frontend', 'gt3pg_pro--lightbox', 'gt3pg_pro--fsslider' ),
		'slider'       => array( 'gt3pg-pro-blocks-frontend', 'gt3pg_pro--lightbox', 'gt3pg_pro--slider' ),
	);

	private $style            = array();
	private $responsive_style = array();

	private $pro_enabled;
	private $optimizer_enabled;

	protected $is_rest             = false;
	protected $is_editor           = false;
	protected $is_elementor_editor = false;

	public function register_script__action(){
		static $loaded = false;
		if($loaded) {
			return;
		}
		$loaded = true;

		foreach($this->assets as $name => $deps) {
			$this->register_script($name, $deps);
			$this->register_style($name, $deps);

			if(is_admin()) {
				wp_enqueue_script($name);
				wp_enqueue_style($name);
			}
		}
		$content = ';window.resturl = window.resturl || "'.get_rest_url().'";';

		wp_script_add_data('gt3pg_pro--core', 'data', $content);
	}

	public function enqueue_style($widget){
		if(key_exists($widget, $this->assets)) {
			wp_enqueue_script($widget);
			wp_enqueue_style($widget);
		}
	}

	private function __construct(){
		$this->optimizer_enabled = defined('GT3PG_FHD_PLUGINPATH');
		$this->pro_enabled = defined('GT3PG_PRO_PLUGINPATH') OR defined('GT3PG_PRO_PLUGIN_ROOT_PATH');

		self::$dist_url = plugin_dir_url(GT3PG_PRO_FILE).'dist/';
		self::$js_url   = self::$dist_url.'js/';
		self::$css_url  = self::$dist_url.'css/';

		self::$dist_path = plugin_dir_path(GT3PG_PRO_FILE).'dist/';
		self::$js_path   = self::$dist_path.'js/';
		self::$css_path  = self::$dist_path.'css/';

		// Frontend
//		add_action('wp_enqueue_scripts', array( $this, 'frontend_elementor' ));
		add_action('elementor/frontend/before_enqueue_scripts', array( $this, 'frontend_elementor' ));
		add_action('enqueue_block_assets', array( $this, 'frontend_gutenberg' ));
		add_action('wp_enqueue_scripts', array( $this, 'frontend_gutenberg' ));

		// Admin area
		add_action('elementor/editor/before_enqueue_scripts', array( $this, 'editor_elementor' ));
		add_action('enqueue_block_editor_assets', array( $this, 'editor_gutenberg' ));
		add_action('admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ));

		add_action('init', array( $this, 'init' ));

		remove_action('wp_head', 'gt3pg_wp_head');
		add_action('wp_head', array( $this, 'print_custom_css' ));

		add_action('wp_enqueue_scripts', array( $this, 'register_script__action' ), 5);
		add_action('enqueue_block_assets', array( $this, 'register_script__action' ), 4);
		add_action('elementor/frontend/before_enqueue_scripts', array( $this, 'register_script__action' ), 5);
	}


	public static function register_style($handle, $deps = array(), $media = 'all'){
		$file = str_replace('gt3pg_pro--', '', $handle);
		if(!file_exists(self::$css_path.$file)) {
//			trigger_error('Css file <b>'.$file.'</b> not found.', E_USER_WARNING);
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
		$file = 'frontend/'.str_replace('gt3pg_pro--', '', $handle).'.js';
		if(!file_exists(self::$js_path.$file)) {
//			trigger_error('JS file <b>'.self::$js_path.$file.'</b> not found.', E_USER_WARNING);
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

	public function init(){
		$this->is_rest             = defined('REST_REQUEST');
		$this->is_elementor_editor = class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode();
		$this->is_editor           = $this->is_rest || $this->is_elementor_editor;
	}

	public function admin_enqueue_scripts(){
		// Styles.
		wp_enqueue_style(
			'gt3pg-pro-blocks-editor',
			GT3PG_PRO_CSSURL.'gutenberg/editor.css',
			array( 'wp-edit-blocks' ),
			filemtime(GT3PG_PRO_CSSPATH.'gutenberg/editor.css')
		);
	}

	public function getPlugins(){
		return array(
			'io'  => $this->optimizer_enabled,
			'pro' => $this->pro_enabled,
		);
	}

	function get_jed_locale_data($domain){
		$translations = get_translations_for_domain($domain);

		$locale = array(
			'' => array(
				'domain' => $domain,
				'lang'   => is_admin() ? get_user_locale() : get_locale(),
			),
		);

		if(!empty($translations->headers['Plural-Forms'])) {
			$locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
		}

		foreach($translations->entries as $msgid => $entry) {
			$locale[$msgid] = $entry->translations;
		}

		return $locale;
	}

	public function frontend_elementor(){
		$this->print_elementor_styles();
		if(Elementor_Plugin::instance()->preview->is_preview()) {
			wp_enqueue_script('gt3pg-pro-frontend');
			foreach($this->assets as $name => $deps) {
				wp_enqueue_script($name);
				wp_enqueue_style($name);
			}
		}
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
		}
	}

	protected function elementor_recursive_style($data){
		if(in_array($data['elType'], array( 'section', 'column' ))) {
			foreach($data['elements'] as $modules) {
				$this->elementor_recursive_style($modules);
			}
		} else {
			$this->elementor_print_style($data);
		}
	}

	protected function elementor_print_style($module){
		if($module['elType'] === 'widget' && strpos($module['widgetType'], 'gt3pg') !== false) {
			$module = str_replace('gt3pg-', '', $module['widgetType']);
			static::print_style($module);
		}
	}

	public static function print_style($module){
		if(key_exists($module, static::$block_style_depends)) {
			$module = static::$block_style_depends[$module];
			if(!is_array($module)) {
				$module = array( $module );
			}
			foreach($module as $style) {
				wp_enqueue_style($style);
			}
		}
	}


	public function editor_elementor(){
		wp_enqueue_script('gt3pg-pro-frontend');
	}

	function frontend_gutenberg(){
		static $loaded = false;
		if($loaded) {
			return;
		}
		$loaded = true;
		if(apply_filters('gt3pg-pro/blocks/enable-style', true)) {
			wp_register_style(
				'gt3pg-pro-blocks-frontend',
				GT3PG_PRO_CSSURL.'gutenberg/frontend.css',
				array(),
				filemtime(GT3PG_PRO_CSSPATH.'gutenberg/frontend.css')
			);
		}

		wp_register_script(
			'gt3pg-pro-frontend',
			GT3PG_PRO_JSURL.'gutenberg/frontend.js',
			array(
				'jquery-ui-tabs',
				'jquery-ui-accordion',
				'wp-i18n',
				'imagesloaded',
			),
			filemtime(GT3PG_PRO_JSPATH.'gutenberg/frontend.js'),
			true
		);

		wp_register_script(
			'isotope',
			GT3PG_PRO_JSURL.'isotope.pkgd.min.js',
			array(),
			filemtime(GT3PG_PRO_JSPATH.'/isotope.pkgd.min.js'),
			true
		);

		$locale  = $this->get_jed_locale_data('gt3pg_pro');
		$content = ';document.addEventListener("DOMContentLoaded", function(){window.wp && wp.i18n && wp.i18n.setLocaleData('.json_encode($locale).', "gt3pg_pro" );;window.ajaxurl = window.ajaxurl || "'.admin_url('admin-ajax.php').'";});';

		wp_script_add_data('gt3pg-pro-frontend', 'data', $content);
		wp_script_add_data('gt3pg_pro--isotope', 'data', $content);

		if($this->is_editor) {
			wp_enqueue_script('vimeo_api', 'https://player.vimeo.com/api/player.js', array(), false, true);
			wp_enqueue_script('youtube_api', 'https://www.youtube.com/iframe_api', array(), false, true);
		}
		wp_register_script('vimeo_api', 'https://player.vimeo.com/api/player.js', array(), false, true);
		wp_register_script('youtube_api', 'https://www.youtube.com/iframe_api', array(), false, true);

		$this->print_gutenberg_styles();
	}

	protected function print_gutenberg_styles(){
		if(is_singular()) {
			global $post;
			$data   = $post->post_content;
			$blocks = parse_blocks($data);
			foreach($blocks as $block) {
				$this->blocks_print_styles($block);
			}
		}
	}

	protected function blocks_print_styles($block){
		foreach($block['innerBlocks'] as $chunk) {
			$this->blocks_print_styles($chunk);
		}

		if(strpos($block['blockName'], 'gt3pg') !== false) {
			$module = str_replace('gt3pg-pro/', '', $block['blockName']);
			static::print_style($module);
		}
	}

	public static function enqueue_block_script($block){
		if($block instanceof Basic_Block) {
			$name = $block->get_name();
			if(key_exists($name, static::$block_style_depends)) {
				$scripts = static::$block_style_depends[$name];
				if(!is_array($scripts)) {
					$scripts = array( $scripts );
				}
				foreach($scripts as $script) {
					wp_enqueue_script($script);
					wp_enqueue_style($script);
				}
			}
		}
	}

	/*protected function elementor_print_style($module){
		if($module['elType'] === 'widget' && strpos($module['widgetType'], 'gt3pg') !== false) {
			$module = str_replace('gt3pg-', '', $module['widgetType']);
			$this->print_style($module);
		}
	}*/

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 */
	function editor_gutenberg(){
		static $loaded = false;
		if($loaded) {
			return;
		}
		$loaded = true;

		global $post_type;
		wp_enqueue_media();
		wp_enqueue_script('media-grid');
		wp_enqueue_script('media');
		// Scripts.

		wp_enqueue_script('block-library');
		wp_enqueue_script('editor');
		wp_enqueue_script('wp-editor');
		wp_enqueue_script('wp-components');

		wp_enqueue_style('wp-components');
		wp_enqueue_style('wp-element');
		wp_enqueue_style('wp-blocks-library');

		wp_enqueue_script(
			'gt3pg-pro-blocks-editor',
			GT3PG_PRO_JSURL.'gutenberg/editor.js',
			array(
				'wp-url',
				'wp-blocks',
				'wp-i18n',
				// Tabs
				'jquery-ui-tabs',
				'jquery-ui-accordion',
			), // Dependencies, defined above.
			filemtime(GT3PG_PRO_JSPATH.'gutenberg/editor.js'),
			true
		);

		$settings = Settings::instance();

		$strtolower_function = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

		wp_localize_script(
			'gt3pg-pro-blocks-editor',
			'gt3pg_pro',
			array(

				'defaults'  => $settings->getSettings(),
				'blocks'    => array_map($strtolower_function, $settings->getBlocks()),
				'plugins'   => array(
					'io'  => $this->optimizer_enabled,
					'pro' => $this->pro_enabled,
				),
				'post_type' => $post_type
			)
		);
		$this->admin_enqueue_scripts();
		$this->frontend_gutenberg();

		wp_enqueue_style('gt3pg-pro-blocks-frontend');
		wp_enqueue_script('gt3pg-pro-frontend');
	}

	public function print_custom_css(){
		$customCss = Settings::instance()->getSettings('basic');
		if(key_exists('gt3pg_text_before_head', $customCss) && !empty($customCss['gt3pg_text_before_head'])) {
			echo '<style id="gt3pg_pro-custom-css">'.$customCss['gt3pg_text_before_head'].'</style>';
		}
	}

	public function camelToUnderscore($string, $us = "-"){
		$patterns = array(
			'/([a-z]+)([0-9]+)/i',
			'/([a-z]+)([A-Z]+)/',
			'/([0-9]+)([a-z]+)/i'
		);
		$string   = preg_replace($patterns, '$1'.$us.'$2', $string);

		$strtolower_function = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

		return call_user_func($strtolower_function, $string);
	}

	public function get_styles($with_tags = true, $getResponsiveStyle = true){
		$style = '';
		if(is_array($this->style) && count($this->style)) {
			foreach($this->style as $selector => $_styles) {
				if(is_array($_styles) && count($_styles)) {
					$_style = '';
					foreach($_styles as $styleName => $value) {
						if(!empty($value)) {
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
		if($getResponsiveStyle) {
			$style .= $this->get_responsive_styles();
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
	public function add_style($selector, $value = null){
		$oldStyle = array();
		if(is_array($selector) && count($selector)) {

			foreach($selector as $_selector => $_value) {
				if(is_numeric($_selector)) {
					$_selector = $_value;
					$_value    = $value;
				}
				if(isset($this->style[$_selector])) {
					$oldStyle = $this->style[$_selector];
				} else {
					$oldStyle = array();
				}
				$this->style[$_selector] = array_merge($oldStyle, $_value);
			}
		} else {
			if(isset($this->style[$selector])) {
				$oldStyle = $this->style[$selector];
			} else {
				$oldStyle = array();
			}
			$this->style[$selector] = array_merge($oldStyle, $value);
		}
	}

	public function get_responsive_styles(){
		$style            = '';
		$responsive_style = '';
		if(is_array($this->responsive_style) && count($this->responsive_style)) {
			krsort($this->responsive_style);
			foreach($this->responsive_style as $maxWidth => $_styles) {
				if(is_array($_styles) && count($_styles)) {
					$this->style      = $_styles;
					$responsive_style = $this->get_styles(false, false);
					if(!empty($responsive_style)) {
						$style .= '@media screen and (max-width: '.$maxWidth.'px) {'."\t".PHP_EOL.$responsive_style."\t".PHP_EOL.'}'.PHP_EOL;
					}
				}
			}
		}

		return $style;
	}

	public function add_responsive_style($maxWidth, $selector, $value = null){
		$oldStyle = array();
		if(is_array($selector) && count($selector)) {
			foreach($selector as $_selector => $value) {
				if(isset($this->responsive_style[$maxWidth]) && isset($this->responsive_style[$maxWidth][$_selector])) {
					$oldStyle = $this->responsive_style[$maxWidth][$_selector];
				} else {
					$oldStyle = array();
				}
				$this->responsive_style[$maxWidth][$_selector] = array_merge($oldStyle, $value);
			}
		} else {
			if(isset($this->responsive_style[$maxWidth]) && isset($this->responsive_style[$maxWidth][$selector])) {
				$oldStyle = $this->responsive_style[$maxWidth][$selector];
			} else {
				$oldStyle = array();
			}
			$this->responsive_style[$maxWidth][$selector] = array_merge($oldStyle, $value);
		}
	}

	/**
	 * @param array|string $selector
	 * @param array|string $style
	 * @param array        $block
	 */
	public function add_responsive_block($selector, $style, $block){
		if(is_array($block) && key_exists('default', $block)) {
			if(is_array($selector) && count($selector)) {
				foreach($selector as $_selector) {
					// Default
					if(is_array($style) && count($style)) {
						foreach($style as $_style) {
							$this->add_style($_selector, array( $_style => $block['default'] ));
						}
					} else {
						$this->add_style($_selector, array( $style => $block['default'] ));
					}

					// Responsive
					if(key_exists('responsive', $block)
					   && $block['responsive']
					   && key_exists('data', $block)
					   && is_array($block['data'])
					   && count($block['data'])) {
						foreach($block['data'] as $name => $data) {
							if(is_array($style) && count($style)) {
								foreach($style as $_style) {
									$this->add_responsive_style($data['width'], $_selector, array( $_style => $data['value'] ));
								}
							} else {
								$this->add_responsive_style($data['width'], $_selector, array( $style => $data['value'] ));
							}
						}
					}
				}
			}
		}
	}
}

