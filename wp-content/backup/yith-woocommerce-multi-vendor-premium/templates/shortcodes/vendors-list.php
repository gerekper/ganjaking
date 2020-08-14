<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

extract( $sc_args );
/** @var $vendor YITH_Vendor */

if ( ! is_bool( $show_description ) ){
	$show_description = $show_description == 'true' ? true : false;
}

?>

<ul class="shortcodes vendors-list">
    <?php foreach( $vendors as $vendor ) :
        $count = absint( $vendor->count );
        if( empty( $count ) && ! empty( $hide_no_products_vendor ) && 'true' == $hide_no_products_vendor ) {
            continue;
        }

        $store_image = '';
        if( 'store' == $vendor_image && ! empty( $vendor->header_image ) ){
            $store_image = wp_get_attachment_image( $vendor->header_image, apply_filters( 'yith_wcmv_avatar_image_size', 'thumbnail' ), false, array( 'class' => 'store-image' ) );
        }

        elseif( 'gravatar' == $vendor_image ) {
            $avatar_image_size = apply_filters( 'yith_wcmv_avatar_image_size', 150 );
            if( ! empty( $vendor->avatar ) ){
	            $store_image = wp_get_attachment_image( $vendor->avatar, array( $avatar_image_size, $avatar_image_size ), false, array( 'class' => 'store-image' ) );
            }
            else {
	            $owner       = get_users( $vendor->get_owner() );
	            $store_image = get_avatar( $vendor->owner, array( $avatar_image_size, $avatar_image_size ), '', $vendor->name, array( 'class' => 'store-image' ) );
            }
        }

        ?>
        <li class="vendor-item <?php echo $vendor->slug; ?>">
            <h3>
                <a href="<?php echo $vendor->get_url() ?>" title="<?php _e( 'Store page', 'yith-woocommerce-product-vendors' ); ?>" class="store-name" >
                    <?php echo $vendor->name; ?>
                </a>
            </h3>
            <div class="vendor-info-wrapper">
                <a href="<?php echo $vendor->get_url() ?>" title="<?php _e( 'Store page', 'yith-woocommerce-product-vendors' ); ?>" class="store-name" >
                    <?php if( ! empty( $store_image ) ) :
                        echo $store_image;
                    else: ?>
                        <img width="150" height="150" src="<?php echo YITH_WPV_ASSETS_URL . 'images/shop-placeholder.jpg'; ?>" class="store-image" alt="store-placeholder">
                    <?php endif; ?>
                </a>
                <ul class="vendor-info<?php echo $show_description ? ' has-description' : '';?>">
                    <?php ! empty( $vendor->location )    && printf( '%s%s%s%s', '<li class="location">', '<i class="fas fa-location-arrow"></i>', $vendor->location, '</li>' ); ?>
                    <?php ! empty( $vendor->store_email ) && printf( '%s%s<a href="mailto:%s">%s</a>%s', '<li class="store-email">', '<i class="fas fa-envelope"></i>', $vendor->store_email, $vendor->store_email, '</li>' ); ?>
                    <?php ! empty( $vendor->telephone )   && printf( '%s%s%s%s', '<li class="telephone">', '<i class="fas fa-phone"></i>', $vendor->telephone, '</li>' ); ?>
                    <?php $vendor_reviews = $vendor->get_reviews_average_and_product(); ?>
                    <?php if( ! empty( $vendor_reviews['reviews_product_count'] ) ) : ?>
                        <li class="store-rating">
                            <i class="<?php echo $icons['rating'] ?>"></i><?php printf(
                                _n( '%s average rating from %d review', '%s average rating from %d reviews', $vendor_reviews['reviews_product_count'],'yith-woocommerce-product-vendors' ),
                                $vendor_reviews['average_rating'], $vendor_reviews['reviews_product_count'] ); ?>
                        </li>
                    <?php endif; ?>
                    <?php if( $show_total_sales ) : ?>
                        <li class="store-sales">
                            <i class="<?php echo $icons['sales'] ?>"></i><?php printf( __( 'Total sales: %d', 'yith-woocommerce-product-vendors' ), count( $vendor->get_orders() ) ); ?>
                        </li>
                    <?php endif; ?>
                    <?php if( ! empty( $vendor->socials ) ) : ?>
                        <li class="store-socials">
                            <span class="socials-container">
                                <?php foreach ( $vendor->socials as $social => $uri ) : ?>
                                    <?php if ( ! empty( $uri ) ) : ?>
		                                <?php $uri = str_replace( 'http://', '', $uri ); ?>
		                                <?php $uri = str_replace( 'https://', '', $uri ); ?>
		                                <?php $uri = ( $uri != '#yith-live-chat' ? '//' . $uri : $uri ); ?>
                                        <a class="vendor-social-uri" href="<?php echo $uri ?>" target="_blank">
                                            <i class="<?php echo $socials_list['social_fields'][ $social ]['icon'] ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </span>
                        </li>
                    <?php endif; ?>
                    <?php if( $show_description && ! empty( $vendor->description ) ) : ?>
                        <li class="store-description">
                            <?php echo wp_trim_words( $vendor->description, $description_lenght, false  ) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
<?php echo paginate_links( $paginate );