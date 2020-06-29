<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_conflicts extends WYSIJA_object{
    var $cleanHooks=array();

    function __construct(){
        parent::__construct();
    }

    /**
     * try to remove hook from plugins inviting themselves onto our interfaces
     * @param type $conflictingPlugins
     */
    function resolve($conflictingPlugins){

        $this->whatToClean=array();
        foreach($conflictingPlugins as $keyPlg =>$plugin){
            foreach($plugin['clean'] as $action => $details){
                foreach($details as $priority =>$info){
                    $this->cleanHooks[$action][$priority][]=$info;
                }
            }
        }
        foreach($this->cleanHooks as $hookToclean => $info){

            switch($hookToclean){
               case 'admin_head':
                   add_action('init', array($this, 'remove_admin_head'), 999);
                   break;
               case 'admin_print_scripts':
                   add_action('admin_menu', array($this, 'remove_admin_print_scripts'), 999);
                   break;
               case 'wp_enqueue_scripts':
                   add_action('admin_menu', array($this, 'remove_wp_enqueue_scripts'), 999);
                   break;
               case 'admin_enqueue_scripts':
                   add_action('admin_menu', array($this, 'remove_admin_enqueue_scripts'), 999);
                   break;
               case 'init':
                   add_action('after_setup_theme', array($this, 'remove_init'), 999);
                   break;
               default:
                   add_action('admin_footer', array($this, 'remove_default'), 999);
            }
        }
    }
    function remove_init(){
        global $wp_filter;
        $this->remove_actions('init');
    }

    function remove_default() {
        $this->remove_actions('admin_init');
    }

    function remove_admin_head(){
        $this->remove_actions('admin_head');
    }
    function remove_admin_print_scripts(){
        $this->remove_actions('admin_print_scripts');
    }

    function remove_wp_enqueue_scripts() {
        $this->remove_actions('wp_enqueue_scripts');
    }

    function remove_admin_enqueue_scripts() {
        $this->remove_actions('admin_enqueue_scripts');
    }



    function remove_actions($actionsToClear){
        global $wp_filter;

        if (!isset($wp_filter[$actionsToClear])) return;

        foreach($wp_filter[$actionsToClear] as $priority => $callbacks) {
            if(!isset($this->cleanHooks[$actionsToClear][$priority])) continue;

            foreach($callbacks as $identifier => $arrayInfo){
                if(is_array($arrayInfo['function'])){
                    foreach($arrayInfo['function'] as $id => $myobject){
                        foreach($this->cleanHooks[$actionsToClear][$priority] as $infoClear) {
                            if(isset($infoClear['objects']) && is_object($myobject) && in_array(get_class($myobject),$infoClear['objects'])){
                              remove_action( $actionsToClear, $infoClear['function'], $priority, $arrayInfo['accepted_args'] );
                            }
                        }
                    }
                } else {
                    foreach($this->cleanHooks[$actionsToClear][$priority] as $infoClear){
                        // if there is more than one function specified (key: functions, type: array)
                        if(isset($infoClear["functions"]) && function_exists($arrayInfo['function']) && in_array($arrayInfo['function'],$infoClear["functions"])){
                          foreach($infoClear['functions'] as $function) {
                            remove_action( $actionsToClear, $function, $priority, $arrayInfo['accepted_args'] );
                          }
                        // if there is only one function to remove (key: function, type: string)
                        } else if(array_key_exists('function', $infoClear) && $infoClear['function'] === $arrayInfo['function']) {
                            remove_action( $actionsToClear, $infoClear['function'], $priority, $arrayInfo['accepted_args'] );
                        }
                    }

                }
            }
        }
     }

    function resolveScriptConflicts() {
      // WP 4.9 mediaelement script conflicts with the MP2 editor
      $dequeue_scripts = array($this, '_deregisterMediaElementScript');
      add_action('wp_print_scripts', $dequeue_scripts, PHP_INT_MAX);
      add_action('admin_print_footer_scripts', $dequeue_scripts, PHP_INT_MAX);
      add_action('admin_footer', $dequeue_scripts, PHP_INT_MAX);
    }

    function _deregisterMediaElementScript() {
      wp_deregister_script('mediaelement');
    }

}
