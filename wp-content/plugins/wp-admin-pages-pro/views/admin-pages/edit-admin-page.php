<?php

add_thickbox();

wp_enqueue_script('dashboard');

$edit = $admin_page->id > 0;

?>

<div id="wp-ultimo-wrap" class="wrap">
  <h1>
    <?php echo $edit ? __('Edit Admin Page', 'wu-apc') : __('Add New Admin Page', 'wu-apc'); ?>
  </h1>
  
	<?php if (isset($_GET['updated'])) : ?>
    <div id="message" class="updated notice notice-success is-dismissible below-h2">
      <p><?php _e('Admin Page updated with success!', 'wu-apc'); ?></p>
    </div>
	<?php endif; ?>
  
  <form name="post" method="post" id="wpultimo-admin-page" autocomplete="off">
    
    <div id="poststuff">
      <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content" style="position: relative;">
          <div id="titlediv">
            
            <div id="titlewrap">
              <input type="text" placeholder="<?php _e('Enter Admin Page Title', 'wu-apc'); ?>" name="title" size="30" value="<?php echo $admin_page->title; ?>" id="title" spellcheck="true" autocomplete="off">
            </div>
          
          </div>
          <!-- /titlediv -->
                    
          <div id="postdivrich" class="postarea wp-editor-expand">

            <div id="wu-apc-editor-app">
              <ul class="wu-apc-content-type-selector">

                <?php foreach (WU_Admin_Pages()->get_editor_options() as $editor_type => $editor_type_info) : ?>

                  <li v-bind:title="'<?php echo $editor_type_info['active'] ? '' : esc_js($editor_type_info['title']); ?>'" v-bind:class="(content_type == '<?php echo $editor_type; ?>' ? 'active' : '')" class="wu-tooltip <?php echo $editor_type_info['active'] ? '' : 'deactivated'; ?>">
                    <label for="<?php echo $editor_type; ?>">
                      <span class="<?php echo $editor_type_info['icon']; ?>"></span>
                      <input <?php disabled(!$editor_type_info['active']); ?> id="<?php echo $editor_type; ?>" name="content_type" v-model="content_type" value="<?php echo $editor_type; ?>" type="radio">
                      <?php echo $editor_type_info['label']; ?>
                    </label>
                  </li>

                <?php endforeach; ?>

              </ul>

              <div id="normal-editor" v-cloak v-show="content_type == 'normal'">
                <?php
                wp_editor(
                  $admin_page->content, 'content', array(
					  'drag_drop_upload'  => true,
					  'tabfocus_elements' => 'content-html,save-post',
					  'editor_height'     => 300,
					  'tinymce'           => array(
						  'resize'             => true,
						  'add_unload_trigger' => false,
					  ),
                  )
                );
                ?>
              </div>

              <div v-cloak id="html-editor" v-show="content_type == 'html'" class="wu-code-container" style="margin: 12px 0 0; border-color: #cecece">
                <textarea id="html-content" name="html-content"><?php echo htmlspecialchars($admin_page->html_content); ?></textarea>
              </div>

				<?php if (defined('WU_APC_ALLOW_PHP_PROCESSING') && WU_APC_ALLOW_PHP_PROCESSING) : ?>

                <div v-cloak v-if="content_type == 'html'" :class="content_type == 'html' ? 'notice notice-success' : ''">
                  <p><?php _e('PHP processing is enabled for this WordPress install and is supported on custom admin pages. To remove PHP processing, remove <code>define("WU_APC_ALLOW_PHP_PROCESSING", true);</code> from your wp-config.php.', 'wu-apc'); ?></p>
                </div>

				<?php else : ?>

                <div v-cloak v-if="content_type == 'html'" :class="content_type == 'html' ? 'notice notice-warning' : ''">
                  <p><?php _e('The HTML content block does not support PHP by default. If you wish to add PHP processing to your pages, add <code>define("WU_APC_ALLOW_PHP_PROCESSING", true);</code> to your wp-config.php.', 'wu-apc'); ?></p>
                </div>

				<?php endif; ?>

				<?php
				/**
				 * Let plugin developers add new editor options =)
				 *
				 * @since 1.0.1
				 * @param WU_Admin_Page The current admin page
				 * @return void
				 */
				do_action('wu_admin_pages_editors', $admin_page);
				?>

            </div>

          </div>

        </div>
        
        <!-- /post-body-content -->
        <div id="postbox-container-1" class="postbox-container">
          <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
            
            <div id="submitdiv" class="postbox <?php echo postbox_classes('submitdiv', get_current_screen()->id); ?>">
              
              <button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle Panel: General Options', 'wu-apc'); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button>
              <h2 class="hndle ui-sortable-handle"><span><?php _e('General Options', 'wu-apc'); ?></span></h2>
              
              <div class="inside">
                <div class="submitbox" id="submitpost">
                  
                  <div id="minor-publishing">
                      
                    <!-- .misc-pub-section -->
                    <div class="misc-pub-section curtime misc-pub-curtime">
                      
                      <p class="wpultimo-price-text">
                        <?php _e('Use this block to define the settings for the menu item for this page.', 'wu-apc'); ?>
                      </p>

                      <p class="" v-if="false" style="text-align: center; margin-bottom: 5px">
                        <?php _e('Loading...', 'wu-apc'); ?>
                      </p>

                      <div v-cloak>

                        <div class="wpultimo-price wpultimo-price-first" style="margin-top: 10px; text-align: left;">

                          <label for="menu_type">
                            <?php _e('Type', 'wu-apc'); ?>
                          </label>

                          <select id="menu_type" name="menu_type" v-model="menu_type" style="width: 100%" class="form-control">
                            <option <?php selected($admin_page->menu_type, 'menu'); ?> v-show="content_type !== 'hide_page'" value="menu"><?php _e('Top-level Menu', 'wu-apc'); ?></option>
                            <option <?php selected($admin_page->menu_type, 'submenu'); ?> v-show="content_type !== 'hide_page'" value="submenu"><?php _e('Submenu', 'wu-apc'); ?></option>
                            <option <?php selected($admin_page->menu_type, 'replace'); ?> value="replace"><?php _e('Replace Existing Page', 'wu-apc'); ?></option>
                            <option <?php selected($admin_page->menu_type, 'replace_submenu'); ?> value="replace_submenu"><?php _e('Replace Existing Sub Page', 'wu-apc'); ?></option>
                            <option <?php selected($admin_page->menu_type, 'widget'); ?> v-show="content_type !== 'hide_page'" value="widget"><?php _e('Dashboard Widget', 'wu-apc'); ?></option>
                          </select>

                        </div>

                        <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="menu_type == 'submenu'">

                          <label for="menu_parent">
                            <?php _e('Menu Parent', 'wu-apc'); ?>
                          </label>

                          <select id="menu_parent" name="menu_parent"  style="width: 100%" class="form-control">
                            <?php foreach ($menu_parent_list as $slug => $label) : ?>
                                <?php if (!is_numeric($label)) : ?>
                                <option <?php selected($admin_page->menu_parent, $slug); ?> value="<?php echo $slug; ?>">
							                	<?php echo wu_apc_remove_html_tags_and_content($label); ?>
                                </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                          </select>

                        </div>

                        <div class="wpultimo-price wpultimo-price-first" id="div_checkbox_apply_multiple_pages" style="text-align: left;" v-show="menu_type == 'replace' && content_type != 'hide_page' || menu_type == 'replace_submenu' && content_type != 'hide_page'" >
                            <label for="apply_multiple_pages">
                              <input style="width: inherit !important;" v-model="apply_multiple_pages" type="checkbox" <?php checked($admin_page->apply_multiple_pages); ?> name="apply_multiple_pages" id="apply_multiple_pages">
                              <span><?php _e('Apply to multiple pages?', 'wu-apc'); ?></span>
                            </label>
                            <small><?php _e('Check this box to apply this page to multiple admin pages.', 'wu-apc'); ?></small>
                        </div>

                        <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="menu_type == 'replace'">

                          <label for="page_to_replace">
                            <?php _e('Page to Replace', 'wu-apc'); ?>
                          </label>

                          <select id="page_to_replace" name="page_to_replace" v-model="page_to_replace" style="width: 100%" class="form-control" v-if="menu_type == 'replace'">

                            <?php foreach ($menu_parent_list as $slug => $label) : ?>
                                <?php if (!is_numeric($label)) : ?>
                                <option <?php !is_array($admin_page->page_to_replace) ? selected($admin_page->page_to_replace, $slug) : ''  ; ?> value="<?php echo $slug; ?>">
							                	  <?php echo wu_apc_remove_html_tags_and_content($label); ?>
                                </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                          </select>

                          <?php if (!$menu_parent_list) : ?>
                          <div v-bind:class="true ? 'error' : ''">
                            <p><?php _e('There seems to be an error fetching your list of admin pages.', 'wu-apc'); ?> <?php printf('<a href="%s">%s</a>.', get_admin_url(1, '?flush_menu_and_submenus=1'), __('Click here to reset that list', 'wu-apc')); ?></p>
                          </div>
                          <?php endif; ?>

                        </div>
                        

                        <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="menu_type == 'replace' && content_type !== 'hide_page'">

                          <label for="replace_mode">
                            <?php _e('How to Replace', 'wu-apc'); ?>
                          </label>

                          <select id="replace_mode" name="replace_mode" v-model="replace_mode" style="width: 100%" class="form-control">
                            <option <?php selected($admin_page->replace_mode, 'all'); ?> value="all"><?php _e('Replace All Content', 'wu-apc'); ?></option>
                            <option <?php selected($admin_page->replace_mode, 'append_top'); ?> value="append_top"><?php _e('Append this content at the Top', 'wu-apc'); ?></option>
                            <option <?php selected($admin_page->replace_mode, 'append_bottom'); ?> value="append_bottom"><?php _e('Append this content at the Bottom', 'wu-apc'); ?></option>
                          </select>

                        </div>


                        <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-if="menu_type == 'replace_submenu'">

                          <label for="page_to_replace">
                            <?php _e('Sub Page to Replace', 'wu-apc'); ?>
                          </label>

                          <select id="page_to_replace" name="page_to_replace" v-model="page_to_replace" style="width: 100%" class="form-control" v-if="menu_type == 'replace_submenu'">
                            <?php $indexsub = 0; ?>
                            <?php foreach ($menu_list as $slug => $label) : ?>
					                  <?php $fixed_slug = trim(wu_cut_string_square_brackets($slug)); ?>
								            <?php if (strpos($slug, 'wusubdiv') !== false && $fixed_slug !== 'none' && !empty($fixed_slug)) : ?>

									            <?php
                                    if ($indexsub !== 0) {
									    	              echo '</optgroup>'; } // end if;

									            ?>

                                <optgroup label="<?php echo wu_apc_remove_html_tags_and_content($label); ?>">

								              <?php else : ?>
									            <?php if (!is_numeric($label) && !empty($label) && $label !== 'none' ) : ?>
                                <option <?php !is_array($admin_page->page_to_replace) ? selected($admin_page->page_to_replace, $slug) : ''; ?> value="<?php echo $slug; ?>">

										          <?php echo wu_apc_remove_html_tags_and_content($label); ?>

                                </option>
					<?php endif; ?>
								<?php endif; ?>

								<?php $indexsub++; ?>
                <?php endforeach; ?>

                          </select>

                          <?php if (!$menu_parent_list) : ?>
                          <div v-bind:class="true ? 'error' : ''">
                            <p><?php _e('There seems to be an error fetching your list of admin pages.', 'wu-apc'); ?> <?php printf('<a href="%s">%s</a>.', get_admin_url(1, '?flush_menu_and_submenus=1'), __('Click here to reset that list', 'wu-apc')); ?></p>
                          </div>
                          <?php endif; ?>

                        </div>


                        <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="menu_type == 'replace_submenu' && content_type !== 'hide_page'">

                          <label for="replace_mode">
                            <?php _e('How to Replace', 'wu-apc'); ?>
                          </label>

                          <select id="replace_mode" name="replace_mode" v-model="replace_mode" style="width: 100%" class="form-control">
                            <option <?php selected($admin_page->replace_mode, 'all'); ?> value="all"><?php _e('Replace All Content', 'wu-apc'); ?></option>
                            <option <?php selected($admin_page->replace_mode, 'append_top'); ?> value="append_top"><?php _e('Append this content at the Top', 'wu-apc'); ?></option>
                            <option <?php selected($admin_page->replace_mode, 'append_bottom'); ?> value="append_bottom"><?php _e('Append this content at the Bottom', 'wu-apc'); ?></option>
                          </select>

                        </div>

                        
                        <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="menu_type == 'widget'">
                            <label for="widget_welcome">
                              <input style="width: inherit !important;" v-model="widget_welcome" type="checkbox" <?php checked($admin_page->widget_welcome); ?> name="widget_welcome" id="widget_welcome">
                              <span><?php _e('Display as Welcome Box?', 'wu-apc'); ?></span>
                            </label>
                            <small><?php _e('Display this widget in the place of the default WordPress Welcome widget.', 'wu-apc'); ?></small>
                          </div>
                          
                          <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="menu_type == 'widget' && widget_welcome">
                              <label for="widget_welcome_dismissible">
                                <input style="width: inherit !important;" v-model="widget_welcome_dismissible" type="checkbox" <?php checked($admin_page->widget_welcome_dismissible); ?> name="widget_welcome_dismissible" id="widget_welcome_dismissible">
                                <span><?php _e('Is Dismissible?', 'wu-apc'); ?></span>
                              </label>
                              <small><?php _e('Unchecking this box will remove the dismiss link from the Welcome box.', 'wu-apc'); ?></small>
                            </div>
                                                  
                            <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="menu_type == 'widget' && !widget_welcome">
    
                              <label for="widget_position">
                                <?php _e('Position', 'wu-apc'); ?>
                              </label>
    
                              <select id="widget_position" name="widget_position" v-model="widget_position" style="width: 100%" class="form-control">
                                <option <?php selected($admin_page->widget_position, 'normal'); ?> value="normal"><?php _e('Normal', 'wu-apc'); ?></option>
                                <option <?php selected($admin_page->widget_position, 'side'); ?> value="side"><?php _e('Side', 'wu-apc'); ?></option>
                              </select>
    
                            </div>
    
                            <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="menu_type == 'widget' && !widget_welcome">
    
                              <label for="widget_priority">
                                <?php _e('Priority', 'wu-apc'); ?>
                              </label>
    
                              <select id="widget_priority" name="widget_priority" v-model="widget_priority" style="width: 100%" class="form-control">
                                <option <?php selected($admin_page->widget_priority, 'high'); ?> value="high"><?php _e('High', 'wu-apc'); ?></option>
                                <option <?php selected($admin_page->widget_priority, 'low'); ?> value="low"><?php _e('Low', 'wu-apc'); ?></option>
                              </select>
    
                            </div>

                            <div class="" style="text-align: left;" v-show="menu_type == 'replace' && page_to_replace == 'index.php' && replace_mode != 'all' ">
                                <div class="inside">
                                  <p style="padding-left: 12px;">
                                      <label for="show_welcome">
                                        <input type="checkbox" <?php checked($admin_page->show_welcome || !$edit); ?> name="show_welcome" id="show_welcome">
                                        <?php _e('Show Welcome box?', 'wu-apc'); ?>
                                      </label><br>
                                    </p>
                                  </div>
                                  
                                </div>
                                


                        <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="!apply_multiple_pages && content_type !== 'hide_page'" v-if="menu_type == 'menu' || menu_type == 'submenu' || menu_type == 'replace' || menu_type == 'replace_submenu'">


                          <label for="menu_label">
                          <span v-if="menu_type == 'replace' || menu_type == 'replace_submenu'"> <?php _e('Rename', 'wu-apc'); ?> </span>  
                          <?php _e('Admin Menu Label', 'wu-apc'); ?>
                          </label>

                          <input type="text" type="text" placeholder="Premium Content" id="menu_label" name="menu_label" value="<?php echo $admin_page->menu_label; ?>">

                        </div>

                        <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-if="menu_type == 'menu' || menu_type == 'submenu'">

                          <label for="menu_order">
                            <?php _e('Menu Order', 'wu-apc'); ?>
                          </label>

                          <input type="number" placeholder="15" id="menu_order" name="menu_order" value="<?php echo $admin_page->menu_order; ?>">

                          <small><?php _e('Items are usually spaced in increments of 10', 'wu-apc'); ?></small>

                        </div>

                      
                         <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="(menu_type == 'menu' || menu_type == 'submenu' || menu_type == 'widget') ">

                          <label for="slug_url">
                            <?php _e('Slug', 'wu-apc'); ?>
                          </label>

                          <input type="text" placeholder="<?php _e('Optional', 'wu-apc'); ?>" id="slug_url" name="slug_url" value="<?php echo $admin_page->slug_url; ?>">
                          
                          <small><?php _e('This will be used for the URL', 'wu-apc'); ?></small>

                        </div>
                      

                        <div class="wpultimo-price wpultimo-price-first" style="text-align: left;" v-show="menu_type == 'menu'">

                          <label for="menu_label">
                            <?php _e('Admin Menu Icon', 'wu-apc'); ?>
                          </label>

                          <select id="menu_icon_selector" name="menu_icon">
                            <option value="<?php _e('No icon', 'wu_apc'); ?>"></option>
                            <?php
                            $menu_icon = $admin_page->menu_icon ?: 'dashicons-before dashicons-admin-site';
                            foreach ($icons_list as $icon) :
								?>
                              <option <?php selected($icon == $menu_icon); ?>><?php echo $icon; ?></option>
                            <?php endforeach; ?>
                          </select>

                        </div>

                      </div>

                      <div class="clear"></div>
                      
                    </div>
                    
                    <div class="clear"></div>
                  </div>
                  
                  <div id="major-publishing-actions">
                
                    <input name="original_publish" type="hidden" id="original_publish" value="Publish">
                    <input type="submit" name="save_admin_page" id="publish" class="button button-primary button-large button-streched" value="<?php echo $edit ? __('Update Admin Page', 'wu-apc') : __('Create Admin Page', 'wu-apc'); ?>">
                    
                    <div class="clear"></div>
                  </div>
                  
                </div>
              </div>
            </div>

            <div id="wu-admin-page-display-options" class="postbox <?php echo postbox_classes('wu-admin-page-display-options', get_current_screen()->id); ?>">

              <button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel: Display Options', 'wu-apc'); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button>
              <h2 class="hndle ui-sortable-handle"><span><?php _e('Display Options', 'wu-apc'); ?></span></h2>
              
              <div class="inside">
                
                <p v-if="menu_type !== 'widget'" >
                  <label for="display_title">
                    <input type="checkbox" <?php checked($admin_page->display_title || !$edit); ?> name="display_title" id="display_title">
                    <?php _e('Display the Page Title?', 'wu-apc'); ?>
                  </label><br>
                </p>
                
                <p v-if="menu_type !== 'widget'" class="description"><?php _e('This will add the page title at the top of the custom page.', 'wu-apc'); ?></p>
              
                <p>
                  <label for="add_margin">
                    <input type="checkbox" <?php checked($admin_page->add_margin || !$edit); ?> name="add_margin" id="add_margin">
                    <?php _e('Add the Page Margin?', 'wu-apc'); ?>
                  </label><br>
                </p>
                
                <p class="description"><?php _e('This will keep the normal margins of and admin page. You might want to remove this if you are using HTML or page builders as the content.', 'wu-apc'); ?></p>

                <p v-if="menu_type !== 'widget'">
                  <label for="display_admin_notices">
                    <input type="checkbox" <?php checked($admin_page->display_admin_notices || !$edit); ?> name="display_admin_notices" id="display_admin_notices">
                    <?php _e('Display Admin Notices?', 'wu-apc'); ?>
                  </label><br>
                </p>

                <p v-if="menu_type !== 'widget'" class="description"><?php _e('Use this option to control wether or not you want to display admin notices on this custom page.', 'wu-apc'); ?></p>
                
                <?php if (WP_Ultimo_APC()->is_network_active()) : ?> 

                <p>
                  <label for="display_page_main_site">
                    <input type="checkbox" <?php checked($admin_page->display_page_main_site || !$edit); ?> name="display_page_main_site" id="display_page_main_site">
                    <?php _e('Display this Page in Main Site?', 'wu-apc'); ?>
                  </label><br>
                </p>
                
                <p class="description"><?php _e('Use this option to control if you want to display this page on main site as well.', 'wu-apc'); ?></p>
                
                <?php endif; ?>

              </div>
              
            </div>

            <div id="wu-admin-page-active" class="postbox <?php echo postbox_classes('wu-admin-page-active', get_current_screen()->id); ?>">
              <button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel: Active', 'wu-apc'); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button>
              <h2 class="hndle ui-sortable-handle"><span><?php _e('Active', 'wu-apc'); ?></span></h2>
              
              <div class="inside">
                
                <p>
                <label for="active">
                  <input type="checkbox" <?php checked($admin_page->active || !$edit); ?> name="active" id="active">
					<?php _e('Is this Admin Page active?', 'wu-apc'); ?>
                </label>
                </p>
                
                <p class="description"><?php _e('This page will not appear inside your users Dashboard panel until it is marked as Active.', 'wu-apc'); ?></p>
                
              </div>
              
            </div>
            
            <?php if ($edit) : ?>
            <div id="wp-admin_page-delete" class="postbox <?php echo postbox_classes('wp-admin_page-delete', get_current_screen()->id); ?>">
              <button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel: Delete this Admin Page', 'wu-apc'); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button>
              <h2 class="hndle ui-sortable-handle"><span><?php _e('Delete this Admin Page', 'wu-apc'); ?></span></h2>
              
              <div class="inside">
                
                <?php
                $delete_nonce = wp_create_nonce('wpultimo_delete_admin_page');
                if (WP_Ultimo_APC()->is_network_active()) {
					$delete_url = network_admin_url(sprintf('admin.php?page=%s&action=%s&admin_page=%s&_wpnonce=%s', 'wp-ultimo-admin-pages', 'delete', absint($admin_page->id), $delete_nonce));
                } else {
					$delete_url = admin_url(sprintf('admin.php?page=%s&action=%s&admin_page=%s&_wpnonce=%s', 'wp-ultimo-admin-pages', 'delete', absint($admin_page->id), $delete_nonce));
                } // end if;

                ?>
                
                <p><?php _e('Be careful, this cannot be undone!', 'wu-apc'); ?></p>
                
                <a class="button button-large button-delete button-streched" href="<?php echo $delete_url; ?>"><?php _e('Delete this Admin Page', 'wu-apc'); ?></a>
                
              </div>
              
            </div>
            <?php endif; ?>
            
          </div>

        </div>
        
        <div id="postbox-container-2" class="postbox-container">
          <div id="normal-sortables" class="meta-box-sortables ui-sortable">
            
            <?php

            WU_Admin_Pages()->enqueue_select2();

            /**
             * Let plugin developers add new metaboxes options =)
             *
             * @since 1.0.1
             */
            do_action('wu_admin_pages_extra_metaboxes');

            WP_Ultimo_APC()->render('admin-pages/admin-page-advanced-options', array(
				'admin_page' => $admin_page,
				'roles_list' => $roles_list,
				'plans_list' => $plans_list,
            ));

            ?>
            
          </div>
          <div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>
        </div>
      </div>
      
      <!-- /post-body -->
      <br class="clear">
      
    </div>
    <!-- /poststuff -->
    
    <?php wp_nonce_field('saving_admin_page', '_wpultimo_nonce'); ?>

    <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
    
    <?php if ($edit) : ?>
      <input type="hidden" name="admin_page_id" value="<?php echo $admin_page->id; ?>">
    <?php endif; ?>
    
  </form>
</div>
