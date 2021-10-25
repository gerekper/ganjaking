<?php

namespace WPForms\SmartTags;

/**
 * Class SmartTags.
 *
 * @since 1.6.7
 */
class SmartTags {

	/**
	 * List of smart tags.
	 *
	 * @since 1.6.7
	 *
	 * @var array
	 */
	protected $smart_tags = [];

	/**
	 * Hooks.
	 *
	 * @since 1.6.7
	 */
	public function hooks() {

		add_filter( 'wpforms_process_smart_tags', [ $this, 'process' ], 10, 4 );
		add_filter( 'wpforms_builder_enqueues_smart_tags', [ $this, 'builder' ] );
	}

	/**
	 * Approved smart tags.
	 *
	 * @codeCoverageIgnore
	 *
	 * @since      1.0.0
	 * @deprecated 1.6.7
	 *
	 * @param string $return Type of data to return.
	 *
	 * @return string|array
	 */
	public function get( $return = 'array' ) {

		_deprecated_argument( __METHOD__, '1.6.7' );
		_deprecated_function( __METHOD__, '1.6.7', __CLASS__ . '->get_smart_tags()' );

		$tags = $this->get_smart_tags();

		if ( $return !== 'list' ) {
			return $tags;
		}

		// Return formatted list.
		$output = '<ul class="smart-tags-list">';

		foreach ( $tags as $key => $tag ) {
			$output .= '<li><a href="#" data-value="' . esc_attr( $key ) . '">' . esc_html( $tag ) . '</a></li>';
		}
		$output .= '</ul>';

		return $output;
	}

	/**
	 * Get list of smart tags.
	 *
	 * @since 1.6.7
	 *
	 * @return array
	 */
	public function get_smart_tags() {

		if ( ! empty( $this->smart_tags ) ) {
			return $this->smart_tags;
		}

		/**
		 * Modify smart tags list.
		 *
		 * @since 1.4.0
		 *
		 * @param array The list of smart tags.
		 */
		$this->smart_tags = (array) apply_filters(
			'wpforms_smart_tags',
			$this->smart_tags_list()
		);

		return $this->smart_tags;
	}

	/**
	 * Get list of registered smart tags.
	 *
	 * @since 1.6.7
	 *
	 * @return array
	 */
	protected function smart_tags_list() {

		return [
			'admin_email'       => esc_html__( 'Site Administrator Email', 'wpforms-lite' ),
			'field_id'          => esc_html__( 'Field ID', 'wpforms-lite' ),
			'field_html_id'     => esc_html__( 'Field HTML ID', 'wpforms-lite' ),
			'field_value_id'    => esc_html__( 'Field Value', 'wpforms-lite' ),
			'form_id'           => esc_html__( 'Form ID', 'wpforms-lite' ),
			'form_name'         => esc_html__( 'Form Name', 'wpforms-lite' ),
			'page_title'        => esc_html__( 'Embedded Post/Page Title', 'wpforms-lite' ),
			'page_url'          => esc_html__( 'Embedded Post/Page URL', 'wpforms-lite' ),
			'page_id'           => esc_html__( 'Embedded Post/Page ID', 'wpforms-lite' ),
			'date'              => esc_html__( 'Date', 'wpforms-lite' ),
			'query_var'         => esc_html__( 'Query String Variable', 'wpforms-lite' ),
			'user_ip'           => esc_html__( 'User IP Address', 'wpforms-lite' ),
			'user_id'           => esc_html__( 'User ID', 'wpforms-lite' ),
			'user_display'      => esc_html__( 'User Display Name', 'wpforms-lite' ),
			'user_full_name'    => esc_html__( 'User Full Name', 'wpforms-lite' ),
			'user_first_name'   => esc_html__( 'User First Name', 'wpforms-lite' ),
			'user_last_name'    => esc_html__( 'User Last Name', 'wpforms-lite' ),
			'user_email'        => esc_html__( 'User Email', 'wpforms-lite' ),
			'user_meta'         => esc_html__( 'User Meta', 'wpforms-lite' ),
			'author_id'         => esc_html__( 'Author ID', 'wpforms-lite' ),
			'author_display'    => esc_html__( 'Author Name', 'wpforms-lite' ),
			'author_email'      => esc_html__( 'Author Email', 'wpforms-lite' ),
			'url_referer'       => esc_html__( 'Referrer URL', 'wpforms-lite' ),
			'url_login'         => esc_html__( 'Login URL', 'wpforms-lite' ),
			'url_logout'        => esc_html__( 'Logout URL', 'wpforms-lite' ),
			'url_register'      => esc_html__( 'Register URL', 'wpforms-lite' ),
			'url_lost_password' => esc_html__( 'Lost Password URL', 'wpforms-lite' ),
		];
	}

	/**
	 * Get all smart tags in the content.
	 *
	 * @since 1.6.7
	 *
	 * @param string $content Content.
	 *
	 * @return array
	 */
	private function get_all_smart_tags( $content ) {

		preg_match_all( '~{([a-z0-9_]+)(|[\s=][\w\s="\'\\\/:|-]+)}~', $content, $smart_tags );

		return array_combine( $smart_tags[0], $smart_tags[1] );
	}

	/**
	 * Replace smart tags.
	 *
	 * @since 1.6.7
	 *
	 * @param string $content   Content.
	 * @param array  $form_data Form data.
	 * @param array  $fields    List of fields.
	 * @param string $entry_id  Entry ID.
	 *
	 * @return string
	 */
	public function process( $content, $form_data, $fields = [], $entry_id = '' ) {

		$smart_tags = $this->get_all_smart_tags( $content );

		if ( empty( $smart_tags ) ) {
			return $content;
		}

		foreach ( $smart_tags as $smart_tag => $tag_name ) {

			$class = $this->get_smart_tag_class_name( $tag_name );

			if ( ! $class ) {
				/**
				 * Modify content for unregistered smart tags.
				 *
				 * @since      1.4.0
				 * @deprecated 1.6.7
				 *
				 * @param string Content of the Smart Tag.
				 * @param string Tag name of the Smart Tag.
				 */
				$content = (string) apply_filters_deprecated(
					'wpforms_smart_tag_process',
					[ $content, $tag_name ],
					'1.6.7',
					'wpforms_smarttags_process_{$tag_name}_value'
				);

				continue;
			}

			/**
			 * Modify a smart tag value.
			 *
			 * @since 1.6.7
			 *
			 * @param string Smart Tag value.
			 * @param array  Form data.
			 * @param string List of fields.
			 * @param int    Entry ID.
			 */
			$value = (string) apply_filters(
				"wpforms_smarttags_process_{$tag_name}_value",
				( new $class( $smart_tag ) )->get_value( $form_data, $fields, $entry_id ),
				$form_data,
				$fields,
				$entry_id
			);

			$content = $this->replace( $smart_tag, $value, $content );
		}

		return $content;
	}

	/**
	 * Determine if the smart tag is registered.
	 *
	 * @since 1.6.7
	 *
	 * @param string $smart_tag_name Smart tag name.
	 *
	 * @return bool
	 */
	protected function has_smart_tag( $smart_tag_name ) {

		return array_key_exists( $smart_tag_name, $this->get_smart_tags() );
	}

	/**
	 * Get smart tag class name.
	 *
	 * @since 1.6.7
	 *
	 * @param string $smart_tag_name Smart tag name.
	 *
	 * @return string
	 */
	protected function get_smart_tag_class_name( $smart_tag_name ) {

		if ( ! $this->has_smart_tag( $smart_tag_name ) ) {
			return '';
		}

		$class_name = str_replace( ' ', '', ucwords( str_replace( '_', ' ', $smart_tag_name ) ) );

		$full_class_name = '\\WPForms\\SmartTags\\SmartTag\\' . $class_name;

		if ( class_exists( $full_class_name ) ) {
			return $full_class_name;
		}

		/**
		 * Modify a smart tag class name that describes the smart tag logic.
		 *
		 * @since 1.6.7
		 *
		 * @param string The value.
		 * @param string Smart tag name.
		 */
		$full_class_name = apply_filters( 'wpforms_smarttags_get_smart_tag_class_name', '', $smart_tag_name );

		return class_exists( $full_class_name ) ? $full_class_name : '';
	}

	/**
	 * Retrieve the builder's special tags.
	 *
	 * @since 1.6.7
	 *
	 * @return array
	 */
	protected function get_replacement_builder_tags() {

		return [
			'date'      => 'date format="m/d/Y"',
			'query_var' => 'query_var key=""',
			'user_meta' => 'user_meta key=""',
		];
	}

	/**
	 * Hide smart tags in the builder.
	 *
	 * @since 1.6.7
	 *
	 * @return array
	 */
	protected function get_hidden_builder_tags() {

		return [
			'field_id',
			'field_html_id',
			'field_value_id',
		];
	}

	/**
	 * Builder tags.
	 *
	 * @since 1.6.7
	 *
	 * @return array
	 */
	public function builder() {

		$smart_tags       = $this->get_smart_tags();
		$replacement_tags = $this->get_replacement_builder_tags();
		$hidden_tags      = $this->get_hidden_builder_tags();

		foreach ( $replacement_tags as $tag => $replacement_tag ) {
			$smart_tags = wpforms_array_insert( $smart_tags, [ $replacement_tag => $smart_tags[ $tag ] ], $tag );

			unset( $smart_tags[ $tag ] );
		}

		foreach ( $hidden_tags as $hidden_tag ) {
			unset( $smart_tags[ $hidden_tag ] );
		}

		return $smart_tags;
	}

	/**
	 * Replace a found smart tag with the final value.
	 *
	 * @since 1.6.7
	 *
	 * @param string $tag     The tag.
	 * @param string $value   The value.
	 * @param string $content Content.
	 *
	 * @return string
	 */
	private function replace( $tag, $value, $content ) {

		return str_replace( $tag, strip_shortcodes( $value ), $content );
	}

	/**
	 * Replace a found smart tag with the final value.
	 *
	 * @codeCoverageIgnore
	 *
	 * @since      1.5.9
	 * @deprecated 1.6.7
	 *
	 * @param string $tag     The tag.
	 * @param string $value   The value.
	 * @param string $content Content.
	 *
	 * @return string
	 */
	public function parse( $tag, $value, $content ) {

		_deprecated_function( __METHOD__, '1.6.7' );

		return $this->replace( $tag, $value, $content );
	}
}
