<?php
$plans = wc_memberships_get_membership_plans();
$storewide_type = (!empty($email->meta['storewide_type'])) ? $email->meta['storewide_type'] : 'all';
?>
<div class="non-signup reminder hideable <?php do_action('fue_form_product_tr_class', $email); ?> product_tr">
	<p class="form-field hideable membership_plan_product_tr">
		<label for="meta_plan_id"><?php esc_html_e('Enable for', 'follow_up_emails'); ?></label>
		<?php
		$plan_id = (!empty($email->meta['plan_id'])) ? $email->meta['plan_id'] : '';
		?>
		<select id="meta_plan_id" name="meta[plan_id]" class="select2" style="width: 400px;">
			<option value="" <?php selected( true, empty( $plan_id ) ); ?>>
				<?php esc_html_e('All membership plans', 'follow_up_emails'); ?>
			</option>
			<?php
			foreach ( $plans as $plan ) :
			?>
				<option value="<?php echo esc_attr( $plan->id ); ?>" <?php selected( $plan_id, $plan->id ); ?>>
					<?php esc_html_e( $plan->name ); ?>
				</option>
			<?php
			endforeach;
			?>
		</select>
	</p>
</div>