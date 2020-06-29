<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_model_stats_subscribers_std extends WYSIJA_module_statistics_model {

	/**
	 * Get all emails which were sent to a specific user
	 * @param int $user_id
	 * @return array list of emails
	 * array(
	 * 1 => array(
	 *  email_id => 1
	 *  subject => 'lorem ipsum'
	 *  sent_at => 123456789
	 * ),
	 * 3 => array(
	 *  email_id => 3
	 *  subject => 'lorem ipsum'
	 *  sent_at => 123456789
	 * ),
	 * 5 => array(
	 *  email_id => 5
	 *  subject => 'lorem ipsum'
	 *  sent_at => 123456789
	 * )
	 * )
	 */
	public function get_emails_by_user_id($user_id, $opened_only = false) {
		$where = array( 1 );
		if ($opened_only) {
			$where[] = 'eus.`opened_at` > 0';
			$where[] = 'eus.`status` > 0';
		}

		$query   = '
            SELECT
                e.`email_id`,
                e.`subject`,
                e.`sent_at`,
                eus.`opened_at`
            FROM
                `[wysija]email_user_stat` eus
            JOIN
                `[wysija]email` e ON e.`email_id` = eus.`email_id` AND eus.`user_id` = '.(int)$user_id.'
            WHERE '.implode(' AND ', $where).'
	    ORDER BY eus.`opened_at` DESC
            ';
		$dataset = $this->get_results($query);
		return $this->indexing_dataset_by_field('email_id', $dataset);
	}

	/**
	 * Get all possible urls embebed in each email
	 * @param array $email_ids
	 * @return array list of urls
	 * array(
	 * email_id => array(
	 *  url_id => array(
	 *      'email_id' => 1,
	 *      'url_id' => 1,
	 *      'url' => 'http://...',
	 *      'number_clicked' => 2
	 *   )
	 *  )
	 * )
	 */
	public function get_urls_by_email_ids(Array $email_ids, $user_id = null) {
		if (empty($email_ids))
			return array( );

		$where = array( 1 );
		$where[] = 'euu.`email_id` IN ('.implode(',', $email_ids).')';
		if (!empty($user_id))
			$where[] = 'euu.`user_id` = '.(int)$user_id;
		$query   = '
            SELECT
                euu.`email_id`,
                u.`url_id`,
                u.`url`,
                euu.`number_clicked`
            FROM
                `[wysija]email_user_url` euu
            JOIN
                `[wysija]url` u ON u.`url_id` = euu.`url_id` AND '.implode(' AND ', $where).'
            ORDER BY euu.`email_id`, u.`url`

            ';

		$dataset = $this->get_results($query);

		$tmp = array( );
		foreach ($dataset as $record) {
			$tmp[$record['email_id']][] = $record;
		}
		foreach ($tmp as $email_id => $_dataset) {
			$tmp[$email_id] = $this->indexing_dataset_by_field('url_id', $_dataset);
		}
		return $tmp;
	}

}