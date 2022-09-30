<fieldset class="inline-edit-col-center">
	<div class="inline-edit-col">
		<span class="title inline-edit-plans-label"><?php esc_html_e( 'Set Access', 'yith-woocommerce-membership' ); ?></span>
		<ul class="plans-checklist cat-checklist product_cat-checklist">
			<?php
			$plans = YITH_WCMBS_Manager()->get_plans();
			?>
			<?php if ( ! empty( $plans ) ) : ?>
				<?php foreach ( $plans as $plan ) : ?>
					<li id='plan-<?php echo esc_attr( $plan->get_id() ); ?>'>
						<label class='selectit'>
							<input value='<?php echo esc_attr( $plan->get_id() ); ?>'
									name='_yith_wcmbs_restrict_access_plan[]'
									id='in-plan-<?php echo esc_attr( $plan->get_id() ); ?>'
									type='checkbox'/>
							<?php echo esc_html( $plan->get_name() ); ?>
						</label>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</div>
</fieldset>
