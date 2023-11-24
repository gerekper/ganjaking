<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCAdminNoticeBannerBuilder extends UCAdminNoticeBuilderAbstract{

	const THEME_DARK = 'dark';
	const THEME_LIGHT = 'light';

	private $theme = self::THEME_LIGHT;
	private $linkUrl;
	private $linkTarget;
	private $imageUrl;

	/**
	 * set the notice theme
	 */
	public function theme($theme){

		$this->theme = $theme;

		return $this;
	}

	/**
	 * set the notice link URL
	 */
	public function link($url, $target = ''){

		$this->linkUrl = $url;
		$this->linkTarget = $target;

		return $this;
	}

	/**
	 * set the notice image URL
	 */
	public function image($url){

		$this->imageUrl = $url;

		return $this;
	}

	/**
	 * get the notice html
	 */
	public function build(){

		$class = implode(' ', array(
			'notice',
			'uc-admin-notice',
			'uc-admin-notice--banner',
			'uc-admin-notice--theme-' . $this->theme,
			'uc-admin-notice--' . $this->getId(),
		));

		$html = '<div class="' . esc_attr($class) . '">';
		$html .= '<a class="uc-notice-link" href="' . esc_attr($this->linkUrl) . '" target="' . esc_attr($this->linkTarget) . '" >';
		$html .= $this->getImageHtml();
		$html .= '</a>';
		$html .= $this->getDebugHtml();
		$html .= $this->getDismissHtml();
		$html .= '</div>';

		return $html;
	}

	/**
	 * get the image html
	 */
	private function getImageHtml(){

		if(empty($this->imageUrl))
			return '';

		return '<img class="uc-notice-image" src="' . esc_attr($this->imageUrl) . '" alt="" />';
	}

}
