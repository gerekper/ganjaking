<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_control_back_tmce extends WYSIJA_control{

    function __construct(){
        parent::__construct();
        $this->viewObj=WYSIJA::get('tmce','view');
    }

    function registerAdd(){
        $this->viewObj->title=__('Insert Subscription Form',WYSIJA);

        $this->viewObj->registerAdd( );
        exit;
    }

}