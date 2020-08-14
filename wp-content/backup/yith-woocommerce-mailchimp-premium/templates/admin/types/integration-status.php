<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
	</th>
	<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo sanitize_title( $value['type'] ) ?>">
		<div class="account-banner">
			<div class="account-avatar">
				<div class="account-thumb">
					<?php echo get_avatar( $email, 96 );	?>
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
	</td>
</tr>