<?php

namespace ACP\QuickAdd\Controller;

use AC\ListScreen;
use AC\Response\Json;

class JsonResponse extends Json
{

    public function create_from_list_screen(ListScreen $list_screen, int $id): JsonResponse
    {
        $this->set_parameter('id', $id);

        if ($list_screen instanceof ListScreen\ListTable) {
            $this->set_parameter('row', $list_screen->list_table()->render_row($id));
        }

        return $this;
    }

}