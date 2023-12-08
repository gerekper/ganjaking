<?php

namespace ACP\RequestHandler\Ajax;

use AC\Capabilities;
use AC\ColumnSize;
use AC\Nonce;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Storage;
use AC\Type\ListScreenId;
use LogicException;

class ListScreenTable implements RequestAjaxHandler
{

    /**
     * @var Storage\ListColumnOrder
     */
    private $order_list_storage;

    /**
     * @var Storage\UserColumnOrder
     */
    private $order_user_storage;

    /**
     * @var ColumnSize\ListStorage
     */
    private $size_list_storage;

    /**
     * @var ColumnSize\UserStorage
     */
    private $size_user_storage;

    public function __construct(
        Storage\ListColumnOrder $order_list_storage,
        Storage\UserColumnOrder $order_user_storage,
        ColumnSize\ListStorage $size_list_storage,
        ColumnSize\UserStorage $size_user_storage
    ) {
        $this->order_list_storage = $order_list_storage;
        $this->order_user_storage = $order_user_storage;
        $this->size_list_storage = $size_list_storage;
        $this->size_user_storage = $size_user_storage;
    }

    public function handle(): void
    {
        if ( ! current_user_can(Capabilities::MANAGE)) {
            return;
        }

        $request = new Request();

        if ( ! (new Nonce\Ajax())->verify($request)) {
            wp_send_json_error();
        }

        try {
            $id = new ListScreenId($request->get('list_id'));
        } catch (LogicException $e) {
            wp_send_json_error();
        }

        $this->set_current_user_column_sizes_as_the_default($id);
        $this->set_current_user_column_orders_as_the_default($id);

        wp_send_json_success();
    }

    private function set_current_user_column_sizes_as_the_default(ListScreenId $id): void
    {
        $sizes = $this->size_user_storage->get_all($id);

        if ( ! $sizes) {
            return;
        }

        foreach ($sizes as $column_name => $width) {
            $this->size_list_storage->save($id, (string)$column_name, $width);
        }

        $this->size_user_storage->delete_by_list_id($id);
    }

    private function set_current_user_column_orders_as_the_default(ListScreenId $id): void
    {
        $column_names = $this->order_user_storage->get($id);

        if ( ! $column_names) {
            return;
        }

        $this->order_list_storage->save($id, $column_names);
        $this->order_user_storage->delete($id);
    }

}