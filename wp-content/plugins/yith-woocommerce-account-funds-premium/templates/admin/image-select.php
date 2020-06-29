<?php
if( !defined('ABSPATH')){
    exit;
}


 $default = isset( $option['default'] ) ? $option['default'] : '';
 $id = $option['id'];
 $name = $option['name'];
 $desc = isset( $option['desc'] ) ? $option['desc'] : '';
 $value = get_option( $option['id'], $default );
 $image_url = wp_get_attachment_image_src( $value );
$image_url = $image_url ? $image_url[0] : '';
$preview_display = $image_url == '' ? 'display:none' : 'display:block';
?>
<tr valign="top">
    <th scope="row" class="titledesc">
        <?php echo $name;?>
    </th>
    <td class="forminp">
        <div class="ywf_fund_image_select">
            <input type="text" value="<?php echo esc_attr($image_url); ?>" class="upload_img_url"/>
            <input type="hidden" name="<?php echo $id ?>" value="<?php echo $value; ?>" class="ywf_att_id"/>
            <input type="button" value="<?php _e('Upload', 'yith-woocommerce-account-funds') ?>" class="upload_button button" data-choose="<?php _e('Select an image','yith-woocommerce-account-funds');?>"/>
        </div>
        <div class="clear"></div>
        <span class="description"><?php echo $desc ?></span>

        <div class="upload_img_preview" style="margin-top:10px;max-width: 200px;<?php echo $preview_display;?>;">
            <img src="<?php esc_attr_e( $image_url );?>" style="width: 100%;"/>
        </div>
        </div>
    </td>
</tr>