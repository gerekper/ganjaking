<?php

namespace NinjaTable\FrontEnd\DataProviders;

use NinjaTables\Classes\ArrayHelper;

class FluentFormProvider
{
    public function boot()
    {
        add_action('wp_ajax_ninja_tables_get-fluentform-forms', array($this, 'getFluentformForms'));
        add_action('wp_ajax_ninja-tables_get-fluentform-fields', array($this, 'getFluentformFields'));
        add_action('wp_ajax_ninja_tables_save_fluentform_table', array($this, 'saveTable'), 10, 1);

        add_filter('ninja_tables_get_table_fluent-form', array($this, 'getTableSettings'));
        add_filter('ninja_tables_get_table_data_fluent-form', array($this, 'getTableData'), 10, 4);
        add_filter('ninja_tables_fetching_table_rows_fluent-form', array($this, 'data'), 10, 5);
    }

    // TODO: Refactoring required.
    // Must use exposed API from fluentform.
    public function getFluentformForms()
    {
        if(!current_user_can(ninja_table_admin_role())) {
            return;
        }
        if (function_exists('wpFluentForm')) {
            $forms = wpFluent()->table('fluentform_forms')->select(array('id', 'title'))->get();
            wp_send_json_success($forms, 200);
            wp_die();
        }
    }

    // TODO: Refactoring required.
    // Must use exposed API from fluentform.
    public function getFluentformFields()
    {
        if(!current_user_can(ninja_table_admin_role())) {
            return;
        }

        $form = wpFluentForm('FluentForm\App\Modules\Form\Form');
        $formFieldParser = wpFluentForm('FluentForm\App\Modules\Form\FormFieldsParser');

        // Default meta data fields.
        $labels = [
            ['name' => 'id', 'label' => 'ID'],
            ['name' => 'serial_number', 'label' => 'Serial Number'],
            ['name' => 'status', 'label' => 'Status']
        ];

        $form = $form->fetchForm(intval($_REQUEST['form_Id']));
        $inputs = $formFieldParser->getEntryInputs($form);
        foreach ($formFieldParser->getAdminLabels($form, $inputs) as $key => $value) {
            $labels[] = array('name' => $key, 'label' => $value);
        }

        wp_send_json_success($labels, 200);
    }

    public function saveTable()
    {
        if(!current_user_can(ninja_table_admin_role())) {
            return;
        }

        $messages = array();
        $tableId = $_REQUEST['table_Id'];
        $formId = $_REQUEST['form']['id'];

        if (!$tableId) {
            // Validate Title
            if (empty($_REQUEST['post_title'])) {
                $messages['title'] = __('The title field is required.', 'ninja-tables');
            }
        }

        // Validate Columns
        $fields = isset($_REQUEST['form']['fields']) ? $_REQUEST['form']['fields'] : array();
        if (!($fields = ninja_tables_sanitize_array($fields))) {
            $messages['fields'] = __('No fields were selected.', 'ninja-tables');
        }

        // If Validation failed
        if (array_filter($messages)) {
            wp_send_json_error(array('message' => $messages), 422);
            wp_die();
        }

        $columns = array();
        foreach ($fields as $field) {
            $columns[] = array(
                'name' => $field['label'],
                'key' => $field['name'],
                'breakpoints' => null,
                'data_type' => 'text',
                'dateFormat' => null,
                'header_html_content' => null,
                'enable_html_content' => false,
                'contentAlign' => null,
                'textAlign' => null,
                'original_name' => $field['name']
            );
        }

        if ($tableId) {
            $oldColumns = get_post_meta($tableId, '_ninja_table_columns', true);
            foreach ($columns as $key => $newColumn) {
                foreach ($oldColumns as $oldColumn) {
                    if ($oldColumn['original_name'] == $newColumn['original_name']) {
                        $columns[$key] = $oldColumn;
                    }
                }
            }

            // Reset/Reorder array indices
            $columns = array_values($columns);
        } else {
            $tableId = $this->saveOrCreateTable();
        }

        update_post_meta($tableId, '_ninja_table_columns', $columns);
        update_post_meta($tableId, '_ninja_tables_data_provider', 'fluent-form');
        update_post_meta($tableId, '_ninja_tables_data_provider_ff_form_id', $formId);

        if(isset($_REQUEST['form']['current_user_entry_only'])) {
            update_post_meta($tableId, '_ninja_tables_ff_own_submission_only', sanitize_text_field($_REQUEST['form']['current_user_entry_only']));
        }

        update_post_meta(
            $tableId, '_ninja_tables_data_provider_ff_entry_limit', $_REQUEST['form']['entry_limit']
        );

        update_post_meta(
            $tableId, '_ninja_tables_data_provider_ff_entry_status', $_REQUEST['form']['entry_status']
        );

        wp_send_json_success(array('table_id' => $tableId, 'form_id' => $formId));
    }

    public function getTableSettings($table)
    {

        $table->isEditable = false;
        $table->dataSourceType = 'fluent-form';
        $table->isEditableMessage = 'You may edit your table settings here.';
        $table->fluentFormFormId = get_post_meta(
            $table->ID, '_ninja_tables_data_provider_ff_form_id', true
        );
        $table->entry_limit = get_post_meta(
            $table->ID, '_ninja_tables_data_provider_ff_entry_limit', true
        );
        $table->entry_status = get_post_meta(
            $table->ID, '_ninja_tables_data_provider_ff_entry_status', true
        );

        $table->current_user_entry_only = get_post_meta($table->ID, '_ninja_tables_ff_own_submission_only', true);

        $table->isExportable = true;
        $table->isImportable = false;
        $table->isCreatedSortable = true;
        $table->isSortable = false;
        $table->hasCacheFeature = false;
        return $table;
    }

    public function getTableData($data, $tableId, $perPage = -1, $offset = 0)
    {
        if (function_exists('wpFluentForm')) {

            // we need this short-circuite to overwrite fluentform entry permissions
            add_filter('fluentform_verify_user_permission_fluentform_entries_viewer', array($this, 'addEntryPermission'));

            $formId = get_post_meta($tableId, '_ninja_tables_data_provider_ff_form_id', true);
            $entries = wpFluentForm('FluentForm\App\Modules\Entries\Entries')->_getEntries(
                intval($formId),
                isset($_GET['page']) ? intval($_GET['page']) : 1,
                intval($perPage),
                $this->getOrderBy($tableId),
                'all',
                null
            );

            // removing this short-circuite to overwrite fluentform entry permissions
            remove_filter('fluentform_verify_user_permission_fluentform_entries_viewer', array($this, 'addEntryPermission'));

            $columns = $this->getTableColumns($tableId);

            $formattedEntries = array();
            foreach ($entries['submissions']['data'] as $key => $value) {
                // Prepare the entry with the selected columns.
                $value->user_inputs = $this->prepareEntry($value, $columns);

                $formattedEntries[] = array(
                    'id' => $value->id,
                    'position' => $key,
                    'values' => $value->user_inputs
                );
            }

            return array(
                $formattedEntries,
                $entries['submissions']['paginate']['total']
            );
        }

        return $data;
    }

    public function data( $data, $tableId, $defaultSorting, $limitEntries = false, $skip = false )
    {
        if (!function_exists('wpFluentForm')) {
            return $data;
        }

        add_filter('fluentform_verify_user_permission_fluentform_entries_viewer', array($this, 'addEntryPermission'));

        $formId = get_post_meta($tableId, '_ninja_tables_data_provider_ff_form_id', true);
        $status = get_post_meta($tableId, '_ninja_tables_data_provider_ff_entry_status', true);

        $limit = null;

        if($limitEntries || $skip) {
            $limit = intval($limitEntries) + intval($skip);
        }

        if(!$limit) {
            $limit = (int) get_post_meta($tableId, '_ninja_tables_data_provider_ff_entry_limit', true);
        }

        $entryStatus = apply_filters(
            'ninja_tables_fluentform_entry_status', $status, $tableId, $formId
        );

        $entryLimit = apply_filters(
            'ninja_tables_fluentform_per_page', ($limit ? $limit : -1), $tableId, $formId
        );

        $orderBy = apply_filters(
            'ninja_tables_fluentform_order_by', $this->getOrderBy($tableId), $tableId, $formId
        );

        $ownSubmissionOnly = get_post_meta($tableId, '_ninja_tables_ff_own_submission_only', true);
        $wheres = array();
        if($ownSubmissionOnly == 'yes') {
            $userId = get_current_user_id();
            if(!$userId) {
                return $data;
            }
            $wheres = array(
                array('user_id', $userId)
            );
        }

        $entries = wpFluentForm('FluentForm\App\Modules\Entries\Entries')->_getEntries(
            intval($formId), -1, $entryLimit, $orderBy, $entryStatus, null, $wheres
        );

        if($skip && isset($entries['submissions']['data'])) {
            $entries['submissions']['data'] = array_slice($entries['submissions']['data'], $skip, $limitEntries);
        }

        remove_filter('fluentform_verify_user_permission_fluentform_entries_viewer', array($this, 'addEntryPermission'));

        $columns = $this->getTableColumns($tableId);

        foreach ($entries['submissions']['data'] as $key => $value) {
            // Prepare the entry with the selected columns.
            $data[] = $this->prepareEntry($value, $columns);
        }

        $data = apply_filters('ninja_tables_fluentform_all_entries', $data, $entries['submissions']['data'], $columns, $tableId);
        return $data;
    }

    private function saveOrCreateTable($postId = null)
    {

        if(!current_user_can(ninja_table_admin_role())) {
            return;
        }

        $attributes = array(
            'post_title' => sanitize_text_field($_REQUEST['post_title']),
            'post_content' => wp_kses_post($_REQUEST['post_content']),
            'post_type' => 'ninja-table',
            'post_status' => 'publish'
        );
        if (!$postId) {
            $postId = wp_insert_post($attributes);
        } else {
            $attributes['ID'] = $postId;
            wp_update_post($attributes);
        }

        return $postId;
    }

    private function getOrderBy($tableId)
    {
        $tableSettings = get_post_meta($tableId, '_ninja_table_settings', true);
        if(ArrayHelper::get($tableSettings, 'default_sorting') == 'old_first') {
            return 'ASC';
        } else {
            return 'DESC';
        }
    }

    public function addEntryPermission() {
        return true;
    }

    /**
     * Prepare the entry with the selected columns.
     *
     * @param  $entry
     * @param  array $columns
     * @return array
     */
    private function prepareEntry($entry, $columns = [])
    {
        $entry->user_inputs = $this->addEntryMeta($entry, $columns);

        return array_intersect_key(
            $entry->user_inputs, array_combine($columns, $columns)
        );
    }

    /**
     * Add available meta data to the entry.
     *
     * @param  $value
     * @param  array $columns
     * @return array
     */
    private function addEntryMeta($value, $columns = [])
    {
        return array_merge($value->user_inputs, array_intersect_key(
            [
                'id'            => $value->id,
                'serial_number' => $value->serial_number,
                'status'        => $value->status
            ],
            array_combine($columns, $columns)
        ));
    }

    /**
     * Get the table columns extracted from the column settings.
     *
     * @param  $tableId
     * @return array
     */
    private function getTableColumns($tableId)
    {
        return array_map(function($column) {
            return $column['original_name'];
        }, get_post_meta($tableId, '_ninja_table_columns', true));
    }
}
