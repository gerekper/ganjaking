<?php

namespace ACP\Search\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;
use AC\Capabilities;
use AC\Helper\Select\Option;
use AC\ListScreen;
use AC\Request;
use ACP\Search\Comparison\RemoteValues;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Comparison\Values;
use ACP\Search\ComparisonFactory;
use ACP\Search\Settings\HideOnScreen;
use ACP\Search\Type\SegmentKey;

final class Table extends Script
{

    protected $filters;

    protected $request;

    protected $list_screen;

    protected $segment_key;

    public function __construct(
        string $handle,
        Location $location,
        array $filters,
        Request $request,
        ListScreen $list_screen,
        SegmentKey $segment_key = null
    ) {
        parent::__construct($handle, $location, ['wp-pointer']);

        $this->filters = $filters;
        $this->request = $request;
        $this->list_screen = $list_screen;
        $this->segment_key = $segment_key;
    }

    private function get_current_segment(): ?SegmentKey
    {
        $segment_key = $this->segment_key;
        $request_segment_key = $this->request->get('ac-segment');

        if ($request_segment_key) {
            $segment_key = new SegmentKey($request_segment_key);
        }

        return $segment_key;
    }

    private function get_rules(): ?array
    {
        $rules_raw = $this->request->get('ac-rules-raw');

        if ( ! $rules_raw) {
            return null;
        }

        $rules = json_decode($rules_raw, true);

        foreach ($rules['rules'] as $key => $rule) {
            $column = $this->list_screen->get_column_by_name($rule['id']);

            if ( ! $column) {
                continue;
            }

            $comparison = (new ComparisonFactory())->create($column);

            if ( ! $comparison) {
                continue;
            }

            if (
                ($comparison instanceof RemoteValues || $comparison instanceof SearchableValues)
                && $rule['value'] && ! is_array($rule['value'])) {
                $rules['rules'][$key]['formatted_value'] = $comparison->format_label($rule['value']);
            }

            if ($comparison instanceof Values) {
                /** @var Option $option */
                foreach ($comparison->get_values()->get_copy() as $option) {
                    if ((string)$option->get_value() === $rule['value']) {
                        $rules['rules'][$key]['formatted_value'] = $option->get_label();
                        break;
                    }
                }
            }
        }

        return $rules;
    }

    public function register(): void
    {
        parent::register();

        wp_localize_script('aca-search-table', 'acp_search', [
            'current_segment' => (string)$this->get_current_segment(),
            'rules'           => $this->get_rules(),
            'filters'         => $this->filters,
            'sorting'         => [
                'orderby' => $_GET['orderby'] ?? null,
                'order'   => $_GET['order'] ?? null,
            ],
            'segments'        => [
                'enabled'    => ! (new HideOnScreen\SavedFilters())->is_hidden($this->list_screen),
                'can_manage' => current_user_can(Capabilities::MANAGE),
            ],
        ]);

        wp_localize_script('aca-search-table', 'acp_search_i18n', [
            'select'              => _x('Select', 'select placeholder', 'codepress-admin-columns'),
            'add_filter'          => __('Add Filter', 'codepress-admin-columns'),
            'days_ago'            => __('days ago', 'codepress-admin-columns'),
            'days'                => __('days', 'codepress-admin-columns'),
            'shared_segment'      => __('Available to all users', 'codepress-admin-columns'),
            'more_search_records' => __('Please enter more characters to narrow down the search results'),
            'no_search_results'   => __('No options found', 'codepress-admin-columns'),
            'clear_filters'       => __('Clear filters', 'codepress-admin-columns'),
            'segments'            => [
                'search_segments' => __('Type to search', 'codepress-admin-columns'),
                'save_filters'    => __('Save Filters', 'codepress-admin-columns'),
                'public_filters'  => __('Public', 'codepress-admin-columns'),
                'name'            => __('Name', 'codepress-admin-columns'),
                'cancel'          => __('Cancel', 'codepress-admin-columns'),
                'save'            => __('Save', 'codepress-admin-columns'),
                'instructions'    => __('Instructions', 'codepress-admin-columns'),
            ],
        ]);
    }

}