<?php

declare(strict_types=1);

namespace ACP\Search\Asset\Script;

use AC;
use AC\Asset\Location;
use AC\Asset\Script;
use AC\Capabilities;
use AC\Request;
use ACP\Search\Type\SegmentKey;

final class Table extends Script
{

    protected $filters;

    protected $request;

    protected $segment_key;

    public function __construct(
        string $handle,
        Location $location,
        array $filters,
        Request $request,
        SegmentKey $segment_key = null
    ) {
        parent::__construct($handle, $location, ['wp-pointer']);

        $this->filters = $filters;
        $this->request = $request;
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

    public function register(): void
    {
        parent::register();

        $current_segment = $this->get_current_segment();
        $rules = $this->request->get('ac-rules-raw');

        wp_localize_script('aca-search-table', 'ac_search', [
            'current_segment' => $current_segment ? (string)$current_segment : null,
            'rules'           => $rules ? json_decode($rules) : null,
            'filters'         => $this->filters,
            'sorting'         => [
                'orderby' => $_GET['orderby'] ?? null,
                'order'   => $_GET['order'] ?? null,
            ],
            'segments'        => [
                'can_manage' => current_user_can(AC\Capabilities::MANAGE),
            ],
            'i18n'            => [
                'select'         => _x('Select', 'select placeholder', 'codepress-admin-columns'),
                'add_filter'     => __('Add Filter', 'codepress-admin-columns'),
                'days_ago'       => __('days ago', 'codepress-admin-columns'),
                'days'           => __('days', 'codepress-admin-columns'),
                'shared_segment' => __('Available to all users', 'codepress-admin-columns'),
                'clear_filters'  => __('Clear filters', 'codepress-admin-columns'),
                'segments'       => [
                    'save_filters'   => __('Save Filters', 'codepress-admin-columns'),
                    'public_filters' => __('Public', 'codepress-admin-columns'),
                    'name'           => __('Name', 'codepress-admin-columns'),
                    'cancel'         => __('Cancel', 'codepress-admin-columns'),
                    'save'           => __('Save', 'codepress-admin-columns'),
                    'instructions'   => __('Instructions', 'codepress-admin-columns'),
                ],
            ],
            'capabilities'    => [
                'user_can_manage_shared_segments' => current_user_can(Capabilities::MANAGE),
            ],
        ]);
    }

}