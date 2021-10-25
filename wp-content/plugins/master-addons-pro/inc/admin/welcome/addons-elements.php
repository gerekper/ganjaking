<?php

namespace MasterAddons\Admin\Dashboard\Addons;

use MasterAddons\Master_Elementor_Addons;
use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Elements;
use MasterAddons\Inc\Helper\Master_Addons_Helper;
?>


<div class="master_addons_feature">

	<div class="master-addons-dashboard-filter">
		<div class="filter-left">
			<h3><?php echo esc_html__('Master Addons Elements', MELA_TD); ?></h3>
			<p>
				<?php echo esc_html__('Enable/Disable all Elements once. Please make sure to click "Save Changes" button'); ?>
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

	<h3><?php echo esc_html__('Content Elements', MELA_TD); ?></h3>

	<?php foreach (JLTMA_Addon_Elements::$jltma_elements['jltma-addons']['elements'] as $key => $widget) : ?>

		<div class="master-addons-dashboard-checkbox col">
			<div class="master-addons-dashboard-checkbox-content">

				<div class="master-addons-features-ribbon">
						echo '<span class="pro-ribbon">Pro</span>';
				</div>

				<div class="master-addons-el-title">
					<div class="master-addons-el-title-content">
						<?php echo $widget['title']; ?>
					</div> <!-- master-addons-el-title-content -->

					<div class="ma-el-tooltip">
						<?php
						Master_Addons_Helper::jltma_admin_tooltip_info('Demo', $widget['demo_url'], 'eicon-device-desktop');
						Master_Addons_Helper::jltma_admin_tooltip_info('Documentation', $widget['docs_url'], 'eicon-info-circle-o');
						Master_Addons_Helper::jltma_admin_tooltip_info('Video Tutorial', $widget['tuts_url'], 'eicon-video-camera');
						?>
					</div>
				</div> <!-- .master-addons-el-title -->

				<div class="master_addons_feature_switchbox">
					<label for="<?php echo esc_attr($widget['key']); ?>" class="switch switch-text switch-primary switch-pill">
							<input type="checkbox" id="<?php echo esc_attr($widget['key']); ?>" class="switch-input" name="<?php echo esc_attr($widget['key']); ?>" <?php checked(1, $this->jltma_get_element_settings[$widget['key']], true); ?>>
						<span data-on="On" data-off="Off" class="switch-label"></span>
						<span class="switch-handle"></span>
					</label>
				</div>
			</div>
		</div>
	<?php endforeach; ?>

</div> <!--  .master_addons_feature-->
