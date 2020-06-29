<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
$is_table = 'table' == $wrapper ? true : false;
$main_wrapper_start = $main_wrapper_end = '';

if( $is_table ){
    $main_wrapper_start = "<tr class='form-field'>";
    $main_wrapper_end   = '</tr>';
} else {
    $main_wrapper_start = "<{$wrapper} class='form-field'>";
    $main_wrapper_end   = "</{$wrapper}>";
}
?>

<?php echo $main_wrapper_start ?>
    <?php echo $is_table ? '<th scope="row" valign="top">' : '' ?>
        <label><?php echo $label; ?></label><br/>
    <?php echo $is_table ? '</th>' : '' ?>

    <?php echo $is_table ? '<td>' : '' ?>
        <div id="<?php echo $image_wrapper_id ?>" style="margin-bottom:10px;">
            <img src="<?php echo $placeholder ?>" style="max-height: 250px; width: auto;" />
        </div>

        <div style="line-height:60px;">
            <?php if( 'header_image' == $image_details['type'] && $image_details['width'] != 0 ) : ?>
                <span class="description" style="display: block;">
                    <?php
                    $size = $image_details['width'];

                    if( $image_details['height'] != 0 ){
                        $size .= " x {$image_details['height']}";
                    }

                    printf( '%s: %s px', _x( 'Upload an image with the correct aspect ratio settings. Recommended size (width x height)', '[Admin]: option description', 'yith-woocommerce-product-vendors' ), $size );
                    ?>
                </span>
            <?php endif; ?>
            <input type="hidden" id="<?php echo $hidden_field_id ?>" name="<?php echo $hidden_field_name ?>" value="<?php echo $image_id ?>"/>
            <button type="button" class="<?php echo $upload_image_button ?> button"><?php _e( 'Upload/Add image', 'yith-woocommerce-product-vendors' ); ?></button>
            <button type="button" class="<?php echo $remove_image_button ?> button"><?php _e( 'Remove image', 'yith-woocommerce-product-vendors' ); ?></button>
        </div>
    <?php echo $is_table ? '</td>' : '' ?>
<?php echo $main_wrapper_end ?>