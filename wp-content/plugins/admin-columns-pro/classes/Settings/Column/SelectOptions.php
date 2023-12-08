<?php

namespace ACP\Settings\Column;

use AC;
use AC\View;
use ACP\ApplyFilter;

class SelectOptions extends AC\Settings\Column implements AC\Settings\FormatValue
{

    /**
     * @var string
     */
    private $select_options;

    protected function define_options()
    {
        return [
            'select_options' => '',
        ];
    }

    public function create_view(): View
    {
        $view = new View([
            'label'   => __('Select Options', 'codepress-admin-columns'),
            'setting' => $this->create_element('input', $this->get_name()) .
                         '<div data-component="ac-select-options"></div>',
        ]);

        return $view;
    }

    public function format($value, $original_value)
    {
        $options = $this->get_options();

        return array_key_exists($value, $options)
            ? $options[$value]
            : $value;
    }

    public function get_select_options(): ?string
    {
        return $this->select_options;
    }

    public function get_options(): array
    {
        $options = [];

        $array = json_decode((string)$this->get_select_options());

        if ($array && is_array($array)) {
            foreach ($array as $option) {
                $options[$option->value] = $option->label ?: $option->value;
            }
        }

        return (new ApplyFilter\SelectOptions($this->column))->apply_filters($options);
    }

    public function set_select_options($select_options): void
    {
        $this->select_options = $select_options;
    }

}