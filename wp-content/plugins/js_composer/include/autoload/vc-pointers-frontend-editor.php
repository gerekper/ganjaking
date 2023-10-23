<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Add WP ui pointers to backend editor.
 */
function vc_frontend_editor_pointer() {
	vc_is_frontend_editor() && add_filter( 'vc-ui-pointers', 'vc_frontend_editor_register_pointer' );
}

add_action( 'admin_init', 'vc_frontend_editor_pointer' );

/**
 * @param $pointers
 * @return mixed
 */
function vc_frontend_editor_register_pointer( $pointers ) {
	global $post;
	if ( is_object( $post ) && ! strlen( $post->post_content ) ) {
		$pointers['vc_pointers_frontend_editor'] = array(
			'name' => 'vcPointerController',
			'messages' => array(
				array(
					'target' => '#vc_add-new-element',
					'options' => array(
						'content' => sprintf( '<h3> %s </h3> <p> %s </p>', esc_html__( 'Add Elements', 'js_composer' ), esc_html__( 'Add new element or start with a template.', 'js_composer' ) ),
						'position' => array(
							'edge' => 'top',
							'align' => 'left',
						),
						'buttonsEvent' => 'vcPointersEditorsTourEvents',
					),
					'closeEvent' => 'shortcodes:add',
				),
				array(
					'target' => '.vc_controls-out-tl:first',
					'options' => array(
						'content' => sprintf( '<h3> %s </h3> <p> %s </p>', esc_html__( 'Rows and Columns', 'js_composer' ), esc_html__( 'This is a row container. Divide it into columns and style it. You can add elements into columns.', 'js_composer' ) ),
						'position' => array(
							'edge' => 'left',
							'align' => 'center',
						),
						'buttonsEvent' => 'vcPointersEditorsTourEvents',
					),
					'closeCallback' => 'vcPointersCloseInIFrame',
					'showCallback' => 'vcPointersSetInIFrame',
				),
				array(
					'target' => '.vc_controls-cc:first',
					'options' => array(
						'content' => sprintf( '<h3> %s </h3> <p> %s <br/><br/> %s</p>', esc_html__( 'Control Elements', 'js_composer' ), esc_html__( 'You can edit your element at any time and drag it around your layout.', 'js_composer' ), sprintf( esc_html__( 'P.S. Learn more at our %1$sKnowledge Base%2$s.', 'js_composer' ), '<a href="https://kb.wpbakery.com" target="_blank">', '</a>' ) ),
						'position' => array(
							'edge' => 'left',
							'align' => 'center',
						),
						'buttonsEvent' => 'vcPointersEditorsTourEvents',
					),
					'closeCallback' => 'vcPointersCloseInIFrame',
					'showCallback' => 'vcPointersSetInIFrame',
				),
			),
		);
	}

	return $pointers;
}

function vc_page_editable_enqueue_pointer_scripts() {
	if ( vc_is_page_editable() ) {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}
}

add_action( 'wp_enqueue_scripts', 'vc_page_editable_enqueue_pointer_scripts' );
