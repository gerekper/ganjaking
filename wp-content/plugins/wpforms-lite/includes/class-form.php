<?php

/**
 * All the form goodness and basics.
 *
 * Contains a bunch of helper methods as well.
 *
 * @since 1.0.0
 */
class WPForms_Form_Handler {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Register wpforms custom post type.
		$this->register_cpt();

		// Add wpforms to new-content admin bar menu.
		add_action( 'admin_bar_menu', array( $this, 'admin_bar' ), 99 );

	}

	/**
	 * Register the custom post type to be used for forms.
	 *
	 * @since 1.0.0
	 */
	public function register_cpt() {

		// Custom post type arguments, which can be filtered if needed.
		$args = apply_filters(
			'wpforms_post_type_args',
			array(
				'label'               => 'WPForms',
				'public'              => false,
				'exclude_from_search' => true,
				'show_ui'             => false,
				'show_in_admin_bar'   => false,
				'rewrite'             => false,
				'query_var'           => false,
				'can_export'          => false,
				'supports'            => array( 'title' ),
				'capability_type'     => 'wpforms_form', // Not using 'capability_type' anywhere. It just has to be custom for security reasons.
				'map_meta_cap'        => false, // Don't let WP to map meta caps to have a granular control over this process via 'map_meta_cap' filter.
			)
		);

		// Register the post type.
		register_post_type( 'wpforms', $args );
	}

	/**
	 * Add "WPForms" item to new-content admin bar menu item.
	 *
	 * @since 1.1.7.2
	 *
	 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	public function admin_bar( $wp_admin_bar ) {

		if ( ! is_admin_bar_showing() || ! wpforms_current_user_can( 'create_forms' ) ) {
			return;
		}

		$args = array(
			'id'     => 'wpforms',
			'title'  => esc_html__( 'WPForms', 'wpforms-lite' ),
			'href'   => admin_url( 'admin.php?page=wpforms-builder' ),
			'parent' => 'new-content',
		);
		$wp_admin_bar->add_node( $args );
	}

	/**
	 * Fetch forms.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $id   Form ID.
	 * @param array $args Additional arguments array.
	 *
	 * @return array|bool|null|WP_Post
	 */
	public function get( $id = '', $args = array() ) {

		$args = apply_filters( 'wpforms_get_form_args', $args, $id );

		if ( false === $id ) {
			return false;
		}

		$forms = empty( $id ) ? $this->get_multiple( $args ) : $this->get_single( $id, $args );

		if ( empty( $forms ) ) {
			return false;
		}

		return $forms;
	}

	/**
	 * Fetch a single form.
	 *
	 * @since 1.5.8
	 *
	 * @param string|int $id   Form ID.
	 * @param array      $args Additional arguments array.
	 *
	 * @return array|bool|null|WP_Post
	 */
	protected function get_single( $id = '', $args = array() ) {

		$args = apply_filters( 'wpforms_get_single_form_args', $args, $id );

		if ( ! isset( $args['cap'] ) && wpforms()->get( 'access' )->init_allowed() ) {
			$args['cap'] = 'view_form_single';
		}

		 if ( ! empty( $args['cap'] ) && ! wpforms_current_user_can( $args['cap'], $id ) ) {
		 	return false;
		 }

		// @todo add $id array support
		// If ID is provided, we get a single form
		$form = get_post( absint( $id ) );

		if ( ! empty( $args['content_only'] ) ) {
			$form = ! empty( $form ) && 'wpforms' === $form->post_type ? wpforms_decode( $form->post_content ) : false;
		}

		return $form;
	}

	/**
	 * Fetch multiple forms.
	 *
	 * @since 1.5.8
	 *
	 * @param array $args Additional arguments array.
	 *
	 * @return array
	 */
	protected function get_multiple( $args = array() ) {

		$args = apply_filters( 'wpforms_get_multiple_forms_args', $args );

		// No ID provided, get multiple forms.
		$defaults = array(
			'orderby'       => 'id',
			'order'         => 'ASC',
			'no_found_rows' => true,
			'nopaging'      => true,
		);

		$args = wp_parse_args( $args, $defaults );

		$args['post_type'] = 'wpforms';

		return get_posts( $args );
	}

	/**
	 * Delete forms.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ids Form IDs.
	 *
	 * @return bool
	 */
	public function delete( $ids = array() ) {

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$ids = array_map( 'absint', $ids );

		foreach ( $ids as $id ) {

			// Check for permissions.
			if ( ! wpforms_current_user_can( 'delete_form_single', $id ) ) {
				return false;
			}

			if ( class_exists( 'WPForms_Entry_Handler', false ) ) {
				wpforms()->entry->delete_by( 'form_id', $id );
				wpforms()->entry_meta->delete_by( 'form_id', $id );
				wpforms()->entry_fields->delete_by( 'form_id', $id );
			}

			$form = wp_delete_post( $id, true );

			if ( ! $form ) {
				return false;
			}
		}

		do_action( 'wpforms_delete_form', $ids );

		return true;
	}

	/**
	 * Add new form.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title Form title.
	 * @param array  $args  Additional arguments.
	 * @param array  $data  Form data.
	 *
	 * @return mixed
	 */
	public function add( $title = '', $args = array(), $data = array() ) {

		// Must have a title.
		if ( empty( $title ) ) {
			return false;
		}

		// Check for permissions.
		if ( ! wpforms_current_user_can( 'create_forms' ) ) {
			return false;
		}

		// This filter breaks forms if they contain HTML.
		remove_filter( 'content_save_pre', 'balanceTags', 50 );

		// Add filter of the link rel attr to avoid JSON damage.
		add_filter( 'wp_targeted_link_rel', '__return_empty_string', 50, 1 );

		$args = apply_filters( 'wpforms_create_form_args', $args, $data );

		$form_content = array(
			'field_id' => '0',
			'settings' => array(
				'form_title' => sanitize_text_field( $title ),
				'form_desc'  => '',
			),
		);

		// Prevent $args['post_content'] from overwriting predefined $form_content.
		// Typically it happens if the form was created with a form template and a user was not redirected to a form editing screen afterwards.
		// This is only possible if a user has 'wpforms_create_forms' and no 'wpforms_edit_own_forms' capability.
		if ( isset( $args['post_content'] ) && is_array( json_decode( wp_unslash( $args['post_content'] ), true ) ) ) {
			$args['post_content'] = wpforms_encode( array_replace_recursive( $form_content, json_decode( wp_unslash( $args['post_content'] ), true ) ) );
		}

		// Merge args and create the form.
		$form = wp_parse_args(
			$args,
			array(
				'post_title'   => esc_html( $title ),
				'post_status'  => 'publish',
				'post_type'    => 'wpforms',
				'post_content' => wpforms_encode( $form_content ),
			)
		);

		$form_id = wp_insert_post( $form );

		// If user has no editing permissions the form considered to be created out of the WPForms form builder's context.
		if ( ! wpforms_current_user_can( 'edit_form_single', $form_id ) ) {
			$data['builder'] = false;
		}

		// If the form is created outside the context of the WPForms form
		// builder, then we define some additional default values.
		if ( ! empty( $form_id ) && isset( $data['builder'] ) && $data['builder'] === false ) {
			$form_data                                       = json_decode( wp_unslash( $form['post_content'] ), true );
			$form_data['id']                                 = $form_id;
			$form_data['settings']['submit_text']            = esc_html__( 'Submit', 'wpforms-lite' );
			$form_data['settings']['submit_text_processing'] = esc_html__( 'Sending...', 'wpforms-lite' );
			$form_data['settings']['notification_enable']    = '1';
			$form_data['settings']['notifications']          = array(
				'1' => array(
					'email'          => '{admin_email}',
					'subject'        => sprintf( esc_html__( 'New Entry: %s', 'wpforms-lite' ), esc_html( $title ) ),
					'sender_name'    => get_bloginfo( 'name' ),
					'sender_address' => '{admin_email}',
					'replyto'        => '{field_id="1"}',
					'message'        => '{all_fields}',
				),
			);
			$form_data['settings']['confirmations']          = array(
				'1' => array(
					'type'           => 'message',
					'message'        => esc_html__( 'Thanks for contacting us! We will be in touch with you shortly.', 'wpforms-lite' ),
					'message_scroll' => '1',
				),
			);

			$this->update( $form_id, $form_data, array( 'cap' => 'create_forms' ) );
		}

		do_action( 'wpforms_create_form', $form_id, $form, $data );

		return $form_id;
	}

	/**
	 * Update form.
	 *
	 * @since    1.0.0
	 *
	 * @param string|int $form_id Form ID.
	 * @param array      $data    Data retrieved from $_POST and processed.
	 * @param array      $args    Empty by default, may have custom data not intended to be saved.
	 *
	 * @return mixed
	 * @internal param string $title
	 */
	public function update( $form_id = '', $data = array(), $args = array() ) {

		if ( empty( $data ) ) {
			return false;
		}

		if ( empty( $form_id ) && isset( $data['id'] ) ) {
			$form_id = $data['id'];
		}

		if ( ! isset( $args['cap'] ) ) {
			$args['cap'] = 'edit_form_single';
		}

		if ( ! empty( $args['cap'] ) && ! wpforms_current_user_can( $args['cap'], $form_id ) ) {
			return false;
		}

		// This filter breaks forms if they contain HTML.
		remove_filter( 'content_save_pre', 'balanceTags', 50 );

		// Add filter of the link rel attr to avoid JSON damage.
		add_filter( 'wp_targeted_link_rel', '__return_empty_string', 50, 1 );

		$data = wp_unslash( $data );

		$title = empty( $data['settings']['form_title'] ) ? get_the_title( $form_id ) : $data['settings']['form_title'];
		$desc  = empty( $data['settings']['form_desc'] ) ? '' : $data['settings']['form_desc'];

		$data['field_id'] = ! empty( $data['field_id'] ) ? absint( $data['field_id'] ) : '0';

		// Preserve form meta.
		$meta = $this->get_meta( $form_id );
		if ( $meta ) {
			$data['meta'] = $meta;
		}

		// Preserve fields meta.
		if ( isset( $data['fields'] ) ) {
			$data['fields'] = $this->update__preserve_fields_meta( $data['fields'], $form_id );
		}

		// Sanitize - don't allow tags for users who do not have appropriate cap.
		// If we don't do this, forms for these users can get corrupt due to
		// conflicts with wp_kses().
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$data = map_deep( $data, 'wp_strip_all_tags' );
		}

		// Sanitize notifications names.
		if ( isset( $data['settings']['notifications'] ) ) {
			$data['settings']['notifications'] = $this->update__sanitize_notifications_names( $data['settings']['notifications'] );
		}
		unset( $notification );

		$form = apply_filters(
			'wpforms_save_form_args',
			array(
				'ID'           => $form_id,
				'post_title'   => esc_html( $title ),
				'post_excerpt' => $desc,
				'post_content' => wpforms_encode( $data ),
			),
			$data,
			$args
		);

		$_form_id = wp_update_post( $form );

		do_action( 'wpforms_save_form', $_form_id, $form );

		return $_form_id;
	}

	/**
	 * Preserve fields meta in 'update' method.
	 *
	 * @since 1.5.8
	 *
	 * @param array      $fields  Form fields.
	 * @param string|int $form_id Form ID.
	 *
	 * @return array
	 */
	protected function update__preserve_fields_meta( $fields, $form_id ) {

		foreach ( $fields as $i => $field_data ) {
			if ( isset( $field_data['id'] ) ) {
				$field_meta = $this->get_field_meta( $form_id, $field_data['id'] );
				if ( $field_meta ) {
					$fields[ $i ]['meta'] = $field_meta;
				}
			}
		}

		return $fields;
	}

	/**
	 * Sanitize notifications names meta in 'update' method.
	 *
	 * @since 1.5.8
	 *
	 * @param array $notifications Form notifications.
	 *
	 * @return array
	 */
	protected function update__sanitize_notifications_names( $notifications ) {

		foreach ( $notifications as $id => &$notification ) {
			if ( ! empty( $notification['notification_name'] ) ) {
				$notification['notification_name'] = sanitize_text_field( $notification['notification_name'] );
			}
		}

		return $notifications;
	}

	/**
	 * Duplicate forms.
	 *
	 * @since 1.1.4
	 *
	 * @param array $ids Form IDs to duplicate.
	 *
	 * @return bool
	 */
	public function duplicate( $ids = array() ) {

		// Check for permissions.
		if ( ! wpforms_current_user_can( 'create_forms' ) ) {
			return false;
		}

		// Add filter of the link rel attr to avoid JSON damage.
		add_filter( 'wp_targeted_link_rel', '__return_empty_string', 50, 1 );

		// This filter breaks forms if they contain HTML.
		remove_filter( 'content_save_pre', 'balanceTags', 50 );

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$ids = array_map( 'absint', $ids );

		foreach ( $ids as $id ) {

			// Get original entry.
			$form = get_post( $id );

			if ( ! wpforms_current_user_can( 'view_form_single', $id ) ) {
				return false;
			}

			// Confirm form exists.
			if ( ! $form || empty( $form ) ) {
				return false;
			}

			// Get the form data.
			$new_form_data = wpforms_decode( $form->post_content );

			// Remove form ID from title if present.
			$new_form_data['settings']['form_title'] = str_replace( '(ID #' . absint( $id ) . ')', '', $new_form_data['settings']['form_title'] );

			// Create the duplicate form.
			$new_form    = array(
				'post_content' => wpforms_encode( $new_form_data ),
				'post_excerpt' => $form->post_excerpt,
				'post_status'  => $form->post_status,
				'post_title'   => $new_form_data['settings']['form_title'],
				'post_type'    => $form->post_type,
			);
			$new_form_id = wp_insert_post( $new_form );

			if ( ! $new_form_id || is_wp_error( $new_form_id ) ) {
				return false;
			}

			// Set new form name.
			$new_form_data['settings']['form_title'] .= ' (ID #' . absint( $new_form_id ) . ')';

			// Set new form ID.
			$new_form_data['id'] = absint( $new_form_id );

			// Update new duplicate form.
			$new_form_id = $this->update( $new_form_id, $new_form_data, array( 'cap' => 'create_forms' ) );

			if ( ! $new_form_id || is_wp_error( $new_form_id ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the next available field ID and increment by one.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $form_id Form ID.
	 * @param array      $args    Additional arguments.
	 *
	 * @return mixed int or false
	 */
	public function next_field_id( $form_id, $args = array() ) {

		if ( empty( $form_id ) ) {
			return false;
		}

		$defaults = array(
			'content_only' => true,
		);

		if ( isset( $args['cap'] ) ) {
			$defaults['cap'] = $args['cap'];
		}

		$form = $this->get( $form_id, $defaults );

		if ( empty( $form ) ) {
			return false;
		}

		if ( ! empty( $form['field_id'] ) ) {

			$field_id = absint( $form['field_id'] );

			if ( ! empty( $form['fields'] ) && max( array_keys( $form['fields'] ) ) > $field_id ) {
				$field_id = max( array_keys( $form['fields'] ) ) + 1;
			}

			$form['field_id'] = $field_id + 1;

		} else {
			$field_id         = '0';
			$form['field_id'] = '1';
		}

		$this->update( $form_id, $form );

		return $field_id;
	}

	/**
	 * Get private meta information for a form.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $form_id Form ID.
	 * @param string     $field   Field.
	 * @param array      $args    Additional arguments.
	 *
	 * @return false|array
	 */
	public function get_meta( $form_id, $field = '', $args = array() ) {

		if ( empty( $form_id ) ) {
			return false;
		}

		$defaults = array(
			'content_only' => true,
		);

		if ( isset( $args['cap'] ) ) {
			$defaults['cap'] = $args['cap'];
		}

		$data = $this->get( $form_id, $defaults );

		if ( isset( $data['meta'] ) ) {
			if ( empty( $field ) ) {
				return $data['meta'];
			} elseif ( isset( $data['meta'][ $field ] ) ) {
				return $data['meta'][ $field ];
			}
		}

		return false;
	}

	/**
	 * Update or add form meta information to a form.
	 *
	 * @since 1.4.0
	 *
	 * @param string|int $form_id    Form ID.
	 * @param string     $meta_key   Meta key.
	 * @param mixed      $meta_value Meta value.
	 * @param array      $args       Additional arguments.
	 *
	 * @return bool
	 */
	public function update_meta( $form_id, $meta_key, $meta_value, $args = array() ) {

		if ( empty( $form_id ) || empty( $meta_key ) ) {
			return false;
		}

		// Add filter of the link rel attr to avoid JSON damage.
		add_filter( 'wp_targeted_link_rel', '__return_empty_string', 50, 1 );

		// This filter breaks forms if they contain HTML.
		remove_filter( 'content_save_pre', 'balanceTags', 50 );

		if ( ! isset( $args['cap'] ) ) {
			$args['cap'] = 'edit_form_single';
		}

		$form = $this->get_single( absint( $form_id ), $args );

		if ( empty( $form ) ) {
			return false;
		}

		$data     = wpforms_decode( $form->post_content );
		$meta_key = wpforms_sanitize_key( $meta_key );

		$data['meta'][ $meta_key ] = $meta_value;

		$form    = array(
			'ID'           => $form_id,
			'post_content' => wpforms_encode( $data ),
		);
		$form    = apply_filters( 'wpforms_update_form_meta_args', $form, $data );
		$form_id = wp_update_post( $form );

		do_action( 'wpforms_update_form_meta', $form_id, $form, $meta_key, $meta_value );

		return $form_id;
	}

	/**
	 * Delete form meta information from a form.
	 *
	 * @since 1.4.0
	 *
	 * @param string|int $form_id  Form ID.
	 * @param string     $meta_key Meta key.
	 * @param array      $args     Additional arguments.
	 *
	 * @return bool
	 */
	public function delete_meta( $form_id, $meta_key, $args = array() ) {

		if ( empty( $form_id ) || empty( $meta_key ) ) {
			return false;
		}

		// Add filter of the link rel attr to avoid JSON damage.
		add_filter( 'wp_targeted_link_rel', '__return_empty_string', 50, 1 );

		// This filter breaks forms if they contain HTML.
		remove_filter( 'content_save_pre', 'balanceTags', 50 );

		if ( ! isset( $args['cap'] ) ) {
			$args['cap'] = 'edit_form_single';
		}

		$form = $this->get_single( absint( $form_id ), $args );

		if ( empty( $form ) ) {
			return false;
		}

		$data     = wpforms_decode( $form->post_content );
		$meta_key = wpforms_sanitize_key( $meta_key );

		unset( $data['meta'][ $meta_key ] );

		$form    = array(
			'ID'           => $form_id,
			'post_content' => wpforms_encode( $data ),
		);
		$form    = apply_filters( 'wpforms_delete_form_meta_args', $form, $data );
		$form_id = wp_update_post( $form );

		do_action( 'wpforms_delete_form_meta', $form_id, $form, $meta_key );

		return $form_id;
	}

	/**
	 * Get private meta information for a form field.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $form_id  Form ID.
	 * @param string     $field_id Field ID.
	 * @param array      $args     Additional arguments.
	 *
	 * @return array|bool
	 */
	public function get_field( $form_id, $field_id = '', $args = array() ) {

		if ( empty( $form_id ) ) {
			return false;
		}

		$defaults = array(
			'content_only' => true,
		);

		if ( isset( $args['cap'] ) ) {
			$defaults['cap'] = $args['cap'];
		}

		$data = $this->get( $form_id, $defaults );

		return isset( $data['fields'][ $field_id ] ) ? $data['fields'][ $field_id ] : false;
	}

	/**
	 * Get private meta information for a form field.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $form_id  Form ID.
	 * @param string     $field_id Field ID.
	 * @param array      $args     Additional arguments.
	 *
	 * @return array|bool
	 */
	public function get_field_meta( $form_id, $field_id = '', $args = array() ) {

		$field = $this->get_field( $form_id, $field_id, $args );
		if ( ! $field ) {
			return false;
		}

		return isset( $field['meta'] ) ? $field['meta'] : false;
	}
}
