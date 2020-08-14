<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
$button_class = apply_filters( 'yith_wpv_quick_info_button_class', 'submit' );
$submit_label = ! empty( $instance['submit_label'] ) ? $instance['submit_label'] : __( 'Submit', 'yith-woocommerce-product-vendors' );
$subject = '';
extract( $instance );

if( $is_singular ){
    $subject = sprintf( '%s: %s', _x( 'Request about', 'part of: Request about: Apple iPhone 6', 'yith-woocommerce-product-vendors' ), $product->get_title() );
}
?>

<div class="clearfix widget yith-wpv-quick-info">
    <h3 class="widget-title"><?php echo $title ?></h3>
    <div class="yith-wpv-quick-info-wrapper">
        <?php
        if( ! empty( $_GET['message'] ) ) {
            $message = sanitize_text_field( $_GET['message'] );
            echo "<div class='woocommerce-{$widget->response[ $message ]['class']}'>" . $widget->response[ $message ]['message'] . "</div>";
        }

        else {
            echo '<p>' . $description . '</p>';
        }?>

        <form action="" method="post" id="respond">
            <input type="text" class="input-text " name="quick_info[name]" value="<?php echo $current_user->display_name ?>" placeholder="<?php _e( 'Name', 'yith-woocommerce-product-vendors' ) ?>" required/>
            <input type="text" class="input-text " name="quick_info[subject]" value="<?php echo $subject ?>" placeholder="<?php _e( 'Subject', 'yith-woocommerce-product-vendors' ) ?>" required/>
            <input type="email" class="input-text " name="quick_info[email]" value="<?php echo $current_user->user_email  ?>" placeholder="<?php _e( 'Email', 'yith-woocommerce-product-vendors' ) ?>" required/>
            <textarea name="quick_info[message]" rows="5" placeholder="<?php _e( 'Message', 'yith-woocommerce-product-vendors' ) ?>" required></textarea>
            <input type="submit" class="<?php echo $button_class ?>" id="submit" name="quick_info[submit]" value="<?php echo $submit_label ?>" />
            <input type="hidden" name="quick_info[spam]" value="" />
            <input type="hidden" name="quick_info[vendor_id]" value="<?php echo $vendor->id ?>" />
            <?php if( is_singular( 'product' ) ) : ?>
                <input type="hidden" name="quick_info[product_id]" value="<?php echo yit_get_base_product_id( $object ) ?>" />
            <?php endif; ?>
            <?php wp_nonce_field( 'yith_vendor_quick_info_submitted', 'yith_vendor_quick_info_submitted' ); ?>
        </form>
    </div>
</div>