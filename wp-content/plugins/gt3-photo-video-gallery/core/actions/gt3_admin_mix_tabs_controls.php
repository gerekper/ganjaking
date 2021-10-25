<?php
if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

add_filter('gt3_admin_mix_tabs_controls', function($controls){

	$controls[5] = new gt3pg_admin_mix_tab_control(array(
		'name'        => 'gt3pg_thumbnail_type',
		'title'       => __('Gallery Style', 'gt3pg'),
		'description' => __('You can select different types to display images thumbnails.', 'gt3pg'),
		'option'      => new gt3select(array(
			'name'    => 'gt3pg_thumbnail_type',
			'attr'    => array( new gt3attr('class', 'thumbnail-type'), ),
			'options' => new ArrayObject(array(
				'10' => new gt3options(__('Square', 'gt3pg'), 'square'),
				'20' => new gt3options(__('Rectangle', 'gt3pg'), 'rectangle'),
				'30' => new gt3options(__('Circle', 'gt3pg'), 'circle'),
				'40' => new gt3options(__('Masonry', 'gt3pg'), 'masonry'),
			))
		))
	));

	$controls[10] = new gt3pg_admin_mix_tab_control(array(
		'name'        => 'linkTo',
		'title'       => __('Link Image To', 'gt3pg'),
		'description' => __('You may use this option to choose where to link your image to.', 'gt3pg'),
		'option'      => new gt3select(array(
			'name'    => 'linkTo',
			'attr'    => array( new gt3attr('class', 'link-to'), ),
			'options' => new ArrayObject(array(
				'10' => new gt3options(array(
					'title' => __('Attachment Page', 'gt3pg'),
					'value' => 'post',
					'attr'  => new ArrayObject(array( new gt3attr('data-link-to', 'linkTo_post') )),
				)),
				'20' => new gt3options(array(
					'title' => __('File', 'gt3pg'),
					'value' => 'file',
					'attr'  => new ArrayObject(array( new gt3attr('data-link-to', 'linkTo_file') )),
				)),
				'30' => new gt3options(array(
					'title' => __('Lightbox', 'gt3pg'),
					'value' => 'lightbox',
					'attr'  => new ArrayObject(array( new gt3attr('data-link-to', 'linkTo_lightbox') )),
				)),
				'40' => new gt3options(array(
					'title' => __('None', 'gt3pg'),
					'value' => 'none',
					'attr'  => new ArrayObject(array( new gt3attr('data-link-to', 'linkTo_none') )),
				)),
			))
		))
	));
	$control      = new gt3pg_admin_mix_tab_control(array(
		'name'        => 'imageSize',
		'title'       => __('Image Size', 'gt3pg'),
		'description' => __('Please select the proper image size to display in the content.', 'gt3pg'),
		'option'      => new gt3select(array(
			'name'    => 'imageSize',
			'attr'    => array( new gt3attr('class', 'size'), ),
			'options' => new ArrayObject(array())
		))
	));
	$size_names   = apply_filters("gt3pg_image_size_names_choose", array(
		"thumbnail" => __('Thumbnail', 'gt3pg'),
		"medium"    => __('Medium', 'gt3pg'),
		"large"     => __('Large', 'gt3pg'),
		"full"      => __('Full Size', 'gt3pg'),
	));

	if(is_array($size_names) && count($size_names)) {
		$i = 1;
		foreach($size_names as $value => $title) {
			$control->option->options[($i++)*10] = new gt3options($title, $value);
		}
		$controls[20] = $control;
	}

	$control         = new gt3pg_admin_mix_tab_control(array(
		'name'        => 'columns',
		'title'       => __('Columns', 'gt3pg'),
		'description' => __('You have an option to display from one up to nine image columns.', 'gt3pg'),
		'option'      => new gt3select(array(
			'name'    => 'columns',
			'attr'    => array( new gt3attr('class', 'columns') ),
			'options' => new ArrayObject(array())
		))
	));
	$gallery_columns = intval(apply_filters("gt3_max_gallery_columns", 9));
	if($gallery_columns > 0) {
		for($i = 1; $i <= $gallery_columns; $i++) {
			$control->option->options[$i*10] = new gt3options($i, $i);
		}
		$controls[30] = $control;
	}

	$controls[50] = new gt3pg_admin_mix_tab_control(array(
		'name'        => 'random',
		'title'       => __('Random Order', 'gt3pg'),
		'description' => __('Display the images by default or randomly.', 'gt3pg'),
		'option'      => new gt3input_onoff(array(
			'name' => 'random',
		))
	));
	$controls[60] = new gt3pg_admin_mix_tab_control(array(
		'name'        => 'margin',
		'title'       => __('Margin, px', 'gt3pg'),
		'description' => __('You can add margins to the images. Please note that they are in pixels.', 'gt3pg'),
		'option'      => new gt3input(array(
			'name' => 'margin',
			'attr' => new ArrayObject(array(
				new gt3attr('class', 'short-input'),
				new gt3attr('maxlength', '3'),
			))
		))
	));

	$controls[70] = new gt3pg_admin_mix_tab_control(array(
		'name'        => 'cornersType',
		'title'       => __('Corners Type', 'gt3pg'),
		'description' => __('You can choose either right angle or rounded individually.', 'gt3pg'),
		'option'      => new gt3select(array(
			'name'    => 'cornersType',
			'attr'    => array( new gt3attr('class', 'corner-type'), ),
			'options' => new ArrayObject(array(
				'10' => new gt3options(__('Standard', 'gt3pg'), 'standard'),
				'20' => new gt3options(__('Rounded', 'gt3pg'), 'rounded'),
			))
		))
	));

	$controls[80] = new gt3pg_admin_mix_tab_control(array(
		'name'        => 'borderType',
		'title'       => __('Image Border', 'gt3pg'),
		'description' => __('You can either display or hide the image border.', 'gt3pg'),
		'option'      => new gt3input_onoff(array(
			'name' => 'borderType',
		))
	));

	$controls[90] = new gt3pg_admin_mix_tab_control(array(
		'name'            => 'borderSize',
		'title'           => __('Border Size, px', 'gt3pg'),
		'description'     => __('You can add margins to the images. Please note that they are in pixels.', 'gt3pg'),
		'main_wrap_class' => 'border-setting',
		'option'          => new gt3input(array(
			'name' => 'borderSize',
			'attr' => new ArrayObject(array(
				new gt3attr('class', 'short-input'),
				new gt3attr('maxlength', '2'),
			))
		))
	));

	$controls[100] = new gt3pg_admin_mix_tab_control(array(
		'name'            => 'borderPadding',
		'title'           => __('Border Padding, px', 'gt3pg'),
		'description'     => __('Add border padding to the image in pixels.', 'gt3pg'),
		'main_wrap_class' => 'border-setting',
		'option'          => new gt3input(array(
			'name' => 'borderPadding',
			'attr' => new ArrayObject(array(
				new gt3attr('class', 'short-input'),
				new gt3attr('maxlength', '2'),
			))
		))
	));

	$controls[110] = new gt3pg_admin_mix_tab_control(array(
		'name'            => 'borderColor',
		'title'           => __('Border Color', 'gt3pg'),
		'description'     => __('Select the desired border color.', 'gt3pg'),
		'main_wrap_class' => 'border-setting',
		'option'          => new gt3input_color(array(
			'name1' => 'gt3pg_color_picker',
			'name2' => 'borderColor',
			'data2' => 'border_col',
		))
	));

	$controls[120] = new gt3pg_admin_mix_tab_control(array(
		'name'             => 'gt3pg_text_before_head',
		'title'            => __('Custom CSS', 'gt3pg'),
		'description'      => __('You can add custom CSS to the gallery.', 'gt3pg'),
		'input_wrap_class' => 'nofloat',
		'option'           => new gt3textarea(array(
			'name' => 'gt3pg_text_before_head',
		))
	));

	return $controls;
});
