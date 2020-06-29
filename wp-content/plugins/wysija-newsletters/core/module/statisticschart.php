<?php
defined('WYSIJA') or die('Restricted access');

abstract class WYSIJA_module_statisticschart extends WYSIJA_module_statistics{    
    public function __construct() {
        parent::__construct();
        $this->data['js_date_format'] = 'yy/mm/dd';
    }
    public function init(){        
    }
}