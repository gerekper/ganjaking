<div id="wu-product-data" class="postbox <?php echo postbox_classes('wu-product-data', get_current_screen()->id); ?>">
  <button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel: Advanced Options', 'wu-apc'); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button>
  <h2 class="hndle ui-sortable-handle">
    <span>
		<?php _e('Advanced Options', 'wu-apc'); ?>
    </span>
  </h2>
  
  <div class="inside">
    
    <div class="panel-wrap product_data">
      
      <ul class="product_data_tabs wc-tabs" style="">

        <?php

        /**
         * Tabs
         */
        $advanced_options_tabs = apply_filters('wu_apc_admin_pages_advanced_options_tabs', array(
          'permissions' => __('Permissions', 'wu-apc'),
          'separator'   => __('Separator', 'wu-apc'),
          'css'         => __('CSS', 'wu-apc'),
          'js'          => __('JavaScript', 'wu-apc'),
        ));

        if (WP_Ultimo_APC()->is_network_active()) {

			$advanced_options_tabs['excludes'] = __('Exclude Sites', 'wu-apc');

        } // end if;

        foreach ($advanced_options_tabs as $tab => $tab_label) :
			?>

          <li class="<?php echo $tab; ?>_options <?php echo $tab; ?>_tab">
            <a class="wu-code-<?php echo $tab; ?>" href="#wu_<?php echo $tab; ?>"><?php echo $tab_label; ?></a>
          </li>

        <?php endforeach; ?>

      </ul>

      <div id="wu_permissions" class="panel wu_options_panel">

        <div class="options_group">
          <p class="form-field limit_access_field">
            <label for="limit_access">
				<?php _e('Limit Access', 'wu-apc'); ?>
            </label>
            <input <?php checked($admin_page->limit_access); ?> type="checkbox" v-model="limit_access" class="checkbox" style="" name="limit_access" id="limit_access" value="1"> 
            <span class="description"><?php _e('Check this box to have access to the permission filters.', 'wu-apc'); ?></span>
          </p>
        </div>

        <?php if (is_array($plans_list)) : ?>

          <div class="options_group" v-show="limit_access">
            <p class="form-field site_template_field">
              
              <label class="form-field-full">
                <strong><?php _e('Plans Allowed', 'wu-apc'); ?></strong><br>
                <?php _e('Select the subscription plans allowed to have access to this page.', 'wu-apc'); ?><br>
              </label>
              
              <div style="margin: 0 15px;">
              <div class="row wu-sortable" id="multiselect-plan" style="margin-bottom: 15px;">

              <?php foreach ($plans_list as $plan_id => $plan_title) : ?>

                <div class="wu-col-sm-4" style="margin-bottom: 4px;">

                  <label for="multiselect-<?php echo $plan_title; ?>" style="margin: 0">
                    <input <?php checked($admin_page->is_plan_allowed($plan_id)); ?> name="<?php echo sprintf('%s[%s]', 'plans', $plan_id); ?>" type="checkbox" id="multiselect-<?php echo $plan_title; ?>" value="1">
                    <?php echo $plan_title; ?>
                  </label>
                
                </div>

			        <?php endforeach; ?>

              </div>

              <button data-select-all="multiselect-plan" class="button wu-select-all"><?php _e('Check / Uncheck All', 'wu-apc'); ?></button>

              </div>

            </p>
          </div>

        <?php endif; ?>
        
        <div class="options_group" v-show="limit_access">
          <p class="form-field site_template_field">
            
            <label class="form-field-full">
              <strong><?php _e('Roles Allowed', 'wu-apc'); ?></strong><br>
				<?php _e('Select the user roles allowed to have access to this page.', 'wu-apc'); ?><br>
            </label>
            
            <div style="margin: 0 15px;">
            <div class="row wu-sortable" id="multiselect-role" style="margin-bottom: 15px;">

            <?php foreach ($roles_list as $role_slug => $role_name) : ?>

              <div class="wu-col-sm-4" style="margin-bottom: 4px;">

                <label for="multiselect-<?php echo $role_name; ?>" style="margin: 0">
                  <input <?php checked($admin_page->is_role_allowed(array($role_slug))); ?> name="<?php echo sprintf('%s[%s]', 'roles', $role_slug); ?>" type="checkbox" id="multiselect-<?php echo $role_name; ?>" value="1">
                  <?php echo $role_name; ?>
                </label>
              
              </div>

            <?php endforeach; ?>

            </div>

            <button data-select-all="multiselect-role" class="button wu-select-all"><?php _e('Check / Uncheck All', 'wu-apc'); ?></button>

            </div>

          </p>
        </div>

        <div class="options_group" v-show="limit_access">
          <p class="form-field">
            <label for="wu-ajax-users">
              <strong><?php _e('Target Users', 'wu-apc'); ?></strong><br>
            </label>
            <input type="text" value="<?php echo $admin_page->id ? $admin_page->target_users : ''; ?>" name="target_users" id="wu-ajax-users" class="regular-text" placeholder="<?php _e('Select the target users', 'wu-apc'); ?>">

            <span style="display: block; clear: both;">
              <?php _e('Leave empty to display the page to users that match all other criteria set in the Permissions tab. Users selected here will see the page even if they are not part of the allowed roles in the Permissions tab.', 'wu-apc'); ?>
            </span>
          </p>
        </div>

      </div>

      <div id="wu_css" class="panel wu_options_panel">
        
        <div class="options_group">
          <p class="form-field">
            <label class="form-field-full">
              <strong><?php _e('CSS', 'wu-apc'); ?></strong><br>
				<?php _e('You can enter custom CSS in the box below. Be careful not to break the styles of the admin panel.', 'wu-apc'); ?>
            </label>
          </p>
          <div class="wu-code-container">
            <textarea id="css-content" name="css-content"><?php echo $admin_page->css_content; ?></textarea>
          </div>
        </div>

        <div class="options_group">
          <p class="form-field _quota_sites_field ">
            <label for="css-scripts">
              <strong><?php _e('Import External Styles', 'wu-apc'); ?></strong><br>
				<?php _e('Use the textarea to add CSS files you want to load. One URL per line.', 'wu-apc'); ?>
            </label>
            <textarea id="css-scripts" name="css-scripts" placeholder="https://site.com/css/styles.css"><?php echo $admin_page->css_scripts; ?></textarea>
          </p>
        </div>

      </div>

      <div id="wu_js" class="panel wu_options_panel">
        
        <div class="options_group">
          <p class="form-field">
            <label class="form-field-full">
              <strong><?php _e('JavaScript', 'wu-apc'); ?></strong><br>
				<?php _e('Use the box below to enter custom JavaScript code for this page.', 'wu-apc'); ?>
            </label>
          </p>
          <div class="wu-code-container">
            <textarea id="js-content" name="js-content"><?php echo $admin_page->js_content; ?></textarea>
          </div>
        </div>

        <div class="options_group">
          <p class="form-field _quota_sites_field ">
            <label for="js-scripts">
              <strong><?php _e('Import External Scripts', 'wu-apc'); ?></strong><br>
				<?php _e('Use the textarea to add JavaScript files you want to load. One URL per line. Be careful and only include scripts from trusted sources!', 'wu-apc'); ?>
            </label>
            <textarea id="js-scripts" name="js-scripts" placeholder="https://site.com/js/script.js"><?php echo $admin_page->js_scripts; ?></textarea>
          </p>
        </div>

      </div>

		<?php if (WP_Ultimo_APC()->is_network_active()) : ?>  

        <div id="wu_excludes" class="panel wu_options_panel">

          <div class="options_group">
            <p class="form-field">
              <label for="wu-ajax-excludes">
                <strong><?php _e('Exclude Sites', 'wu-apc'); ?></strong><br>
              </label>
              <input type="text" value="<?php echo $admin_page->id ? $admin_page->excludes_sites : ''; ?>" name="excludes_sites" id="wu-ajax-excludes" class="regular-text" placeholder="<?php _e('Select the target sites', 'wu-apc'); ?>">

              <span style="display: block; clear: both;">
                <?php _e('Select in which sites this admin page should NOT be displayed.', 'wu-apc'); ?>
              </span>
            </p>

          </div>
        </div>

      <?php endif; ?>  

      <div id="wu_separator" class="panel wu_options_panel" v-model="conditionaly_input">

        <div v-if="!conditionaly_input" 
        style="background-color: #f9f9f9;
        padding-top: 1px !important;
        box-shadow: none;
        border-right: solid 1px #ddd;
        border-left: 4px solid #dc3232;  
        margin: 12px;
        border-bottom: solid 1px #ddd;">
          <p style=""><?php _e('Separators are not applicable to this menu type or content source.', 'wu-apc'); ?></p>
        </div>

        <div class="options_group">
        <p class="form-field separator_before_field">
          <label for="separator_before">
      <?php _e('Before Menu Item', 'wu-apc'); ?>
          </label>
          <input :disabled="!conditionaly_input" v-model="separator_before" type="checkbox" class="checkbox" style="" name="separator_before" id="separator_before" value="1"> 
          <span class="description"><?php _e('Check this box to add a separator before the menu item.', 'wu-apc'); ?></span>
        </p>
        </div>
        <div class="options_group">
        <p class="form-field separator_after_field">
          <label for="separator_after">
      <?php _e('After Menu item', 'wu-apc'); ?>
          </label>
          <input :disabled="!conditionaly_input" v-model="separator_after" <?php checked($admin_page->separator_after); ?> type="checkbox" class="checkbox" style="" name="separator_after" id="separator_after" value="1"> 
          <span class="description"><?php _e('Check this box to add a separator after the menu item.', 'wu-apc'); ?></span>
        </p>
        </div>

      </div>
        

		<?php

		/**
		 * Displays the extra option panels for added Tabs
		 */

		do_action('wu_admin_pages_advanced_options_after_panels', $admin_page);

		?>
      
      <div class="clear"></div>
    </div>
  </div>
</div>
