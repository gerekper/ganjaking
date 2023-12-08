<?php

declare(strict_types=1);

namespace ACP\Search\Middleware;

use AC;
use AC\Middleware;
use AC\Table\TableFormView;
use ACP\Search\DefaultSegmentTrait;
use ACP\Search\SegmentRepository;

final class Segment implements Middleware
{

    use DefaultSegmentTrait;

    private $list_screen;

    public function __construct(AC\ListScreen $list_screen, SegmentRepository\Database $segment_repository)
    {
        $this->list_screen = $list_screen;
        $this->segment_repository = $segment_repository;
    }

    public function handle(AC\Request $request): void
    {
        $rules_key = 'rules';

        if ($request->get_method() === AC\Request::METHOD_GET) {
            $rules_key = 'ac-' . $rules_key;
        }

        // If rules request is empty and action form is not submitted, insert the preferred segment into the request
        if ($request->get($rules_key)) {
            return;
        }

        if (null !== $request->get(TableFormView::PARAM_ACTION)) {
            return;
        }

        $segment = $this->get_default_segment($this->list_screen);

        if ( ! $segment) {
            return;
        }

        $url_parameters = $segment->get_url_parameters();

        if (isset($url_parameters['ac-rules'])) {
            $request->get_parameters()->merge([
                $rules_key => $url_parameters['ac-rules'],
            ]);
        }

        if (isset($url_parameters['acp_filter'])) {
            $request->get_parameters()->merge([
                'acp_filter' => $url_parameters['acp_filter'],
            ]);
        }
    }

}