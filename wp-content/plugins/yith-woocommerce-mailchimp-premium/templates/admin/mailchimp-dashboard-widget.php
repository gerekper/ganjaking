<div class="yith_wcmc_integration_status">
	<div class="account-banner">
		<div class="account-avatar">
			<div class="account-thumb">
				<? echo get_avatar( $email, 96 ); ?>
			</div>
			<div class="account-name tips" data-tip="<?php echo ! empty( $username ) ? __( 'MailChimp user', 'yith-woocommerce-mailchimp' ) : __( 'No user can be found with this API key', 'yith-woocommerce-mailchimp' )?>">
				<?php echo ! empty( $username ) ? $username : __( '&lt; Not Found &gt;' ); ?>
			</div>
		</div>
		<div class="account-details">
			<p class="account-info">
				<span class="label"><b><?php _e( 'Status:', 'yith-woocommerce-mailchimp' )?></b></span>

				<?php if( ! empty( $user_id ) ): ?>
					<mark class="completed tips" data-tip="<?php _e( 'Correctly synchronized', 'yith-woocommerce-mailchimp' )?>"><?php _e( 'OK', 'yith-woocommerce-mailchimp' )?></mark>
				<?php else: ?>
					<mark class="cancelled tips" data-tip="<?php _e( 'Wrong API key', 'yith-woocommerce-mailchimp' )?>"><?php _e( 'KO', 'yith-woocommerce-mailchimp' )?></mark>
				<?php endif; ?>
			</p>

			<p class="account-info">
				<span class="label"><b><?php _e( 'Name:', 'yith-woocommerce-mailchimp' )?></b></span>

				<?php echo ! empty( $name ) ? $name : __( '&lt; Not Found &gt;', 'yith-woocommerce-mailchimp' ) ?>
			</p>

			<p class="account-info">
				<span class="label"><b><?php _e( 'Email:', 'yith-woocommerce-mailchimp' )?></b></span>

				<?php echo ! empty( $email ) ? $email : __( '&lt; Not Found &gt;', 'yith-woocommerce-mailchimp' ) ?>
			</p>
		</div>
	</div>
</div>

<div class="list-stat-container">
	<?php if( ! empty( $lists['lists'] ) ):?>

		<div class="carousel_controls">
			<a class="prev" href="#"><?php _e( 'Prev', 'yith-woocommerce-mailchimp' ) ?></a>
			<a class="next" href="#"><?php _e( 'Next', 'yith-woocommerce-mailchimp' ) ?></a>
		</div>
		<div class="yith_wcmc_list_stats">
			<?php foreach( $lists['lists'] as $list ): ?>
				<div class="list-stat">
					<h3><?php echo esc_html( $list['name'] ) ?></h3>
					<table>
						<tr>
							<th><?php _e( 'Member count' )?></th>
							<td>
								<span class="number"><?php echo esc_attr( $list['stats']['member_count'] )?></span>
								<span class="description"><?php _e( 'total', 'yith-woocommerce-mailchimp' )?></span>
							</td>
						</tr>
						<tr>
							<th><?php _e( 'Avg sub rate' )?></th>
							<td>
								<span class="number"><?php echo esc_attr( number_format( $list['stats']['avg_sub_rate'], 2 ) )?>%</span>
								<span class="description"><?php _e( 'per month', 'yith-woocommerce-mailchimp' )?></span>
							</td>
						</tr>
						<tr>
							<th><?php _e( 'Avg unsub rate' )?></th>
							<td>
								<span class="number"><?php echo esc_attr( number_format( $list['stats']['avg_unsub_rate'], 2 ) )?>%</span>
								<span class="description"><?php _e( 'per month', 'yith-woocommerce-mailchimp' )?></span>
							</td>
						</tr>
						<tr>
							<th><?php _e( 'Open rate' )?></th>
							<td>
								<span class="number"><?php echo esc_attr( number_format( $list['stats']['open_rate'], 2 ) )?>%</span>
								<span class="description"><?php _e( 'per campaign', 'yith-woocommerce-mailchimp' )?></span>
							</td>
						</tr>
						<tr>
							<th><?php _e( 'Click rate' )?></th>
							<td>
								<span class="number"><?php echo esc_attr( number_format( $list['stats']['click_rate'], 2 ) )?>%</span>
								<span class="description"><?php _e( 'per campaign', 'yith-woocommerce-mailchimp' )?></span>
							</td>
						</tr>
					</table>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<a href="<?php echo wp_nonce_url( admin_url( 'index.php' ), 'refresh_lists_action', 'refresh_lists_nonce' ) ?>" class="refresh-list-stats button button-secondary"><?php _e( 'Refresh stats', 'yith-woocommerce-mailchimp' )?></a>

</div>
<script type="text/javascript">
	jQuery( document ).ready( function($){
		var owl = $( '.yith_wcmc_list_stats' );

		owl.owlCarousel({
			items : 1
		});

		$(".carousel_controls .next").click(function(ev){
			ev.preventDefault();
			owl.trigger('owl.next');
		});

		$(".carousel_controls .prev").click(function(ev){
			ev.preventDefault();
			owl.trigger('owl.prev');
		});
	});
</script>