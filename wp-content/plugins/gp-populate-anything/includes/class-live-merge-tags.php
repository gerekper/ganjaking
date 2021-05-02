<?php

/**
 * Class GP_Populate_Anything_Live_Merge_Tags
 */
class GP_Populate_Anything_Live_Merge_Tags {

	private static $instance = null;

	private $_live_attrs_on_page            = array();
	private $_escapes                       = array();
	private $_current_live_merge_tag_values = array();
	private $_lmt_whitelist                 = array();

	public $live_merge_tag_regex_option_placeholder = '/(<option.*?class=\'gf_placeholder\'>)(.*?@({.*?:?.+?}).*?)<\/option>/';
	public $live_merge_tag_regex_option_choice      = '/(<option.*>)(.*?@({.*?:?.+?}).*?)<\/option>/';
	public $live_merge_tag_regex_textarea           = '/(<textarea.*>)([\S\s]*?@({.*?:?.+?})[\S\s]*?)<\/textarea>/';
	public $live_merge_tag_regex                    = '/@({((.*?):?(.+?))})/';
	public $merge_tag_regex                         = '/{((.*?):?([0-9]+?)?(:(.+?))?)}/';
	public $live_merge_tag_regex_attr               = '/([a-zA-Z-]+)=([\'"]([^\'"]*@{.*?:?.+?}[^\'"]*)(?<!\\\)[\'"])/';
	public $value_attr                              = '/value=\'/';
	public $script_regex                            = '/<script[\s\S]*?<\/script>/';

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_filter( 'gform_pre_render', array( $this, 'populate_lmt_whitelist' ), 5 );

		add_filter( 'gform_field_choice_markup_pre_render', array( $this, 'replace_live_merge_tag_select_field_option' ), 10, 4 );

		/**
		 * Prepare fields for LMTs such as adding data attributes and so on. Anything that is scoped to a specific
		 * input should be done here.
		 */
		foreach ( array( 'gform_field_content', 'gppa_hydrate_input_html' ) as $field_filter ) {
			add_filter( $field_filter, array( $this, 'replace_live_merge_tag_select_placeholder' ), 99, 2 );
			add_filter( $field_filter, array( $this, 'replace_live_merge_tag_textarea_default_value' ), 99, 2 );
			add_filter( $field_filter, array( $this, 'add_live_value_attr' ), 99, 2 );
			add_filter( $field_filter, array( $this, 'add_live_value_attr_textarea' ), 99, 2 );
			add_filter( $field_filter, array( $this, 'add_live_value_attr_radio_choice' ), 99, 2 );
			add_filter( $field_filter, array( $this, 'add_select_default_value_attr' ), 99, 2 );
		}

		/**
		 * After the fields/inputs have been prepared, we can process the entire form (or hydrated HTML) for LMTs.
		 */
		foreach ( array( 'gform_get_form_filter', 'gppa_hydrate_field_html' ) as $wrapper_filter ) {
			add_filter( $wrapper_filter, array( $this, 'preserve_scripts' ), 98, 2 );
			add_filter( $wrapper_filter, array( $this, 'preserve_product_field_label' ), 98, 2 );
			add_filter( $wrapper_filter, array( $this, 'replace_live_merge_tag_attr' ), 99, 2 );
			add_filter( $wrapper_filter, array( $this, 'replace_live_merge_tag_non_attr' ), 99, 2 );
			add_filter( $wrapper_filter, array( $this, 'unescape_live_merge_tags' ), 99, 2 );
			add_filter( $wrapper_filter, array( $this, 'add_localization_attr_variable' ), 99, 2 );
			add_filter( $wrapper_filter, array( $this, 'restore_escapes' ), 100, 2 );
		}

		add_filter( 'gppa_hydrate_field_html', array( $this, 'replace_live_merge_tag_textarea_default_value_hydrate_field' ), 99, 4 );
		add_filter( 'gform_field_choice_markup_pre_render', array( $this, 'replace_live_merge_tags_in_radio_choice_value' ), 10, 4 );

		add_filter( 'gform_replace_merge_tags', array( $this, 'replace_live_merge_tags_static' ), 10, 7 );
		add_filter( 'gform_admin_pre_render', array( $this, 'replace_field_label_live_merge_tags_static' ) );

		/**
		 * Prevent replacement of Live Merge Tags in Preview Submission.
		 */
		add_filter( 'gpps_pre_replace_merge_tags', array( $this, 'escape_live_merge_tags' ) );
		add_filter( 'gpps_post_replace_merge_tags', array( $this, 'unescape_live_merge_tags' ) );

	}


	/**
	 * Signal to the front end that a Live Merge Tag exists on the page so the frontend knows what elements to
	 * search for
	 *
	 * @param string|number $form_id
	 * @param string $live_merge_tag
	 */
	public function register_lmt_on_page( $form_id, $live_merge_tag ) {
		if ( ! isset( $this->_live_attrs_on_page[ $form_id ] ) ) {
			$this->_live_attrs_on_page[ $form_id ] = array();
		}

		$this->_live_attrs_on_page[ $form_id ][] = $live_merge_tag;
		$this->_live_attrs_on_page[ $form_id ]   = array_unique( $this->_live_attrs_on_page[ $form_id ] );
	}

	/**
	 * Add current live merge tag value to handle coupling of inputs on the frontend on initial load.
	 *
	 * @param string|number $form_id
	 * @param string $live_merge_tag
	 * @param string $live_merge_tag_value
	 */
	public function add_current_lmt_value( $form_id, $live_merge_tag, $live_merge_tag_value ) {
		if ( ! isset( $this->_current_live_merge_tag_values[ $form_id ] ) ) {
			$this->_current_live_merge_tag_values[ $form_id ] = array();
		}

		$this->_current_live_merge_tag_values[ $form_id ][ $live_merge_tag ] = $live_merge_tag_value;
	}

	/**
	 * Check whether or not a form contains Live Merge Tags
	 *
	 * @param string|number $form_id ID of form to check for Live Merge Tags.
	 * @return boolean
	 */
	public function form_has_lmts( $form_id ) {
		return count( rgar( $this->_lmt_whitelist, $form_id, array() ) ) > 0;
	}

	/**
	 * Get LMTs that have been set in the default value to whitelist the merge tags that can be used in a particular
	 * form.
	 *
	 * This helps prevent abuse with entry editing flows.
	 *
	 * @param $form
	 *
	 * @return array
	 */
	public function populate_lmt_whitelist( $form ) {

		if ( ! is_array( $form ) ) {
			return $form;
		}

		/* Do not populate if already populated. */
		if ( isset( $this->_lmt_whitelist[ $form['id'] ] ) ) {
			return $form;
		}

		$this->_lmt_whitelist[ $form['id'] ] = array();

		$iterator = new RecursiveIteratorIterator(
			new RecursiveArrayIterator( $form )
		);

		foreach ( $iterator as $key => $value ) {
			preg_match_all(
				$this->live_merge_tag_regex,
				$value,
				$merge_tag_matches,
				PREG_SET_ORDER
			);

			foreach ( $merge_tag_matches as $match ) {
				$merge_tag = preg_replace( '/^@/', '', $match[0] );

				if ( isset( $this->_lmt_whitelist[ $form['id'] ][ $merge_tag ] ) ) {
					continue;
				}

				$nonce_action                                      = 'gppa-lmt-' . $form['id'] . '-' . $merge_tag;
				$this->_lmt_whitelist[ $form['id'] ][ $merge_tag ] = wp_create_nonce( $nonce_action );
			}
		}

		return $form;
	}

	public function get_lmt_whitelist( $form ) {
		if ( gf_apply_filters( array( 'gppa_allow_all_lmts', $form['id'] ), false, $form ) ) {
			return null;
		}

		/**
		 * Filter the whitelist of Live Merge Tags for the current form.
		 *
		 * @since 1.0-beta-4.45
		 *
		 * @param array    $value The Live Merge Tag whitelist array. Each key is a Live Merge Tag, the values are nonces.
		 * @param array    $form  The current form.
		 */
		return gf_apply_filters( array( 'gppa_lmt_whitelist', $form['id'] ), rgar( $this->_lmt_whitelist, $form['id'] ), $form );
	}

	/**
	 * Gravity Forms outputs scripts in the form markup for things like conditional logic. Sometimes field settings
	 * such as the default value are included. Without intervention, the regular expressions in this class will match
	 * the Live Merge tags inside the JavaScript thus wreaking havoc and causing JavaScript errors.
	 *
	 * The easiest workaround is to shelve the JavaScript, run our replacements, and then re-add the JavaScript.
	 *
	 * @param $form_string
	 * @param $form
	 *
	 * @return string
	 */
	public function preserve_scripts( $form_string, $form ) {

		preg_match_all( $this->script_regex, $form_string, $matches, PREG_SET_ORDER );

		if ( ! $matches ) {
			return $form_string;
		}

		foreach ( $matches as $index => $match ) {
			$placeholder = "%%SCRIPT_FORM_{$form['id']}_{$index}%%";

			$this->_escapes[ $placeholder ] = $match[0];
			$form_string                    = str_replace( $match[0], $placeholder, $form_string );
		}

		return $form_string;

	}

	/**
	 * Gravity Forms validates Product fields using hashing and if the product name doesn't match due to a LMT on
	 * the Product field's label, it will fail validation.
	 *
	 * We need to escape the LMT on the hidden input that contains the product name.
	 *
	 * See ticket #13740
	 *
	 * @param $form_string
	 * @param $form
	 *
	 * @return string
	 */
	public function preserve_product_field_label( $form_string, $form ) {

		preg_match_all( '/ginput_container_singleproduct\'>[.\s]*?<input type=\'hidden\' name=\'input_\d+\.\d+\' value=\'(.*)?\' class=\'gform_hidden\' \/>/', $form_string, $matches, PREG_SET_ORDER );

		if ( ! $matches ) {
			return $form_string;
		}

		foreach ( $matches as $index => $match ) {
			$placeholder = "%%PRODUCT_NAME_{$form['id']}_{$index}%%";

			/**
			 * $search and $replace are needed since we're only replacing $match[0] inside of $match[1]
			 *
			 * Without this, it can get a bit aggressive and replace the LMT in other locations.
			 */
			$search  = $match[0];
			$replace = str_replace( $match[1], $placeholder, $search );

			$this->_escapes[ $placeholder ] = $match[1];
			$form_string                    = str_replace( $search, $replace, $form_string );
		}

		return $form_string;

	}

	public function restore_escapes( $form_string, $form ) {

		foreach ( $this->_escapes as $placeholder => $script ) {
			$form_string = str_replace( $placeholder, $script, $form_string );
		}

		return $form_string;

	}

	public function replace_live_merge_tag_attr( $form_string, $form ) {

		preg_match_all( $this->live_merge_tag_regex_attr, $form_string, $matches, PREG_SET_ORDER );

		if ( ! $matches ) {
			return $form_string;
		}

		foreach ( $matches as $match ) {
			$full_match = $match[0];
			$merge_tag  = $match[3];

			$output = $this->get_live_merge_tag_value( $merge_tag, $form );

			$replaced_attr = $match[1] . '="' . esc_attr( $output ) . '"';

			if ( strpos( $match[1], 'data-gppa-live-merge-tag' ) === 0 ) {
				continue;
			}

			$data_attr_name  = 'data-gppa-live-merge-tag-' . $match[1];
			$data_attr_value = $this->escape_live_merge_tags( $match[3] );
			$data_attr       = $data_attr_name . '="' . esc_attr( $data_attr_value ) . '"';

			$this->register_lmt_on_page( $form['id'], 'data-gppa-live-merge-tag-' . $match[1] );
			$this->add_current_lmt_value( $form['id'], $merge_tag, $output );

			$form_string = str_replace( $full_match, $replaced_attr . ' ' . $data_attr, $form_string );
		}

		return $form_string;

	}

	public function replace_live_merge_tag_select_placeholder( $content, $field ) {

		preg_match_all( $this->live_merge_tag_regex_option_placeholder, $content, $matches, PREG_SET_ORDER );

		if ( ! $matches ) {
			return $content;
		}

		$form = GFAPI::get_form( $field->formId );

		/**
		 * $match[0] = Entire <option>...</option> string
		 * $match[1] = Starting tag and attributes
		 * $match[2] = Inner HTML of option
		 * $match[3] = First live merge tag that's seen
		 */
		foreach ( $matches as $match ) {

			$full_match = $match[0];

			$output    = $this->get_live_merge_tag_value( $match[2], $form );
			$data_attr = 'data-gppa-live-merge-tag-innerHtml="' . esc_attr( $this->escape_live_merge_tags( $match[2] ) ) . '"';

			$class_string = "class='gf_placeholder'";

			$full_match_replacement = str_replace( $match[2], $output, $full_match );
			$full_match_replacement = str_replace( $class_string, $class_string . ' ' . $data_attr, $full_match_replacement );

			$this->register_lmt_on_page( $form['id'], 'data-gppa-live-merge-tag-innerHtml' );
			$this->add_current_lmt_value( $form['id'], $match[2], $output );

			$content = str_replace( $full_match, $full_match_replacement, $content );
		}

		return $content;

	}

	public function replace_live_merge_tag_textarea_default_value_hydrate_field( $content, $form, $result, $field ) {
		return $this->replace_live_merge_tag_textarea_default_value( $content, $field );
	}

	public function replace_live_merge_tag_textarea_default_value( $content, $field ) {

		preg_match_all( $this->live_merge_tag_regex_textarea, $content, $matches, PREG_SET_ORDER );

		if ( ! $matches || ! $field ) {
			return $content;
		}

		$form = GFAPI::get_form( $field->formId );

		/**
		 * $match[0] = Entire <textarea>...</textarea> string
		 * $match[1] = Starting tag and attributes
		 * $match[2] = Inner HTML of textarea
		 * $match[3] = First live merge tag that's seen
		 */
		foreach ( $matches as $match ) {

			$full_match = $match[0];

			$output    = $this->get_live_merge_tag_value( $match[2], $form );
			$data_attr = 'data-gppa-live-merge-tag-innerHtml="' . esc_attr( $this->escape_live_merge_tags( $match[2] ) ) . '"';

			$full_match_replacement = str_replace( $match[2], $output, $full_match );
			$full_match_replacement = str_replace( '<textarea ', '<textarea ' . $data_attr . ' ', $full_match_replacement );

			$this->register_lmt_on_page( $form['id'], 'data-gppa-live-merge-tag-innerHtml' );
			$this->add_current_lmt_value( $form['id'], $match[2], $output );

			$content = str_replace( $full_match, $full_match_replacement, $content );
		}

		return $content;

	}

	/**
	 * In some cases such as using a multi-page form, Gravity Forms will supply GPPA with form values which will overwrite
	 * the values that were initially LMTs. Because of this, LMTs won't be detected by the broad form filters that
	 * add in the data attr's for the LMTs.
	 *
	 * To get around this, we check if there are LMTs in the value and if not we re-add the data attr as long as there
	 * are LMTs in the field's default value.
	 *
	 * @param $content
	 * @param $field
	 *
	 * @return mixed
	 * @see GP_Populate_Anything_Live_Merge_Tags::add_live_value_attr_radio_choice() for choices
	 *
	 * @see GP_Populate_Anything_Live_Merge_Tags::add_live_value_attr_textarea() for textareas
	 */
	public function add_live_value_attr( $content, $field ) {

		preg_match_all( '/value=([\'"]([^\'"]*@{.*?:?.+?}[^\'"]*)(?<!\\\)[\'"])/', $content, $matches, PREG_SET_ORDER );

		/**
		 * If there are already LMTs in the value, then bail out since the filters for the entry form string will
		 * add in the data attrs.
		 */
		if ( $matches && count( $matches ) ) {
			return $content;
		}

		if ( ! preg_match( '/@{.*?:?.+?}/', $field->defaultValue ) ) {
			return $content;
		}

		$merge_tag_value = $this->get_live_merge_tag_value( $field->defaultValue, GFAPI::get_form( $field->formId ) );

		$this->register_lmt_on_page( $field->formId, 'data-gppa-live-merge-tag-value' );

		if ( $merge_tag_value ) {
			$this->add_current_lmt_value( $field->formId, $field->defaultValue, $merge_tag_value );
		}

		$data_attr = 'data-gppa-live-merge-tag-value="' . esc_attr( $this->escape_live_merge_tags( $field->defaultValue ) ) . '"';

		return str_replace( ' value=\'', ' ' . $data_attr . ' value=\'', $content );

	}

	/**
	 * In some cases such as using a multi-page form or nested form, Gravity Forms will supply GPPA with form values
	 * which will overwrite the values that were initially LMTs. Because of this, LMTs won't be detected by the broad
	 * form filters that add in the data attr's for the LMTs.
	 *
	 * To get around this, we check if there are LMTs in the value and if not we re-add the data attr as long as there
	 * are LMTs in the field's default value.
	 *
	 * @param $content
	 * @param $field
	 *
	 * @return mixed
	 * @see GP_Populate_Anything_Live_Merge_Tags::add_live_value_attr_radio_choice() for choices
	 *
	 * @see GP_Populate_Anything_Live_Merge_Tags::add_live_value_attr() for other inputs
	 */
	public function add_live_value_attr_textarea( $content, $field ) {

		preg_match_all( '/<textarea.*>([\s\S]*?)<\/textarea>/', $content, $matches, PREG_SET_ORDER );

		/**
		 * Skip if this field does not contain a textarea or the default value does NOT contain an LMT.
		 */
		if (
			( ! $matches || ! count( $matches ) )
			|| ! preg_match( '/@{.*?:?.+?}/', $field->defaultValue )
		) {
			return $content;
		}

		/**
		 * If there are already LMTs in the value, bail out since the filters for the entry form string will
		 * add in the data attrs.
		 */
		if ( preg_match( '/@{.*?:?.+?}/', $matches[0][1] ) ) {
			return $content;
		}

		$merge_tag_value = $this->get_live_merge_tag_value( $field->defaultValue, GFAPI::get_form( $field->formId ) );

		$this->register_lmt_on_page( $field->formId, 'data-gppa-live-merge-tag-innerHtml' );

		if ( $merge_tag_value ) {
			$this->add_current_lmt_value( $field->formId, $field->defaultValue, $merge_tag_value );
		}

		$data_attr = 'data-gppa-live-merge-tag-innerHtml="' . esc_attr( $this->escape_live_merge_tags( $field->defaultValue ) ) . '"';

		return str_replace( '<textarea ', '<textarea ' . $data_attr, $content );

	}

	/**
	 * Add in value LMT data attr if the value for a specific choice has already had its Live Merge Tags parsed.
	 * @see GP_Populate_Anything_Live_Merge_Tags::replace_live_merge_tags_in_radio_choice_value()
	 *
	 * @see GP_Populate_Anything_Live_Merge_Tags::add_live_value_attr() for other inputs
	 * @see GP_Populate_Anything_Live_Merge_Tags::add_live_value_attr_textarea() for textareas
	 *
	 * For additional context, see ticket #20452.
	 *
	 * @param $content
	 * @param $field GF_Field
	 *
	 * @return mixed
	 */
	public function add_live_value_attr_radio_choice( $content, $field ) {
		if ( ! $field->choices || $field->get_input_type() !== 'radio' ) {
			return $content;
		}

		foreach ( $field->choices as $choice_index => $choice ) {
			if (
				$this->has_live_merge_tag( $choice['value'] )
				&& strpos( $content, "value='{$choice['value']}'" ) === false
			) {
				$id_attr   = "id='choice_{$field->formId}_{$field->id}_{$choice_index}'";
				$data_attr = 'data-gppa-live-merge-tag-value="' . esc_attr( $this->escape_live_merge_tags( $choice['value'] ) ) . '"';
				$content   = str_replace( $id_attr, $id_attr . ' ' . $data_attr, $content );

				$this->register_lmt_on_page( $field->formId, 'data-gppa-live-merge-tag-value' );
			}
		}

		return $content;
	}

	/**
	 * @param $content
	 * @param $field
	 *
	 * @return mixed
	 */
	public function add_select_default_value_attr( $content, $field ) {

		preg_match_all( '/<select name=\'input_(\d+(\.\d+)?)\'/', $content, $matches, PREG_SET_ORDER );

		if ( ! $matches ) {
			return $content;
		}

		$default_values               = ! empty( $field->inputs ) ? $this->pluck( $field->inputs, 'defaultValue', 'id' ) : array();
		$default_values[ $field->id ] = $field->defaultValue;

		$has_lmt = false;

		foreach ( array_values( $default_values ) as $default_value ) {
			if ( preg_match( '/@{.*?:?.+?}/', $default_value ) ) {
				$has_lmt = true;
				break;
			}
		}

		if ( ! $has_lmt ) {
			return $content;
		}

		foreach ( $matches as $match ) {
			$input_id      = $match[1];
			$default_value = $default_values[ $input_id ];

			/**
			 * With future AJAX optimizations, we will need to output get_live_merge_tag_value for initial load.
			 */
			$data_attr = 'data-gppa-live-merge-tag-innerHtml="' . esc_attr( $this->escape_live_merge_tags( $default_value ) ) . '"';

			$this->register_lmt_on_page( $field->formId, 'data-gppa-live-merge-tag-innerHtml' );

			$content = str_replace( $match[0], $match[0] . ' ' . $data_attr, $content );
		}

		return $content;

	}

	public function pluck( $list, $field, $index_key = null ) {

		$newlist = array();

		if ( ! $index_key ) {
			/*
			 * This is simple. Could at some point wrap array_column()
			 * if we knew we had an array of arrays.
			 */
			foreach ( $list as $key => $value ) {
				if ( is_object( $value ) ) {
					$newlist[ $key ] = $value->$field;
				} else {
					$newlist[ $key ] = $value[ $field ];
				}
			}

			$list = $newlist;

			return $list;
		}

		/*
		 * When index_key is not set for a particular item, push the value
		 * to the end of the stack. This is how array_column() behaves.
		 */
		foreach ( $list as $value ) {
			if ( is_object( $value ) ) {
				if ( isset( $value->$index_key ) ) {
					$newlist[ $value->$index_key ] = $value->$field;
				} else {
					$newlist[] = $value->$field;
				}
			} else {
				if ( isset( $value[ $index_key ] ) ) {
					$newlist[ $value[ $index_key ] ] = rgar( $value, $field );
				} else {
					$newlist[] = rgar( $value, $field );
				}
			}
		}

		return $newlist;
	}

	public function replace_live_merge_tag_select_field_option( $choice_markup, $choice, $field, $value ) {

		preg_match_all( $this->live_merge_tag_regex_option_choice, $choice_markup, $matches, PREG_SET_ORDER );

		if ( ! $matches ) {
			return $choice_markup;
		}

		$form = GFAPI::get_form( $field->formId );

		/**
		 * $match[0] = Entire <option>...</option> string
		 * $match[1] = Starting tag and attributes
		 * $match[2] = Option label
		 * $match[3] = First live merge tag that's seen
		 */
		foreach ( $matches as $match ) {

			$full_match = $match[0];

			$output    = $this->get_live_merge_tag_value( $match[2], $form );
			$data_attr = 'data-gppa-live-merge-tag-innerHtml="' . esc_attr( $this->escape_live_merge_tags( $match[2] ) ) . '"';

			$full_match_replacement = str_replace( $match[2], $output, $full_match );
			$full_match_replacement = str_replace( '<option ', '<option ' . $data_attr . ' ', $full_match_replacement );

			$this->register_lmt_on_page( $form['id'], 'data-gppa-live-merge-tag-innerHtml' );
			$this->add_current_lmt_value( $form['id'], $match[2], $output );

			$choice_markup = str_replace( $full_match, $full_match_replacement, $choice_markup );
			// Remove empty values to default to innerHTML
			$choice_markup = str_replace( " value=''", '', $choice_markup );
		}

		return $choice_markup;

	}

	public function replace_live_merge_tag_non_attr( $form_string, $form ) {

		preg_match_all( $this->live_merge_tag_regex, $form_string, $matches, PREG_SET_ORDER );

		if ( ! $matches ) {
			return $form_string;
		}

		foreach ( $matches as $match ) {
			$full_match = $match[0];
			$merge_tag  = $match[1];

			$populated_merge_tag = $this->get_live_merge_tag_value( $merge_tag, $form );

			$span        = '<span data-gppa-live-merge-tag="' . esc_attr( $this->escape_live_merge_tags( $full_match ) ) . '">' . $populated_merge_tag . '</span>';
			$form_string = str_replace( $full_match, $span, $form_string );
		}

		return $form_string;

	}

	/**
	 * Escape live merge tags to prevent regex interference.
	 *
	 * @param $string
	 */
	public function escape_live_merge_tags( $string ) {
		return preg_replace( $this->live_merge_tag_regex, '#!GPPA!!$2!!GPPA!#', $string );
	}

	public function unescape_live_merge_tags( $form_string ) {
		return preg_replace( '/#!GPPA!!((.*?):?(.+?))!!GPPA!#/', '@{$1}', $form_string );
	}

	public function add_localization_attr_variable( $form_string, $form ) {
		if ( ! empty( $this->_live_attrs_on_page[ $form['id'] ] ) ) {
			wp_localize_script( 'gp-populate-anything', "GPPA_LIVE_ATTRS_FORM_{$form['id']}", array_values( array_unique( $this->_live_attrs_on_page[ $form['id'] ] ) ) );
		}

		if ( ! empty( $this->_current_live_merge_tag_values[ $form['id'] ] ) ) {
			/**
			 * We explicitly add this to the form string to add support for Live Merge Tags when editing nested entries
			 * with GP Nested Forms.
			 */
			$form_string .= '<script type="text/javascript">
				var GPPA_CURRENT_LIVE_MERGE_TAG_VALUES_FORM_' . $form['id'] . ' = ' . json_encode( $this->_current_live_merge_tag_values[ $form['id'] ] ) . ';
			</script>';
		}

		if ( $this->get_lmt_whitelist( $form ) ) {
			wp_localize_script( 'gp-populate-anything', "GPPA_LMT_WHITELIST_{$form['id']}", $this->get_lmt_whitelist( $form ) );
		}

		return $form_string;
	}

	public function extract_merge_tag_modifiers( $non_live_merge_tag ) {

		$merge_tag_parts = explode( ':', $non_live_merge_tag );

		if ( count( $merge_tag_parts ) < 3 ) {
			return array();
		}

		$modifiers       = array();
		$merge_tag_parts = array_slice( $merge_tag_parts, 2 );
		$modifiers_str   = rtrim( join( ':', $merge_tag_parts ), '}' );

		preg_match_all( '/([a-z]+)(?:(?:\[(.+?)\])|,?)/', $modifiers_str, $matches, PREG_SET_ORDER );

		foreach ( $matches as $match_group ) {
			$modifiers[ $match_group[1] ] = isset( $match_group[2] ) ? $match_group[2] : true;
		}

		return $modifiers;

	}

	/**
	 * Check if a field has empty inputs if all are needed. Example: Date field using inputs and not all three inputs
	 * have been filled out. Without all inputs filled out, Merge Tags typically return odd values.
	 *
	 * @param $field
	 * @param $form
	 *
	 * @return bool
	 */
	public function is_value_submission_empty( $entry_value, $field, $form ) {
		$is_empty = $field->is_value_submission_empty( $form['id'] );

		if ( $is_empty ) {
			return true;
		}

		/**
		 * GF 2.5 changed the behavior of is_value_submission_empty() and it won't return false if there are missing
		 * inputs like <GF 2.5 would.
		 *
		 * Fortunately, GF_Field->validate() has been changed and it's more suitable for this use-case.
		 */
		if ( version_compare( GFForms::$version, '2.5-beta-1', '>=' ) ) {
			$field->isRequired = true;
			$field->validate( $entry_value, $form );

			if ( ! empty( $field->validation_message ) ) {
				return true;
			}
		}

		return false;
	}

	public function get_live_merge_tag_value( $merge_tag, $form, $entry_values = null ) {

		$lmt_nonces = null;

		/**
		 * JSON is used here due to issues with modifiers causing merge tags to be truncated in $_REQUEST and $_POST
		 */
		if ( rgar( $_REQUEST, 'lmt-nonces' ) ) {
			$lmt_nonces = GFCommon::json_decode( stripslashes( rgar( $_REQUEST, 'lmt-nonces' ) ), true );
		}

		if ( ! $entry_values ) {
			$entry_values = gp_populate_anything()->get_posted_field_values( $form );
		}

		/**
		 * Use get_value_save_entry() to get a more accurate entry value for field types such as Date and Time.
		 */
		foreach ( $entry_values as $field_id => $entry_value ) {
			$field = GFAPI::get_field( $form, $field_id );

			if ( ! $field || ! in_array( $field['type'], GP_Populate_Anything::get_interpreted_multi_input_field_types(), true ) ) {
				continue;
			}

			if ( $this->is_value_submission_empty( $entry_value, $field, $form ) ) {
				$entry_values[ $field_id ] = null;
				continue;
			}

			$save_value = $field->get_value_save_entry( $entry_value, $form, $field_id, null, null );

			if ( ! $save_value ) {
				continue;
			}

			$entry_values[ $field_id ] = $save_value;
		}

		/**
		 * Change Live Merge Tags to regular merge tags.
		 */
		$merge_tag = preg_replace( $this->live_merge_tag_regex, '$1', $merge_tag );
		$output    = $merge_tag;

		/**
		 * Sometimes one Live Merge Tag can contain multiple Merge Tags.
		 *
		 * Loop through and replace them individually to detect which are blank so we can properly use fallback
		 * modifier.
		 */
		preg_match_all( $this->merge_tag_regex, $merge_tag, $merge_tag_matches, PREG_SET_ORDER );

		foreach ( $merge_tag_matches as $merge_tag_match ) {
			$merge_tag = $merge_tag_match[0];

			/**
			 * Filter if all Live Merge Tags should be allowed. This is disabled by default for security.
			 *
			 * @since 1.0-beta-4.52
			 *
			 * @param array    $value Whether or not all Live Merge Tags are allowed
			 * @param array    $form  The current form.
			 */
			if ( ! gf_apply_filters( array( 'gppa_allow_all_lmts', $form['id'] ), false, $form ) ) {
				/**
				 * Verify that LMT was supplied by trusted source and not injected.
				 */
				$nonce_action = 'gppa-lmt-' . $form['id'] . '-' . $merge_tag;

				$lmt_whitelist = $this->get_lmt_whitelist( $form );

				if ( $lmt_nonces ) {
					if ( ! wp_verify_nonce( rgar( $lmt_nonces, $merge_tag ), $nonce_action ) ) {
						gp_populate_anything()->log_debug( 'Live Merge Tag is not valid for merge tag: ' . $merge_tag );
						$output = str_replace( $merge_tag, '', $output );

						continue;
					}
				} elseif ( ! isset( $lmt_whitelist[ $merge_tag ] ) ) {
					gp_populate_anything()->log_debug( 'Live Merge Tag nonce not found for merge tag: ' . $merge_tag );
					$output = str_replace( $merge_tag, '', $output );

					continue;
				}
			}

			// We probably should create a proper GF entry but for now, we'll add the currency property to prevent a
			// series of notices generated by passing the $entry_values to GFCommon::replace_variables() below when
			// {all_fields} or {pricing_fields} are used on the form.
			if ( ! isset( $entry_values['currency'] ) ) {
				$entry_values['currency'] = GFCommon::get_currency();
			}

			$merge_tag_match_value_html = GFCommon::replace_variables( $merge_tag, $form, $entry_values, false, false, false );

			/**
			 * If the merge tag is returning HTML, use it. We check if the string is actually HTML by utilizing
			 * strip_tags. This will ensure that Live Merge Tags containing {all_fields} or similar continue to work
			 * as expected.
			 *
			 * Otherwise, we need to use the merge tag result when the format is text to avoid an issue where
			 * HTML entities get escaped and break coupling/decoupling when users enter characters such as & in
			 * fields that are depended upon.
			 */
			if ( strip_tags( $merge_tag_match_value_html ) !== $merge_tag_match_value_html ) {
				$merge_tag_match_value = $merge_tag_match_value_html;
			} else {
				$merge_tag_match_value = GFCommon::replace_variables( $merge_tag, $form, $entry_values, false, false, false, 'text' );
			}

			$merge_tag_modifiers = $this->extract_merge_tag_modifiers( $merge_tag );

			if ( ( $fallback = rgar( $merge_tag_modifiers, 'fallback' ) ) && ! $merge_tag_match_value ) {
				$merge_tag_match_value = $fallback;
			}

			// Return field ID for field-specific merge tags; otherwise, return generic merge tag (e.g. "all_fields").
			// For input-specific merge tags (e.g. {:1.6}) desired match is at index 5. For field-specific merge tags (e.g. {:1}), it's 3.
			$field_id = rgar( $merge_tag_match, 5, rgar( $merge_tag_match, 3, $merge_tag_match[1] ) );
			/**
			 * Filter the live merge tag value.
			 *
			 * @since 1.0-beta-4.35
			 *
			 * @param string|int $merge_tag_match_value The value with which the live merge tag will be replaced.
			 * @param string     $merge_tag_match       The merge tag that is being replaced.
			 * @param array      $form                  The current form object.
			 * @param int        $field_id              The field ID targeted by the current merge tag.
			 * @param array      $entry_values          An array of values that should be used to determine the value with which to replace the merge tag.
			 */
			$merge_tag_match_value = gf_apply_filters( array( 'gppa_live_merge_tag_value', $form['id'], $field_id ), $merge_tag_match_value, $merge_tag, $form, $field_id, $entry_values );

			$output = str_replace( $merge_tag, $merge_tag_match_value, $output );
		}

		/**
		 * Handle recursive Live Merge Tags.
		 */
		while ( preg_match_all( $this->live_merge_tag_regex, $output, $populated_merge_tag_matches, PREG_SET_ORDER ) ) {
			$output = $this->get_live_merge_tag_value( $output, $form, $entry_values, $lmt_nonces );
		}

		return $output;

	}

	public function replace_live_merge_tags( $text, $form, $entry = null ) {
		preg_match_all( $this->live_merge_tag_regex, $text, $matches, PREG_SET_ORDER );

		if ( ! $matches ) {
			return $text;
		}

		foreach ( $matches as $match ) {
			$full_match = $match[0];
			$merge_tag  = $match[1];

			/**
			 * Prevent recursion.
			 */
			remove_filter( 'gform_replace_merge_tags', array( $this, 'replace_live_merge_tags_static' ), 10 );
			$populated_merge_tag = $this->get_live_merge_tag_value( $merge_tag, $form, $entry );
			add_filter( 'gform_replace_merge_tags', array( $this, 'replace_live_merge_tags_static' ), 10, 7 );

			$text = str_replace( $full_match, $populated_merge_tag, $text );
		}

		return $text;
	}

	/**
	 * In some cases, live merge tags should be replaced statically without the need to make them "live" (i.e. in field
	 * labels when rendering the {all_fields} merge tag).
	 *
	 * @return string $text
	 */
	public function replace_live_merge_tags_static( $text, $form, $entry = null, $url_encode = false, $esc_html = false, $nl2br = false, $format = 'html' ) {

		if ( ! $entry ) {
			return $text;
		}

		return $this->replace_live_merge_tags( $text, $form, $entry );

	}

	public function replace_field_label_live_merge_tags_static( $form ) {

		$entry = false;
		if ( in_array( GFForms::get_page(), array( 'entry_detail', 'entry_detail_edit' ) ) ) {
			$entry = GFAPI::get_entry( rgget( 'lid' ) );
		}

		if ( ! $entry || is_wp_error( $entry ) ) {
			return $form;
		}

		foreach ( $form['fields'] as $field ) {
			$field->label = $this->replace_live_merge_tags_static( $field->label, $form, $entry );
		}

		return $form;
	}

	public function has_live_merge_tag( $string ) {
		preg_match_all( $this->live_merge_tag_regex, $string, $matches, PREG_SET_ORDER );
		return (bool) count( $matches );
	}

	/**
	 * When using Live Merge Tags in radio-based choice values, the selected radio will be lost when navigating
	 * multi-page forms.
	 *
	 * @param $choice_markup string
	 * @param $choice array
	 * @param $field GF_Field_Radio
	 * @param $value string
	 *
	 * @see GP_Populate_Anything_Live_Merge_Tags::add_live_value_attr_radio_choice() which re-adds the data attribute to make
	 *   the LMT reactive on the frontend.
	 *
	 * For additional context, see ticket #20452.
	 *
	 */
	public function replace_live_merge_tags_in_radio_choice_value( $choice_markup, $choice, $field, $value ) {
		if ( $field->get_input_type() !== 'radio' ) {
			return $choice_markup;
		}

		if ( ! $this->has_live_merge_tag( $choice['value'] ) ) {
			return $choice_markup;
		}

		$form            = GFAPI::get_Form( $field->formId );
		$choice['value'] = $this->replace_live_merge_tags( $choice['value'], $form );

		/**
		 * If there are still merge tags after performing a replacement, bail here to prevent infinite loop as
		 * this recursively calls $field->get_choice_html().
		 */
		if ( $this->has_live_merge_tag( $choice['value'] ) ) {
			return $choice_markup;
		}

		$is_entry_detail = $field->is_entry_detail();
		$is_form_editor  = $field->is_form_editor();
		$is_admin        = $is_entry_detail || $is_form_editor;

		$disabled_text = $is_form_editor ? 'disabled="disabled"' : '';

		$choice_id_pattern = '/\'gchoice_[0-9_]*?_(\d+)\'/';

		preg_match( $choice_id_pattern, $choice_markup, $choice_id_match );

		if ( ! $choice_id_match || ! is_numeric( $choice_id_match[1] ) ) {
			return $choice_markup;
		}

		$choice_id = (int) $choice_id_match[1];

		return $field->get_choice_html( $choice, $choice_id, $value, $disabled_text, $is_admin );
	}

}
