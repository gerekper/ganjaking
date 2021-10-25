<?php

namespace GT3\PhotoVideoGalleryPro\VC_modules\Module;
defined('ABSPATH') OR exit;

use GT3_Post_Type_Gallery;
use GT3\PhotoVideoGalleryPro\Block\Flow as Gallery;
use GT3\PhotoVideoGalleryPro\Settings;

class Flow extends Basic {
	protected $SHORTCODE = 'gt3pg_flow';
	protected $name = 'flow';

	public function map(){
		$settings = Settings::instance()->getDefaultsSettings();
		$basic    = $settings['basic'];
		$module   = $settings[$this->name];

		return array(
			'name'        => esc_html__('Flow', 'gt3pg_pro'),
			"category"    => esc_html__('GT3 Galleries', 'gt3pg_pro'),
			'description' => esc_html__('Flow Gallery', 'gt3pg_pro'),
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
					"type"       => "dropdown",
					"heading"    => esc_html__('Autoplay', 'gt3pg_pro'),
					"param_name" => "autoplay",
					'std'        => 'default',
					"group"      => esc_html__("Settings", 'gt3pg_pro'),
					"value"      => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Enabled', 'gt3pg_pro')  => '1',
						esc_html__('Disabled', 'gt3pg_pro') => '0',
					),
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Autoplay Time (s.)', 'gt3pg_pro'),
					"param_name"       => "interval",
					'std'              => $module['interval'],
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					'dependency'       => array(
						'element' => 'autoplay',
						'value'   => '1',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"       => "dropdown",
					"heading"    => esc_html__('Image Size Ratio', 'gt3pg_pro'),
					"param_name" => "imageRatio",
					'std'        => 'default',
					"group"      => esc_html__("Settings", 'gt3pg_pro'),
					"value"      => array(
						esc_html__('Default', 'gt3pg_pro') => 'default',
						esc_html__('4x3', 'gt3pg_pro')     => '4x3',
						esc_html__('16x9', 'gt3pg_pro')    => '16x9',
					),
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
					"type"             => "textfield",
					"heading"          => esc_html__('Module Height', 'gt3pg_pro'),
					"param_name"       => "moduleHeight",
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"description"      => esc_html__("Set module height in px (pixels). Enter '100%' for full height mode", 'gt3pg_pro'),
					'std'              => $module['moduleHeight'],
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Transition Time', 'gt3pg_pro'),
					"param_name"       => "transitionTime",
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"description"      => esc_html__("Sets Transition animation time in milliseconds", 'gt3pg_pro'),
					'std'              => $module['transitionTime'],
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
