<?php


namespace Premmerce\WooCommercePinterest\Task;

abstract class AbstractTaskManager {

	/**
	 * Task status option name
	 *
	 * @var string
	 */
	protected $taskStatusOptionName = '';

	/**
	 * Init
	 *
	 * @return void
	 */
	public function init() {
		$this->schedule();
	}

	abstract public function schedule();

	/**
	 * Set task started
	 *
	 * @return void
	 */
	public function setTaskStarted() {
		update_option($this->taskStatusOptionName, true);
	}

	/**
	 * Check if task started
	 *
	 * @return bool
	 */
	public function taskStarted() {
		return get_option($this->taskStatusOptionName, false);
	}

	/**
	 * Unset task started
	 *
	 * @return void
	 */
	public function unsetTaskStarted() {
		delete_option($this->taskStatusOptionName);
	}
}
