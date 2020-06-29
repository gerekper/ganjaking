<div class="wrap">
	<h1><?php esc_html_e( 'Activities', 'yith-woocommerce-subscription' ); ?></h1>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<form method="post">
						<input type="hidden" name="page" value="yith_woocommerce_subscription" />
						<?php $this->cpt_obj_activities->search_box( 'search', 'search_id' ); ?>
					</form>
					<input type="hidden" name="page" value="yith_woocommerce_subscription" />
					<input type="hidden" name="tab" value="activities" />

					<form method="post">
						<?php
						$this->cpt_obj_activities->prepare_items();
						$this->cpt_obj_activities->display();
						?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
