<?php
$coupons    = self::get_coupons();
?>
<div id="coupons_details" class="panel fue_panel">
	<div class="options_group send_coupon_tr">
		<p class="form-field">
			<?php if (! empty($coupons) ): ?>
				<label for="send_coupon" class="long">
					<?php esc_html_e('Generate a Coupon Code', 'follow_up_emails'); ?>
				</label>
				<input type="checkbox" name="send_coupon" id="send_coupon" value="1" <?php if ($email->send_coupon == 1) echo 'checked'; ?> />
			<?php
			else:
				?>
				<label for="send_coupon" class="long"><?php esc_html_e('Generate a Coupon Code', 'follow_up_emails'); ?></label>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=followup-emails-coupons&action=new-coupon' ) );?>" class="button-secondary">
					<?php esc_html_e('No coupons found. Create a Coupon', 'follow_up_emails'); ?>
				</a>
			<?php
			endif;
			?>
		</p>
	</div>

	<div class="options_group class_coupon coupon_tr" style="display: none;">
		<p class="form-field">
			<label for="coupon_id"><?php esc_html_e('Select a Coupon', 'follow_up_emails'); ?></label>

			<select name="coupon_id" id="coupon_id" style="width: 400px;">
				<option value="0" <?php if ($email->coupon_id == 0) echo 'selected'; ?>>Select a coupon&hellip;</option>
				<?php
				foreach ( $coupons as $coupon ):
					?>
					<option value="<?php echo esc_attr($coupon->id); ?>" <?php selected( $email->coupon_id, $coupon->id ); ?>><?php echo esc_attr($coupon->coupon_name); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	</div>
</div>
