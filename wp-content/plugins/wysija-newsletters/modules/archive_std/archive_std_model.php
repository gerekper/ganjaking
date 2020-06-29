<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_model_archive_std extends WYSIJA_model {

	/**
	 * allowed newsletters to be listed
	 * @var array
	 */
	protected $email_types = array(
		1, // standard newsletter
		2 // AutoNL (we will remove all AutoResponders, but keep Post Notification. Refer to self::remove_auto_responders()
	);

	/**
	 * allowed newsletter's statuses
	 * @var array
	 */
	protected $email_status = array(
		1, // sending
		3, // sending
		99, // sending
		2 // sent
	);

	/**
	 * Get newsletters, based on list ids
	 * @param array $list_ids
	 * @return array
	 */
	public function get_newsletters(Array $list_ids = array( )) {
		$where = array( 1 );
		$where[]	= 'e.`type` IN ('.implode(',', $this->email_types).')';
		$where[]	= 'e.`status` IN ('.implode(',', $this->email_status).')';
		$where[]	= 'e.`sent_at` IS NOT NULL';
		$where_join = '';
		if (!empty($list_ids))
			$where_join = ' AND cl.`list_id` IN ('.implode(',', $list_ids).')';
		$query	  = '
            SELECT
                e.`email_id`,
                e.`type`,
                e.`subject`,
                e.`created_at`,
                e.`sent_at`,
                e.`params`
            FROM
                `[wysija]email` e
            JOIN
		`[wysija]campaign` c ON e.`campaign_id` = c.`campaign_id`
	    JOIN
		`[wysija]campaign_list` cl ON c.`campaign_id` = cl.`campaign_id` '.$where_join.'
            WHERE
		'.implode(' AND ', $where).'
            GROUP BY
		e.`email_id`
            ORDER BY
		e.`sent_at` DESC
	    ';
		return $this->replace_shortcodes(
						$this->remove_auto_responders(
								$this->get_results($query, OBJECT) // get in object please. We need to pass them to Shortcodes factory. Shortcodes factory accepts Object instead of Array
						)
		);
	}

	/**
	 * Remove AutoResponders from the dataset, but keep post notification.
	 * Refer to WYSIJA_help_autonews::getNextSend()
	 * @param type $newsletters
	 */
	protected function remove_auto_responders($newsletters) {
		foreach ($newsletters as $index => $newsletter) {
			$newsletters[$index]->params = unserialize(base64_decode($newsletter->params));
			if (!empty($newsletters[$index]->type) && (int)$newsletters[$index]->type === 2) {
				if (isset($newsletters[$index]->params['autonl']['event']) && $newsletters[$index]->params['autonl']['event'] === 'new-articles') {
					// keep post notification
				}
				else {
					unset($newsletters[$index]);
				}
			}
		}
		return $newsletters;
	}

	/**
	 * Render shortcodes
	 * @param array $newsletters a list of object Email
	 * @return array a list of object Email, with renedered shortcodes at subject
	 */
	protected function replace_shortcodes($newsletters) {
		$helper_mailer	 = WYSIJA::get('mailer', 'helper');
		$helper_shortcodes = WYSIJA::get('shortcodes', 'helper');
		foreach ($newsletters as &$newsletter) {
			$helper_mailer->parseSubjectUserTags($newsletter);
			$newsletter->subject = $helper_shortcodes->replace_subject($newsletter);
		}
		return $newsletters;
	}

}