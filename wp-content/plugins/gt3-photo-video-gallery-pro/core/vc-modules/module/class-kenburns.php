<?php

namespace GT3\PhotoVideoGalleryPro\VC_modules\Module;
defined('ABSPATH') OR exit;

use GT3_Post_Type_Gallery;
use GT3\PhotoVideoGalleryPro\Block\Kenburns as Gallery;

class Kenburns extends Basic {

	protected $SHORTCODE  = 'gt3pg_kenburns';
	protected $name = 'kenburns';

	public function map(){
		return array(
			'name'        => esc_html__('Kenburns', 'gt3pg_pro'),
			"category"    => esc_html__('GT3 Galleries', 'gt3pg_pro'),
			'description' => esc_html__('Kenburns Gallery', 'gt3pg_pro'),
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
					"heading"          => esc_html__('Overlay', 'gt3pg_pro'),
					"param_name"       => "overlayState",
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
					"type"             => "colorpicker",
					"heading"          => esc_html__('Overlay Background Color', 'gt3pg_pro'),
					"param_name"       => "overlayBg",
					'std'              => '',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
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

				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Module Height', 'gt3pg_pro'),
					"param_name"       => "moduleHeight",
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"description"      => esc_html__("Set module height in px (pixels). Enter '100%' for full height mode", 'gt3pg_pro'),
					'std'              => '100%',
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Slide Duration', 'gt3pg_pro'),
					"param_name"       => "interval",
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"description"      => esc_html__("Set the timing of single slides in milliseconds", 'gt3pg_pro'),
					'std'              => 4000,
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Transition Time', 'gt3pg_pro'),
					"param_name"       => "transitionTime",
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"description"      => esc_html__("Sets Transition animation time in milliseconds", 'gt3pg_pro'),
					'std'              => 600,
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
