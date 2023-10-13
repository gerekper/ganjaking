<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Ask an estimate settings settings
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Options
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * APPLY_FILTERS: yith_wcwl_ask_an_estimate_options
 *
 * Filter the options available in the 'Ask for an estimate' tab.
 *
 * @param array $options Array of options
 *
 * @return array
 */
return array(
	'settings-ask_an_estimate' => apply_filters(
		'yith_wcwl_ask_an_estimate_options',
		array(
			'yith_ask_an_estimate_start'             => array(
				'name' => __( 'Ask for an estimate', 'yith-woocommerce-wishlist' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'yith_wcwl_ask_an_estimate',
			),

			'enable_ask_an_estimate'                 => array(
				'name'      => __( 'Enable "Ask for an estimate" button', 'yith-woocommerce-wishlist' ),
				'desc'      => sprintf(
					'%s. %s <a href="%s">%s</a>',
					__( 'Shows "Ask for an estimate" button on Wishlist page', 'yith-woocommerce-wishlist' ),
					__( 'If you want to customize the email that will be sent to the admin, please, visit the', 'yith-woocommerce-wishlist' ),
					add_query_arg(
						array(
							'page' => 'yith_wcwl_panel',
							'tab'  => 'email',
						),
						admin_url( 'admin.php' )
					),
					__( 'emails settings page', 'yith-woocommerce-wishlist' )
				),
				'id'        => 'yith_wcwl_show_estimate_button',
				'default'   => 'yes',
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
			),
			'yith_ask_an_estimate_end'               => array(
				'type' => 'sectionend',
				'id'   => 'yith_wcwl_ask_an_estimate',
			),

			'ask_an_estimate_fields_section'         => array(
				'name' => __( 'Additional Popup', 'yith-woocommerce-wishlist' ),
				'type' => 'title',
				'desc' => __( 'These fields will be shown in the popup opened by Ask an Estimate button. The Email field will be prepended for unauthenticated users. An Additional Notes textarea will be postponed to the selected fields.', 'yith-woocommerce-wishlist' ),
				'id'   => 'yith_wcwl_additional_fields_settings',
			),

			'enable_ask_an_estimate_additional_info' => array(
				'name'      => __( 'Enable "Additional notes" popup', 'yith-woocommerce-wishlist' ),
				'desc'      => __( 'Show an Additional notes popup before submitting the price estimate request to let customers add extra notes', 'yith-woocommerce-wishlist' ),
				'id'        => 'yith_wcwl_show_additional_info_textarea',
				'default'   => 'no',
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'deps'      => array(
					'id'    => 'yith_wcwl_show_estimate_button',
					'value' => 'yes',
				),
			),

			'yith_wcwl_ask_an_estimate_fields'       => array(
				'id'               => 'yith_wcwl_ask_an_estimate_fields',
				'type'             => 'yith-field',
				'yith-type'        => 'toggle-element',
				'add_button'       => __( 'Add new field', 'yith-woocommerce-wishlist' ),
				'title'            => __( 'Field %%label%%', 'yith-woocommerce-wishlist' ),
				'yith-display-row' => false,
				'elements'         => array(
					array(
						'id'        => 'label',
						'name'      => __( 'Label for the field', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Enter the label that will be shown for this field', 'yith-woocommerce-wishlist' ),
						'type'      => 'yith-field',
						'yith-type' => 'text',
					),
					array(
						'id'        => 'required',
						'name'      => __( 'Required field', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Choose whether this field is required or not', 'yith-woocommerce-wishlist' ),
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
					),
					array(
						'id'        => 'placeholder',
						'name'      => __( 'Placeholder for the field', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Enter the placeholder that will be shown in the field', 'yith-woocommerce-wishlist' ),
						'type'      => 'yith-field',
						'yith-type' => 'text',
					),
					array(
						'id'        => 'description',
						'name'      => __( 'Field description', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Enter the description that will be shown above the field', 'yith-woocommerce-wishlist' ),
						'type'      => 'yith-field',
						'yith-type' => 'textarea',
					),
					array(
						'id'      => 'position',
						'name'    => __( 'Position of the field in the form', 'yith-woocommerce-wishlist' ),
						'desc'    => __( 'Choose between first (the field will be the first in a row that contains two items), last (the field will be the second in a row of two) or wide (the field will take an entire row)', 'yith-woocommerce-wishlist' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'first' => __( 'First', 'yith-woocommerce-wishlist' ),
							'last'  => __( 'Last', 'yith-woocommerce-wishlist' ),
							'wide'  => __( 'Wide', 'yith-woocommerce-wishlist' ),
						),
					),
					array(
						'id'      => 'type',
						'name'    => __( 'Type of field', 'yith-woocommerce-wishlist' ),
						'desc'    => __( 'Choose the type of field to print in the form', 'yith-woocommerce-wishlist' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'text'     => __( 'Text', 'yith-woocommerce-wishlist' ),
							'email'    => __( 'Email', 'yith-woocommerce-wishlist' ),
							'tel'      => __( 'Phone', 'yith-woocommerce-wishlist' ),
							'url'      => __( 'URL', 'yith-woocommerce-wishlist' ),
							'number'   => __( 'Number', 'yith-woocommerce-wishlist' ),
							'date'     => __( 'Date', 'yith-woocommerce-wishlist' ),
							'textarea' => __( 'Textarea', 'yith-woocommerce-wishlist' ),
							'radio'    => __( 'Radio', 'yith-woocommerce-wishlist' ),
							'checkbox' => __( 'Checkbox', 'yith-woocommerce-wishlist' ),
							'select'   => __( 'Select', 'yith-woocommerce-wishlist' ),
						),
					),
					array(
						'id'        => 'options',
						'name'      => __( 'Enter options for the field', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Enter the options for the field type you\'ve selected. Separate options with pipes (|), and key from value with double colon (::). E.g. key::value|key2::value2', 'yith-woocommerce-wishlist' ),
						'type'      => 'yith-field',
						'yith-type' => 'textarea',
					),
				),
				'onoff_field'      => array(
					'id'      => 'active',
					'type'    => 'onoff',
					'default' => 'no',
				),
				'sortable'         => true,
				'save_button'      => array(
					'id'   => 'yith_wcwl_save_field',
					'name' => __( 'Save', 'yith-woocommerce-wishlist' ),
				),
				'delete_button'    => array(
					'id'   => 'yith_wcwl_delete_field',
					'name' => __( 'Delete', 'yith-woocommerce-wishlist' ),
				),
			),

			'ask_an_estimate_fields_end'             => array(
				'type' => 'sectionend',
				'id'   => 'yith_wcwl_additional_fields_settings',
			),

			'text_section_start'                     => array(
				'name' => __( 'Text customization', 'yith-woocommerce-wishlist' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'yith_wcwl_text_section_settings',
			),

			'ask_an_estimate_label'                  => array(
				'name'      => __( '"Ask for an estimate" button label', 'yith-woocommerce-wishlist' ),
				'id'        => 'yith_wcwl_ask_an_estimate_label',
				'desc'      => __( 'This option lets you customize the label of "Ask for an Estimate" button', 'yith-woocommerce-wishlist' ),
				'default'   => __( 'Ask for an estimate', 'yith-woocommerce-wishlist' ),
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'deps'      => array(
					'id'    => 'yith_wcwl_show_estimate_button',
					'value' => 'yes',
				),
			),

			'additional_info_textarea_label'         => array(
				'name'      => __( '"Additional notes" textarea label', 'yith-woocommerce-wishlist' ),
				'id'        => 'yith_wcwl_additional_info_textarea_label',
				'desc'      => __( 'This option lets you customize the label for the "Additional notes" text area', 'yith-woocommerce-wishlist' ),
				'default'   => __( 'Additional notes', 'yith-woocommerce-wishlist' ),
				'type'      => 'yith-field',
				'yith-type' => 'textarea',
			),

			'text_section_end'                       => array(
				'type' => 'sectionend',
				'id'   => 'yith_wcwl_text_section_settings',
			),

			'style_section_start'                    => array(
				'name' => __( 'Style & Color customization', 'yith-woocommerce-wishlist' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'yith_wcwl_style_section_settings',
			),

			'use_buttons'                            => array(
				'name'      => __( 'Style of "Ask for an estimate"', 'yith-woocommerce-wishlist' ),
				'desc'      => __( 'Choose if you want to show a textual "Ask for an Estimate" link or a button', 'yith-woocommerce-wishlist' ),
				'id'        => 'yith_wcwl_ask_an_estimate_style',
				'options'   => array(
					'link'           => __( 'Textual (anchor)', 'yith-woocommerce-wishlist' ),
					'button_default' => __( 'Button with theme style', 'yith-woocommerce-wishlist' ),
					'button_custom'  => __( 'Button with custom style', 'yith-woocommerce-wishlist' ),
				),
				'default'   => 'button_default',
				'type'      => 'yith-field',
				'yith-type' => 'radio',
			),

			'ask_an_estimate_colors'                 => array(
				'name'         => __( '"Ask for an Estimate" button style', 'yith-woocommerce-wishlist' ),
				'id'           => 'yith_wcwl_color_ask_an_estimate',
				'type'         => 'yith-field',
				'yith-type'    => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'desc' => '<br>' . __( 'Choose colors for the "Ask for an estimate" button', 'yith-woocommerce-wishlist' ),
						array(
							'name'    => __( 'Background', 'yith-woocommerce-wishlist' ),
							'id'      => 'background',
							'default' => '#333333',
						),
						array(
							'name'    => __( 'Text', 'yith-woocommerce-wishlist' ),
							'id'      => 'text',
							'default' => '#FFFFFF',
						),
						array(
							'name'    => __( 'Border', 'yith-woocommerce-wishlist' ),
							'id'      => 'border',
							'default' => '#333333',
						),
					),
					array(
						'desc' => '<br>' . __( 'Choose colors for the "Ask for an estimate" button on hover state', 'yith-woocommerce-wishlist' ),
						array(
							'name'    => __( 'Background Hover', 'yith-woocommerce-wishlist' ),
							'id'      => 'background_hover',
							'default' => '#4F4F4F',
						),
						array(
							'name'    => __( 'Text Hover', 'yith-woocommerce-wishlist' ),
							'id'      => 'text_hover',
							'default' => '#FFFFFF',
						),
						array(
							'name'    => __( 'Border Hover', 'yith-woocommerce-wishlist' ),
							'id'      => 'border_hover',
							'default' => '#4F4F4F',
						),
					),
				),
				'deps'         => array(
					'id'    => 'yith_wcwl_ask_an_estimate_style',
					'value' => 'button_custom',
				),
			),

			'rounded_buttons_radius'                 => array(
				'name'      => __( 'Border radius', 'yith-woocommerce-wishlist' ),
				'desc'      => __( 'Choose radius for the "Ask for an Estimate" button', 'yith-woocommerce-wishlist' ),
				'id'        => 'yith_wcwl_ask_an_estimate_rounded_corners_radius',
				'default'   => 16,
				'type'      => 'yith-field',
				'yith-type' => 'slider',
				'min'       => 1,
				'max'       => 100,
				'deps'      => array(
					'id'    => 'yith_wcwl_ask_an_estimate_style',
					'value' => 'button_custom',
				),
			),

			'ask_an_estimate_icon'                   => array(
				'name'      => __( '"Ask for an estimate" icon', 'yith-woocommerce-wishlist' ),
				'desc'      => __( 'Select an icon for the "Ask for an Estimate" button (optional)', 'yith-woocommerce-wishlist' ),
				'id'        => 'yith_wcwl_ask_an_estimate_icon',
				/**
				 * APPLY_FILTERS: yith_wcwl_ask_an_estimate_std_icon
				 *
				 * Filter the default 'Ask for an estimate' icon.
				 *
				 * @param string $icon Default icon
				 *
				 * @return string
				 */
				'default'   => apply_filters( 'yith_wcwl_ask_an_estimate_std_icon', '' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'class'     => 'icon-select',
				'options'   => yith_wcwl_get_plugin_icons(),
				'deps'      => array(
					'id'    => 'yith_wcwl_ask_an_estimate_style',
					'value' => 'button_custom',
				),
			),

			'ask_an_estimate_custom_icon'            => array(
				'name'      => __( '"Ask for an estimate" custom icon', 'yith-woocommerce-wishlist' ),
				'desc'      => __( 'Upload an icon you\'d like to use for "Ask for an estimate" button (suggested 32px x 32px)', 'yith-woocommerce-wishlist' ),
				'id'        => 'yith_wcwl_ask_an_estimate_custom_icon',
				'default'   => '',
				'type'      => 'yith-field',
				'yith-type' => 'media',
			),

			'style_section_end'                      => array(
				'type' => 'sectionend',
				'id'   => 'yith_wcwl_style_section_settings',
			),
		),
	),
);
