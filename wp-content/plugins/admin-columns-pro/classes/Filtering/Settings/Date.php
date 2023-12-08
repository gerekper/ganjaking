<?php

namespace ACP\Filtering\Settings;

use AC\Column;
use AC\View;
use ACP\Filtering\Settings;

class Date extends Settings
{

    /**
     * @var string
     * Options: range, daily, monthly, yearly, exact_match, future, past
     */
    private $filter_format;

    private $exclude_options;

    public function __construct(Column $column, array $exclude_options = null)
    {
        parent::__construct($column);

        $this->exclude_options = $exclude_options;
    }

    protected function define_options()
    {
        $options = parent::define_options();

        $options['filter_format'] = ''; // default empty

        return $options;
    }

    public function create_view()
    {
        $view = parent::create_view();

        $options = [
            ''            => __('Daily', 'codepress-admin-columns'),
            'monthly'     => __('Monthly', 'codepress-admin-columns'),
            'yearly'      => __('Yearly', 'codepress-admin-columns'),
            'future_past' => __('Future / Past', 'codepress-admin-columns'),
            'range'       => __('Range', 'codepress-admin-columns'),
        ];

        if ($this->exclude_options) {
            $options = array_diff_key($options, array_flip($this->exclude_options));
        }

        $filter_format = $this->create_element('select', 'filter_format')->set_options($options);

        $format_view = new View();
        $format_view->set('label', __('Filter by', 'codepress-admin-columns'))
                    ->set('tooltip', __('This will allow you to set the filter format.', 'codepress-admin-columns'))
                    ->set('setting', $filter_format)
                    ->set('for', $filter_format->get_id());

        $sections = $view->get('sections');
        $sections[] = $format_view;

        $view->set('sections', $sections);

        return $view;
    }

    /**
     * @return string
     */
    public function get_filter_format()
    {
        return $this->filter_format;
    }

    /**
     * @param string $filter_format
     *
     * @return $this
     */
    public function set_filter_format($filter_format)
    {
        $this->filter_format = $filter_format;

        return $this;
    }

}