<?php

namespace ACP\Editing\RequestHandler;

use AC\Column;
use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Editing\ApplyFilter;
use ACP\Editing\Editable;
use ACP\Editing\ListScreen;
use ACP\Editing\Model;
use ACP\Editing\RequestHandler;
use ACP\Editing\RequestHandler\Exception\InvalidUserPermissionException;
use ACP\Editing\RequestHandler\Exception\NotEditableException;
use ACP\Editing\Service;
use ACP\Editing\Service\Editability;
use ACP\Editing\Strategy;
use RuntimeException;

class BulkSave implements RequestHandler
{

    private const SAVE_FAILED = 'failed';
    private const SAVE_SUCCESS = 'success';
    private const SAVE_NOTICE = 'not_editable';

    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function handle(Request $request)
    {
        $response = new Response\Json();

        $ids = $request->filter('ids', false, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        $form_data = $request->get('value', false);

        if ($ids === false || $form_data === false) {
            $response->error();
        }

        $list_id = $request->get('layout');

        if ( ! ListScreenId::is_valid_id($list_id)) {
            $response->error();
        }

        $list_screen = $this->storage->find(new ListScreenId($list_id));

        if ( ! $list_screen || ! $list_screen->is_user_allowed(wp_get_current_user())) {
            $response->error();
        }

        if ( ! $list_screen instanceof ListScreen) {
            $response->error();
        }

        $strategy = $list_screen->editing();

        if ( ! $strategy) {
            $response->error();
        }

        if ( ! $strategy->user_can_edit()) {
            $response->error();
        }

        $column = $list_screen->get_column_by_name($request->get('column'));

        if ( ! $column instanceof Editable) {
            $response->error();
        }

        $service = $column->editing();

        if ( ! $service) {
            $response->error();
        }

        $results = [];

        foreach ($ids as $id) {
            $error = null;

            try {
                $this->save($id, $form_data, $strategy, $service, $column);
                $status = self::SAVE_SUCCESS;
            } catch (NotEditableException|InvalidUserPermissionException $e) {
                $error = $e->getMessage();
                $status = self::SAVE_NOTICE;
            } catch (RuntimeException $e) {
                $error = $e->getMessage();
                $status = self::SAVE_FAILED;
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

    /**
     * @param int      $id
     * @param mixed    $form_data
     * @param Strategy $strategy
     * @param Service  $service
     * @param Column   $column
     *
     * @return void
     */
    private function save($id, $form_data, Strategy $strategy, Service $service, Column $column)
    {
        $id = (int)$id;

        if ( ! $id) {
            throw new RuntimeException(__('Missing id', 'codepress-admin-columns'));
        }

        if ( ! $strategy->user_can_edit_item($id)) {
            throw new InvalidUserPermissionException();
        }

        if ($service instanceof Editability && ! $service->is_editable($id)) {
            throw new NotEditableException($service->get_not_editable_reason($id));
        }

        $filter = new ApplyFilter\SaveValue($id, $column);
        $form_data = $filter->apply_filters($form_data);

        do_action('acp/editing/before_save', $column, $id, $form_data);

        $service->update(
            $id,
            $form_data
        );

        // Legacy..
        if ($service instanceof Model && $service->has_error()) {
            throw new RuntimeException($service->get_error()->get_error_message());
        }

        do_action('acp/editing/saved', $column, $id, $form_data);
    }

}