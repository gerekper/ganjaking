<?php

namespace ACP\Sorting\Controller;

use AC;
use ACP\Sorting\ApplyFilter;
use ACP\Sorting\NativeSortable\Request\Sort;
use ACP\Sorting\Settings\ListScreen\PreferredSort;
use ACP\Sorting\Type\SortType;
use ACP\Sorting\UserPreference;

/**
 * When you revisit a page, set the orderby variable so WordPress prints the columns headers properly
 */
class RequestSetterHandler
{

    private const QUERY_PARAM_MODE = 'mode';

    /**
     * @var UserPreference\SortType
     */
    private $user_preference;

    /**
     * @var PreferredSort
     */
    private $setting_sort_default;

    /**
     * @var ApplyFilter\DefaultSort
     */
    private $default_sort_filter;

    public function __construct(
        UserPreference\SortType $user_preference,
        PreferredSort $setting_sort_default,
        ApplyFilter\DefaultSort $default_sort_filter
    ) {
        $this->user_preference = $user_preference;
        $this->setting_sort_default = $setting_sort_default;
        $this->default_sort_filter = $default_sort_filter;
    }

    private function get_request_sort_type(): ?SortType
    {
        $request = Sort::create_from_globals();

        if ( ! $request->get_order_by()) {
            return null;
        }

        return SortType::create_by_request($request);
    }

    public function handle(): void
    {
        $request = new AC\Request();

        if ($request->get(Sort::PARAM_ORDERBY)) {
            return;
        }

        // Ignore media grid
        if ('grid' === $request->get(self::QUERY_PARAM_MODE)) {
            return;
        }

        $sort_type = $this->get_request_sort_type();

        if ( ! $sort_type) {
            $sort_type = $this->user_preference->get();
        }

        if ( ! $sort_type) {
            $sort_type = $this->setting_sort_default->get();
        }

        $sort_type = $this->default_sort_filter->apply_filters($sort_type);

        if ( ! $sort_type) {
            return;
        }

        // Set $_GET and $_REQUEST (used by WP_User_Query)
        $_REQUEST[Sort::PARAM_ORDERBY] = $_GET[Sort::PARAM_ORDERBY] = $sort_type->get_order_by();
        $_REQUEST[Sort::PARAM_ORDER] = $_GET[Sort::PARAM_ORDER] = $sort_type->get_order();
    }

}