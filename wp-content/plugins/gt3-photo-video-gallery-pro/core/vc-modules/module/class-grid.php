<?php

namespace GT3\PhotoVideoGalleryPro\VC_modules\Module;
defined('ABSPATH') OR exit;

use GT3_Post_Type_Gallery;
use GT3\PhotoVideoGalleryPro\Block\Grid as Gallery;
use GT3\PhotoVideoGalleryPro\Settings;

class Grid extends Basic {
	protected $SHORTCODE = 'gt3pg_grid';
	protected $name = 'grid';

	public function map(){
		$settings = Settings::instance()->getDefaultsSettings();
		$basic    = $settings['basic'];
		$module   = $settings[$this->name];

		return array(
			'name'        => esc_html__('Grid', 'gt3pg_pro'),
			"category"    => esc_html__('GT3 Galleries', 'gt3pg_pro'),
			'description' => esc_html__('Grid', 'gt3pg_pro'),
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
					'type'       => 'button',
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
						esc_html__('Select Gallery', 'gt3pg_pro') => ''
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
					"heading"          => esc_html__('Link Image To', 'gt3pg_pro'),
					"param_name"       => "linkTo",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')         => 'default',
						esc_html__('Attachment Page', 'gt3pg_pro') => 'post',
						esc_html__('File', 'gt3pg_pro')            => 'file',
						esc_html__('Lightbox', 'gt3pg_pro')        => 'lightbox',
						esc_html__('None', 'gt3pg_pro')            => 'none',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Show Image Title', 'gt3pg_pro'),
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
					"heading"          => esc_html__('Show Captions', 'gt3pg_pro'),
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
					"heading"          => esc_html__('Lazy Load', 'gt3pg_pro'),
					"param_name"       => "lazyLoad",
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
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Select Image Size', 'gt3pg_pro'),
					"param_name"       => "imageSize",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')           => 'default',
						esc_html__('Medium (300px)', 'gt3pg_pro')    => 'medium',
						esc_html__('Thumbnail (768px)', 'gt3pg_pro') => 'medium_large',
						esc_html__('Large (1024px)', 'gt3pg_pro')    => 'large',
						esc_html__('Optimized', 'gt3pg_pro')         => 'gt3pg_optimized',
						esc_html__('Full Size', 'gt3pg_pro')         => 'full',
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
					"heading"          => esc_html__('Margin', 'gt3pg_pro'),
					"param_name"       => "isMargin",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro') => 'default',
						esc_html__('Custom', 'gt3pg_pro')  => 'custom',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Margin, px', 'gt3pg_pro'),
					"param_name"       => "margin",
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					'dependency'       => array(
						'element' => 'isMargin',
						'value'   => 'custom',
					),
					'std'              => $module['margin'],
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Corners Type', 'gt3pg_pro'),
					"param_name"       => "cornersType",
					'std'              => 'default',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')  => 'default',
						esc_html__('Standard', 'gt3pg_pro') => 'standard',
						esc_html__('Rounded', 'gt3pg_pro')  => 'rounded',
					),
					'dependency'       => array(
						'element'            => 'gridType',
						'value_not_equal_to' => 'circle',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Image Border', 'gt3pg_pro'),
					"param_name"       => "borderType",
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
					"heading"          => esc_html__('Border Size, px', 'gt3pg_pro'),
					"param_name"       => "borderSize",
					'std'              => $module['borderSize'],
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					'dependency'       => array(
						'element' => 'borderType',
						'value'   => '1',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "textfield",
					"heading"          => esc_html__('Border Padding, px', 'gt3pg_pro'),
					"param_name"       => "borderPadding",
					'std'              => $module['borderPadding'],
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					'dependency'       => array(
						'element' => 'borderType',
						'value'   => '1',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "colorpicker",
					"heading"          => esc_html__('Border Color', 'gt3pg_pro'),
					"param_name"       => "borderColor",
					'std'              => '',
					"group"            => esc_html__("Settings", 'gt3pg_pro'),
					'dependency'       => array(
						'element' => 'borderType',
						'value'   => '1',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				/* Lightbox */
				array(
					"type"       => "dropdown",
					"heading"    => esc_html__('Theme', 'gt3pg_pro'),
					"param_name" => "lightboxTheme",
					'std'        => 'default',
					"group"      => esc_html__("Lightbox Settings", 'gt3pg_pro'),
					"value"      => array(
						esc_html__('Default', 'gt3pg_pro') => 'default',
						esc_html__('Dark', 'gt3pg_pro')    => 'dark',
						esc_html__('Light', 'gt3pg_pro')   => 'light',
					),
					'dependency' => array(
						'element' => 'linkTo',
						'value'   => 'lightbox',
					),
//						'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Autoplay', 'gt3pg_pro'),
					"param_name"       => "lightboxAutoplay",
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
					"type"             => "textfield",
					"heading"          => esc_html__('Autoplay Time (sec.)', 'gt3pg_pro'),
					"param_name"       => "lightboxAutoplayTime",
					'std'              => $basic['lightboxAutoplayTime'],
					"group"            => esc_html__("Lightbox Settings", 'gt3pg_pro'),
					'dependency'       => array(
						'element' => 'lightboxAutoplay',
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
					"param_name"       => "lightboxThumbnails",
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
					"heading"          => esc_html__('Select image size', 'gt3pg_pro'),
					"param_name"       => "lightboxImageSize",
					'std'              => 'default',
					"group"            => esc_html__("Lightbox Settings", 'gt3pg_pro'),
					"value"            => array(
						esc_html__('Default', 'gt3pg_pro')           => 'default',
						esc_html__('Thumbnail (768px)', 'gt3pg_pro') => 'medium_large',
						esc_html__('Large (1024px)', 'gt3pg_pro')    => 'large',
						esc_html__('Optimized', 'gt3pg_pro')         => 'gt3pg_optimized',
						esc_html__('Full Size', 'gt3pg_pro')         => 'full',
					),
					'dependency'       => array(
						'element' => 'linkTo',
						'value'   => 'lightbox',
					),
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					"type"             => "dropdown",
					"heading"          => esc_html__('Image Scaling', 'gt3pg_pro'),
					"param_name"       => "lightboxCover",
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
					"heading"          => esc_html__('Deeplink', 'gt3pg_pro'),
					"param_name"       => "lightboxDeeplink",
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
					"heading"          => esc_html__('Social Links', 'gt3pg_pro'),
					"param_name"       => "socials",
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
					"heading"          => esc_html__('Download Image', 'gt3pg_pro'),
					"param_name"       => "allowDownload",
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
					"heading"          => esc_html__('YouTube Width', 'gt3pg_pro'),
					"param_name"       => "ytWidth",
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
