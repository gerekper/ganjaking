<?php

namespace ACP\Editing\RequestHandler;

use AC;
use AC\Request;
use AC\Response;
use ACP;
use ACP\Editing\RequestHandler;

class DeleteUserSelectValues implements RequestHandler
{

    public function handle(Request $request)
    {
        $response = new Response\Json();

        $options = (new ACP\Helper\Select\User\PaginatedFactory())->create([
            'number' => 200,
            'search' => (string)$request->get('searchterm', ''),
        ]);

        $select = new AC\Helper\Select\Response(
            $options,
            false
        );

        $response
            ->set_parameters($select())
            ->success();
    }

}