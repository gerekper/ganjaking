<?php

declare(strict_types=1);

namespace ACP\Search\RequestHandler;

use AC\ListScreen;
use AC\Request;
use AC\Table\TableFormView;
use ACP\Search;
use ACP\Sorting;

/**
 * Fill the $_GET and $_REQUEST params with the preferred segment query parameters.
 */
class RequestSetter
{

    use Search\DefaultSegmentTrait;

    private $list_screen;

    public function __construct(ListScreen $list_screen, Search\SegmentRepository\Database $segment_repository)
    {
        $this->list_screen = $list_screen;
        $this->segment_repository = $segment_repository;
    }

    public function handle(Request $request): void
    {
        // Ignore when switching to another segment or when the filter form is submitted.
        if ($request->filter('ac-segment') || null !== $request->get(TableFormView::PARAM_ACTION)) {
            return;
        }

        $segment = $this->get_default_segment($this->list_screen);

        if ( ! $segment) {
            return;
        }

        $params = $segment->get_url_parameters();

        $ignored_params = [
            Sorting\NativeSortable\Request\Sort::PARAM_ORDERBY,
            Sorting\NativeSortable\Request\Sort::PARAM_ORDER,
            'layout',
            'ac-rules',
            'ac-rules-raw',
        ];

        foreach ($params as $key => $value) {
            if (in_array($key, $ignored_params, true)) {
                continue;
            }

            if (isset($_GET[$key], $_REQUEST[$key])) {
                continue;
            }

            $_REQUEST[$key] = $_GET[$key] = $value;
        }
    }

}