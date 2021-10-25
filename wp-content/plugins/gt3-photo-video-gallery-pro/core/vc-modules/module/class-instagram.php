<?php

namespace GT3\PhotoVideoGalleryPro\VC_modules\Module;
defined('ABSPATH') OR exit;

use GT3_Post_Type_Gallery;
use GT3\PhotoVideoGalleryPro\Block\Instagram as Gallery;

class Instagram extends Basic {

	protected $SHORTCODE = 'gt3pg_instagram';
	protected $name = 'instagram';

	public function map(){
		return array(
			'name'        => esc_html__('Instagram', 'gt3pg_pro'),
			"category"    => esc_html__('GT3 Galleries', 'gt3pg_pro'),
			'description' => esc_html__('Instagram', 'gt3pg_pro'),
			'base'        => $this->SHORTCODE,
			'icon'        => 'gt3-editor-icon gt3_icon_'.$this->name,
			'params'      => array(
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__('Select Source', 'gt3pg_pro'),
					'param_name' => 'source',
					'std'        => 'module',
					"value"      => array(
						esc_html__('Username', 'gt3pg_pro') => 'user',
						esc_html__('Tag', 'gt3pg_pro')      => 'tag',
					),
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Username', 'gt3pg_pro'),
					"param_name"       => 'userName',
					'std'              => '',
					'dependency'       => array(
						'element' => 'source',
						'value'   => 'user',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('User ID', 'gt3pg_pro'),
					"param_name"       => "userID",
					'std'              => '',
					'dependency'       => array(
						'element'  => 'source',
						'value'    => 'user',
						'callback' => 'gt3pgInstagramUserID',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Tag', 'gt3pg_pro'),
					"param_name"       => "tag",
					'std'              => '',
					'dependency'       => array(
						'element' => 'source',
						'value'   => 'tag',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				/* Settings */
				array(
					"type"       => "dropdown",
					"heading"    => esc_html__('Grid Type', 'gt3pg_pro'),
					"param_name" => "gridType",
					'std'        => 'default',
					"group"      => esc_html__("Settings", 'gt3pg_pro'),
					"value"      => array(
						esc_html__('Default', 'gt3pg_pro')        => 'default',
						esc_html__('Square', 'gt3pg_pro')         => 'square',
						esc_html__('Rectangle 4x3', 'gt3pg_pro')  => 'rectangle',
						esc_html__('Rectangle 16x9', 'gt3pg_pro') => 'rectangle-16x9',
						esc_html__('Circle', 'gt3pg_pro')         => 'circle',
					),
				),

				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Grid Type', 'gt3pg_pro'),
					"param_name"       => "linkTo",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')   => 'default',
						esc_html__('Instagram', 'gt3pg_pro') => 'instagram',
						esc_html__('Lightbox', 'gt3pg_pro')  => 'lightbox',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),

				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Columns', 'gt3pg_pro'),
					"param_name"       => "columns",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro') => 'default',
						esc_html__('1', 'gt3pg_pro')       => '1',
						esc_html__('2', 'gt3pg_pro')       => '2',
						esc_html__('3', 'gt3pg_pro')       => '3',
						esc_html__('4', 'gt3pg_pro')       => '4',
						esc_html__('5', 'gt3pg_pro')       => '5',
						esc_html__('6', 'gt3pg_pro')       => '6',
						esc_html__('7', 'gt3pg_pro')       => '7',
						esc_html__('8', 'gt3pg_pro')       => '8',
						esc_html__('9', 'gt3pg_pro')       => '9',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),

				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Margin, px', 'gt3pg_pro'),
					"param_name"       => "margin",
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					'std'              => 20,
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Show Images (max: 12)', 'gt3pg_pro'),
					"param_name"       => "loadMoreFirst",
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					'std'              => 12,
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

