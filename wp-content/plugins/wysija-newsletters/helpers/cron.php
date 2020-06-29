<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_cron extends WYSIJA_object{

    var $report=false;

    function __construct(){
        parent::__construct();
    }

    /**
     * the cron tasks are being run for a certain number of processes (all queue, bounce etc..)
     * @return void
     */
    function run() {
        @ini_set('max_execution_time',0);
        $model_config = WYSIJA::get('config','model');
        $running = false;
        if(!$model_config->getValue('cron_manual')){
            return;
        }
        // get the param from where you want
        $report = $process = false;
        if(isset($_REQUEST['process']) && $_REQUEST['process']){
            $process = $_REQUEST['process'];
        }elseif(!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['SHELL']) && isset($_SERVER['argv'][2]) && $_SERVER['argv'][2]){
            $process = $_SERVER['argv'][2];
        }

        if(isset($_REQUEST['report']) && $_REQUEST['report']){
            $this->report = $_REQUEST['report'];
        }elseif(!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['SHELL']) && isset($_SERVER['argv'][3]) && $_SERVER['argv'][3]){
            $this->report = $_SERVER['argv'][3];
        }

        if( !empty($process) ){
            //include the needed parts of wp plus wysija
            if(isset($_REQUEST[WYSIJA_CRON]) || ( isset($_SERVER['argv'][1]) && $_SERVER['argv'][1]==WYSIJA_CRON )) echo '';
            else{
                wp_die("<h2>" . 'Invalid token' . "</h2>", "MailPoet CRON error", array(
                        'response' => 404,
                        'back_link' => false
                ));
            }
            $cron_schedules = get_option('wysija_schedules');

            $processes = array();
            if(strpos($process, ',')!==false){
                $processes = explode(',', $process);
            }else $processes[] = $process;

            $allProcesses = array('queue','bounce','daily','weekly','monthly');
            foreach($processes as $scheduleprocess){
                if($scheduleprocess!='all'){
                    if( in_array( $scheduleprocess, $allProcesses ) ){
                       $this->check_scheduled_task($cron_schedules,$scheduleprocess);
                    }else{
                        wp_die("<h2>" . 'Invalid process' . "</h2>", "MailPoet CRON error", array(
                                'response' => 404,
                                'back_link' => false
                        ));
                    }
                }else{
                    foreach($allProcesses as $processNK){
                        $this->check_scheduled_task($cron_schedules,$processNK);
                    }
                    if($this->report) echo 'processed : All<br/>';
                    if(!isset($_REQUEST['silent'])) echo 'MailPoet\'s cron is ready. Simply setup a CRON job on your server (cpanel or other) to trigger this page.';
                    exit;
                }
            }
        }else{
            wp_die("<h2>" . 'Missing process' . "</h2>", "MailPoet CRON error", array(
                        'response' => 404,
                        'back_link' => false
                ));
        }
        if(!isset($_REQUEST['silent'])) echo '"MailPoet\'s cron is ready. Simply setup a CRON job on your server (cpanel or other) to trigger this page.' ;
        if($process)    exit;
    }

    /**
     * check that one scheduled task is ready to be executed
     * @param type $cron_schedules list of recorded cron schedules
     * @param type $processNK what to process queue, bounce etc...
     */
    function check_scheduled_task($cron_schedules,$processNK){
        $helper_toolbox = WYSIJA::get('toolbox','helper');
        $time_passed = $time_left = 0;
        $run_scheduled = true;
        $extra_text = $multisite_prefix = '';
        // this is to display a different message whether we're dealing with bounce or not.
        if($processNK == 'bounce'){
             $model_config = WYSIJA::get( 'config' , 'model' );
             // if premium is activated we launch the premium function
             $multisite_prefix = '';
             if(is_multisite()){
                 $multisite_prefix = 'ms_';
             }

             // we don't process the bounce automatically unless the option is ticked
             if(!(defined('WYSIJANLP') && $model_config->getValue( $multisite_prefix . 'bounce_process_auto' )) ){
                 $extra_text = ' (bounce handling not activated)';
                 $run_scheduled=false;
             }

        }

        // calculate the time passed processing a scheduled task
        if(!empty($cron_schedules[$processNK]['running'])){
            $time_passed = time()- $cron_schedules[$processNK]['running'];
            $time_passed = $helper_toolbox->duration_string($time_passed,true,2,5);
        }else{
            $time_left = $cron_schedules[$processNK]['next_schedule'] - time();
            $time_left = $helper_toolbox->duration_string($time_left,true,2,5);
        }

        if($run_scheduled && $cron_schedules[$processNK]['next_schedule'] < time() && !$cron_schedules[$processNK]['running']){
            if($this->report) echo 'exec process '.$processNK.'<br/>';
            $this->run_scheduled_task($processNK);
        }else{
           if($this->report){
               if($time_passed) $text_time = ' running since : '.$time_passed;
               else  $text_time = ' next run : '.$time_left;
               if(!empty($extra_text)) $text_time = $extra_text;
               echo 'skip process <strong>'.$processNK.'</strong>'.$text_time.'<br/>';
           }
        }
    }

    /**
     * run process if it's not detected as already running
     * @param type $process
     * @return type
     */
    function run_scheduled_task($process = 'queue'){
        //first let's make sure that the process asked to be run is not already running
        $scheduled_times = WYSIJA::get_cron_schedule($process);
        $processes = WYSIJA::get_cron_frequencies();
        $process_frequency = $processes[$process];

        // check if the scheduled task is already being processed,
        // we consider it timed out once the started running time plus the frequency has been passed
        if(!empty($scheduled_times['running']) && ($scheduled_times['running'] + $process_frequency) > time()){
            if($this->report)   echo 'already running : '.$process.'<br/>';
            return;
        }

        // set schedule as running
        WYSIJA::set_cron_schedule($process,0,time());

        // execute schedule
        switch($process){
            case 'queue':
                // check if there are any scheduled newsletters ready for action
                WYSIJA::check_scheduled_newsletters();

                // if premium is activated we execute the premium cron process
                if(defined('WYSIJANLP')){
                    $helper_premium = WYSIJA::get('premium', 'helper', false, WYSIJANLP);
                    $helper_premium->croned_queue_process();
                }else{
                    // run the standard queue process no scheduled tasks will be check since it has already been checked above
                    WYSIJA::croned_queue(false);
                }
                break;
            case 'bounce':
                $helper_premium = WYSIJA::get('premium', 'helper', false, WYSIJANLP);
                $model_config = WYSIJA::get( 'config' , 'model' );
                // if premium is activated we launch the premium function
                if(is_multisite()){
                    $multisite_prefix='ms_';
                }

                // we don't process the bounce automatically unless the option is ticked
                if(defined('WYSIJANLP') && $model_config->getValue( $multisite_prefix . 'bounce_process_auto' )){
                    $helper_premium->croned_bounce();
                }else{
                    $process .= ' (bounce handling not activated)';
                }

                break;
            case 'daily':
                WYSIJA::croned_daily();
                break;
            case 'weekly':
                if(defined('WYSIJANLP')){
                    $helper_premium = WYSIJA::get('premium', 'helper', false, WYSIJANLP);
                    $helper_premium->croned_weekly();
                }
                WYSIJA::croned_weekly();
                break;
            case 'monthly':
                WYSIJA::croned_monthly();
                break;
        }
        // set next_schedule details
        WYSIJA::set_cron_schedule($process);
        if($this->report) echo 'processed : '.$process.'<br/>';
    }
}
