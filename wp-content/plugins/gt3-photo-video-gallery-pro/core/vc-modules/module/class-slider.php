<?php

namespace GT3\PhotoVideoGalleryPro\VC_modules\Module;
defined('ABSPATH') OR exit;

use GT3_Post_Type_Gallery;
use GT3\PhotoVideoGalleryPro\Block\Slider as Gallery;
use GT3\PhotoVideoGalleryPro\Settings;

class Slider extends Basic {

	protected $SHORTCODE  = 'gt3pg_slider';
	protected $name = 'slider';

	public function map(){
		$settings = Settings::instance()->getDefaultsSettings();
		$basic    = $settings['basic'];
		$module   = $settings[$this->name];

		return array(
			'name'        => esc_html__('Slider', 'gt3pg_pro'),
			"category"    => esc_html__('GT3 Galleries', 'gt3pg_pro'),
			'description' => esc_html__('Slider', 'gt3pg_pro'),
			'base'        => $this->SHORTCODE,
			'icon'        => 'gt3-editor-icon gt3_icon_'.$this->name,
			'params'      => array(
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__('Select Source', 'gt3pg_pro'),
					'param_name' => 'source',
					'std'        => 'module',
					"value"      => array(
						esc_html__('Media Library (WordPress media library)', 'gt3pg_pro')    => 'module',
						esc_html__('Galleries (custom post type GT3 Galleries)', 'gt3pg_pro') => 'gallery',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__('Select Gallery', 'gt3pg_pro'),
					'param_name' => 'gallery',
					'std'        => '',
					"value"      => array_merge(array(
						esc_html__('Select Gallery') => ''
					), array_flip(GT3_Post_Type_Gallery::get_galleries())),
					'dependency' => array(
						'element' => 'source',
						'value'   => 'gallery',
					),
				),
				array(
					'type'        => 'attach_images',
					'heading'     => esc_html__('Images', 'gt3pg_pro'),
					'param_name'  => 'ids',
					'std'         => '',
					'value'       => '',
					'description' => esc_html__('Select images.', 'gt3pg_pro'),
					'dependency'  => array(
						'element' => 'source',
						'value'   => 'module',
					),
				),
				/* Settings */
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Right Click Guard', 'gt3pg_pro'),
					"param_name"       => "rightClick",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Enabled', 'gt3pg_pro')  => '1',
						esc_html__('Disabled', 'gt3pg_pro') => '0',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Random Order', 'gt3pg_pro'),
					"param_name"       => "random",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Enabled', 'gt3pg_pro')  => '1',
						esc_html__('Disabled', 'gt3pg_pro') => '0',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),

				/* Lightbox */
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Theme', 'gt3pg_pro'),
					"param_name"       => "lightboxTheme",
					'std'              => 'default',
					"group"            => esc_html__("Slider Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro') => 'default',
						esc_html__('Dark', 'gt3pg_pro')    => 'dark',
						esc_html__('Light', 'gt3pg_pro')   => 'light',
					),
					'dependency'       => array(
						'element' => 'thumbnails_Lightbox',
						'value'   => '1',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Autoplay', 'gt3pg_pro'),
					"param_name"       => "sliderAutoplay",
					'std'              => 'default',
					"group"            => esc_html__("Slider Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Enabled', 'gt3pg_pro')  => '1',
						esc_html__('Disabled', 'gt3pg_pro') => '0',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Autoplay Time (sec.)', 'gt3pg_pro'),
					"param_name"       => "sliderAutoplayTime",
					'std'              => $module['sliderAutoplayTime'],
					"group"            => esc_html__("Slider Settings", 'gt3pg_pro'),
					'dependency'       => array(
						'element' => 'sliderAutoplay',
						'value'   => '1',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('External Video Thumb', 'gt3pg_pro'),
					"param_name"       => "externalVideoThumb",
					'std'              => 'default',
					"group"            => esc_html__("Lightbox Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Enabled', 'gt3pg_pro')  => '1',
						esc_html__('Disabled', 'gt3pg_pro') => '0',
					),
					'dependency'       => array(
						'element' => 'linkTo',
						'value'   => 'lightbox',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Thumbnails', 'gt3pg_pro'),
					"param_name"       => "sliderThumbnails",
					'std'              => 'default',
					"group"            => esc_html__("Slider Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Enabled', 'gt3pg_pro')  => '1',
						esc_html__('Disabled', 'gt3pg_pro') => '0',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Select image size', 'gt3pg_pro'),
					"param_name"       => "sliderImageSize",
					'std'              => 'default',
					"group"            => esc_html__("Slider Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')           => 'default',
						esc_html__('Thumbnail (768px)', 'gt3pg_pro') => 'medium_large',
						esc_html__('Large (1024px)', 'gt3pg_pro')    => 'large',
						esc_html__('Optimized', 'gt3pg_pro')         => 'gt3pg_optimized',
						esc_html__('Full Size', 'gt3pg_pro')         => 'full',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Image Scaling', 'gt3pg_pro'),
					"param_name"       => "sliderCover",
					'std'              => 'default',
					"group"            => esc_html__("Slider Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Enabled', 'gt3pg_pro')  => '1',
						esc_html__('Disabled', 'gt3pg_pro') => '0',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Social Links', 'gt3pg_pro'),
					"param_name"       => "socials",
					'std'              => 'default',
					"group"            => esc_html__("Slider Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Enabled', 'gt3pg_pro')  => '1',
						esc_html__('Disabled', 'gt3pg_pro') => '0',
					),
					'dependency'       => array(
						'element' => 'thumbnails_Lightbox',
						'value'   => '1',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Download Image', 'gt3pg_pro'),
					"param_name"       => "allowDownload",
					'std'              => 'default',
					"group"            => esc_html__("Slider Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Enabled', 'gt3pg_pro')  => '1',
						esc_html__('Disabled', 'gt3pg_pro') => '0',
					),
					'dependency'       => array(
						'element' => 'thumbnails_Lightbox',
						'value'   => '1',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('YouTube Width', 'gt3pg_pro'),
					"param_name"       => "ytWidth",
					'std'              => 'default',
					"group"            => esc_html__("Slider Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Enabled', 'gt3pg_pro')  => '1',
						esc_html__('Disabled', 'gt3pg_pro') => '0',
					),
					'dependency'       => array(
						'element' => 'thumbnails_Lightbox',
						'value'   => '1',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
			),
		);
	}

	protected function render($atts){
		/* @var \GT3\PhotoVideoGalleryPro\Block\Basic $gallery */
		$gallery = Gallery::instance();
		$atts    = array_merge($atts, array(
			'_uid'       => mt_rand(9999, 99999),
			'_blockName' => $this->name,
			'className'  => '',

			'blockAlignment' => '',
		));
		echo $gallery->render_block($atts);
	}
}
