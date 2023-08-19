<?php

namespace ACP\Editing\RequestHandler;

use AC\ListScreen\ManageValue;
use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Editing\ApplyFilter;
use ACP\Editing\Editable;
use ACP\Editing\Model;
use ACP\Editing\RequestHandler;
use ACP\Editing\Service;
use Exception;

class InlineSave implements RequestHandler
{

    /**
     * @var Storage;
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function handle(Request $request)
    {
        $response = new Response\Json();

        $id = (int)$request->filter('id', null, FILTER_SANITIZE_NUMBER_INT);

        if ( ! $id) {
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

        $strategy = $list_screen->editing();

        if ( ! $strategy) {
            $response->error();
        }

        if ( ! $strategy->user_can_edit_item($id)) {
            $response->set_message(__("You don't have permissions to edit this item", 'codepress-admin-columns'))
                     ->error();
        }

        $column = $list_screen->get_column_by_name($request->get('column'));

        if ( ! $column instanceof Editable) {
            $response->error();
        }

        $service = $column->editing();

        if ( ! $service instanceof Service) {
            $response->error();
        }

        $id = (int)$request->get('id');
        $form_data = $request->get('value');

        $filter = new ApplyFilter\SaveValue($id, $column);
        $form_data = $filter->apply_filters($form_data);

        try {
            do_action('acp/editing/before_save', $column, $id, $form_data);

            $service->update(
                $id,
                $form_data
            );

            do_action('acp/editing/saved', $column, $id, $form_data);
        } catch (Exception $e) {
            $response->set_message($e->getMessage())
                     ->error();
        }

        // Legacy error handling..
        if ($service instanceof Model && $service->has_error()) {
            $response->set_message($service->get_error()->get_error_message())
                     ->error();
        }

        $filter = new ApplyFilter\EditValue($id, $column);

        try {
            $edit_value = $filter->apply_filters($service->get_value($id));
        } catch (Exception $e) {
            $response->set_message($e->getMessage())
                     ->error();
        }

        $response
            ->set_parameters([
                'id'            => $id,
                'value'         => $edit_value,
                'display_value' => $list_screen instanceof ManageValue
                    ? $list_screen->manage_value()->render_cell($column->get_name(), $id)
                    : null,
            ])
            ->success();
    }

}