<?php

declare(strict_types=1);

namespace ACP\Export\RequestHandler\Ajax;

use AC\Nonce;
use AC\Request;
use AC\RequestAjaxHandler;
use AC\Type\ListScreenId;
use ACP\Export\ColumnStateCollection;
use ACP\Export\Repository\UserColumnStateRepository;
use ACP\Export\Type\ColumnState;
use Exception;

class SaveExportPreference implements RequestAjaxHandler
{

    private $state_repository;

    public function __construct()
    {
        $this->state_repository = new UserColumnStateRepository();
    }

    public function handle(): void
    {
        $request = new Request();

        if ( ! (new Nonce\Ajax())->verify($request)) {
            wp_send_json_error('invalid nonce');
        }

        $id = (string)$request->filter('list_id', null, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $data = (string)$request->filter('data');

        if ( ! $data || ! ListScreenId::is_valid_id($id)) {
            wp_send_json_error();
        }

        try {
            $this->state_repository->save(
                new ListScreenId($id),
                $this->get_column_states($data)
            );
        } catch (Exception $e) {
            wp_send_json_error();
        }

        wp_send_json_success();
    }

    private function get_column_states(string $data): ColumnStateCollection
    {
        $collection = new ColumnStateCollection();
        foreach (json_decode($data, true) as $item) {
            $collection->add(new ColumnState($item['column_name'], $item['active']));
        }

        return $collection;
    }

}