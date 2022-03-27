<?php

namespace ACP\Sorting\Admin\Section;

use AC\Admin\Section;
use AC\View;

class ResetSorting extends Section {

	const NAME = 'reset-sorting';

	public function __construct() {
		parent::__construct( self::NAME );
	}

	public function render() {
		$form = ( new View() )->set_template( 'admin/page/settings-section-sorting' );

		$view = new View( [
			'title'       => __( 'Sorting Preferences', 'codepress-admin-columns' ),
			'description' => __( 'Reset the sorting preference for all users.', 'codepress-admin-columns' ),
			'content'     => $form->render(),
			'class'       => '-general',
		] );

		$view->set_template( 'admin/page/settings-section' );

		return $view->render();
	}

}