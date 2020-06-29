<div class="options_group storewide non-signup hideable <?php do_action('fue_form_coupons_class', $email); ?> coupons_div">

	<p class="form-field">
		<label for="coupon_id"><?php esc_html_e('Coupon', 'follow_up_emails'); ?></label>

		<?php
		$coupon_id = !empty( $email->meta['coupon'] ) ? $email->meta['coupon'] : '';
		$coupon_name   = '';

		if ( !empty( $coupon_id ) ) {
			$coupon = get_post( $coupon_id );
			$coupon_name = $coupon->post_title;
		}
		?>
		<input
			type="hidden"
			id="coupon_id"
			name="meta[coupon]"
			class="wc-coupon-search"
			data-multiple="false"
			data-placeholder="<?php esc_attr_e('Search for a coupon&hellip;', 'follow_up_emails'); ?>"
			value="<?php echo esc_attr( $coupon_id ); ?>"
			data-selected="<?php echo esc_attr( $coupon_name ); ?>"
			>
		<!--<select id="coupon_id" name="meta[coupon]" class="wc-coupon-search" data-placeholder="<?php esc_attr_e('All coupons', 'follow_up_emails'); ?>" data-multiple="false" style="width:400px;">
			<option value=""></option>
			<?php
			/*$coupons = get_posts(array(
				'post_type'     => 'shop_coupon',
				'nopaging'      => true
			));

			if ( !is_wp_error( $coupons ) ):
				foreach ( $coupons as $coupon ):
			?>
					<option value="<?php echo $coupon->ID; ?>" <?php selected( $selected, $coupon->ID ); ?>><?php echo esc_attr( $coupon->post_title ); ?></option>
			<?php
				endforeach;
			endif;*/
			?>
		</select>-->
	</p>

</div>