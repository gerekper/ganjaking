<?php

defined('WYSIJA') or die('Restricted access');

class WYSIJA_control_back_mp3 extends WYSIJA_control_back {

    /**
     * Main view of this controller
     * @var string
     */
    public $view = 'mp3';
    public $model = 'config';


    /**
     * Constructor
     */
    function __construct(){
        parent::__construct();
    }

    function defaultDisplay() {
        $this->jsTrans['premium_activate'] = __('Activate now', WYSIJA);
        $this->jsTrans['premium_activating'] = __('Checking license', WYSIJA);
        $model_config = WYSIJA::get('config', 'model');
        $is_multisite = is_multisite();
        $is_network_admin = WYSIJA::current_user_can('manage_network');
        if ($is_multisite && $is_network_admin) {
          $model_config->save(array('ms_wysija_whats_new' => WYSIJA::get_version()));
        } else {
          $model_config->save(array('wysija_whats_new' => WYSIJA::get_version()));
        }
    }
}
