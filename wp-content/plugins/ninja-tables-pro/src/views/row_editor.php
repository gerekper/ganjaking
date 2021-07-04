<?php
    $columns = $table_vars['original_columns'];
    $editing = $table_vars['editing'];
?>
    <div class="nt_editor_modal has_nt_modal" id="nt_editor_modal_<?php echo $table_id; ?>">
        <div class="nt_modal_wrapper">
            <div class="nt_form_loader">
                <i class="fooicon fooicon-loader"></i>
            </div>
            <form id="nt_editor_form_<?php echo $table_id; ?>">
                <div class="nt_modal_header">
                    <h3 class="nt_add_data_header">
                        <?php _e($editing['addModalLabel'] ? $editing['addModalLabel'] : 'Add Data', 'ninja-tables-pro'); ?>
                    </h3>
                    <h3 class="nt_edit_data_header">
                        <?php _e($editing['editModalLabel'] ? $editing['editModalLabel'] : 'Edit Data', 'ninja-tables-pro'); ?>
                    </h3>
                    <h3 class="nt_delete_data_header"><?php _e('Are you sure?', 'ninja-tables-pro'); ?></h3>
                    <span class="nt_editor_close nt_close_modal">x</span>
                </div>
                <div class="nt_modal_body nt_edit_add_modal_body">
                    <?php foreach ($columns as $column): ?>
                        <?php
                        if (empty($column['data_type'])) {
                            $column['data_type'] = 'text';
                        }
                        $isRequired = false;
                        if (in_array($column['key'], $requiredFields)) {
                            $isRequired = true;
                        }
                        if (in_array($column['key'], $editableFields)) :
                            ?>
                            <div class="nt_form_group">
                                <label><?php echo $column['name']; ?><?php if ($isRequired): ?> <span
                                        class="nt_is_required">*</span><?php endif; ?></label>
                                <div class="nt_form_control">
                                    <?php if ($column['data_type'] == 'text') { ?>
                                        <input name="<?php echo $column['key']; ?>"
                                               class="nt_form_input nt_data_item nt_input_<?php echo $column['key']; ?>"
                                               type="text"/>
                                    <?php } else if ($column['data_type'] == 'number') { ?>
                                        <input name="<?php echo $column['key']; ?>"
                                               data-number="yes"
                                               class="nt_form_input nt_data_item nt_input_<?php echo $column['key']; ?>"
                                               pattern="^[0-9.,]*"
                                               title="please input proper number"
                                               type="text"/>
                                    <?php } else if ($column['data_type'] == 'textarea') { ?>
                                        <textarea data-type="textarea" name="<?php echo $column['key']; ?>"
                                                  class="nt_form_textarea nt_data_item nt_input_<?php echo $column['key']; ?>"></textarea>
                                    <?php } else if ($column['data_type'] == 'html') { ?>
                                        <?php
                                        if (function_exists('wp_enqueue_editor')) {
                                            wp_enqueue_editor();
                                        }
                                        $mediaStatus = 'no';
                                        if (function_exists('wp_enqueue_media') && current_user_can('upload_files')) {
                                            wp_enqueue_media();
                                            $mediaStatus = 'yes';
                                        }
                                        ?>
                                        <textarea data-media_status="<?php echo $mediaStatus; ?>"
                                                  id="ninja_html_editor_<?php echo $table_id . '_' . $column['key']; ?>_"
                                                  name="<?php echo $column['key']; ?>"
                                                  data-type="html"
                                                  class="nt_form_html nt_data_item nt_input_<?php echo $column['key']; ?>"></textarea>
                                    <?php } else if ($column['data_type'] == 'date') { ?>
                                        <?php
                                        wp_enqueue_script('pikaday', NINJAPROPLUGIN_URL . 'assets/libs/datepicker/js/pikaday.min.js', array('jquery'), NINJAPROPLUGIN_VERSION, true);
                                        wp_enqueue_script('pikaday.jquery', NINJAPROPLUGIN_URL . 'assets/libs/datepicker/js/pikaday.jquery.js', array('pikaday'), NINJAPROPLUGIN_VERSION, true);
                                        wp_enqueue_style('pickaday.css', NINJAPROPLUGIN_URL . 'assets/libs/datepicker/css/pikaday.css', array(), NINJAPROPLUGIN_VERSION);
                                        ?>
                                        <input name="<?php echo $column['key']; ?>"
                                               data-date_format="<?php echo $column['dateFormat']; ?>"
                                               data-show_time="<?php echo @$column['showTime']; ?>"
                                               data-first_day_of_week="<?php echo @$column['firstDayOfWeek']; ?>"
                                               class="nt_form_input nt_form_date nt_data_item nt_input_<?php echo $column['key']; ?>"
                                               type="text"/>
                                    <?php } else if ($column['data_type'] == 'selection') { ?>
                                        <?php
                                        $selects = array();
                                        $selectionPlaceholder = $column['placeholder'] ? $column['placeholder'] : 'Select';
                                        if (isset($column['selections']) && $column['selections']) {
                                            $selectStrings = $column['selections'];
                                            $selects = preg_split('/\r\n|\r|\n/', $selectStrings);
                                        }
                                        $isMultple = '';
                                        if (isset($column['isMultiple'])) {
                                            $isMultple = $column['isMultiple'];
                                        }
                                        ?>
                                        <select <?php if ($isMultple == 'yes') {
                                            echo 'multiple';
                                        } ?> data-is_multi_select="<?php echo $isMultple; ?>"
                                             name="<?php echo $column['key']; ?>"
                                             class="nt_form_input nt_data_item nt_input_<?php echo $column['key']; ?>">
                                            <option
                                                value=""><?php echo apply_filters('ninja_edit_select_placeholder', __($selectionPlaceholder, 'ninja-tables-pro'), $column, $table_id); ?></option>
                                            <?php foreach ($selects as $select): ?>
                                                <option value="<?php echo $select; ?>"><?php echo $select; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php } else if ($column['data_type'] == 'number') { ?>
                                        <input name="<?php echo $column['key']; ?>"
                                               class="nt_form_input nt_data_item nt_input_<?php echo $column['key']; ?>"
                                               type="number"/>
                                    <?php } else if ($column['data_type'] == 'image') {
                                        $linkType = $column['link_type'];
                                        $mediaStatus = 'no';
                                        if (function_exists('wp_enqueue_media') && current_user_can('upload_files')) {
                                            wp_enqueue_media();
                                            $mediaStatus = 'yes';
                                        }
                                        ?>
                                        <?php if($mediaStatus == 'yes'): ?>
                                        <div data-type="image" data-link_type="image_light_box" class="nt_image_lightbox_wrapper nt_image_uploader_wrapper nt_input_<?php echo $column['key']; ?>">
                                            <?php if ($linkType == 'image_light_box' || $linkType == 'none' || $linkType == 'iframe_ligtbox' || $linkType == 'hyperlinked'):
                                                ?>
                                                    <input class="nt_hidden_image_input" data-key="<?php echo $column['key']; ?>" data-value_name="image_thumb" type="hidden" name="<?php echo $column['key'] ?>[image_thumb]" />
                                                    <input class="nt_hidden_image_input" data-key="<?php echo $column['key']; ?>" data-value_name="image_full" type="hidden" name="<?php echo $column['key'] ?>[image_full]" />
                                                    <input class="nt_hidden_image_input" data-key="<?php echo $column['key']; ?>" data-value_name="alt_text" type="hidden" name="<?php echo $column['key'] ?>[alt_text]" />
                                                    <div class="nt_image_preview">
                                                        <img src="" />
                                                    </div>
                                                    <div class="nt_image_change"><button class="nt_btn_upload"><?php _e('Upload', 'ninja-tables-pro');?></button></div>
                                                    <div class="nt_image_remove"><button class="nt_btn_remove"><?php _e('Remove', 'ninja-tables-pro');?></button></div>
                                            <?php endif; ?>
                                            <?php if($linkType == 'iframe_ligtbox' || $linkType == 'hyperlinked'): ?>
                                                <br />
                                                <label>
                                                    <?php if($linkType == 'iframe_ligtbox'): ?>
                                                    Iframe ULR
                                                    <?php else : ?>
                                                        Target URL
                                                    <?php endif; ?>
                                                    <input class="nt_hidden_image_input nt_form_input" data-key="<?php echo $column['key']; ?>" data-value_name="permalink" type="url" name="<?php echo $column['key'] ?>[permalink]" />
                                                </label>
                                            <?php endif; ?>
                                        </div>
                                        <?php else: ?>
                                            <p style="color: red;">You do not have access to upload media</p>
                                        <?php endif; ?>

                                    <?php } else if ($column['data_type'] == 'button') { ?>
                                            <input placeholder="Provide Button URL" name="<?php echo $column['key']; ?>"
                                                   class="nt_form_input nt_data_item nt_input_<?php echo $column['key']; ?>"
                                                   type="url"/>
                                        <?php } else { ?>
                                            <input name="<?php echo $column['key']; ?>"
                                                   class="nt_form_input nt_data_item nt_input_<?php echo $column['key']; ?>"
                                                   type="text"/>
                                        <?php } ?>
                                    </div>
                            </div>
                        <?php endif ?>
                    <?php endforeach; ?>
                </div>
                <div class="nt_modal_body nt_delete_modal_body">
                    <p><?php _e('Please confirm deletion. There is no undo!', 'ninja-tables-pro'); ?></p>
                </div>
                <div class="nt_modal_footer">
                    <div
                        class="nt_editor_action nt_editor_cancel nt_close_modal"><?php _e('Cancel', 'ninja-tables-pro'); ?></div>
                    <div data-action="keep_new"
                         class="nt_editor_action nt_editor_submit nt_editor_apply"><?php _e('Apply and Add New', 'ninja-tables-pro'); ?></div>
                    <div data-action="close"
                         class="nt_editor_action nt_editor_submit nt_editor_update"><?php _e('Update', 'ninja-tables-pro'); ?></div>
                    <div data-action="close"
                         class="nt_editor_action nt_editor_submit nt_editor_add"><?php _e('Add', 'ninja-tables-pro'); ?></div>
                    <div data-action="close" class="nt_editor_action nt_editor_delete"><span
                            style="vertical-align: middle;"
                            class="fooicon fooicon-delete"></span> <?php _e('Delete', 'ninja-tables-pro'); ?></div>
                </div>
            </form>
        </div>
    </div>

<?php if (!$userId): ?>
    <style>
        td.footable-editing, th.footable-editing {
            display: none !important;
            visibility: hidden !important;
        }
    </style>
<?php endif; ?>
