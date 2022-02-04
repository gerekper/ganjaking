<?php
/**
 * MY ACCOUNT TEMPLATE MENU
 *
 * @since 3.0.0
 * @package YITH WooCommerce Customize My Account Page
 * @var WP_User $current_user
 * @var boolean $avatar_upload
 * @var string  $logout_url
 * @var array   $endpoints
 * @var string  $wrap_classes
 * @var string  $wrap_id
 * @var integer $avatar_size
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

global $woocommerce, $wp, $post;

?>
<div id="<?php echo esc_attr( $wrap_id ); ?>" class="yith-wcmap <?php echo esc_attr( $wrap_classes ); ?>">
	<div class="user-profile">

		<div class="user-avatar <?php echo $avatar_upload ? 'avatar-upload' : ''; ?>">
			<?php echo get_avatar( $current_user->ID, $avatar_size ); ?>
		</div>
		<div class="user-info">
			<span class="username"><?php echo esc_html( apply_filters( 'yith_wcmap_user_name_in_menu', $current_user->display_name, $current_user ) ); ?> </span>
			<span class="user-email"><?php echo esc_html( $current_user->user_email ); ?></span>
			<?php if ( isset( $current_user ) && $current_user->ID ) : ?>
				<span class="logout">
					<a href="<?php echo esc_url( $logout_url ); ?>"><?php esc_html_e( 'Logout', 'yith-woocommerce-customize-myaccount-page' ); ?></a>
				</span>
			<?php endif; ?>
		</div>

	</div>

<?php do_action( 'yith_wcmap_before_endpoints_menu' ); ?>

	<ul class="myaccount-menu">

		<?php do_action( 'yith_wcmap_before_endpoints_items' ); ?>

		<?php
		foreach ( $endpoints as $endpoint => $options ) {

			if ( isset( $options['children'] ) ) {
				/**
				 * Print endpoints group
				 */
				do_action( 'yith_wcmap_print_endpoints_group', $endpoint, $options );
			} else {
				/**
				 * Print single endpoint
				 */
				do_action( 'yith_wcmap_print_single_endpoint', $endpoint, $options );
			}
		}
		?>

		<?php do_action( 'yith_wcmap_after_endpoints_items' ); ?>

	</ul>

<?php do_action( 'yith_wcmap_after_endpoints_menu' ); ?>
</div>
