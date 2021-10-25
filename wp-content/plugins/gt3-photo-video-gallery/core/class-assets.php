<?php

namespace GT3\PhotoVideoGallery;

defined('ABSPATH') OR exit;

use GT3\PhotoVideoGallery\Block\Basic as Basic_Block;
use ELementor\Plugin as Elementor_Plugin;

class Assets {
	private static $instance = null;

	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private static $dist_url  = '';
	private static $dist_path = '';
	private static $js_url    = '';
	private static $js_path   = '';
	private static $css_url   = '';
	private static $css_path  = '';

	protected $assets = array(
		'gt3pg_pro--core'            => array(),
		'gt3pg_pro--lightbox'        => array( 'gt3pg_pro--core' ),
		'gt3pg_pro--isotope'         => array(),
		'gt3pg_pro--gallery-isotope' => array( 'gt3pg_pro--core', 'gt3pg_pro--isotope' ),
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
		'grid'    => array( 'gt3pg_pro--gallery-isotope', 'gt3pg-lite-frontend' ),
		'masonry' => array( 'gt3pg_pro--gallery-isotope', 'gt3pg-lite-frontend' ),
		'packery' => array( 'gt3pg_pro--gallery-isotope', 'gt3pg-lite-frontend' ),
	);

	private $style            = array();
	private $responsive_style = array();

	private $pro_enabled;
	private $optimizer_enabled;

	private function __construct(){
		add_action(
			'after_setup_theme', function(){
			$this->optimizer_enabled = defined('GT3PG_FHD_PLUGINPATH');
			$this->pro_enabled       = defined('GT3PG_PRO_PLUGIN_VERSION');

			if(!$this->pro_enabled) {
				// Frontend
				add_action('elementor/frontend/before_enqueue_scripts', array( $this, 'frontend_elementor' ));
				add_action('enqueue_block_assets', array( $this, 'frontend_gutenberg' ));
				add_action('wp_enqueue_scripts', array( $this, 'frontend_gutenberg' ));

				// Admin area
				add_action('elementor/editor/before_enqueue_scripts', array( $this, 'editor_elementor' ));
				add_action('enqueue_block_editor_assets', array( $this, 'editor_gutenberg' ));
				add_action('elementor/preview/enqueue_styles', array( $this, 'print_all_assets' ));

				add_action('wp_enqueue_scripts', array( $this, 'register_script__action' ), 0);
				add_action('enqueue_block_assets', array( $this, 'register_script__action' ), 0);
				add_action('elementor/frontend/before_enqueue_scripts', array( $this, 'register_script__action' ), 0);
			}
		}, 15
		);
		///
		self::$dist_url = plugin_dir_url(GT3PG_LITE_PLUGIN_ROOT_FILE).'dist/';
		self::$js_url   = self::$dist_url.'js/';
		self::$css_url  = self::$dist_url.'css/';

		self::$dist_path = plugin_dir_path(GT3PG_LITE_PLUGIN_ROOT_FILE).'dist/';
		self::$js_path   = self::$dist_path.'js/';
		self::$css_path  = self::$dist_path.'css/';
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
				}
			}
		}
	}

	public static function get_dist_url() {
		return self::$dist_url;
	}

	public function print_all_assets(){
		$this->register_script__action();

		foreach($this->assets as $name => $asset) {
			wp_enqueue_script($name);
			wp_enqueue_style($name);
		}
		wp_enqueue_style('gt3pg-lite-frontend');
	}

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


	public function getPlugins(){
		return array(
			'io'  => $this->optimizer_enabled,
			'pro' => $this->pro_enabled,
		);
	}

	public function pluginEnable($name){
		$plugins = $this->getPlugins();
		if(key_exists($name, $plugins)) {
			return $plugins[$name];
		} else {
			return false;
		}
	}

	public function frontend_elementor(){
		$this->print_elementor_styles();
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

	public function print_style($module){
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

	protected function elementor_print_style($module){
		if($module['elType'] === 'widget' && strpos($module['widgetType'], 'gt3pg') !== false) {
			$module = str_replace('gt3pg-', '', $module['widgetType']);
			$this->print_style($module);
		}
	}

	public function editor_elementor(){
		$this->print_all_assets();
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

	/**
	 * Enqueue Gutenberg block assets for both frontend + backend.
	 */
	function frontend_gutenberg(){
//		wp_enqueue_style('wp-blocks');
		wp_register_style(
			'gt3pg-lite-frontend',
			GT3PG_LITE_CSS_URL.'gutenberg/frontend.css',
			array(),
			filemtime(GT3PG_LITE_CSS_PATH.'gutenberg/frontend.css')
		);

		$locale  = $this->get_jed_locale_data('gt3pg');
		$content = ';document.addEventListener("DOMContentLoaded", function(){window.wp && wp.i18n && wp.i18n.setLocaleData('.json_encode($locale).', "gt3pg" );;window.ajaxurl = window.ajaxurl || "'.admin_url('admin-ajax.php').'";});';

		wp_script_add_data('gt3pg_pro--core', 'data', $content);

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
			$this->print_style($module);
		}
	}



	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 */
	function editor_gutenberg(){
		wp_enqueue_media();
		wp_enqueue_script('media-grid');
		wp_enqueue_script('media');
		// Scripts.
		wp_enqueue_script(
			'gt3pg-lite-blocks-editor',
			GT3PG_LITE_JS_URL.'gutenberg/editor.js',
			array(
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				// Tabs
				'jquery-ui-tabs',
				'jquery-ui-accordion',
			), // Dependencies, defined above.
			filemtime(GT3PG_LITE_JS_PATH.'gutenberg/editor.js'),
			true
		);

		$settings = Settings::instance();

		wp_localize_script(
			'gt3pg-lite-blocks-editor',
			'gt3pg_lite',
			array(
				'defaults'         => $settings->getSettings(),
				'blocks'           => array_map('strtolower', $settings->getBlocks()),
				'plugins'          => array(
					'io'  => $this->optimizer_enabled,
					'pro' => $this->pro_enabled,
				),
				'_watermark_nonce' => wp_create_nonce('process_watermarks'),
				'_nonce'   => wp_create_nonce('gallery_settings'),

			)
		);
		// Styles.
		wp_enqueue_style(
			'gt3pg-lite-blocks-editor',
			GT3PG_LITE_CSS_URL.'gutenberg/editor.css',
			array( 'wp-edit-blocks' ),
			filemtime(GT3PG_LITE_CSS_PATH.'gutenberg/editor.css')
		);

		$this->frontend_gutenberg();
		wp_enqueue_style('gt3pg-lite-frontend');
		wp_enqueue_script('gt3pg-lite-frontend');

		wp_enqueue_style('blueimp-gallery.css');
		wp_enqueue_script('blueimp-gallery.js');
	}

	public function camelToUnderscore($string, $us = "-"){
		$patterns = array(
			'/([a-z]+)([0-9]+)/i',
			'/([a-z]+)([A-Z]+)/',
			'/([0-9]+)([a-z]+)/i'
		);
		$string   = preg_replace($patterns, '$1'.$us.'$2', $string);

		return strtolower($string);
	}

	public function getStyles($with_tags = true, $getResponsiveStyle = true){
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
			$style .= $this->getResponsiveStyles();
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
	public function addStyle($selector, $value = null){
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

	public function getResponsiveStyles(){
		$style = '';
		if(is_array($this->responsive_style) && count($this->responsive_style)) {
			krsort($this->responsive_style);
			foreach($this->responsive_style as $maxWidth => $_styles) {
				if(is_array($_styles) && count($_styles)) {
					$this->style      = $_styles;
					$responsive_style = $this->getStyles(false, false);
					if(!empty($responsive_style)) {
						$style .= '@media screen and (max-width: '.$maxWidth.'px) {'."\t".PHP_EOL.$responsive_style."\t".PHP_EOL.'}'.PHP_EOL;
					}
				}
			}
		}

		return $style;
	}

	public function addResponsiveStyle($maxWidth, $selector, $value = null){
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
	public function addResponsiveBlock($selector, $style, $block){
		if(is_array($block) && key_exists('default', $block)) {
			if(is_array($selector) && count($selector)) {
				foreach($selector as $_selector) {
					// Default
					if(is_array($style) && count($style)) {
						foreach($style as $_style) {
							$this->addStyle($_selector, array( $_style => $block['default'] ));
						}
					} else {
						$this->addStyle($_selector, array( $style => $block['default'] ));
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
									$this->addResponsiveStyle($data['width'], $_selector, array( $_style => $data['value'] ));
								}
							} else {
								$this->addResponsiveStyle($data['width'], $_selector, array( $style => $data['value'] ));
							}
						}
					}
				}
			}
		}
	}
}


