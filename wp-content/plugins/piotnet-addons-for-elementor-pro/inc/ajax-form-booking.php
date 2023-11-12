<?php
	add_action( 'wp_ajax_pafe_form_booking', 'pafe_form_booking' );
	add_action( 'wp_ajax_nopriv_pafe_form_booking', 'pafe_form_booking' );

	function find_element_recursive_form_booking( $elements, $form_id ) {
		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = find_element_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	function pafe_form_booking() {
		$post_id = $_POST['post_id'];
		$element_id = $_POST['element_id'];
		$date = $_POST['date'];
		$form_id = $_POST['form_id'];

		if (!empty($element_id) && !empty($post_id)) {
			$settings = $_POST['form_booking'];

			require_once( __DIR__ . '/../inc/templates/template-form-booking.php' );

			pafe_template_form_booking($settings, $element_id, $post_id, $date, $form_id);
		}

		wp_die(); 
	}