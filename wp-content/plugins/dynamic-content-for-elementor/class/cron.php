<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
}
/**
 * Cron Class
 *
 * Execute all crons for the plugin
 */
class Cron
{
    /**
     * All tasks
     *
     * @var array
     */
    private $tasks = [];
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = ['check_license' => ['interval' => 'daily']];
        $this->schedule_all_tasks();
    }
    /**
     * Retrieve all tasks
     *
     * @return array
     */
    protected function get_tasks()
    {
        return $this->tasks;
    }
    /**
     * Schedule all tasks
     *
     * @return void
     */
    protected function schedule_all_tasks()
    {
        foreach ($this->get_tasks() as $method_name => $info) {
            // Continue if the interval is empty or the callback doesn't exist
            if (empty($info['interval']) || !\method_exists($this, $method_name)) {
                continue;
            }
            $hook = $this->get_hook_name($method_name);
            if (!wp_next_scheduled($hook)) {
                wp_schedule_event(\time(), $info['interval'], $hook);
            }
            add_action($hook, [$this, $method_name]);
        }
    }
    /**
     * Clear all tasks from the list
     *
     * @return void
     */
    public function clear_all_tasks()
    {
        foreach ($this->get_tasks() as $method_name => $info) {
            $hook = $this->get_hook_name($method_name);
            wp_clear_scheduled_hook($hook);
        }
    }
    /**
     * Get Hook Name
     *
     * @param string $method_name
     * @return string
     */
    protected function get_hook_name(string $method_name)
    {
        return DCE_PREFIX . '_' . $method_name . '_cron';
    }
    /**
     * Check license on cron
     * 
     * @return void
     */
    public function check_license()
    {
        Plugin::instance()->license_system->refresh_and_repair_license_status();
    }
}
