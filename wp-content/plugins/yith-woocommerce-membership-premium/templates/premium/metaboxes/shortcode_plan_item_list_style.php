<?php
/*
 * Template for Metabox Plan Item Order
 */
/*
$default_plan_list_styles = array(
                'list_style'           => 'none',
                'title_color'          => '#333333',
                'title_background'     => 'transparent',
                'title_font_size'      => '15',
                'title_margin_top'     => '0',
                'title_margin_right'   => '0',
                'title_margin_bottom'  => '0',
                'title_margin_left'    => '0',
                'title_padding_top'    => '0',
                'title_padding_right'  => '0',
                'title_padding_bottom' => '0',
                'title_padding_left'   => '0',
                'item_background'      => 'transparent',
                'item_color'           => '#333333',
                'item_font_size'       => '15',
                'item_margin_top'      => '0',
                'item_margin_right'    => '0',
                'item_margin_bottom'   => '0',
                'item_margin_left'     => '20',
                'item_padding_top'     => '0',
                'item_padding_right'   => '0',
                'item_padding_bottom'  => '0',
                'item_padding_left'    => '0',
                'show_icons'           => 'yes'
            );*/

extract( $plan_list_styles );

?>

    <div id="yith-wcmbs-custom-list-style-settings">
        <h3><?php _e( 'Custom List Style', 'yith-woocommerce-membership' ) ?></h3>

        <table class="yith-wcmbs-full-width">
            <tr>
                <td>
                    <label><?php _e( 'List Style', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <select name="_yith_wcmbs_plan_list_styles[list_style]" class="yith-wcmbs-full-width">
                        <option value="none" <?php selected( $list_style, 'none' ) ?> ><?php _e( 'None', 'yith-woocommerce-membership' ) ?></option>
                        <option value="disc" <?php selected( $list_style, 'disc' ) ?> ><?php _e( 'Dotted', 'yith-woocommerce-membership' ) ?></option>
                        <option value="decimal" <?php selected( $list_style, 'decimal' ) ?> ><?php _e( 'Numbers', 'yith-woocommerce-membership' ) ?></option>
                    </select>
                </td>
            </tr>
        </table>

        <h3><?php _e( 'Title', 'yith-woocommerce-membership' ) ?></h3>

        <table class="yith-wcmbs-full-width">
            <tr>
                <td>
                    <label><?php _e( 'Color', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="text" class="yith-wcmbs-color-picker" name="_yith_wcmbs_plan_list_styles[title_color]" value="<?php echo $title_color ?>"
                           data-default-color="<?php echo $default_plan_list_styles[ 'title_color' ]; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Background', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="text" class="yith-wcmbs-color-picker" name="_yith_wcmbs_plan_list_styles[title_background]" value="<?php echo $title_background ?>"
                           data-default-color="<?php echo $default_plan_list_styles[ 'title_background' ]; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Font Size', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[title_font_size]" value="<?php echo $title_font_size ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Margin Top', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[title_margin_top]" value="<?php echo $title_margin_top ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Margin Right', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[title_margin_right]" value="<?php echo $title_margin_right ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Margin Bottom', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[title_margin_bottom]" value="<?php echo $title_margin_bottom ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Margin Left', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[title_margin_left]" value="<?php echo $title_margin_left ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Padding Top', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[title_padding_top]" value="<?php echo $title_padding_top ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Padding Right', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[title_padding_right]" value="<?php echo $title_padding_right ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Padding Bottom', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[title_padding_bottom]" value="<?php echo $title_padding_bottom ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Padding Left', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[title_padding_left]" value="<?php echo $title_padding_left ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
        </table>

        <h3><?php _e( 'Items', 'yith-woocommerce-membership' ) ?></h3>

        <table class="yith-wcmbs-full-width">
            <tr>
                <td>
                    <label><?php _e( 'Color', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="text" class="yith-wcmbs-color-picker" name="_yith_wcmbs_plan_list_styles[item_color]" value="<?php echo $item_color ?>"
                           data-default-color="<?php echo $default_plan_list_styles[ 'item_color' ]; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Background', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="text" class="yith-wcmbs-color-picker" name="_yith_wcmbs_plan_list_styles[item_background]" value="<?php echo $item_background ?>"
                           data-default-color="<?php echo $default_plan_list_styles[ 'item_background' ]; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Font Size', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[item_font_size]" value="<?php echo $item_font_size ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="yith-wcmbs-show-icons"><?php _e( 'Show Icons', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input id="yith-wcmbs-show-icons" type="checkbox" name="_yith_wcmbs_plan_list_styles[show_icons]" value="yes" <?php checked(true, $show_icons == 'yes') ?> >
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Margin Top', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[item_margin_top]" value="<?php echo $item_margin_top ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Margin Right', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[item_margin_right]" value="<?php echo $item_margin_right ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Margin Bottom', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[item_margin_bottom]" value="<?php echo $item_margin_bottom ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Margin Left', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[item_margin_left]" value="<?php echo $item_margin_left ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Padding Top', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[item_padding_top]" value="<?php echo $item_padding_top ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Padding Right', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[item_padding_right]" value="<?php echo $item_padding_right ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Padding Bottom', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[item_padding_bottom]" value="<?php echo $item_padding_bottom ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
            <tr>
                <td>
                    <label><?php _e( 'Padding Left', 'yith-woocommerce-membership' ) ?></label>
                </td>
                <td>
                    <input type="number" min="0" name="_yith_wcmbs_plan_list_styles[item_padding_left]" value="<?php echo $item_padding_left ?>" class="yith-wcmbs-full-width">
                </td>
            </tr>
        </table>
    </div>
