<?php
/**
 * Items options array
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

/**
 * APPLY_FILTERS: yith_wcmap_items_options_fields
 *
 * Filters the fields for the new items in the plugin panel (endpoints, groups, links).
 *
 * @param array $fields Fields.
 *
 * @return array
 */
return apply_filters(
	'yith_wcmap_items_options_fields',
	array(
		'endpoint' => array(
			'label'            => array(
				'type'  => 'text',
				'label' => __( 'Endpoint label', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'Enter a name to identify this endpoint', 'yith-woocommerce-customize-myaccount-page' ),
				'class' => 'required',
			),
			'slug'             => array(
				'type'    => 'text',
				'label'   => __( 'Endpoint slug', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Enter a slug for this endpoint', 'yith-woocommerce-customize-myaccount-page' ),
				'exclude' => array(
					'dashboard',
				),
				'class'   => 'required',
			),
			'icon_type'        => array(
				'type'    => 'radio',
				'label'   => __( 'Endpoint icon', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Choose if show or not an icon to identify this endpoint', 'yith-woocommerce-customize-myaccount-page' ),
				'options' => array(
					'empty'   => __( 'Don\'t show an icon', 'yith-woocommerce-customize-myaccount-page' ),
					'default' => __( 'Show a default icon', 'yith-woocommerce-customize-myaccount-page' ),
					'custom'  => __( 'Upload a custom icon', 'yith-woocommerce-customize-myaccount-page' ),
				),
				'default' => 'default',
			),
			'icon'             => array(
				'type'    => 'icon-select',
				'label'   => __( 'Choose icon', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Choose an icon to identify this endpoint', 'yith-woocommerce-customize-myaccount-page' ),
				'class'   => 'icon-select',
				'options' => array(),
				'deps'    => array(
					'ids'    => 'icon_type',
					'values' => 'default',
				),
			),
			'custom_icon'      => array(
				'type'  => 'media',
				'label' => __( 'Upload icon', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'Upload you custom icon to identify this endpoint', 'yith-woocommerce-customize-myaccount-page' ),
				'deps'  => array(
					'ids'    => 'icon_type',
					'values' => 'custom',
				),
			),
			'visibility'       => array(
				'type'    => 'radio',
				'label'   => __( 'Show endpoint to', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Choose whether to show this endpoint to all users or only to specific users', 'yith-woocommerce-customize-myaccount-page' ),
				'options' => array(
					'all'   => __( 'All users', 'yith-woocommerce-customize-myaccount-page' ),
					'roles' => __( 'Only users with a specific role', 'yith-woocommerce-customize-myaccount-page' ),
				),
				'default' => 'all',
			),
			'usr_roles'        => array(
				'type'     => 'select',
				'label'    => __( 'User roles', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'     => __( 'Restrict visibility to the following user role(s).', 'yith-woocommerce-customize-myaccount-page' ),
				'options'  => yith_wcmap_get_editable_roles(),
				'multiple' => true,
				'deps'     => array(
					'ids'    => 'visibility',
					'values' => 'roles',
				),
			),
			'content'          => array(
				'type'           => 'textarea-editor',
				'label'          => __( 'Content', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'           => __( 'Set the endpoint content. Use <code>%%customer_name%%</code> as placeholder for print the current customer name.', 'yith-woocommerce-customize-myaccount-page' ),
				'wpautop'        => false,
				'default_editor' => 'tinymce',
			),
			'content_position' => array(
				'type'    => 'radio',
				'label'   => __( 'Place content', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Choose to overwrite the default content or add before/after the default content', 'yith-woocommerce-customize-myaccount-page' ),
				'options' => array(
					'before'   => __( 'Before the default content', 'yith-woocommerce-customize-myaccount-page' ),
					'after'    => __( 'After the default content', 'yith-woocommerce-customize-myaccount-page' ),
					'override' => __( 'Overriding the default content', 'yith-woocommerce-customize-myaccount-page' ),
				),
				'default' => 'override',
			),
			'class'            => array(
				'type'  => 'text',
				'label' => __( 'CSS class', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'Add additional CSS classes to this endpoint container', 'yith-woocommerce-customize-myaccount-page' ),
			),
		),
		'group'    => array(
			'label'       => array(
				'type'  => 'text',
				'label' => __( 'Group label', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'Enter a name to identify this endpoints group', 'yith-woocommerce-customize-myaccount-page' ),
				'class' => 'required',
			),
			'open'        => array(
				'type'  => 'checkbox',
				'label' => __( 'Show open', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'Show the group open by default. (Please note: this option is valid only for "Sidebar" style)', 'yith-woocommerce-customize-myaccount-page' ),
			),
			'icon_type'   => array(
				'type'    => 'radio',
				'label'   => __( 'Group icon', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Choose if show or not an icon to identify this endpoints group', 'yith-woocommerce-customize-myaccount-page' ),
				'options' => array(
					'empty'   => __( 'Don\'t show an icon', 'yith-woocommerce-customize-myaccount-page' ),
					'default' => __( 'Show a default icon', 'yith-woocommerce-customize-myaccount-page' ),
					'custom'  => __( 'Upload a custom icon', 'yith-woocommerce-customize-myaccount-page' ),
				),
				'default' => 'default',
			),
			'icon'        => array(
				'type'    => 'icon-select',
				'label'   => __( 'Choose icon', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Choose an icon to identify this endpoints group', 'yith-woocommerce-customize-myaccount-page' ),
				'class'   => 'icon-select',
				'options' => array(),
				'deps'    => array(
					'ids'    => 'icon_type',
					'values' => 'default',
				),
			),
			'custom_icon' => array(
				'type'  => 'media',
				'label' => __( 'Upload icon', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'Upload you custom icon to identify this endpoints group', 'yith-woocommerce-customize-myaccount-page' ),
				'deps'  => array(
					'ids'    => 'icon_type',
					'values' => 'custom',
				),
			),
			'visibility'  => array(
				'type'    => 'radio',
				'label'   => __( 'Show group to', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Choose whether to show this group to all users or only to specific users', 'yith-woocommerce-customize-myaccount-page' ),
				'options' => array(
					'all'   => __( 'All users', 'yith-woocommerce-customize-myaccount-page' ),
					'roles' => __( 'Only users with a specific role', 'yith-woocommerce-customize-myaccount-page' ),
				),
				'default' => 'all',
			),
			'usr_roles'   => array(
				'type'     => 'select',
				'label'    => __( 'User roles', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'     => __( 'Restrict visibility to the following user role(s).', 'yith-woocommerce-customize-myaccount-page' ),
				'options'  => yith_wcmap_get_editable_roles(),
				'multiple' => true,
				'deps'     => array(
					'ids'    => 'visibility',
					'values' => 'roles',
				),
			),
			'class'       => array(
				'type'  => 'text',
				'label' => __( 'CSS class', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'Add additional CSS classes to this endpoint container', 'yith-woocommerce-customize-myaccount-page' ),
			),
		),
		'link'     => array(
			'url'          => array(
				'type'  => 'text',
				'label' => __( 'Link URL', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'The URL of the menu item', 'yith-woocommerce-customize-myaccount-page' ),
			),
			'label'        => array(
				'type'  => 'text',
				'label' => __( 'Link label', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'Enter a name to identify this link', 'yith-woocommerce-customize-myaccount-page' ),
				'class' => 'required',
			),
			'icon_type'    => array(
				'type'    => 'radio',
				'label'   => __( 'Link icon', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Choose if show or not an icon to identify this link', 'yith-woocommerce-customize-myaccount-page' ),
				'options' => array(
					'empty'   => __( 'Don\'t show an icon', 'yith-woocommerce-customize-myaccount-page' ),
					'default' => __( 'Show a default icon', 'yith-woocommerce-customize-myaccount-page' ),
					'custom'  => __( 'Upload a custom icon', 'yith-woocommerce-customize-myaccount-page' ),
				),
				'default' => 'default',
			),
			'icon'         => array(
				'type'    => 'icon-select',
				'label'   => __( 'Choose icon', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Choose an icon to identify this link', 'yith-woocommerce-customize-myaccount-page' ),
				'class'   => 'icon-select',
				'options' => array(),
				'deps'    => array(
					'ids'    => 'icon_type',
					'values' => 'default',
				),
			),
			'custom_icon'  => array(
				'type'  => 'media',
				'label' => __( 'Upload icon', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'Upload you custom icon to identify this link', 'yith-woocommerce-customize-myaccount-page' ),
				'deps'  => array(
					'ids'    => 'icon_type',
					'values' => 'custom',
				),
			),
			'visibility'   => array(
				'type'    => 'radio',
				'label'   => __( 'Show link to', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'    => __( 'Choose whether to show this link to all users or only to specific users', 'yith-woocommerce-customize-myaccount-page' ),
				'options' => array(
					'all'   => __( 'All users', 'yith-woocommerce-customize-myaccount-page' ),
					'roles' => __( 'Only users with a specific role', 'yith-woocommerce-customize-myaccount-page' ),
				),
				'default' => 'all',
			),
			'usr_roles'    => array(
				'type'     => 'select',
				'label'    => __( 'User roles', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'     => __( 'Restrict visibility to the following user role(s).', 'yith-woocommerce-customize-myaccount-page' ),
				'options'  => yith_wcmap_get_editable_roles(),
				'multiple' => true,
				'deps'     => array(
					'ids'    => 'visibility',
					'values' => 'roles',
				),
			),
			'class'        => array(
				'type'  => 'text',
				'label' => __( 'CSS class', 'yith-woocommerce-customize-myaccount-page' ),
				'desc'  => __( 'Add additional CSS classes to this link container', 'yith-woocommerce-customize-myaccount-page' ),
			),
			'target_blank' => array(
				'type'  => 'checkbox',
				'label' => __( 'Open link in a new tab?', 'yith-woocommerce-customize-myaccount-page' ),
			),
		),
	)
);
