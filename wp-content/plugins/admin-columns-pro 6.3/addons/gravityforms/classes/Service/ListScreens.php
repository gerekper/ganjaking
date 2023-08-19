<?php

namespace ACA\GravityForms\Service;

use AC;
use AC\Registerable;
use AC\Table\ListKeyCollection;
use AC\Type\ListKey;
use GFAPI;

class ListScreens implements Registerable {

	public function register(): void
    {
		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_group' ] );
		add_action( 'ac/list_keys', [ $this, 'add_list_keys' ] );
	}

	public function register_list_screen_group( AC\Groups $groups ): void {
		$groups->add( 'gravity_forms', __( 'Gravity Forms', 'codepress-admin-columns' ), 8 );
	}

	public function add_list_keys( ListKeyCollection $keys ): ListKeyCollection {
		$forms = array_merge( GFAPI::get_forms(), GFAPI::get_forms( false ) );

		foreach ( $forms as $form ) {
			$keys->add( new ListKey( 'gf_entry_' . $form['id'] ) );
		}

		return $keys;
	}

}