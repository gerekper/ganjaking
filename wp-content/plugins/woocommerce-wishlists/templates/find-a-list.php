<?php global $wp_query; ?>

<?php do_action( 'woocommerce_wishlists_before_wrapper' ); ?>
    <div id="wl-wrapper" class="woocommerce">

		<?php if ( function_exists( 'wc_print_messages' ) ) : ?>
			<?php wc_print_messages(); ?>
		<?php else : ?>
			<?php WC_Wishlist_Compatibility::wc_print_notices(); ?>
		<?php endif; ?>

        <form class="wl-search-form" method="get">
            <label for="f-list"><?php _e( "Find Someone's List", 'wc_wishlist' ); ?></label>
            <input type="text" name="f-list" id="f-list" class="find-input" value="<?php echo( isset( $_GET['f-list'] ) ? esc_attr( $_GET['f-list'] ) : '' ); ?>" placeholder="<?php _e( 'Enter name or email', 'wc_wishlist' ); ?>"/>
            <input type="submit" class="button" value="<?php _e( 'Search', 'wc_wishlist' ); ?>"/>
        </form>
        <hr/>

		<?php if ( have_posts() ) : ?>
			<?php if ( isset( $_GET['f-list'] ) && $_GET['f-list'] ) : ?>
                <p class="wl-results-msg"><?php printf( __( "We've found %s lists matching <strong>%s</strong>" ), $wp_query->found_posts, esc_html( $_GET['f-list'] ) ); ?></p> <?php printf( __( '<a class="wl-clear-results" href="%s">Clear Results</a>' ), WC_Wishlists_Pages::get_url_for( 'find-a-list' ) ); ?>
			<?php endif; ?>
            <table class="shop_table cart wl-table wl-manage wl-find-table" cellspacing="0">
                <thead>
                <tr>
                    <th class="product-name"><?php _e( 'List Name', 'wc_wishlist' ); ?></th>
                    <th class="wl-pers-name"><?php _e( 'Name', 'wc_wishlist' ); ?></th>
                    <th class="wl-date-added"><?php _e( 'Date Added', 'wc_wishlist' ); ?></th>
                </tr>
                </thead>

				<?php
				while ( have_posts() ) : the_post();
					$list = new WC_Wishlists_Wishlist( get_the_ID() );
					?>
                    <tr>
                        <td><a href="<?php $list->the_url_view(); ?>"><?php $list->the_title(); ?></a></td>
                        <td><?php echo esc_attr( get_post_meta( $list->id, '_wishlist_first_name', true ) ) . ' ' . esc_attr( get_post_meta( $list->id, '_wishlist_last_name', true ) ); ?></td>
                        <td class="wl-date-added"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $list->post->post_date ) ); ?></td>
                    </tr>
				<?php endwhile; ?>
            </table>

			<?php woocommerce_wishlists_nav( 'nav-below' ); ?>

		<?php elseif ( isset( $_GET['f-list'] ) ): ?>
            <!-- results go down here -->
            <p><?php _e( "We're sorry, we couldn't find a list for that name. Please double check your entry and try again.", 'wc_wishlist' ); ?></p>
            <h2 class="wl-search-result"><?php _e( 'We found 0 matching lists', 'wc_wishlist' ); ?></h2>
		<?php endif; ?>
    </div>
<?php do_action( 'woocommerce_wishlists_after_wrapper' ); ?>