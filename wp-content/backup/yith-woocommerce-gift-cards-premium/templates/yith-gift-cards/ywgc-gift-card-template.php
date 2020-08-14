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

/**
 * Context -> email or pdf
 */

$shop_page_url = apply_filters( 'yith_ywgc_shop_page_url_qr', get_permalink ( wc_get_page_id ( 'shop' ) ) ? get_permalink ( wc_get_page_id ( 'shop' ) ) : site_url () );
$apply_discount_url = $shop_page_url . '?ywcgc-add-discount=' . $gift_card_code . '%26ywcgc-verify-code=' . YITH_YWGC ()->hash_gift_card ( $object );

$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');
$expiration_date = !is_numeric($object->expiration) ? strtotime( $object->expiration ) : $object->expiration ;


?>
<table cellspacing="0" class="ywgc-table-template">

    <?php do_action( 'yith_wcgc_template_before_logo', $object, $context ); ?>

    <?php if ( get_option( 'ywgc_shop_logo_on_gift_card_before', 'no' ) == 'yes' ) : ?>
        <tr>
            <td class="ywgc-logo-shop" colspan="2" align="<?php echo get_option( 'ywgc_shop_logo_before_alignment' ) ?>">
                <?php if( isset( $company_logo_url ) && $company_logo_url  ) {  ?>
                    <img src="<?php echo apply_filters( 'ywgc_custom_company_logo_url', $company_logo_url, $context ); ?>"
                         class="ywgc-logo-shop-image"
                         alt="<?php _e( "The shop logo for the gift card", 'yith-woocommerce-gift-cards' ); ?>"
                         title="<?php _e( "The shop logo for the gift card", 'yith-woocommerce-gift-cards' ); ?>">

                <?php } ?>
            </td>
        </tr>

    <?php endif; ?>

    <?php do_action( 'yith_wcgc_template_before_main_image', $object, $context ); ?>

    <?php if ( $header_image_url = apply_filters( 'ywgc_custom_header_image_url', preg_replace('/^https(?=:\/\/)/i','http',$header_image_url), $context ) ):

        // This add the default gift card image when the image is lost
        if ( substr($header_image_url, -strlen('/'))=== '/' )
            $header_image_url = $default_header_image_url;

        ?>

        <tr>

            <td class="ywgc-main-image-td" colspan="2">
                <?php
                if ( $object->design_type == 'custom-modal' && $context == 'email' ){
                    $header_image_64 = $object->design;
                    ?>
                    <img src="<?php echo $header_image_64; ?>"
                         class="ywgc-main-image"
                         alt="<?php _e( "Gift card image", 'yith-woocommerce-gift-cards' ); ?>"
                         title="<?php _e( "Gift card image", 'yith-woocommerce-gift-cards' ); ?>">
                <?php }
                else { ?>
                    <img src="<?php echo $header_image_url; ?>"
                        class="ywgc-main-image"
                        alt="<?php _e( "Gift card image", 'yith-woocommerce-gift-cards' ); ?>"
                        title="<?php _e( "Gift card image", 'yith-woocommerce-gift-cards' ); ?>">
                <?php } ?>

            </td>

        </tr>
    <?php endif; ?>

    <?php do_action( 'yith_wcgc_template_after_main_image' , $object, $context ); ?>

    <tr>

        <td class="ywgc-logo-shop" colspan="2" align="<?php echo get_option( 'ywgc_shop_logo_after_alignment' ) ?>">
            <?php if( isset( $company_logo_url ) && $company_logo_url &&  get_option( 'ywgc_shop_logo_on_gift_card_after', 'no' ) == 'yes'    ) {  ?>
                <img src="<?php echo apply_filters( 'ywgc_custom_company_logo_url', $company_logo_url, $context ); ?>"
                     class="ywgc-logo-shop-image"
                     alt="<?php _e( "The shop logo for the gift card", 'yith-woocommerce-gift-cards' ); ?>"
                     title="<?php _e( "The shop logo for the gift card", 'yith-woocommerce-gift-cards' ); ?>">

            <?php } ?>
        </td>

        <?php do_action( 'yith_wcgc_template_after_logo', $object, $context ); ?>

    </tr>

    <tr>
        <td class="ywgc-card-product-name" style="float: left">
            <?php

            $product = wc_get_product( $product_id );

            $product_name_text =  is_object( $product ) && $product instanceof WC_Product_Gift_Card && $object->product_as_present != 1 ? $product->get_name() : esc_html__( "Gift card", 'yith-woocommerce-gift-cards' );

            echo apply_filters( 'yith_wcgc_template_product_name_text', $product_name_text . ' ' . esc_html__( "on", 'yith-woocommerce-gift-cards' )  . ' ' . get_bloginfo( 'name' ) , $object, $context, $product_id ); ?>
        </td>

	    <?php if ( apply_filters( 'ywgc_display_price_template', true, $formatted_price, $object, $context ) && 'yes' === get_option( 'ywgc_display_price', 'yes' ) ) : ?>

            <td class="ywgc-card-amount" valign="bottom">

                <?php echo apply_filters( 'yith_wcgc_template_formatted_price', $formatted_price, $object, $context ); ?>

            </td>

        <?php endif; ?>

        <?php do_action( 'yith_wcgc_template_after_price', $object, $context ); ?>

    </tr>

    <?php do_action( 'yith_wcgc_template_after_logo_price', $object, $context ); ?>

    <tr>
        <td colspan="2"> <hr style="color: lightgrey"> </td>
    </tr>


    <?php if ( $context == 'pdf' ): ?>
        <tr>
            <td style="text-align: left;"><b><?php echo esc_html__( "From: ", 'yith-woocommerce-gift-cards' )?></b><?php echo $object->sender_name ?></td>
        </tr>

        <tr>
            <td style="text-align: left;"><b><?php echo esc_html__( "To: ", 'yith-woocommerce-gift-cards' )?></b><?php echo $object->recipient_name ?></td>
        </tr>

        <tr>
            <td></td>
        </tr>

    <?php endif; ?>

    <?php if ( $message ): ?>
        <tr>
            <td class="ywgc-message-text" colspan="2"> <?php echo nl2br(str_replace( '\\','',$message )) ?> </td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
    <?php endif; ?>

    <?php do_action( 'yith_wcgc_template_after_message', $object, $context ); ?>

        <tr>
            <td>
                <span class="ywgc-card-code-title"><?php echo apply_filters('ywgc_preview_code_title', esc_html__( "Gift card code:", 'yith-woocommerce-gift-cards' ) ); ?></span>
                <br>
                <br>
                <span class="ywgc-card-code">  <?php echo $gift_card_code; ?></span>
            </td>

            <?php if ( get_option( 'ywgc_display_qr_code' , 'no' ) == 'yes' ): ?>
                <td class="ywgc-card-qr-code" style="text-align: right;"><img class="ywgc-card-qr-code-image" src="https://chart.googleapis.com/chart?chs=120x120&cht=qr&chl=<?php echo $apply_discount_url ; ?>" /></td>
            <?php endif; ?>
        </tr>


    <?php do_action( 'yith_wcgc_template_after_code', $object, $context ); ?>



    <?php if ( get_option( 'ywgc_display_description_template', 'no' ) == "yes" ) :?>

        <tr>
            <td colspan="2"> <hr style="color: lightgrey"> </td>
        </tr>

        <?php if ( $context == "email" ) :?>
        <tr>
            <td colspan="2" class="ywgc-description-template-email-message" style="text-align: center"><?php echo get_option( 'ywgc_description_template_email_text', esc_html__( "To use this gift card, you can either enter the code in the gift card field on the cart page or click on the following link to automatically get the discount.", 'yith-woocommerce-gift-cards' ) ); ?></td>
        </tr>
        <?php endif; ?>

        <?php if ( $context == "pdf" ) :?>
            <tr>
                <td colspan="2" class="ywgc-description-template-pdf-message" style="text-align: center"><?php echo get_option( 'ywgc_description_template_text_pdf', esc_html__( "You can automatically apply the gift card in our shop by reading the QR code with your phone.", 'yith-woocommerce-gift-cards' ) ); ?></td>
            </tr>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ( get_option( 'ywgc_display_expiration_date', 'no' ) == "yes" && $object->expiration ) :
        $expiration_message = apply_filters ( 'yith_ywgc_gift_card_email_expiration_message',
            sprintf ( _x ( 'This gift card code will be valid until %s (%s)', 'gift card expiration date', 'yith-woocommerce-gift-cards' ),date_i18n ( $date_format, $expiration_date ) , get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' )), $object, $context);
        ?>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;" class="ywgc-expiration-message"><?php echo $expiration_message; ?></td>
        </tr>
    <?php endif; ?>

    <?php do_action( 'yith_wcgc_template_after_expiration_date', $object, $context ); ?>

</table>
