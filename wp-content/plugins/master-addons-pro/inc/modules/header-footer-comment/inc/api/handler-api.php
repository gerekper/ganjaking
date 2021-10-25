<?php 
namespace MasterHeaderFooter;

class Handler_Api{

    public $prefix = '';
    public $param = '';
    public $request = null;
    private static $_instance = null;
    
    public function __construct(){
        $this->config();
        $this->init();
    }

    public function config(){

    }

    public function init(){
        add_action( 'rest_api_init', function () {
            register_rest_route( untrailingslashit('masteraddons/v2/' . $this->prefix), '/(?P<action>\w+)/' . ltrim($this->param, '/'), array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'action'],
                'permission_callback' => '__return_true'
            ));
        });
    }

    public function action($request){
        $this->request = $request;
        $action_class = strtolower($this->request->get_method()) .'_'. sanitize_key($this->request['action']);

        if(method_exists($this, $action_class)){
            return $this->{$action_class}();
        }
    }

}


new Handler_Api();