<div class="yith_wcac_integration_status yith_wcac_integration_status_widget">
    <div class="account-banner">
        <div class="account-avatar">
            <div class="account-thumb">
				<?php if ( ! empty( $avatar ) ): ?>
                    <img src="<?php echo $avatar ?>" alt="<?php echo $username; ?>" width="66" heigth="66" />
					<?php
				else:
					echo get_avatar( 0, 96 );
				endif;
				?>
            </div>
            <div class="account-name tips" data-tip="<?php echo ! empty( $username ) ? __( 'Active Campaign user', 'yith-woocommerce-active-campaign' ) : __( 'No user can be found with this API key', 'yith-woocommerce-active-campaign' ) ?>">
				<?php echo ! empty( $username ) ? $username : __( '&lt; Not Found &gt;' ); ?>
            </div>
        </div>
        <div class="account-details">
            <p class="account-info">
                <span class="label"><b><?php _e( 'Status:', 'yith-woocommerce-active-campaign' ) ?></b></span>

				<?php if ( ! empty( $username ) ): ?>
                    <mark class="completed tips" data-tip="<?php _e( 'Correctly synchronized', 'yith-woocommerce-active-campaign' ) ?>"><?php _e( 'OK', 'yith-woocommerce-active-campaign' ) ?></mark>
				<?php else: ?>
                    <mark class="cancelled tips" data-tip="<?php _e( 'Wrong API key', 'yith-woocommerce-active-campaign' ) ?>"><?php _e( 'KO', 'yith-woocommerce-active-campaign' ) ?></mark>
				<?php endif; ?>
            </p>

            <p class="account-info">
                <span class="label"><b><?php _e( 'Name:', 'yith-woocommerce-active-campaign' ) ?></b></span>

				<?php echo ! empty( $name ) ? $name : __( '&lt; Not Found &gt;', 'yith-woocommerce-active-campaign' ) ?>
            </p>

            <p class="account-info">
                <span class="label"><b><?php _e( 'Email:', 'yith-woocommerce-active-campaign' ) ?></b></span>

				<?php echo ! empty( $email ) ? $email : __( '&lt; Not Found &gt;', 'yith-woocommerce-active-campaign' ) ?>
            </p>
        </div>
    </div>
</div>

<div class="list-stat-container">
	<?php if ( ! empty( $lists ) ): ?>

        <div class="carousel_controls">
            <a class="prev" href="#"><?php _e( 'Prev', 'yith-woocommerce-active-campaign' ) ?></a>
            <a class="next" href="#"><?php _e( 'Next', 'yith-woocommerce-active-campaign' ) ?></a>
        </div>
        <div class="yith_wcac_list_stats">
			<?php foreach ( $lists as $list ): ?>
                <div class="list-stat">
                    <h3><?php echo esc_html( $list['name'] ) ?></h3>
                    <table>
                        <tr>
                            <th><?php _e( 'Member count' ) ?></th>
                            <td>
                                <span class="number"><?php echo esc_attr( $list['subscriber_count'] ) ?></span>
                                <span class="description"><?php _e( 'total', 'yith-woocommerce-active-campaign' ) ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
			<?php endforeach; ?>
        </div>
	<?php endif; ?>

    <a href="<?php echo wp_nonce_url( admin_url( 'index.php' ), 'refresh_lists_action', 'refresh_lists_nonce' ) ?>" class="refresh-list-stats button button-secondary"><?php _e( 'Refresh stats', 'yith-woocommerce-active-campaign' ) ?></a>

</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var owl = $('.yith_wcac_list_stats');

        owl.owlCarousel({
            items: 1
        });

        $(".carousel_controls .next").click(function (ev) {
            ev.preventDefault();
            owl.trigger('owl.next');
        });

        $(".carousel_controls .prev").click(function (ev) {
            ev.preventDefault();
            owl.trigger('owl.prev');
        });
    });
</script>