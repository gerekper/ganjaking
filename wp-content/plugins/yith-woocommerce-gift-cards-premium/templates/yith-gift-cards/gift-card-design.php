<?php
/**
 * Gift Card product add to cart
 *
 * @author  Yithemes
 * @package YITH WooCommerce Gift Cards
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $post;

//dont show the design gallery in the physical gift cards
if ( is_product() && is_object($product) && ! $product->is_virtual() ) {
    return;
}

$desings_to_show = get_option('ywgc_template_design_number_to_show', '3' );

$categories_number = count( $item_categories );

if ( $desings_to_show > $categories_number )
    $desings_to_show = $categories_number;


$allow_templates = get_option ( "ywgc_template_design", 'yes');
$allow_customer_images = get_option ( "ywgc_custom_design", 'no');

if ( $allow_templates == 'yes' ){
    $display = "";
}
else{
    $display = "display: none";
}

$selected_categories = get_post_meta( $post->ID, 'selected_images_categories', true );

$selected_categories_unserialized = unserialize($selected_categories);

$selected_categories_unserialized = isset($selected_categories_unserialized) && is_array( $selected_categories_unserialized ) ? $selected_categories_unserialized : array();

$default_gift_product = wc_get_product( get_option ( YWGC_PRODUCT_PLACEHOLDER ) );

if ( is_object($product) && is_object($default_gift_product) && $product->get_id() == $default_gift_product->get_id() ){
    $selected_categories_unserialized = array('0' => '0');

}


if ( $allow_templates == 'yes' || $allow_customer_images == 'yes' ) : ?>

    <h3 class="ywgc_choose_design_title"><?php echo get_option( 'ywgc_choose_design_title' , esc_html__( "Choose your image", 'yith-woocommerce-gift-cards') ); ?></h3>

    <?php do_action('yith_ywgc_before_choose_design_section'); ?>

    <div class="gift-card-content-editor step-appearance">

        <div id="ywgc-choose-design-preview" class="ywgc-choose-design-preview" style="<?php echo $display ?>" >
            <div class="ywgc-design-list">

                <?php $cnt = 0;

                ?><ul>

                    <!--        Default product image                -->
                    <?php if ( $product instanceof WC_Product_Gift_Card  ) :

                        $default_image_url = YITH_WooCommerce_Gift_Cards_Premium::get_instance()->get_default_header_image();
                        $default_image_id = ywgc_get_attachment_id_from_url($default_image_url);

                        $post_thumbnail_id = ! empty( get_post_thumbnail_id( $post->ID ) ) ? get_post_thumbnail_id( $post->ID ) : $default_image_id;
                        $post_thumbnail_url = ! empty( yith_get_attachment_image_url( intval( get_post_thumbnail_id( $post->ID ) ) ) ) ? yith_get_attachment_image_url( intval( get_post_thumbnail_id( $post->ID ) ), 'full' ) : $default_image_url;

                        ?>
                        <li>
                            <div class="ywgc-preset-image ywgc-default-product-image selected_image_parent" data-design-id="<?php echo $post_thumbnail_id; ?>" data-design-url="<?php echo $post_thumbnail_url; ?>" >
                                <?php echo wp_get_attachment_image( intval($post_thumbnail_id ), apply_filters('yith_ywgc_preset_image_size','thumbnail' ) ); ?>
                            </div>
                        </li>
                    <?php endif; ?>

                    <?php
                    foreach ( $item_categories as $item_id => $categories ):

                        $category_id = str_replace( "ywgc-category-", "", $categories );

                        $term_slug_array = array();

                        foreach ( $selected_categories_unserialized  as $selected_categories ) {

                            if ( $selected_categories != 0 ){
                                $term_slug_array[] = get_term( $selected_categories )->slug;
                            }

                        }

                        if ( in_array( 'none', $term_slug_array ) )
                            continue;

                        if ( in_array( $category_id, $selected_categories_unserialized  ) && $item_id != $post->ID || in_array( 'all', $term_slug_array ) || count($selected_categories_unserialized) == 1 ): ?>

                            <li><?php

                            if ($cnt <= ($desings_to_show - 2 ) ){ ?>
                                <div class="ywgc-preset-image" data-design-id="<?php echo $item_id; ?>"  data-design-url="<?php echo yith_get_attachment_image_url( intval( $item_id ), 'full' ); ?>" >

                                    <?php echo wp_get_attachment_image( intval( $item_id ), apply_filters('yith_ywgc_preset_image_size','thumbnail' ) ); ?>
                                </div>

                            <?php }
                            else { ?>
                                <div class="ywgc-preset-image-view-all">
                                    <div class="ywgc-preset-image" data-design-id="<?php echo $item_id; ?>"  data-design-url="<?php echo yith_get_attachment_image_url( intval( $item_id ), 'full' ); ?>" >

                                        <?php echo wp_get_attachment_image( intval( $item_id ), apply_filters('yith_ywgc_preset_image_size','thumbnail' ) ); ?>
                                        <input type="button"
                                               class="ywgc-choose-image ywgc-choose-template"
                                               href="#ywgc-choose-design"
                                               value="<?php echo get_option( 'ywgc_template_design_view_all_button' , esc_html__( "VIEW ALL", 'yith-woocommerce-gift-cards') ); ?>" />
                                    </div>
                                </div>

                            <?php } ?>

                            <?php
                            $cnt++;

                            if ($cnt == $desings_to_show ) break;

                            ?></li><?php

                        endif;
                    endforeach; ?>
                </ul>
            </div>
        </div>

        <?php do_action('yith_ywgc_after_choose_design_section'); ?>


        <!-- Let the user to upload a file to be used as gift card main image -->
        <?php if ( $allow_customer_images == 'yes' ) : ?>

            <p class="ywgc-custom-design-link"> <?php echo  esc_html__( "Or ", 'yith-woocommerce-gift-cards' ); ?> <a href="#" title="<?php echo esc_html__( "Suggested size (px): ", 'yith-woocommerce-gift-cards' ) . get_option( 'ywgc_custom_design_suggested_size' , '180x330' ); ?>"  class="ywgc-choose-image ywgc-custom-picture"><?php echo  esc_html__( "Upload your image >", 'yith-woocommerce-gift-cards' ); ?></a></p>

	        <?php wc_get_template ( 'support-attachments.php', array(), '', YITH_YWGC_TEMPLATES_DIR ); ?>



            <input type="file" class="ywgc-hidden" name="ywgc-upload-picture" id="ywgc-upload-picture" accept="image/*" />
        <?php endif; ?>
    </div>
<?php endif;

