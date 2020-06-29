<?php

/*
Class Name: WP_SM_Admin_Settings
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2016 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Admin_Settings {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page' ) );
		add_filter( 'wbs_data_settings', array( $this, 'set_options' ) );
		add_action( 'wbs_settings_end_of_tab_crosssell', array( $this, 'add_bundle_price_rule' ) );
		add_action( 'woocommerce-boost-sales-update-tab', array( $this, 'auto_update_key' ) );
		add_filter( 'admin_enqueue_scripts', array( $this, 'init_scripts' ), 999999 );
	}

	public function auto_update_key( $params ) {
		?>
        <tr valign="top">
            <th scope="row">
                <label for="auto-update-key"><?php esc_html_e( 'Auto Update Key', 'woocommerce-boost-sales' ) ?></label>
            </th>
            <td>
                <div class="fields">
                    <div class="ten wide field">
                        <input type="text" name="_woocommerce_boost_sales[key]" id="auto-update-key"
                               class="villatheme-autoupdate-key-field"
                               value="<?php echo htmlentities( $params->get_option( 'key' ) ) ?>">
                    </div>
                    <div class="six wide field">
                        <span class="vi-ui button green villatheme-get-key-button"
                              data-href="https://api.envato.com/authorization?response_type=code&client_id=villatheme-download-keys-6wzzaeue&redirect_uri=https://villatheme.com/update-key"
                              data-id="19668456"><?php echo esc_html__( 'Get Key', 'woocommerce-boost-sales' ) ?></span>
                    </div>
                </div>
				<?php do_action( 'woocommerce-boost-sales_key' ) ?>
                <p class="description"><?php echo __( 'Please fill your key what you get from <a target="_blank" href="https://villatheme.com/my-download">Villatheme</a>. You can automatically update WooCommerce Boost sales plugin. See guide <a target="_blank" href="https://villatheme.com/knowledge-base/how-to-use-auto-update-feature/">here</a>', 'woocommerce-boost-sales' ) ?></p>
            </td>
        </tr>
		<?php
	}

	protected function set_params( $name = '', $class = false, $multiple = false ) {
		if ( $name ) {
			if ( $class ) {
				echo 'wbs-crosssell-' . str_replace( '_', '-', $name );
			} else {
				if ( $multiple ) {
					echo 'wbs_crosssell_' . $name . '[]';
				} else {
					echo 'wbs_crosssell_' . $name;
				}
			}
		}
	}

	public function add_bundle_price_rule( $params ) {
		$price_from     = $params->get_option( 'bundle_price_from' );
		$discount_value = $params->get_option( 'bundle_price_discount_value' );
		$discount_type  = $params->get_option( 'bundle_price_discount_type' );
		$level_count    = is_array( $price_from ) ? count( $price_from ) : 0;
		$currency       = get_woocommerce_currency();
		?>
        <table class="optiontable form-table">
            <h4><?php esc_html_e( 'Recalculate bundle price.', 'woocommerce-boost-sales' ) ?></h4>
            <tbody class="<?php $this->set_params( 'price_rule_container', true ) ?>">
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Bundle Price From', 'woocommerce-boost-sales' ) ?></th>
                <th scope="row"><?php esc_html_e( 'Discount Value', 'woocommerce-boost-sales' ) ?></th>
                <th scope="row"><?php esc_html_e( 'Discount Type', 'woocommerce-boost-sales' ) ?></th>
            </tr>
			<?php
			if ( is_array( $price_from ) && $level_count > 0 ) {
				for ( $i = 0; $i < $level_count; $i ++ ) {
					?>
                    <tr valign="top" class="<?php $this->set_params( 'price_rule_row', true ) ?>">
                        <td>
                            <input type="number"
                                   min="<?php echo isset( $price_from[ $i - 1 ] ) ? ( $price_from[ $i - 1 ] + 1 ) : 0 ?>"
                                   max="<?php echo $i > 0 ? ( isset( $price_from[ $i + 1 ] ) ? ( ( $price_from[ $i + 1 ] - 1 ) ) : '' ) : 0 ?>"
                                   value="<?php echo $i > 0 ? $price_from[ $i ] : 0; ?>"
                                   name="_woocommerce_boost_sales[bundle_price_from][]"
                                   class="<?php $this->set_params( 'bundle_price_from', true ); ?>">
                        </td>
                        <td>
                            <input type="number" min="0" step="0.01" <?php if ( $discount_type[ $i ] === 'percent' )
								echo 'max="100"' ?>
                                   value="<?php echo $discount_value[ $i ]; ?>"
                                   name="_woocommerce_boost_sales[bundle_price_discount_value][]"
                                   class="<?php $this->set_params( 'bundle_price_discount_value', true ); ?>">
                        </td>
                        <td>
                            <div>
                                <select name="_woocommerce_boost_sales[bundle_price_discount_type][]"
                                        class="<?php $this->set_params( 'bundle_price_discount_type', true ); ?> vi-ui fluid dropdown">
                                    <option value="fixed" <?php selected( $discount_type[ $i ], 'fixed' ) ?>><?php esc_html_e( 'Fixed' . '(' . $currency . ')', 'woocommerce-boost-sales' ) ?></option>
                                    <option value="percent" <?php selected( $discount_type[ $i ], 'percent' ) ?>><?php esc_html_e( 'Percent(%)', 'woocommerce-boost-sales' ) ?></option>
                                </select>
                            </div>
                        </td>
                    </tr>
					<?php
				}
			} else {
				?>
                <tr valign="top" class="<?php $this->set_params( 'price_rule_row', true ) ?>">
                    <td>
                        <input type="number"
                               min="0"
                               max="0"
                               value="0"
                               name="_woocommerce_boost_sales[bundle_price_from][]"
                               class="<?php $this->set_params( 'bundle_price_from', true ); ?>">
                    </td>
                    <td>
                        <input type="number" min="0" step="0.01"
                               name="_woocommerce_boost_sales[bundle_price_discount_value][]"
                               class="<?php $this->set_params( 'bundle_price_discount_value', true ); ?>">
                    </td>
                    <td>
                        <select name="_woocommerce_boost_sales[bundle_price_discount_type][]"
                                class="<?php $this->set_params( 'bundle_price_discount_type', true ); ?>">
                            <option value="fixed"><?php echo esc_html__( 'Fixed', 'woocommerce-boost-sales' ) . '(' . $currency . ')' ?></option>
                            <option value="percent"><?php esc_html_e( 'Percent(%)', 'woocommerce-boost-sales' ) ?></option>
                        </select>
                    </td>
                </tr>
				<?php
			}
			?>
            </tbody>
        </table>
        <span class="<?php $this->set_params( 'price_rule_add', true ); ?> vi-ui button positive"><?php esc_html_e( 'Add', 'woocommerce-boost-sales' ) ?></span>
        <span class="<?php $this->set_params( 'price_rule_remove', true ); ?> vi-ui button negative"><?php esc_html_e( 'Remove last level', 'woocommerce-boost-sales' ) ?></span>
		<?php
	}

	public function init_scripts() {
		$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
		if ( $page == 'woocommerce-boost-sales' ) {
			wp_enqueue_script( 'woocommerce-boost-sales-admin', VI_WBOOSTSALES_JS . 'woocommerce-boost-sales-admin.js', array( 'jquery' ), VI_WBOOSTSALES_VERSION );
			wp_enqueue_style( 'woocommerce-boost-sales', VI_WBOOSTSALES_CSS . 'woocommerce-boost-sales-admin.css', array(), VI_WBOOSTSALES_VERSION );
		}
	}

	/**
	 * Get list shortcode
	 * @return array
	 */
	public static function page_callback() {
		?>
        <div class="wrap woocommerce-boost-sales">
            <h2><?php esc_attr_e( 'WooCommerce Boost Sales Settings', ' woocommerce-boost-sales' ) ?></h2>
			<?php
			do_action( 'villatheme_setting_html' );
			do_action( 'villatheme_support_woocommerce-boost-sales' );
			?>


        </div>
		<?php
	}

	/**
	 * Get list option
	 * @return array
	 */
	public function set_options( $data ) {

		$data['general'] = array(
			'title'  => esc_html__( 'General', 'woocommerce-boost-sales' ),
			'active' => true,
			'fields' => array(
				array(
					'name'        => 'enable',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Enable', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( esc_html__( 'Yes', 'woocommerce-boost-sales' ), 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'enable_mobile',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Enable Mobile', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( esc_html__( 'Yes', 'woocommerce-boost-sales' ), 'woocommerce-boost-sales' ),
					)
				)
			)
		);

		$data['upsell'] = array(
			'title'  => esc_html__( 'Upsell', 'woocommerce-boost-sales' ),
			'fields' => array(
				array(
					'name'        => 'enable_upsell',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Enable', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'hide_on_single_product_page',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Hide on Single Product Page', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'hide_on_cart_page',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Hide on Cart Page', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'hide_on_checkout_page',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Hide on Checkout Page', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'hide_out_stock',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Hide Products Out Of Stock', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'hide_products_added',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Hide Products Added to Cart', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'go_to_cart',
					'type'        => 'checkbox',
					'value'       => 0,
					'label'       => esc_html__( 'Go to cart page', 'woocommerce-boost-sales' ),
					'description' => esc_html__( 'Go to cart page when product is added to cart on up sells.', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'show_recently_viewed_products',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => esc_html__( 'Upsell popup will show recently viewed products.', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Recently Viewed Products', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'show_with_category',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => esc_html__( 'Upsell popup will show products in the same category. Upsell products of Upsells page will not use.', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Products in category', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),

				array(
					'name'        => 'show_upsells_checkbox',
					'class'       => 'wbs-products-in-category',
					'type'        => 'select',
					'value'       => 0,
					'description' => esc_html__( 'Customer can add to cart many upsell products to cart.', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Show up products ', 'woocommerce-boost-sales' ),
					'options'     => array(
						'0' => esc_html__( 'Not Show', 'woocommerce-boost-sales' ),
						'1' => esc_html__( 'Show above description', 'woocommerce-boost-sales' )
					)
				),
				array(
					'name'        => 'show_with_subcategory',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => esc_html__( 'Only get products from current subcategory. It is the end subcategory.', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Only Subcategory', 'woocommerce-boost-sales' ),
					'class'       => 'wbs_exclude_product',
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'upsell_exclude_products',
					'type'        => 'select2_ajax',
					'value'       => '',
					'description' => '',
					'placeholder' => esc_html__( 'Please fill your product title', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Exclude products to enable upsell', 'woocommerce-boost-sales' ),
					'class'       => 'product-search wbs_exclude_product',
					'multiple'    => 'multiple'
				),
				array(
					'name'        => 'exclude_product',
					'type'        => 'select2_ajax',
					'value'       => '',
					'description' => '',
					'placeholder' => esc_html__( 'Please fill your product title', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Exclude products that display in upsell popup', 'woocommerce-boost-sales' ),
					'class'       => 'product-search wbs_exclude_product',
					'multiple'    => 'multiple'
				),
				array(
					'name'        => 'upsell_exclude_categories',
					'type'        => 'select2_ajax_category',
					'value'       => '',
					'description' => '',
					'placeholder' => esc_html__( 'Please fill your category title', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Exclude categories to enable upsell', 'woocommerce-boost-sales' ),
					'class'       => 'wbs-category-search wbs_exclude_product',
					'multiple'    => 'multiple'
				),
				array(
					'name'        => 'exclude_categories',
					'type'        => 'select2_ajax_category',
					'value'       => '',
					'description' => '',
					'placeholder' => esc_html__( 'Please fill your category title', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Exclude categories that display in upsell popup', 'woocommerce-boost-sales' ),
					'class'       => 'wbs-category-search wbs_exclude_product',
					'multiple'    => 'multiple'
				),

				array(
					'name'        => 'sort_product',
					'type'        => 'select',
					'value'       => '0',
					'description' => '',
					'label'       => esc_html__( 'Sort by', 'woocommerce-boost-sales' ),
					'class'       => 'wbs_exclude_product',
					'options'     => array(
						'0' => esc_html__( 'Title A-Z', 'woocommerce-boost-sales' ),
						'1' => esc_html__( 'Title Z-A', 'woocommerce-boost-sales' ),
						'2' => esc_html__( 'Price highest', 'woocommerce-boost-sales' ),
						'3' => esc_html__( 'Price lowest', 'woocommerce-boost-sales' ),
						'4' => esc_html__( 'Random', 'woocommerce-boost-sales' ),
						'5' => esc_html__( 'Best Selling', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'    => 'hide_view_more_button',
					'type'    => 'checkbox',
					'value'   => 0,
					'label'   => esc_html__( 'Hide view more button', 'woocommerce-boost-sales' ),
					'options' => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'ajax_button',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => esc_html__( 'Add to cart will use Ajax instead of submit in Detail Product page.', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Add To Cart button', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'ajax_add_to_cart_for_upsells',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => esc_html__( 'Do not redirect page when customers add up-sells products to their cart. This will override option "Go to cart page"', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Ajax add to cart for product on up-sell popup', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
			)
		);

		$data['crosssell'] = array(
			'title'  => esc_html__( 'Cross sell', 'woocommerce-boost-sales' ),
			'fields' => array(
				array(
					'name'    => 'crosssell_enable',
					'type'    => 'checkbox',
					'value'   => 0,
					'label'   => esc_html__( 'Enable', 'woocommerce-boost-sales' ),
					'options' => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					),
				),
				array(
					'name'        => 'crosssells_hide_on_single_product_page',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Hide on Single Product Page', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'    => 'crosssell_display_on',
					'type'    => 'select',
					'value'   => 0,
					'label'   => esc_html__( 'Display on', 'woocommerce-boost-sales' ),
					'options' => array(
						'0' => esc_html__( 'Popup', 'woocommerce-boost-sales' ),
						'1' => esc_html__( 'Below Add to cart button', 'woocommerce-boost-sales' ),
						'2' => esc_html__( 'Above Description Tab', 'woocommerce-boost-sales' ),
						'3' => esc_html__( 'Below description', 'woocommerce-boost-sales' ),
					),
				),
				array(
					'name'        => 'crosssell_display_on_slide',
					'type'        => 'checkbox',
					'value'       => 0,
					'label'       => esc_html__( 'Slide', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					),
					'description' => esc_html__( 'Make slider for cross-sells', 'woocommerce-boost-sales' ),
					'class'       => 'crosssell_display_on'
				),
				array(
					'name'    => 'enable_cart_page',
					'type'    => 'checkbox',
					'value'   => 0,
					'label'   => esc_html__( 'Show on Cart page', 'woocommerce-boost-sales' ),
					'options' => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					),
				),
				array(
					'name'        => 'cart_page_option',
					'type'        => 'select',
					'value'       => 0,
					'label'       => esc_html__( 'Product bundle type', 'woocommerce-boost-sales' ),
					'options'     => array(
						'0' => esc_html__( 'The largest quantity in order', 'woocommerce-boost-sales' ),
						'1' => esc_html__( 'Random', 'woocommerce-boost-sales' ),
						'2' => esc_html__( 'The most expensive', 'woocommerce-boost-sales' )
					),
					'class'       => 'select_product_bundle',
					'description' => esc_html__( 'Select product bundle type on Cart page', 'woocommerce-boost-sales' )
				),
				array(
					'name'    => 'enable_checkout_page',
					'type'    => 'checkbox',
					'value'   => 0,
					'label'   => esc_html__( 'Show on Checkout page', 'woocommerce-boost-sales' ),
					'options' => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					),
				),
				array(
					'name'        => 'checkout_page_option',
					'type'        => 'select',
					'value'       => 1,
					'label'       => esc_html__( 'Product bundle type', 'woocommerce-boost-sales' ),
					'options'     => array(
						'0' => esc_html__( 'The largest quantity in order', 'woocommerce-boost-sales' ),
						'1' => esc_html__( 'Random', 'woocommerce-boost-sales' ),
						'2' => esc_html__( 'The most expensive', 'woocommerce-boost-sales' )
					),
					'class'       => 'select_product_bundle_checkout',
					'description' => esc_html__( 'Select product bundle type on Checkout page', 'woocommerce-boost-sales' )
				),
				array(
					'name'        => 'bundle_added',
					'type'        => 'checkbox',
					'value'       => 0,
					'label'       => esc_html__( 'The same bundle in cart', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					),
					'description' => esc_html__( 'The same bundle can display in cart page and checkout page.', 'woocommerce-boost-sales' )

				),
				array(
					'name'            => 'crosssell_description',
					'type'            => 'text',
					'value'           => esc_html__( 'Hang on! We have this offer just for you!', 'woocommerce-boost-sales' ),
					'label'           => esc_html__( 'Description', 'woocommerce-boost-sales' ),
					'is_multilingual' => true,
				),
				array(
					'name'        => 'display_saved_price',
					'type'        => 'select',
					'value'       => 0,
					'label'       => esc_html__( 'Display saved price', 'woocommerce-boost-sales' ),
					'options'     => array(
						'0' => esc_html__( 'Price', 'woocommerce-boost-sales' ),
						'1' => esc_html__( 'Percent', 'woocommerce-boost-sales' ),
					),
					'description' => esc_html__( 'Display saved price on cross-sell.', 'woocommerce-boost-sales' )
				),
				array(
					'name'        => 'override_products_on_cart',
					'type'        => 'checkbox',
					'value'       => 0,
					'label'       => esc_html__( 'Override products', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					),
					'description' => esc_html__( 'Remove the same products on cart when add combo.', 'woocommerce-boost-sales' )
				),
				array(
					'name'        => 'ajax_add_to_cart_for_crosssells',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => esc_html__( 'Do not redirect page when customers add bundle to their cart on single product page', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Ajax add to cart for bundle', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'hide_out_of_stock',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => esc_html__( 'Do not show crosssell if one of bundle items is out of stock', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Hide out of stock', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'product_bundle_name',
					'type'        => 'text',
					'value'       => 'Bundle of {product_title}',
					'label'       => esc_html__( 'Product bundle name', 'woocommerce-boost-sales' ),
					'description' => __( 'Name of product bundle when creating new bundle. {product_title} refers to the title of main product that the bundle is created for.<p>e.g when you create a bundle for product named "Product A", if Product bundle name is set "Bundle of {product_title}" then new bundle\'s name will be "Bundle of Product A"</p>', 'woocommerce-boost-sales' )
				),
				array(
					'name'        => 'bundle_categories',
					'type'        => 'select2_ajax_category',
					'value'       => array(),
					'label'       => esc_html__( 'Bundle categories', 'woocommerce-boost-sales' ),
					'description' => __( 'Default categories when you create new bundle', 'woocommerce-boost-sales' ),
					'placeholder' => esc_html__( 'Please fill your category name', 'woocommerce-boost-sales' ),
					'class'       => 'wbs-category-search',
					'multiple'    => 'multiple'
				),
			)
		);
		$data['discount']  = array(
			'title'  => esc_html__( 'Discount', 'woocommerce-boost-sales' ),
			'fields' => array(
				array(
					'name'        => 'enable_discount',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Enable', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( esc_html__( 'Yes', 'woocommerce-boost-sales' ), 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'discount_always_show',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' =>esc_html__( 'If this option is disabled, discount bar will only show each time a customer add a product to cart', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Always show discount bar', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( 'Always display discount bar if customers have not reached the minimum amount and the cart is not empty', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'         => 'coupon',
					'type'         => 'select2_ajax',
					'product_type' => 'shop_coupon',
					'placeholder'  => esc_html__( 'Please fill your coupon name', 'woocommerce-boost-sales' ),
					'value'        => 0,
					'description'  => esc_html__( 'If 2 coupons name coincide - the coupon latest will eventually be used. ', 'woocommerce-boost-sales' ) . esc_html__( 'Dashboard >> WooCommerce >> Coupons >>', 'woocommerce-boost-sales' ) . '<a target="_bank" href="' . esc_url( admin_url( 'post-new.php?post_type=shop_coupon' ) ) . '">' . esc_html__( 'Add New Coupon', 'woocommerce-boost-sales' ) . '</a>',
					'label'        => esc_html__( 'Select Coupon', 'woocommerce-boost-sales' ),
					'class'        => 'select-coupon select2',
					'options'      => array()
				),
				array(
					'name'            => 'coupon_desc',
					'type'            => 'text',
					'value'           => esc_html__( 'SWEET! Add more products and get {discount_amount} off on your entire order!', 'woocommerce-boost-sales' ),
					'description'     => esc_html__( '{discount_amount} - The number of discount.', 'woocommerce-boost-sales' ),
					'label'           => esc_html__( 'Head line', 'woocommerce-boost-sales' ),
					'is_multilingual' => true,
				),
				array(
					'name'        => 'enable_thankyou',
					'type'        => 'checkbox',
					'value'       => 0,
					'description' => esc_html__( 'Congrats when get coupon', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Thank You', 'woocommerce-boost-sales' ),
					'options'     => array(
						'1' => esc_html__( esc_html__( 'Yes', 'woocommerce-boost-sales' ), 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'            => 'message_congrats',
					'type'            => 'textarea',
					'value'           => esc_html__( 'You have successfully reached the goal, and a {discount_amount} discount will be applied to your order.', 'woocommerce-boost-sales' ),
					'description'     => esc_html__( '{discount_amount} - The number of discount', 'woocommerce-boost-sales' ),
					'label'           => esc_html__( 'Message congratulation', 'woocommerce-boost-sales' ),
					'class'           => 'wbs-message_congrats',
					'is_multilingual' => true,
				),
				array(
					'name'            => 'text_btn_checkout',
					'type'            => 'text',
					'value'           => esc_html__( 'Checkout now', 'woocommerce-boost-sales' ),
					'label'           => esc_html__( 'Text button checkout', 'woocommerce-boost-sales' ),
					'is_multilingual' => true,
				),
				array(
					'name'    => 'enable_checkout',
					'type'    => 'checkbox',
					'value'   => 0,
					'label'   => esc_html__( 'Auto redirect checkout', 'woocommerce-boost-sales' ),
					'options' => array(
						'1' => esc_html__( esc_html__( 'Yes', 'woocommerce-boost-sales' ), 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'  => 'redirect_after_second',
					'type'  => 'number',
					'value' => 5,
					'label' => esc_html__( 'Redirect after second', 'woocommerce-boost-sales' ),
					'class' => 'wbs-enable_checkout',
				),
			)
		);
		$data['design']    = array(
			'title'  => esc_html__( 'Design', 'woocommerce-boost-sales' ),
			'fields' => array(
				array(
					'type'  => 'title',
					'value' => esc_html__( 'General', 'woocommerce-boost-sales' )
				),
				array(
					'name'  => 'button_color',
					'type'  => 'color-picker',
					'value' => '#111111',
					'label' => esc_html__( 'Button color', 'woocommerce-boost-sales' ),
				),
				array(
					'name'  => 'button_bg_color',
					'type'  => 'color-picker',
					'value' => '#bdbdbd',
					'label' => esc_html__( 'Button background color', 'woocommerce-boost-sales' ),
				),
				array(
					'type'  => 'title',
					'value' => esc_html__( 'Cross-Sells', 'woocommerce-boost-sales' )
				),
				array(
					'name'        => 'init_delay',
					'type'        => 'text',
					'value'       => '3,10',
					'label'       => esc_html__( 'Init delay', 'woocommerce-boost-sales' ),
					'description' => esc_html__( 'Cross-sell will show with popup or gift icon. If you want to time randomly, 2 numbers are separated by comma. Eg: 3,20. It is random from 3 to 20.', 'woocommerce-boost-sales' ),
				),
				array(
					'name'    => 'enable_cross_sell_open',
					'type'    => 'checkbox',
					'value'   => 0,
					'label'   => esc_html__( 'Auto popup', 'woocommerce-boost-sales' ),
					'options' => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					),
				),
				array(
					'name'    => 'hide_gift',
					'type'    => 'checkbox',
					'value'   => 0,
					'label'   => esc_html__( 'Hide Gift Icon', 'woocommerce-boost-sales' ),
					'options' => array(
						'1' => esc_html__( 'Yes', 'woocommerce-boost-sales' ),
					),
				),
				array(
					'name'        => 'icon',
					'type'        => 'select',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Icon ', 'woocommerce-boost-sales' ),
					'options'     => array(
						'0' => esc_html__( 'Default', 'woocommerce-boost-sales' ),
						'1' => esc_html__( 'Gift box', 'woocommerce-boost-sales' ),
						'2' => esc_html__( 'Custom', 'woocommerce-boost-sales' ),
					)
				),
				array(
					'name'        => 'custom_gift_image',
					'type'        => 'image',
					'value'       => '',
					'label'       => esc_html__( 'Custom Gift Box Icon', 'woocommerce-boost-sales' ),
					'description' => esc_html__( 'Dimension should be 58x58(px). Please change "Icon Option" to "Custom"', 'woocommerce-boost-sales' ),
				),
				array(
					'name'        => 'icon_color',
					'type'        => 'color-picker',
					'value'       => '#555',
					'description' => esc_html__( 'Only apply with Icon default', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Icon Color', 'woocommerce-boost-sales' ),
				),
				array(
					'name'        => 'icon_bg_color',
					'type'        => 'color-picker',
					'value'       => '#fff',
					'description' => esc_html__( 'Only apply with Icon default', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Icon Background Color', 'woocommerce-boost-sales' ),
				),
				array(
					'name'        => 'icon_position',
					'type'        => 'select',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Icon Position', 'woocommerce-boost-sales' ),
					'options'     => array(
						'0' => esc_html__( 'Bottom right', 'woocommerce-boost-sales' ),
						'1' => esc_html__( 'Botton left', 'woocommerce-boost-sales' )
					)
				),
				array(
					'name'        => 'bg_color_cross_sell',
					'type'        => 'color-picker',
					'value'       => '#ffffff',
					'description' => esc_html__( 'Background color for popup cross-sell.', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Background Color', 'woocommerce-boost-sales' ),
				),
				array(
					'name'        => 'bg_image_cross_sell',
					'type'        => 'image',
					'value'       => '',
					'description' => '',
					'label'       => esc_html__( 'Background Image', 'woocommerce-boost-sales' ),
				),
				array(
					'name'        => 'text_color_cross_sell',
					'type'        => 'color-picker',
					'value'       => '#9e9e9e',
					'description' => '',
					'label'       => esc_html__( 'Text Color', 'woocommerce-boost-sales' ),
				),
				array(
					'name'        => 'price_text_color_cross_sell',
					'type'        => 'color-picker',
					'value'       => '#111111',
					'description' => '',
					'label'       => esc_html__( 'Price Color', 'woocommerce-boost-sales' ),
				),
				array(
					'name'        => 'save_price_text_color_cross_sell',
					'type'        => 'color-picker',
					'value'       => '#111111',
					'description' => '',
					'label'       => esc_html__( 'Save Price Color', 'woocommerce-boost-sales' ),
				),
				array(
					'name'        => 'crosssell_mobile_template',
					'type'        => 'select',
					'value'       => 'slider',
					'description' => '',
					'label'       => esc_html__( 'Template on mobile', 'woocommerce-boost-sales' ),
					'options'     => array(
						'slider' => esc_html__( 'Slider', 'woocommerce-boost-sales' ),
						'scroll' => esc_html__( 'Scroll', 'woocommerce-boost-sales' )
					)
				),

				array(
					'type'  => 'title',
					'value' => esc_html__( 'Upsells', 'woocommerce-boost-sales' )
				),
				array(
					'name'  => 'item_per_row',
					'type'  => 'number',
					'value' => '4',
					'label' => esc_html__( 'Item per row', 'woocommerce-boost-sales' ),
				),
				array(
					'name'        => 'limit',
					'type'        => 'number',
					'value'       => '8',
					'label'       => esc_html__( 'Max item', 'woocommerce-boost-sales' ),
					'description' => esc_html__( 'Maximum number of upsells per product. Used only if "Products in category" is enabled.', 'woocommerce-boost-sales' ),
				),
				array(
					'name'    => 'select_template',
					'type'    => 'radio',
					'value'   => '1',
					'label'   => esc_html__( 'Popup style', 'woocommerce-boost-sales' ),
					'class'   => 'wbs_template_upsell',
					'options' => array(
						'1' => $this->get_url_template( 'upsell-template1.png' ),
						'2' => $this->get_url_template( 'upsell-template2.png' )
					)
				),
				array(
					'name'            => 'message_bought',
					'type'            => 'text',
					'value'           => 'Frequently bought with {name_product}',
					'description'     => esc_html__( '{name_product} - The name of product purchased', 'woocommerce-boost-sales' ),
					'label'           => esc_html__( 'Message in popup', 'woocommerce-boost-sales' ),
					'is_multilingual' => true,
				),
				array(
					'name'        => 'upsell_mobile_template',
					'type'        => 'select',
					'value'       => 'slider',
					'description' => '',
					'label'       => esc_html__( 'Template on mobile', 'woocommerce-boost-sales' ),
					'options'     => array(
						'slider' => esc_html__( 'Slider', 'woocommerce-boost-sales' ),
						'scroll' => esc_html__( 'Scroll', 'woocommerce-boost-sales' )
					)
				),
				array(
					'name'            => 'continue_shopping_title',
					'type'            => 'text',
					'value'           => 'Continue Shopping',
					'description'     => '',
					'label'           => esc_html__( 'Button "Continue Shopping" title', 'woocommerce-boost-sales' ),
					'is_multilingual' => true,
				),
				array(
					'name'        => 'continue_shopping_action',
					'type'        => 'select',
					'value'       => 'stay',
					'description' => '',
					'label'       => esc_html__( 'Button "Continue Shopping" action', 'woocommerce-boost-sales' ),
					'options'     => array(
						'stay' => esc_html__( 'Just close popup', 'woocommerce-boost-sales' ),
						'shop' => esc_html__( 'Go to Shop page', 'woocommerce-boost-sales' ),
						'home' => esc_html__( 'Go to Home page', 'woocommerce-boost-sales' ),
					)
				),

				array(
					'type'  => 'title',
					'value' => esc_html__( 'Discount Bar', 'woocommerce-boost-sales' )
				),
				array(
					'name'        => 'coupon_position',
					'type'        => 'select',
					'value'       => 0,
					'description' => '',
					'label'       => esc_html__( 'Select Position', 'woocommerce-boost-sales' ),
					'options'     => array(
						'0' => esc_html__( 'Top', 'woocommerce-boost-sales' ),
						'1' => esc_html__( 'Bottom', 'woocommerce-boost-sales' )
					)
				),
				array(
					'name'        => 'text_color_discount',
					'type'        => 'color-picker',
					'value'       => '#111111',
					'description' => esc_html__( 'Color for text of process bar.', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Text color', 'woocommerce-boost-sales' ),
				),
				array(
					'name'  => 'process_color',
					'type'  => 'color-picker',
					'value' => '#111111',
					'label' => esc_html__( 'Process bar main color', 'woocommerce-boost-sales' ),
				),
				array(
					'name'        => 'process_background_color',
					'type'        => 'color-picker',
					'value'       => '#bdbdbd',
					'description' => esc_html__( 'Color of main process bar.', 'woocommerce-boost-sales' ),
					'label'       => esc_html__( 'Process bar background color', 'woocommerce-boost-sales' ),
				),

				array(
					'type'  => 'title',
					'value' => esc_html__( 'Custom', 'woocommerce-boost-sales' )
				),
				array(
					'name'        => 'custom_css',
					'type'        => 'textarea',
					'value'       => '',
					'description' => '',
					'label'       => esc_html__( 'Custom CSS', 'woocommerce-boost-sales' ),
				),
			)
		);
		$data['update']    = array(
			'title'  => esc_html__( 'Update', 'woocommerce-boost-sales' ),
			'fields' => array(
				array(
					'name'        => '',
					'type'        => '',
					'do_action'   => 'woocommerce-boost-sales-update-tab',
					'value'       => '',
					'label'       => esc_html__( 'Auto Update Key', 'woocommerce-boost-sales' ),
					'description' => ''
				),
			)
		);

		return $data;
	}

	protected function get_url_template( $src ) {
		$imag = '<img src="' . VI_WBOOSTSALES_IMAGES . $src . '" />';
		if ( $src ) {
			return $imag;
		}

		return '';
	}

	/**
	 * Register a custom menu page.
	 */
	public function menu_page() {
		add_menu_page(
			esc_html__( 'WooCommerce Boost Sales', 'woocommerce-boost-sales' ), esc_html__( 'Woo Boost Sales', 'woocommerce-boost-sales' ), 'manage_options', 'woocommerce-boost-sales', array(
			$this,
			'page_callback'
		), 'dashicons-chart-line', 2
		);

	}
} ?>