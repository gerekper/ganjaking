<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$defaults = array(
	'option_id'             => '',
	'current_row'           => '',
	'watermark_url'         => '',
	'watermark_id'          => '',
	'watermark_type'        => 'type_img',
	'watermark_position'    => 'bottom_right',
	'watermark_margin_x'    => 0,
	'watermark_margin_y'    => 0,
	'watermark_sizes'       => 'shop_single',
	'watermark_category'    => array(),
	'watermark_text'        => '',
	'watermark_font'        => '',
	'watermark_font_color'  => '#000000',
	'watermark_font_size'   => 11,
	'watermark_bg_color'    => '#ffffff',
	'watermark_opacity'     => 75,
	'watermark_padding'     => 0,
	'watermark_box_width'   => 100,
	'watermark_box_height'  => 50,
	'watermark_line_height' => - 1,
	'watermark_coeff_prop'  => '',
	'watermark_angle'       => 0,
	'watermark_repeat'      => 'no'
);

$defaults = wp_parse_args( $params, $defaults );

extract( $defaults );
global $YWC_Watermark_Instance;
$gd_ver    = $YWC_Watermark_Instance->get_gd_version();
$unit_size = $gd_ver >= 2 ? __( '( in pt )', 'yith-woocommerce-watermark' ) : __( '( in px )', 'yith-woocommerce-watermark' );
?>

<div class="ywcwat_product_watermark_row" id="ywcwat_product_watermark_row-<?php echo $current_row; ?>">
    <div class="options_group ywcwat_product_collapse">
        <input type="button" class="button button-secondary ywcwat_remove_product_watermark"
               data-element_id="<?php echo $current_row; ?>"
               value="<?php _e( 'Remove', 'yith-woocommerce-watermark' ); ?>" style="float:left"/>
        <span class="ywcwat-collapse-sign"></span>
        <span class="ywcwat-collapse-collapsed"><?php _e( 'Expand', 'yith-woocommerce-watermark' ); ?></span>
        <span class="ywcwat-collapse-expanded"><?php _e( 'Collapse', 'yith-woocommerce-watermark' ); ?></span>
    </div>
    <div class="ywcwat_product_watermark_container">
        <div class="options_group ywcwat_product_type_watermark">
            <p class="form-field ywcwat_select_type">
                <label for="ywcwat_select_type_wat"><?php _e( 'Create Watermark', 'yith-woocommerce-watermark' ); ?></label>
                <select class="ywcwat_select_type_wat"
                        name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_type]">
                    <option value="type_text" <?php selected( 'type_text', $watermark_type ); ?> ><?php _e( 'Starting from a text', 'yith-woocommerce-watermark' ); ?></option>
                    <option value="type_img" <?php selected( 'type_img', $watermark_type ); ?> ><?php _e( 'Starting from an image', 'yith-woocommerce-watermark' ); ?></option>
                </select>
            </p>
        </div>
        <div class="options_group ywcwat_custom_wat_text_fields">
            <p class="form-field">
                <label for="ywcwat_watermark_text"><?php _e( 'Text', 'yith-woocommerce-watermark' ); ?></label>
                <input type="text" class="product_ywcwat_text"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_text]"
                       value="<?php echo $watermark_text; ?>"
                       placeholder="<?php _e( 'Write a text', 'yith-woocommerce-watermark' ); ?>">
            </p>
            <p class="form-field">
                <label for="ywcwat_watermark_font"><?php _e( 'Font Type', 'yith-woocommerce-watermark' ); ?></label>
				<?php
				$fonts = ywcwat_get_font_name(); ?>

                <select class="product_ywcwat_font"
                        name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_font]"
                        style="text-transform: capitalize;">
					<?php foreach ( $fonts as $font ): ?>
						<?php $key = basename( $font );
						$font_name = str_replace( array( '_', '.ttf' ), ' ', strtolower( $key ) );
						?>
                        <option value="<?php echo $key; ?>" <?php selected( $key, $watermark_font ); ?> ><?php echo $font_name; ?></option>
					<?php endforeach; ?>
                </select>
            </p>
            <p class="form-field">
                <label for="ywcwat_watermark_font_size"><?php _e( 'Font Size', 'yith-woocommerce-watermark' ); ?></label>
                <input type="number" class="product_ywcwat_font_size"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_font_size]"
                       min=0 max=100 value="<?php echo $watermark_font_size; ?>">
                <span class="description"><?php echo $unit_size; ?> </span>
            </p>
            <p class="form-field">
                <label for="ywcwat_watermark_font_color"><?php _e( 'Font Color', 'yith-woocommerce-watermark' ); ?></label>
                <input type="text" class="product_colorpicker product_ywcwat_font_color"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_font_color]"
                       value="<?php echo $watermark_font_color; ?>"
                       data-default-color="#000000">
            </p>
            <p class="form-field">
                <label for="ywcwat_watermark_bg_color"><?php _e( 'Background Color', 'yith-woocommerce-watermark' ); ?></label>
                <input type="text" class="product_colorpicker product_ywcwat_bg_color"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_bg_color]"
                       value="<?php echo $watermark_bg_color; ?>"
                       data-default-color="#ffffff">
            </p>
            <p class="form-field">
                <label for="ywcwat_watermark_opacity"><?php _e( 'Opacity', 'yith-woocommerce-watermark' ); ?></label>
                <input type="number" class="product_ywcwat_opacity"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_opacity]"
                       value="<?php echo $watermark_opacity; ?>" min=0 max=100
                       value="<?php echo $watermark_opacity; ?>">
                <span class="description"><?php _e( 'Set background opacity, set to 0 for complete transparency, or to
  100 for complete opacity', 'yith-woocommerce-watermark' ); ?></span>
            </p>
            <p class="form-field">
                <label for="ywcwat_watermark_box_width"><?php _e( 'Box Width', 'yith-woocommerce-watermark' ); ?></label>
                <input type="number" class="product_ywcwat_box_width" min=0 max="100"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_width]"
                       value="<?php echo $watermark_box_width; ?>">
                <span class="description"><?php _e( 'Set the width of the box by percentage compared to the selected "Shop Size" option', 'yith-woocommerce-watermark' ); ?></span>
            </p>
            <p class="form-field">
                <label for="ywcwat_watermark_box_height"><?php _e( 'Box Height', 'yith-woocommerce-watermark' ); ?></label>
                <input type="number" class="product_ywcwat_box_height" min=0
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_height]"
                       value="<?php echo $watermark_box_height; ?>">
                <span class="description"><?php _e( 'Set the height of the box by percentage compared to the selected "Shop Size" option', 'yith-woocommerce-watermark' ); ?></span>
            </p>
            <p class="form-field">
                <label><?php _e( 'Rotate text to', 'yith-woocommerce-watermark' ); ?></label>
                <input type="number" max="360" min="0" class="ywcwat_text_angle"
                       name="<?php esc_attr_e( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_angle]"
                       value="<?php echo $watermark_angle; ?>">
                <span class="description"><?php _e( 'Specify an angle between 0 and 360° to rotate your text', 'yith-woocommerce-watermark' ); ?></span>
            </p>
            <p class="form-field">
                <label for="ywcwat_watermark_line_height"><?php _e( 'Line Height', 'yith-woocommerce-watermark' ); ?></label>
                <input type="number" class="product_ywcwat_line_height" min=-1
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_line_height]"
                       value="<?php echo $watermark_line_height; ?>">
                <span class="description"><?php _e( 'Set -1 to set it equal to the height of the watermark', 'yith-woocommerce-watermark' ); ?></span>
            </p>
        </div>

        <div class="options_group ywcwat_custom_wat_img_fields">
            <p class="form-field">
                <label for="ywcwat_product_image"><?php _e( 'Select Watermark', 'yith-woocommerce-watermark' ); ?></label>
                <input type="text" class="ywcwat_product_wat_url"
                       id="ywcwat_product_wat_url-<?php echo $current_row; ?>" value="<?php echo $watermark_url; ?>">
                <input type="button" class="button button-secondary ywcwat_product_image"
                       value="<?php _e( 'Select Watermark', 'yith-woocommerce-watermark' ); ?>"
                       data-choose="<?php _e( 'Select Watermark', 'yith-woocommerce-watermark' ); ?>">
                <input type="hidden"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_id]"
                       id="ywcwat_product_image_hidden-<?php echo $current_row; ?>"
                       value="<?php echo $watermark_id; ?>">
            </p>
            <p class="form-field">
                <label><?php _e( 'Repeat image', 'yith-woocommerce-watermark' ); ?></label>
                <input type="checkbox" class="ywcwat_repeat"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_repeat]"
                       value="1" <?php checked( 'yes', $watermark_repeat ); ?>>
                <span class="description"><?php _e( 'If enabled, the watermark is replicated on the whole image', 'yith-woocommerce-watermark' ); ?></span>
            </p>
        </div>
        <div class="options_group ywcwat_margin">
            <p class="form-field">
                <label for="ywcwat_margin_x"><?php _e( 'Margin X', 'yith-woocommerce-watermark' ); ?></label>
                <input type="number" class="ywcwat_prod_margin_x"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_margin_x]"
                       value="<?php echo $watermark_margin_x; ?>">
            </p>
            <p class="form-field">
                <label for="ywcwat_margin_y"><?php _e( 'Margin Y', 'yith-woocommerce-watermark' ); ?></label>
                <input type="number" class="ywcwat_prod_margin_y"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_margin_y]"
                       value="<?php echo $watermark_margin_y; ?>">
            </p>
        </div>
        <div class="options_group ywcwat_product_size_option">
            <p class="form-field">
                <label for="ywcwat_product_size"><?php _e( 'Shop Size', 'yith-woocommerce-watermark' ); ?></label>
                <select name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_sizes]"
                        class="ywcwat_product_size">
				    <?php
				    $wc_sizes = yith_watermark_get_image_size();
				    foreach ( $wc_sizes as $key => $size_name ):
					    ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $watermark_sizes ); ?>><?php echo $size_name; ?></option>
				    <?php endforeach; ?>
                </select>
                <span class="description"><?php _e( 'Select the images you want to apply the watermark on', 'yith-woocommerce-watermark' ); ?></span>
            </p>
        </div>
        <div class="options_group ywcwat_custom_general_fields">
            <div class="form-field position_table">

                <label for="product_ywcwat_container_position"><?php _e( 'Watermark Position', 'yith-woocommerce-watermark' ); ?></label>
                <table class="product_ywcwat_container_position">
                    <tbody>
                    <tr class="top_positions">
                        <td class="ywcwat_top_left <?php echo $watermark_position == 'top_left' ? 'position_select' : '' ?>"></td>
                        <td class="ywcwat_top_center <?php echo $watermark_position == 'top_center' ? 'position_select' : '' ?>"></td>
                        <td class="ywcwat_top_right <?php echo $watermark_position == 'top_right' ? 'position_select' : '' ?>"></td>
                    </tr>
                    <tr class="middle_positions">
                        <td class="ywcwat_middle_left <?php echo $watermark_position == 'middle_left' ? 'position_select' : '' ?>"></td>
                        <td class="ywcwat_middle_center <?php echo $watermark_position == 'middle_center' ? 'position_select' : '' ?>"></td>
                        <td class="ywcwat_middle_right <?php echo $watermark_position == 'middle_right' ? 'position_select' : '' ?>"></td>
                    </tr>
                    <tr class="bottom_positions">
                        <td class="ywcwat_bottom_left <?php echo $watermark_position == 'bottom_left' ? 'position_select' : '' ?>"></td>
                        <td class="ywcwat_bottom_center <?php echo $watermark_position == 'bottom_center' ? 'position_select' : '' ?>"></td>
                        <td class="ywcwat_bottom_right <?php echo $watermark_position == 'bottom_right' ? 'position_select' : '' ?>"></td>
                    </tr>
                    </tbody>
                </table>
                <input type="button" class="ywcwat_preview button button-secondary"
                       data-watermark_id="<?php echo $watermark_id; ?>"
                       value="<?php _e( 'Preview', 'yith-woocommerce-watermark' ); ?>"/>
                <input type="hidden"
                       name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_position]"
                       class="product_ywcwat_pos_value" value="<?php echo $watermark_position; ?>">
            </div>
            <div class="form-field position_text"></div>


        </div>

    </div>
</div>