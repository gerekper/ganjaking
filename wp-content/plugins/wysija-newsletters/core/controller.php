<?php
defined('WYSIJA') or die('Restricted access');


class WYSIJA_control extends WYSIJA_object{
	var $model="";
	var $view="";
	var $action="";
	var $list_columns=array();
	var $form_columns=array();
	var $filters=array();
	var $js=array();
	var $jsLoc=array();
	var $extension="wysija-newsletters";
	var $joins=array();
	var $title="";

	function __construct(){
		//setup some required objects for the request
		if(!defined('DOING_AJAX')){
			if($this->view) $this->viewObj = WYSIJA::get($this->view,"view",false,$this->extension);
			if(empty($this->viewObj)) $this->viewObj = new stdClass (); // In some cases, viewObj can not be created
			if($this->model){
				$this->modelObj=WYSIJA::get($this->model,"model",false,$this->extension);
				$this->viewObj->model=WYSIJA::get($this->model,"model",false,$this->extension);
			}
		}
	}

        private function _rebuild_ajax_nonce_action(){
            $actionnonce = 'wysija_ajax';
            if( !empty( $_REQUEST['controller']) && !empty( $_REQUEST['task']) ){
                $actionnonce = 'wysija_'.$_REQUEST['controller'] . '-action_'.$_REQUEST['task'];
            }
            return $actionnonce;
        }

        private function _rebuild_nonce_action(){
            $actionnonce = '';
            //backend case
            if(is_admin() && !empty($_REQUEST['page'])){
                    $actionnonce=$_REQUEST['page'].'-action_'.$_REQUEST['action'];
                    if(!empty($_REQUEST['id'])) $actionnonce.='-id_'.$_REQUEST['id'];
            //frontend case
            } elseif (!empty($_REQUEST['controller'])) {
                    $actionnonce=$_REQUEST['controller'].'-action_'.$_REQUEST['action'];
                    if(!empty($_REQUEST['id'])) $actionnonce.='-id_'.$_REQUEST['id'];
            }
            return $actionnonce;
        }

	private function _nonce_verification(){
		if(!empty($_REQUEST['_wpnonce'])){
			if($_REQUEST['action']=='wysija_ajax'){
				$actionnonce = $this->_rebuild_ajax_nonce_action();
			}else{
                                $actionnonce = $this->_rebuild_nonce_action();
			}

			//if the wp_nonce has been set up then we test it against the one here if it fails we just die
			$nonce=$_REQUEST['_wpnonce'];

			if(!wp_verify_nonce($nonce, $actionnonce) ){
				wp_die("<h2>" . __('Security failure during request') . "</h2>", __("Security Problem"), array(
					'response' => 403,
					'back_link' => false
				));
			}


		}else{
                    if(!wp_verify_nonce($nonce, $actionnonce) ){
				wp_die("<h2>" . __('Security failure during request') . "</h2>", __("Security Problem"), array(
					'response' => 403,
					'back_link' => false
				));
			}
                }

	}


	/**
	 * if a controller calls that page then it needs those global parameters to be set
	 * @return boolean
	 */
	function requireSecurity(){

		if( !isset($_REQUEST['_wpnonce']) ) {
		   die('Your request is not safe.');
		}else{
                    $this->_nonce_verification();
                    return true;
		}
	}


	/**
	 * prepare an array of condition for a where statement with the pk and its value.
	 * note: not sure this function should be here though.
	 * @return array
	 */
	function getPKVal(){

		if(isset($_POST['wysija'][$this->modelObj->table_name][$this->modelObj->pk]) && $_POST['wysija'][$this->modelObj->table_name][$this->modelObj->pk]){
			//this is an update
			$conditions=array($this->modelObj->pk =>$_POST['wysija'][$this->modelObj->table_name][$this->modelObj->pk]);
			unset($_POST['wysija'][$this->modelObj->table_name][$this->modelObj->pk]);
		}elseif(isset($_GET['id'])){
			$conditions=array($this->modelObj->pk =>$_GET['id']);
		}else{
			$conditions=array();
		}

		return $conditions;
	}
}