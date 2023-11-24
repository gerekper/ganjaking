<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCAdminNoticeSimpleExample extends UCAdminNoticeAbstract{

	/**
	 * get the notice identifier
	 */
	public function getId(){

		return 'simple-example';
	}

	/**
	 * get the notice html
	 */
	public function getHtml(){

		$heading = __('Example of a simple notice', 'unlimited-elements-for-elementor');
		$content = __('Here is the content of a simple notice.', 'unlimited-elements-for-elementor');

		$linkText = __('Link Text', 'unlimited-elements-for-elementor');
		$linkUrl = 'https://google.com';
		$linkVariant = UCAdminNoticeBuilder::ACTION_VARIANT_PRIMARY;
		$linkTarget = '_blank';

		$builder = $this->createBuilder();
		$builder->dismissible();
		$builder->withHeading($heading);
		$builder->withContent($content);
		$builder->withLinkAction($linkText, $linkUrl, $linkVariant, $linkTarget);

		$html = $builder->build();

		return $html;
	}

	/**
	 * initialize the notice
	 */
	protected function init(){

		$this->setLocation(self::LOCATION_PLUGIN);
		$this->setDuration(8760); // 365 days in hours
	}

}
