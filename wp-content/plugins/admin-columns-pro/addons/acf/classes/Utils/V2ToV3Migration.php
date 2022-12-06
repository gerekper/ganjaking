<?php

namespace ACA\ACF\Utils;

use AC\ListScreen;

class V2ToV3Migration {

	public function migrate_list_screen_settings( ListScreen $list_screen ) {
		$settings = $list_screen->get_settings();

		foreach ( $settings as $column_name => $setting ) {
			if ( 'column-acf_field' === $setting['type'] ) {
				$field = $setting['field'];
				$acf_field = acf_get_field( $setting['field'] );

				if ( $acf_field && $acf_field['type'] === 'group' && isset( $setting['sub_field'] ) ) {
					$field = 'acfgroup__' . $field . '-' . $setting['sub_field'];
				}

				$setting['type'] = $field;

				$settings[ $column_name ] = $setting;
			}
		}

		$list_screen->set_settings( $settings );
	}

}