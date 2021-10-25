<?php $jltma_white_label_options = get_option('jltma_white_label_settings'); ?>

<div class="wp-tab-panel" id="jltma_white_label" style="display: none;">

        <div class="master-addons-el-dashboard-header-wrapper">
            <div class="master-addons-el-dashboard-header-right">
                <button type="submit" class="master-addons-el-btn master-addons-el-js-element-save-setting" data-lic="valid">
                    <?php _e('Save Settings', MELA_TD); ?>
                </button>
            </div>
        </div>
  


    <form action="" method="POST" id="jltma-white-label-settings" class="jltma-white-label-settings" name="jltma-white-label-settings">

        <div class="master_addons_feature jltma-center-align">

            <!-- Start of White Label Settings -->
            <div class="api-settings-element">
                <h3><?php echo esc_html__('White Label Settings', MELA_TD); ?></h3>
                <div class="api-element-inner">
                    <div class="api-forms">

                        <div class="form-group">
                            <label for="jltma_wl_plugin_logo">
                                <?php echo esc_html__('Logo Image', MELA_TD); ?>
                            </label>
                            <div class="jltma-logo-handler">
                                <?php
                                $jltma_white_label_setting = jltma_get_options('jltma_white_label_settings');
                                $image_id = jltma_check_options($jltma_white_label_setting['jltma_wl_plugin_logo']);

                                if ($image = wp_get_attachment_image_src($image_id)) {
                                    echo '<a href="#" class="form-control jltma-wl-plugin-logo"><img src="' . $image[0] . '" /></a>
                                            <a href="#" class="jltma-remove-button"><i class="dashicons dashicons-no-alt"></i></a>
                                            <input type="hidden" name="jltma_wl_plugin_logo" value="' . $image_id . '">';
                                } else {
                                    $selected_image = isset($jltma_white_label_options['jltma_wl_plugin_logo']) ? $jltma_white_label_options['jltma_wl_plugin_logo'] : "";
                                    echo '<a href="#" class="form-control jltma-wl-plugin-logo"><i class="dashicons dashicons-cloud-upload"></i> <span>Upload image</span></a>
                                            <a href="#" class="jltma-remove-button" style="display:none"><i class="dashicons dashicons-no-alt"></i></a>
                                            <input type="hidden" class="jltma-whl-selected-image" name="jltma_wl_plugin_logo" value="">';
                                }
                                ?>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="jltma_wl_plugin_name">
                                <?php echo esc_html__('Plugin Name', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_name" type="text" class="form-control jltma_wl_plugin_name" value="<?php echo isset($jltma_white_label_options['jltma_wl_plugin_name']) ? $jltma_white_label_options['jltma_wl_plugin_name'] : ""; ?>">
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_desc">
                                <?php echo esc_html__('Plugin Description', MELA_TD); ?>
                            </label>
                            <textarea name="jltma_wl_plugin_desc" type="text" class="form-control jltma_wl_plugin_desc" cols="50"><?php echo isset($jltma_white_label_options['jltma_wl_plugin_desc']) ? $jltma_white_label_options['jltma_wl_plugin_desc'] : ""; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_author_name">
                                <?php echo esc_html__('Developer/Agency Name', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_author_name" type="text" class="form-control jltma_wl_plugin_author_name" value="<?php echo isset($jltma_white_label_options['jltma_wl_plugin_author_name']) ? $jltma_white_label_options['jltma_wl_plugin_author_name'] : ""; ?>">
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_menu_label">
                                <?php echo esc_html__('Menu Label', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_menu_label" type="text" class="form-control jltma_wl_plugin_menu_label" value="<?php echo isset($jltma_white_label_options['jltma_wl_plugin_menu_label']) ? $jltma_white_label_options['jltma_wl_plugin_menu_label'] : ""; ?>">
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_url">
                                <?php echo esc_html__('Plugin URL', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_url" type="text" class="form-control jltma_wl_plugin_url" value="<?php echo isset($jltma_white_label_options['jltma_wl_plugin_url']) ? $jltma_white_label_options['jltma_wl_plugin_url'] : ""; ?>">
                        </div>
                        <div class="form-group">
                            <label for="jltma_wl_plugin_row_links">
                                <?php echo esc_html__('Hide Plugin Row Meta Links', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_row_links" type="checkbox" class="form-control jltma_wl_plugin_row_links" <?php checked(1, $jltma_white_label_options['jltma_wl_plugin_row_links'], true) ?>>
                            <p class="pl-3"><?php echo __('This will hide Support, Docs & FAQs and Video Tutorials links on Plugins page.', MELA_TD); ?></p>
                        </div>

                    </div>
                </div><!-- /.api-element-inner -->
            </div><!-- /.api-settings-element -->
            <!-- End of White Label Settings -->


            <!-- Start of White Label Admin Settings -->
            <div class="api-settings-element">
                <h3><?php echo esc_html__('Admin Settings', MELA_TD); ?></h3>
                <div class="api-element-inner">
                    <div class="api-forms">

                        <div class="form-group">
                            <label for="jltma_wl_plugin_tab_welcome">
                                <?php echo esc_html__('Hide Welcome Tab', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_tab_welcome" type="checkbox" class="form-control jltma_wl_plugin_tab_welcome" <?php checked(1, $jltma_white_label_options['jltma_wl_plugin_tab_welcome'], true) ?>>
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_tab_addons">
                                <?php echo esc_html__('Hide Addons Tab', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_tab_addons" type="checkbox" class="form-control jltma_wl_plugin_tab_addons" <?php checked(1, $jltma_white_label_options['jltma_wl_plugin_tab_addons'], true) ?>>
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_tab_extensions">
                                <?php echo esc_html__('Hide Extensions Tab', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_tab_extensions" type="checkbox" class="form-control jltma_wl_plugin_tab_extensions" <?php checked(1, $jltma_white_label_options['jltma_wl_plugin_tab_extensions'], true) ?>>
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_tab_api">
                                <?php echo esc_html__('Hide Welcome Tab', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_tab_api" type="checkbox" class="form-control jltma_wl_plugin_tab_api" <?php checked(1, $jltma_white_label_options['jltma_wl_plugin_tab_api'], true) ?>>
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_tab_white_label">
                                <?php echo esc_html__('Hide White Label Tab', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_tab_white_label" type="checkbox" class="form-control jltma_wl_plugin_tab_white_label" <?php checked(1, $jltma_white_label_options['jltma_wl_plugin_tab_white_label'], true) ?>>
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_tab_version">
                                <?php echo esc_html__('Hide Version Tab', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_tab_version" type="checkbox" class="form-control jltma_wl_plugin_tab_version" <?php checked(1, $jltma_white_label_options['jltma_wl_plugin_tab_version'], true) ?>>
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_tab_changelogs">
                                <?php echo esc_html__('Hide Changelogs Tab', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_tab_changelogs" type="checkbox" class="form-control jltma_wl_plugin_tab_changelogs" <?php checked(1, $jltma_white_label_options['jltma_wl_plugin_tab_changelogs'], true) ?>>
                        </div>

                        <div class="form-group">
                            <label for="jltma_wl_plugin_tab_system_info">
                                <?php echo esc_html__('Hide System Info Tab', MELA_TD); ?>
                            </label>
                            <input name="jltma_wl_plugin_tab_system_info" type="checkbox" class="form-control jltma_wl_plugin_tab_system_info" <?php checked(1, $jltma_white_label_options['jltma_wl_plugin_tab_system_info'], true) ?>>
                        </div>

                        <p class="border border-danger p-2">
                            <strong><?php _e('NOTE: ', MELA_TD); ?></strong>
                            <?php echo __('You will need to reactivate Master Addons PRO for Elementor plugin to be able to reset White Labeling Tab Options.', MELA_TD); ?>
                        </p>

                    </div>
                </div><!-- /.api-element-inner -->
            </div><!-- /.api-settings-element -->
            <!-- End of White Label Admin Settings -->


        </div><!-- /.master_addons_feature -->
    </form>
</div>
