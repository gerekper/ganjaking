<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_forms extends WYSIJA_model{

    var $pk = 'form_id';
    var $table_name = 'form';
    var $columns = array(
        'form_id' => array('type' => 'integer'),
        'name' => array('req' => true),
        'data' => array('type' => 'text'),
        'styles' => array('type' => 'text'),
        'subscribed' => array('type' => 'integer')
    );
    var $escapeFields = array('name');
    var $escapingOn = true;

    function WYSIJA_model_form() {
        $this->WYSIJA_model();
    }
}