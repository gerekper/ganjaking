<?php

namespace MasterAddons\Admin\Dashboard\Addons;

use MasterAddons\Master_Elementor_Addons;
use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Marketing;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

// include_once MELA_PLUGIN_PATH . '/inc/admin/jltma-elements/ma-marketing.php';
?>

<div class="master_addons_feature">

    <h3><?php echo esc_html__('Marketing', MELA_TD); ?></h3>

    <?php foreach (JLTMA_Addon_Marketing::$jltma_marketing['jltma-marketing']['elements'] as $key => $widget) : ?>

        <div class="master-addons-dashboard-checkbox col">
            <div class="master-addons-dashboard-checkbox-content">

                <div class="master-addons-features-ribbon">
                    <?php if (isset($widget['is_pro']) && $widget['is_pro']) {
                        echo '<span class="pro-ribbon">Pro</span>';
                    } ?>
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
                    <label for="<?php echo esc_attr($widget['key']); ?>" class="switch switch-text switch-primary switch-pill
                    <?php if (isset($widget['is_pro']) && $widget['is_pro']) {
                        echo "ma-el-pro";
                    } ?>">


                            <input type="checkbox" id="<?php echo esc_attr($widget['key']); ?>" class="switch-input" name="<?php echo esc_attr($widget['key']); ?>" <?php checked(1, $this->jltma_get_element_settings[$widget['key']], true); ?>>

                       
                        <span data-on="On" data-off="Off" class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
        </div>

    <?php endforeach; ?>

</div> <!--  .master_addons_feature-->
