<?php

declare(strict_types=1);

namespace ACP\Editing\BulkDelete;

use AC\Request;
use AC\Response;
use ACP;
use Exception;

abstract class RequestHandler implements ACP\Editing\RequestHandler
{

    private const STATUS_FAILED = 'failed';
    private const STATUS_SUCCESS = 'success';

    abstract protected function delete($id, array $args = []): void;

    public function handle(Request $request)
    {
        $response = new Response\Json();

        $ids = $request->filter('ids', false, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);

        if ($ids === false) {
            $response->error();
        }

        $args = $request->get('arguments', []);

        $results = [];

        foreach ($ids as $id) {
            $error = null;
            $status = self::STATUS_SUCCESS;

            try {
                $this->delete($id, $args);
            } catch (Exception $e) {
                $error = $e->getMessage();
                $status = self::STATUS_FAILED;
            }

            $results[] = [
                'id'     => $id,
                'error'  => $error,
                'status' => $status,
            ];
        }

        $response
            ->set_parameter('results', $results)
            ->set_parameter('total', count($results))
            ->success();
    }

}