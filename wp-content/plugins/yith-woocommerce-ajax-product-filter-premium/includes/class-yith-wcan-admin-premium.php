<?php
/**
 * Admin class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 4.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * This class manage all the admin features.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Admin_Premium extends YITH_WCAN_Admin_Extended {

		/**
		 * Construct
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			parent::__construct();

			// admin panel.
			add_action( 'yith_wcan_price_ranges', array( $this, 'filter_ranges_field' ), 10, 1 );

			// Add premium options.
			add_filter( 'yith_wcan_panel_customization_options', array( $this, 'add_customization_options' ) );
			add_filter( 'yith_wcan_panel_preset_options', array( $this, 'add_preset_options' ) );
			add_filter( 'yith_wcan_panel_filter_options', array( $this, 'add_filter_options' ) );
			add_filter( 'yith_wcan_panel_seo_options', array( $this, 'add_seo_options' ) );
			add_filter( 'yith_wcan_panel_legacy_options', array( $this, 'add_legacy_options' ) );

			// Add your store tools.
			add_filter( 'yith_wcan_panel_args', array( $this, 'filter_panel_args' ) );
		}

		/* === PANEL METHODS === */

		/**
		 * Add premium plugin options
		 *
		 * @param array $settings List of filter options.
		 * @return array Filtered list of filter options.
		 */
		public function add_general_options( $settings ) {
			$settings = parent::add_general_options( $settings );
			$options  = $settings['general'];

			$additional_options_batch_1 = array(
				'show_clear_filter' => array(
					'name'      => _x( 'Show "Clear" above each filter', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enable to show the "Clear" link above each filter of the preset', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_show_clear_filter',
					'type'      => 'yith-field',
					'default'   => 'no',
					'yith-type' => 'onoff',
				),

			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_1, 'hide_out_of_stock' );

			$additional_options_batch_2 = array(
				'modal_on_mobile' => array(
					'name'      => _x( 'Show as modal on mobile', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enable this option if you want to show filter section as a modal on mobile devices.<small>The modal opener will appear before products. When using WooCommerce\'s Gutenberg product blocks, this may not work as expected. If this is the case, you can place the Modal opener button anywhere in the page using <code>[yith_wcan_mobile_modal_opener]</code> shortcode or <code>YITH Mobile Filters Modal Opener</code> block</small>', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_modal_on_mobile',
					'default'   => 'no',
					'type'      => 'yith-field',
					'yith-type' => 'onoff',
				),
			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_2, 'show_clear_filter' );

			$additional_options_batch_3 = array(
				'horizontal_instant_filter' => array(
					'name'      => _x( 'Filter mode (horizontal)', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Choose to apply filters immediately after picking one in the popup, or allow multiple selection with a Save button', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_instant_horizontal_filter',
					'type'      => 'yith-field',
					'yith-type' => 'radio',
					'default'   => 'no',
					'options'   => array(
						'no'  => _x( 'Show "Save" button', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
						'yes' => _x( 'Filter immediately', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					),
				),
			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_3, 'instant_filter' );

			$additional_options_batch_4 = array(
				'active_filters_section_start' => array(
					'name' => _x( 'Active Filters options', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'yith_wcan_active_filters_settings',
				),

				'show_active_filters'          => array(
					'name'      => _x( 'Show active filters as labels', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enable to show the active filters as labels. Labels show the current filters selection, and can be used to remove any active filter.', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_show_active_labels',
					'type'      => 'yith-field',
					'default'   => 'no',
					'yith-type' => 'onoff',
				),

				'active_labels_position'       => array(
					'name'      => _x( 'Active filters labels position', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Choose the default position for Active Filters labels', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_active_labels_position',
					'type'      => 'yith-field',
					'yith-type' => 'radio',
					'default'   => 'before_filters',
					'options'   => array(
						'before_filters'  => _x( 'Before filters', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
						'after_filters'   => _x( 'After filters', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
						'before_products' => _x( 'Above products list<small>When using WooCommerce\'s Gutenberg product blocks, this may not work as expected; in these cases you can place Reset Button anywhere in the page using <code>[yith_wcan_active_filters_labels]</code> shortcode or <code>YITH Active Filters Labels</code> block</small>', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					),
					'deps'      => array(
						'ids'    => 'yith_wcan_show_active_labels',
						'values' => 'yes',
					),
				),

				'active_labels_with_titles'    => array(
					'name'      => _x( 'Show titles for active filter labels', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enable to show labels subdivided by filter, and to show a title for each group', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_active_labels_with_titles',
					'type'      => 'yith-field',
					'default'   => 'yes',
					'yith-type' => 'onoff',
					'deps'      => array(
						'ids'    => 'yith_wcan_show_active_labels',
						'values' => 'yes',
					),
				),

				'active_filters_section_end'   => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcan_active_filters_settings_end',
				),
			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_4, 'general_section_end' );

			// add premium options to existing settings.
			$options['reset_button_position']['options'] = array_merge(
				$options['reset_button_position']['options'],
				array(
					'after_active_labels' => _x( 'Inline with active filters', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
				)
			);

			$settings['general'] = $options;

			return $settings;
		}

		/**
		 * Add premium plugin options
		 *
		 * @param array $settings List of filter options.
		 * @return array Filtered list of filter options.
		 */
		public function add_customization_options( $settings ) {
			$options = $settings['customization'];

			$additional_options_batch_1 = array(
				'filters_title' => array(
					'name'      => _x( 'Filters area title', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enter a title to identify the “AJAX filter Preset” section', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_filters_title',
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => '',
				),
			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_1, 'global_section_start' );

			$additional_options_batch_2 = array(
				'filters_style' => array(
					'name'      => _x( 'Options style', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Choose which preset of style options you\'d like to apply to your filters', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_filters_style',
					'type'      => 'yith-field',
					'default'   => 'default',
					'yith-type' => 'radio',
					'options'   => array(
						'default' => _x( 'Theme style', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
						'custom'  => _x( 'Custom style', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					),
				),
			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_2, 'filters_colors' );

			$default_accent_color       = apply_filters( 'yith_wcan_default_accent_color', '#A7144C' );
			$additional_options_batch_3 = array(

				'color_swatches_section_start' => array(
					'name' => _x( 'Color swatches', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'yith_wcan_color_swatches_settings',
				),

				'color_swatches_style'         => array(
					'name'      => _x( 'Color swatch style', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Choose the style for color thumbnails', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_color_swatches_style',
					'type'      => 'yith-field',
					'default'   => 'round',
					'yith-type' => 'radio',
					'options'   => array(
						'round'  => _x( 'Rounded', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
						'square' => _x( 'Square', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					),
				),

				'color_swatches_size'          => array(
					'name'      => _x( 'Color swatch size', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'The size for color thumbnails', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_color_swatches_size',
					'type'      => 'yith-field',
					'default'   => '30',
					'yith-type' => 'number',
					'min'       => '5',
					'max'       => '200',
				),

				'color_swatches_section_end'   => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcan_color_swatches_settings',
				),
			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_3, 'global_section_end' );

			$additional_options_batch_4 = array(
				'labels_text_section_start' => array(
					'name' => _x( 'Labels & text', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'yith_wcan_labels_text_settings',
				),

				'labels_style'              => array(
					'name'         => _x( 'Labels style color', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'           => 'yith_wcan_labels_style',
					'type'         => 'yith-field',
					'yith-type'    => 'multi-colorpicker',
					'colorpickers' => array(
						array(
							array(
								'name'    => _x( 'Background', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
								'id'      => 'background',
								'default' => '#FFFFFF',
							),
							array(
								'name'    => _x( 'Background Hover', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
								'id'      => 'background_hover',
								'default' => $default_accent_color,
							),
							array(
								'name'    => _x( 'Background Active', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
								'id'      => 'background_active',
								'default' => $default_accent_color,
							),
						),
						array(
							array(
								'name'    => _x( 'Text', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
								'id'      => 'text',
								'default' => '#434343',
							),
							array(
								'name'    => _x( 'Text Hover', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
								'id'      => 'text_hover',
								'default' => '#FFFFFF',
							),
							array(
								'name'    => _x( 'Text Active', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
								'id'      => 'text_active',
								'default' => '#FFFFFF',
							),
						),
					),
				),

				'anchors_style'             => array(
					'name'         => _x( 'Textual terms color', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'           => 'yith_wcan_anchors_style',
					'type'         => 'yith-field',
					'yith-type'    => 'multi-colorpicker',
					'colorpickers' => array(
						array(
							'name'    => _x( 'Text', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
							'id'      => 'text',
							'default' => '#434343',
						),
						array(
							'name'    => _x( 'Text hover', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
							'id'      => 'text_hover',
							'default' => $default_accent_color,
						),
						array(
							'name'    => _x( 'Text active', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
							'id'      => 'text_active',
							'default' => $default_accent_color,
						),
					),
				),

				'labels_text_section_end'   => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcan_labels_text_settings',
				),
			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_4, 'color_swatches_section_end' );

			$additional_options_batch_5 = array(
				'ajax_loader_section_start' => array(
					'name' => _x( 'Ajax loader', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'yith_wcan_ajax_loader__settings',
				),

				'ajax_loader_style'         => array(
					'name'      => _x( 'AJAX loader', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Choose the style for AJAX loader icon', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_ajax_loader_style',
					'type'      => 'yith-field',
					'default'   => 'default',
					'yith-type' => 'radio',
					'options'   => array(
						'default' => _x( 'Use default loader', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
						'custom'  => _x( 'Upload custom loader', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					),
				),

				'ajax_loader_custom_icon'   => array(
					'name'      => _x( 'Custom AJAX loader', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Upload an icon you\'d like to use as AJAX Loader (suggested 50px x 50px)', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_ajax_loader_custom_icon',
					'default'   => '',
					'type'      => 'yith-field',
					'yith-type' => 'media',
					'deps'      => array(
						'id'    => 'yith_wcan_ajax_loader_style',
						'value' => 'custom',
					),
				),

				'ajax_loader_section_end'   => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcan_ajax_loader_settings',
				),
			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_5, 'labels_text_section_end' );

			$settings['customization'] = $options;

			return $settings;
		}

		/**
		 * Add premium preset options
		 *
		 * @param array $settings List of preset options.
		 * @return array Filtered list of preset options.
		 */
		public function add_preset_options( $settings ) {
			$settings['preset_layout'] = array(
				'label'   => _x( 'Preset layout', '[Admin] Label in new preset page', 'yith-woocommerce-ajax-navigation' ),
				'type'    => 'radio',
				'options' => YITH_WCAN_Preset_Factory::get_supported_layouts(),
				'desc'    => _x( 'Choose the layout for this filter preset', '[Admin] Label in new preset page', 'yith-woocommerce-ajax-navigation' ),
			);

			return $settings;
		}

		/**
		 * Add premium filter options
		 *
		 * @param array $settings List of filter options.
		 * @return array Filtered list of filter options.
		 */
		public function add_filter_options( $settings ) {
			// add premium settings.
			$additional_options_batch_2 = array(
				'show_search'                  => array(
					'label' => _x( 'Show search field', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'  => 'onoff',
					'desc'  => _x( 'Enable if you want to show search field inside dropdown', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'price_slider_design'          => array(
					'label'   => _x( 'Price slider style', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'    => 'radio',
					'options' => array(
						'slider' => _x( 'Show slider', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
						'fields' => _x( 'Show "From" and "To" fields', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
						'both'   => _x( 'Show both', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					),
					'desc'    => _x( 'Choose the design for your price slider', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'price_slider_adaptive_limits' => array(
					'label' => _x( 'Adaptive limits', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'  => 'onoff',
					'desc'  => _x( 'Automatically calculate min/max values for the slider, depending on current selection of products.', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'price_slider_min'             => array(
					'label'             => _x( 'Slider min value', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'              => 'number',
					'min'               => 0,
					'step'              => 0.01,
					'custom_attributes' => 'data-currency="' . esc_attr( get_woocommerce_currency_symbol() ) . '"',
					'desc'              => _x( 'Set the minimum value for the price slider', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'price_slider_max'             => array(
					'label'             => _x( 'Slider max value', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'              => 'number',
					'min'               => 0,
					'step'              => 0.01,
					'custom_attributes' => 'data-currency="' . esc_attr( get_woocommerce_currency_symbol() ) . '"',
					'desc'              => _x( 'Set the maximum value for the price slider', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'price_slider_step'            => array(
					'label'             => _x( 'Slider step', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'              => 'number',
					'min'               => 0.01,
					'step'              => 0.01,
					'custom_attributes' => 'data-currency="' . esc_attr( get_woocommerce_currency_symbol() ) . '"',
					'desc'              => _x( 'Set the value for each increment of the price slider', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'order_options'                => array(
					'label'    => _x( 'Order options', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'     => 'select-buttons',
					'multiple' => true,
					'class'    => 'wc-enhanced-select',
					'options'  => YITH_WCAN_Filter_Factory::get_supported_orders(),
					'desc'     => _x( 'Select sorting options to show', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'price_ranges'                 => array(
					'label'  => _x( 'Customize price ranges', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'   => 'custom',
					'action' => 'yith_wcan_price_ranges',
				),

				'show_stock_filter'            => array(
					'label' => _x( 'Show stock filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'  => 'onoff',
					'desc'  => _x( 'Enable if you want to show "In Stock" filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'show_sale_filter'             => array(
					'label' => _x( 'Show sale filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'  => 'onoff',
					'desc'  => _x( 'Enable if you want to show "On Sale" filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'show_featured_filter'         => array(
					'label' => _x( 'Show featured filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'  => 'onoff',
					'desc'  => _x( 'Enable if you want to show "Featured" filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'show_toggle'                  => array(
					'label' => _x( 'Show as toggle', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'class' => 'show-toggle',
					'type'  => 'onoff',
					'desc'  => _x( 'Enable if you want to show this filter as a toggle', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'toggle_style'                 => array(
					'label'   => _x( 'Toggle style', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'class'   => 'toggle-style',
					'type'    => 'radio',
					'options' => array(
						'closed' => _x( 'Closed by default', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
						'opened' => _x( 'Opened by default', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					),
					'desc'    => _x( 'Choose if the toggle has to be closed or opened by default', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'order_by'                     => array(
					'label'   => _x( 'Order by', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'    => 'select',
					'class'   => 'wc-enhanced-select order-by',
					'options' => array(
						'name'       => _x( 'Name', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
						'slug'       => _x( 'Slug', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
						'count'      => _x( 'Term count', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
						'term_order' => _x( 'Term order', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
						'include'    => _x( 'Drag & drop', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					),
					'desc'    => _x( 'Select the default order for terms of this filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'order'                        => array(
					'label'   => _x( 'Order type', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'    => 'select',
					'class'   => 'wc-enhanced-select',
					'options' => array(
						'asc'  => _x( 'ASC', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
						'desc' => _x( 'DESC', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					),
					'desc'    => _x( 'Select the default order for terms of this filter', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),

				'show_count'                   => array(
					'label' => _x( 'Show count of items', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'  => 'onoff',
					'desc'  => _x( 'Enable if you want to show how many items are available for each term', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),
			);
			$settings = yith_wcan_merge_in_array( $settings, $additional_options_batch_2, 'terms_options' );

			$additional_options_batch_3 = array(
				'adoptive' => array(
					'label'   => _x( 'Adoptive filtering', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'type'    => 'radio',
					'options' => array(
						'hide' => _x( 'Terms will be hidden', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
						'or'   => _x( 'Terms will be visible, but not clickable', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					),
					'desc'    => _x( 'Decide how to manage filter options that show no results when applying filters. Choose to hide them or make them visible (this will show them in lighter grey and not clickable)', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),
			);
			$settings                   = yith_wcan_merge_in_array( $settings, $additional_options_batch_3, 'relation' );

			// add premium options to existing settings.
			$settings['hierarchical']['options'] = yith_wcan_merge_in_array(
				$settings['hierarchical']['options'],
				array(
					'collapsed' => _x( 'Yes, with terms collapsed', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'expanded'  => _x( 'Yes, with terms expanded', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),
				'parents_only'
			);

			$settings['hierarchical']['options']['open'] = _x( 'Yes, without toggles', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' );

			return $settings;
		}

		/**
		 * Add premium plugin options to Legacy tab
		 *
		 * @param array $settings List of legacy options.
		 * @return array Filtered list of legacy options.
		 */
		public function add_legacy_options( $settings ) {
			$options = $settings['legacy'];

			$additional_options_batch_1 = array(
				'scroll_to_top'         => array(
					'name'      => _x( 'Scroll to top after filtering', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Choose whether you want to enable the "Scroll to top" option on Desktop, Mobile, or on both of them', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_scroll_top_mode]',
					'type'      => 'yith-field',
					'default'   => 'menu_order',
					'yith-type' => 'radio',
					'options'   => array(
						'disabled' => _x( 'Disabled', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
						'mobile'   => _x( 'Mobile', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
						'desktop'  => _x( 'Desktop', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
						'both'     => _x( 'Mobile and Desktop', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					),
				),

				'widget_title_selector' => array(
					'name'      => _x( 'Widget title selector', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enter here the CSS selector (class or ID) of the widget title', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_ajax_widget_title_class]',
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => 'h3.widget-title',
				),

				'widget_container'      => array(
					'name'      => _x( 'Widget container', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enter here the CSS selector (class or ID) of the widget container', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_ajax_widget_wrapper_class]',
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => '.widget',
				),

				'filter_style'          => array(
					'name'      => _x( 'Filter style', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Choose the style of the filter inside widgets', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_ajax_shop_filter_style]',
					'type'      => 'yith-field',
					'default'   => 'standard',
					'yith-type' => 'radio',
					'options'   => array(
						'standard'   => _x( '"x" icon before active filters', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
						'checkboxes' => _x( 'Checkboxes', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					),
				),
			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_1, 'order_by' );

			$additional_options_batch_2 = array(
				'legacy_general_start'     => array(
					'name' => _x( 'General options', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'yith_wcan_legacy_general_settings',
				),

				'ajax_loader'              => array(
					'name'      => _x( 'Ajax Loader', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Choose loading icon you want to use for your widget filters', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_ajax_loader]',
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => YITH_WCAN_URL . 'assets/images/ajax-loader.gif',
				),

				'ajax_price_filter'        => array(
					'name'      => _x( 'Filter by price using AJAX', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Filter products via AJAX when using WooCommerce price filter widget', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_enable_ajax_price_filter]',
					'type'      => 'yith-field',
					'default'   => 'yes',
					'yith-type' => 'onoff',
				),

				'price_slider'             => array(
					'name'      => _x( 'Use slider for price filtering', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Transform default WooCommerce price filter into a slider', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_enable_ajax_price_filter]',
					'type'      => 'yith-field',
					'default'   => 'no',
					'yith-type' => 'onoff',
				),

				'ajax_price_slider'        => array(
					'name'      => _x( 'Filter by price using AJAX slider', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Filter products via AJAX when using WooCommerce price filter widget', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_enable_ajax_price_filter_slider]',
					'type'      => 'yith-field',
					'default'   => 'yes',
					'yith-type' => 'onoff',
				),

				'price_dropdown'           => array(
					'name'      => _x( 'Add toggle for price filter widget', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Show price filtering widget as a toggle', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_enable_dropdown_price_filter]',
					'type'      => 'yith-field',
					'default'   => 'no',
					'yith-type' => 'onoff',
				),

				'price_dropdown_style'     => array(
					'name'      => _x( 'Chose how to show price filter toggle', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Choose whether to show price filtering widget as an open or closed toggle', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_dropdown_style]',
					'type'      => 'yith-field',
					'default'   => 'open',
					'yith-type' => 'radio',
					'options'   => array(
						'open'  => _x( 'Opened', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
						'close' => _x( 'Closed', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					),
				),

				'ajax_shop_pagination'     => array(
					'name'      => _x( 'Enable ajax pagination', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Make shop pagination anchors load new page via ajax', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_enable_ajax_shop_pagination]',
					'type'      => 'yith-field',
					'default'   => 'no',
					'yith-type' => 'onoff',
				),

				'shop_pagination_selector' => array(
					'name'      => _x( 'Shop pagination selector', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enter here the CSS selector (class or ID) of the shop pagination', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_ajax_shop_pagination_anchor_class]',
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => 'a.page-numbers',
				),

				'show_current_categories'  => array(
					'name'      => _x( 'Show current categories', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enable if you want to show link to current category in the filter, when visiting category page', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_show_current_categories_link]',
					'type'      => 'yith-field',
					'default'   => 'no',
					'yith-type' => 'onoff',
				),

				'show_all_categories'      => array(
					'name'      => _x( 'Show "All categories" anchor', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enable if you want to show a link to retrieve products from all categories, after a category filter is applied', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_enable_see_all_categories_link]',
					'type'      => 'yith-field',
					'default'   => 'no',
					'yith-type' => 'onoff',
				),

				'all_categories_label'     => array(
					'name'      => _x( '"All categories" anchor label', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enter here the text you want to use for "All categories" anchor', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_enable_see_all_categories_link_text]',
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => _x( 'See all categories', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				),

				'show_all_tags'            => array(
					'name'      => _x( 'Show "All tags" anchor', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enable if you want to show a link to retrieve products from all tags, after a category filter is applied', '[ADMIN] General settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_enable_see_all_tags_link]',
					'type'      => 'yith-field',
					'default'   => 'no',
					'yith-type' => 'onoff',
				),

				'all_tags_label'           => array(
					'name'      => _x( '"All tags" anchor label', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Enter here the text you want to use for "All tags" anchor', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_enable_see_all_tags_link_text]',
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => _x( 'See all tags', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
				),

				'hierarchical_tags'        => array(
					'name'      => _x( 'Hierarchical tags', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Make product tag taxonomy hierarchical', '[ADMIN] Legacy settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yit_wcan_options[yith_wcan_enable_hierarchical_tags_link]',
					'type'      => 'yith-field',
					'default'   => 'no',
					'yith-type' => 'onoff',
				),

				'legacy_general_end'       => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcan_legacy_general_settings',
				),
			);

			$options = yith_wcan_merge_in_array( $options, $additional_options_batch_2, 'legacy_frontend_end' );

			$settings['legacy'] = $options;

			return $settings;
		}

		/**
		 * Filter admin panel args.
		 *
		 * @param array $args Panel args.
		 *
		 * @return mixed
		 */
		public function filter_panel_args( $args ) {
			$args['your_store_tools'] = array(
				'items' => array(
					'wishlist'               => array(
						'name'           => 'YITH WooCommerce Wishlist',
						'icon_url'       => YITH_WCAN_ASSETS . '/images/plugins/wishlist.svg',
						'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-wishlist/',
						'description'    => _x(
							'Allow your customers to create lists of products they want and share them with family and friends.',
							'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Wishlist',
							'yith-woocommerce-ajax-navigation'
						),
						'is_active'      => defined( 'YITH_WCWL_PREMIUM' ),
						'is_recommended' => true,
					),
					'gift-cards'             => array(
						'name'           => 'YITH WooCommerce Gift Cards',
						'icon_url'       => YITH_WCAN_ASSETS . '/images/plugins/gift-cards.svg',
						'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-gift-cards/',
						'description'    => _x(
							'Sell gift cards in your shop to increase your earnings and attract new customers.',
							'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Gift Cards',
							'yith-woocommerce-ajax-navigation'
						),
						'is_active'      => defined( 'YITH_YWGC_PREMIUM' ),
						'is_recommended' => true,
					),
					'request-a-quote'        => array(
						'name'           => 'YITH WooCommerce Request a Quote',
						'icon_url'       => YITH_WCAN_ASSETS . '/images/plugins/request-a-quote.svg',
						'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-request-a-quote/',
						'description'    => _x(
							'Hide prices and/or the "Add to cart" button and let your customers request a custom quote for every product.',
							'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Request a Quote',
							'yith-woocommerce-ajax-navigation'
						),
						'is_active'      => defined( 'YITH_YWRAQ_PREMIUM' ),
						'is_recommended' => false,
					),
					'points-rewards'         => array(
						'name'           => 'YITH WooCommerce Points and Rewards',
						'icon_url'       => YITH_WCAN_ASSETS . '/images/plugins/points-rewards.svg',
						'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-points-and-rewards/',
						'description'    => _x(
							'Loyalize your customers with an effective points-based loyalty program and instant rewards.',
							'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Points and Rewards',
							'yith-woocommerce-ajax-navigation'
						),
						'is_active'      => defined( 'YITH_YWPAR_PREMIUM' ),
						'is_recommended' => false,
					),
					'product-addons'         => array(
						'name'           => 'YITH WooCommerce Product Add-Ons & Extra Options',
						'icon_url'       => YITH_WCAN_ASSETS . '/images/plugins/product-add-ons.svg',
						'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-product-add-ons/',
						'description'    => _x(
							'Add paid or free advanced options to your product pages using fields like radio buttons, checkboxes, drop-downs, custom text inputs, and more.',
							'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Product Add-Ons',
							'yith-woocommerce-ajax-navigation'
						),
						'is_active'      => defined( 'YITH_WAPO_PREMIUM' ),
						'is_recommended' => false,
					),
					'dynamic-pricing'        => array(
						'name'           => 'YITH WooCommerce Dynamic Pricing and Discounts',
						'icon_url'       => YITH_WCAN_ASSETS . '/images/plugins/dynamic-pricing-and-discounts.svg',
						'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-dynamic-pricing-and-discounts/',
						'description'    => _x(
							'Increase conversions through dynamic discounts and price rules, and build powerful and targeted offers.',
							'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Dynamic Pricing and Discounts',
							'yith-woocommerce-ajax-navigation'
						),
						'is_active'      => defined( 'YITH_YWDPD_PREMIUM' ),
						'is_recommended' => false,
					),
					'customize-my-account'   => array(
						'name'           => 'YITH WooCommerce Customize My Account Page',
						'icon_url'       => YITH_WCAN_ASSETS . '/images/plugins/customize-myaccount-page.svg',
						'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-customize-my-account-page/',
						'description'    => _x(
							'Customize the My Account page of your customers by creating custom sections with promotions and ad-hoc content based on your needs.',
							'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Customize My Account',
							'yith-woocommerce-ajax-navigation'
						),
						'is_active'      => defined( 'YITH_WCMAP_PREMIUM' ),
						'is_recommended' => false,
					),
					'recover-abandoned-cart' => array(
						'name'           => 'YITH WooCommerce Recover Abandoned Cart',
						'icon_url'       => YITH_WCAN_ASSETS . '/images/plugins/recover-abandoned-cart.svg',
						'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-recover-abandoned-cart/',
						'description'    => _x(
							'Contact users who have added products to the cart without completing the order and try to recover lost sales.',
							'[YOUR STORE TOOLS TAB] Description for plugin Recover Abandoned Cart',
							'yith-woocommerce-ajax-navigation'
						),
						'is_active'      => defined( 'YITH_YWRAC_PREMIUM' ),
						'is_recommended' => false,
					),
				),
			);
			return $args;
		}

		/**
		 * Prints single item of "Term edit" template
		 *
		 * @param int    $id Current row id.
		 * @param int    $term_id Current term id.
		 * @param string $term_name Current term name.
		 * @param string $term_options Options for current term (it may include label, tooltip, colors, and image).
		 *
		 * @return void
		 */
		public function filter_term_field( $id, $term_id, $term_name, $term_options = array() ) {
			// just include template, and provide passed terms.
			include YITH_WCAN_DIR . 'templates/admin/preset-filter-term-advanced.php';
		}

		/**
		 * Prints "Price Ranges edit" template
		 *
		 * @param array $field Array of options for current template.
		 *
		 * @return void
		 */
		public function filter_ranges_field( $field ) {
			$filter_id = isset( $field['index'] ) ? $field['index'] : 0;
			$ranges    = isset( $field['value'] ) ? $field['value'] : array();

			include YITH_WCAN_DIR . 'templates/admin/preset-filter-ranges.php';
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @param array $tabs Array of available tabs.
		 *
		 * @return   array Filtered array of tabs
		 * @since    1.0
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function settings_tabs( $tabs ) {
			$tabs = parent::settings_tabs( $tabs );

			if ( isset( $tabs['premium'] ) ) {
				unset( $tabs['premium'] );
			}

			return $tabs;
		}

		/* === PLUGIN META === */

		/**
		 * Adds action links to plugin row in plugins.php admin page
		 *
		 * @param array  $new_row_meta_args Array of data to filter.
		 * @param array  $plugin_meta       Array of plugin meta.
		 * @param string $plugin_file       Path to init file.
		 * @param array  $plugin_data       Array of plugin data.
		 * @param string $status            Not used.
		 * @param string $init_file         Constant containing plugin int path.
		 *
		 * @return   array
		 * @since    1.0
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCAN_INIT' ) {
			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

	}
}
