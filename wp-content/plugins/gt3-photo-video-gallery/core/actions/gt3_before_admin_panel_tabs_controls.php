<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly

	add_filter('gt3_before_admin_panel_tabs_controls', function ($panels){
		$panel = null;
		$panels["10"] = new gt3panel_control(array(
			'title'  => __('Link Image To', 'gt3pg'),
			'name'   => 'link',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting' ),
			) ),
			'option' => new gt3panel_select(array(
				'name'    => 'link',
				'options' => new ArrayObject(array(
					'10' => new gt3options( __('Default', 'gt3pg'), 'default' ),
					'20' => new gt3options( __('Attachment Page', 'gt3pg'), 'post' ),
					'30' => new gt3options( __('File', 'gt3pg'), 'file' ),
					'40' => new gt3options( __('Lightbox', 'gt3pg'), 'lightbox' ),
					'50' => new gt3options( __('None', 'gt3pg'), 'none' ),
				) )
			) )
		) );


		$panel = new gt3panel_control(array(
			'title'  => __('Image Size', 'gt3pg'),
			'name'   => 'size',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting size' ),
			) ),
			'option' => new gt3panel_select(array(
				'name'    => 'size',
				'options' => new ArrayObject(array(
					'10' => new gt3options( __('Default', 'gt3pg'), 'default' ),
				) )
			) )
		) );
		$size_names = apply_filters( "image_size_names_choose", array(
			"thumbnail" => __('Thumbnail','gt3pg'),
			"medium"    => __('Medium','gt3pg'),
			"large"     => __('Large','gt3pg'),
			"full"      => __('Full Size','gt3pg'),
		) );


		if ( is_array( $size_names ) && count( $size_names ) ) {
			$i = 2;
			foreach ( $size_names as $value => $title ) {
				$panel->option->options[ ( $i ++ )*10 ] = new gt3options( $title, $value );
			}

			$panels["20"] = $panel;
		}

		$panel = new gt3panel_control(array(
			'title'  => __('Columns', 'gt3pg'),
			'name'   => 'columns',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting hidden' ),
			) ),
			'option' => new gt3panel_select(array(
				'name'    => 'columns',
				'options' => new ArrayObject(array(
					//'10' => new gt3options( __('Default', 'gt3pg'), 'default' ),
				) )
			) )
		) );
		$gallery_columns = intval( apply_filters( "gt3_max_gallery_columns", 9 ) );
		if ( $gallery_columns > 0 ) {
			$i = 1;
			for ( $f = 1; $f <= $gallery_columns; $f ++ ) {
				$panel->option->options[ ( $i ++ )*10 ] = new gt3options( $f, $f );
			}
			$panels["30"] = $panel;
		}

		$panel = new gt3panel_control(array(
			'title'  => __('Columns', 'gt3pg'),
			'name'   => 'real_columns',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting' ),
			) ),
			'option' => new gt3panel_select(array(
				'name'    => 'real_columns',
				'options' => new ArrayObject(array(
					'10' => new gt3options( __('Default', 'gt3pg'), 'default' ),
				) )
			) )
		) );
		if ( $gallery_columns > 0 ) {
			$i = 2;
			for ( $f = 1; $f <= $gallery_columns; $f ++ ) {
				$panel->option->options[ ( $i ++ )*10 ] = new gt3options( $f, $f );
			}
			$panels["40"] = $panel;
		}

		$panels["50"] = new gt3panel_control(array(
			'title'  => __('Random Order', 'gt3pg'),
			'name'   => 'orderby',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting random' ),
			) ),
			'option' => new gt3panel_input(array(
				'name' => 'orderby',
				'type' => 'checkbox',
				'attr' => new ArrayObject(array(
					new gt3attr( 'data-setting', '_orderbyRandom' ),
				) )
			) )
		) );

		$panels["60"] = new gt3panel_control(array(
			'title'  => __('Random Order', 'gt3pg'),
			'name'   => 'rand_order',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting' ),
			) ),
			'option' => new gt3panel_select(array(
				'name'    => 'rand_order',
				'options' => new ArrayObject(array(
					'10' => new gt3options( __('Default', 'gt3pg'), 'default' ),
					'20' => new gt3options( __('Random', 'gt3pg'), 'rand' ),
					'30' => new gt3options( __('Ordered', 'gt3pg'), 'menu_order ID' ),
				) )
			) )
		) );

		$panels["70"] = new gt3panel_control(array(
			'title'  => __('Margin', 'gt3pg'),
			'name'   => 'is_margin',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting' ),
			) ),
			'option' => new gt3panel_select(array(
				'name'    => 'is_margin',
				'options' => new ArrayObject(array(
					'10' => new gt3options( __('Default', 'gt3pg'), 'default' ),
					'20' => new gt3options( __('Custom', 'gt3pg'), 'custom' ),
				) )
			) )
		) );

		$panels["80"] = new gt3panel_control(array(
			'title'  => __('Margin, px','gt3pg'),
			'name'   => 'margin',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting margin' ),
			) ),
			'option' => new gt3panel_input(array(
				'name' => 'margin',
				'attr' => new ArrayObject(array(
					new gt3attr( 'class', 'short-input' ),
					new gt3attr( 'data-setting', 'margin' ),
				) )
			) )
		) );

		$panels["90"] = new gt3panel_control(array(
			'title'  => __('Thumbnail Type', 'gt3pg'),
			'name'   => 'thumb_type',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting' ),
			) ),
			'option' => new gt3panel_select(array(
				'name'    => 'thumb_type',
				'options' => new ArrayObject(array(
					'10' => new gt3options( __('Default', 'gt3pg'), 'default' ),
					'20' => new gt3options( __('Square', 'gt3pg'), 'square' ),
					'30' => new gt3options( __('Rectangle', 'gt3pg'), 'rectangle' ),
					'40' => new gt3options( __('Circle', 'gt3pg'), 'circle' ),
					'50' => new gt3options( __('Masonry', 'gt3pg'), 'masonry' ),
				) )
			) )
		) );

		$panels["100"] = new gt3panel_control(array(
			'title'  => __('Corners Type', 'gt3pg'),
			'name'   => 'corners_type',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting' ),
			) ),
			'option' => new gt3panel_select(array(
				'name'    => 'corners_type',
				'options' => new ArrayObject(array(
					'10' => new gt3options( __('Default', 'gt3pg'), 'default' ),
					'20' => new gt3options( __('Standard', 'gt3pg'), 'standard' ),
					'30' => new gt3options( __('Rounded', 'gt3pg'), 'rounded' ),
				) )
			) )
		) );

		$panels["110"] = new gt3panel_control(array(
			'title'  => __('Image Border', 'gt3pg'),
			'name'   => 'border_type',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting' ),
			) ),
			'option' => new gt3panel_select(array(
				'name'    => 'border_type',
				'options' => new ArrayObject(array(
					'10' => new gt3options( __('Default', 'gt3pg'), 'default' ),
					'20' => new gt3options( __('On', 'gt3pg'), 'on' ),
					'30' => new gt3options( __('Off', 'gt3pg'), 'off' ),
				) )
			) )
		) );

		$panels["120"] = new gt3panel_control(array(
			'title'  => __('Border Size, px','gt3pg'),
			'name'   => 'border_size',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting border_setting' ),
			) ),
			'option' => new gt3panel_input(array(
				'name' => 'border_size',
				'attr' => new ArrayObject(array(
					new gt3attr( 'class', 'short-input' ),
					new gt3attr( 'data-setting', 'border_size' ),
				) )
			) )
		) );

		$panels["130"] = new gt3panel_control(array(
			'title'  => __('Border Padding, px','gt3pg'),
			'name'   => 'border_padding',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting border_setting' ),
			) ),
			'option' => new gt3panel_input(array(
				'name' => 'border_padding',
				'attr' => new ArrayObject(array(
					new gt3attr( 'class', 'short-input' ),
					new gt3attr( 'data-setting', 'border_padding' ),
				) )
			) )
		) );

		$panels["140"] = new gt3panel_control(array(
			'title'  => __('Border Color', 'gt3pg'),
			'name'   => 'border_col',
			'attr'   => new ArrayObject(array(
				new gt3attr( 'class', 'gt3pg_setting border_setting' ),
			) ),
			'option' => new gt3panel_input_color(array(
				'name1' => 'color_picker',
				'name2' => 'border_col',
				'data2' => 'border_col',
			) )
		) );
		return $panels;
	});