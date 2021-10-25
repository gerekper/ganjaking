<?php

namespace MasterAddons\Admin\Dashboard\Extensions;

use MasterAddons\Master_Elementor_Addons;
use MasterAddons\Inc\Helper\Master_Addons_Helper;
use MasterAddons\Admin\Dashboard\Addons\Extensions\JLTMA_Third_Party_Extensions;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/5/19
 */
?>

<h3><?php echo esc_html__('Third Party Plugins', MELA_TD); ?></h3>

<!-- Third Party Plugins -->
<?php foreach (JLTMA_Third_Party_Extensions::$jltma_third_party_plugins['jltma-plugins']['plugin'] as $key => $jltma_plugins) {

	$plugin_file = $jltma_plugins['plugin_file'];
	$plugin_slug = $jltma_plugins['wp_slug'];
?>

	<div class="master-addons-dashboard-checkbox col">
		<div class="master-addons-dashboard-checkbox-content">

			<div class="master-addons-features-ribbon">
				<?php if (isset($jltma_plugins['is_pro']) && $jltma_plugins['is_pro']) {
					echo '<span class="pro-ribbon">Pro</span>';
				} ?>
			</div>

			<div class="master-addons-el-title">
				<div class="master-addons-el-title-content">
					<?php echo $jltma_plugins['title']; ?>
				</div> <!-- master-addons-el-title-content -->
				<div class="ma-el-tooltip">
					<?php
					if ($plugin_slug and $plugin_file) {
						if (Master_Addons_Helper::is_plugin_installed($plugin_slug, $plugin_file)) {
							if (!current_user_can('install_plugins')) {
								return;
							}
							if (!jltma_is_plugin_active($plugin_file)) {
								$activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin_file);
								$html = '<a class="thrd-party-plgin-dnld thrd-party-plgin-dnld-active" href="' . $activation_url . '" ><span class="thrd-party-plgin-dnld thrd-party-plgin-dnld-active pr-1">' . esc_html__('Activate', MELA_TD) . '</span><i class="dashicons dashicons-yes-alt"></i></a>';
							} else {
								$html = '';
							}
						} else {

							$install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_slug), 'install-plugin_' . $plugin_slug);
							$html = '<a class="thrd-party-plgin-dnld" href="' . $install_url . '"><span class="thrd-party-plgin-dnld pr-1">' . esc_html__('Download', MELA_TD) . '</span><i class="dashicons dashicons-download"></i></a>';

							activate_plugin($plugin_file);
						}
						echo $html;
					}
					?>
				</div>
			</div> <!-- .master-addons-el-title -->


			<div class="master_addons_feature_switchbox">
				<label for="<?php echo esc_attr($jltma_plugins['key']); ?>" class="switch switch-text switch-primary switch-pill
				<?php if (isset($jltma_plugins['is_pro']) && $jltma_plugins['is_pro']) {
					echo "ma-el-pro";
				} ?>">
						<input type="checkbox" id="<?php echo esc_attr($jltma_plugins['key']); ?>" class="switch-input" name="<?php echo esc_attr($jltma_plugins['key']); ?>" <?php checked(1, $this->jltma_get_third_party_plugins_settings[$jltma_plugins['key']], true); ?>>

					<span data-on="On" data-off="Off" class="switch-label"></span>
					<span class="switch-handle"></span>

				</label>
			</div>
		</div>
	</div>

<?php } ?>
