<?php

namespace ACP\RequestHandler\Ajax;

use AC\Nonce;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Storage\TableListOrder;

class ListScreenOrderUser implements RequestAjaxHandler
{

    private $preference_user;

    public function __construct(TableListOrder $preference_user)
    {
        $this->preference_user = $preference_user;
    }

    public function handle(): void
    {
        $request = new Request();

        if ( ! (new Nonce\Ajax())->verify($request)) {
            wp_send_json_error();
        }

        $list_screen_key = $request->get('list_screen');
        $order = $request->filter('order', [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if ( ! $order || ! $list_screen_key) {
            wp_send_json_error();
        }

        $this->preference_user->set($list_screen_key, $order);

        wp_send_json_success();
    }

}