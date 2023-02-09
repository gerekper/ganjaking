<?php

use WPML\FP\Obj;

define( 'ICL_GRAVITY_FORM_ELEMENT_TYPE', 'gravity_form' );

/**
 * Class Gravity_Forms_Multilingual
 * - Registers and updates WPML translation jobs
 * - Enables GF forms on WPML TM Dashboard screen
 * - Filters GF form on frontend ('gform_pre_render')
 * - Translates notifications
 */
abstract class Gravity_Forms_Multilingual {

	const FORMS_TABLE        = 'gf_form';
	const LEGACY_FORMS_TABLE = 'rg_form';

	/** @var array */
	protected $current_forms;

	/** @var array */
	protected $form_fields;

	/** @var string */
	private $forms_table_name;

	/** @var array */
	private $forms_to_update_on_shutdown = [];

	/** @var array */
	private $field_keys = [
		'label',
		'checkboxLabel',
		'adminLabel',
		'description',
		'defaultValue',
		'errorMessage',
	];

	/**
	 * Set of languages supported by reCAPTCHA
	 *
	 * @var array */
	private $recaptcha_supported_languages = [
		'af'     => 1,
		'am'     => 1,
		'ar'     => 1,
		'az'     => 1,
		'bg'     => 1,
		'bn'     => 1,
		'ca'     => 1,
		'cs'     => 1,
		'da'     => 1,
		'de-AT'  => 1,
		'de-CH'  => 1,
		'de'     => 1,
		'el'     => 1,
		'en-GB'  => 1,
		'en'     => 1,
		'es-419' => 1,
		'es'     => 1,
		'et'     => 1,
		'eu'     => 1,
		'fa'     => 1,
		'fi'     => 1,
		'fil'    => 1,
		'fr-CA'  => 1,
		'fr'     => 1,
		'gl'     => 1,
		'gu'     => 1,
		'hi'     => 1,
		'hr'     => 1,
		'hu'     => 1,
		'hy'     => 1,
		'id'     => 1,
		'is'     => 1,
		'it'     => 1,
		'iw'     => 1,
		'ja'     => 1,
		'ka'     => 1,
		'kn'     => 1,
		'ko'     => 1,
		'lo'     => 1,
		'lt'     => 1,
		'lv'     => 1,
		'ml'     => 1,
		'mn'     => 1,
		'mr'     => 1,
		'ms'     => 1,
		'nl'     => 1,
		'no'     => 1,
		'pl'     => 1,
		'pt-BR'  => 1,
		'pt-PT'  => 1,
		'pt'     => 1,
		'ro'     => 1,
		'ru'     => 1,
		'si'     => 1,
		'sk'     => 1,
		'sl'     => 1,
		'sr'     => 1,
		'sv'     => 1,
		'sw'     => 1,
		'ta'     => 1,
		'te'     => 1,
		'th'     => 1,
		'tr'     => 1,
		'uk'     => 1,
		'ur'     => 1,
		'vi'     => 1,
		'zh-CN'  => 1,
		'zh-HK'  => 1,
		'zh-TW'  => 1,
		'zu'     => 1,
	];

	/** @var array */
	private $form_keys;

	/**
	 * Registers filters and hooks.
	 * Called on 'init' hook at default priority.
	 */
	public function __construct() {
		$this->current_forms = [];
		$this->form_fields   = [];

		/* WPML translation job hooks */
		add_filter( 'WPML_get_link', [ $this, 'get_link' ], 10, 3 );
		add_filter( 'wpml_document_view_item_link', [ $this, 'get_document_view_link' ], 10, 5 );
		add_filter( 'wpml_document_edit_item_link', [ $this, 'get_document_edit_link' ], 10, 5 );
		add_filter( 'wpml_document_edit_item_url', [ $this, 'get_document_edit_url' ], 10, 3 );
		add_filter( 'page_link', [ $this, 'gform_redirect' ], 10, 1 );

		/* GF frontend hooks: form rendering and submission */
		if ( version_compare( GFForms::$version, '1.9', '<' ) ) {
			add_filter( 'gform_pre_render', [ $this, 'gform_pre_render_deprecated' ] );
		} else {
			add_filter( 'gform_pre_render', [ $this, 'gform_pre_render' ] );
		}
		add_filter( 'gform_pre_validation', [ $this, 'gform_pre_render' ] );
		add_filter( 'gform_pre_process', [ $this, 'translate_conditional_logic' ] );
		add_filter( 'gform_pre_submission_filter', [ $this, 'gform_pre_submission_filter' ] );
		add_filter( 'gform_notification', [ $this, 'gform_notification' ], 10, 2 );
		add_action( 'gform_after_email', [ $this, 'restoreSwitchedEmailLanguage' ] );
		add_filter( 'gform_field_validation', [ $this, 'gform_field_validation' ], 10, 4 );
		add_filter( 'gform_merge_tag_filter', [ $this, 'gform_merge_tag_filter' ], 10, 5 );
		add_filter( 'gform_pre_replace_merge_tags', [ $this, 'gform_pre_replace_merge_tags' ], 100, 2 );

		/* GF admin hooks for updating WPML translation jobs */
		add_action( 'gform_after_save_form', [ $this, 'update_form_translations' ], 10, 2 );
		add_filter( 'gform_pre_confirmation_save', [ $this, 'update_confirmation_translations' ], 10, 2 );
		add_filter( 'gform_pre_notification_save', [ $this, 'update_notifications_translations' ], 10, 2 );
		add_action( 'gform_pre_enqueue_scripts', [ $this, 'set_enqueued_captcha_language' ] );
		add_action( 'gform_after_delete_form', [ $this, 'after_delete_form' ] );
		add_action( 'gform_after_delete_field', [ $this, 'after_delete_field' ] );
		add_action( 'shutdown', [ $this, 'update_form_translations_on_shutdown' ] );

		/**
		 * An action to clear the $current_forms cached forms e.g.
		 *
		 * ```
		 * do_action( 'wpml_gf_clear_current_forms' );
		 * ```
		 *
		 * @since 1.6.3
		 */
		add_action( 'wpml_gf_clear_current_forms', [ $this, 'clear_current_forms' ] );

		add_action( 'init', [ $this, 'show_package_language_admin_bar' ] );
	}

	public function get_form_package( $form ) {
		$form_package            = new stdClass();
		$form_package->kind      = __( 'Gravity Form', 'gravity-forms-ml' );
		$form_package->kind_slug = ICL_GRAVITY_FORM_ELEMENT_TYPE;
		$form_package->name      = $form['id'];
		$form_package->title     = $form['title'];
		$form_package->edit_link = admin_url( sprintf( 'admin.php?page=gf_edit_forms&id=%d', $form['id'] ) );

		return $form_package;
	}

	abstract public function get_type();

	abstract public function get_st_context( $form );

	abstract public function update_form_translations( $form_meta, $is_new, $needs_update = true );

	abstract public function after_delete_form( $form_id );

	abstract protected function register_strings( $form );

	abstract protected function gform_id( $id );

	/**
	 * @return void
	 */
	public function show_package_language_admin_bar() {

		global $pagenow;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if (
			'admin.php' === $pagenow && isset( $_GET['page'] ) &&
			'gf_edit_forms' === $_GET['page'] && isset( $_GET['id'] )
		) {
			$form_id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
			$form    = RGFormsModel::get_form_meta( $form_id );
			$package = $this->get_form_package( $form );

			do_action( 'wpml_show_package_language_admin_bar', $package );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Filters the link to the edit page of the Gravity Form
	 *
	 * @param string $item
	 * @param Int    $id
	 * @param string $anchor
	 *
	 * @return bool|string
	 */
	public function get_link( $item, $id, $anchor ) {
		$id = $this->gform_id( $id );
		if ( '' === $item && $id && false === $anchor ) {
			global $wpdb;
			// todo: Add caching.
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
			$anchor = $wpdb->get_var(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->prepare( "SELECT title FROM {$this->get_forms_table_name()} WHERE id = %d", $id )
			);
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery

			$item = $anchor ? sprintf( '<a href="%s">%s</a>', 'admin.php?page=gf_edit_forms&id=' . $id, $anchor ) : '';
		}

		return $id ? $item : '';
	}

	/**
	 * @param string                            $post_view_link
	 * @param string                            $label
	 * @param stdClass|\WPML_TM_Post_Job_Entity $current_document See https://onthegosystems.myjetbrains.com/youtrack/issue/wpmldev-1435/.
	 * @param string                            $element_type
	 * @param string                            $content_type
	 *
	 * @return string
	 */
	public function get_document_view_link( $post_view_link, $label, $current_document, $element_type, $content_type ) {
		if ( 'package' === $element_type && 'gravity_form' === $content_type ) {
			if ( $current_document instanceof \WPML_TM_Post_Job_Entity ) {
				$original_doc_id = $current_document->get_original_element_id();
			} else {
				$original_doc_id = Obj::prop( 'ID', $current_document ) ?: Obj::prop( 'original_doc_id', $current_document );
			}

			$element_id = apply_filters( 'wpml_element_id_from_package', null, $original_doc_id );
			if ( $element_id ) {
				$form_id = $this->gform_id( $element_id );
				if ( $form_id ) {
					$post_view_link = sprintf( '<a href="%s" target="_blank">%s</a>', get_home_url() . '/?gf_page=preview&id=' . $form_id, $label );
				}
			}
		}
		return $post_view_link;
	}

	public function get_document_edit_link( $post_view_link, $label, $current_document, $element_type, $content_type ) {
		if ( 'package' === $element_type && 'gravity_form' === $content_type ) {
			$element_id = apply_filters( 'wpml_element_id_from_package', null, $current_document->ID );
			if ( $element_id ) {
				$form_id = $this->gform_id( $element_id );
				if ( $form_id ) {
					$post_view_link = sprintf( '<a href="%s">%s</a>', 'admin.php?page=gf_edit_forms&id=' . $form_id, $label );
				}
			}
		}

		return $post_view_link;
	}

	public function get_document_edit_url( $edit_url, $content_type, $element_id ) {
		if ( 'gravity_form' === $content_type ) {
			$element_id = apply_filters( 'wpml_element_id_from_package', null, $element_id );
			if ( $element_id ) {
				$form_id = $this->gform_id( $element_id );
				if ( $form_id ) {
					$edit_url = 'admin.php?page=gf_edit_forms&id=' . $form_id;
				}
			}
		}

		return $edit_url;
	}

	/**
	 * Fix for default lang parameter settings + default WordPress permalinks.
	 *
	 * @param string $link
	 *
	 * @return mixed
	 */
	public function gform_redirect( $link ) {
		global $sitepress;
		$icl_settings = $sitepress->get_settings();
		if ( 3 === (int) $icl_settings['language_negotiation_type'] ) {
			$link = str_replace( '&amp;lang=', '&lang=', $link );
		}

		return $link;
	}

	/**
	 * Returns an array of keys under which translatable strings are saved in a Gravity Form array
	 *
	 * @return array
	 */
	protected function get_form_keys() {
		if ( ! isset( $this->form_keys ) ) {
			$this->form_keys = [
				'limitEntriesMessage',
				'scheduleMessage',
				'requireLoginMessage',
				'schedulePendingMessage',
				'postTitleTemplate',
				'postContentTemplate',
				'button-imageUrl',
				'lastPageButton-text',
				'lastPageButton-imageUrl',
			];
		}

		return apply_filters( 'gform_multilingual_form_keys', $this->form_keys );
	}

	/**
	 * Returns an array of keys under which a Gravity Form Field array stores translatable strings
	 *
	 * @return array
	 */
	public function get_field_keys() {
		$this->form_fields = apply_filters( 'gform_multilingual_field_keys', $this->field_keys );

		return $this->form_fields;
	}

	private function add_button_to_data( $string_data, $field, $kind, $key ) {
		$kind .= 'Button';
		if ( isset( $field[ $kind ][ $key ] ) ) {
			$string_data[ 'page-' . ( $field['pageNumber'] - 1 ) . '-' . $kind . '-' . $key ] = $field[ $kind ][ $key ];
		}

		return $string_data;
	}

	private function add_pagination_data_deprecated( $string_data, $field ) {
		// Page breaks are stored as belonging to the next page,
		// but their buttons are actually displayed in the previous page.
		$string_data = $this->add_button_to_data( $string_data, $field, 'next', 'text' );
		$string_data = $this->add_button_to_data( $string_data, $field, 'next', 'imageUrl' );
		$string_data = $this->add_button_to_data( $string_data, $field, 'previous', 'text' );
		$string_data = $this->add_button_to_data( $string_data, $field, 'previous', 'imageUrl' );

		return $string_data;
	}

	/**
	 * Translation job package - collect translatable strings from GF form.
	 *
	 * @param int $form_id
	 *
	 * @return array
	 */
	private function get_form_strings_deprecated( $form_id ) {

		$snh = new GFML_String_Name_Helper();

		$form        = RGFormsModel::get_form_meta( $form_id );
		$string_data = $this->get_form_main_strings( $form );

		// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		// - Paging Page Names           - $form["pagination"]["pages"][i]
		if ( isset( $form['pagination'] ) ) {
			foreach ( $form['pagination']['pages'] as $field_key => $page_title ) {
				$snh->page_index                                       = $field_key;
				$string_data[ $snh->get_form_pagination_page_title() ] = $page_title;
			}
		}

		// Fields (including paging fields).
		$keys = $this->get_field_keys();

		foreach ( $form['fields'] as $id => $field ) {
			$snh->field = $field;

			if ( 'page' !== $field['type'] ) {
				foreach ( $keys as $field_key ) {
					if ( isset( $field[ $field_key ] ) && '' !== $field[ $field_key ] ) {
						$snh->field_key                          = $field_key;
						$string_data[ $snh->get_field_common() ] = $field[ $field_key ];
					}
				}
			}

			switch ( $field['type'] ) {
				case 'text':
				case 'textarea':
				case 'email':
				case 'number':
				case 'section':
					break;

				case 'html':
					$string_data[ $snh->get_field_html() ] = $field['content'];
					break;

				case 'page':
					$string_data = $this->add_pagination_data_deprecated( $string_data, $field );
					break;
				case 'select':
				case 'multiselect':
				case 'checkbox':
				case 'radio':
				case 'list':
					if ( ! empty( $field['choices'] ) ) {
						foreach ( $field['choices'] as $index => $choice ) {
							$snh->field_choice                                        = $choice;
							$snh->field_choice_index                                  = $index;
							$string_data[ $snh->get_field_multi_input_choice_text() ] = $choice['text'];
						}
					}
					break;

				case 'product':
				case 'shipping':
				case 'option':
					if (
						in_array( $field['inputType'], [ 'select', 'checkbox', 'radio', 'hiddenproduct' ], true ) &&
						! empty( $field['choices'] )
					) {
						foreach ( $field['choices'] as $index => $choice ) {
							$snh->field_choice                                        = $choice;
							$snh->field_choice_index                                  = $index;
							$string_data[ $snh->get_field_multi_input_choice_text() ] = $choice['text'];
						}
					}
					break;
				case 'post_custom_field':
					if ( isset( $field['customFieldTemplate'] ) ) {
						$string_data[ $snh->get_field_post_custom_field() ] = $field['customFieldTemplate'];
					}
					break;
				case 'post_category':
					if ( isset( $field['categoryInitialItem'] ) ) {
						$string_data[ $snh->get_field_post_category() ] = $field['categoryInitialItem'];
					}
					break;
			}
		}

		// Confirmations.
		foreach ( $form['confirmations'] as $field_key => $confirmation ) {
			$snh->confirmation = $confirmation;
			switch ( $confirmation['type'] ) {
				case 'message':
					// Add prefix 'field-' to get a textarea editor box.
					$string_data[ $snh->get_form_confirmation_message() ] = $confirmation['message'];
					break;
				case 'redirect':
					$string_data[ $snh->get_form_confirmation_redirect_url() ] = $confirmation['url'];
					break;
				case 'page':
					$string_data[ $snh->get_form_confirmation_page_id() ] = $confirmation['pageId'];
					break;
			}
		}

		// Notifications: translate only those for user submitted emails.
		if ( ! empty( $form['notifications'] ) ) {
			foreach ( $form['notifications'] as $field_key => $notification ) {
				$snh->notification = $notification;
				if ( 'field' === $notification['toType'] || 'email' === $notification['toType'] ) {
					$string_data[ $snh->get_form_notification_subject() ] = $notification['subject'];
					$string_data[ $snh->get_form_notification_message() ] = $notification['message'];
				}
			}
		}

		return $string_data;
	}

	private function get_form_main_strings( $form ) {
		$string_data = [];
		$form_keys   = $this->get_form_keys();

		// Form main fields.
		foreach ( $form_keys as $key ) {
			$parts = explode( '-', $key );
			if ( count( $parts ) === 1 ) {
				if ( isset( $form[ $key ] ) && '' !== $form[ $key ] ) {
					$string_data[ $key ] = $form[ $key ];
				}
			} else {
				if ( isset( $form[ $parts[0] ][ $parts[1] ] ) && '' !== $form[ $parts[0] ][ $parts[1] ] ) {
					$string_data[ $key ] = $form[ $parts[0] ][ $parts[1] ];
				}
			}
		}

		return $string_data;
	}

	/**
	 * Translation job package - collect translatable strings from GF form.
	 *
	 * @todo See to merge this and gform_pre_render (already overlapping)
	 *
	 * @param int $form_id
	 *
	 * @return array Associative array that holds the forms string values as values and uses the ST string name field
	 *                   suffixes as indexes. The value $form_id_$index would be the actual ST icl_strings string name.
	 */
	public function get_form_strings( $form_id ) {

		if ( version_compare( GFCommon::$version, '1.9', '<' ) ) {
			return $this->get_form_strings_deprecated( $form_id );
		}

		$snh         = new GFML_String_Name_Helper();
		$form        = RGFormsModel::get_form_meta( $form_id );
		$string_data = $this->get_form_main_strings( $form );

		// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		// Pagination - Paging Page Names - $form["pagination"]["pages"][i].
		if ( isset( $form['pagination']['pages'] ) && is_array( $form['pagination']['pages'] ) ) {
			foreach ( $form['pagination']['pages'] as $page_index => $page_title ) {
				$snh->page_index                                       = $page_index;
				$string_data[ $snh->get_form_pagination_page_title() ] = $page_title;
			}
		}

		// Common field properties.
		$keys = $this->get_field_keys();

		// Fields.
		foreach ( $form['fields'] as $id => $field ) {
			$snh->field = $field;

			if ( 'page' !== $field->type ) {
				foreach ( $keys as $field_key ) {
					$snh->field_key = $field_key;
					if ( '' !== $field->{$field_key} ) {
						$string_data[ $snh->get_field_common() ] = $field->{$field_key};
					}
				}
			}

			switch ( $field['type'] ) {
				case 'text':
				case 'textarea':
				case 'email':
				case 'number':
				case 'section':
					break;
				case 'html':
					$string_data[ $snh->get_field_html() ] = $field->content;
					break;
				case 'page':
					/*
					 * Page breaks are stored as belonging to the next page,
					 * but their buttons are actually displayed in the previous page
					 */
					foreach ( [ 'text', 'imageUrl' ] as $page_index ) {
						$snh->page_index = $page_index;
						if ( isset( $field->nextButton[ $page_index ] ) ) {
							$string_data[ $snh->get_field_page_nextButton() ] = $field->nextButton[ $page_index ];
						}
						if ( isset( $field->previousButton[ $page_index ] ) ) {
							$string_data[ $snh->get_field_page_previousButton() ] = $field->previousButton[ $page_index ];
						}
					}
					break;
				case 'post_custom_field':
					// TODO not registered at my tests.
					if ( '' !== $field->customFieldTemplate ) {
						$string_data[ $snh->get_field_post_custom_field() ] = $field->customFieldTemplate;
					}
					break;
				case 'post_category':
					if ( '' !== $field->categoryInitialItem ) {
						$string_data[ $snh->get_field_post_category() ] = $field->categoryInitialItem;
					}
					break;
			}

			if ( isset( $field->choices ) && is_array( $field->choices ) ) {
				foreach ( $field->choices as $index => $choice ) {
					$snh->field_choice                                        = $choice;
					$snh->field_choice_index                                  = $index;
					$string_data[ $snh->get_field_multi_input_choice_text() ] = $choice['text'];
				}
			}
		}

		// Confirmations.
		if ( is_array( $form['confirmations'] ) ) {
			foreach ( $form['confirmations'] as $confirmation_index => $confirmation ) {
				$snh->confirmation = $confirmation;
				switch ( $confirmation['type'] ) {
					case 'message':
						// Add prefix 'field-' to get a textarea editor box.
						$string_data[ $snh->get_form_confirmation_message() ] = $confirmation['message'];
						break;
					case 'redirect':
						$string_data[ $snh->get_form_confirmation_redirect_url() ] = $confirmation['url'];
						break;
					case 'page':
						$string_data[ $snh->get_form_confirmation_page_id() ] = $confirmation['pageId'];
						break;
				}
			}
		}

		// Notifications: translate only those for user submitted emails.
		if ( is_array( $form['notifications'] ) ) {
			foreach ( $form['notifications'] as $notification_index => $notification ) {
				$snh->notification = $notification;
				if ( 'field' === $notification['toType']
					 || 'email' === $notification['toType']
				) {
					$string_data[ $snh->get_form_notification_subject() ] = $notification['subject'];
					$string_data[ $snh->get_form_notification_message() ] = $notification['message'];
				}
			}
		}

		return $string_data;
	}

	/**
	 * @param array  $form
	 * @param string $st_context
	 *
	 * @return array
	 */
	private function populate_translated_values( $form, $st_context ) {
		$form_keys = $this->get_form_keys();

		foreach ( $form_keys as $key ) {
			$parts = explode( '-', $key );
			if ( count( $parts ) === 1 ) {
				if ( isset( $form[ $key ] ) && '' !== $form[ $key ] ) {
					$form[ $key ] = icl_t( $st_context, $key, $form[ $key ] );
				}
			} else {
				if ( isset( $form[ $parts[0] ][ $parts[1] ] ) && '' !== $form[ $parts[0] ][ $parts[1] ] ) {
					$form[ $parts[0] ][ $parts[1] ] = icl_t( $st_context, $key, $form[ $parts[0] ][ $parts[1] ] );
				}
			}
		}

		return $form;
	}

	/**
	 * Front-end form rendering (deprecated).
	 *
	 * @param array $form
	 *
	 * @return array
	 */
	public function gform_pre_render_deprecated( $form ) {
		// Render the form.
		global $sitepress;
		$st_context = $this->get_st_context( $form['id'] );

		$current_lang = $sitepress->get_current_language();
		if ( isset( $this->current_forms[ $form['id'] ][ $current_lang ] ) ) {
			return $this->current_forms[ $form['id'] ][ $current_lang ];
		}

		$snh = new GFML_String_Name_Helper();

		$form = $this->populate_translated_values( $form, $st_context );

		// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		// - Paging Page Names           - $form["pagination"]["pages"][i].
		if ( isset( $form['pagination'] ) ) {
			foreach ( $form['pagination']['pages'] as $page_index => $page_title ) {
				$snh->page_index                            = $page_index;
				$form['pagination']['pages'][ $page_index ] = icl_t( $st_context, $snh->get_form_pagination_page_title(), $form['pagination']['pages'][ $page_index ] );
			}
		}

		// Fields (including paging fields).
		$keys = $this->get_field_keys();

		foreach ( $form['fields'] as $id => $field ) {

			$snh->field = $id;

			foreach ( $keys as $field_key ) {
				$snh->field_key = $field_key;
				if ( isset( $field[ $field_key ] ) && '' !== $field[ $field_key ] && 'page' !== $field['type'] ) {
					$form['fields'][ $id ][ $field_key ] = icl_t( $st_context, $snh->get_field_common(), $field[ $field_key ] );
				}
			}

			switch ( $field['type'] ) {
				case 'text':
				case 'textarea':
				case 'email':
				case 'number':
				case 'section':
					break;

				case 'html':
					$form['fields'][ $id ]['content'] = icl_t( $st_context, $snh->get_field_html(), $field['content'] );
					break;

				case 'page':
					foreach ( [ 'text', 'imageUrl' ] as $page_index ) {
						$snh->page_index = $page_index;
						if ( isset( $form['fields'][ $id ]['nextButton'][ $page_index ] ) ) {
							$form['fields'][ $id ]['nextButton'][ $page_index ] = icl_t( $st_context, $snh->get_field_page_nextButton(), $field['nextButton'][ $page_index ] );
						}
						if ( isset( $form['fields'][ $id ]['previousButton'][ $page_index ] ) ) {
							$form['fields'][ $id ]['previousButton'][ $page_index ] = icl_t( $st_context, $snh->get_field_page_previousButton(), $field['previousButton'][ $page_index ] );
						}
					}
					break;
				case 'select':
				case 'multiselect':
				case 'checkbox':
				case 'radio':
				case 'list':
					if ( ! empty( $field['choices'] ) ) {
						foreach ( $field['choices'] as $index => $choice ) {
							$snh->field_choice                                  = $choice;
							$snh->field_choice_index                            = $index;
							$string_name                                        = $snh->get_field_multi_input_choice_text();
							$translation                                        = icl_t( $st_context, $string_name, $choice['text'] );
							$form['fields'][ $id ]['choices'][ $index ]['text'] = $translation;
						}
					}
					break;
				case 'product':
				case 'shipping':
				case 'option':
					if (
						in_array( $field['inputType'], [ 'select', 'checkbox', 'radio', 'hiddenproduct' ], true ) &&
						! empty( $field['choices'] )
					) {
						foreach ( $field['choices'] as $index => $choice ) {
							$snh->field_choice                                  = $choice;
							$snh->field_choice_index                            = $index;
							$translation                                        = icl_t( $st_context, $snh->get_field_multi_input_choice_text(), $choice['text'] );
							$form['fields'][ $id ]['choices'][ $index ]['text'] = $translation;
						}
					}
					break;

				case 'post_custom_field':
					$form['fields'][ $id ]['customFieldTemplate'] = icl_t( $st_context, $snh->get_field_post_custom_field() . '-customFieldTemplate', $field['customFieldTemplate'] );
					break;
				case 'post_category':
					$form['fields'][ $id ]['categoryInitialItem'] = icl_t( $st_context, $snh->get_field_post_custom_field(), $field['categoryInitialItem'] );
					break;
			}
		}

		if ( isset( $form['pagination']['pages'] ) ) {
			foreach ( $form['pagination']['pages'] as $page_index => $page_title ) {
				$snh->page_index                            = $page_index;
				$form['pagination']['pages'][ $page_index ] = icl_t( $st_context, $snh->get_form_pagination_page_title(), $form['pagination']['pages'][ $page_index ] );
			}
			if ( isset( $form['pagination']['progressbar_completion_text'] ) ) {
				$form['pagination']['progressbar_completion_text'] = icl_t( $st_context, $snh->get_form_pagination_completion_text(), $form['pagination']['progressbar_completion_text'] );
			}
		}

		if ( isset( $form['lastPageButton'] ) ) {
			$form['lastPageButton'] = icl_t( $st_context, $snh->get_form_pagination_last_page_button_text(), $form['lastPageButton'] );
		}

		$this->current_forms[ $form['id'] ][ $current_lang ] = $form;

		return $form;
	}

	/**
	 * Front-end form rendering.
	 *
	 * @global object $sitepress
	 *
	 * @param array $form
	 *
	 * @return array
	 */
	public function gform_pre_render( $form ) {

		global $sitepress;

		$st_context = $this->get_st_context( $form['id'] );
		// Cache.
		$current_lang = $sitepress->get_current_language();
		if ( isset( $this->current_forms[ $form['id'] ][ $current_lang ] ) ) {
			return $this->current_forms[ $form['id'] ][ $current_lang ];
		}

		$snh = new GFML_String_Name_Helper();

		$form = $this->populate_translated_values( $form, $st_context );

		$this->get_global_strings( $form, $st_context );

		// Pagination.
		if ( ! empty( $form['pagination'] ) ) {
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
			// Paging Page Names - $form["pagination"]["pages"][i].
			if ( isset( $form['pagination']['pages'] ) && is_array( $form['pagination']['pages'] ) ) {
				foreach ( $form['pagination']['pages'] as $page_index => $page_title ) {
					$snh->page_index                            = $page_index;
					$form['pagination']['pages'][ $page_index ] = icl_t( $st_context, $snh->get_form_pagination_page_title(), $page_title );
				}
			}
			// Completion text.
			if ( ! empty( $form['pagination']['progressbar_completion_text'] ) ) {
				$form['pagination']['progressbar_completion_text'] = icl_t( $st_context, $snh->get_form_pagination_completion_text(), $form['pagination']['progressbar_completion_text'] );
			}
			// Last page button text.
			// todo: not registered at my tests.
			if ( ! empty( $form['lastPageButton']['text'] ) ) {
				$form['lastPageButton']['text'] = icl_t( $st_context, $snh->get_form_pagination_last_page_button_text(), $form['lastPageButton']['text'] );
			}
		}

		// Common field properties.
		$keys = $this->get_field_keys();

		$form = $this->translate_conditional_logic( $form );

		// Filter form fields (array of GF_Field objects).
		foreach ( $form['fields'] as $id => &$field ) {

			$snh->field = $field;

			// Filter common properties.
			foreach ( $keys as $field_key ) {
				$snh->field_key = $field_key;
				if ( ! empty( $field->{$field_key} ) && 'page' !== $field->type ) {
					$field->{$field_key} = icl_t( $st_context, $snh->get_field_common(), $field->{$field_key} );
				}
			}

			// Field specific code.
			switch ( $field->type ) {
				case 'html':
					$field->content = icl_t( $st_context, $snh->get_field_html(), $field->content );
					break;
				case 'page':
					foreach ( [ 'text', 'imageUrl' ] as $page_index ) {
						$snh->field_key = $page_index;
						if ( ! empty( $field->nextButton[ $page_index ] ) ) {
							$field->nextButton[ $page_index ] = icl_t( $st_context, $snh->get_field_page_nextButton(), $field->nextButton[ $page_index ] );
						}
						if ( isset( $field->previousButton[ $page_index ] ) ) {
							$field->previousButton[ $page_index ] = icl_t( $st_context, $snh->get_field_page_previousButton(), $field->previousButton[ $page_index ] );
						}
					}
					break;
				case 'post_custom_field':
					// TODO if multi options - 'choices' (register and translate) 'inputType' => select, etc.
					if ( '' !== $field->customFieldTemplate ) {
						$field->customFieldTemplate = icl_t( $st_context, $snh->get_field_post_custom_field(), $field->customFieldTemplate );
					}
					break;
				case 'post_category':
					// TODO if multi options - 'choices' have static values (register and translate) 'inputType' => select, etc.
					if ( '' !== $field->categoryInitialItem ) {
						$field->categoryInitialItem = icl_t( $st_context, $snh->get_field_post_category(), $field->categoryInitialItem );
					}
					break;
				case 'address':
					if ( ! empty( $field->copyValuesOptionLabel ) ) {
						$field->copyValuesOptionLabel = icl_t(
							$st_context,
							$snh->get_field_address_copy_values_option(),
							$field->copyValuesOptionLabel
						);
					}
					break;
				case 'name':
					if ( isset( $field->inputs ) ) {
						foreach ( $field->inputs as $input_index => $input ) {
							if ( isset( $input['choices'] ) ) {
								foreach ( $input['choices'] as $choice_index => $choice ) {
									if ( isset( $choice['text'] ) ) {
										do_action( 'wpml_register_single_string', 'gfml_prefix_name_choices', 'text_' . $choice['text'], $choice['text'] );
										$field->inputs[ $input_index ]['choices'][ $choice_index ]['text'] =
											apply_filters( 'wpml_translate_single_string', $choice['text'], 'gfml_prefix_name_choices', 'text_' . $choice['text'] );
									}
									if ( isset( $choice['value'] ) ) {
										do_action( 'wpml_register_single_string', 'gfml_prefix_name_choices', 'value_' . $choice['value'], $choice['value'] );
										$field->inputs[ $input_index ]['choices'][ $choice_index ]['value'] =
											apply_filters( 'wpml_translate_single_string', $choice['value'], 'gfml_prefix_name_choices', 'value_' . $choice['value'] );
									}
								}
							}
						}
					}
					break;
				case 'captcha':
					$field->captchaLanguage = $this->get_captcha_language();
					break;
				default:
					break;
			}

			if ( isset( $field->choices ) && is_array( $field->choices ) ) {
				$field = $this->handle_multi_input( $field, $st_context );
			}

			$field = $this->maybe_translate_placeholder( $field, $st_context, $form );
			$field = $this->maybe_translate_customLabels( $field, $st_context, $form );
		}

		$this->current_forms[ $form['id'] ][ $current_lang ] = $form;

		/**
		 * A filter to allow translating additional strings
		 *
		 * Used to add additional translations to the form before rendering e.g.
		 *
		 * ```
		 * add_filter( 'wpml_gf_translate_strings', function ( $form, $st_context ) {
		 *     $form['custom_string'] = apply_filters( 'wpml_translate_single_string', $form['custom_string'], $st_context, 'my_custom_string' );
		 *     return $form;
		 * }, 10, 2 );
		 * ```
		 *
		 * @since 1.6.0
		 *
		 * @param array  $form       The Gravity Form data after 'gform_pre_render'
		 * @param string $st_context The context
		 */
		$form = apply_filters( 'wpml_gf_translate_strings', $form, $st_context );

		return $form;
	}

	public function translate_conditional_logic( $form ) {
		$st_context = $this->get_st_context( $form['id'] );
		return $this->get_conditional_logic()->translate_conditional_logic( $form, $st_context );
	}

	/**
	 * @return GFML_Conditional_Logic
	 */
	public function get_conditional_logic() {
		return new GFML_Conditional_Logic();
	}

	private function get_global_strings( &$form, $st_context ) {
		$snh = new GFML_String_Name_Helper();

		if ( isset( $form['title'] ) ) {
			$form['title'] = icl_t( $st_context, $snh->get_form_title(), $form['title'] );
		}
		if ( isset( $form['description'] ) ) {
			$form['description'] = icl_t( $st_context, $snh->get_form_description(), $form['description'] );
		}
		if ( isset( $form['button']['text'] ) ) {
			$form['button']['text'] = icl_t( $st_context, $snh->get_form_submit_button(), $form['button']['text'] );
		}
		if ( isset( $form['save']['button']['text'] ) ) {
			$form['save']['button']['text'] = icl_t( $st_context, $snh->get_form_save_and_continue_later_text(), $form['save']['button']['text'] );
		}
		$this->get_notifications( $form, $st_context );
		$this->get_confirmations( $form, $st_context );
	}

	protected function get_notifications( &$form, $st_context ) {
		if ( isset( $form['notifications'] ) && $form['notifications'] ) {
			$snh = new GFML_String_Name_Helper();
			foreach ( $form['notifications'] as &$notification ) {
				$snh->notification       = $notification;
				$notification['subject'] = icl_t( $st_context, $snh->get_form_notification_subject(), $notification['subject'] );
				$notification['message'] = icl_t( $st_context, $snh->get_form_notification_message(), $notification['message'] );
			}
		}
	}

	protected function get_confirmations( &$form, $st_context ) {
		if ( isset( $form['confirmations'] ) && $form['confirmations'] ) {
			$snh = new GFML_String_Name_Helper();
			foreach ( $form['confirmations'] as &$confirmation ) {
				$snh->confirmation = $confirmation;
				switch ( $confirmation['type'] ) {
					case 'message':
						$confirmation['message'] = icl_t( $st_context, $snh->get_form_confirmation_message(), $confirmation['message'] );
						break;
					case 'redirect':
						$confirmation['url'] = icl_t( $st_context, $snh->get_form_confirmation_redirect_url(), $confirmation['url'] );
						break;
					case 'page':
						$confirmation['pageId'] = icl_t( $st_context, $snh->get_form_confirmation_redirect_url(), $confirmation['pageId'] );
						break;
				}
			}
		}
	}

	private function maybe_translate_placeholder( $field, $st_context, $form ) {
		$snh        = new GFML_String_Name_Helper();
		$snh->field = $field;

		$string_name = $snh->get_field_placeholder();

		if ( isset( $field->placeholder ) && $field->placeholder ) {
			$field->placeholder = icl_t( $st_context, $string_name, $field->placeholder );
		}

		if ( isset( $field->inputs ) && $field->inputs ) {
			foreach ( $field->inputs as $key => $input ) {
				$snh->field_input = $input;
				if ( isset( $input['placeholder'] ) && $input['placeholder'] ) {
					$string_input_name                    = $snh->get_field_input_placeholder();
					$field->inputs[ $key ]['placeholder'] = icl_t( $st_context, $string_input_name, $field->inputs[ $key ]['placeholder'] );
				}
			}
		}

		return $field;
	}

	private function maybe_translate_customLabels( $field, $st_context, $form ) {
		$snh        = new GFML_String_Name_Helper();
		$snh->field = $field;

		if ( isset( $field->inputs ) && $field->inputs ) {
			foreach ( $field->inputs as $key => $input ) {
				$snh->field_input = $input;
				if ( isset( $input['customLabel'] ) && $input['customLabel'] ) {
					$string_input_name                    = $snh->get_field_input_customLabel();
					$field->inputs[ $key ]['customLabel'] = icl_t( $st_context, $string_input_name, $field->inputs[ $key ]['customLabel'] );
				}
			}
		}

		return $field;
	}

	private function handle_multi_input( $field, $st_context ) {
		$snh        = new GFML_String_Name_Helper();
		$snh->field = $field;

		if ( is_array( $field->choices ) ) {
			foreach ( $field->choices as $index => $choice ) {
				$snh->field_choice                = $choice;
				$snh->field_choice_index          = $index;
				$string_name                      = $snh->get_field_multi_input_choice_text();
				$field->choices[ $index ]['text'] = icl_t( $st_context, $string_name, $choice['text'] );
				if ( isset( $choice['value'] ) ) {
					$string_name                       = $snh->get_field_multi_input_choice_value();
					$field->choices[ $index ]['value'] = icl_t( $st_context, $string_name, $choice['value'] );
				}
			}
		}

		return $field;
	}

	/**
	 * Translate confirmations before submission.
	 *
	 * @param array $form
	 *
	 * @return array
	 */
	public function gform_pre_submission_filter( $form ) {
		$form = $this->gform_pre_render( $form );
		if ( ! empty( $form['confirmations'] ) ) {
			$snh        = new GFML_String_Name_Helper();
			$st_context = $this->get_st_context( $form['id'] );
			foreach ( $form['confirmations'] as $key => &$confirmation ) {
				$snh->confirmation = $confirmation;

				switch ( $confirmation['type'] ) {
					case 'message':
						$confirmation['message'] = icl_t( $st_context, $snh->get_form_confirmation_message(), $confirmation['message'] );
						break;
					case 'redirect':
						global $sitepress;
						$confirmation['url'] = str_replace( '&amp;lang=', '&lang=', icl_t( $st_context, $snh->get_form_confirmation_redirect_url(), $confirmation['url'] ) );
						break;
					case 'page':
						$page_id                = icl_t( $st_context, $snh->get_form_confirmation_page_id(), $confirmation['pageId'] );
						$confirmation['pageId'] = apply_filters( 'wpml_object_id', $page_id, 'page', true );
						break;
				}
			}
		}
		global $sitepress;
		$current_lang                                        = $sitepress->get_current_language();
		$this->current_forms[ $current_lang ][ $form['id'] ] = $form;

		return $form;
	}

	/**
	 * Translate notifications.
	 *
	 * @param array $notification
	 * @param array $form
	 *
	 * @return array
	 */
	public function gform_notification( $notification, $form ) {
		if ( isset( $notification['subject'] ) || isset( $notification['message'] ) ) {
			$snh               = new GFML_String_Name_Helper();
			$snh->notification = $notification;
			$st_context        = $this->get_st_context( $form['id'] );

			if ( email_exists( $notification['to'] ) ) {
				do_action( 'wpml_switch_language_for_email', $notification['to'] );
			}

			if ( isset( $notification['subject'] ) ) {
				$notification['subject'] = apply_filters( 'wpml_translate_single_string', $notification['subject'], $st_context, $snh->get_form_notification_subject() );
			}
			if ( isset( $notification['message'] ) ) {
				$notification['message'] = apply_filters( 'wpml_translate_single_string', $notification['message'], $st_context, $snh->get_form_notification_message() );

				if ( isset( $notification['toType'] ) && 'hidden' === $notification['toType'] ) {
					// phpcs:disable WordPress.Security.NonceVerification.Missing
					$resume_token = isset( $_POST['gform_resume_token'] ) ? filter_var( wp_unslash( $_POST['gform_resume_token'] ), FILTER_SANITIZE_STRING ) : '';
					$resume_email = isset( $_POST['gform_resume_email'] ) ? filter_var( wp_unslash( $_POST['gform_resume_email'] ), FILTER_SANITIZE_EMAIL ) : '';
					// phpcs:enable WordPress.Security.NonceVerification.Missing

					if ( $resume_token && $resume_email ) {
						$notification['message'] = GFFormDisplay::replace_save_variables( $notification['message'], $form, $resume_token, $resume_email );
					}
				}
			}
		}

		return $notification;
	}

	/**
	 * Translate validation messages.
	 *
	 * @param array    $result
	 * @param mixed    $value
	 * @param array    $form
	 * @param GF_Field $field
	 *
	 * @return array
	 */
	public function gform_field_validation( $result, $value, $form, $field ) {
		if ( ! $result['is_valid'] ) {
			$snh               = new GFML_String_Name_Helper();
			$snh->field        = $field;
			$st_context        = $this->get_st_context( $form['id'] );
			$result['message'] = icl_t( $st_context, $snh->get_field_validation_message(), $result['message'] );
		}

		return $result;
	}

	/**
	 * Get translated form.
	 *
	 * @param string      $form_id
	 * @param null|String $lang
	 *
	 * @return array
	 */
	public function get_form( $form_id, $lang = null ) {
		if ( ! $lang ) {
			global $sitepress;
			$lang = $sitepress->get_current_language();
		}

		return isset( $this->current_forms[ $form_id ][ $lang ] ) ? $this->current_forms[ $form_id ][ $lang ] : $this->gform_pre_render( RGFormsModel::get_form_meta( $form_id ) );
	}

	/**
	 * Get translated field value to use with merge tags.
	 *
	 * @param string    $value
	 * @param string    $merge_tag
	 * @param string    $options
	 * @param \GF_Field $field
	 * @param string    $format
	 *
	 * @return array|string
	 */
	public function gform_merge_tag_filter( $value, $merge_tag, $options, $field, $format ) {

		if ( RGFormsModel::get_input_type( $field ) !== 'multiselect' ) {
			return $value;
		}

		$options = [];
		$value   = explode( ',', $value );
		foreach ( $value as $selected ) {
			$options[] = GFCommon::selection_display( $selected, $field, $currency = null, $use_text = true );
		}

		return implode( ', ', $options );
	}

	/**
	 * Remove translations of deleted field
	 *
	 * @param int $form_id
	 */
	public function after_delete_field( $form_id ) {
		$form_meta = RGFormsModel::get_form_meta( $form_id );
		// It is not new form (second parameter) and when deleting field do not need to update status (third parameter).
		$this->update_form_translations( $form_meta, false, false );
	}

	/**
	 * Undocumented.
	 *
	 * @param array $notification
	 * @param array $form
	 *
	 * @return array
	 */
	public function update_notifications_translations( $notification, $form ) {

		$this->add_form_to_shutdown_list( $form );

		return $notification;
	}

	/**
	 * Undocumented.
	 *
	 * @param array $confirmation
	 * @param array $form
	 *
	 * @return array
	 */
	public function update_confirmation_translations( $confirmation, $form ) {

		$this->add_form_to_shutdown_list( $form );

		return $confirmation;
	}

	private function add_form_to_shutdown_list( $form ) {
		if ( ! in_array( $form['id'], $this->forms_to_update_on_shutdown, true ) ) {
			$this->forms_to_update_on_shutdown[] = $form['id'];
		}
	}

	/**
	 * Return language code to use for captcha.
	 * If not supported tries the default language.
	 * If default is also not supported uses $fallback.
	 *
	 * @param string $fallback The fallback language if it's not supported.
	 * @return string The language code.
	 */
	private function get_captcha_language( $fallback = 'en' ) {
		$tags = [ 'wpml_current_language', 'wpml_default_language' ];
		foreach ( $tags as $tag ) {
			$language_code = apply_filters( $tag, null );
			if ( isset( $this->recaptcha_supported_languages[ $language_code ] ) ) {
				return $language_code;
			}
		}
		return $fallback;
	}

	/**
	 * @return string|false
	 */
	public function get_forms_table_name() {
		global $wpdb;

		if ( null !== $this->forms_table_name ) {
			return $this->forms_table_name;
		}

		$this->forms_table_name = '';

		$name = self::LEGACY_FORMS_TABLE;
		if ( version_compare( GFFormsModel::get_database_version(), '2.3-dev-1', '>' ) ) {
			$name = self::FORMS_TABLE;
		}

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}{$name}'" ) === $wpdb->prefix . $name ) {
			$this->forms_table_name = $wpdb->prefix . $name;
		}
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery

		return $this->forms_table_name;
	}

	/**
	 * @param string $text
	 * @param array  $form
	 *
	 * @return string
	 */
	public function gform_pre_replace_merge_tags( $text, $form ) {
		if ( isset( $form['confirmation'] ) && rgar( $form['confirmation'], 'message' ) === $text ) {
			$text = $this->get_translated_confirmation_message( $form, $form['confirmation'] );
			// todo: Add nonce verification. Next line is temporary.
			// phpcs:disable WordPress.Security.NonceVerification.Missing
		} elseif ( isset( $_POST['gform_resume_token'] ) && isset( $_POST['gform_resume_email'] ) && isset( $form['confirmation'] ) ) {
			// phpcs:enable WordPress.Security.NonceVerification.Missing
			$resume_token         = filter_input( INPUT_POST, 'gform_resume_token', FILTER_SANITIZE_STRING );
			$resume_email         = filter_input( INPUT_POST, 'gform_resume_email', FILTER_SANITIZE_EMAIL );
			$confirmation_message = GFFormDisplay::replace_save_variables( rgar( $form['confirmation'], 'message' ), $form, $resume_token, $resume_email );
			$confirmation_message = '<div class="form_saved_message_sent" role="alert"><span>' . $confirmation_message . '</span></div>';

			if ( $text === $confirmation_message ) {
				foreach ( $form['confirmations'] as $resume_token => $confirmation ) {
					if ( isset( $confirmation['event'] ) && 'form_save_email_sent' === $confirmation['event'] ) {
						$form['confirmations'][ $resume_token ]['message'] = $this->get_translated_confirmation_message( $form, $confirmation );
					}
				}

				if ( isset( $form['confirmation'] ) && 'form_save_email_sent' === $form['confirmation']['event'] ) {
					$form['confirmation']['message'] = $this->get_translated_confirmation_message( $form, $form['confirmation'] );
				}

				$text = GFFormDisplay::replace_save_variables( $form['confirmation']['message'], $form, $resume_token, $resume_email );
				$text = '<div class="form_saved_message_sent"><span>' . $text . '</span></div>';

			}
		}

		return $text;
	}

	/**
	 * @param array $form
	 * @param array $confirmation
	 *
	 * @return string
	 */
	private function get_translated_confirmation_message( $form, $confirmation ) {
		$st_context                       = $this->get_st_context( $form['id'] );
		$string_name_helper               = new GFML_String_Name_Helper();
		$string_name_helper->confirmation = $confirmation;
		return icl_t( $st_context, $string_name_helper->get_form_confirmation_message(), $confirmation['message'] );
	}

	/**
	 * Add clear current_forms
	 */
	public function clear_current_forms() {
		unset( $this->current_forms );
	}

	public function update_form_translations_on_shutdown() {
		foreach ( $this->forms_to_update_on_shutdown as $form_id ) {
			$form = RGFormsModel::get_form_meta( $form_id );
			$this->update_form_translations( $form, false );
		}
	}

	/**
	 * Set correct language for all reCAPTCHA fields in form.
	 *
	 * @param array $form
	 */
	public function set_enqueued_captcha_language( $form ) {
		foreach ( $form['fields'] as $field ) {
			if ( 'captcha' === $field->type ) {
				$field->captchaLanguage = $this->get_captcha_language();
			}
		}
	}

	/**
	 * @return void
	 */
	public function restoreSwitchedEmailLanguage() {
		do_action( 'wpml_restore_language_from_email' );
	}
}
