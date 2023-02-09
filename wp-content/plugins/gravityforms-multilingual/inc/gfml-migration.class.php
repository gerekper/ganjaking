<?php

class GFML_Migration {

	/** @var GFML_TM_API */
	private $tm_api;

	/**
	 * @param GFML_TM_API $tm_api
	 */
	public function __construct( $tm_api ) {
		$this->tm_api = $tm_api;
	}

	public function migrate() {
		global $wpdb;

		// todo: Add caching.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$form_ids = $wpdb->get_col(
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT id FROM {$this->tm_api->get_forms_table_name()}"
		);

		foreach ( $form_ids as $id ) {
			$form = RGFormsModel::get_form_meta( $id );
			$this->tm_api->update_form_translations( $form, true );
			$wpdb->delete(
				$wpdb->prefix . 'icl_translations',
				[
					'element_id'   => $id,
					'element_type' => 'post_gravity_form',
				]
			);

			$this->migrate_old_translated_values( $id );
		}
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
	}

	private function migrate_old_translated_values( $form_id ) {
		global $wpdb;

		$st_context   = $this->tm_api->get_st_context( $form_id );
		$form_strings = array_keys( $this->tm_api->get_form_strings( $form_id ) );
		foreach ( $form_strings as &$string_name ) {
			$string_name = "{$form_id}_" . $string_name;
		}
		$s_name_in = wpml_prepare_in( $form_strings );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$id_s_needing_update = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT st.id AS id, cs.id AS right_id
                 FROM {$wpdb->prefix}icl_strings s
                 JOIN {$wpdb->prefix}icl_strings cs
                  ON CONCAT(%s,cs.name) = s.name
                 JOIN {$wpdb->prefix}icl_string_translations st
                  ON s.id = st.string_id
                 LEFT JOIN {$wpdb->prefix}icl_string_translations cst
                  ON cs.id = cst.string_id AND st.language = cst.language
                 WHERE s.context = 'gravity_form'
                  AND cs.context = %s
                  AND cst.language IS NULL
                  AND s.name IN ({$s_name_in})",
				$form_id . '_',
				$st_context
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		foreach ( $id_s_needing_update as $id_data ) {
			$wpdb->update(
				$wpdb->prefix . 'icl_string_translations',
				[ 'string_id' => $id_data->right_id ],
				[ 'id' => $id_data->id ]
			);

			icl_update_string_status( $id_data->right_id );
			icl_update_string_status( $id_data->id );
		}
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
	}
}
