<?php

namespace ACP\RequestHandler\Ajax;

use AC\Helper\Select\Response;
use AC\Nonce;
use AC\Request;
use AC\RequestAjaxHandler;
use ACP\Helper\Select;
use ACP\Helper\Select\User\PaginatedFactory;

class ListScreenUsers implements RequestAjaxHandler
{

    public function handle(): void
    {
        $request = new Request();

        if ( ! (new Nonce\Ajax())->verify($request)) {
            wp_send_json_error();
        }

        $options = (new PaginatedFactory())->create([
            'search' => $request->get('search'),
            'paged'  => $request->get('page', 1),
            'number' => 10,
        ]);

        $has_more = ! $options->is_last_page();

        $response = new Response($options, $has_more);

        wp_send_json_success($response());
    }
}