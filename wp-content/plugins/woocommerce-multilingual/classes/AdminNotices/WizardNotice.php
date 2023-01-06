<?php

namespace WCML\AdminNotices;

class WizardNotice extends \WCML_Menu_Wrap_Base {

	/**
	 * @return array
	 */
	protected function get_child_model() {
		return [
			'strings'       => [
				'title' => \WCML_Admin_Menus::getWcmlLabel(),
			],
			'is_standalone' => false,
			'rate'          => [
				'on' => 0,
			],
			'content'       => ( new \WCML_Setup_Notice_UI() )->get_view( 'setup/wizard-notice.twig' ),
		];
	}

}
