<?php

// Porto Page Header
add_action( 'vc_after_init', 'porto_load_page_header_shortcode' );
add_action( 'save_post', 'porto_check_page_header_shortcode', 10, 2 );

function porto_check_page_header_shortcode( $post_id, $post = false ) {
	if ( isset( $_POST['action'] ) && 'elementor_ajax' == $_POST['action'] ) {
		return;
	}
	$post_content = '';
	if ( defined( 'VCV_VERSION' ) && wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'vcv-admin-ajax' == $_REQUEST['action'] && isset( $_REQUEST['vcv-admin-ajax'] ) && isset( $_REQUEST['vcv-zip'] ) && false !== $post && $post->post_content ) {
		$post_content = $post->post_content;
	} else {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && 'post' == $screen->base && isset( $_POST['content'] ) ) {
			$post_content = $_POST['content'];
		}
	}

	if ( $post_content ) {
		if ( stripos( $post_content, '[porto_page_header ' ) !== false ) {
			preg_match( '/\[porto_page_header\sbreadcrumbs_type=([^ ]*)([^]]*)\]/', $post_content, $matches );
			$breadcrumbs_type = '1';
			if ( isset( $matches[1] ) ) {
				$breadcrumbs_type = str_replace( array( '\\', '"' ), '', $matches[1] );
			}
			update_post_meta( $post_id, 'porto_page_header_shortcode_type', $breadcrumbs_type );
		} else {
			delete_post_meta( $post_id, 'porto_page_header_shortcode_type' );
		}
	}
}

function porto_load_page_header_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			/* translators: %s: Theme name */
			'name'        => sprintf( __( '%s Page Header', 'porto-functionality' ), 'Porto' ),
			'base'        => 'porto_page_header',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Display the custom page header', 'porto-functionality' ),
			'icon'        => 'fas fa-link',
			'controls'    => 'full',
			'params'      => array(
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'description_header',
					'text'       => esc_html__( 'Please see Theme Options -> Breadcrumbs.  If the type is different with theme option, it doesn\'t work well.', 'porto-functionality' ),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Breadcrumbs Type', 'porto-functionality' ),
					'param_name'  => 'breadcrumbs_type',
					'value'       => array(
						__( 'Theme Options', 'porto-functionality' ) => '',
						__( 'Type 1', 'porto-functionality' ) => '1',
						__( 'Type 2', 'porto-functionality' ) => '2',
						__( 'Type 3', 'porto-functionality' ) => '3',
						__( 'Type 4', 'porto-functionality' ) => '4',
						__( 'Type 5', 'porto-functionality' ) => '5',
						__( 'Type 6', 'porto-functionality' ) => '6',
						__( 'Type 7', 'porto-functionality' ) => '7',
					),
					'admin_label' => true,
				),
				array(
					'type'       => 'checkbox',
					'param_name' => 'hide_page_title',
					'std'        => '',
					'heading'    => __( 'Hide Page Title', 'porto-functionality' ),
					'group'      => __( 'Page Title', 'porto-functionality' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Page Title', 'porto-functionality' ),
					'param_name'  => 'page_title',
					'value'       => '',
					'description' => __( 'Please leave this field blank to display default page title.', 'porto-functionality' ),
					'admin_label' => true,
					'dependency'  => array(
						'element'  => 'hide_page_title',
						'is_empty' => true,
					),
					'group'       => __( 'Page Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Page Title', 'porto-functionality' ),
					'param_name' => 'page_title_font_size',
					'group'      => __( 'Page Title', 'porto-functionality' ),
					'selectors'  => array(
						'.page-top .page-title',
					),
					'dependency' => array(
						'element'  => 'hide_page_title',
						'is_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'page_title_color',
					'dependency' => array(
						'element'  => 'hide_page_title',
						'is_empty' => true,
					),
					'group'      => __( 'Page Title', 'porto-functionality' ),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Margin Bottom', 'porto-functionality' ),
					'param_name' => 'page_title_margin_bottom',
					'dependency' => array(
						'element'  => 'hide_page_title',
						'is_empty' => true,
					),
					'group'      => __( 'Page Title', 'porto-functionality' ),
				),

				array(
					'type'        => 'textfield',
					'heading'     => __( 'Page Sub Title', 'porto-functionality' ),
					'param_name'  => 'page_sub_title',
					'value'       => '',
					'admin_label' => true,
					'group'       => __( 'Page Subtitle', 'porto-functionality' ),
					'description' => __( 'Please leave this field blank to display default page subtitle.', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Page Subtitle', 'porto-functionality' ),
					'param_name' => 'page_subtitle_font',
					'group'      => __( 'Page Subtitle', 'porto-functionality' ),
					'selectors'  => array(
						'.page-top .page-sub-title',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Sub Title Color', 'porto-functionality' ),
					'param_name' => 'page_subtitle_color',
					'group'      => __( 'Page Subtitle', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'param_name' => 'hide_breadcrumb',
					'heading'    => __( 'Hide Breadcrumbs', 'porto-functionality' ),
					'value'      => array(
						__( 'Hide Breadcrumbs', 'porto-functionality' ) => 'yes',
					),
					'group'      => __( 'Path', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Breadcrumb Path', 'porto-functionality' ),
					'param_name' => 'breadcrumbs_font',
					'group'      => __( 'Path', 'porto-functionality' ),
					'dependency' => array(
						'element'  => 'hide_breadcrumb',
						'is_empty' => true,
					),
					'selectors'  => array(
						'.page-top .breadcrumbs-wrap ul.breadcrumb > li',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Delimiter Font Size', 'porto-functionality' ),
					'param_name' => 'delimiter_font_size',
					'units'      => array( 'px', 'rem' ),
					'group'      => __( 'Path', 'porto-functionality' ),
					'dependency' => array(
						'element'  => 'hide_breadcrumb',
						'is_empty' => true,
					),
					'selectors'  => array(
						'.page-top ul.breadcrumb > li i.delimiter' => 'font-size: {{VALUE}}{{UNIT}}',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Breadcrumbs Text Color', 'porto-functionality' ),
					'param_name' => 'breadcrumbs_text_color',
					'group'      => __( 'Path', 'porto-functionality' ),
					'dependency' => array(
						'element'  => 'hide_breadcrumb',
						'is_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Breadcrumbs Link Color', 'porto-functionality' ),
					'param_name' => 'breadcrumbs_link_color',
					'group'      => __( 'Path', 'porto-functionality' ),
					'dependency' => array(
						'element'  => 'hide_breadcrumb',
						'is_empty' => true,
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Margin Top', 'porto-functionality' ),
					'description' => __( 'Controls the margin top of breadcrumb path.', 'porto-functionality' ),
					'param_name'  => 'bc_margin_top',
					'group'       => __( 'Path', 'porto-functionality' ),
					'selectors'   => array(
						'.page-top .breadcrumbs-wrap' => 'margin-top: {{VALUE}};',
					),
					'dependency'  => array(
						'element'  => 'hide_breadcrumb',
						'is_empty' => true,
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Margin Bottom', 'porto-functionality' ),
					'description' => __( 'Controls the margin bottom of breadcrumb path.', 'porto-functionality' ),
					'param_name'  => 'bc_margin_bottom',
					'group'       => __( 'Path', 'porto-functionality' ),
					'selectors'   => array(
						'.page-top .breadcrumbs-wrap' => 'margin-bottom: {{VALUE}};',
					),
					'dependency'  => array(
						'element'  => 'hide_breadcrumb',
						'is_empty' => true,
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Page_Header' ) ) {
		class WPBakeryShortCode_Porto_Page_Header extends WPBakeryShortCode {
		}
	}
}
