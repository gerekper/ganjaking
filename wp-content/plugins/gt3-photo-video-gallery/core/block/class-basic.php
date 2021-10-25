<?php

namespace GT3\PhotoVideoGallery\Block;
defined('ABSPATH') OR exit;

use GT3\PhotoVideoGallery\Settings;
use GT3\PhotoVideoGallery\Assets;
use WP_REST_Server;
use WP_REST_Request;
use GT3_Post_Type_Gallery;

abstract class Basic {
	use Traits\Get_Attachment_Image_Trait;
	use Traits\Lightbox_Trait;
	use Traits\Attributes_Trait;
	use Traits\Inline_Style_Trait;
	use Traits\Default_Attributes_Trait;
	use Traits\Clear_Attributes_Trait;

	protected $enqueue_scripts = array();
	protected $enqueue_styles = array();
	protected $_id = array();
	protected $WRAP = '';
	protected $wrapper_classes = array();
	protected static $index = 0;

	protected $render_index = 1;
	protected $slug = 'gt3pg-pro/basic';
	protected $name = 'basic';

	protected $is_rest = false;
	protected $is_editor = false;
	protected $is_elementor_editor = false;
	protected $isCategoryEnabled = false;

	protected $blacklist_atts = array(
		'nonces',
		'editLink',
		'compat',
		'icon',
	);

	protected static $instance = null;

	public static function instance(){
		if(!static::$instance instanceof static) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	protected function getPrepareAttributes(){
		return array();
	}

	public function get_name() {
		return $this->name;
	}

	public function get_slug() {
		return $this->slug;
	}

	protected function __construct(){
		$this->default_attributes = $this->getDefaultsAttributes();
		$this->name               = substr($this->slug, strpos($this->slug, '/')+1);

		add_action('init', array( $this, 'initHandler' ));

		$this->construct();
	}

	protected function construct() {

	}

	function rest_api_init(){
		$namespace = 'gt3/v1/block-renderer';

		register_rest_route($namespace,
			'/'.$this->slug,
			array(
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},	'callback' => array( $this, 'restHandler' ),
				),
			)
		);
	}

	public function restHandler(WP_REST_Request $Request){
		$data = array(
			'rendered' => $this->render_block($Request->get_params()),
		);

		return rest_ensure_response($data);
	}


	public function initHandler(){
		if(function_exists('register_block_type')) {
			register_block_type($this->slug, array(
				'attributes'      => $this->default_attributes,
				'render_callback' => array( $this, 'render_block' ),
			));

			if(is_user_logged_in() && current_user_can('edit_posts')) {
				add_action('rest_api_init', array( $this, 'rest_api_init' ));
			}
		}
	}

	function the_content($content){
		return $this->get_styles().$content;
	}

	protected function serializeImages(&$settings){
		$ids = array();
		/** @var GT3_Post_Type_Gallery $GALLERY */
		$GALLERY            = class_exists('GT3_Post_Type_Gallery') ? GT3_Post_Type_Gallery::instance() : null;
		$count              = array( '*' => 0 );
		$settings['source'] = 'module';

		switch($settings['source']) {
			case 'gallery':
				$settings['ids'] = (bool) $settings['gallery']
					? ($GALLERY ? $GALLERY->get_gallery_images($settings['gallery']) : array())
					: array();
				break;
			case 'categories':
				if(!$GALLERY || !is_array($settings['categories'])) {
					break;
				}
				$args                = array(
					'post_status'    => 'publish',
					'post_type'      => $GALLERY::post_type,
					'paged'          => 1,
					'posts_per_page' => -1
				);
				$args['tax_query']   = array(
					'relation' => 'AND',
				);
				$args['tax_query'][] = array(
					'field'    => 'slug',
					'taxonomy' => $GALLERY::taxonomy,
					'operator' => 'IN',
					'terms'    => $settings['categories'],
				);
				$module_wp_query     = new \WP_Query($args);
				$slides              = array();
				if($module_wp_query->post_count) {
					$max_count = 0;
					while($module_wp_query->have_posts()) {
						$module_wp_query->the_post();
						/* @var \WP_Post $image_post */
						$gallery_id     = get_the_ID();
						$images_gallery = $GALLERY->get_gallery_images($gallery_id);
						if(is_array($images_gallery) && count($images_gallery)) {
							$categories = get_the_terms($gallery_id, $GALLERY::taxonomy);
							if(!$categories || is_wp_error($categories)) {
								$categories = array();
							}
							$item_class_list    = array();
							$item_category_list = array();
							if(count($categories)) {
								foreach($categories as $category) {
									/* @var \WP_Term $category */
									if(!isset($settings['filter_array'][$category->slug])
									   && is_array($settings['categories'])
									   && count($settings['categories'])
									   && in_array($category->slug, $settings['categories'])) {
										$settings['filter_array'][$category->slug] = array(
											'slug' => $category->slug,
											'name' => $category->name,
										);
									}
									if(in_array($category->slug, $settings['categories'])) {
										if(!key_exists($category->slug, $count)) {
											$count[$category->slug] = 0;
										}
										$count[$category->slug] += count($images_gallery);
										$item_class_list[]      = $category->slug;
										$item_category_list[]   = '<span>'.$category->name.'</span>';
									}
								}
							}
							foreach($images_gallery as $slide) {
								$slides[$gallery_id][] = array_merge(array(
									'id'                 => $slide,
									'p'                  => $gallery_id,
									'item_class_list'    => $item_class_list,
									'item_category_list' => $item_category_list,
									'item_class'         => implode(' ', $item_class_list),
									'item_category'      => implode(' ', $item_category_list),
								),
									is_array($slide) ? $slide : array());
							}
							if($max_count < count($slides[$gallery_id])) {
								$max_count = count($slides[$gallery_id]);
							}
						}
					}
					for($i = 0; $i < $max_count; $i++) {
						foreach($slides as $slide_array) {
							if(isset($slide_array[$i])) {
								$ids[] = $slide_array[$i];
							}
						}
					}

					wp_reset_postdata();
				}
				$settings['ids'] = $ids;
				break;

		}
		if(!is_array($settings['ids']) && ((is_string($settings['ids']) || is_numeric($settings['ids'])) && !!strlen((string)$settings['ids']))) {
			try {
				$ids = json_decode($settings['ids'], true);
				if(!json_last_error() && $ids != $settings['ids']) {
					$settings['ids'] = $ids;
				} else {
					throw new \Exception('JSON decode error');
				}
			} catch(\Exception $ex) {
				$settings['ids'] = explode(',', $settings['ids']);
			}
		}
		if(is_array($settings['ids']) && !!count($settings['ids'])) {
			foreach($settings['ids'] as $image_key => &$image) {
				$image_id = (is_array($image) && key_exists('id', $image) && !key_exists('sizes', $image)) ? $image['id'] : intval($image);
				$_image   = wp_prepare_attachment_for_js($image_id);

				if($_image) {
					$image = array_diff_key(array_merge(
						$_image,
						array(
							'item_class_list'    => array(),
							'item_category_list' => array(),
							'item_category'      => '',
							'item_class'         => '',
						),
						is_array($image) ? $image : []
					), array_flip($this->blacklist_atts));
				} else {
					unset($settings['ids'][$image_key]);
				}
			}

		} else {
			$settings['ids'] = array();
		}

		$settings['filterCount']      = $count;
		$settings['filterCount']['*'] = is_array($settings['ids']) ? count($settings['ids']) : 0;
	}


	protected function add_script_depends($slug){
		if(is_array($slug) && count($slug)) {
			foreach($slug as $script) {
				$this->enqueue_scripts[] = $script;
			}
		} else {
			$this->enqueue_scripts[] = $slug;
		}
	}

	protected function add_style_depends($slug){
		if(is_array($slug) && count($slug)) {
			foreach($slug as $styles) {
				$this->enqueue_styles[] = $styles;
			}
		} else {
			$this->enqueue_styles[] = $slug;
		}
	}

	protected function enqueue_scripts(){
		if(is_array($this->enqueue_scripts) && count($this->enqueue_scripts)) {
			foreach($this->enqueue_scripts as $script) {
				wp_enqueue_script($script);
			}
		}
	}

	protected function enqueue_styles(){
		if(is_array($this->enqueue_styles) && count($this->enqueue_styles)) {
			foreach($this->enqueue_styles as $style) {
				wp_enqueue_style($style);
			}
		}
	}

	public function render_block($settings){

		self::$index++;
		$this->render_index       = 1;
		$this->responsive_style   = array();
		$this->wrapper_classes    = array();
		$this->_render_attributes = array();
		$this->style              = array();

		$this->is_rest             = defined('REST_REQUEST');
		$this->is_elementor_editor = class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode();
		$this->is_editor           = $this->is_rest || $this->is_elementor_editor;

		$this->enqueue_scripts();
		$this->enqueue_styles();

		$default_settings = Settings::instance()->getSettings();
		if($settings instanceof WP_REST_Request) {
			$settings = $settings->get_params();
		}

		$settings = array_merge($this->getDefaults(), $settings);
		$settings = $this->deprecatedSettings($settings);
		$settings = $this->removeDefaultsSettings($settings);

		if(!key_exists('_blockName', $settings) || empty($settings['_blockName'])) {
			$settings['_blockName'] = $this->name;
		}

		$default_settings = array_merge(
			$default_settings['basic'],
			key_exists($settings['_blockName'], $default_settings) ? $default_settings[$settings['_blockName']] : array()
		);

		$settings = array_merge(
			$default_settings,
			$settings
		);
		$settings = $this->checkTypeSettings($settings);

		$this->_id       = 'uid-'.substr(md5($settings['_uid'].mt_rand(100, 9999)), 0, 16);
		$this->WRAP      = esc_html('.'.$this->_id.' ');
		$wrapper_classes = array(
			$this->_id,
			'gt3pg-pro--wrapper',
			'gt3pg-pro--'.(str_replace('_', '-', $this->name)),
			$settings['className'],
		);

		$this->add_render_attribute('_wrapper', 'id', $this->_id);
		$this->add_render_attribute('_wrapper', 'data-gt3pg-block', $this->name);
		$this->add_render_attribute('_wrapper', 'data-index', self::$index);

		$settings['blockAlignment'] = isset($settings['align']) && !empty($settings['align']) ? $settings['align'] : $settings['blockAlignment'];
		if(!empty($settings['blockAlignment'])) {
			$this->add_render_attribute('_wrapper', 'data-align', $settings['blockAlignment']);
		}

		if(!empty($settings['blockAnimation']) && is_array($settings['blockAnimation']) && key_exists('type', $settings['blockAnimation']) && !empty($settings['blockAnimation']['type'])) {
			$wrapper_classes[] = 'animated';
			$this->add_render_attribute('_wrapper', 'data-animation', $settings['blockAnimation']['type']);

			if(key_exists('infinite', $settings['blockAnimation']) && (bool) $settings['blockAnimation']['infinite']) {
				$wrapper_classes[] = 'infinite';
			}
			if(key_exists('speed', $settings['blockAnimation']) && $settings['blockAnimation']['speed'] !== 'normal') {
				$wrapper_classes[] = $settings['blockAnimation']['speed'];
			}
			if(key_exists('delay', $settings['blockAnimation']) && $settings['blockAnimation']['delay'] > 0) {
				$wrapper_classes[] = sprintf('delay-%ss', (int) $settings['blockAnimation']['delay']);
			}
		}

		$settings['uid']          = $this->_id;
		$settings['WRAP']         = $this->WRAP;
		$settings['filter_array'] = array();

		$this->serializeImages($settings);

		if(!is_array($settings['ids'])) {
			$settings['ids'] = array();
		}

		ob_start();
		$this->render($settings);
		$content = ob_get_clean();
		Assets::enqueue_block_script($this);

		$styles = '';
		if($this->style_print) {
			$styles = $this->get_styles();
		}

		$wrapper_classes = array_merge($wrapper_classes, $this->wrapper_classes);

		$this->add_render_attribute('_wrapper', 'class', $wrapper_classes);

		return $styles.'<div '.$this->get_render_attribute_string('_wrapper').'>'.$content.'</div>';
	}

	protected function render($settings){
	}

	protected function _renderItem(){
		$this->render_index++;
	}

	protected function checkImagesNoEmpty($settings){
		if($this->is_editor && !count($settings['ids'])) {
			$msg = $this->is_elementor_editor ? esc_html__('Please Select Images in Widget Editor', 'gt3pg') : esc_html__('Please Select Images in Block Editor', 'gt3pg');
			echo '<div class="gt3_description_info">' . $msg . '</div>';
		}
	}
}
