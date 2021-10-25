<?php
/*
* Master Addons : Welcome Screen by Jewel Theme
*/

$jltma_white_label_setting 	= jltma_get_options('jltma_white_label_settings');
$jltma_hide_welcome 		= jltma_check_options($jltma_white_label_setting['jltma_wl_plugin_tab_welcome']);
$jltma_hide_addons 			= jltma_check_options($jltma_white_label_setting['jltma_wl_plugin_tab_addons']);
$jltma_hide_extensions 		= jltma_check_options($jltma_white_label_setting['jltma_wl_plugin_tab_extensions']);
$jltma_hide_api 			= jltma_check_options($jltma_white_label_setting['jltma_wl_plugin_tab_api']);
$jltma_hide_white_label 	= jltma_check_options($jltma_white_label_setting['jltma_wl_plugin_tab_white_label']);
$jltma_hide_version 		= jltma_check_options($jltma_white_label_setting['jltma_wl_plugin_tab_version']);
$jltma_hide_changelogs 		= jltma_check_options($jltma_white_label_setting['jltma_wl_plugin_tab_changelogs']);
$jltma_hide_system_info 	= jltma_check_options($jltma_white_label_setting['jltma_wl_plugin_tab_system_info']);
?>
<div class="master_addons">
	<div class="wrappper about-wrap">

		<div class="intro_wrapper">

			<header class="header">
				<a class="ma_el_logo" href="https://wordpress.org/plugins/master-addons" target="_blank">
					<div class="wp-badge welcome__logo ma_logo"></div>
				</a>

				<h1 class="ma_title">
					<?php if (!empty($jltma_white_label_setting['jltma_wl_plugin_menu_label'])) {
						printf(__('%s <small>v %s</small>'), $jltma_white_label_setting['jltma_wl_plugin_menu_label'], JLTMA_PLUGIN_VERSION);
					} else {
						printf(__('%s <small>v %s</small>'), MELA, JLTMA_PLUGIN_VERSION);
					}
					?>
				</h1>

				<div class="about-text"></div>
			</header>

		</div>

		<?php require_once MELA_PLUGIN_PATH . '/inc/admin/welcome/navigation.php'; ?>

		<div class="master_addons_contents">
			<?php
			require MELA_PLUGIN_PATH . '/inc/admin/welcome/supports.php';

			if (isset($jltma_hide_welcome) && !$jltma_hide_welcome) {
				require MELA_PLUGIN_PATH . '/inc/admin/welcome/addons.php';
			}

			if (isset($jltma_hide_extensions) && !$jltma_hide_extensions) {
				require MELA_PLUGIN_PATH . '/inc/admin/welcome/extensions.php';
			}

			if (isset($jltma_hide_api) && !$jltma_hide_api) {
				require MELA_PLUGIN_PATH . '/inc/admin/welcome/api-keys.php';
			}

			if (isset($jltma_hide_version) && !$jltma_hide_version) {
				require MELA_PLUGIN_PATH . '/inc/admin/welcome/version-control.php';
			}

			if (isset($jltma_hide_changelogs) && !$jltma_hide_changelogs) {
				require MELA_PLUGIN_PATH . '/inc/admin/welcome/changelogs.php';
			}

			if (isset($jltma_hide_white_label) && !$jltma_hide_white_label) {
				require MELA_PLUGIN_PATH . '/inc/admin/welcome/white-label.php';
			}

			if (isset($jltma_hide_system_info) && !$jltma_hide_system_info) {
				require MELA_PLUGIN_PATH . '/inc/admin/welcome/system-info.php';
			}
			?>
		</div>

	</div>
</div>
