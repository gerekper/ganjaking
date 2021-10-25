<?php

namespace MasterAddons\Admin\Dashboard\Extensions;

use MasterAddons\Master_Elementor_Addons;
use MasterAddons\Admin\Dashboard\Addons\Extensions\JLTMA_Addon_Extensions;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/5/19
 */
?>

<div class="wp-tab-panel" id="extensions" style="display: none;">

	<div class="master-addons-el-dashboard-header-wrapper">
		<div class="master-addons-el-dashboard-header-right">
			<button type="submit" class="master-addons-el-btn master-addons-el-js-element-save-setting">
				<?php _e('Save Settings', MELA_TD); ?>
			</button>
		</div>
	</div>


	<div class="parent">

		<div class="left_block">

			<form action="" method="POST" id="master-addons-el-extensions-settings" class="master-addons-el-extensions-settings" name="master-addons-el-extensions-settings">

				<?php wp_nonce_field('ma_el_extensions_settings_nonce_action'); ?>


				<div class="master-addons-el-dashboard-tabs-wrapper">

					<div id="master-addons-extensions" class="master-addons-el-dashboard-header-left master-addons-dashboard-tab master_addons_features ma_el_extensions">

						<div class="master_addons_feature">

							<div class="master-addons-dashboard-filter">
								<div class="filter-left">
									<h3><?php echo esc_html__('Master Addons Extensions', MELA_TD); ?></h3>
									<p>
										<?php echo esc_html__('Enable/Disable all Extensions at once. Please make sure to click "Save Changes" button'); ?>
									</p>
								</div>

								<div class="filter-right">
									<button class="addons-enable-all">
										<?php echo esc_html__('Enable All', MELA_TD); ?>
									</button>
									<button class="addons-disable-all">
										<?php echo esc_html__('Disable All', MELA_TD); ?>
									</button>
								</div>
							</div><!-- /.master-addons-dashboard-filter -->

							<!-- Master Addons Extensions -->
							<?php foreach (JLTMA_Addon_Extensions::$jltma_extensions['jltma-extensions']['extension'] as $key => $extension) : ?>

								<div class="master-addons-dashboard-checkbox col">
									<div class="master-addons-dashboard-checkbox-content">

										<div class="master-addons-features-ribbon">
											<?php if (isset($extension['is_pro']) && $extension['is_pro']) {
												echo '<span class="pro-ribbon">Pro</span>';
											} ?>
										</div>

										<div class="master-addons-el-title">
											<div class="master-addons-el-title-content">
												<?php echo $extension['title']; ?>
											</div> <!-- master-addons-el-title-content -->
											<div class="ma-el-tooltip">
												<?php
												Master_Addons_Helper::jltma_admin_tooltip_info('Demo', $extension['demo_url'], 'eicon-device-desktop');
												Master_Addons_Helper::jltma_admin_tooltip_info('Documentation', $extension['docs_url'], 'eicon-info-circle-o');
												Master_Addons_Helper::jltma_admin_tooltip_info('Video Tutorial', $extension['tuts_url'], 'eicon-video-camera');
												?>
											</div>
										</div> <!-- .master-addons-el-title -->


										<div class="master_addons_feature_switchbox">
											<label for="<?php echo esc_attr($extension['key']); ?>" class="switch switch-text switch-primary switch-pill
											<?php
											if (isset($extension['is_pro']) && $extension['is_pro']) {
												echo "ma-el-pro";
											} ?>">
													<input type="checkbox" id="<?php echo esc_attr($extension['key']); ?>" class="switch-input" name="<?php echo esc_attr($extension['key']); ?>" <?php checked(1, $this->jltma_get_extension_settings[$extension['key']], true); ?>>

												<span data-on="On" data-off="Off" class="switch-label"></span>
												<span class="switch-handle"></span>

											</label>
										</div>
									</div>

								</div>

							<?php endforeach; ?>


							<?php include_once MELA_PLUGIN_PATH . '/inc/admin/welcome/third-party-plugins.php'; ?>

						</div> <!--  .master_addons_extensions-->

					</div>
				</div> <!-- .master-addons-el-dashboard-tabs-wrapper-->
			</form>


		</div>

	</div>
</div>
