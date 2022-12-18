<?php

add_filter( 'the_excerpt', 'do_shortcode' );

if ( ! function_exists( 'porto_vc_commons' ) ) {
	function porto_vc_commons( $asset = '' ) {
		switch ( $asset ) {
			case 'accordion':
				return Porto_VcSharedLibrary::getAccordionType();
			case 'accordion_size':
				return Porto_VcSharedLibrary::getAccordionSize();
			case 'align':
				return Porto_VcSharedLibrary::getTextAlign();
			case 'tabs':
				return Porto_VcSharedLibrary::getTabsPositions();
			case 'tabs_type':
				return Porto_VcSharedLibrary::getTabsType();
			case 'tabs_icon_style':
				return Porto_VcSharedLibrary::getTabsIconStyle();
			case 'tabs_icon_effect':
				return Porto_VcSharedLibrary::getTabsIconEffect();
			case 'tour':
				return Porto_VcSharedLibrary::getTourPositions();
			case 'tour_type':
				return Porto_VcSharedLibrary::getTourType();
			case 'separator':
				return Porto_VcSharedLibrary::getSeparator();
			case 'separator_type':
				return Porto_VcSharedLibrary::getSeparatorType();
			case 'separator_style':
				return Porto_VcSharedLibrary::getSeparatorStyle();
			case 'separator_repeat':
				return Porto_VcSharedLibrary::getSeparatorRepeat();
			case 'separator_position':
				return Porto_VcSharedLibrary::getSeparatorPosition();
			case 'separator_icon_style':
				return Porto_VcSharedLibrary::getSeparatorIconStyle();
			case 'separator_icon_size':
				return Porto_VcSharedLibrary::getSeparatorIconSize();
			case 'separator_icon_pos':
				return Porto_VcSharedLibrary::getSeparatorIconPosition();
			case 'separator_elements':
				return Porto_VcSharedLibrary::getSeparatorElements();
			case 'colors':
				return Porto_VcSharedLibrary::getColors();
			case 'contextual':
				return Porto_VcSharedLibrary::getContextual();
			case 'progress_border_radius':
				return Porto_VcSharedLibrary::getProgressBorderRadius();
			case 'progress_size':
				return Porto_VcSharedLibrary::getProgressSize();
			case 'circular_view_type':
				return Porto_VcSharedLibrary::getCircularViewType();
			case 'circular_view_size':
				return Porto_VcSharedLibrary::getCircularViewSize();
			case 'section_skin':
				return Porto_VcSharedLibrary::getSectionSkin();
			case 'section_color_scale':
				return Porto_VcSharedLibrary::getSectionColorScale();
			case 'section_text_color':
				return Porto_VcSharedLibrary::getSectionTextColor();
			case 'heading_border_type':
				return Porto_VcSharedLibrary::getHeadingBorderType();
			case 'heading_border_size':
				return Porto_VcSharedLibrary::getHeadingBorderSize();
			default:
				return array();
		}
	}
}

if ( ! class_exists( 'Porto_VcSharedLibrary' ) ) {

	class Porto_VcSharedLibrary {

		public static function getTextAlign() {
			return array(
				__( 'None', 'porto' )    => '',
				__( 'Left', 'porto' )    => 'left',
				__( 'Right', 'porto' )   => 'right',
				__( 'Center', 'porto' )  => 'center',
				__( 'Justify', 'porto' ) => 'justify',
			);
		}

		public static function getTabsPositions() {
			return array(
				__( 'Default', 'porto' )        => '',
				__( 'Top left', 'porto' )       => 'top-left',
				__( 'Top right', 'porto' )      => 'top-right',
				__( 'Bottom left', 'porto' )    => 'bottom-left',
				__( 'Bottom right', 'porto' )   => 'bottom-right',
				__( 'Top justify', 'porto' )    => 'top-justify',
				__( 'Bottom justify', 'porto' ) => 'bottom-justify',
				__( 'Top center', 'porto' )     => 'top-center',
				__( 'Bottom center', 'porto' )  => 'bottom-center',
			);
		}

		public static function getTabsType() {
			return array(
				__( 'Default', 'porto' ) => '',
				__( 'Simple', 'porto' )  => 'tabs-simple',
			);
		}

		public static function getTabsIconStyle() {
			return array(
				__( 'Default', 'porto' ) => '',
				__( 'Style 1', 'porto' ) => 'featured-boxes-style-1',
				__( 'Style 2', 'porto' ) => 'featured-boxes-style-2',
				__( 'Style 3', 'porto' ) => 'featured-boxes-style-3',
				__( 'Style 4', 'porto' ) => 'featured-boxes-style-4',
				__( 'Style 5', 'porto' ) => 'featured-boxes-style-5',
				__( 'Style 6', 'porto' ) => 'featured-boxes-style-6',
				__( 'Style 7', 'porto' ) => 'featured-boxes-style-7',
				__( 'Style 8', 'porto' ) => 'featured-boxes-style-8',
			);
		}

		public static function getTabsIconEffect() {
			return array(
				__( 'Default', 'porto' )  => '',
				__( 'Effect 1', 'porto' ) => 'featured-box-effect-1',
				__( 'Effect 2', 'porto' ) => 'featured-box-effect-2',
				__( 'Effect 3', 'porto' ) => 'featured-box-effect-3',
				__( 'Effect 4', 'porto' ) => 'featured-box-effect-4',
				__( 'Effect 5', 'porto' ) => 'featured-box-effect-5',
				__( 'Effect 6', 'porto' ) => 'featured-box-effect-6',
				__( 'Effect 7', 'porto' ) => 'featured-box-effect-7',
			);
		}

		public static function getTourPositions() {
			return array(
				__( 'Left', 'porto' )  => 'vertical-left',
				__( 'Right', 'porto' ) => 'vertical-right',
			);
		}

		public static function getTourType() {
			return array(
				__( 'Default', 'porto' )    => '',
				__( 'Navigation', 'porto' ) => 'tabs-navigation',
			);
		}

		public static function getSeparator() {
			return array(
				__( 'Normal', 'porto' ) => '',
				__( 'Short', 'porto' )  => 'short',
				__( 'Tall', 'porto' )   => 'tall',
				__( 'Taller', 'porto' ) => 'taller',
			);
		}

		public static function getSeparatorType() {
			return array(
				__( 'Normal', 'porto' ) => '',
				__( 'Small', 'porto' )  => 'small',
			);
		}

		public static function getSeparatorStyle() {
			return array(
				__( 'Gradient', 'porto' ) => '',
				__( 'Solid', 'porto' )    => 'solid',
				__( 'Dashed', 'porto' )   => 'dashed',
				__( 'Pattern', 'porto' )  => 'pattern',
			);
		}

		public static function getSeparatorRepeat() {
			return array(
				__( 'Repeat', 'porto' )    => '',
				__( 'No Repeat', 'porto' ) => 'no-repeat',
			);
		}

		public static function getSeparatorPosition() {
			return array(
				__( 'Left Top', 'porto' )      => '',
				__( 'Left Center', 'porto' )   => 'left center',
				__( 'Left Bottom', 'porto' )   => 'left bottom',
				__( 'Center Top', 'porto' )    => 'center top',
				__( 'Center Center', 'porto' ) => 'center center',
				__( 'Center Bottom', 'porto' ) => 'center bottom',
				__( 'Right Top', 'porto' )     => 'right top',
				__( 'Right Center', 'porto' )  => 'right center',
				__( 'Right Bottom', 'porto' )  => 'right bottom',
			);
		}

		public static function getSeparatorIconStyle() {
			return array(
				__( 'Style 1', 'porto' ) => '',
				__( 'Style 2', 'porto' ) => 'style-2',
				__( 'Style 3', 'porto' ) => 'style-3',
				__( 'Style 4', 'porto' ) => 'style-4',
			);
		}

		public static function getSeparatorIconSize() {
			return array(
				__( 'Normal', 'porto' ) => '',
				__( 'Small', 'porto' )  => 'sm',
				__( 'Large', 'porto' )  => 'lg',
			);
		}

		public static function getSeparatorIconPosition() {
			return array(
				__( 'Center', 'porto' ) => '',
				__( 'Left', 'porto' )   => 'left',
				__( 'Right', 'porto' )  => 'right',
			);
		}

		public static function getSeparatorElements() {
			return array(
				__( 'h1', 'porto' )  => 'h1',
				__( 'h2', 'porto' )  => 'h2',
				__( 'h3', 'porto' )  => 'h3',
				__( 'h4', 'porto' )  => 'h4',
				__( 'h5', 'porto' )  => 'h5',
				__( 'h6', 'porto' )  => 'h6',
				__( 'p', 'porto' )   => 'p',
				__( 'div', 'porto' ) => 'div',
			);
		}

		public static function getAccordionType() {
			return array(
				__( 'Default', 'porto' )                   => 'panel-default',
				__( 'Modern', 'porto' )                    => 'panel-modern',
				__( 'Modern Without Background', 'porto' ) => 'panel-modern without-bg',
				__( 'Without Background', 'porto' )        => 'without-bg',
				__( 'Without Borders and Background', 'porto' ) => 'without-bg without-borders',
				__( 'Custom', 'porto' )                    => 'custom',
			);
		}

		public static function getAccordionSize() {
			return array(
				__( 'Default', 'porto' ) => '',
				__( 'Small', 'porto' )   => 'panel-group-sm',
				__( 'Large', 'porto' )   => 'panel-group-lg',
			);
		}

		public static function getColors() {
			return array(
				''                          => 'custom',
				__( 'Primary', 'porto' )    => 'primary',
				__( 'Secondary', 'porto' )  => 'secondary',
				__( 'Tertiary', 'porto' )   => 'tertiary',
				__( 'Quaternary', 'porto' ) => 'quaternary',
				__( 'Dark', 'porto' )       => 'dark',
				__( 'Light', 'porto' )      => 'light',
			);
		}

		public static function getContextual() {
			return array(
				__( 'None', 'porto' )    => '',
				__( 'Success', 'porto' ) => 'success',
				__( 'Info', 'porto' )    => 'info',
				__( 'Warning', 'porto' ) => 'warning',
				__( 'Danger', 'porto' )  => 'danger',
			);
		}

		public static function getProgressBorderRadius() {
			return array(
				__( 'Default', 'porto' )               => '',
				__( 'No Border Radius', 'porto' )      => 'no-border-radius',
				__( 'Rounded Border Radius', 'porto' ) => 'border-radius',
			);
		}

		public static function getProgressSize() {
			return array(
				__( 'Normal', 'porto' ) => '',
				__( 'Small', 'porto' )  => 'sm',
				__( 'Large', 'porto' )  => 'lg',
			);
		}

		public static function getCircularViewType() {
			return array(
				__( 'Show Title and Value', 'porto' ) => '',
				__( 'Show Only Icon', 'porto' )       => 'only-icon',
				__( 'Show Only Title', 'porto' )      => 'single-line',
			);
		}

		public static function getCircularViewSize() {
			return array(
				__( 'Normal', 'porto' ) => '',
				__( 'Small', 'porto' )  => 'sm',
				__( 'Large', 'porto' )  => 'lg',
			);
		}

		public static function getSectionSkin() {
			return array(
				__( 'Default', 'porto' )     => 'default',
				__( 'Transparent', 'porto' ) => 'parallax',
				__( 'Primary', 'porto' )     => 'primary',
				__( 'Secondary', 'porto' )   => 'secondary',
				__( 'Tertiary', 'porto' )    => 'tertiary',
				__( 'Quaternary', 'porto' )  => 'quaternary',
				__( 'Dark', 'porto' )        => 'dark',
				__( 'Light', 'porto' )       => 'light',
			);
		}

		public static function getSectionColorScale() {
			return array(
				__( 'Default', 'porto' ) => '',
				__( 'Scale 1', 'porto' ) => 'scale-1',
				__( 'Scale 2', 'porto' ) => 'scale-2',
				__( 'Scale 3', 'porto' ) => 'scale-3',
				__( 'Scale 4', 'porto' ) => 'scale-4',
				__( 'Scale 5', 'porto' ) => 'scale-5',
				__( 'Scale 6', 'porto' ) => 'scale-6',
				__( 'Scale 7', 'porto' ) => 'scale-7',
				__( 'Scale 8', 'porto' ) => 'scale-8',
				__( 'Scale 9', 'porto' ) => 'scale-9',
			);
		}

		public static function getSectionTextColor() {
			return array(
				__( 'Default', 'porto' ) => '',
				__( 'Dark', 'porto' )    => 'dark',
				__( 'Light', 'porto' )   => 'light',
			);
		}

		public static function getHeadingBorderType() {
			return array(
				__( 'Bottom Border', 'porto' )         => 'bottom-border',
				__( 'Bottom Double Border', 'porto' )  => 'bottom-double-border',
				__( 'Middle Border', 'porto' )         => 'middle-border',
				__( 'Middle Border Reverse', 'porto' ) => 'middle-border-reverse',
				__( 'Middle Border Center', 'porto' )  => 'middle-border-center',
			);
		}

		public static function getHeadingBorderSize() {
			return array(
				__( 'Normal', 'porto' )      => '',
				__( 'Extra Small', 'porto' ) => 'xs',
				__( 'Small', 'porto' )       => 'sm',
				__( 'Large', 'porto' )       => 'lg',
				__( 'Extra Large', 'porto' ) => 'xl',
			);
		}
	}
}

if ( ! function_exists( 'porto_image_resize' ) ) :
	function porto_image_resize( $attach_id, $thumb_size ) {
		if ( ! isset( $attach_id ) || ! $attach_id ) {
			return false;
		}

		if ( is_string( $thumb_size ) ) {
			preg_match_all( '/\d+/', $thumb_size, $thumb_matches );
			if ( isset( $thumb_matches[0] ) ) {
				$thumb_size = array();
				if ( count( $thumb_matches[0] ) > 1 ) {
					$thumb_size[] = $thumb_matches[0][0]; // width
					$thumb_size[] = $thumb_matches[0][1]; // height
				} elseif ( count( $thumb_matches[0] ) > 0 && count( $thumb_matches[0] ) < 2 ) {
					$thumb_size[] = $thumb_matches[0][0]; // width
					$thumb_size[] = $thumb_matches[0][0]; // height
				} else {
					return false;
				}
			}
		}

		$width  = $thumb_size[0];
		$height = $thumb_size[1];
		$crop   = true;

		$image_src = array();

		$image_src        = wp_get_attachment_image_src( $attach_id, 'full' );
		$actual_file_path = get_attached_file( $attach_id );
		// this is not an attachment, let's use the image url

		if ( ! empty( $actual_file_path ) ) {
			$file_info = pathinfo( $actual_file_path );
			$extension = '.' . $file_info['extension'];

			// the image path without the extension
			$no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

			$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

			// checking if the file size is larger than the target size
			// if it is smaller or the same size, stop right here and return
			if ( $image_src[1] > $width || $image_src[2] > $height ) {

				// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
				if ( file_exists( $cropped_img_path ) ) {
					$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
					$vt_image        = array(
						$cropped_img_url,
						$width,
						$height,
					);

					return $vt_image;
				}

				// no cache files - let's finally resize it
				$img_editor = wp_get_image_editor( $actual_file_path );

				if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
					return array(
						'',
						'',
						'',
					);
				}

				$new_img_path = $img_editor->generate_filename();

				if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
					return array(
						'',
						'',
						'',
					);
				}
				if ( ! is_string( $new_img_path ) ) {
					return array(
						'',
						'',
						'',
					);
				}

				$new_img_size = getimagesize( $new_img_path );
				$new_img      = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

				// resized output
				$vt_image = array(
					$new_img,
					$new_img_size[0],
					$new_img_size[1],
				);

				return $vt_image;
			}

			// default output - without resizing
			$vt_image = array(
				$image_src[0],
				$image_src[1],
				$image_src[2],
			);

			return $vt_image;
		}
		return false;
	}
endif;
