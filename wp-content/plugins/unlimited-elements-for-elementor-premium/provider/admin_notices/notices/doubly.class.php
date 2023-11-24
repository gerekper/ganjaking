<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCAdminNoticeDoubly extends UCAdminNoticeAbstract{

	/**
	 * get the notice identifier
	 */
	public function getId(){

		return 'doubly';
	}

	/**
	 * get the notice html
	 */
	public function getHtml(){

		$heading = __('Live Copy Paste from Unlimited Elements', 'unlimited-elements-for-elementor');
		$content = __('Did you know that now you can copy fully designed sections from Unlimited Elements to your website for FREE? <br /> If you want to try then install our new plugin called Doubly.', 'unlimited-elements-for-elementor');

		$installText = __('Install Doubly Now', 'unlimited-elements-for-elementor');
		$installUrl = UniteFunctionsWPUC::getInstallPluginLink('doubly');
		$installUrl = UniteFunctionsUC::addUrlParams($installUrl, array('uc_dismiss_notice' => $this->getId()));

		$builder = $this->createBuilder();
		$builder->dismissible();
		$builder->color(UCAdminNoticeBuilder::COLOR_DOUBLY);
		$builder->withHeading($heading);
		$builder->withContent($content);
		$builder->withLinkAction($installText, $installUrl);

		$html = $builder->build();

		return $html;
	}

	/**
	 * initialize the notice
	 */
	protected function init(){

		$this->setDuration(48); // 2 days in hours
	}

	/**
	 * check if the notice condition is allowed
	 */
	protected function isConditionAllowed(){

		// check if the Doubly plugin is installed
		if(defined('DOUBLY_INC'))
			return false;

		return true;
	}

}
