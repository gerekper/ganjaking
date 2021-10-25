<?php

namespace GT3\PhotoVideoGalleryPro\Divi\Modules;

defined('ABSPATH') OR exit;

use ET_Builder_Module;
use GT3_Post_Type_Gallery;



class Gallery extends ET_Builder_Module {
	public $slug = 'gt3-gallery-divi';

	public $vb_support = 'off';

	function init(){
		$this->name      = esc_html__('GT3 Gallery', 'gt3pg_pro');
		$this->icon_path = __DIR__.'/svg/gallery.svg';
	}

	function get_fields(){

		if(!class_exists('\GT3_Post_Type_Gallery')) {
			return array();
		}

		$posts = GT3_Post_Type_Gallery::get_galleries();

		$options = array(
			'none' => esc_html__('Select a Gallery', 'et_builder'),
		);

		foreach($posts as $id => $title) {
			$options[(string) $id] = $title;
		}

		return array(
			'gallery' => array(
				'label'           => esc_html__('GT3 Gallery', 'gt3pg_pro'),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'class'           => 'et-pb-social-network',
				'options'         => $options,
				'default'         => 'none',
				'description'     => esc_html__('Choose the Gallery', 'gt3pg_pro'),
			),
		);
	}


	/**
	 * @param array  $attrs
	 * @param string $content
	 * @param string $render_slug
	 *
	 * @return mixed|string|\WP_REST_Response
	 */
	function render($attrs, $content = '', $render_slug = 'gt3-gallery-divi'){

		if(!class_exists('\GT3_Post_Type_Gallery') || !key_exists('gallery', $attrs) || empty($attrs['gallery']) || $attrs['gallery'] === 'none') {
			return '';
		}

		$content = GT3_Post_Type_Gallery::instance()->render_shortcode(array( 'id' => $attrs['gallery'] ));

		return $content;
	}
}

