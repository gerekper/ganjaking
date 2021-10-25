<?php
	namespace MasterAddons\Admin\Dashboard\Addons;
	use MasterAddons\Master_Elementor_Addons;
?>


<div class="wp-tab-panel" id="ma-addons" style="display: none;">
    <div class="master_addons_features">

        <div class="master-addons-el-dashboard-header-wrapper">
            <div class="master-addons-el-dashboard-header-right">
                <button type="submit" class="master-addons-el-btn master-addons-el-js-element-save-setting">
					<?php _e('Save Settings', MELA_TD ); ?>
                </button>
            </div>
        </div>


        <div class="master-addons-el-dashboard-wrapper">
            <form action="" method="POST" id="master-addons-el-settings" class="master-addons-el-settings"
                  name="master-addons-el-settings">
				<?php wp_nonce_field( 'maad_el_settings_nonce_action' ); ?>
                <div class="master-addons-el-dashboard-tabs-wrapper">
                    <div id="master-addons-elements" class="master-addons-el-dashboard-header-left master-addons-dashboard-tab master_addons_features">
						<?php
							include_once MELA_PLUGIN_PATH . '/inc/admin/welcome/addons-elements.php';
							include_once MELA_PLUGIN_PATH . '/inc/admin/welcome/addons-forms.php';
							include_once MELA_PLUGIN_PATH . '/inc/admin/welcome/addons-marketing.php';
						?>
                    </div>
                </div> <!-- .master-addons-el-dashboard-tabs-wrapper-->
            </form>
        </div>
    </div>
</div>