<?php

namespace GFML\Confirmation;

use GFFormDisplay;
use GFML_TM_API;
use WPML\FP\Fns;

class SaveAndContinue implements \IWPML_Frontend_Action, \IWPML_DIC_Action {

	/**
	 * @var GFML_TM_API
	 */
	private $tmApi;

	/**
	 * @param GFML_TM_API $tmApi
	 */
	public function __construct( GFML_TM_API $tmApi ) {
		$this->tmApi = $tmApi;
	}

	public function add_hooks() {
		add_filter( 'gform_get_form_save_email_confirmation_filter', Fns::withoutRecursion( Fns::identity(), [ $this, 'translate' ] ), 10, 2 );
	}

	/**
	 * @param string $text
	 * @param array  $form
	 *
	 * @return string
	 */
	public function translate( $text, $form ) {
		/* phpcs:ignore WordPress.Security.NonceVerification.Missing */
		$ajax = isset( $_POST['gform_ajax'] );
		$form = $this->tmApi->gform_pre_render( $form );

		return GFFormDisplay::handle_save_email_confirmation( $form, $ajax );
	}

}
