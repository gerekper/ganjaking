<?php

/**
 * Class WPML_GFML_Meta_Update
 */
class WPML_GFML_Filter_Field_Meta implements \IWPML_Backend_Action, \IWPML_Frontend_Action, \IWPML_DIC_Action {

	/**
	 * @var null|string
	 */
	public $current_language;

	public function __construct( SitePress $sitepress ) {
		$this->current_language = $sitepress->get_current_language();
	}

	public function add_hooks() {
		add_filter( 'gform_form_post_get_meta', [ $this, 'filter_taxonomy_terms' ] );
	}

	/**
	 * @param array $field_data
	 *
	 * @return array
	 */
	public function filter_taxonomy_terms( $field_data ) {
		if ( is_array( $field_data['fields'] ) ) {
			/** @var GF_Field $field */
			foreach ( $field_data['fields'] as &$field ) {
				if ( ! empty( $field->choices ) && 'post_category' === $field->type ) {
					foreach ( $field->choices as &$choice ) {
						$tr_cat = apply_filters( 'wpml_object_id', $choice['value'], 'category', false, $this->current_language );
						if ( null !== $tr_cat ) {
							/** @var WP_Term|WP_Error|null $tr_cat */
							$tr_cat = get_category( $tr_cat );
							if ( ! is_wp_error( $tr_cat ) && $tr_cat ) {
								$choice['value'] = $tr_cat->term_id;
								$choice['text']  = $tr_cat->name;
							}
						}
					}
				}
			}
		}

		return $field_data;
	}
}
