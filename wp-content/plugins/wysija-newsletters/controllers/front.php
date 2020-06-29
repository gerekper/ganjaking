<?php
defined('WYSIJA') or die('Restricted access');


class WYSIJA_control_front extends WYSIJA_control{

    function __construct($extension="wysija-newsletters"){
        $this->extension=$extension;
        parent::__construct();
        $_REQUEST   = stripslashes_deep($_REQUEST);
        $_POST   = stripslashes_deep($_POST);

        if(isset($_REQUEST['action'])){
            $this->action = preg_replace('|[^a-z0-9_\-]|i','',$_REQUEST['action']);
        }else{
            $this->action = 'index';
        }
    }

    function save(){
        $this->requireSecurity();
        /* see if it's an update or an insert */
        /*get the pk and its value as a conditions where pk = pkval*/
        $conditions=$this->getPKVal($this->modelObj);

        if($conditions){
            /* this an update */

            $result=$this->modelObj->update($_REQUEST['wysija'][$this->model],$conditions);

            if($result) $this->notice($this->messages['update'][true]);
            else{
                $this->error($this->messages['update'][false],true);
            }

        }else{
            /* this is an insert */
            unset($_REQUEST['wysija'][$this->modelObj->pk]);

            $result=$this->modelObj->insert($_REQUEST['wysija'][$this->model]);

            if($result) $this->notice($this->messages['insert'][true]);
            else{
                $this->error($this->messages['insert'][false],true);
            }

        }
        return $result;
    }

    function redirect($location) {
        // make sure we encode square brackets as wp_redirect will strip them off
        $location = str_replace(array('[', ']'), array('%5B', '%5D'), $location);

        // redirect to specified location
        wp_redirect($location);
        exit;
    }
}