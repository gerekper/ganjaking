<?php

/**
 * Description of A2W_SyncCurrencyController
 *
 * @author Ali2Woo Team
 *
 * @autoload: a2w_init
 *
 * @cron: true
 */
if (!class_exists('A2W_SyncCurrencyController')) {
    class A2W_SyncCurrencyController extends A2W_AbstractController
    {
        private $event_name = 'a2w_sync_currency_event';

        public function __construct()
        {
            parent::__construct();

            add_action('a2w_install', array($this, 'install'));

            add_action('a2w_uninstall', array($this, 'uninstall'));

            add_filter('cron_schedules', array($this, 'init_reccurences'));

            add_action('admin_init', array($this, 'init'));

            add_action('a2w_sync_currency_event_check', array($this, 'sync_currency_event_check'));

            add_action($this->event_name, array($this, 'sync_currency_event'));
        }

        public function init_reccurences($schedules)
        {
            $schedules['a2w_5_mins'] = array('interval' => 5 * 60, 'display' => __('Every 5 Minutes', 'ali2woo'));
            $schedules['a2w_15_mins'] = array('interval' => 15 * 60, 'display' => __('Every 15 Minutes', 'ali2woo'));
            return $schedules;
        }

        public function init()
        {
            add_action('a2w_set_setting_local_currency', array($this, 'toggle_local_currency'), 10, 3);

            if (!wp_next_scheduled('a2w_sync_currency_event_check')) {
                wp_schedule_event(time(), 'a2w_5_mins', 'a2w_sync_currency_event_check');
            }
        }

        public function install()
        {
            $this->unschedule_event();
            $this->schedule_event_earlier();
            
            wp_clear_scheduled_hook('a2w_sync_currency_event_check');
        }

        public function uninstall()
        {
            $this->unschedule_event();

            wp_clear_scheduled_hook('a2w_sync_currency_event_check');
        }

        private function schedule_event()
        {
            if (!($timestamp = wp_next_scheduled($this->event_name))) {
                wp_schedule_single_event(time() + MINUTE_IN_SECONDS * 15, $this->event_name);
            }
        }

        private function schedule_event_earlier()
        {
            if (!($timestamp = wp_next_scheduled($this->event_name))) {
                wp_schedule_single_event(time() + 5, $this->event_name);
            }
        }

        private function unschedule_event()
        {
            wp_clear_scheduled_hook($this->event_name);
        }

        public function toggle_local_currency($old_value, $value, $option){

            if ($old_value !== $value) {
                $this->unschedule_event();
                $this->schedule_event_earlier();   
            }
        }

        public function sync_currency_event_check()
        {
            if (!wp_next_scheduled($this->event_name)) {
                $this->schedule_event();
            }
        }

        public function sync_currency_event()
        {
            if ($this->is_process_running($this->event_name)) {
                return;
            }

            $this->lock_process($this->event_name);

            a2w_init_error_handler();
            try {

                A2W_CurrencyRates::sync();

            } catch (Throwable $e) {
                a2w_print_throwable($e);
            } catch (Exception $e) {
                a2w_print_throwable($e);
            }

            $this->unlock_process($this->event_name);

            $this->schedule_event();
        }

        protected function is_process_running($process)
        {
            if (get_site_transient($process . '_process_lock')) {
                return true;
            }

            return false;
        }

        protected function lock_process($process)
        {
            set_site_transient($process . '_process_lock', microtime(), MINUTE_IN_SECONDS * 2);
        }

        protected function unlock_process($process)
        {
            delete_site_transient($process . '_process_lock');
        }
    }
}