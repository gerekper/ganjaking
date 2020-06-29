<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$defaults = array(
	'option_id'                 => '',
	'current_row'               => '',
	'watermark_url'             => '',
	'watermark_id'              => '',
	'watermark_type'            => 'type_img',
	'unique_id'                 => '',
	'watermark_position'        => 'bottom_right',
	'watermark_margin_x'        => 0,
	'watermark_margin_y'        => 0,
	'watermark_sizes'           => 'shop_single',
	'watermark_category'        => array(),
	'watermark_text'            => '',
	'watermark_font'            => '',
	'watermark_font_color'      => '#000000',
	'watermark_font_size'       => 11,
	'watermark_font_background' => '#ffffff',
	'watermark_opacity'         => 75,
	'watermark_padding'         => 0,
	'watermark_width'           => 100,
	'watermark_height'          => 50,
	'watermark_line_height'     => - 1,
	'watermark_coeff_prop'      => '',
	'watermark_angle'           => 0,
	'watermark_repeat'          => 'no'
);


$defaults = wp_parse_args( $params, $defaults );

extract( $defaults );

global $YWC_Watermark_Instance;

$gd_ver    = $YWC_Watermark_Instance->get_gd_version();
$unit_size = $gd_ver >= 2 ? __( '( in pt )', 'yith-woocommerce-watermark' ) : __( '( in px )', 'yith-woocommerce-watermark' );

?>
<table class="form-table ywcwat_row" id="ywcwat_row-<?php echo $current_row; ?>">
    <tbody>
    <tr valign="top" class="ywcwat-collapse">
        <td colspan="2">
            <input type="button" class="button button-secondary ywcwat_remove_watermark"
                   data-element_id="<?php echo $current_row; ?>"
                   value="<?php _e( 'Remove', 'yith-woocommerce-watermark' ); ?>" style="float:left"/>
            <span class="ywcwat-collapse-sign"></span>
            <span class="ywcwat-collapse-collapsed"><?php _e( 'Expand', 'yith-woocommerce-watermark' ); ?></span>
            <span class="ywcwat-collapse-expanded"><?php _e( 'Collapse', 'yith-woocommerce-watermark' ); ?></span>
        </td>
    </tr>
    <tr valign="top">
        <td colspan="2">
            <table class="form-table ywcwat_field" id="ywcwat_field-<?php echo $current_row; ?>">
                <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label><?php _e( 'Create Watermark from', 'yith-woocommerce-watermark' ); ?></label></th>
                    <td class="forminp forminp-select">
                        <select name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_type]"
                                class="ywcwat_select_type_wat">
                            <option value="no" <?php selected( $watermark_type, '' ); ?>><?php _e( 'Select an option', 'yith-woocommerce-watermark' ); ?></option>
                            <option value="type_text" <?php selected( $watermark_type, 'type_text' ); ?>><?php _e( 'From Text', 'yith-woocommerce-watermark' ); ?></option>
                            <option value="type_img" <?php selected( $watermark_type, 'type_img' ); ?>><?php _e( 'From Image', 'yith-woocommerce-watermark' ); ?></option>
                        </select>
                        <input type="hidden"
                               name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_id]"
                               id="<?php echo esc_attr( 'ywcwat_id' ) . '-' . $current_row; ?>"
                               value="<?php echo $unique_id; ?>">
                    </td>
                </tr>
                <tr valign="top" class="ywcwat_text_field_container">
                    <td colspan="2" style="padding: 0;">
                        <table class="form-table ywcwat_text_option">
                            <tbody>
                            <tr valign="top">
                                <th scope="row"><?php _e( 'Watermark Text', 'yith-woocommerce-watermark' ); ?></th>
                                <td colspan="2">
                                    <input type="text" class="ywcwat_text_wat"
                                           name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_text]"
                                           value="<?php echo $watermark_text; ?>">
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label><?php _e( 'Watermark Font', 'yith-woocommerce-watermark' ); ?></label></th>
                                <td colspan="2">
									<?php $fonts = ywcwat_get_font_name(); ?>

                                    <select class="ywcwat_watermark_font"
                                            name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_font]"
                                            style="text-transform: capitalize;">
										<?php foreach ( $fonts as $font ): ?>
											<?php $key = basename( $font );
											$font_name = str_replace( array( '_', '.ttf' ), ' ', strtolower( $key ) );
											?>
                                            <option value="<?php echo $key; ?>" <?php selected( $key, $watermark_font ); ?> ><?php echo $font_name; ?></option>
										<?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e( 'Watermark Font Color', 'yith-woocommerce-watermark' ); ?></th>
                                <td colspan="2">
                                    <input type="text" class="ywcwat_color_picker ywcwat_font_color"
                                           name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_font_color]"
                                           value="<?php echo $watermark_font_color; ?>"
                                           data-default-color="#000000">
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label><?php _e( 'Watermark Font Size ', 'yith-woocommerce-watermark' ); ?></label>
                                </th>
                                <td colspan="2">
                                    <input type="number" min="0" class="ywcwat_font_size"
                                           name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_font_size]"
                                           value="<?php echo $watermark_font_size; ?>">
                                    <span class="description"><?php echo $unit_size; ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label><?php _e( 'Box Width ( % ) ', 'yith-woocommerce-watermark' ); ?></label></th>
                                <td colspan="2">
                                    <input type="number" min="0" max="100" class="ywcwat_box_width"
                                           name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_width]"
                                           value="<?php echo $watermark_width; ?>">
                                    <span class="description"><?php _e( 'Set the width of the box in percent compared to the shop size', 'yith-woocommerce-watermark' ); ?></span>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label><?php _e( 'Box Height ( % ) ', 'yith-woocommerce-watermark' ); ?></label>
                                </th>
                                <td colspan="2">
                                    <input type="number" min="0" max="100" class="ywcwat_box_height"
                                           name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_height]"
                                           value="<?php echo $watermark_height; ?>">
                                    <span class="description"><?php _e( 'Set the height of the box in percent compared to the shop size', 'yith-woocommerce-watermark' ); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label><?php _e( 'Rotate text to', 'yith-woocommerce-watermark' ); ?></label>
                                <td colspan="2">
                                    <input type="number" max="360" min="0" class="ywcwat_text_angle"
                                           name="<?php esc_attr_e( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_angle]"
                                           value="<?php echo $watermark_angle; ?>">
                                    <span class="description"><?php _e( 'Specify an angle between 0 and 360° to rotate your text', 'yith-woocommerce-watermark' ); ?></span>
                                </td>
                                </th>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label><?php _e( 'Line Height', 'yith-woocommerce-watermark' ); ?></label></th>
                                <td colspan="2">
                                    <input type="number" min="-1" class="ywcwat_line_height"
                                           name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_line_height]"
                                           value="<?php echo $watermark_line_height; ?>">
                                    <span class="description"><?php _e( 'Set -1 to set it equal to the height of the watermark', 'yith-woocommerce-watermark' ); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e( 'Watermark Background Color', 'yith-woocommerce-watermark' ); ?></th>
                                <td colspan="2">
                                    <input type="text" class="ywcwat_color_picker ywcwat_bg_color"
                                           name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_bg_color]"
                                           value="<?php echo $watermark_font_background; ?>"
                                           data-default-color="#ffffff">
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e( 'Watermark Opacity', 'yith-woocommerce-watermark' ); ?></th>
                                <td colspan="2">
                                    <input type="number" min=0 max=100 class="ywcwat_opacity"
                                           name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_opacity]"
                                           value="<?php echo $watermark_opacity; ?>">
                                    <span class="description"><?php _e( 'Set background opacity, set to 0 for complete transparency, or set to 100 for complete opacity', 'yith-woocommerce-watermark' ); ?></span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr valign="top" class="ywcwat_img_field_container">
                    <th scope="row" class="titledesc">
                        <label><?php _e( 'Watermark Image', 'yith-woocommerce-watermark' ); ?></label></th>
                    <td class="forminp forminp-button">
                        <input type="text" class="ywcwat_url" id="ywcwat_url-<?php echo $current_row; ?>"
                               value="<?php echo $watermark_url; ?>">
                        <input type="button" class="button button-secondary ywcwat_load_image_watermark"
                               id="ywcwat_add_watermark-<?php echo $current_row; ?>"
                               name="ywcwat_add_watermark_<?php echo $current_row; ?>"
                               value="<?php _e( 'Select Watermark', 'yith-woocommerce-watermark' ); ?>"
                               data-choose="<?php _e( 'Select Watermark', 'yith-woocommerce-watermark' ); ?>">
                        <input type="hidden"
                               name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_id]"
                               id="<?php echo esc_attr( 'ywcwat_watermark_id' ) . '-' . $current_row; ?>"
                               value="<?php echo $watermark_id; ?>">

                    </td>
                </tr>
                <tr valign="top" class="ywcwat_img_field_container">
                    <th scope="row">
                        <label><?php _e( 'Repeat Image', 'yith-woocommerce-watermark' ); ?></label>
                    </th>
                    <td class="forminp" colspan="2">
                        <input type="checkbox" class="ywcwat_repeat"
                               name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_repeat]"
                               value="1" <?php checked( 'yes', $watermark_repeat ); ?>>
                        <span class="description"><?php _e( 'If enabled, the watermark is replicated on the whole image', 'yith-woocommerce-watermark' ); ?></span>
                    </td>
                </tr>
                <tr valign="top" class="ywcwat_general_field_container">
                    <th scope="row" class="titledesc">
                        <label><?php _e( 'Watermark Margin', 'yith-woocommerce-watermark' ); ?></label></th>
                    <td class="forminp" colspan="2">
                        <label class="titledesc"><?php _e( 'Margin X', 'yith-woocommerce-watermark' ); ?></label>
                        <input type="number" class="ywcwat_margin_x"
                               id="ywcwat_watermark_margin_x-<?php echo $current_row; ?>"
                               name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_margin_x]"
                               value="<?php echo esc_attr( $watermark_margin_x ); ?>">
                        <label class="titledesc"><?php _e( 'Margin Y', 'yith-woocommerce-watermark' ); ?></label>
                        <input type="number" class="ywcwat_margin_y"
                               id="ywcwat_watermark_margin_y-<?php echo $current_row; ?>"
                               name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_margin_y]"
                               value="<?php echo esc_attr( $watermark_margin_y ); ?>">
                        <input type="hidden" class="ywcwat_coeff_prop"
                               id="ywcwat_coeff_prop-<?php echo $current_row; ?>"
                               name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_coeff_prop]"
                               value="<?php echo $watermark_coeff_prop; ?>">
                    </td>
                </tr>
                <tr valign="top" class="ywcwat_general_field_container">
                    <th scope="row" class="titledesc">
                        <label><?php _e( 'Shop Size', 'yith-woocommerce-watermark' ); ?></label></th>
                    <td class="forminp forminp-multiselect">
                        <select name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_sizes]"
                                class="ywcwat_sizeselect">
							<?php
							$wc_sizes = yith_watermark_get_image_size();
							foreach ( $wc_sizes as $key => $size_name ):
								?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $watermark_sizes ); ?>><?php echo $size_name; ?></option>
							<?php endforeach; ?>
                        </select>
                        <span class="description"><?php _e( 'Select the images to which you want to apply the watermark', 'yith-woocommerce-watermark' ); ?></span>
                    </td>
                </tr>

                <tr valign="top" class="ywcwat_general_field_container">
                    <th scope="row" class="titledesc">
                        <label><?php _e( 'Product Categories', 'yith-woocommerce-watermark' ); ?></label></th>
                    <td class="forminp forminp-enhanceselect">
						<?php


						$args_select2 = array(
							'id'       => 'ywcwat_watermark_category-' . $current_row,
							'name'     => $option_id . '[' . $current_row . '][ywcwat_watermark_category][]',
							'type'     => 'ajax-terms',
							'data' => array(
                                'taxonomy' => 'product_cat',
                            ),
							'multiple' => true,
                            'placeholder' => __('Search for product categories', 'yith-woocommerce-watermark' ),
                            'value' => $watermark_category

						);
						echo yith_plugin_fw_get_field( $args_select2, true );
						?>
                        <span class="description"><?php _e( 'Select the categories to which you want to apply the watermark, leave empty to select all', 'yith-woocommerce-watermark' ); ?></span>
                    </td>
                </tr>
                <tr valign="top" class="ywcwat_general_field_container">
                    <th scope="row" class="titledesc">
                        <label><?php _e( 'Watermark Position', 'yith-woocommerce-watermark' ); ?></label></th>
                    <td colspan="2" class="ywcwat_column_position">
                        <table class="ywcwat_container_position">
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
                        <div class="ywcwat_text_position"></div>
                        <input type="hidden"
                               name="<?php echo esc_attr( $option_id ); ?>[<?php echo $current_row; ?>][ywcwat_watermark_position]"
                               class="ywcwat_watermark_position">
                        <input type="button" class="ywcwat_preview button button-secondary"
                               data-watermark_id="<?php echo $unique_id; ?>"
                               value="<?php _e( 'Preview', 'yith-woocommerce-watermark' ); ?>"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>

    </tbody>
</table>
