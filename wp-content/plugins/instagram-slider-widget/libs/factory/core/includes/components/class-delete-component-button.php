<?php

namespace WBCR\Factory_439\Components;

/**
 * This file groups the settings for quick setup
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 16.09.2017, Webcraftic
 * @version       1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

require_once 'class-install-component-button.php';

class Delete_Button extends Install_Button {

	/**
	 * @throws \Exception
	 */
	protected function build_wordpress()
	{
		parent::build_wordpress();

		$this->action = 'delete';
		$this->add_data('plugin-action', $this->action);
		$this->remove_class('button-primary');
	}

	protected function build_internal()
	{
		// nothing
	}

	/**
	 * @param bool $echo
	 *
	 * @return string|void
	 */
	public function get_button()
	{
		$button = '<a href="#" class="' . implode(' ', $this->get_classes()) . '" ' . implode(' ', $this->get_data()) . '><span class="dashicons dashicons-trash"></span></a>';

		if( $this->type == 'internal' || !$this->is_plugin_install() || $this->is_plugin_activate() ) {
			$button = '';
		}

		return $button;
	}
}

