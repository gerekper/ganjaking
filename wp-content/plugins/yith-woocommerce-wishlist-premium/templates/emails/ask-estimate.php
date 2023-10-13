<?php
/**
 * Admin ask estimate email
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Emails
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist_data       \YITH_WCWL_Wishlist
 * @var $email_heading       string
 * @var $email               \WC_Email
 * @var $user_formatted_name string
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>
	<?php
	// translators: 1. User name.
	echo esc_html( sprintf( __( 'You have received an estimate request from %s. The request is the following:', 'yith-woocommerce-wishlist' ), $user_formatted_name ) );
	?>
</p>

<?php
/**
 * DO_ACTION: yith_wcwl_email_before_wishlist_table
 *
 * Allows to render some content or fire some action before the wishlist in the 'Ask for an estimate' email.
 *
 * @param YITH_WCWL_Wishlist $wishlist_data Wishlist object
 */
do_action( 'yith_wcwl_email_before_wishlist_table', $wishlist_data );
?>

<?php if ( $wishlist_data->get_token() ) : ?>
	<h2>
		<a href="<?php echo esc_url( $wishlist_data->get_url() ); ?>">
			<?php
			// translators: 1. Wishlist name.
			echo esc_html( sprintf( apply_filters( 'yith_wcwl_ask_estimate_email_wishlist_name', __( 'Wishlist: %s', 'yith-woocommerce-wishlist' ), $wishlist_data ), $wishlist_data->get_token() ) );
			?>
		</a>
	</h2>
<?php else : ?>
	<h2><?php esc_html_e( 'Wishlist:', 'yith-woocommerce-wishlist' ); ?></h2>
<?php endif; ?>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
	<tr>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'yith-woocommerce-wishlist' ); ?></th>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Quantity', 'yith-woocommerce-wishlist' ); ?></th>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Price', 'yith-woocommerce-wishlist' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if ( $wishlist_data->has_items() ) :
		foreach ( $wishlist_data->get_items() as $item ) :
			$product = $item->get_product();
			?>
			<tr>
				<td scope="col" style="text-align:left;">
					<a href="<?php echo esc_url( get_edit_post_link( $product->get_id() ) ); ?>"><?php echo wp_kses_post( $product->get_title() ); ?></a>
					<?php
					if ( $product->is_type( 'variation' ) ) {
						echo wp_kses_post( wc_get_formatted_variation( $product ) );
					}
					?>
				</td>
				<td scope="col" style="text-align:left;">
					<?php echo esc_html( $item->get_quantity() ); ?>
				</td>
				<td scope="col" style="text-align:left;">
					<?php echo wp_kses_post( $item->get_formatted_product_price() ); ?>
				</td>
			</tr>
			<?php
		endforeach;
	endif;
	?>
	</tbody>
</table>

<?php if ( ! empty( $additional_notes ) ) : ?>
	<h2><?php esc_html_e( 'Additional info:', 'yith-woocommerce-wishlist' ); ?></h2>
	<p>
		<?php echo esc_html( $additional_notes ); ?>
	</p>

<?php endif; ?>

<?php if ( ! empty( $additional_data ) ) : ?>
	<h2><?php esc_html_e( 'Additional data:', 'yith-woocommerce-wishlist' ); ?></h2>
	<p>
		<?php foreach ( $additional_data as $key => $value ) : ?>

			<?php
			$key   = wp_strip_all_tags( ucwords( str_replace( array( '_', '-' ), ' ', $key ) ) );
			$value = wp_strip_all_tags( $value );
			?>

			<b><?php echo esc_html( $key ); ?></b>: <?php echo esc_html( $value ); ?><br/>

		<?php endforeach; ?>
	</p>

<?php endif; ?>

<?php
/**
 * DO_ACTION: yith_wcwl_email_after_wishlist_table
 *
 * Allows to render some content or fire some action after the wishlist in the 'Ask for an estimate' email.
 *
 * @param YITH_WCWL_Wishlist $wishlist_data Wishlist object
 */
do_action( 'yith_wcwl_email_after_wishlist_table', $wishlist_data );
?>

<h2><?php esc_html_e( 'Customer details', 'yith-woocommerce-wishlist' ); ?></h2>

<p>
	<b><?php esc_html_e( 'Email:', 'yith-woocommerce-wishlist' ); ?></b>
	<a href="mailto:<?php echo esc_url( $email->reply_email ); ?>">
		<?php echo esc_html( $email->reply_email ); ?></a>
</p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
