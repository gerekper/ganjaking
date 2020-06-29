<?php

class FUE_JSON_Importer {

	private $json = null;
	public $num_imported = 0;
	public $num_skipped = 0;

	public function __construct( $json = null ) {
		if ( !is_null( $json ) ) {
			$this->json = $json;
		}
	}

	public function set_json( $json ) {
		$this->json = $json;
	}

	public function get_json() {
		return $this->json;
	}

	/**
	 * Import follow-ups from the JSON string
	 * @return bool|WP_Error
	 */
	public function import() {
		$this->num_imported = 0;
		$this->num_skipped  = 0;

		if ( !$this->json ) {
			return new WP_Error( 'fue_json_importer', __('JSON is not set', 'follow_up_emails') );
		}

		$follow_ups = json_decode( $this->json, true );

		if ( is_null( $follow_ups ) ) {
			return new WP_Error( 'fue_json_importer', __('Error parsing the JSON string', 'follow_up_emails') );
		}

		foreach ( $follow_ups as $follow_up ) {
			// skip if post_name exists - do not overwrite existing emails
			if ( !empty( $follow_up['post']['post_name'] ) && $this->post_name_exists( $follow_up['post']['post_name'] ) ) {
				$this->num_skipped++;
				continue;
			}

			$args = $this->get_post_args( $follow_up );

			$post_id = wp_insert_post( $args );

			if ( !$post_id ) {
				$this->num_skipped++;
				continue;
			}

			// set type
			wp_set_object_terms( $post_id, $follow_up['type'], 'follow_up_email_type' );

			$this->import_post_meta( $post_id, $follow_up['meta'] );

			if ( !empty( $follow_up['campaigns'] ) ) {
				wp_set_object_terms( $post_id, $follow_up['campaigns'], 'follow_up_email_campaign', false );
			}

			$this->num_imported++;
		}

		return true;
	}

	/**
	 * Checks if the same post name exists
	 * @param string $post_name
	 * @return bool
	 */
	private function post_name_exists( $post_name ) {
		global $wpdb;

		$post_name_count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$wpdb->posts}
			WHERE post_name = %s",
			$post_name
		) );

		return ( $post_name_count > 0 );
	}

	/**
	 * Return an array of post args
	 * @param array $follow_up
	 * @return array
	 */
	private function get_post_args( $follow_up ) {
		$args = array(
			'post_type'         => 'follow_up_email',
			'post_title'        => $follow_up['name'],
			'post_excerpt'      => $follow_up['subject'],
			'post_content'      => $follow_up['message'],
			'post_status'       => $follow_up['status'],
			'post_name'         => $follow_up['post']['post_name'],
			'menu_order'        => $follow_up['post']['menu_order'],
			'comment_status'    => $follow_up['post']['comment_status'],
			'ping_status'       => $follow_up['post']['ping_status'],
			'post_parent'       => $follow_up['post']['post_parent']
		);

		return $args;
	}

	private function import_post_meta( $post_id, $meta ) {
		foreach ( $meta as $meta_key => $meta_value ) {
			add_post_meta( $post_id, $meta_key, $meta_value );
		}
	}

}
