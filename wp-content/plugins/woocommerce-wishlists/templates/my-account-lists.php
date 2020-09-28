<?php do_action( 'woocommerce_wishlists_before_wrapper' ); ?>
<div id="wl-wrapper" class="woocommerce">
    <h2><?php echo apply_filters( 'woocommerce_my_account_my_wishlists_title', __( 'Wishlists', 'wc_wishlist' ) ); ?></h2>
    <table class="shop_table cart wl-table wl-manage" cellspacing="0">
        <thead>
        <tr>
            <th class="product-name"><?php _e( 'List Name', 'wc_wishlist' ); ?></th>
            <th class="wl-date-added"><?php _e( 'Date Added', 'wc_wishlist' ); ?></th>
            <th class="wl-privacy-col"><?php _e( 'Privacy Settings', 'wc_wishlist' ); ?></th>
        </tr>
        </thead>
        <tbody>
		<?php $lists = WC_Wishlists_User::get_wishlists(); ?>
		<?php if ( $lists && count( $lists ) ) : ?>
			<?php foreach ( $lists as $list ) : ?>
				<?php $sharing = $list->get_wishlist_sharing(); ?>
                <tr class="cart_table_item">
                    <td class="product-name">
                        <a href="<?php $list->the_url_edit(); ?>"><?php $list->the_title(); ?></a>
                        <div class="row-actions"></div>
						<?php if ( $sharing == 'Public' || $sharing == 'Shared' ) : ?>
							<?php woocommerce_wishlists_get_template( 'wishlist-sharing-menu.php', array( 'id' => $list->id ) ); ?>
						<?php endif; ?>
                    </td>
                    <td class="wl-date-added"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $list->post->post_date ) ); ?></td>
                    <td class="wl-privacy-col">
						<?php echo $list->get_wishlist_sharing( true ); ?>
                    </td>
                </tr>

				<?php
				//Registers the email form modal to be printed in the footer.
				woocommerce_wishlists_get_template( 'wishlist-email-form.php', array( 'wishlist' => $list ) );
				?>
			<?php endforeach; ?>
            <tr>

            </tr>
		<?php endif; ?>
        </tbody>
    </table>
</div><!-- /wishlist-wrapper -->
<?php do_action( 'woocommerce_wishlists_after_wrapper' ); ?>
