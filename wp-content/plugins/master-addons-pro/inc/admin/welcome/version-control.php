<div class="wp-tab-panel" id="version" style="display: none;">
	<div class="master_addons_features">


		<div class="master-addons-el-dashboard-wrapper">



			<div class="response-wrap"></div>

			<div class="mb-4">
				<h2 class="jltma-roll-back" style="text-align: left; margin-bottom: 5px; margin-left: 5px;">
					<?php echo __('Rollback to Previous Version', MELA_TD); ?>
				</h2>
				<p class="jltma-roll-back-span"><?php echo sprintf(__('Experiencing an issue with Master Addons for Elementor version <strong>%s</strong>? Rollback to a previous version before the issue appeared.', MELA_TD), MELA_VERSION); ?></p>
			</div>


			<div class="border border-muted p-3 mt-4 mb-4 align-left">
				<div class="jltma-row">

					<div class="jltma-col-4">
						<h3><?php echo __('Rollback Version', MELA_TD); ?></h3>
					</div>
					<div class="jltma-col-8">
						<div class="pt-4">
							<?php echo  sprintf('<a href="%1$s" class="button jltma-btn jltma-rollback-button elementor-button-spinner">%2$s</a>', wp_nonce_url(admin_url('admin-post.php?action=master_addons_rollback'), 'master_addons_rollback'), __('Rollback to Version ' . JLTMA_STABLE_VERSION, MELA_TD)); ?>
						</div>
						<p class="jltma-roll-desc pt-2 text-danger">
							<?php echo __('Warning: Please backup your database before making the rollback.', MELA_TD); ?>
						</p>
					</div>

				</div>
			</div>

		</div>
	</div>
</div>
