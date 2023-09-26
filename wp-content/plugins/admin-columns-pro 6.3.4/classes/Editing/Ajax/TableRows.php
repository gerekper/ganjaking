<?php

namespace ACP\Editing\Ajax;

use AC;
use AC\Registerable;
use AC\Request;
use AC\Response;

abstract class TableRows implements Registerable
{

    protected $request;

    protected $list_screen;

    public function __construct(Request $request, AC\ListScreen $list_screen)
    {
        $this->request = $request;
        $this->list_screen = $list_screen;
    }

    public function is_request(): bool
    {
        return $this->request->get('ac_action') === 'get_table_rows';
    }

    public function handle_request(): void
    {
        check_ajax_referer('ac-ajax');

        $ids = $this->request->filter('ac_ids', [], FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);

        $response = new Response\Json();

        if ( ! $ids) {
            $response->error();
        }

        if ($this->list_screen instanceof AC\ListScreen\ListTable) {
            $rows = [];

            foreach ($ids as $id) {
                $rows[$id] = $this->list_screen->list_table()->render_row($id);
            }

            $response->set_parameter('table_rows', $rows);
        }

        $response->success();
    }

}