<?php
/**
 * Attachment Data Extra fields
 *
 * @package UAEL
 */

if ( ! class_exists( 'UAEL_Attachment' ) ) {

	/**
	 * Class UAEL_Attachment.
	 */
	class UAEL_Attachment {

		/**
		 * Constructor function that initializes required actions and hooks
		 *
		 * @since 1.0
		 */
		public function __construct() {

			add_filter( 'attachment_fields_to_edit', array( $this, 'custom_attachment_field_link' ), 10, 2 );
			add_filter( 'attachment_fields_to_save', array( $this, 'custom_attachment_field_link_save' ), 10, 2 );
		}

		/**
		 * Add CTA Link field to media uploader
		 *
		 * @param array  $form_fields fields to include in attachment form.
		 * @param object $post attachment record in database.
		 * @return aaray $form_fields modified form fields
		 */
		public function custom_attachment_field_link( $form_fields, $post ) {

			$form_fields['uael-custom-link'] = array(
				/* translators: %1$s: uael name. */
				'label' => sprintf( __( '%1s - Custom Link', 'uael' ), UAEL_PLUGIN_SHORT_NAME ),
				'input' => 'text',
				'value' => get_post_meta( $post->ID, 'uael-custom-link', true ),
			);

			$form_fields['uael-categories'] = array(
				/* translators: %1$s: uael name. */
				'label' => sprintf( __( '%1s - Categories (Ex: Cat1, Cat2)', 'uael' ), UAEL_PLUGIN_SHORT_NAME ),
				'input' => 'text',
				'value' => get_post_meta( $post->ID, 'uael-categories', true ),
			);

			return $form_fields;
		}


		/**
		 * Save values of CTA Link field in media uploader
		 *
		 * @param array $post the post data for database.
		 * @param array $attachment attachment fields from $_POST form.
		 * @return array $post modified post data.
		 */
		public function custom_attachment_field_link_save( $post, $attachment ) {

			if ( isset( $attachment['uael-categories'] ) ) {
				update_post_meta( $post['ID'], 'uael-categories', $attachment['uael-categories'] );
			}

			if ( isset( $attachment['uael-custom-link'] ) ) {
				update_post_meta( $post['ID'], 'uael-custom-link', $attachment['uael-custom-link'] );
			}

			return $post;
		}
	}

	new UAEL_Attachment();
}
