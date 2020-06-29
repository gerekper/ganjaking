<?php
defined('WYSIJA') or die('Restricted access');

require_once(dirname(__FILE__).DS.'archive_std_model.php');

class WYSIJA_module_archive_std extends WYSIJA_module {

	public $name  = 'archive_std';

	public $model = 'WYSIJA_model_archive_std';

	public $view  = 'archive_std_view';

	/**
	 * hook_newsletter - page MailPoet >> Newsletters >> view detail
	 * @param array $params
	 * @return string
	 */
	public function hook_settings_super_advanced(Array $params) {
		$this->view_show = 'hook_settings_super_advanced';
		$this->data['lists'] = $this->get_lists();
		return $this->render();
	}

	/**
	 * hook: before saving settings
	 * @param type $params = array(
	 *                  'REQUEST' =>& $_REQUEST
	 * )
	 */
	public function hook_settings_before_save(Array $params) {
		$config_model  = WYSIJA::get('config', 'model');
		$archive_lists = $config_model->getValue('archive_lists');
		if (!empty($archive_lists))// just save for the first time only
			return;
		// save to database, useful for MixPanel
		$lists		 = $this->get_lists();
		if (empty($lists))
			return;
		$tmp		   = array( );
		foreach ($lists as $list) {
			$is_checked											 = false;
			if (isset($params['REQUEST']['wysija']['config']['archive_lists']) && in_array($list['list_id'], $params['REQUEST']['wysija']['config']['archive_lists']))
				$is_checked											 = true;
			$tmp[$list['list_id']]								  = $is_checked;
		}
		$params['REQUEST']['wysija']['config']['archive_lists'] = $tmp;
	}

	/**
	 * register shortcode, invoked by Wysija-newsletters/index.php::helper_front()
	 */
	public function front_init() {
		add_shortcode('wysija_archive', array( $this, 'scan_wysija_archive_shortcode' ));
	}

	/**
	 * scan shortcode [wysija_archive]
	 * @param array $attributes
	 */
	public function scan_wysija_archive_shortcode($attributes = array( )) {
		$list_ids = !empty($attributes['list_id']) ? explode(',', $attributes['list_id']) : array( );
		return $this->render_archive($list_ids);
	}

	/**
	 * Render archive based on lists which are sent to
	 * @param array $list_ids
	 */
	protected function render_archive(Array $list_ids) {
		$this->data['newsletters'] = $this->model_obj->get_newsletters($list_ids);
		$this->view_show = 'render_archive';
		return $this->render();
	}

	/**
	 * Get all available list of subscribers
	 * @return array(
	 *      0 => array( // first list
	 *          'name' => string
	 *          'list_id' => int,
	 *          'is_public' => boolean
	 *  )
	 * )
	 */
	protected function get_lists() {
		$model_list = WYSIJA::get('list', 'model');
		return $model_list->get(array( 'name', 'list_id', 'is_public' ), array( 'is_enabled' => 1 ));
	}

}