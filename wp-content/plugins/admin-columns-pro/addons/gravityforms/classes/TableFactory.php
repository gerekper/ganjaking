<?php

namespace ACA\GravityForms;

use GF_Entry_List_Table;
use GFCommon;

class TableFactory {

	/**
	 * @param string $screen_id
	 * @param int    $form_id
	 *
	 * @return GF_Entry_List_Table
	 */
	public function create( $screen_id, $form_id ) {
		require_once( GFCommon::get_base_path() . '/entry_list.php' );

		return new GF_Entry_List_Table( [
			'screen'  => $screen_id,
			'form_id' => $form_id,
		] );
	}

}