<?php

namespace WeDevs\PM_Pro\Settings\Controllers;

use WP_REST_Request;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Common\Traits\Request_Filter;
use WeDevs\PM\Project\Models\Project;
use Illuminate\Database\Capsule\Manager as DB;
use WeDevs\PM_Pro\Label\Models\Label;
use WeDevs\PM_Pro\Label\Transformers\Label_Transformer;
use WeDevs\PM_Pro\Label\Models\Task_Label_Task;

class Settings_Controller {
	use Transformer_Manager, Request_Filter;

    public function store_label( WP_REST_Request $request ) {
        $title       = $request->get_param( 'title' );
        $color       = $request->get_param( 'color' );
        $description = $request->get_param( 'description' );
        $project_id = $request->get_param( 'project_id' );
        $status      = 1;

        $label = Label::create(
            [
                'title'       => $title,
                'color'       => $color,
                'description' => $description,
                'status'      => $status,
                'project_id'  => $project_id
            ]
        );

        $resource = new Item( $label, new Label_Transformer );

        return $this->get_response( $resource );
    }

    public function update_label( WP_REST_Request $request ) {
        $title       = $request->get_param( 'title' );
        $color       = $request->get_param( 'color' );
        $description = $request->get_param( 'description' );
        $project_id  = $request->get_param( 'project_id' );
        $label_id    = $request->get_param( 'label_id' );
        $status      = 1;

        $label = [
            'title'       => $title,
            'color'       => $color,
            'description' => $description,
            'status'      => $status,
            'project_id'  => $project_id
        ];

        $stored_label = Label::where( 'id', $label_id )
            ->where( 'project_id', $project_id )
            ->first();

        $stored_label->update_model( $label );

        $resource = new Item( $stored_label, new Label_Transformer );

        return $this->get_response( $resource );
    }

    public function destroy_label( WP_REST_Request $request ) {
        $project_id  = $request->get_param( 'project_id' );
        $label_id    = $request->get_param( 'label_id' );

        $stored_label = Label::where( 'id', $label_id )
            ->where( 'project_id', $project_id )
            ->first();

        if ( $stored_label ) {
            $stored_label->delete();
            $this->destroy_task_label_task( $label_id );
        }

        $message = [
            'message' => __( 'successfully deleted label', 'pm-pro' )
        ];

        return $this->get_response( false, $message );
    }

    public function destroy_task_label_task( $label_id ) {
        if ( ! is_array( $label_id ) ) {
            $label_id = [$label_id];
        }

        Task_Label_Task::whereIn('label_id', $label_id)->delete();
    }

    public static function get_task_labels( $self ) {
        return $self->hasMany( 'WeDevs\PM_Pro\Label\Models\Label', 'project_id' );
    }

    public static function set_project_labels( $data, $item ) {
        $db_labels = $item->labels()
            ->orderBy('id', 'DESC')
            ->get();

        $labels = new Collection( $db_labels, new \WeDevs\PM_Pro\Label\Transformers\Label_Transformer );
        $labels = pm_get_response( $labels );

        $data['labels'] = $labels;

        return $data;
    }
}
