<?php

namespace WeDevs\PM_Pro\Module_Lists\Controllers;

use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM_Pro\Common\Traits\Transformer_Manager;

class Module_Lists_Controller {
    use Transformer_Manager;

    public function index( WP_REST_Request $request ) {

        $modules = array(
            'all'    => pm_pro_get_modules(),
            'active' => $this->active_module_data()
        );

        return $modules;
    }

    public function active_module_data() {
        $module_lists = pm_pro_get_active_modules();
        $modules      = array();

        foreach ( $module_lists as $key => $path ) {
            $modules[] = pm_pro_module_data_format( $path );
        }

        return $modules;
    }

    public function update(WP_REST_Request $request) {
        $module  = $request->get_param('module');
        $type    = $request->get_param('type');


        if ( ! $module ) {
            
        }

        if ( ! in_array( $type, array( 'activate', 'deactivate' ) ) ) {
            
        }

        $module_data = pm_pro_get_module( $module );

        if ( 'activate' == $type ) {
            $status = pm_pro_activate_module( $module );
            
            if ( is_wp_error( $status ) ) {
                
            }

        } else {
            pm_pro_deactivate_module( $module );
        }

        return pm_pro_module_data_format( $module );
    }
}







        // $module  = $request->get_param('module');
        // $type    = $request->get_param('type');
        // $modules = array();

        // if ( ! $module ) {
            
        // }

        // if ( ! in_array( $type, array( 'activate', 'deactivate' ) ) ) {
            
        // }

        // $module_data = pm_pro_get_module( $module );

        // if ( 'activate' == $type ) {
        //     $status = pm_pro_activate_module( $module );
            
        //     if ( is_wp_error( $status ) ) {
                
        //     }

        //     foreach ( pm_pro_get_active_modules() as $key => $path ) {
        //         $modules[] = array(
        //             'path'   => $path,
        //             'slug'   => basename( $path, '.php' ),
        //             'status' => 'active'
        //         );
        //     }

        // } else {
        //     pm_pro_deactivate_module( $module );

        //     $modules[] = array(
        //         'path'   => $module,
        //         'slug'   => basename( $module, '.php' ),
        //         'status' => 'deactive'
        //     );
        // }

        // return $modules;