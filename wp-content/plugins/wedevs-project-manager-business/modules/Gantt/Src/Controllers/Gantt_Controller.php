<?php
namespace WeDevs\PM_Pro\Modules\Gantt\Src\Controllers;

use Reflection;
use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Common\Traits\Request_Filter;
use Carbon\Carbon;
use WeDevs\PM_Pro\Modules\Gantt\Src\Models\Gantt;
use WeDevs\PM_Pro\Modules\Gantt\Src\Transformers\Link_Transformer;


class Gantt_Controller {

    use Transformer_Manager, Request_Filter;

    public function index( WP_REST_Request $request ) {

    }


    public function show( WP_REST_Request $request ) {

    }


    public function store( WP_REST_Request $request ) {
        $source  = $request->get_param('source');
        $target  = $request->get_param('target');
        $type    = $request->get_param('type');

        $link = Gantt::create([
            'source' => $source,
            'target' => $target,
            'type'   => $type
        ]);

        $resource = new Item( $link, new Link_Transformer );

        $message = [
            'message' => 'Relation update successfully'
        ];

        return $this->get_response( $resource, $message );
    }


    public function update( WP_REST_Request $request ) {

    }

    public function destroy( WP_REST_Request $request ) {
        $link_id = $request->get_param('link_id');
        $link    = Gantt::find($link_id);

        if ( $link ) {
            $link->delete();
        }

        return $this->get_response( false, [] );
    }

    function delete_all_relation(Gantt $board) {

    }

    function board_order( WP_REST_Request $request ) {


    }

    function task_order( WP_REST_Request $request ) {


    }
}


