<?php
/**
 * Admin ask estimate email
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist_data \YITH_WCWL_Wishlist
 * @var $email_heading string
 * @var $email \WC_Email
 * @var $user_formatted_name string
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action('woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( __( 'You have received an estimate request from %s. The request is the following:', 'yith-woocommerce-wishlist' ), $user_formatted_name ); ?></p>

<?php do_action( 'yith_wcwl_email_before_wishlist_table', $wishlist_data ); ?>

<?php if( $wishlist_data->get_token() ) : ?>
	<h2><a href="<?php echo $wishlist_data->get_url() ?>"><?php printf( __( 'Wishlist: %s', 'yith-woocommerce-wishlist'), $wishlist_data->get_token() ); ?></a></h2>
<?php else: ?>
	<h2><?php _e( 'Wishlist:', 'yith-woocommerce-wishlist' ); ?></h2>
<?php endif; ?>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <thead>
    <tr>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'woocommerce' ); ?></th>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
    </tr>
    </thead>
    <tbody>
        <?php
        if( $wishlist_data->has_items() ):
            foreach( $wishlist_data->get_items() as $item ):
                $product = $item->get_product();
        ?>
            <tr>
                <td scope="col" style="text-align:left;"><a href="<?php echo get_edit_post_link( yit_get_product_id( $product ) )?>"><?php echo $product->get_title() ?></a></td>
                <td scope="col" style="text-align:left;"><?php echo $item->get_quantity() ?></td>
            </tr>
        <?php
            endforeach;
        endif;
        ?>
    </tbody>
</table>

<?php if( ! empty( $additional_notes ) ): ?>

	<h2><?php _e( 'Additional info:', 'yith-woocommerce-wishlist' );?></h2>

	<p>
		<?php echo  $additional_notes . "\n"; ?>
	</p>

<?php endif; ?>

<?php if( ! empty( $additional_data ) ): ?>

	<h2><?php _e( 'Additional data:', 'yith-woocommerce-wishlist' ) ?></h2>

	<p>
		<?php foreach( $additional_data as $key => $value ): ?>

			<?php
				$key = strip_tags( ucwords( str_replace( array( '_', '-' ), ' ', $key ) ) );
				$value = strip_tags( $value );
			?>

			<b><?php echo $key ?></b>: <?php echo $value ?><br/>

		<?php endforeach; ?>
	</p>

<?php endif; ?>

<?php do_action( 'yith_wcwl_email_after_wishlist_table', $wishlist_data ); ?>

<h2><?php _e( 'Customer details', 'yith-woocommerce-wishlist' ); ?></h2>

<p><strong><?php _e( 'Email:', 'yith-woocommerce-wishlist' ); ?></strong> <a href="mailto:<?php echo $email->reply_email; ?>"><?php echo $email->reply_email; ?></a></p>

<?php  do_action( 'woocommerce_email_footer', $email ); ?>
