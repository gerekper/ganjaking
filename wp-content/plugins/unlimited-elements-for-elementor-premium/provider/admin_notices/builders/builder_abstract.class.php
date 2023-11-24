<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

abstract class UCAdminNoticeBuilderAbstract{

	private $id;
	private $dismissible = false;
	private $debug;

	/**
	 * get the notice html
	 */
	abstract public function build();

	/**
	 * create a new builder instance
	 */
	public function __construct($id){

		$this->id = $id;
	}

	/**
	 * set the notice as dismissible
	 */
	public function dismissible(){

		$this->dismissible = true;

		return $this;
	}

	/**
	 * set the notice debug data
	 */
	public function debug($data){

		$this->debug = $data;

		return $this;
	}

	/**
	 * get the notice identifier
	 */
	protected function getId(){

		return $this->id;
	}

	/**
	 * get the dismiss html
	 */
	protected function getDismissHtml(){

		if($this->dismissible === false)
			return '';

		$ajaxUrl = $this->getDismissAjaxUrl();
		$text = __('Dismiss', 'unlimited-elements-for-elementor');
		$title = __('Dismiss Notice', 'unlimited-elements-for-elementor');

		return '<a class="uc-notice-dismiss" href="#" data-action="dismiss" title="' . esc_attr($title) . '" data-ajax-url="' . esc_attr($ajaxUrl) . '">' . $text . '</a>';
	}

	/**
	 * get the debug html
	 */
	protected function getDebugHtml(){

		if(empty($this->debug))
			return '';

		return '<p class="uc-notice-debug"><b>DEBUG:</b> ' . $this->debug . '</p>';
	}

	/**
	 * get the dismiss ajax url
	 */
	protected function getDismissAjaxUrl(){

		$ajaxUrl = HelperUC::getUrlAjax('dismiss_notice');
		$ajaxUrl = UniteFunctionsUC::addUrlParams($ajaxUrl, array('id' => $this->id));

		return $ajaxUrl;
	}

	/**
	 * get the postpone ajax url (duration in hours)
	 */
	protected function getPostponeAjaxUrl($duration){

		$ajaxUrl = HelperUC::getUrlAjax('postpone_notice');
		$ajaxUrl = UniteFunctionsUC::addUrlParams($ajaxUrl, array('id' => $this->id, 'duration' => $duration));

		return $ajaxUrl;
	}

}
