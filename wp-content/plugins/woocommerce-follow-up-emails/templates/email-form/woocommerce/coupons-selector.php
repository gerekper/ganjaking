<div class="options_group storewide non-signup hideable <?php do_action( 'fue_form_coupons_class', $email ); ?> coupons_div">
	<p class="form-field">
		<label for="coupon_id"><?php esc_html_e( 'Coupon', 'follow_up_emails' ); ?></label>
		<select
			id="coupon_id"
			name="meta[coupon]"
			class="wc-coupon-search"
			data-allow_clear="true"
			data-placeholder="<?php esc_attr_e( 'All coupons', 'follow_up_emails' ); ?>"
		>
		<?php
			$coupon_id   = ! empty( $email->meta['coupon'] ) ? $email->meta['coupon'] : '';
			$coupon_name = '';

			if ( ! empty( $coupon_id ) ) {
				$coupon      = get_post( $coupon_id );
				$coupon_name = $coupon ? $coupon->post_title : '';
			?>
			<option value="<?php echo esc_attr( $coupon_id ); ?>" selected><?php echo esc_html( $coupon_name ); ?></option>
		<?php
			}
		?>
		</select>
	</p>
</div>
