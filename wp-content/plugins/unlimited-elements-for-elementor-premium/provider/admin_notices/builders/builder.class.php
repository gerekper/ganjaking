<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCAdminNoticeBuilder extends UCAdminNoticeBuilderAbstract{

	const COLOR_INFO = 'info';
	const COLOR_WARNING = 'warning';
	const COLOR_ERROR = 'error';
	const COLOR_DOUBLY = 'doubly';

	const ACTION_VARIANT_PRIMARY = 'primary';
	const ACTION_VARIANT_SECONDARY = 'secondary';

	private $color = self::COLOR_INFO;
	private $heading;
	private $content;
	private $actions = array();

	/**
	 * set the notice color
	 */
	public function color($color){

		$this->color = $color;

		return $this;
	}

	/**
	 * set the notice heading
	 */
	public function withHeading($heading){

		$this->heading = $heading;

		return $this;
	}

	/**
	 * set the notice content
	 */
	public function withContent($content){

		$this->content = $content;

		return $this;
	}

	/**
	 * add the notice action
	 */
	public function addAction($action){

		$this->actions[] = $action;

		return $this;
	}

	/**
	 * add the link action
	 */
	public function withLinkAction($text, $url, $variant = self::ACTION_VARIANT_PRIMARY, $target = ''){

		$action = '<a class="button button-' . $variant . '" href="' . esc_attr($url) . '" target="' . esc_attr($target) . '">' . $text . '</a>';

		return $this->addAction($action);
	}

	/**
	 * add the dismiss action
	 */
	public function withDismissAction($text, $variant = self::ACTION_VARIANT_SECONDARY){

		$ajaxUrl = $this->getDismissAjaxUrl();

		$action = '<a class="button button-' . $variant . '" href="#" data-action="dismiss" data-ajax-url="' . esc_attr($ajaxUrl) . '">' . $text . '</a>';

		return $this->addAction($action);
	}

	/**
	 * add the postpone action
	 */
	public function withPostponeAction($text, $duration, $variant = self::ACTION_VARIANT_SECONDARY){

		$ajaxUrl = $this->getPostponeAjaxUrl($duration);

		$action = '<a class="button button-' . $variant . '" href="#" data-action="postpone" data-ajax-url="' . esc_attr($ajaxUrl) . '">' . $text . '</a>';

		return $this->addAction($action);
	}

	/**
	 * get the notice html
	 */
	public function build(){

		$class = implode(' ', array(
			'notice',
			'notice-' . $this->color,
			'uc-admin-notice',
			'uc-admin-notice--' . $this->getId(),
		));

		$html = '<div class="' . esc_attr($class) . '">';
		$html .= '<div class="uc-notice-wrapper">';
		$html .= $this->getLogoHtml();
		$html .= '<div class="uc-notice-container">';
		$html .= $this->getHeadingHtml();
		$html .= $this->getContentHtml();
		$html .= $this->getActionsHtml();
		$html .= $this->getDebugHtml();
		$html .= '</div>';
		$html .= '</div>';
		$html .= $this->getDismissHtml();
		$html .= '</div>';

		return $html;
	}

	/**
	 * get the logo html
	 */
	private function getLogoHtml(){

		$logoUrl = GlobalsUC::$urlPluginImages . 'logo-circle.svg';

		return '<img class="uc-notice-logo" src="' . esc_attr($logoUrl) . '" alt="Logo" width="40" height="40" />';
	}

	/**
	 * get the heading html
	 */
	private function getHeadingHtml(){

		if(empty($this->heading))
			return '';

		return '<h3 class="uc-notice-heading">' . $this->heading . '</h3>';
	}

	/**
	 * get the content html
	 */
	private function getContentHtml(){

		if(empty($this->content))
			return '';

		return '<p class="uc-notice-content">' . $this->content . '</p>';
	}

	/**
	 * get actions html
	 */
	private function getActionsHtml(){

		if(empty($this->actions))
			return '';

		return '<div class="uc-notice-actions">' . implode('', $this->actions) . '</div>';
	}

}
