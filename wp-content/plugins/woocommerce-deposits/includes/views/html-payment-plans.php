<div class="wrap woocommerce wc-deposits-admin">
	<h2><?php _e( 'Payment Plans', 'woocommerce-deposits' ); ?> <a class="add-new-h2" href="#"><?php _e( 'Add Plan', 'woocommerce-deposits' ); ?></a></h2>
	<div class="wc-col-container">
		<div class="wc-col-right">
			<div class="wc-col-wrap">
				<h3><?php _e( 'Existing Payment Plans', 'woocommerce-deposits' ); ?></h3>
				<?php $this->output_plans(); ?>
			</div>
		</div>
		<div class="wc-col-left">
			<div class="wc-col-wrap">
				<h3><?php _e( 'Add Payment Plan', 'woocommerce-deposits' ); ?></h3>
				<?php include( 'html-payment-plan-form.php' ); ?>
			</div>
		</div>
	</div>
</div>
