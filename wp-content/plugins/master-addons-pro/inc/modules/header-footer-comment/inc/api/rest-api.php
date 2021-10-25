<?php
namespace MasterHeaderFooter;

defined( 'ABSPATH' ) || exit;

class JLTMA_Header_Footer_Rest_API{

    private static $_instance = null;

    public $prefix = '';
    public $param = '';
	public $request = null;

	
    public function config( $prefix="", $param=""){
        $this->prefix = $prefix;
        $this->param  = $param;
    }

	public function init(){

        add_action( 'rest_api_init', function () {
            register_rest_route( untrailingslashit('masteraddons/v2/' . $this->prefix), '/(?P<action>\w+)/' . ltrim($this->param, '/'), array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'jltma_rest_api_action'],
                'permission_callback' => '__return_true'
            ));
        });

    }

    public function jltma_rest_api_action($request){

        $this->request = $request;
        $action_class = strtolower($this->request->get_method()) .'_'. sanitize_key($this->request['action']);

        if(method_exists($this, $action_class)){
            return $this->{$action_class}();
        }
        
    }


    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

// JLTMA_Header_Footer_Rest_API::get_instance();