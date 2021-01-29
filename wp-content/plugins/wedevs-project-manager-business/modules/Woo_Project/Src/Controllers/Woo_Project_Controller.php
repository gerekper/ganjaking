<?php
namespace WeDevs\PM_Pro\Modules\Woo_Project\Src\Controllers;

use WP_REST_Request;
use WC_Data_Store;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\Project\Transformers\Project_Transformer;
use WeDevs\PM\Common\Traits\Request_Filter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use League\Fractal\Resource\Collection as Collection;
use WeDevs\PM_Pro\Modules\Woo_Project\Src\Transformers\Product_Transformer;

class Woo_Project_Controller {

    use Transformer_Manager, Request_Filter;

    public function search_products ( WP_REST_Request $request ) {
        $search_key = $request->get_param('s');
        $id = $request->get_param('id');
        $products =[];

        if ( !empty($search_key) ) {
            $args = array(
                's'                     => $search_key,
                'post_type'              => 'product',
                'post_status'            => 'publish',
            );

            $products = get_posts( $args );
        } else if ( is_array( $id )){
            $args = array(
                'post__in'               =>  $id,
                'post_type'              => 'product',
                'post_status'            => 'publish',
            );

            $products = get_posts( $args );
        }

        $resource = new Collection( $products, new Product_Transformer );

        return $this->get_response( $resource );
    }

    public function search_project ( WP_REST_Request $request ) {
        $search_key = $request->get_param('s');
        $id = $request->get_param('id');
        $projects = [];

        if ( ! empty( $search_key ) ) {
            $projects = Project::search($search_key)->get();
        }else if ( is_array( $id ) ){
            $projects = Project::whereIn( 'id', $id )->get();
        }

        $transformer = new Project_Transformer;
        $transformer = $transformer->setDefaultIncludes([]);
        $resource = new Collection( $projects, $transformer);

        return $this->get_response( $resource );
    }
}
