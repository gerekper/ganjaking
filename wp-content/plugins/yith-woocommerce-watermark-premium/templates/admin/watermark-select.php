<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$value    = get_option( $option['id'] );
$defaults = array();

if ( isset( $option['default'] ) ) {
	$defaults = $option['default'];
}
$value = wp_parse_args( $value, $defaults );

?>
<h3 class="ywcwat_listsection"><?php echo $option['name']; ?></h3>
<?php
if ( $value ) {
	foreach ( $value as $i => $watermark ) {

		$watermark_position = isset( $value[ $i ]['ywcwat_watermark_position'] ) && ! empty( $value[ $i ]['ywcwat_watermark_position'] ) ? $value[ $i ]['ywcwat_watermark_position'] : 'bottom_right';
		$watermark_margin_x = isset( $value[ $i ]['ywcwat_watermark_margin_x'] ) ? $value[ $i ]['ywcwat_watermark_margin_x'] : 0;
		$watermark_margin_y = isset( $value[ $i ]['ywcwat_watermark_margin_y'] ) ? $value[ $i ]['ywcwat_watermark_margin_y'] : 0;
		$watermark_sizes    = isset( $value[ $i ]['ywcwat_watermark_sizes'] ) ? $value[ $i ]['ywcwat_watermark_sizes'] : 'shop_single';

		if ( isset( $value[ $i ]['ywcwat_watermark_category'] ) && ! is_array( $value[ $i ]['ywcwat_watermark_category'] ) ) {
			$watermark_category = explode( ',', $value[ $i ]['ywcwat_watermark_category'] );
		} elseif ( ! empty( $value[ $i ]['ywcwat_watermark_category'] ) ) {
			$watermark_category = $value[ $i ]['ywcwat_watermark_category'];
		} else {
			$watermark_category = array();
		}

		$unique_id = isset( $value[ $i ]['ywcwat_id'] ) ? $value[ $i ]['ywcwat_id'] : '';

		$watermark_type = isset( $value[ $i ]['ywcwat_watermark_type'] ) ? $value[ $i ]['ywcwat_watermark_type'] : 'type_img';

		/*$json_ids = array();

		if ( ! empty( $watermark_category ) ) {

			foreach ( $watermark_category as $category_id ) {

				$cat_name = get_term_by( 'id', $category_id, 'product_cat' );
				if ( ! empty( $cat_name ) ) {
					$json_ids[ $category_id ] = '#' . $cat_name->term_id . '-' . $cat_name->name;
				}
			}
		}*/

		$global_params = array(
			'option_id'          => $option['id'],
			'current_row'        => $i,
			'unique_id'          => $unique_id,
			'watermark_position' => $watermark_position,
			'watermark_margin_x' => $watermark_margin_x,
			'watermark_margin_y' => $watermark_margin_y,
			'watermark_sizes'    => $watermark_sizes,
			'watermark_category' => $watermark_category,
			'watermark_type'     => $watermark_type
		);
		$type_params   = array();

		if ( $watermark_type == 'type_text' ) {

			$watermark_text            = isset( $value[ $i ]['ywcwat_watermark_text'] ) ? $value[ $i ]['ywcwat_watermark_text'] : '';
			$watermark_font            = isset( $value[ $i ]['ywcwat_watermark_font'] ) ? $value[ $i ]['ywcwat_watermark_font'] : '';
			$watermark_font_size       = isset( $value[ $i ]['ywcwat_watermark_font_size'] ) ? $value[ $i ]['ywcwat_watermark_font_size'] : 11;
			$watermark_width           = isset( $value[ $i ]['ywcwat_watermark_width'] ) ? $value[ $i ]['ywcwat_watermark_width'] : 100;
			$watermark_height          = isset( $value[ $i ]['ywcwat_watermark_height'] ) ? $value[ $i ]['ywcwat_watermark_height'] : 50;
			$watermark_font_color      = isset( $value[ $i ]['ywcwat_watermark_font_color'] ) ? $value[ $i ]['ywcwat_watermark_font_color'] : '#000000';
			$watermark_font_background = isset( $value[ $i ]['ywcwat_watermark_bg_color'] ) ? $value[ $i ]['ywcwat_watermark_bg_color'] : '#ffffff';
			$watermark_opacity         = isset( $value[ $i ]['ywcwat_watermark_opacity'] ) ? $value[ $i ]['ywcwat_watermark_opacity'] : 75;
			$watermark_line_height     = isset( $value[ $i ]['ywcwat_watermark_line_height'] ) ? $value[ $i ]['ywcwat_watermark_line_height'] : - 1;
			$watermark_angle           = isset( $value[ $i ]['ywcwat_watermark_angle'] ) ? $value[ $i ]['ywcwat_watermark_angle'] : 0;
			$type_params               = array(
				'watermark_text'            => $watermark_text,
				'watermark_font'            => $watermark_font,
				'watermark_font_color'      => $watermark_font_color,
				'watermark_font_size'       => $watermark_font_size,
				'watermark_font_background' => $watermark_font_background,
				'watermark_opacity'         => $watermark_opacity,
				'watermark_width'           => $watermark_width,
				'watermark_height'          => $watermark_height,
				'watermark_line_height'     => $watermark_line_height,
				'watermark_angle'           => $watermark_angle

			);

		} else {
			$watermark_url    = '';
			$watermark_id     = isset( $value[ $i ]['ywcwat_watermark_id'] ) ? $value[ $i ]['ywcwat_watermark_id'] : '';
			$watermark_repeat = isset( $value[ $i ]['ywcwat_watermark_repeat'] ) ? 'yes' : 'no';
			if ( ! empty( $watermark_id ) ) {


				$watermark_url = wp_get_attachment_image_src( $watermark_id, 'full' );

				$watermark_url = $watermark_url[0];

				$type_params = array(
					'watermark_url'    => $watermark_url,
					'watermark_id'     => $watermark_id,
					'watermark_repeat' => $watermark_repeat
				);
			}

		}


		$params = array_merge( $global_params, $type_params );

		$params['params'] = $params;

		wc_get_template( 'single-watermark-template.php', $params, YWCWAT_TEMPLATE_PATH, YWCWAT_TEMPLATE_PATH );

	}
}
wc_get_template( 'watermark-preview.php', array(), YWCWAT_TEMPLATE_PATH, YWCWAT_TEMPLATE_PATH );
?>


