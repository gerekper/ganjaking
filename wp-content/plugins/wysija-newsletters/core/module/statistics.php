<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_module_statistics extends WYSIJA_module{
    const GROUP_BY_YEAR = 1;
    const GROUP_BY_MONTH = 2;
    const GROUP_BY_DATE = 3;
    const GROUP_BY_HOUR = 4;
    const GROUP_BY_MINUTE = 5;
    
    const ORDER_BY_SENT = 1;
    const ORDER_BY_OPEN = 2;
    const ORDER_BY_CLICK = 3;
    const ORDER_BY_UNSUBSCRIBE = 4;
    
    const ORDER_DIRECTION_ASC = 1;
    const ORDER_DIRECTION_DESC = 2;
    
    const SWITCHING_DATE_TO_MONTH_THRESHOLD = 90;// if the days between FROM and TO is greater than this value, we will group data by month instead of by date. Useful for charts.
    
    const DEFAULT_TOP_RECORDS = 5; // default number of how many first records we should retrieve
    
    public function __construct() {
        parent::__construct();
        $this->data['messages'] = $this->init_messages();
    }
    
    protected function init_messages() {
        return array(
            'data_not_available' => __("There's no stats to load!", WYSIJA)
            );
    }
}