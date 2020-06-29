<?php
defined('WYSIJA') or die('Restricted access');

require_once(WYSIJA_CORE.'module'.DS.'statistics.php'); // @todo

class WYSIJA_control_back_statistics extends WYSIJA_control {

	/**
	 * Main model of this controller
	 * @var string
	 */
	public $model = 'statistics';

	/**
	 * Main view of this controller
	 * @var string
	 */
	public $view = 'statistics';

	/**
	 * Date format of filter
	 * @var string
	 */
	protected $date_format = 'Y/m/d';

	/**
	 * Render a hook of a specific module
	 * @return string
	 */
	public function get_block() {
		if (!WYSIJA::current_user_can('wysija_stats_dashboard'))
			die('Action is forbidden.');

		if (empty($_REQUEST['block']))
			return '';
		$module	= $_REQUEST['block'];
		$hook_name = 'hook_stats';
		return apply_filters('custom_module_hook', '', $module, $hook_name, $this->get_post_params());
	}

	protected function get_post_params() {
		$params = array( );
		$order_by = !empty($_REQUEST['filter']['orderBy']) ? $_REQUEST['filter']['orderBy'] : null;
		switch (strtolower($order_by)) {
			case 'sent':
				$order_by		= WYSIJA_module_statistics::ORDER_BY_SENT;
				break;
			case 'open':
				$order_by		= WYSIJA_module_statistics::ORDER_BY_OPEN;
				break;
			case 'click':
				$order_by		= WYSIJA_module_statistics::ORDER_BY_CLICK;
				break;
			case 'unsubscribe':
				$order_by		= WYSIJA_module_statistics::ORDER_BY_UNSUBSCRIBE;
				break;
			default:
				$order_by		= null;
				break;
		}
		$order_direction = !empty($_REQUEST['filter']['orderDirection']) ? $_REQUEST['filter']['orderDirection'] : null;
		switch (strtolower($order_direction)) {
			case 'asc':
				$order_direction		   = WYSIJA_module_statistics::ORDER_DIRECTION_ASC;
				break;
			case 'desc':
			default:
				$order_direction		   = WYSIJA_module_statistics::ORDER_DIRECTION_DESC;
				break;
		}
		$params['top']			= !empty($_REQUEST['filter']['itemPerPage']) ? (int)$_REQUEST['filter']['itemPerPage'] : WYSIJA_module_statistics::DEFAULT_TOP_RECORDS;
		$params['from']			= !empty($_REQUEST['filter']['from']) ? $_REQUEST['filter']['from'] : null;
		$params['to']			= !empty($_REQUEST['filter']['to']) ? $_REQUEST['filter']['to'] : null;
		$params['last_days']	= isset($_REQUEST['filter']['lastDays']) ? $_REQUEST['filter']['lastDays'] : null;

		$params['order_by']		= $order_by;
		$params['order_direction'] = $order_direction;
		$params['additional_param'] = !empty($_REQUEST['filter']['additionalParam']) ? trim($_REQUEST['filter']['additionalParam']) : null;

		// this doesn't work when php is less than 5.3, this is the case on my host (ben) which is very popular in France, SPain and UK
		// we cannot use functions from php 5.3
		if (function_exists('date_diff')) {
			$this->data['date_interval'] = date_diff(date_create($params['from']), date_create($params['to']));
		}
		else {
			$duration		   = strtotime($params['to']) - strtotime($params['from']);
			$helper_toolbox	 = WYSIJA::get('toolbox', 'helper');
			$this->data['date_interval'] = (object)$helper_toolbox->convert_seconds_to_array($duration, false);
		}
		$params['group_by'] = ( $this->data['date_interval']->days == 0 || $this->data['date_interval']->days > WYSIJA_module_statistics::SWITCHING_DATE_TO_MONTH_THRESHOLD) ?
				WYSIJA_module_statistics::GROUP_BY_MONTH :
				WYSIJA_module_statistics::GROUP_BY_DATE; // $date_interval->days == 0, means, no begin date, no end date
		// Hack!
		$_REQUEST['limit_pp'] = $params['top']; // Pagination, mark current selected value
		
		$this->save_last_selection($params);

		// Modify TO date to make sure we always count 23:59:59 of that day
		$to		   = new DateTime($params['to']);
		$to->modify('+1 day');
		$params['to'] = $to->format($this->date_format);

		return $params;
	}

	protected function save_last_selection($params) {
		$stats_session_manager = new WJ_StatsSessionManager();
		$stats_session = new WJ_StatsSession();
		$stats_session->last_days = $params['last_days'];
		$stats_session->from = $params['from'];
		$stats_session->to = $params['to'];
		$stats_session_manager->set_last_selection($stats_session);
	}

}
