<?php

namespace ACP\ConditionalFormat\Asset;

use AC\Asset\Location\Absolute;
use AC\Asset\Script;
use AC\Table\AdminHeadStyle;
use AC\Type\Url\Documentation;
use AC\View;
use ACP\ConditionalFormat\Operators;
use ACP\ConditionalFormat\RuleCollection;

final class Table extends Script
{

    private $root_location;

    private $operators;

    private $rules;

    private $columns;

    public function __construct(
        Absolute $location,
        Operators $operators,
        RuleCollection $rules,
        array $columns
    ) {
        parent::__construct('acp-cf-table', $location->with_suffix('assets/conditional-format/js/table.js'));

        $this->root_location = $location;
        $this->operators = $operators;
        $this->rules = $rules;
        $this->columns = $columns;
    }

    public function register(): void
    {
        parent::register();

        $rules = [];

        foreach ($this->rules as $rule) {
            $rules[] = [
                'column_name' => $rule->get_column_name(),
                'format'      => (string)$rule->get_format(),
                'operator'    => $rule->get_operator(),
                'fact'        => $rule->has_fact() ? $rule->get_fact() : null,
            ];
        }

        $styles = $this->get_color_styles();

        $this->add_inline_variable('acp_cf_settings', [
            'operators'     => array_values($this->operators->get_operators()),
            'rules'         => $rules,
            'columns'       => $this->columns,
            'format_styles' => $styles,
        ]);

        $view = (new View(['styles' => $styles]))->set_template('conditional-formatting/styles');

        AdminHeadStyle::add($view->render());

        wp_localize_script($this->get_handle(), 'acp_cf_settings_i18n', [
            'between_and'            => _x('and', 'between_operator', 'codepress-admin-columns'),
            'save_apply'             => __('Save &amp; Apply', 'codepress-admin-columns'),
            'cancel'                 => __('Cancel', 'codepress-admin-columns'),
            'add_rule'               => __('Add Rule', 'codepress-admin-columns'),
            'add_another_condition'  => __('Add condition', 'codepress-admin-columns'),
            'conditional_formatting' => __('Conditional Formatting', 'codepress-admin-columns'),
            'formatting'             => __('Conditional Formatting', 'codepress-admin-columns'),
            'formatting_style'       => __('Formatting Style', 'codepress-admin-columns'),
            'documentation_link'     => $this->get_documentation_link(),
        ]);
    }

    private function get_color_styles(): array
    {
        $location = $this->root_location->with_suffix('config/color_styles.php');

        return (array)apply_filters('acp/conditional_format/formats', require $location->get_path());
    }

    private function get_documentation_link(): string
    {
        return sprintf(
            '<a href="%s" class="ac-external" target="_blank">%s</a><span class="dashicons dashicons-external"></span>',
            Documentation::create_with_path(Documentation::ARTICLE_CONDITIONAL_FORMATTING),
            __('documentation', 'codepress-admin-columns')
        );
    }

}