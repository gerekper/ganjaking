<?php

namespace NinjaTablesPro\App\Hooks\Handlers;

use NinjaTables\Framework\Support\Arr;
use NinjaTablesPro\App\Models\NinjaTableItem;

class TableEditorHandler
{
    public static function savedCustomCode($data)
    {
        $tableId = intval($data['table_id']);
        $js      = $data['custom_js'];
        update_post_meta($tableId, '_ninja_tables_custom_js', $js);
    }

    public function addEditorDom($table, $table_vars)
    {
        if ( ! empty($table_vars['editing']['enabled']) && $table_vars['editing']['enabled']) {
            wp_enqueue_script('ninja-tables-pro', NINJAPROPLUGIN_URL . 'assets/js/ninja-tables-pro.js', array('jquery'),
                NINJAPROPLUGIN_VERSION, true);
            $table_id       = $table->ID;
            $requiredFields = $this->getRequiredFields($table_id);
            $editableFields = $this->getEditableFields($table_id);
            $userId         = get_current_user_id();
            include NINJAPROPLUGIN_PATH . 'app/Views/row-editor.php';
        }
    }

    private function getRequiredFields($tableId)
    {
        $editPref = get_post_meta($tableId, '_ninja_table_frontedit_pref', true);
        if ($editPref && ! empty($editPref['required_items'])) {
            $requiredFields = array_filter($editPref['required_items'], function ($val) {
                return $val == 'yes';
            });
            $editFields     = array_filter($editPref['editing_items'], function ($val) {
                return $val == 'yes';
            });

            return array_intersect(array_keys($requiredFields), array_keys($editFields));
        }

        return array();
    }

    private function getEditableFields($tableId)
    {
        $editPref = get_post_meta($tableId, '_ninja_table_frontedit_pref', true);
        if ($editPref && ! empty($editPref['editing_items'])) {
            $fields = array_filter($editPref['editing_items'], function ($val) {
                return $val == 'yes';
            });

            return array_keys($fields);
        }

        return array();
    }

    public function routeUpdateRow()
    {
        ninjaTablesValidateNonce('ninja_table_public_nonce');
        $tableId = intval($_REQUEST['table_id']);
        $rowId   = false;

        if (isset($_REQUEST['row_id']) && intval($_REQUEST['row_id'])) {
            $rowId = intval($_REQUEST['row_id']);
        }

        $this->checkRowPermission($tableId, $rowId);
        $data     = ninja_tables_sanitize_array($_REQUEST);
        $values   = wp_unslash($data['values']);
        $provider = ninja_table_get_data_provider($tableId);

        // Validate Submitted Data
        // Get Required Fields
        $columns        = ninja_table_get_table_columns($tableId, 'admin');
        $allColumnArray = array();
        foreach ($columns as $column) {
            $allColumnArray[$column['key']] = $column['name'];
        }
        // Now get the required fields
        $requiredFields = $this->getRequiredFields($tableId);

        $validRequiredFields = array_intersect(array_keys($allColumnArray), $requiredFields);

        $errors = array();
        foreach ($validRequiredFields as $requiredField) {
            if ( ! isset($values[$requiredField]) || $values[$requiredField] == '') {
                $errors[$requiredField] = $allColumnArray[$requiredField] . __(' is required', 'ninja-tables-pro');
            }
        }

        $errors = apply_filters('ninja_table_pro_editor_data_submission', $errors, $tableId, $values, $allColumnArray);

        if ($errors) {
            wp_send_json_error(array(
                'errors'  => $errors,
                'message' => __('Validation failed, Please fill up required fields', 'ninja-tables-pro')
            ), 400);
        }

        do_action('ninja_table_update_row_data_' . $provider, $values, $rowId, $tableId);
    }

    private function checkRowPermission($tableId, $rowId = false)
    {
        if (current_user_can('administrator')) {
            return true;
        }

        $operation = 'add';
        if ($rowId) {
            $operation = 'update';
        }
        $userId   = get_current_user_id();
        $settings = get_post_meta($tableId, '_ninja_table_frontedit_settings', true);

        if ( ! $userId && $operation == 'update') {
            $this->errorResponse(__('Sorry! You do not have permission to edit this data', 'ninja-table-pro'));

            return;
        }

        if ( ! $this->hasEditingPermission($tableId, $userId, $settings)) {
            $this->errorResponse(__('Sorry! You do not have permission to edit this data', 'ninja-table-pro'));

            return;
        }
        // Check The editor Permission Now
        if ($settings['own_data_only'] == 'no' || $operation == 'add') {
            return true;
        }

        // Now Check if the Current Row Have
        $preRow = NinjaTableItem::where('table_id', $tableId)
                                ->where('id', $rowId)
                                ->first();

        if ($preRow && $preRow->owner_id == $userId) {
            return true;
        }
        $this->errorResponse(__('Sorry! You do not have permission to perform this action', 'ninja-table-pro'));

        return;
    }

    public function updateRowDefaultTable($values, $rowId, $tableId)
    {
        $columns        = ninja_table_get_table_columns($tableId, 'admin');
        $allColumnArray = array();
        $imageTypes     = [];
        foreach ($columns as $column) {
            $allColumnArray[$column['key']] = $column['name'];
            if ($column['data_type'] == 'image') {
                $imageTypes[$column['key']] = $column;
            }
        }

        if ($rowId) {
            $preRow = NinjaTableItem::where('table_id', $tableId)
                                    ->where('id', $rowId)
                                    ->first();
            if ( ! $preRow) {
                $this->errorResponse('No record found to update, Please try again');
            }
            $prevValues = json_decode($preRow->value, true);
            foreach ($values as $valueKey => $value) {
                $prevValues[$valueKey] = $value;
            }
            $attributes = array(
                'value'      => json_encode($prevValues, JSON_UNESCAPED_UNICODE),
                'updated_at' => date('Y-m-d H:i:s')
            );
            $attributes = apply_filters('ninja_tables_item_attributes', $attributes);
            do_action('ninja_table_before_update_item', $rowId, $tableId, $attributes);
            NinjaTableItem::where('id', $rowId)->update($attributes);
            do_action('ninja_table_after_update_item', $rowId, $tableId, $attributes);
        } else {
            $valuePairs = array();
            foreach ($columns as $column) {
                $columnKey              = $column['key'];
                $valuePairs[$columnKey] = (isset($values[$columnKey])) ? $values[$columnKey] : '';
            }
            $attributes = array(
                'table_id'   => $tableId,
                'attribute'  => 'value',
                'owner_id'   => get_current_user_id(),
                'value'      => json_encode($valuePairs, JSON_UNESCAPED_UNICODE),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            );
            $attributes = apply_filters('ninja_tables_item_attributes', $attributes);
            do_action('ninja_table_before_add_item', $tableId, $attributes);
            $rowId = NinjaTableItem::insertGetId($attributes);
            do_action('ninja_table_after_add_item', $rowId, $tableId, $attributes);
        }
        update_post_meta($tableId, '_last_edited_time', date('Y-m-d H:i:s'));
        if ($userId = get_current_user_id()) {
            update_post_meta($tableId, '_last_edited_by', $userId);
        }

        ninjaTablesClearTableDataCache($tableId);

        $reponseValues = json_decode($attributes['value'], true);

        if ($imageTypes) {
            foreach ($imageTypes as $imageTypeIndex => $column) {
                $reponseValues[$imageTypeIndex] = $reponseValues[$imageTypeIndex];
            }
        }

        wp_send_json_success(array(
            'message' => __('Data successfully saved', 'ninja-tables-pro'),
            'values'  => array(
                'options' => array(
                    'classes' => 'nt_row_id_' . $rowId
                ),
                'value'   => $reponseValues
            )
        ), 200);
    }

    public function getSettings()
    {
        if ( ! ninja_table_admin_role()) {
            return;
        }

        ninjaTablesValidateNonce();

        $tableId = intval($_REQUEST['table_id']);
        // check if the table is editable
        $defaultSettings = array(
            'allow_frontend'      => 'no',
            'own_data_only'       => 'no',
            'user_roles_editing'  => array(),
            'user_roles_deleting' => array()
        );

        $defaultEditPref = array(
            'editing_items'       => array(),
            'required_items'      => array(),
            'default_values'      => array(),
            'appearance_settings' => array(
                'alwaysShow'     => 'no',
                'addText'        => __('New Row', 'ninja-tables-pro'),
                'showText'       => __('Edit Rows', 'ninja-tables-pro'),
                'addModalLabel'  => __('Add Data', 'ninja-tables-pro'),
                'editModalLabel' => __('Edit Data', 'ninja-tables-pro')
            )
        );

        $settings = get_post_meta($tableId, '_ninja_table_frontedit_settings', true);
        if ( ! $settings || ! is_array($settings)) {
            $settings = $defaultSettings;
        } else {
            $settings = wp_parse_args($settings, $defaultSettings);
        }

        $editPref = get_post_meta($tableId, '_ninja_table_frontedit_pref', true);

        if ( ! $editPref || ! is_array($editPref)) {
            $editPref = $defaultEditPref;
        } else {
            $editPref = wp_parse_args($editPref, $defaultEditPref);
        }

        $formattedPref = array();
        foreach ($editPref as $prefKey => $pref) {
            if ( ! $pref) {
                $pref = (object)$pref;
            }
            $formattedPref[$prefKey] = $pref;
        }

        $userRoles = array();
        $roles     = get_editable_roles();
        foreach ($roles as $key => $role) {
            if ($key != 'administrator') {
                $userRoles[$key] = $role['name'];
            }
        }
        $editingUserRoles                     = $userRoles;
        $editingUserRoles['__public_users__'] = 'Public Users (Only add data)';

        wp_send_json_success(array(
            'settings'           => $settings,
            'editor_pref'        => $formattedPref,
            'editing_user_roles' => $editingUserRoles,
            'user_roles'         => $userRoles
        ), 200);
    }

    public function updateSettings()
    {
        if ( ! ninja_table_admin_role()) {
            return;
        }
        ninjaTablesValidateNonce();

        $tableId             = Arr::get($_REQUEST, 'table_id');
        $settings            = wp_unslash(Arr::get($_REQUEST, 'settings'));
        $editing_items       = wp_unslash(Arr::get($_REQUEST, 'editing_items'));
        $required_items      = wp_unslash(Arr::get($_REQUEST, 'required_items'));
        $default_values      = wp_unslash(Arr::get($_REQUEST, 'default_values'));
        $appearance_settings = wp_unslash(Arr::get($_REQUEST, 'appearance_settings'));
        if ($settings['allow_frontend'] == 'yes') {
            // Do Validation Here for editing
            if ( ! count($editing_items)) {
                $this->errorResponse(__('Please check which columns can be edit at frontend', 'ninja-tables-pro'), 400);

                return;
            } else {
                $fields = array_filter($editing_items, function ($val) {
                    return $val == 'yes';
                });
                if ( ! count($fields)) {
                    $this->errorResponse(__('Please check which columns can be edit at frontend', 'ninja-tables-pro'),
                        400);

                    return;
                }
            }
        }

        $editingPref = array(
            'editing_items'       => $editing_items,
            'required_items'      => $required_items,
            'default_values'      => $default_values,
            'appearance_settings' => $appearance_settings
        );

        update_post_meta($tableId, '_ninja_table_frontedit_settings', $settings);
        update_post_meta($tableId, '_ninja_table_frontedit_pref', $editingPref);


        // Assign Orphaned Data to current user as owner
        global $wpdb;
        $wpdb->query("UPDATE " . $wpdb->prefix . "ninja_table_items SET owner_id = " . get_current_user_id() . " WHERE table_id = " . $tableId . " AND owner_id IS NULL");

        wp_send_json_success(array(
            'message' => __('Settings successfully updated', 'ninja-tables-pro')
        ), 200);
    }

    public function routeDeleteRow()
    {
        ninjaTablesValidateNonce('ninja_table_public_nonce');
        $tableId  = intval($_POST['table_id']);
        $rowId    = intval($_POST['row_id']);
        $provider = ninja_table_get_data_provider($tableId);
        $this->checkRowDeletePermission($tableId, $rowId);
        do_action('ninja_table_delete_row_data_' . $provider, $rowId, $tableId);
    }

    public function deleteRowDefaultTable($rowId, $tableId)
    {
        NinjaTableItem::where('table_id', $tableId)
                      ->where('id', $rowId)
                      ->delete();
        ninjaTablesClearTableDataCache($tableId);

        update_post_meta($tableId, '_last_edited_time', date('Y-m-d H:i:s'));
        if ($userId = get_current_user_id()) {
            update_post_meta($tableId, '_last_edited_by', $userId);
        }

        wp_send_json_success(array(
            'message' => __('Data successfully deleted', 'ninja-tables-pro')
        ), 200);
    }

    private function checkRowDeletePermission($tableId, $rowId = false)
    {
        if (current_user_can('administrator')) {
            return true;
        }
        $userId   = get_current_user_id();
        $settings = get_post_meta($tableId, '_ninja_table_frontedit_settings', true);

        if ( ! $this->hasDeletePermission($tableId, $userId, $settings)) {
            $this->errorResponse(__('Sorry! You do not have permission to edit this data', 'ninja-table-pro'));

            return;
        }
        // Check The editor Permission Now
        if ($settings['own_data_only'] == 'no') {
            return true;
        }
        // Now Check if the Current Row Have
        $preRow = NinjaTableItem::where('table_id', $tableId)
                                ->where('id', $rowId)
                                ->first();

        if ($preRow && $preRow->owner_id == $userId) {
            return true;
        }
        $this->errorResponse(__('Sorry! You do not have permission to perform this action', 'ninja-table-pro'));

        return;
    }

    public function getEditingVars($tableId)
    {
        $editPermission   = false;
        $deletePermission = false;
        $currentUserId    = get_current_user_id();
        $status           = false;
        $checkEditing     = 'no';
        $settings         = get_post_meta($tableId, '_ninja_table_frontedit_settings', true);

        if (Arr::get($settings, 'allow_frontend') == 'yes') {
            $checkEditing = 'yes';
        }

        if ($checkEditing == 'yes') {
            if (current_user_can('administrator')) {
                $editPermission   = true;
                $deletePermission = true;
            } else {
                // Check If User Have Right Permission
                $editPermission   = $this->hasEditingPermission($tableId, $currentUserId, $settings);
                $deletePermission = $this->hasDeletePermission($tableId, $currentUserId, $settings);
            }
        }

        if ($editPermission || $deletePermission) {
            $status = true;
        }

        $editorLabels = $this->getEditorLabels($tableId);

        return array(
            'enabled'        => $status,
            'editing'        => $editPermission,
            'check_editing'  => $checkEditing,
            'deleting'       => $deletePermission,
            'alwaysShow'     => Arr::get($editorLabels, 'alwaysShow') == 'yes',
            'own_data_only'  => Arr::get($settings, 'own_data_only'),
            'addText'        => Arr::get($editorLabels, 'addText'),
            'hideText'       => __('Cancel', 'ninja-tables-pro'),
            'showText'       => Arr::get($editorLabels, 'showText'),
            'position'       => Arr::get($editorLabels, 'position'),
            'addModalLabel'  => Arr::get($editorLabels, 'addModalLabel'),
            'editModalLabel' => Arr::get($editorLabels, 'editModalLabel'),
            'defaultValues'  => $this->getDefaultValues($tableId)
        );
    }

    private function hasEditingPermission($tableId, $userId = false, $settings = false)
    {
        if (current_user_can('administrator')) {
            return true;
        }
        if ( ! $userId) {
            $userId = get_current_user_id();
        }
        if ( ! $settings) {
            $settings = get_post_meta($tableId, '_ninja_table_frontedit_settings', true);
        }

        if (Arr::get($settings, 'allow_frontend') != 'yes' || ! Arr::get($settings,
                'user_roles_editing')) {
            return false;
        }

        $editingRoles = Arr::get($settings, 'user_roles_editing');

        if ( ! $editingRoles) {
            return false;
        }

        foreach ($editingRoles as $role) {
            if (current_user_can($role)) {
                return true;
            }
        }

        if ( ! $userId && in_array('__public_users__', $editingRoles)) {
            return true;
        }

        return false;
    }

    private function hasDeletePermission($tableId, $userId = false, $settings = false)
    {
        if ( ! $userId) {
            $userId = get_current_user_id();
        }
        if ( ! $userId) {
            return false;
        }
        if ( ! $settings) {
            $settings = get_post_meta($tableId, '_ninja_table_frontedit_settings', true);
        }

        if ( ! $settings || ! Arr::get($settings, 'user_roles_deleting')) {
            return false;
        }

        $deletingRoles = Arr::get($settings, 'user_roles_deleting');

        foreach ($deletingRoles as $role) {
            if (current_user_can($role)) {
                return true;
            }
        }

        return false;
    }

    private function getEditorLabels($tableId)
    {
        $pref          = get_post_meta($tableId, '_ninja_table_frontedit_pref', true);
        $labelDefaults = array(
            'alwaysShow'     => 'no',
            'position'       => 'right',
            'addText'        => __('New Row', 'ninja-tables-pro'),
            'showText'       => __('Edit Rows', 'ninja-tables-pro'),
            'addModalLabel'  => __('Add Data', 'ninja-tables-pro'),
            'editModalLabel' => __('Edit Data', 'ninja-tables-pro')
        );

        if ($pref && isset($pref['appearance_settings'])) {
            $labels = wp_parse_args($pref['appearance_settings'], $labelDefaults);
        } else {
            $labels = $labelDefaults;
        }

        return $labels;
    }

    private function getDefaultValues($tableId)
    {
        $pref = get_post_meta($tableId, '_ninja_table_frontedit_pref', true);
        if ($pref && isset($pref['default_values'])) {
            return $pref['default_values'];
        }

        return array();
    }

    private function errorResponse($message, $code = 400)
    {
        wp_send_json_error(array(
            'message' => $message
        ), $code);
    }
}
