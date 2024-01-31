<?php

/* * class
 * Description of AbstractController
 *
 * @author Ali2Woo Team
 * 
 * @position: 1
 */

namespace Ali2Woo;

abstract class AbstractController {
    private array $model = [];
    private array $views = [];
    
    public function __construct($views_path='') {
        if(!$views_path){
            $views_path = A2W()->plugin_path() . '/view/';
        }
        
        $this->views_path = $views_path;
    }
    
    protected function set_views_path($views_path) {
        $this->views_path = $views_path;
        if (substr($this->views_path, -1) !== '/') {
            $this->views_path = $this->views_path . '/';
        }
    }
    
    protected function model_put($name, $value) {
        $this->model[$name] = $value;
    }

    protected function include_view($view, $action_hook = false) {
        $this->views = is_array($view) ? $view : array($view);

        if ($action_hook) {
            add_action($action_hook, array($this, 'show_view'));
        } else {
            $this->show_view();
        }
    }

    public function show_view() {
        extract($this->model);
        
        foreach ($this->views as $v) {
            $view_action = str_replace(".php", "", str_replace(array('\\', '/'), '_', $v));
            
            do_action('a2w_before_'.$view_action);
            
            $theme_view_path = get_template_directory() . sprintf('/%s/', A2W()->plugin_slug);
            
            if (file_exists( $theme_view_path . $v) && is_file($theme_view_path . $v)){
                include($theme_view_path . $v);
            } else if (file_exists($this->views_path . $v) && is_file($this->views_path . $v)) {
                include($this->views_path . $v );
            }
            
            do_action('a2w_after_'.$view_action);
        }
    }

}
