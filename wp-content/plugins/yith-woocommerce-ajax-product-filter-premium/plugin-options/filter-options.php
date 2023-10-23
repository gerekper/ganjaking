<?php
/**
 * Filter options
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Options
 * @version 4.0.0
 */

$supported_taxonomies = YITH_WCAN_Query()->get_supported_taxonomies();
$taxonomy_options     = array();
$taxonomy_details     = array();

if ( ! empty( $supported_taxonomies ) ) {
	foreach ( $supported_taxonomies as $taxonomy_slug => $taxonomy_obj ) {
		$taxonomy_options[ $taxonomy_slug ] = $taxonomy_obj->label;
		$taxonomy_details[ $taxonomy_slug ] = array(
			'terms_count'     => wp_count_terms( $taxonomy_slug ),
			'is_attribute'    => 0 === strpos( $taxonomy_slug, 'pa_' ),
			'supports_images' => apply_filters( 'yith_wcan_taxonomy_supports_images', 'product_cat' === $taxonomy_slug, $taxonomy_slug ),
		);
	}
}

$supported_types = YITH_WCAN_Filter_Factory::get_supported_types();

return apply_filters(
	'yith_wcan_panel_filter_options',
	array_merge(
		array(
			'title' => array(
				'label' => _x( 'Filter name', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'  => 'text',
				'class' => 'filter-title heading-field',
				'desc'  => _x( 'Enter a name to identify this filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
			),
		),
		$supported_types && 1 < count( $supported_types ) ? array(
			'type' => array(
				'label'   => _x( 'Filter for', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'    => 'select',
				'class'   => 'wc-enhanced-select filter-type',
				'options' => YITH_WCAN_Filter_Factory::get_supported_types(),
				'desc'    => _x( 'Select the parameters you wish to filter for', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
			),
		) : array(),
		array(
			'taxonomy'        => array(
				'label'             => _x( 'Choose taxonomy', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'              => 'select',
				'class'             => 'wc-enhanced-select taxonomy',
				'options'           => $taxonomy_options,
				'desc'              => _x( 'Select which taxonomy to use for this filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'custom_attributes' => 'data-taxonomies="' . wc_esc_json( wp_json_encode( $taxonomy_details ) ) . '"',
			),

			'use_all_terms'   => array(
				'label' => _x( 'Auto-populate with all terms', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'  => 'onoff',
				'desc'  => _x(
					'Populate this filter with all existing terms of the selected taxonomy.
					<span class="future-terms-notice"><b>Notice:</b> if you enable this option, all terms created in the future will be added to this filter as well.</span>',
					'[Admin] Filter edit form',
					'yith-woocommerce-ajax-navigation'
				),
			),

			'term_ids'        => array(
				'label'    => _x( 'Choose terms', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'     => 'select-buttons',
				'multiple' => true,
				'class'    => 'wc-enhanced-select term-search',
				'options'  => array(),
				'desc'     => _x( 'Select which terms to use for filtering', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
			),

			'filter_design'   => array(
				'label'   => _x( 'Filter type', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'    => 'select',
				'class'   => 'wc-enhanced-select filter-design',
				'options' => YITH_WCAN_Filter_Factory::get_supported_designs(),
				'desc'    => _x( 'Select the filter type for this filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
			),

			'label_position'  => array(
				'label'   => _x( 'Labels', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'    => 'radio',
				'options' => array(
					'below' => _x( 'Show below', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'right' => _x( 'Show on the right', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'hide'  => _x( 'Hide', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),
				'desc'    => _x( 'Choose if and where to show the label inside your filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
			),

			'column_number'   => array(
				'label' => _x( 'Columns number', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'  => 'number',
				'min'   => 1,
				'step'  => 1,
				'max'   => 8,
				'desc'  => _x( 'Set the number of items per row you want to show for this design', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
			),

			'customize_terms' => array(
				'label' => _x( 'Customize terms', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'class' => 'customize-terms',
				'type'  => 'onoff',
				'desc'  => _x(
					'Configure your terms individually; system will otherwise use default labels/images for terms.
					<span class="wccl-notice"><b>Notice:</b> optionally,  you can configure labels/images/colors for your attributes using YITH WooCommerce Color and Label Variations options, in the term\'s edit page. Otherwise, just enable this option and use boxes below.</span>
					<span class="images-notice"><b>Notice:</b> optionally, you can configure the images in ‘edit page’ for the terms. Otherwise, just enable this option and use the boxes below</span>',
					'[Admin] Filter edit form',
					'yith-woocommerce-ajax-navigation'
				),
			),

			'terms_options'   => array(
				'label'  => _x( 'Terms options', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'   => 'custom',
				'action' => 'yith_wcan_terms_options',
			),

			'hierarchical'    => array(
				'label'   => _x( 'Show hierarchy', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'    => 'radio',
				'options' => array(
					'no'           => _x( 'No, show all terms in same level', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'parents_only' => _x( 'No, show only parent terms', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'open'         => _x( 'Yes', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),
				'desc'    => _x( 'Choose how to show terms hierarchy', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
			),

			'multiple'        => array(
				'label' => _x( 'Allow multiple selection', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'  => 'onoff',
				'desc'  => _x( 'Enable if the user can select multiple terms when filtering products', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
			),

			'relation'        => array(
				'label'   => _x( 'Multiselect relation', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				'type'    => 'radio',
				'options' => array(
					'and' => _x( 'AND - Results need to match all selected terms at the same time', '[Admin] Filter edit form; logical operator that affects query behaviour', 'yith-woocommerce-ajax-navigation' ),
					'or'  => _x( 'OR - Results need to match at least one of the selected terms', '[Admin] Filter edit form; logical operator that affects query behaviour', 'yith-woocommerce-ajax-navigation' ),
				),
				'desc'    => _x( 'Choose how multiple terms selection should behave', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
			),

		)
	)
);
