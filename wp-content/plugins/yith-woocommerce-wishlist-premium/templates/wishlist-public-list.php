<?php
/**
 * List of public wishlists
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist
 * @version 2.2.9
 */

/**
 * Template variables:
 *
 * @var $wishlists \YITH_WCWL_Wishlist[] User wishlists
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>
<div class="yith-wcwl-public-lists">
	<?php
	if ( ! empty( $wishlists ) ) :
		foreach ( $wishlists as $wishlist ) :
			?>
			<p>
				<a href="<?php echo esc_url( $wishlist->get_url() ); ?>">
					<?php echo esc_html( $wishlist->get_formatted_name() ); ?>
				</a>
			</p>
			<?php
		endforeach;
	endif;
	?>
</div>
