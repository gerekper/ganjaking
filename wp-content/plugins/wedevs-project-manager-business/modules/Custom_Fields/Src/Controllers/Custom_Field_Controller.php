<?php
namespace WeDevs\PM_Pro\Modules\Custom_Fields\Src\Controllers;

use Reflection;
use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Common\Traits\Request_Filter;
use Carbon\Carbon;
use WeDevs\PM_Pro\Modules\Custom_Fields\Src\Models\Custom_Field;
use WeDevs\PM_Pro\Modules\Custom_Fields\Src\Transformers\Custom_Field_Transformer;
use WeDevs\PM_Pro\Modules\Custom_Fields\Src\Models\Task_Custom_Field;
use WeDevs\PM\Common\Models\Boardable;
use WeDevs\PM_Pro\Modules\Custom_Fields\Src\Transformers\Task_Custom_Field_Transformer;


class Custom_Field_Controller {

    use Transformer_Manager, Request_Filter;

    public function store( WP_REST_Request $request ) {
        $title       = $request->get_param('title');
        $type        = $request->get_param('type');
        $description = $request->get_param('description');
        $optional    = maybe_serialize( $request->get_param('options') );
        $project_id  = $request->get_param('project_id');
        $order       = Custom_Field::latest_order( $project_id );


        $custom_field = Custom_Field::create([
            'project_id'     => $project_id,
            'title'          => $title,
            'description'    => $description,
            'type'           => $type,
            'optional_value' => $optional,
            'order'          => $order + 1
        ]);

        $resource  = new Item( $custom_field, new Custom_Field_Transformer );

        $message = [
            'message' => 'Relation update successfully'
        ];

        return pm_get_response( $resource, $message );
    }

    public function update( WP_REST_Request $request ) {
        $title       = $request->get_param('title');
        $description = $request->get_param('description');
        $optional    = maybe_serialize( $request->get_param('options') );
        $id          = $request->get_param('id');

        $custom_field = Custom_Field::find( $id );


        $custom_field->update_model( [
            'title'    => $title,
            'description'    => $description,
            'optional_value' => $optional
        ] );

        $resource = new Item( $custom_field, new Custom_Field_Transformer );

        return pm_get_response( $resource );
    }

    public function destroy( WP_REST_Request $request ) {
        $id = $request->get_param( 'field_id' );
        $field    = Custom_Field::find( $id );

        if ( $field ) {
            $field->delete();
        }

        return $this->get_response( false, [] );
    }

    function index( WP_REST_Request $request ) {
        $project_id = $request->get_param('project_id');
        $task_id    = $request->get_param('task_id');
        $with       = $request->get_param('with');

        return $this->get_tasks_custom_fields_value([
            'project_id' => $project_id,
            'task_id'    => $task_id,
            'with'       => $with
        ]);
    }

    function get_tasks_custom_fields_value( $params ) {
        $project_id = $params['project_id'];
        $task_id    = $params['task_id'];
        $with       = $params['with'];

        $fields = Custom_Field::where( 'project_id', $project_id )
            ->orderBy( 'order', 'ASC');

        if( ! empty( $with ) && $with == 'value' ) {
            $fields = $fields->with(['value' => function($q) use($project_id, $task_id) {

                $q->where( 'project_id', $project_id )
                    ->where( 'task_id', $task_id );

            }]);
        }

        $resource  = new Collection( $fields->get(), new Custom_Field_Transformer );

        return pm_get_response($resource);
        die();
    }

    function ajax_store_field_value( WP_REST_Request $request ) {
        $project_id = $request->get_param('project_id');
        $task_id    = $request->get_param('task_id');
        $field_id   = $request->get_param('field_id');
        $color      = $request->get_param('color');
        $value      = $request->get_param('value');

        return $this->store_field_value([
            'project_id' => $project_id,
            'task_id'    => $task_id,
            'field_id'   => $field_id,
            'color'      => $color,
            'value'      => $value
        ]);
    }

    function store_field_value( $params ) {
        $project_id = $params['project_id'];
        $task_id    = $params['task_id'];
        $field_id   = $params['field_id'];
        $color      = $params['color'];
        $value      = $params['value'];

        $list_id = Boardable::select('board_id')
            ->where('board_type', 'task_list')
            ->where('boardable_type', 'task')
            ->where('boardable_id', $task_id)
            ->first()
            ->board_id;

        $has_value = Task_Custom_Field::where( 'project_id', $project_id )
            ->where( 'task_id', $task_id )
            ->where( 'field_id', $field_id )
            ->first();

        if( empty( $has_value ) ) {
            $has_value = Task_Custom_Field::create(
                [
                    'project_id' => $project_id,
                    'task_id'    => $task_id,
                    'field_id'   => $field_id,
                    'list_id'    => $list_id,
                    'value'      => $value,
                    'color'      => $color
                ]
            );
        } else {
            $has_value->update_model(
                [
                    'value' => $value,
                    'color' => $color
                ]
            );
        }

        $resource = new Item( $has_value, new Task_Custom_Field_Transformer );

        return pm_get_response( $resource );
    }


}


