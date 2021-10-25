<?php

namespace GT3\PhotoVideoGalleryPro\VC_modules\Module;
defined('ABSPATH') OR exit;

use GT3_Post_Type_Gallery;
use GT3\PhotoVideoGalleryPro\Block\FS_Slider as Gallery;

class FS_Slider extends Basic {

	protected $SHORTCODE = 'gt3pg_fsslider';
	protected $name = 'fsslider';

	public function map(){
		return array(
			'name'        => esc_html__('FS Slider', 'gt3pg_pro'),
			"category"    => esc_html__('GT3 Galleries', 'gt3pg_pro'),
			'description' => esc_html__('FussScreen Slider Gallery', 'gt3pg_pro'),
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
					"heading"          => esc_html__('Autoplay', 'gt3pg_pro'),
					"param_name"       => "autoplay",
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
					"type"             => "textfield",
					"heading"          => esc_html__('Autoplay Time ms.)', 'gt3pg_pro'),
					"param_name"       => "interval",
					'std'              => '6000',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					'dependency'       => array(
						'element' => 'autoplay',
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
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Thumbnails', 'gt3pg_pro'),
					"param_name"       => "thumbnails",
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
					"heading"          => esc_html__('Select image size', 'gt3pg_pro'),
					"param_name"       => "imageSize",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
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
					"param_name"       => "cover",
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
					"heading"          => esc_html__('Social Links', 'gt3pg_pro'),
					"param_name"       => "socials",
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
					"heading"          => esc_html__('Animation Type', 'gt3pg_pro'),
					"param_name"       => "animationType",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro') => 'default',
						esc_html__('Slide', 'gt3pg_pro')   => 'slide',
						esc_html__('Fade', 'gt3pg_pro')    => 'fade',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('YouTube Width', 'gt3pg_pro'),
					"param_name"       => "ytWidth",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
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
					"heading"          => esc_html__('Show Title', 'gt3pg_pro'),
					"param_name"       => "showTitle",
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
					"heading"          => esc_html__('Show Caption', 'gt3pg_pro'),
					"param_name"       => "showCaption",
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
					"heading"          => esc_html__('Scroll', 'gt3pg_pro'),
					"param_name"       => "scroll",
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
					"heading"          => esc_html__('Boxed', 'gt3pg_pro'),
					"param_name"       => "boxed",
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
					"heading"          => esc_html__('Footer Above Slider', 'gt3pg_pro'),
					"param_name"       => "footerAboveSlider",
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
					"heading"          => esc_html__('Border Opacity', 'gt3pg_pro'),
					"param_name"       => "borderOpacity",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro') => 'default',
						esc_html__('0%', 'gt3pg_pro')      => '0.00',
						esc_html__('5%', 'gt3pg_pro')      => '0.05',
						esc_html__('10%', 'gt3pg_pro')     => '0.10',
						esc_html__('15%', 'gt3pg_pro')     => '0.15',
						esc_html__('20%', 'gt3pg_pro')     => '0.20',
						esc_html__('25%', 'gt3pg_pro')     => '0.25',
						esc_html__('30%', 'gt3pg_pro')     => '0.30',
						esc_html__('35%', 'gt3pg_pro')     => '0.35',
						esc_html__('40%', 'gt3pg_pro')     => '0.40',
						esc_html__('45%', 'gt3pg_pro')     => '0.45',
						esc_html__('50%', 'gt3pg_pro')     => '0.50',
						esc_html__('55%', 'gt3pg_pro')     => '0.55',
						esc_html__('60%', 'gt3pg_pro')     => '0.60',
						esc_html__('65%', 'gt3pg_pro')     => '0.65',
						esc_html__('70%', 'gt3pg_pro')     => '0.70',
						esc_html__('75%', 'gt3pg_pro')     => '0.75',
						esc_html__('80%', 'gt3pg_pro')     => '0.80',
						esc_html__('85%', 'gt3pg_pro')     => '0.85',
						esc_html__('90%', 'gt3pg_pro')     => '0.90',
						esc_html__('95%', 'gt3pg_pro')     => '0.95',
						esc_html__('100%', 'gt3pg_pro')    => '1.00',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "colorpicker",
					"heading"          => esc_html__('Text Color', 'gt3pg_pro'),
					"param_name"       => "textColor",
					'std'              => '',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
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

