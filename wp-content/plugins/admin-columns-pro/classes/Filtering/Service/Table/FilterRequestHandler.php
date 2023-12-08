<?php

declare(strict_types=1);

namespace ACP\Filtering\Service\Table;

use AC\ListScreen;
use AC\Registerable;
use AC\Request;
use ACP\Filtering\RequestHandler;
use ACP\Search\ComparisonFactory;

class FilterRequestHandler implements Registerable
{

    private $request;

    private $comparison_factory;

    public function __construct(Request $request, ComparisonFactory $comparison_factory)
    {
        $this->request = $request;
        $this->comparison_factory = $comparison_factory;
    }

    public function register(): void
    {
        add_action('ac/table/list_screen', [$this, 'handle_request']);
    }

    public function handle_request(ListScreen $list_screen): void
    {
        (new RequestHandler\Filters($list_screen, $this->comparison_factory))->handle($this->request);
    }

}