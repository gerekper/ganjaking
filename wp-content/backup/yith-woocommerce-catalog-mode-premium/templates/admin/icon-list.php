<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Implements an icon list in YITH plugin admin tab
 *
 * @class   YITH_Icon_List
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */
class YITH_Icon_List {

    /**
     * Outputs an icon list template in plugin options panel
     *
     * @since   1.0.0
     *
     * @param   $option array the current option
     *
     * @return  void
     * @author  Alberto Ruggiero
     */
    public static function output( $option ) {

        $current_options = get_option( $option['id'] );
        $current_icon    = YITH_Icon()->get_icon_data( $current_options['icon'] );

        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo $option['id']; ?>"><?php echo esc_html( $option['title'] ); ?></label>
            </th>
            <td class="forminp forminp-<?php echo sanitize_title( $option['type'] ) ?>">
                <select class="icon_list_type" name="<?php echo $option['id']; ?>[select]" id="<?php echo $option['id']; ?>">
                    <?php foreach ( $option['options']['select'] as $val => $opt ) : ?>
                        <option value="<?php echo $val ?>"<?php selected( $current_options['select'], $val ); ?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="description"><?php echo $option['desc']; ?></span>

                <div class="ywctm-icon-option ywctm-icon-list">
                    <div class="ywctm-icon-manager-text">
                        <div class="ywctm-icon-preview" <?php echo $current_icon; ?>></div>
                        <input type="text" id="<?php echo $option['id']; ?>[icon]" class="ywctm-icon-text" name="<?php echo $option['id']; ?>[icon]" value="<?php echo $current_options['icon']; ?>" />
                    </div>
                    <div class="ywctm-icon-manager">
                        <ul class="ywctm-icon-list-wrapper">
                            <?php foreach ( $option['options']['icon'] as $font => $icons ):
                                foreach ( $icons as $key => $icon ): ?>
                                    <li data-font="<?php echo $font ?>" data-icon="<?php echo ( strpos( $key, '\\' ) === 0 ) ? '&#x' . substr( $key, 1 ) : $key ?>" data-key="<?php echo $key ?>" data-name="<?php echo $icon ?>"></li>
                                <?php
                                endforeach;
                            endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="ywctm-icon-option custom-icon">
                    <input type="text" name="<?php echo $option['id']; ?>[custom]" id="<?php echo $option['id'] . '-custom' ?>" value="<?php echo $current_options['custom']; ?>" class="upload_img_url upload_custom_icon" />
                    <input type="button" value="<?php _e( 'Upload', 'yith-woocommerce-catalog-mode' ) ?>" id="<?php echo $option['id']; ?>-custom-button" class="upload_button button" />

                    <div class="upload_img_preview">
                        <?php
                        $file = $current_options['custom'];
                        if ( !preg_match( '/(jpg|jpeg|png|gif|ico)$/', $file ) ) {
                            $file = YWCTM_ASSETS_URL . 'images/sleep.png';
                        }
                        ?>
                        <?php _e( 'Image preview', 'yith-woocommerce-catalog-mode' ) ?> :
                        <img src="<?php echo $file; ?>" />
                    </div>
                </div>

            </td>
        </tr>
    <?php
    }

}


