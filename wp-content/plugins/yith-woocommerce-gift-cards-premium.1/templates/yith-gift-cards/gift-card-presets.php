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

//dont show the design presets in the physical gift cards
if ( is_product() && is_object($product) && ! $product->is_virtual() ) {
    return;
}

$allow_customer_images = get_option ( "ywgc_custom_design", 'no');

$selected_categories = get_post_meta( $product->get_id(), 'selected_images_categories', true );

$selected_categories_unserialized = unserialize($selected_categories);

$selected_categories_unserialized = isset($selected_categories_unserialized) && is_array( $selected_categories_unserialized ) ? $selected_categories_unserialized : array();

$default_gift_product = wc_get_product( get_option ( YWGC_PRODUCT_PLACEHOLDER ) );

if ( $product->get_id() == $default_gift_product->get_id() ){
    $selected_categories_unserialized = array('0' => '0');

}

?>
<script type="text/template" id="tmpl-gift-card-presets">
    <div id="ywgc-choose-design" class="ywgc-template-design">
        <div class="ywgc-design-list-menu">
            <?php if ( count( $categories ) > 0 ): ?>
            <h3 class="ywgc-design-categories-title"><?php echo apply_filters( 'yith_ywgc_design_categories_title_text', __( "Categories", 'yith-woocommerce-gift-cards' ) ); ?></h3>
                <ul class="ywgc-template-categories">
                    <li class="ywgc-template-item ywgc-category-all">
                        <a href="#" class="ywgc-show-category ywgc-category-selected" data-category-id="all">
                            <?php echo apply_filters( 'yith_ywgc_show_all_design_text', _x( "All", 'choose image', 'yith-woocommerce-gift-cards' ) ); ?>
                        </a>
                    </li>
                    <?php foreach ( $categories as $item ):

                        $term_slug_array = array();

                        foreach ( $selected_categories_unserialized  as $selected_categories ) {

                            if ( $selected_categories != 0 ){
                                $term_slug_array[] = get_term( $selected_categories )->slug;
                            }

                        }

                        if ( in_array( 'none', $term_slug_array ) )
                            continue;

                        if ( in_array( $item->term_id, $selected_categories_unserialized  ) && $item->term_id != $product->get_id() || in_array( 'all', $term_slug_array ) || count($selected_categories_unserialized) == 1 ): ?>

                        <li class="ywgc-template-item ywgc-category-<?php echo $item->term_id; ?>">
                            <a href="#" class="ywgc-show-category" data-category-id="ywgc-category-<?php echo $item->term_id; ?>"><?php echo $item->name; ?></a>
                        </li>
                        <?php endif; ?>

                    <?php endforeach; ?>

                    <!-- Let the user to upload a file to be used as gift card main image -->
                    <?php if ( $allow_customer_images == 'yes' ) : ?>
                        <li class="ywgc-upload-section-modal">
                            <p class="ywgc-custom-design-menu-title">
                                <a href="#" class="ywgc-custom-design-menu-title-link"><?php echo  __( "Upload your image", 'yith-woocommerce-gift-cards' ); ?></a>
                            </p>
                        </li>
                    <?php endif; ?>

                </ul>
            <?php endif; ?>
        </div>
        <div class="ywgc-design-list-modal">

            <?php foreach ( $item_categories as $item_id => $categories ):

                $category_id = str_replace( "ywgc-category-", "", $categories );

                $term_slug_array = array();

                foreach ( $selected_categories_unserialized  as $selected_categories ) {

                    if ( $selected_categories != 0 ){
                        $term_slug_array[] = get_term( $selected_categories )->slug;
                    }

                }

                if ( in_array( 'none', $term_slug_array ) )
                    continue;

                if ( in_array( $category_id, $selected_categories_unserialized  ) && $item_id != $product->get_id() || in_array( 'all', $term_slug_array ) || count($selected_categories_unserialized) == 1 ): ?>

                <div class="ywgc-design-item <?php echo $categories; ?> template-<?php echo $item_id; ?>">

                    <div class="ywgc-preset-image" data-design-id="<?php echo $item_id; ?>"  data-design-url="<?php echo yith_get_attachment_image_url( intval( $item_id ), 'full' ); ?>" >
                        <?php echo wp_get_attachment_image( intval( $item_id ), apply_filters('yith_ywgc_preset_image_size','shop_catalog' ) ); ?>

                        <?php if ( ( "yes" == get_option ( 'ywgc_show_preset_title', 'no' ) ) ):
                            $post = get_post( $item_id );
                            if ( $post ): ?>
                                <span class="ywgc-preset-title"><?php echo $post->post_title; ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <span class="choose-design"><?php _e('Choose', 'yith-woocommerce-gift-cards')?></span>
                    </div>

                </div>
                <?php endif; ?>

            <?php endforeach; ?>

        </div>

        <?php if ( $allow_customer_images == 'yes' ) : ?>
            <div class="ywgc-custom-upload-container-modal ywgc-hidden">
                <h2><?php echo __( "Upload your image", 'yith-woocommerce-gift-cards' ); ?></h2>
                <p><?php echo __( "Here you can upload a customized image to make your gift card even more special!", 'yith-woocommerce-gift-cards' ); ?></p>
                <div class="ywgc-custom-design-modal-wrapper">
                    <span class="ywgc-custom-design-modal-preview-close ywgc-hidden">×</span>
                    <div class="ywgc-custom-design-modal-preview-container">
                    </div>
                </div>
                <p class="ywgc-custom-design-link">
                    <a href="#" title="<?php echo __( "Suggested size (px): ", 'yith-woocommerce-gift-cards' ) . get_option( 'ywgc_custom_design_suggested_size' , '180x330' ); ?>"
                       class="ywgc-choose-image-modal ywgc-custom-picture-modal">
                        <img src="<?php echo YITH_YWGC_ASSETS_IMAGES_URL . 'addimage.svg'?>" alt="">
                    </a>
                </p>

                <p class="ywgc-custom-design-link"><a href="#" title="<?php echo __( "Suggested size (px): ", 'yith-woocommerce-gift-cards' ) . get_option( 'ywgc_custom_design_suggested_size' , '180x330' ); ?>"  class="ywgc-choose-image-modal ywgc-custom-picture-modal"><?php echo  __( "Upload your image here >", 'yith-woocommerce-gift-cards' ); ?></a></p>
                <input type="file" class="ywgc-hidden" name="ywgc-upload-picture-modal" id="ywgc-upload-picture-modal" accept="image/*" />

                <a class="ywgc-custom-image-modal-submit-link ywgc-hidden" href="#"><?php echo  __( "OK, USE THIS IMAGE", 'yith-woocommerce-gift-cards' ); ?></a>

            </div>
        <?php endif; ?>

    </div>
</script>