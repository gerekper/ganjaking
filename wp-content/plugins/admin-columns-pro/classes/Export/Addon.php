<?php

namespace ACP\Export;

use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use AC\RequestAjaxHandlers;
use AC\RequestAjaxParser;
use AC\Services;

class Addon implements Registerable
{

    private $location;

    private $list_screen_repository;

    public function __construct(
        Location\Absolute $location,
        Storage $list_screen_repository
    ) {
        $this->location = $location;
        $this->list_screen_repository = $list_screen_repository;
    }

    public function register(): void
    {
        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        $request_ajax_handlers = new RequestAjaxHandlers();
        $request_ajax_handlers->add(
            'acp-export-file-name',
            new RequestHandler\Ajax\FileName($this->list_screen_repository)
        );
        $request_ajax_handlers->add(
            'acp-export-order-preference',
            new RequestHandler\Ajax\SaveExportPreference()
        );
        $request_ajax_handlers->add(
            'acp-export-show-export-button',
            new RequestHandler\Ajax\ToggleExportButtonTable()
        );

        return new Services([
            new Admin(),
            new RequestAjaxParser($request_ajax_handlers),
            new Settings($this->location),
            new TableScreen($this->location),
        ]);
    }

}