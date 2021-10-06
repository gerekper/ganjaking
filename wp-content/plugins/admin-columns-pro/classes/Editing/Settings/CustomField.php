<?php

namespace ACP\Editing\Settings;

use AC;
use ACP\Editing\Settings;

class CustomField extends Settings {

	protected function create_radio_element() {
		$radio = parent::create_radio_element();

		$radio = new AC\Settings\Form\Instruction(
			$radio,
			sprintf(
				'<p class="help-msg">%s</p>',
				sprintf(
					__( 'Learn more about %s.', 'codepress-admin-columns' ),
					sprintf(
						'<a target="_blank" href="%s#%s">%s</a>', esc_url( AC\Type\Url\Documentation::create_with_path( AC\Type\Url\Documentation::ARTICLE_CUSTOM_FIELD_EDITING ) ),
						'formats',
						__( 'custom field save formats', 'codepress-admin-columns' )
					)
				)
			)
		);

		return $radio;
	}

}