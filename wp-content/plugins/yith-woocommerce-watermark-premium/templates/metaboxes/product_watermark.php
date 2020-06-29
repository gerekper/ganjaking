<?php
if( !defined('ABSPATH'))
    exit;

global $post;

$product_watermark = get_post_meta( $post->ID, '_ywcwat_product_watermark', true );
$is_enabled = get_post_meta( $post->ID, 'ywcwat_product_enabled_watermark', true );

?>
<div id="ywcwat_watermark_data" class="panel woocommerce_options_panel" xmlns="http://www.w3.org/1999/html">
        <div class="options_group ywcwat_enabled_custom_watermark">
            <p class="form-field">
                <label for="ywcwat_custom_watermark"><?php _e('Add Watermark','yith-woocommerce-watermark');?></label>
               <button type="button" class="button add_product_watermark"><?php _e('Add','yith-woocommerce-watermark');?></button>
            </p>
        </div>
        <div id="ywcwat_product_watermark_list">
            <?php
                if( !empty( $product_watermark )){

                    foreach( $product_watermark as $i=>$single_watermark ){

                        $watermark_type = isset( $single_watermark['ywcwat_watermark_type'] ) ? $single_watermark['ywcwat_watermark_type'] : 'type_img';
                        $watermark_position = isset( $single_watermark['ywcwat_watermark_position'] ) ? $single_watermark['ywcwat_watermark_position'] : 'bottom_right';
                        $watermark_margin_x = isset( $single_watermark['ywcwat_watermark_margin_x'] ) ? $single_watermark['ywcwat_watermark_margin_x'] : 0;
                        $watermark_margin_y = isset( $single_watermark['ywcwat_watermark_margin_y'] ) ? $single_watermark['ywcwat_watermark_margin_y'] : 0;
                        $watermark_sizes = isset( $single_watermark['ywcwat_watermark_sizes'] ) ? $single_watermark['ywcwat_watermark_sizes'] : 'shop_single';
	                    $unique_id = isset( $single_watermark['ywcwat_id'] ) ? $single_watermark['ywcwat_id'] : '';

                        $global_params = array(
                            'option_id'    => 'ywcwat_custom_watermark',
                            'current_row' => $i,
                            'watermark_position' => $watermark_position,
                            'watermark_margin_x'    => $watermark_margin_x,
                            'watermark_margin_y'    => $watermark_margin_y,
                            'watermark_sizes'   => $watermark_sizes,
                            'watermark_type' => $watermark_type,
                            'unique_id' => $unique_id
                        );

                        if( $watermark_type == 'type_text' ){

                            $watermark_text =  isset( $single_watermark['ywcwat_watermark_text'] ) ? $single_watermark['ywcwat_watermark_text'] : '';
                            $watermark_font =  isset( $single_watermark['ywcwat_watermark_font'] ) ? $single_watermark['ywcwat_watermark_font'] : '';
                            $watermark_font_size = isset( $single_watermark['ywcwat_watermark_font_size'] ) ? $single_watermark['ywcwat_watermark_font_size'] : 11;
                            $watermark_font_color = isset( $single_watermark['ywcwat_watermark_font_color'] ) ? $single_watermark['ywcwat_watermark_font_color'] : '#000000';
                            $watermark_box_width  = isset( $single_watermark['ywcwat_watermark_width'] ) ? $single_watermark['ywcwat_watermark_width'] : 100;
                            $watermark_box_height  = isset( $single_watermark['ywcwat_watermark_height'] ) ? $single_watermark['ywcwat_watermark_height'] : 50;
                            $watermark_bg_color = isset( $single_watermark['ywcwat_watermark_bg_color'] ) ? $single_watermark['ywcwat_watermark_bg_color'] : '#ffffff';
                            $watermark_opacity = isset( $single_watermark['ywcwat_watermark_opacity'] ) ? $single_watermark['ywcwat_watermark_opacity'] : 75;
                            $watermark_line_height = isset( $single_watermark['ywcwat_watermark_line_height'] ) ? $single_watermark['ywcwat_watermark_line_height'] : -1;
                            $watermark_angle    = isset( $single_watermark['ywcwat_watermark_angle'] ) ? $single_watermark['ywcwat_watermark_angle'] : 0;
                            $type_params = array(
                                'watermark_text'    => $watermark_text,
                                'watermark_font'    =>$watermark_font,
                                'watermark_font_color'  => $watermark_font_color,
                                'watermark_font_size'   => $watermark_font_size,
                                'watermark_bg_color' =>$watermark_bg_color,
                                'watermark_opacity' => $watermark_opacity,
                                'watermark_box_width'   => $watermark_box_width,
                                'watermark_box_height'  => $watermark_box_height,
                                'watermark_line_height' => $watermark_line_height,
                                'watermark_angle'   => $watermark_angle

                            );

                        }else{

                            $watermark_id =  isset( $single_watermark['ywcwat_watermark_id'] ) ? $single_watermark['ywcwat_watermark_id'] : '';
                            $watermark_repeat = isset($single_watermark['ywcwat_watermark_repeat'])? 'yes' :'no';
                            $watermark_url = '';

                            if (!empty($watermark_id)) {
                                $watermark_url = wp_get_attachment_image_src($watermark_id, 'full');
                                $watermark_url = $watermark_url[0];
                            }
                            $type_params = array(
                                'watermark_url' => $watermark_url,
                                'watermark_id'  => $watermark_id,
                                'watermark_repeat' =>$watermark_repeat
                            );

                        }

                        $params = array_merge( $global_params, $type_params );

                        $params['params'] = $params;

                        wc_get_template( 'metaboxes/single-product-watermark-template.php', $params, YWCWAT_TEMPLATE_PATH, YWCWAT_TEMPLATE_PATH );


                    }

                }
            wc_get_template( 'watermark-preview.php', array(), YWCWAT_TEMPLATE_PATH,YWCWAT_TEMPLATE_PATH );
            ?>
        </div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){

    $('.add_product_watermark').on('click', function(){

        var product_wat_list_size = $('.ywcwat_product_watermark_row').size(),
            container_list = $('#ywcwat_product_watermark_list');

        var data = {
            ywcwat_product_addnewwat: product_wat_list_size,
            ywcwat_product_option_id : 'ywcwat_custom_watermark',
            action: 'add_new_product_watermark_admin'
        };

        $.ajax({

            type: 'POST',
            url: '<?php echo admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' );?>',
            data: data,
            dataType: 'json',
            success: function (response) {

                container_list.append( response.result );
               $('body').trigger('ywcwat-product-init-fields');
            }

        });

    });
});
</script>