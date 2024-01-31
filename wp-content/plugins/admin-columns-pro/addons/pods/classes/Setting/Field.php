<?php

namespace ACA\Pods\Setting;

use AC;
use AC\View;
use ACA\Pods\Column;

/**
 * @property Column $column
 */
class Field extends AC\Settings\Column
{

    /**
     * @var string
     */
    private $pods_field;

    protected function define_options()
    {
        return ['pods_field'];
    }

    public function get_dependent_settings()
    {
        return $this->column->get_field()->get_dependent_settings();
    }

    public function create_view()
    {
        $setting = $this->create_element('select');

        $no_result = sprintf(__('No %s fields available.', 'codepress-admin-columns'), __('Pods', 'pods'));
        $no_result .= ' ' . sprintf(
                __('Create your first %s field.', 'codepress-admin-columns'),
                ac_helper()->html->link($this->get_link_create_pod_field(), __('Pods', 'pods'))
            );

        $setting
            ->set_no_result($no_result)
            ->set_attribute('data-refresh', 'column')
            ->set_attribute('data-label', 'update')
            ->set_options($this->get_field_types());

        $view = new View();
        $view->set('label', __('Field', 'codepress-admin-columns'))
             ->set('setting', $setting);

        return $view;
    }

    private function get_link_create_pod_field()
    {
        return add_query_arg(['page' => 'pods-add-new'], admin_url('admin.php'));
    }

    /**
     * @return string
     */
    public function get_pods_field()
    {
        if (null === $this->pods_field) {
            $this->set_pods_field($this->get_first_pods_field());
        }

        return $this->pods_field;
    }

    /**
     * @return bool|mixed
     */
    public function get_first_pods_field()
    {
        $fields = $this->get_field_types();
        reset($fields);

        return key($fields);
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function set_pods_field($field)
    {
        $this->pods_field = $field;

        return $this;
    }

    private function get_field_types()
    {
        $options = [];

        $fields = $this->column->get_pod_fields();

        if ($fields) {
            foreach ($fields as $field) {
                if ($field['repeatable']) {
                    continue;
                }
                $options[$field['name']] = $field['label'] ?: __('empty label', 'codepress-admin-columns');
            }
        }

        natcasesort($options);

        return $options;
    }

}