<div class="wrap">
	<h1><?php esc_html_e( 'Subscriptions', 'yith-woocommerce-subscription' ); ?></h1>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<form method="get">
						<input type="hidden" name="page" value="yith_woocommerce_subscription" />
						<?php $this->cpt_obj_subscriptions->search_box( 'search', 'search_id' ); ?>
					</form>
					<form method="get">
						<input type="hidden" name="page" value="yith_woocommerce_subscription" />
						<input type="hidden" name="tab" value="subscriptions" />
						<?php
						$this->cpt_obj_subscriptions->views();
						$this->cpt_obj_subscriptions->prepare_items();
						$this->cpt_obj_subscriptions->display();
						?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
