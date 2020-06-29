<?php
defined('WYSIJA') or die('Restricted access');

require_once(dirname(__FILE__).DS.'stats_subscribers_std_model.php');

class WYSIJA_module_stats_subscribers_std extends WYSIJA_module_statistics {

	public $name  = 'stats_subscribers_std';

	public $model = 'WYSIJA_model_stats_subscribers_std';

	public $view  = 'stats_subscribers_std_view';

	static protected $stats_data;

	public function __construct() {
		parent::__construct();
	}

	protected function get_order_direction($params) {
		$order_direction = (!empty($params['order_direction']) && $params['order_direction'] == WYSIJA_module_statistics::ORDER_DIRECTION_ASC ? 'asc' : 'desc');
		return array(
			'sent'   => !empty($params['order_by']) && $params['order_by'] == WYSIJA_module_statistics::ORDER_BY_SENT ? 'sorted '.$order_direction : '',
			'opens'  => !empty($params['order_by']) && $params['order_by'] == WYSIJA_module_statistics::ORDER_BY_OPEN ? 'sorted '.$order_direction : '',
			'clicks' => !empty($params['order_by']) && $params['order_by'] == WYSIJA_module_statistics::ORDER_BY_CLICK ? 'sorted '.$order_direction : ''
		);
	}

	public function hook_subscriber_bottom($params = array( )) {
		if (empty($params['user_id']))
			return;
		$this->data['order_direction'] = $this->get_order_direction($params);
		$this->data['opened_newsletters'] = $this->get_opened_newsletters($params);
		$this->view_show = 'hook_subscriber_bottom';
		return $this->render();
	}

	/**
	 * Get already opened newsletters by current user
	 * @param array $params
	  Array
	  (
	  user_id => 2
	  )
	 * @return array
	 * array(
	 *  'stats' => array(
	 *      emails_count => 123,
	 *      opened_emails_count => 12
	 *   ),
	 *   array(
	 *      1 => array( // First email
	 *          email_id => 1,
	 *          subject => Lorem ipsum,
	 *          sent_at => 123456789
	 *          urls => array(
	 *              1 => array( // first url in the current email
	 *                  email_id => 1,
	 *                  url_id => 2,
	 *                  url => http://domain.com/link/to/page
	 *                  number_clicked => 234
	 *              )
	 *          )
	 *      )
	 *    )
	 * )

	 */
	protected function get_opened_newsletters($params) {
		// get emails by user
		$emails = $this->model_obj->get_emails_by_user_id($params['user_id']);
		if (empty($emails))
			return;

		$emails_count = count($emails);

		// get urls by email
		$urls = $this->model_obj->get_urls_by_email_ids(array_keys($emails), $params['user_id']);

		// combine
		foreach ($emails as $email_id => $email) {
			$emails[$email_id]['urls'] = !empty($urls[$email_id]) ? $urls[$email_id] : array( );
		}

		// filter emails, keep opened or clicked ones only
		foreach ($emails as $email_id => $email) {
			if ((empty($email['opened_at']) || $email['opened_at'] <= 0) AND empty($email['urls'])) {
				unset($emails[$email_id]);
			}
		}
		$opened_emails_count = count($emails);
		return array(
			'stats' => array(
				'emails_count'		=> $emails_count,
				'opened_emails_count' => $opened_emails_count
			),
			'emails'			  => $emails
		);
	}

	/**
	 * hook_newsletter - page MailPoet >> Newsletters >> view detail
	 * @param array $params
	 * @return string
	 */
	public function hook_newsletter($params) {
		return $this->hook_stats($params);
	}

}