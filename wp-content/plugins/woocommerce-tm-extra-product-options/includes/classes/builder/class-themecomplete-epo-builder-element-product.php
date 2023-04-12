<?php
/**
 * Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_PRODUCT extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

	/**
	 * Class Constructor
	 *
	 * @param string $name The element name.
	 * @since 6.0
	 */
	public function __construct( $name = '' ) {
		$this->element_name     = $name;
		$this->is_addon         = false;
		$this->namespace        = $this->elements_namespace;
		$this->name             = esc_html__( 'Product', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfa-cube';
		$this->is_post          = 'post';
		$this->type             = 'single';
		$this->post_name_prefix = 'product';
		$this->fee_type         = '';
		$this->tags             = 'price content product';
		$this->show_on_backend  = true;
	}

	/**
	 * Fetch product categories
	 * for use in a select box
	 *
	 * @since  5.0
	 * @access public
	 */
	public function fetch_product_categories_array() {
		$list               = [];
		$product_categories = (array) get_terms( 'product_cat', [ 'get' => 'all' ] );

		foreach ( $product_categories as $product_category ) {
			$list[] = [
				'text'  => $product_category->name,
				'value' => $product_category->term_id,
			];
		}

		return $list;
	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.0
	 */
	public function set_properties() {
		$this->properties = $this->add_element(
			$this->element_name,
			[
				'_tabs' =>
					[
						'general_options'  =>
							[
								'enabled',
								'required',
								'hide_amount',

								[
									'id'               => 'product_mode',
									'wpmldisable'      => 1,
									'default'          => 'products',
									'message0x0_class' => 'tm-epo-switch-wrapper',
									'type'             => 'radio',
									'tags'             => [
										'class' => 'product-mode',
										'id'    => 'builder_product_mode',
										'name'  => 'tm_meta[tmfbuilder][product_mode][]',
									],
									'options'          => [
										[
											'text'  => esc_html__( 'Products', 'woocommerce-tm-extra-product-options' ),
											'value' => 'products',
										],
										[
											'text'  => esc_html__( 'Single Product', 'woocommerce-tm-extra-product-options' ),
											'value' => 'product',
										],
										[
											'text'  => esc_html__( 'Categories', 'woocommerce-tm-extra-product-options' ),
											'value' => 'categories',
										],
									],
									'label'            => esc_html__( 'Select mode', 'woocommerce-tm-extra-product-options' ),
									'desc'             => esc_html__( 'Whether to include specific products or categories.', 'woocommerce-tm-extra-product-options' ),
								],

								[
									'id'          => 'product_categoryids',
									'wpmldisable' => 1,
									'default'     => '',
									'type'        => 'select',
									'multiple'    => 'multiple',
									'fill'        => 'category',
									'tags'        => [
										'data-placeholder' => esc_attr__( 'Search for a category ...', 'woocommerce-tm-extra-product-options' ),
										'data-action'      => 'woocommerce_json_search_categories',
										'class'            => 'wc-category-search product-categories-selector',
										'id'               => 'builder_product_categoryids',
										'name'             => 'tm_meta[tmfbuilder][product_categoryids][]',
									],
									'options'     => $this->fetch_product_categories_array(),
									'label'       => esc_html__( 'Select categories', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Add categories to include all their associated products.', 'woocommerce-tm-extra-product-options' ),
									'required'    => [
										'.product-mode' => [
											'operator' => 'is',
											'value'    => 'categories',
										],
									],
								],

								[
									'id'          => 'product_productids',
									'wpmldisable' => 1,
									'default'     => '',
									'type'        => 'select',
									'multiple'    => 'multiple',
									'fill'        => 'product',
									'tags'        => [
										'data-placeholder' => esc_attr__( 'Search for a product ...', 'woocommerce-tm-extra-product-options' ),
										'data-action'      => 'woocommerce_json_search_products_and_variations',
										'data-sortable'    => 'true',
										'class'            => 'wc-product-search product-products-selector',
										'id'               => 'builder_product_productids',
										'name'             => 'tm_meta[tmfbuilder][product_productids][]',
									],
									'options'     => [],
									'label'       => esc_html__( 'Select products', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Add the products individually.', 'woocommerce-tm-extra-product-options' ),
									'required'    => [
										'.product-mode' => [
											'operator' => 'is',
											'value'    => [ 'products', 'product' ],
										],
									],
								],

								[
									'id'          => 'product_orderby',
									'wpmldisable' => 1,
									'default'     => 'ID',
									'type'        => 'select',
									'tags'        => [
										'data-placeholder' => esc_attr__( 'Choose a value', 'woocommerce-tm-extra-product-options' ),
										'class'            => 'fullwidth',
										'id'               => 'builder_product_orderby',
										'name'             => 'tm_meta[tmfbuilder][product_orderby][]',
									],
									'options'     => [
										[
											'text'  => esc_html__( 'Default', 'woocommerce-tm-extra-product-options' ),
											'value' => 'none',
										],
										[
											'text'  => esc_html__( 'Base price', 'woocommerce-tm-extra-product-options' ),
											'value' => 'baseprice',
										],
										[
											'text'  => esc_html__( 'ID', 'woocommerce-tm-extra-product-options' ),
											'value' => 'ID',
										],
										[
											'text'  => esc_html__( 'Title', 'woocommerce-tm-extra-product-options' ),
											'value' => 'title',
										],
										[
											'text'  => esc_html__( 'Date', 'woocommerce-tm-extra-product-options' ),
											'value' => 'date',
										],
										[
											'text'  => esc_html__( 'Name', 'woocommerce-tm-extra-product-options' ),
											'value' => 'name',
										],
										[
											'text'  => esc_html__( 'Menu Order', 'woocommerce-tm-extra-product-options' ),
											'value' => 'menu_order',
										],
										[
											'text'  => esc_html__( 'Random', 'woocommerce-tm-extra-product-options' ),
											'value' => 'rand',
										],
									],
									'label'       => esc_html__( 'Order by', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Select the parameter which the products will be sorted by.', 'woocommerce-tm-extra-product-options' ),
									'required'    => [
										'.product-mode' => [
											'operator' => 'isnot',
											'value'    => 'product',
										],
									],
								],

								[
									'id'               => 'product_order',
									'wpmldisable'      => 1,
									'default'          => 'asc',
									'message0x0_class' => 'tm-epo-switch-wrapper',
									'type'             => 'radio',
									'tags'             => [
										'class' => 'product-order',
										'id'    => 'builder_product_order',
										'name'  => 'tm_meta[tmfbuilder][product_order][]',
									],
									'options'          => [
										[
											'text'  => esc_html__( 'Ascending', 'woocommerce-tm-extra-product-options' ),
											'value' => 'asc',
										],
										[
											'text'  => esc_html__( 'Descending', 'woocommerce-tm-extra-product-options' ),
											'value' => 'desc',
										],
									],
									'label'            => esc_html__( 'Order', 'woocommerce-tm-extra-product-options' ),
									'desc'             => esc_html__( 'Select the sorting order of the products.', 'woocommerce-tm-extra-product-options' ),
									'required'         => [
										'.product-mode'    => [
											'operator' => 'isnot',
											'value'    => 'product',
										],
										'.product-orderby' => [
											'operator' => 'isnot',
											'value'    => 'none',
										],
									],
								],

								[
									'id'          => 'product_default_value',
									'wpmldisable' => 1,
									'default'     => '',
									'type'        => 'select',
									'tags'        => [
										'data-placeholder' => esc_attr__( 'Select a product', 'woocommerce-tm-extra-product-options' ),
										'class'            => 'wc-product-search product-default-value-search',
										'id'               => 'builder_product_default_value',
										'name'             => 'tm_meta[tmfbuilder][product_default_value][]',
									],
									'options'     => [],
									'label'       => esc_html__( 'Default product', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Select the product that will be pre-selected.', 'woocommerce-tm-extra-product-options' ),
								],

								[
									'id'               => 'product_layout_mode',
									'wpmldisable'      => 1,
									'default'          => 'dropdown',
									'message0x0_class' => 'tm-epo-switch-wrapper',
									'type'             => 'radio',
									'tags'             => [
										'class' => 'product-layout-mode',
										'id'    => 'builder_product_layout_mode',
										'name'  => 'tm_meta[tmfbuilder][product_layout_mode][]',
									],
									'options'          => [
										[
											'text'  => esc_html__( 'Dropdown', 'woocommerce-tm-extra-product-options' ),
											'value' => 'dropdown',
										],
										[
											'text'  => esc_html__( 'Radio buttons', 'woocommerce-tm-extra-product-options' ),
											'value' => 'radio',
										],
										[
											'text'  => esc_html__( 'Thumbnails', 'woocommerce-tm-extra-product-options' ),
											'value' => 'thumbnail',
										],
										[
											'text'  => esc_html__( 'Checkboxes', 'woocommerce-tm-extra-product-options' ),
											'value' => 'checkbox',
										],
										[
											'text'  => esc_html__( 'Thumbnails multiple', 'woocommerce-tm-extra-product-options' ),
											'value' => 'thumbnailmultiple',
										],
									],
									'label'            => esc_html__( 'Layout mode', 'woocommerce-tm-extra-product-options' ),
									'desc'             => esc_html__( 'Select how the products will be presented.', 'woocommerce-tm-extra-product-options' ),
									'required'         => [
										'.product-mode' => [
											'operator' => 'isnot',
											'value'    => 'product',
										],
									],
								],

								THEMECOMPLETE_EPO_BUILDER()->add_setting_items_per_row(
									'product',
									[],
									[
										'.product-layout-mode' => [
											'operator' => 'is',
											'value'    => [ 'thumbnail', 'thumbnailmultiple' ],
										],
										'.product-mode' => [
											'operator' => 'isnot',
											'value'    => 'product',
										],
									]
								),

								[
									'placeholder',
									[
										'label'    => esc_html__( 'Dropdown Placeholder', 'woocommerce-tm-extra-product-options' ),
										'desc'     => esc_html__( 'Enter the placeholder for the dropdown.', 'woocommerce-tm-extra-product-options' ),
										'required' => [
											'.product-layout-mode' => [
												'operator' => 'is',
												'value'    => 'dropdown',
											],
										],
									],
								],

								THEMECOMPLETE_EPO_BUILDER()->add_setting_min(
									'product_quantity',
									[
										'extra_tags' => [ 'min' => 0 ],
										'default'    => '',
										'label'      => esc_html__( 'Minimum quantity', 'woocommerce-tm-extra-product-options' ),
									],
									false
								),
								THEMECOMPLETE_EPO_BUILDER()->add_setting_max(
									'product_quantity',
									[
										'extra_tags' => [ 'min' => 0 ],
										'default'    => '',
										'label'      => esc_html__( 'Maximum quantity', 'woocommerce-tm-extra-product-options' ),
									],
									false
								),
								[
									'id'          => 'product_disable_epo',
									'wpmldisable' => 1,
									'default'     => '',
									'type'        => 'checkbox',
									'tags'        => [
										'value' => '1',
										'id'    => 'builder_product_disable_epo',
										'name'  => 'tm_meta[tmfbuilder][product_disable_epo][]',
									],
									'label'       => esc_html__( 'Disable Addons', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'If this is enabled the included addons the associated product has will not be displayed.', 'woocommerce-tm-extra-product-options' ),
								],
								[
									'id'          => 'product_shipped_individually',
									'wpmldisable' => 1,
									'default'     => '',
									'type'        => 'checkbox',
									'tags'        => [
										'value' => '1',
										'id'    => 'builder_product_shipped_individually',
										'name'  => 'tm_meta[tmfbuilder][product_shipped_individually][]',
									],
									'label'       => esc_html__( 'Shipped individually', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'If this is enabled the included product is not shipped with the main product.', 'woocommerce-tm-extra-product-options' ),
								],
								[
									'id'          => 'product_maintain_weight',
									'wpmldisable' => 1,
									'default'     => '',
									'type'        => 'checkbox',
									'tags'        => [
										'value' => '1',
										'id'    => 'builder_product_maintain_weight',
										'name'  => 'tm_meta[tmfbuilder][product_maintain_weight][]',
									],
									'label'       => esc_html__( 'Maintain weight', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'If this is enabled the included product will add its weight to the main product.', 'woocommerce-tm-extra-product-options' ),
									'required'    => [
										'#builder_product_shipped_individually' => [
											'operator' => 'isnot',
											'value'    => '1',
										],
									],
								],
								[
									'id'          => 'product_priced_individually',
									'wpmldisable' => 1,
									'default'     => '',
									'type'        => 'checkbox',
									'tags'        => [
										'value' => '1',
										'id'    => 'builder_product_priced_individually',
										'name'  => 'tm_meta[tmfbuilder][product_priced_individually][]',
									],
									'label'       => esc_html__( 'Priced individually', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'If this is enabled the included product will maintain its own price.', 'woocommerce-tm-extra-product-options' ),
								],
								[
									'id'               => 'product_discount_type',
									'wpmldisable'      => 1,
									'default'          => 'percent',
									'message0x0_class' => 'tm-epo-switch-wrapper',
									'type'             => 'radio',
									'tags'             => [
										'id'   => 'builder_product_discount_type',
										'name' => 'tm_meta[tmfbuilder][product_discount_type][]',
									],
									'options'          => [
										[
											'text'  => esc_html__( 'Percentage', 'woocommerce-tm-extra-product-options' ),
											'value' => 'percent',
										],
										[
											'text'  => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ),
											'value' => 'fixed',
										],
									],
									'label'            => esc_html__( 'Discount type', 'woocommerce-tm-extra-product-options' ),
									'desc'             => esc_html__( 'Select the discount type. The discount applies to the final product price. If the product has extra options then the discount applies to the amount after the options have been added to the product price.', 'woocommerce-tm-extra-product-options' ),
									'required'         => [
										'#builder_product_priced_individually' => [
											'operator' => 'is',
											'value'    => '1',
										],
									],
								],
								[
									'id'          => 'product_discount',
									'wpmldisable' => 1,
									'default'     => '',
									'type'        => 'text',
									'tags'        => [
										'class' => 't',
										'id'    => 'builder_product_discount',
										'name'  => 'tm_meta[tmfbuilder][product_discount][]',
										'value' => '',
									],
									'label'       => esc_html__( 'Discount', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Enter the discount amount.', 'woocommerce-tm-extra-product-options' ),
									'required'    => [
										'#builder_product_priced_individually' => [
											'operator' => 'is',
											'value'    => '1',
										],
									],
								],
								[
									'id'          => 'product_discount_exclude_addons',
									'wpmldisable' => 1,
									'default'     => '',
									'type'        => 'checkbox',
									'tags'        => [
										'value' => '1',
										'id'    => 'builder_product_discount_exclude_addons',
										'name'  => 'tm_meta[tmfbuilder][product_discount_exclude_addons][]',
									],
									'label'       => esc_html__( 'Exlude Addon prices from the discount', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'If this is enabled the discount will not be applied to the product addons.', 'woocommerce-tm-extra-product-options' ),
								],

								[
									'id'          => 'product_hiddenin',
									'wpmldisable' => 1,
									'default'     => '',
									'type'        => 'select',
									'multiple'    => 'multiple',
									'tags'        => [
										'class' => 'product-hiddenin-selector',
										'id'    => 'builder_product_hiddenin',
										'name'  => 'tm_meta[tmfbuilder][product_hiddenin][]',
									],
									'options'     => [
										[
											'text'  => esc_attr__( 'Cart', 'woocommerce-tm-extra-product-options' ),
											'value' => 'cart',
										],
										[
											'text'  => esc_attr__( 'Checkout', 'woocommerce-tm-extra-product-options' ),
											'value' => 'checkout',
										],
										[
											'text'  => esc_attr__( 'Order and Emails', 'woocommerce-tm-extra-product-options' ),
											'value' => 'order',
										],
									],
									'label'       => esc_html__( 'Hide product in', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Select where to hide the product', 'woocommerce-tm-extra-product-options' ),
								],

							],
						'advanced_options' =>
							[
								[
									'id'          => 'product_show_image',
									'wpmldisable' => 1,
									'default'     => '1',
									'type'        => 'checkbox',
									'tags'        => [
										'value' => '1',
										'id'    => 'builder_product_show_image',
										'name'  => 'tm_meta[tmfbuilder][product_show_image][]',
									],
									'label'       => esc_html__( 'Show image', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Enable to show the image of the associated product.', 'woocommerce-tm-extra-product-options' ),
								],
								[
									'id'          => 'product_show_title',
									'wpmldisable' => 1,
									'default'     => '1',
									'type'        => 'checkbox',
									'tags'        => [
										'value' => '1',
										'id'    => 'builder_product_show_title',
										'name'  => 'tm_meta[tmfbuilder][product_show_title][]',
									],
									'label'       => esc_html__( 'Show title', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Enable to show the title of the associated product.', 'woocommerce-tm-extra-product-options' ),
								],
								[
									'id'          => 'product_show_price',
									'wpmldisable' => 1,
									'default'     => '1',
									'type'        => 'checkbox',
									'tags'        => [
										'value' => '1',
										'id'    => 'builder_product_show_price',
										'name'  => 'tm_meta[tmfbuilder][product_show_price][]',
									],
									'label'       => esc_html__( 'Show price', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Enable to show the price of the associated product.', 'woocommerce-tm-extra-product-options' ),
								],
								[
									'id'          => 'product_show_description',
									'wpmldisable' => 1,
									'default'     => '1',
									'type'        => 'checkbox',
									'tags'        => [
										'value' => '1',
										'id'    => 'builder_product_show_description',
										'name'  => 'tm_meta[tmfbuilder][product_show_description][]',
									],
									'label'       => esc_html__( 'Show description', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Enable to show the description of the associated product.', 'woocommerce-tm-extra-product-options' ),
								],
								[
									'id'          => 'product_show_meta',
									'wpmldisable' => 1,
									'default'     => '1',
									'type'        => 'checkbox',
									'tags'        => [
										'value' => '1',
										'id'    => 'builder_product_show_meta',
										'name'  => 'tm_meta[tmfbuilder][product_show_meta][]',
									],
									'label'       => esc_html__( 'Show meta', 'woocommerce-tm-extra-product-options' ),
									'desc'        => esc_html__( 'Enable to show the meta of the associated product.', 'woocommerce-tm-extra-product-options' ),
								],
							],
					],
			],
			false,
			[
				'label_options'        => 1,
				'general_options'      => 1,
				'advanced_options'     => 1,
				'conditional_logic'    => 1,
				'css_settings'         => 0,
				'woocommerce_settings' => 0,
			],
			[
				'advanced_options' => [
					'name' => esc_html__( 'Advanced options', 'woocommerce-tm-extra-product-options' ),
					'icon' => 'tcfa tcfa-cog',
					'slug' => 'tma-tab-advanced',
				],
			]
		);
	}
}
