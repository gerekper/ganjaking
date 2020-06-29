<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ywctm_exists_inquiry_forms() ) {

	$label       = esc_html__( 'Choose form', 'yith-woocommerce-catalog-mode' );
	$description = esc_html__( 'Choose the form to display in product page.', 'yith-woocommerce-catalog-mode' );
	$forms       = array( 'yit-contact-form', 'contact-form-7', 'ninja-forms', 'formidable-forms', 'gravity-forms' );
	$options     = array();
	$fields      = array();

	if ( ywctm_is_wpml_active() ) {

		$languages = apply_filters( 'wpml_active_languages', null, array() );

		foreach ( $forms as $form ) {

			foreach ( $languages as $language ) {
				$fields[ $language['language_code'] ] = array(
					'label'   => $language['translated_name'],
					'options' => ywctm_get_forms_list( $form ),
					'type'    => 'select',
					'std'     => '',
				);
			}

			$options[ $form ] = array(
				'name'      => $label,
				'type'      => 'yith-field',
				'yith-type' => 'yith-multiple-field',
				'class'     => 'ywctm-multiple-languages',
				'desc'      => $description,
				'id'        => 'ywctm_inquiry_' . str_replace( '-', '_', $form ) . '_id_wpml' . ywctm_get_vendor_id(),
				'fields'    => $fields,
				'deps'      => array(
					'id'    => 'ywctm_inquiry_form_type' . ywctm_get_vendor_id(),
					'value' => $form,
					'type'  => 'hide-disable',
				),
			);
		}
	} else {

		foreach ( $forms as $form ) {

			$options[ $form ] = array(
				'name'      => $label,
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'class'     => 'wc-enhanced-select',
				'desc'      => $description,
				'id'        => 'ywctm_inquiry_' . str_replace( '-', '_', $form ) . '_id' . ywctm_get_vendor_id(),
				'options'   => ywctm_get_forms_list( $form ),
				'deps'      => array(
					'id'    => 'ywctm_inquiry_form_type' . ywctm_get_vendor_id(),
					'value' => $form,
					'type'  => 'hide-disable',
				),
			);

		}
	}

	return array(
		'inquiry-form' => array(
			'inquiry_form_title'   => array(
				'name' => esc_html__( 'Inquiry Form', 'yith-woocommerce-catalog-mode' ),
				'type' => 'title',
			),
			'inquiry_form_setting' => array(
				'name'      => esc_html__( 'Set inquiry form as:', 'yith-woocommerce-catalog-mode' ),
				'type'      => 'yith-field',
				'yith-type' => 'radio',
				'desc'      => esc_html__( 'Choose whether to enable the inquiry form to all products or only to the ones in the exclusion list.', 'yith-woocommerce-catalog-mode' ),
				'options'   => array(
					'hidden'    => esc_html__( 'Hidden in all products', 'yith-woocommerce-catalog-mode' ),
					'visible'   => esc_html__( 'Visible in all products', 'yith-woocommerce-catalog-mode' ),
					'exclusion' => esc_html__( 'Visible in items of Exclusion list only', 'yith-woocommerce-catalog-mode' ),
				),
				'default'   => 'hidden',
				'id'        => 'ywctm_inquiry_form_enabled' . ywctm_get_vendor_id(),
			),
			'inquiry_form'         => array(
				'name'      => esc_html__( 'Form Plugin', 'yith-woocommerce-catalog-mode' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'class'     => 'wc-enhanced-select',
				'desc'      => esc_html__( 'Choose from which activated plugin you want to display the inquiry form in the product page.', 'yith-woocommerce-catalog-mode' ),
				'options'   => ywctm_get_active_form_plugins(),
				'default'   => 'none',
				'id'        => 'ywctm_inquiry_form_type' . ywctm_get_vendor_id(),
			),
			'yit_contact_form'     => $options['yit-contact-form'],
			'contact_form_7'       => $options['contact-form-7'],
			'ninja_forms'          => $options['ninja-forms'],
			'formidable_forms'     => $options['formidable-forms'],
			'gravity_forms'        => $options['gravity-forms'],
			'where_show'           => array(
				'name'      => esc_html__( 'Show form in:', 'yith-woocommerce-catalog-mode' ),
				'type'      => 'yith-field',
				'yith-type' => 'radio',
				'options'   => array(
					'tab'  => esc_html__( 'WooCommerce Tabs', 'yith-woocommerce-catalog-mode' ),
					'desc' => esc_html__( 'Short description area', 'yith-woocommerce-catalog-mode' ),
				),
				'default'   => 'tab',
				'id'        => 'ywctm_inquiry_form_where_show' . ywctm_get_vendor_id(),
				'desc'      => esc_html__( 'Choose if to show the inquiry form inside a WooCommerce tab or in the short description area.', 'yith-woocommerce-catalog-mode' ),

			),
			'form_position'        => array(
				'name'      => esc_html__( 'Form Position', 'yith-woocommerce-catalog-mode' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'class'     => 'wc-enhanced-select',
				'options'   => array(
					'15' => esc_html__( 'After price', 'yith-woocommerce-catalog-mode' ),
					'25' => esc_html__( 'After short description', 'yith-woocommerce-catalog-mode' ),
					'35' => esc_html__( 'After "Add to cart" button', 'yith-woocommerce-catalog-mode' ),
				),
				'default'   => '15',
				'id'        => 'ywctm_inquiry_form_position' . ywctm_get_vendor_id(),
				'deps'      => array(
					'id'    => 'ywctm_inquiry_form_where_show' . ywctm_get_vendor_id(),
					'value' => 'desc',
					'type'  => 'fadeIn',
				),
			),
			'form_style'           => array(
				'name'      => esc_html__( 'Form style', 'yith-woocommerce-catalog-mode' ),
				'type'      => 'yith-field',
				'yith-type' => 'radio',
				'options'   => array(
					'classic' => esc_html__( 'Classic', 'yith-woocommerce-catalog-mode' ),
					'toggle'  => esc_html__( 'Hidden in toggle', 'yith-woocommerce-catalog-mode' ),
				),
				'default'   => 'classic',
				'id'        => 'ywctm_inquiry_form_style' . ywctm_get_vendor_id(),
				'deps'      => array(
					'id'    => 'ywctm_inquiry_form_where_show' . ywctm_get_vendor_id(),
					'value' => 'desc',
					'type'  => 'fadeIn',
				),
				'desc'      => esc_html__( 'Choose whether to show the form is visible in the page or it is hidden in a toggled section.', 'yith-woocommerce-catalog-mode' ),
			),
			'tab_title'            => array(
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'name'              => esc_html__( 'Tab title', 'yith-woocommerce-catalog-mode' ),
				'id'                => 'ywctm_inquiry_form_tab_title' . ywctm_get_vendor_id(),
				'default'           => esc_html__( 'Inquiry form', 'yith-woocommerce-catalog-mode' ),
				'custom_attributes' => 'required',
			),
			'text_before'          => array(
				'type'          => 'yith-field',
				'yith-type'     => 'textarea-editor',
				'media_buttons' => false,
				'wpautop'       => false,
				'textarea_rows' => 5,
				'name'          => esc_html__( 'Text before form', 'yith-woocommerce-catalog-mode' ),
				'id'            => 'ywctm_text_before_form' . ywctm_get_vendor_id(),
				'desc'          => esc_html__( 'Enter a custom text to show before the inquiry form.', 'yith-woocommerce-catalog-mode' ),
			),
			'toggle_button'        => array(
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'name'      => esc_html__( 'Toggle button text', 'yith-woocommerce-catalog-mode' ),
				'id'        => 'ywctm_toggle_button_text' . ywctm_get_vendor_id(),
				'default'   => esc_html__( 'Send an inquiry', 'yith-woocommerce-catalog-mode' ),
				'deps'      => array(
					'id'    => 'ywctm_inquiry_form_style' . ywctm_get_vendor_id(),
					'value' => 'toggle',
					'type'  => 'fadeIn',
				),
			),
			'button_text_colors'   => array(
				'id'           => 'ywctm_toggle_button_text_color' . ywctm_get_vendor_id(),
				'type'         => 'yith-field',
				'yith-type'    => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'default',
						'name'    => esc_html__( 'Default', 'yith-woocommerce-catalog-mode' ),
						'default' => '#247390',
					),
					array(
						'id'      => 'hover',
						'name'    => esc_html__( 'Hover', 'yith-woocommerce-catalog-mode' ),
						'default' => '#FFFFFF',
					),
				),
				'name'         => esc_html__( 'Toggle button text colors', 'yith-woocommerce-catalog-mode' ),
				'deps'         => array(
					'id'    => 'ywctm_inquiry_form_style' . ywctm_get_vendor_id(),
					'value' => 'toggle',
					'type'  => 'fadeIn',
				),
			),
			'button_background'    => array(
				'id'           => 'ywctm_toggle_button_background_color' . ywctm_get_vendor_id(),
				'type'         => 'yith-field',
				'yith-type'    => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'default',
						'name'    => esc_html__( 'Default', 'yith-woocommerce-catalog-mode' ),
						'default' => '#FFFFFF',
					),
					array(
						'id'      => 'hover',
						'name'    => esc_html__( 'Hover', 'yith-woocommerce-catalog-mode' ),
						'default' => '#247390',
					),
				),
				'name'         => esc_html__( 'Toggle button colors', 'yith-woocommerce-catalog-mode' ),
				'deps'         => array(
					'id'    => 'ywctm_inquiry_form_style' . ywctm_get_vendor_id(),
					'value' => 'toggle',
					'type'  => 'fadeIn',
				),
			),
			'product_permalink'    => array(
				'name'      => esc_html__( 'Include product Permalink', 'yith-woocommerce-catalog-mode' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html__( 'Use this option to include the product permalink to the email body (it does not work with YIT Contact Form).', 'yith-woocommerce-catalog-mode' ) . '<br />' . esc_html__( 'In this way, it is possible to see from which product page the message was sent.', 'yith-woocommerce-catalog-mode' ),
				'id'        => 'ywctm_inquiry_product_permalink' . ywctm_get_vendor_id(),
				'default'   => 'no',
			),
			'inquiry_form_end'     => array(
				'type' => 'sectionend',
			),
		),
	);

} else {

	$links = array(
		sprintf( '<b>%2$s:</b> <a href="%1$s">%1$s</a>', 'https://wordpress.org/plugins/contact-form-7/', 'Contact Form 7' ) . '<br /><br />',
		sprintf( '<b>%2$s:</b> <a href="%1$s">%1$s</a>', 'https://wordpress.org/plugins/ninja-forms/', 'Ninja Forms' ) . '<br /><br />',
		sprintf( '<b>%2$s:</b> <a href="%1$s">%1$s</a>', 'https://wordpress.org/plugins/formidable/', 'Formidable Forms' ) . '<br /><br />',
		sprintf( '<b>%2$s:</b> <a href="%1$s">%1$s</a>', 'https://www.gravityforms.com/', 'Gravity Forms' ),
	);

	return array(
		'inquiry-form' => array(
			'inquiry_form_title' => array(
				'name' => esc_html__( 'Inquiry Form', 'yith-woocommerce-catalog-mode' ),
				'type' => 'title',
				'desc' => esc_html__( 'To use this feature, Contact Form 7, Ninja Forms, Formidable Forms or Gravity Forms must be installed and activated.', 'yith-woocommerce-catalog-mode' ),
			),
			'forms_list'         => array(
				'type'             => 'yith-field',
				'yith-type'        => 'html',
				'html'             => implode( '', $links ),
				'yith-display-row' => false,
			),
			'inquiry_form_end'   => array(
				'type' => 'sectionend',
			),
		),
	);

}
