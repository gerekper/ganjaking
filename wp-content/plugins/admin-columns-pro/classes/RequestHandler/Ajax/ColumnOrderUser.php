<?php

namespace ACP\RequestHandler\Ajax;

use AC\Nonce;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Storage\UserColumnOrder;
use AC\Type\ListScreenId;
use LogicException;

class ColumnOrderUser implements RequestAjaxHandler
{

    /**
     * @var UserColumnOrder
     */
    private $user_storage;

    public function __construct(UserColumnOrder $user_storage)
    {
        $this->user_storage = $user_storage;
    }

    public function handle(): void
    {
        $request = new Request();

        if ( ! (new Nonce\Ajax())->verify($request)) {
            wp_send_json_error();
        }

        try {
            $id = new ListScreenId($request->get('list_id'));
        } catch (LogicException $e) {
            wp_send_json_error();
        }

        $column_names = $request->get('column_names');

        if ( ! $column_names) {
            wp_send_json_error();
        }

        $this->user_storage->save($id, $column_names);

        wp_send_json_success();
    }

}