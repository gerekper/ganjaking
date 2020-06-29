<?php
/**
 * Created by PhpStorm.
 * User: Your Inspiration
 * Date: 20/01/2015
 * Time: 12:04
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


$array_form     = array();
$active_plugins = array(
	'none' => __( 'Select a form type', 'yith-woocommerce-popup' ),
);

if ( function_exists( 'YIT_Contact_Form' ) ) {
	$active_plugins['yit-contact-form'] = __( 'YIT Contact Form', 'yith-woocommerce-popup' );
}

if ( function_exists( 'wpcf7_contact_form' ) ) {
	$active_plugins['contact-form-7'] = __( 'Contact Form 7', 'yith-woocommerce-popup' );
}


if ( ! empty( $active_plugins ) || function_exists( 'YIT_Contact_Form' ) || function_exists( 'wpcf7_contact_form' ) ) {

	$array_form['form-type'] = array(
		'label'   => __( 'Request form', 'yith-woocommerce-popup' ),
		'desc'    => __( 'Choose one. You can also add forms from YIT Contact Form or Contact Form 7 that must be installed and activated.', 'yith-woocommerce-popup' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'options' => $active_plugins,
		'std'     => 'none',
	);

	$array_form['form-contact-form-7'] = array(
		'label'   => __( 'Contact form 7', 'yith-woocommerce-popup' ),
		'desc'    => __( 'Choose the form to display', 'yith-woocommerce-popup' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'options' => yith_ypop_wpcf7_get_contact_forms(),
		'std'     => '',
		'deps'    => array(
			'ids'    => '_form-type',
			'values' => 'contact-form-7',
		),

	);

	$array_form['form-yit-contact-form'] = array(
		'label'   => __( 'YIT Contact Form', 'yith-woocommerce-popup' ),
		'desc'    => __( 'Choose the form to display', 'yith-woocommerce-popup' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'options' => yith_ypop_get_contact_forms(),
		'std'     => '',
		'deps'    => array(
			'ids'    => '_form-type',
			'values' => 'yit-contact-form',
		),
	);



} else {

	$no_form_plugin = __( 'To use this feature, YIT Contact Form or Contact Form 7 must be installed and activated.', 'yith-woocommerce-popup' );

}

$type_of_content = array(
	'text'       => __( 'Text', 'yith-woocommerce-popup' ),
	'newsletter' => __( 'Newsletter', 'yith-woocommerce-popup' ),
	'form'       => __( 'Form', 'yith-woocommerce-popup' ),
	'social'     => __( 'Social network', 'yith-woocommerce-popup' ),
);

if ( function_exists( 'WC' ) ) {
	$type_of_content['woocommerce'] = __( 'WooCommerce', 'yith-woocommerce-popup' );
}

$integration_types = YITH_Popup_Newsletter()->get_integration();
$options           = array(
	'label'    => __( 'Popup Settings', 'yith-woocommerce-popup' ),
	'pages'    => 'yith_popup',
	'context'  => 'normal', // ('normal', 'advanced', or 'side')
	'priority' => 'default',
	'tabs'     => array(
		/*************************************
		 * CONTENT TAB
		 */
		'content'       => array(
			'label'  => __( 'Content', 'yith-woocommerce-popup' ),
			'fields' => apply_filters(
				'ypop_content_metabox',
				array(

					/*************************************
					 * GENERAL OPTIONS
					 */
					'enable_popup'          => array(
						'label' => __( 'Enable popup', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',

					),
					'content_type'          => array(
						'label'   => __( 'Content type', 'yith-woocommerce-popup' ),
						'desc'    => __( 'Select the type of the content', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'std'     => 'newsletter',
						'options' => $type_of_content,
					),

					/*************************************
					 * THEME 1 CONTENT
					 */
					'theme1_header'         => array(
						'label' => __( 'Header', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'desc'  => __( 'Add the header content of the popup', 'yith-woocommerce-popup' ),
						'std'   => __( 'SIGN UP TO OUR NEWSLETTER AND SAVE 25% OFF FOR YOUR NEXT PURCHASE', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_content'        => array(
						'label' => __( 'Content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '<h3>Increase more than 500% of Email Subscribers!</h3>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis viverra, urna vitae vehicula congue, purus nibh vestibulum lacus, sit amet tristique ante odio.</p>',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_footer_content' => array(
						'label' => __( 'Footer content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the footer of the popup', 'yith-woocommerce-popup' ),
						'std'   => '<img src="' . YITH_YPOP_TEMPLATE_URL . '/themes/theme1/images/icon-lock.png"> Your Information will never be shared with any third party.',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),

					/*************************************
					 * THEME 2 CONTENT
					 */
					'theme2_header_content' => array(
						'label' => __( 'Header content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the header content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '<h2 style="text-align: center;"><span style="color: #306582;">Get it NOW!</span></h2>
<p style="text-align: center;">Increase more than 700% of Email Subscribers!</p>',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_left_content'   => array(
						'label' => __( 'Left content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the left content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '<img class="aligncenter" src="' . YITH_YPOP_TEMPLATE_URL . '/themes/theme2/images/1.jpg" alt="1"  />',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_right_content'  => array(
						'label' => __( 'Right content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the right content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_footer_content' => array(
						'label' => __( 'Footer content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the footer of the popup', 'yith-woocommerce-popup' ),
						'std'   => '<img src="' . YITH_YPOP_TEMPLATE_URL . '/themes/theme2/images/icon-lock.png"> Your Information will never be shared with any third party.',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),

					/*************************************
					 * THEME 3 CONTENT
					 */
					'theme3_header_title'   => array(
						'label' => __( 'Header title', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the header content of the popup', 'yith-woocommerce-popup' ),
						'std'   => 'SUMMER<br>SALES!',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_right_content'  => array(
						'label' => __( 'Right content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the right content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_footer_content' => array(
						'label' => __( 'Footer content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the footer content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '<img src="' . YITH_YPOP_TEMPLATE_URL . '/themes/theme3/images/icon-lock.png"> Your Information will never be shared with any third party.',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),

					/*************************************
					 * THEME 4 CONTENT
					 */
					'theme4_header_title'   => array(
						'label' => __( 'Header content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the header content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '<h2>ARE YOU READY?<br>GET IT NOW!</h2><p>Increase more than 500% of Email Subscribers!</p>',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_footer_content' => array(
						'label' => __( 'Footer content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the footer content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),

					/*************************************
					 * THEME 5 CONTENT
					 */
					'theme5_header'         => array(
						'label' => __( 'Header', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'desc'  => __( 'Add the header content of the popup', 'yith-woocommerce-popup' ),
						'std'   => __( 'GREAT DISCOUNT ON MAKEUP!', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_left_content'   => array(
						'label' => __( 'Left content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the left content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '<img class="alignleft" src="' . YITH_YPOP_TEMPLATE_URL . '/themes/theme5/images/picture.jpg" />',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_right_content'  => array(
						'label' => __( 'Right content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the right content of the popup', 'yith-woocommerce-popup' ),
						'std'   => __( '<strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam placerat commodo quam, vel malesuada metus.</strong> Suspendisse suscipit laoreet ante, ut posuere purus ultrices vitae. Etiam eget felis a diam tristique lacinia sed id lorem. Morbi sed quam ac odio ultricies condimentum non sit amet urna. ', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_footer_content' => array(
						'label' => __( 'Footer content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the footer content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '<img class="alignleft" src="' . YITH_YPOP_TEMPLATE_URL . '/themes/theme5/images/icon-lock.png"> Your Information will never be shared with any third party.',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),

					/*************************************
					 * THEME 6 CONTENT
					 */
					'theme6_left_content'   => array(
						'label' => __( 'Left content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the left content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '<img class="aligncenter" src="' . YITH_YPOP_TEMPLATE_URL . '/themes/theme6/images/header_left.png" />',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_right_content'  => array(
						'label' => __( 'Right content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the right content of the popup', 'yith-woocommerce-popup' ),
						'std'   => __( '<h2 style="color:#990000;">SPECIAL OFFER</h2><h4 style="color:#000000;">ON LEATHER RED BAG</h4><img class="aligncenter" src="' . YITH_YPOP_TEMPLATE_URL . '/themes/theme6/images/picture.jpg" /><p>Suspendisse suscipit laoreet ante, ut posuere purus ultrices vitae. <a href="#">Etiam eget felis a diam tristiq!</a></p>', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_footer_content' => array(
						'label' => __( 'Footer content', 'yith-woocommerce-popup' ),
						'type'  => 'textarea-editor',
						'desc'  => __( 'Add the footer content of the popup', 'yith-woocommerce-popup' ),
						'std'   => '',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
				)
			),
		),
		/*************************************
		 * LAYOUT TAB
		 */
		'layout'        => array(
			'label'  => __( 'Layout', 'yith-woocommerce-popup' ),
			'fields' => apply_filters(
				'ypop_layout_metabox',
				array(

					/*************************************
					 * THEME 1 LAYOUT
					 */

					'theme1_width'                         => array(
						'label' => __( 'Width', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the width of the popup.', 'yith-woocommerce-popup' ),
						'min'   => 10,
						'max'   => 2000,
						'std'   => 550,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_height'                        => array(
						'label' => __( 'Height', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the height of the popup. Leave 0 to set it automatically', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the height of the popup. Leave 0 to set it automatically', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 0,

						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_body_bg_color'                 => array(
						'label' => __( 'Background color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color of the popup', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_header_bg_image'               => array(
						'label' => __( 'Header background image', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background image for the header', 'yith-woocommerce-popup' ),
						'type'  => 'upload',
						'std'   => YITH_YPOP_TEMPLATE_URL . '/themes/theme1/images/header.png',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_header_height'                 => array(
						'label' => __( 'Header height', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the height of the header popup', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 159,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_header_color'                  => array(
						'label' => __( 'Header color', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'desc'  => __( 'Select the color of the header', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_footer_bg_color'               => array(
						'label' => __( 'Footer background color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color of the footer', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#f4f4f4',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_label_position'                => array(
						'label'   => __( 'Position of the field title in newsletter content type', 'yith-woocommerce-popup' ),
						'desc'    => __( 'Select the position of the label ', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'std'     => 'label',
						'options' => array(
							'label'       => __( 'Label', 'yith-woocommerce-popup' ),
							'placeholder' => __( 'Placeholder', 'yith-woocommerce-popup' ),
						),
						'deps'    => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_submit_button_bg_color'        => array(
						'label' => __( 'Background color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ff8a00',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_submit_button_bg_color_hover'  => array(
						'label' => __( 'Background color on hover for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color on hover for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#db7600',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					'theme1_submit_button_color'           => array(
						'label' => __( 'Color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the text color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme1',
						),
					),
					// 'theme1_submit_button_icon'           => array(
					// 'label'   => __( 'Icon for submit button', 'yith-woocommerce-popup' ),
					// 'desc'    => '',
					// 'type'    => 'iconlist',
					// 'options' => array(
					// 'select' => array(
					// 'icon'   => __( 'Theme Icon', 'yith-woocommerce-popup' ),
					// 'custom' => __( 'Custom Icon', 'yith-woocommerce-popup' ),
					// 'none'   => __( 'None', 'yith-woocommerce-popup' )
					// ),
					// 'icon'   => ''
					// ),
					// 'std'     => array(
					// 'select' => 'custom',
					// 'icon'   => 'retinaicon-font:retina-the-essentials-082',
					// 'custom' => YITH_YPOP_TEMPLATE_URL . '/themes/theme1/images/submit-icon.png',
					// ),
					// 'deps'    => array(
					// 'ids'    => '_template_name',
					// 'values' => 'theme1',
					// )
					// ),

					/*************************************
					 * THEME 2 LAYOUT
					 */

					'theme2_width'                         => array(
						'label' => __( 'Width', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the width of the popup.', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 750,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_height'                        => array(
						'label' => __( 'Height', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the height of the popup. Leave 0 to set it automatically', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 670,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_body_bg_color'                 => array(
						'label' => __( 'Background color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color of the popup', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_content_bg_color'              => array(
						'label' => __( 'Content background color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color of the content', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#36c7d2',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_content_link_color'            => array(
						'label' => __( 'Content link color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color for links', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_content_link_color_hover'      => array(
						'label' => __( 'Content link hover color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color on hover for the link', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#306582',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_header_bg_image'               => array(
						'label' => __( 'Header background image', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background image for the header', 'yith-woocommerce-popup' ),
						'type'  => 'upload',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_header_bg_color'               => array(
						'label' => __( 'Header background color', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'desc'  => __( 'Select the color of the header', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_header_height'                 => array(
						'label' => __( 'Header height', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the height of the header', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 0,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_header_color'                  => array(
						'label' => __( 'Header color', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#484848',
						'desc'  => __( 'Select the color of the header', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_header_border_bottom_color'    => array(
						'label' => __( 'Header border color', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#e3e3e3',
						'desc'  => __( 'Select the border color of the header', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_footer_bg_color'               => array(
						'label' => __( 'Footer background color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color of the footer', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_label_position'                => array(
						'label'   => __( 'Position of the field title in newsletter', 'yith-woocommerce-popup' ),
						'desc'    => __( 'Select the position of the label ', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'std'     => 'placeholder',
						'options' => array(
							'label'       => __( 'Label', 'yith-woocommerce-popup' ),
							'placeholder' => __( 'Placeholder', 'yith-woocommerce-popup' ),
						),
						'deps'    => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_submit_button_bg_color'        => array(
						'label' => __( 'Background color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#eb5949',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_submit_button_bg_color_hover'  => array(
						'label' => __( 'Background color on hover for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color on hover for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#a01000',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					'theme2_submit_button_color'           => array(
						'label' => __( 'Color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the text color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme2',
						),
					),
					// 'theme2_submit_button_icon'           => array(
					// 'label'   => __( 'Icon for submit button', 'yith-woocommerce-popup' ),
					// 'desc'    => '',
					// 'type'    => 'iconlist',
					// 'options' => array(
					// 'select' => array(
					// 'icon'   => __( 'Theme icon', 'yith-woocommerce-popup' ),
					// 'custom' => __( 'Custom icon', 'yith-woocommerce-popup' ),
					// 'none'   => __( 'None', 'yith-woocommerce-popup' )
					// ),
					// 'icon'   => ''
					// ),
					// 'std'     => array(
					// 'select' => 'custom',
					// 'icon'   => 'retinaicon-font:retina-the-essentials-082',
					// 'custom' => YITH_YPOP_TEMPLATE_URL . '/themes/theme3/images/submit-icon.png',
					// ),
					// 'deps'    => array(
					// 'ids'    => '_template_name',
					// 'values' => 'theme2',
					// )
					// ),

					/*************************************
					 * THEME 3 LAYOUT
					 */

					'theme3_width'                         => array(
						'label' => __( 'Width', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the width of the popup.', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 750,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_height'                        => array(
						'label' => __( 'Height', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the height of the popup. Leave 0 to set it automatically', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 510,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_bg_image'                      => array(
						'label' => __( 'Background image', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background image', 'yith-woocommerce-popup' ),
						'type'  => 'upload',
						'std'   => YITH_YPOP_TEMPLATE_URL . '/themes/theme3/images/bg.jpg',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_body_bg_color'                 => array(
						'label' => __( 'Background color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color of the popup', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#516fc8',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_content_link_color'            => array(
						'label' => __( 'Content link color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color for links', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_content_link_color_hover'      => array(
						'label' => __( 'Content link hover color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color on hover for the links', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ff6b43',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_text_color'                    => array(
						'label' => __( 'Text color', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#fff',
						'desc'  => __( 'Select the color of the text', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_label_position'                => array(
						'label'   => __( 'Position of the field title in newsletter', 'yith-woocommerce-popup' ),
						'desc'    => __( 'Select the position of the label', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'std'     => 'placeholder',
						'options' => array(
							'label'       => __( 'Label', 'yith-woocommerce-popup' ),
							'placeholder' => __( 'Placeholder', 'yith-woocommerce-popup' ),
						),
						'deps'    => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_submit_button_bg_color'        => array(
						'label' => __( 'Background color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ff6b43',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_submit_button_bg_color_hover'  => array(
						'label' => __( 'Background color on hover for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color on hover for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ff4614',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					'theme3_submit_button_color'           => array(
						'label' => __( 'Color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the text color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme3',
						),
					),
					// 'theme3_submit_button_icon'           => array(
					// 'label'   => __( 'Icon for submit button', 'yith-woocommerce-popup' ),
					// 'desc'    => '',
					// 'type'    => 'iconlist',
					// 'options' => array(
					// 'select' => array(
					// 'icon'   => __( 'Theme icon', 'yith-woocommerce-popup' ),
					// 'custom' => __( 'Custom icon', 'yith-woocommerce-popup' ),
					// 'none'   => __( 'None', 'yith-woocommerce-popup' )
					// ),
					// 'icon'   => ''
					// ),
					// 'std'     => array(
					// 'select' => 'none',
					// 'icon'   => 'retinaicon-font:retina-the-essentials-082',
					// 'custom' => YITH_YPOP_TEMPLATE_URL . '/themes/theme3/images/submit-icon.png',
					// ),
					// 'deps'    => array(
					// 'ids'    => '_template_name',
					// 'values' => 'theme3',
					// )
					// ),

					/*************************************
					 * THEME 4 LAYOUT
					 */

					'theme4_width'                         => array(
						'label' => __( 'Width', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the width of the popup.', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 750,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_height'                        => array(
						'label' => __( 'Height', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the height of the popup. Leave 0 to set it automatically', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 380,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_bg_image'                      => array(
						'label' => __( 'Background image', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background image for the header', 'yith-woocommerce-popup' ),
						'type'  => 'upload',
						'std'   => YITH_YPOP_TEMPLATE_URL . '/themes/theme4/images/bg.jpg',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_body_bg_color'                 => array(
						'label' => __( 'Background color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color of the popup', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_content_link_color'            => array(
						'label' => __( 'Content link color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color for the links', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_content_link_color_hover'      => array(
						'label' => __( 'Content link hover color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color on hover for links', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ff4200',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_text_color'                    => array(
						'label' => __( 'Text color', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'desc'  => __( 'Select the color of the text', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_label_position'                => array(
						'label'   => __( 'Position of the field title in newsletter', 'yith-woocommerce-popup' ),
						'desc'    => __( 'Select the position of the label', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'std'     => 'placeholder',
						'options' => array(
							'label'       => __( 'Label', 'yith-woocommerce-popup' ),
							'placeholder' => __( 'Placeholder', 'yith-woocommerce-popup' ),
						),
						'deps'    => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_submit_button_bg_color'        => array(
						'label' => __( 'Background color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ff4200',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_submit_button_bg_color_hover'  => array(
						'label' => __( 'Background color on hoverfor submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color on hover for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#912600',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					'theme4_submit_button_color'           => array(
						'label' => __( 'Color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the text color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme4',
						),
					),
					// 'theme4_submit_button_icon'           => array(
					// 'label'   => __( 'Icon for submit button', 'yith-woocommerce-popup' ),
					// 'desc'    => '',
					// 'type'    => 'iconlist',
					// 'options' => array(
					// 'select' => array(
					// 'icon'   => __( 'Theme icon', 'yith-woocommerce-popup' ),
					// 'custom' => __( 'Custom icon', 'yith-woocommerce-popup' ),
					// 'none'   => __( 'None', 'yith-woocommerce-popup' )
					// ),
					// 'icon'   => ''
					// ),
					// 'std'     => array(
					// 'select' => 'none',
					// 'icon'   => 'retinaicon-font:retina-the-essentials-082',
					// 'custom' => YITH_YPOP_TEMPLATE_URL . '/themes/theme4/images/submit-icon.png',
					// ),
					// 'deps'    => array(
					// 'ids'    => '_template_name',
					// 'values' => 'theme4',
					// )
					// ),

					/*************************************
					 * THEME 5 LAYOUT
					 */

					'theme5_width'                         => array(
						'label' => __( 'Width', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the width of the popup.', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 750,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_height'                        => array(
						'label' => __( 'Height', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the height of the popup. Leave 0 to set it automatically', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 525,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_body_bg_color'                 => array(
						'label' => __( 'Background color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color of the popup', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_border_color'                  => array(
						'label' => __( 'Border color', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#b68e67',
						'desc'  => __( 'Select the color of borders', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),

					'theme5_content_link_color'            => array(
						'label' => __( 'Content link color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color for links', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#b68e67',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_content_link_color_hover'      => array(
						'label' => __( 'Content link hover color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color on hover for the links', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#b57434',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_text_color'                    => array(
						'label' => __( 'Text color', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#5c5c5c',
						'desc'  => __( 'Select the color of the header', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_header_bg_image'               => array(
						'label' => __( 'Header background image', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background image for the header', 'yith-woocommerce-popup' ),
						'type'  => 'upload',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_header_bg_color'               => array(
						'label' => __( 'Header background color', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'desc'  => __( 'Select the color of the header', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),

					'theme5_label_position'                => array(
						'label'   => __( 'Position of the field title in newsletter', 'yith-woocommerce-popup' ),
						'desc'    => __( 'Select the position of the label', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'std'     => 'placeholder',
						'options' => array(
							'label'       => __( 'Label', 'yith-woocommerce-popup' ),
							'placeholder' => __( 'Placeholder', 'yith-woocommerce-popup' ),
						),
						'deps'    => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_submit_button_bg_color'        => array(
						'label' => __( 'Background color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#b68e67',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_submit_button_bg_color_hover'  => array(
						'label' => __( 'Background color on hover for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color on hover for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#9e7b5a',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					'theme5_submit_button_color'           => array(
						'label' => __( 'Color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the text color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme5',
						),
					),
					// 'theme5_submit_button_icon'           => array(
					// 'label'   => __( 'Icon for submit button', 'yith-woocommerce-popup' ),
					// 'desc'    => '',
					// 'type'    => 'iconlist',
					// 'options' => array(
					// 'select' => array(
					// 'icon'   => __( 'Theme icon', 'yith-woocommerce-popup' ),
					// 'custom' => __( 'Custom icon', 'yith-woocommerce-popup' ),
					// 'none'   => __( 'None', 'yith-woocommerce-popup' )
					// ),
					// 'icon'   => ''
					// ),
					// 'std'     => array(
					// 'select' => 'custom',
					// 'icon'   => 'retinaicon-font:retina-the-essentials-082',
					// 'custom' => YITH_YPOP_TEMPLATE_URL . '/themes/theme5/images/submit-icon.png',
					// ),
					// 'deps'    => array(
					// 'ids'    => '_template_name',
					// 'values' => 'theme5',
					// )
					// ),

					/*************************************
					 * THEME 6 LAYOUT
					 */
					'theme6_width'                         => array(
						'label' => __( 'Width', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the width of the popup.', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 750,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_height'                        => array(
						'label' => __( 'Height', 'yith-woocommerce-popup' ),
						'type'  => 'number',
						'desc'  => __( 'Select the height of the popup. Leave 0 to set it automatically', 'yith-woocommerce-popup' ),
						'min'   => 0,
						'max'   => 2000,
						'std'   => 500,
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),

					'theme6_body_bg_color_left'            => array(
						'label' => __( 'Background color on the left', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color of the left side', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#990000',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_content_left_link_color'       => array(
						'label' => __( 'Content link color on the left side', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color for the link on the left side', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_content_left_link_color_hover' => array(
						'label' => __( 'Content link hover color on the left side ', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color for the link on hover on the left side', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_left_text_color'               => array(
						'label' => __( 'Text color on the left side', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'desc'  => __( 'Select the color of the text on the left side', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),

					'theme6_body_bg_color_right'           => array(
						'label' => __( 'Background color on the right', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color on the right side', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_content_right_link_color'      => array(
						'label' => __( 'Content link color on the right side', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color for the link on the right side', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#990000',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_content_right_link_color_hover' => array(
						'label' => __( 'Content link hover color on the right side', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the color for the link on hover on the right side', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#990000',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_right_text_color'              => array(
						'label' => __( 'Text color on the right side', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#000000',
						'desc'  => __( 'Select the color of the text on the right side', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),

					'theme6_label_position'                => array(
						'label'   => __( 'Position of the field title in newsletter', 'yith-woocommerce-popup' ),
						'desc'    => __( 'Select the position of the title', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'std'     => 'placeholder',
						'options' => array(
							'label'       => __( 'Label', 'yith-woocommerce-popup' ),
							'placeholder' => __( 'Placeholder', 'yith-woocommerce-popup' ),
						),
						'deps'    => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),

					'theme6_submit_button_bg_color'        => array(
						'label' => __( 'Background color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#000000',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_submit_button_bg_color_hover'  => array(
						'label' => __( 'Background color on hover for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color on hover for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#000000',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),
					'theme6_submit_button_color'           => array(
						'label' => __( 'Color for submit button', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the text color for submit button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ffffff',
						'deps'  => array(
							'ids'    => '_template_name',
							'values' => 'theme6',
						),
					),

					'submit_button_icon'                   => array(
						'label' => __( 'Icon for submit button', 'plugin-test-domain' ),
						'desc'  => __( 'Set your icon', 'plugin-test-domain' ),
						'type'  => 'iconlist',
						'std'   => 'FontAwesome:envelope-o',
					),

					/*************************************
					 * COMMON LAYOUT OPTIONS
					 */
					'checkzone_bg_color'                   => array(
						'label' => __( 'Background color for "Hide" text area', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color for "Hide" text area', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => 'transparent',
					),
					'checkzone_text_color'                 => array(
						'label' => __( 'Text color for "Hide" text', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the text color for "Hide" text', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#333333',
					),


				)
			),
		),
		'display'       => array(
			'label'  => __( 'Display Settings', 'yith-woocommerce-popup' ),
			'fields' => apply_filters(
				'ypop_display_metabox',
				array(
					'position'        => array(
						'label'   => __( 'Position', 'yith-woocommerce-popup' ),
						'desc'    => '',
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'center'       => __( 'Center', 'yith-woocommerce-popup' ),
							'left-top'     => __( 'Left Top', 'yith-woocommerce-popup' ),
							'left-bottom'  => __( 'Left Bottom', 'yith-woocommerce-popup' ),
							'right-top'    => __( 'Right Top', 'yith-woocommerce-popup' ),
							'right-bottom' => __( 'Right Bottom', 'yith-woocommerce-popup' ),
						),
						'std'     => 'center',
					),
					'sep'             => array(
						'type' => 'sep',
					),
					'overlay_opacity' => array(
						'label' => __( 'Overlay opacity', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'slider',
						'min'   => 0,
						'max'   => 100,
						'step'  => 10,
						'std'   => 50,
					),
					'overlay_color'   => array(
						'label' => __( 'Overlay color', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'colorpicker',
						'std'   => '#000000',
					),
					'sep1'            => array(
						'type' => 'sep',
					),
					'when_display'    => array(
						'label'   => __( 'Choose when displaying the popup', 'yith-woocommerce-popup' ),
						'desc'    => '',
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'load'           => __( 'As soon as the page has been loaded', 'yith-woocommerce-popup' ),
							'leave-viewport' => __( 'When mouse leaves the browser viewport (Not available on mobile devices)', 'yith-woocommerce-popup' ),
							'leave-page'     => __( 'When users try to leave the page.', 'yith-woocommerce-popup' ),
							'external-link'  => __( 'When users click an external link', 'yith-woocommerce-popup' ),
							'internal-link'  => __( 'When users click an link with #yithpopup in url', 'yith-woocommerce-popup' ),
						),
						'std'     => 'load',
					),

					'delay'           => array(
						'label' => __( 'Delay time before the popup appears', 'yith-woocommerce-popup' ),
						'desc'  => __( 'in seconds', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '0',

					),


				)
			),
		),
		'close'         => array(
			'label'  => __( 'Closing button', 'yith-woocommerce-popup' ),
			'fields' => apply_filters(
				'ypop_close_metabox',
				array(
					'close_button_icon'             => array(
						'label'   => __( 'Closing button icon', 'yith-woocommerce-popup' ),
						'desc'    => __( 'Select the icon for closing button', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'close1' => __( 'Closing button 1', 'yith-woocommerce-popup' ),
							'close2' => __( 'Closing button 2', 'yith-woocommerce-popup' ),
							'close3' => __( 'Closing button 3', 'yith-woocommerce-popup' ),
							'close4' => __( 'Closing button 4', 'yith-woocommerce-popup' ),
							'custom' => __( 'Custom image', 'yith-woocommerce-popup' ),
						),
						'std'     => 'close1',
					),
					'close_button_icon_preview'     => array(
						'label' => '',
						'type'  => 'preview',
						'std'   => YITH_YPOP_ASSETS_URL . '/images/close-buttons/preview/close1.png',
						'deps'  => array(
							'ids'    => '_close_button_icon',
							'values' => 'close1',
						),
					),
					'close_button_icon_preview2'    => array(
						'label' => '',
						'type'  => 'preview',
						'std'   => YITH_YPOP_ASSETS_URL . '/images/close-buttons/preview/close2.png',
						'deps'  => array(
							'ids'    => '_close_button_icon',
							'values' => 'close2',
						),
					),
					'close_button_icon_preview3'    => array(
						'label' => '',
						'type'  => 'preview',
						'std'   => YITH_YPOP_ASSETS_URL . '/images/close-buttons/preview/close3.png',
						'deps'  => array(
							'ids'    => '_close_button_icon',
							'values' => 'close3',
						),
					),
					'close_button_icon_preview4'    => array(
						'label' => '',
						'type'  => 'preview',
						'std'   => YITH_YPOP_ASSETS_URL . '/images/close-buttons/preview/close4.png',
						'deps'  => array(
							'ids'    => '_close_button_icon',
							'values' => 'close4',
						),
					),


					'close_button_custom_icon'      => array(
						'label' => __( 'Closing button custom icon', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Upload the icon for closing button', 'yith-woocommerce-popup' ),
						'type'  => 'upload',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_close_button_icon',
							'values' => 'custom',
						),
					),

					'close_button_background_color' => array(
						'label' => __( 'Closing button background color', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select the background color of the closing button', 'yith-woocommerce-popup' ),
						'type'  => 'colorpicker',
						'std'   => '#ff8a00',
					),

				)
			),
		),
		'customization' => array(
			'label'  => __( 'Customization', 'yith-woocommerce-popup' ),
			'fields' => apply_filters(
				'ypop_customization_metabox',
				array(
					'ypop_css'        => array(
						'label' => __( 'CSS', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'textarea',
						'std'   => '',
					),
					'sep'             => array(
						'type' => 'sep',
					),
					'ypop_javascript' => array(
						'label' => __( 'JavaScript', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'textarea',
						'std'   => '',
					),
				)
			),
		),
		'newsletter'    => apply_filters(
			'yith-popup-newsletter-metabox',
			array(
				'label'  => __( 'Newsletter', 'yith-woocommerce-popup' ),
				'fields' => array(
					'newsletter-integration'          => array(
						'label'   => __( 'Form integration preset', 'yith-woocommerce-popup' ),
						'desc'    => __( 'Select what kind of newsletter service you want to use, or set a custom form.', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => $integration_types,
						'std'     => 'custom',
					),

					'newsletter-action'               => array(
						'label' => __( 'Form action', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The attribute "action" of the form.', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),

					'newsletter-method'               => array(
						'label'   => __( 'Request method', 'yith-woocommerce-popup' ),
						'desc'    => __( 'The attribute "method" of the form.', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'post' => __( 'POST', 'yith-woocommerce-popup' ),
							'get'  => __( 'GET', 'yith-woocommerce-popup' ),
						),
						'std'     => 'post',
						'deps'    => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),

					'newsletter-show-name'            => array(
						'label' => __( 'Show name field', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Show the "Name" field in the newsletter', 'yith-woocommerce-popup' ),
						'type'  => 'onoff',
						'std'   => 'no',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),

					'newsletter-name-label'           => array(
						'label' => __( 'Name field label', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The label for "Name" field', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => 'Your Name',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),

					'newsletter-name-name'            => array(
						'label' => __( '"Name" attribute of the Name field', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The "Name" attribute of the Name field.', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => 'ypop_name',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),

					'newsletter-email-label'          => array(
						'label' => __( 'Email field label', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The label for the "Email" field', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => 'Email',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),

					'newsletter-email-name'           => array(
						'label' => __( '"Name" attribute for Email field', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The attribute "Name" of the email address field.', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => 'ypop_email',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),

					'newsletter-add-privacy-checkbox' => array(
						'label' => __( 'Add Privacy Policy', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'no',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),
					'newsletter-privacy-name'         => array(
						'label' => __( '"Name" attribute of the Privacy field', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The "Name" attribute of the Privacy field.', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => 'ypop_privacy',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),
					'newsletter-privacy-label'        => array(
						'label' => __( 'Privacy Policy Label', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'text',
						'std'   => __( 'I have read and agree to the website terms and conditions.', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),

					'newsletter-privacy-description'  => array(
						'label' => __( 'Privacy Policy Description', 'yith-woocommerce-popup' ),
						'desc'  => __( 'You can use the shortcode [privacy_policy] (from WordPress 4.9.6) to add the link to privacy policy page', 'yith-woocommerce-popup' ),
						'type'  => 'textarea',
						'std'   => __( 'Your personal data will be used to process your request, support your experience throughout this website, and for other purposes described in our [privacy_policy].', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),

					'newsletter-submit-label'         => array(
						'label' => __( 'Submit button label', 'yith-woocommerce-popup' ),
						'desc'  => __( 'This field is not always used. It depends on the style of the form.', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => __( 'Add Me', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),

					'newsletter-hidden-fields'        => array(
						'label' => __( 'Hidden fields', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Type here all hidden field names and values in a serial way. Example: name1=value1&name2=value2.', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'custom',
						),
					),
				),
			)
		),
		'form'          => apply_filters(
			'yith-popup-form-metabox',
			array(
				'label'  => __( 'Form', 'yith-woocommerce-popup' ),
				'fields' => $array_form,
			)
		),
		'social'        => apply_filters(
			'yith-popup-social-metabox',
			array(
				'label'  => __( 'Social network', 'yith-woocommerce-popup' ),
				'fields' => array(
					'social_view_icon' => array(
						'label' => __( 'Show social network sharing as icon', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Select this option to show social networks as icon, and not as text', 'yith-woocommerce-popup' ),
						'type'  => 'onoff',
						'std'   => 'yes',
					),
					'facebook_button'  => array(
						'label' => __( 'Show Facebook button', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',
					),
					'facebook_url'     => array(
						'label' => __( 'Facebook like URL', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Add the url for Facebook like, leave empty to link to current page', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_facebook_button',
							'values' => 'yes',
						),
					),
					'sep'              => array(
						'type' => 'sep',
					),

					'twitter_button'   => array(
						'label' => __( 'Show Twitter', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',
					),
					'twitter_url'      => array(
						'label' => __( 'Twitter URL', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Add the URL for Twitter, leave empty to link to current page', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_twitter_button',
							'values' => 'yes',
						),
					),
					'sep1'             => array(
						'type' => 'sep',
					),

					'google_button'    => array(
						'label' => __( 'Show Google+ Button', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',
					),
					'google_url'       => array(
						'label' => __( 'Google+ URL', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Add the URL for Google+, leave empty to link to current page', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_google_button',
							'values' => 'yes',
						),
					),

					'sep2'             => array(
						'type' => 'sep',
					),

					'linkedin_button'  => array(
						'label' => __( 'Show LinkedIn Button', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',
					),
					'linkedin_url'     => array(
						'label' => __( 'LinkedIn URL', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Add the URL for LinkedIn, leave empty to link to current page', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_linkedin_button',
							'values' => 'yes',
						),
					),


					'sep3'             => array(
						'type' => 'sep',
					),

					'pinterest_button' => array(
						'label' => __( 'Show Pinterest Button', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',
					),
					'pinterest_url'    => array(
						'label' => __( 'Pinterest URL', 'yith-woocommerce-popup' ),
						'desc'  => __( 'Add the URL for Pinterest, leave empty to link to current page', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_pinterest_button',
							'values' => 'yes',
						),
					),
				),
			)
		),
	),
);

if ( function_exists( 'WC' ) ) {
	$woocommerce_options = array(
		'woocommerce' => array(
			'label'  => __( 'WooCommerce', 'yith-woocommerce-popup' ),
			'fields' => apply_filters(
				'ypop_woocommerce_metabox',
				array(
					'ypop_product_from'          => array(
						'label'   => __( 'Choose a random product to show from', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'desc'    => '',
						'options' => array(
							'product'  => __( 'A products list', 'yith-woocommerce-popup' ),
							'category' => __( 'Categories', 'yith-woocommerce-popup' ),
							'onsale'   => __( 'Discounted items', 'yith-woocommerce-popup' ),
							'featured' => __( 'Featured items', 'yith-woocommerce-popup' ),
						),
					),
					'ypop_products'              => array(
						'label'    => __( 'Select products', 'yith-woocommerce-popup' ),
						'desc'     => '',
						'type'     => 'ajax-products',
						'multiple' => true,
						'options'  => array(),
						'std'      => array(),
						'deps'     => array(
							'ids'    => '_ypop_product_from',
							'values' => 'product',
						),
					),


					'ypop_category'              => array(
						'label'    => __( 'Select categories', 'yith-woocommerce-popup' ),
						'desc'     => '',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'multiple' => true,
						'options'  => ypop_get_shop_categories( false ),
						'std'      => array(),
						'deps'     => array(
							'ids'    => '_ypop_product_from',
							'values' => 'category',
						),
					),

					'show_title'                 => array(
						'label' => __( 'Show name of product', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',
					),


					'show_thumbnail'             => array(
						'label' => __( 'Show thumbnail of product', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',
					),

					'show_price'                 => array(
						'label' => __( 'Show price of product', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',
					),

					'show_add_to_cart'           => array(
						'label' => __( 'Show Add to Cart', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',
					),
					'redirect_after_add_to_cart' => array(
						'label'   => __( 'Redirect user', 'yith-woocommerce-popup' ),
						'desc'    => '',
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'none'         => __( 'None', 'yith-woocommerce-popup' ),
							'home'         => __( 'Home', 'yith-woocommerce-popup' ),
							'same_page'    => __( 'Current Page', 'yith-woocommerce-popup' ),
							'product_page' => __( 'Product Page', 'yith-woocommerce-popup' ),
						),
						'std'     => 'home',

					),

					'add_to_cart_label'          => array(
						'label' => __( '"Add to cart" Label', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'text',
						'std'   => __( 'Add to cart', 'yith-woocommerce-popup' ),
					),


					'show_summary'               => array(
						'label' => __( 'Show summary', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'yes',
					),



				)
			),
		),
	);

	$options['tabs'] = array_merge( $options['tabs'], $woocommerce_options );
}

return $options;


