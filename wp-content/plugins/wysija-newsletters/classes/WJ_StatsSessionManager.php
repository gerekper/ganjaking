<?php
defined('WYSIJA') or die('Restricted access');

class WJ_StatsSession {
	public $last_days = null;
	public $from = null;
	public $to = null;
}

class WJ_StatsSessionManager {
	/**
	 * default selection of predefined dates
	 * @var WJ_StatsSession
	 */
	protected $default_selection;

	protected $pre_defined_dates;

	public function __construct(){}

	public function set_pre_defined_dates(Array $pre_defined_dates) {
		$this->pre_defined_dates = $pre_defined_dates;
	}

	public function get_pre_defined_dates() {
		return $this->pre_defined_dates;
	}

	public function set_default_selection(WJ_StatsSession $stats_session) {
		$this->default_selection = $stats_session;
	}

	public function get_default_selection() {
		return $this->default_selection;
	}

	public function get_last_selection() {
		$last_selection = new WJ_StatsSession();

		$stats_session_last_days = $this->get_stats_session_last_days();
		if ($stats_session_last_days !== null) {
			$last_selection->last_days = $stats_session_last_days;
			if ($stats_session_last_days == 0) {// custom dates, "from" and "to" are fixed since the last session
				$last_selection->from = $this->get_stats_session_from();
				$last_selection->to = $this->get_stats_session_to();
			} else {// pre-defined dates, "from" and "to" are relatively assigned
				$pre_defined_dates = $this->get_pre_defined_dates();
				foreach ($pre_defined_dates as $pre_defined_date) {
					if ($pre_defined_date['value'] == $stats_session_last_days) {
						$last_selection->from = $pre_defined_date['from'];
						$last_selection->to = $pre_defined_date['to'];
						break;
					}
				}
			}
		} else {
			$last_selection = $this->get_default_selection();
		}

		$this->set_last_selection($last_selection);

		return $last_selection;
	}

	public function set_last_selection(WJ_StatsSession $stats_session) {
		$this->set_stats_session_last_days($stats_session->last_days);
		$this->set_stats_session_from($stats_session->from);
		$this->set_stats_session_to($stats_session->to);
	}

	protected function is_default_selection(WJ_StatsSession $stats_session) {
		$stats_session = $stats_session;
		return true;
	}

	protected function set_stats_session_last_days($value) {
		setcookie('stats_session_last_days', $value);
	}

	protected function get_stats_session_last_days() {
		return isset($_COOKIE['stats_session_last_days']) ? $_COOKIE['stats_session_last_days'] : null;
	}

	protected function set_stats_session_from($value) {
		setcookie('stats_session_from', $value);
	}

	protected function get_stats_session_from() {
		return isset($_COOKIE['stats_session_from']) ? $_COOKIE['stats_session_from'] : null;
	}

	protected function set_stats_session_to($value) {
		setcookie('stats_session_to', $value);
	}

	protected function get_stats_session_to() {
		return isset($_COOKIE['stats_session_to']) ? $_COOKIE['stats_session_to'] : null;
	}
}

