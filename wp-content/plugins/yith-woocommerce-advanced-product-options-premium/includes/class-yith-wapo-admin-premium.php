<?php
/**
 * WAPO Admin Premium Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 3.0.4
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Admin_Premium' ) ) {

	/**
	 *  YITH_WAPO_Admin_Premium Class
	 */
	class YITH_WAPO_Admin_Premium extends YITH_WAPO_Admin {
		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WAPO_Admin_Premium
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			parent::__construct();

            add_action( 'admin_footer', array( $this, 'yith_wapo_date_rule_template_js' ) );

			// Refund order.
			add_action( 'woocommerce_order_refunded', array( $this, 'manage_refunded_product_type_addons' ), 10, 2 );
			add_action( 'woocommerce_restore_order_stock', array( $this, 'restore_addons_type_product_stock' ) );
			add_action( 'woocommerce_reduce_order_stock', array( $this, 'reduce_addons_type_product_stock' ) );

			add_action( 'yith_wapo_options_before_add_image', array( $this, 'show_additional_options_before_add_image' ), 10, 3 );
			add_action( 'yith_plugin_fw_get_field_after', array( $this, 'yith_plugin_fw_get_field_after_addons' ) );

			// Plugin tabs.
			add_filter( 'yith_wapo_admin_panel_tabs', array( $this, 'yith_wapo_admin_panel_tabs_premium' ), 10, 2 );

            // Debug Tab.
            add_action( 'yith_wapo_debug_tab', array( $this, 'show_debug_tab' ) );

			// Plugin options.
			add_filter( 'yith_wapo_general_options_array', array( $this, 'add_extra_general_premium_options' ) );
			add_filter( 'yith_wapo_panel_style_options', array( $this, 'add_premium_panel_style_options' ) );

            // Add-on editor tabs
            add_filter( 'yith_wapo_get_addon_tabs', array( $this, 'add_additional_tabs' ) );
		}

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @use      YIT_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( ! empty( $this->panel ) ) {
                return;
            }

            $capability  = apply_filters( 'yith_wapo_register_panel_capabilities', 'manage_woocommerce' );
            $parent_page = 'yit_plugin_panel';

            $args = array(
                'ui_version'       => 2,
                'create_menu_page' => true,
                'class'            => yith_set_wrapper_class(),
                'parent_slug'      => '',
                'plugin_slug'      => YITH_WAPO_SLUG,
                'page_title'       => 'YITH WooCommerce Product Add-ons & Extra Options',
                'menu_title'       => 'Product Add-ons & Extra Options',
                'capability'       => $capability,
                'parent'           => YITH_WAPO_SLUG,
                'parent_page'      => $parent_page,
                'page'             => $this->panel_page,
                'admin-tabs'       => apply_filters( 'yith_wapo_admin_panel_tabs', array(), $capability ),
                'plugin-url'       => YITH_WAPO_DIR,
                'options-path'     => YITH_WAPO_DIR . 'plugin-options',
                'is_free'          => false,
                'is_extended'      => false,
                'is_premium'       => true,
                'plugin_version'   => YITH_WAPO_VERSION,
                'plugin_icon'      => YITH_WAPO_ASSETS_URL . '/img/plugins/product-add-ons.svg',
                'welcome_modals'   => array(
                    'show_in' => function ( $context ) {
                        return 'blocks' !== $context['tab'];
                    },
                    'on_close' => function () {
                        update_option( 'yith-wapo-welcome-modal', 'no' );
                    },
                    'modals'   => array(
                        'welcome' => array(
                            'type'        => 'welcome',
                            'description' => __( 'With this plugin you can add advanced options to your product pages using fields like radio buttons, checkboxes, drop-downs, custom text inputs, and more.', 'yith-woocommerce-product-add-ons' ),
                            'show'        => get_option( 'yith-wapo-welcome-modal', 'welcome' ) === 'welcome',
                            'items'       => array(
                                'documentation' => array(
                                    'url' => $this->get_doc_url(),
                                ),
                                'how-to-video'  => array(
                                    'url' => array(
                                        'en' => 'https://www.youtube.com/watch?v=EGjhyE3u_30',
                                        'it' => 'https://www.youtube.com/watch?v=EEC3YEPUeCQ',
                                        'es' => 'https://www.youtube.com/watch?v=HxROXERLP8k',
                                    ),
                                ),
                                'create-block'  => array(
                                    'title'       => __( 'Are you ready? Create your first <mark>block of options</mark>', 'yith-woocommerce-product-add-ons' ),
                                    'description' => __( '... and start the adventure!', 'yith-woocommerce-product-add-ons' ),
                                    'url'         => add_query_arg( array(
                                        'page' => 'yith_wapo_panel'

                                    ), admin_url( 'admin.php' ) ),
                                ),
                            ),
                        ),
                    ),
                ),
                'help_tab'         => array(
                    'main_video' => array(
                        //translators: [HELP TAB] Video title.
                        'desc' => __( 'Check this video to learn how to <b>create an options block and show it in a product page:</b>', 'yith-woocommerce-product-add-ons' ),
                        'url'  => array(
                            'en' => 'https://www.youtube.com/embed/EGjhyE3u_30',
                            'it' => 'https://www.youtube.com/embed/SKGteRKsYnA',
                            'es' => 'https://www.youtube.com/embed/HxROXERLP8k',
                        ),
                    ),
                    'playlists'  => array(
                        'en' => 'https://www.youtube.com/watch?v=v5JTUCmPUyQ&list=PLDriKG-6905ksfE-ofI5k1iu1D6NVzi3I',
                        'it' => 'https://www.youtube.com/watch?v=gV5pa5KYfaA&list=PL9c19edGMs09Lzsq-rvTm-6fgb6WhdRJX',
                        'es' => 'https://www.youtube.com/watch?v=N50b2nlT_YA&list=PL9Ka3j92PYJPJSgfgSWWeVXg2xQHYLx4a',
                    ),
                    'hc_url'     => 'https://support.yithemes.com/hc/en-us/categories/360003474698-YITH-WOOCOMMERCE-PRODUCT-ADD-ONS',
                    'doc_url'    => 'https://docs.yithemes.com/yith-woocommerce-product-add-ons/',
                ),
                'your_store_tools' => array(
                    'items' => array(
                        'request-quote' => array(
                            'name'        => 'Request a Quote',
                            'icon_url'    => YITH_WAPO_ASSETS_URL . '/img/plugins/request-quote.svg',
                            'url'         => '//yithemes.com/themes/plugins/yith-woocommerce-request-a-quote/',
                            //translators: [Your Store Tools TAB] Plugin description.
                            'description' => __( 'Hide prices and/or the "Add to cart" button and let your customers request a custom quote for every product.', 'yith-woocommerce-product-add-ons' ),
                            'is_active'   => defined( 'YITH_YWRAQ_PREMIUM' ),
                            'is_recommended' => true,
                        ),
                        'catalog-mode' => array(
                            'name'        => 'Catalog Mode',
                            'icon_url'    => YITH_WAPO_ASSETS_URL . '/img/plugins/catalog-mode.svg',
                            'url'         => '//yithemes.com/themes/plugins/yith-woocommerce-catalog-mode/',
                            //translators: [Your Store Tools TAB] Plugin description.
                            'description' => __( 'Use your shop as a catalog by hiding prices and/or the "Add to cart" button on product pages.', 'yith-woocommerce-product-add-ons' ),
                            'is_active'   => defined( 'YWCTM_PREMIUM' ),
                        ),
                        'gift-cards' => array(
                            'name'        => 'Gift Cards',
                            'icon_url'    => YITH_WAPO_ASSETS_URL . '/img/plugins/gift-card.svg',
                            'url'         => '//yithemes.com/themes/plugins/yith-woocommerce-gift-cards/',
                            //translators: [Your Store Tools TAB] Plugin description.
                            'description' => __( 'Sell gift cards in your shop to increase your earnings and attract new customers.', 'yith-woocommerce-product-add-ons' ),
                            'is_active'   => defined( 'YITH_YWGC_PREMIUM' ),
                            'is_recommended' => true,
                        ),
                        'ajax-filter' => array(
                            'name'        => 'Ajax Product Filter',
                            'icon_url'    => YITH_WAPO_ASSETS_URL . '/img/plugins/ajax-filter.svg',
                            'url'         => '//yithemes.com/themes/plugins/yith-woocommerce-ajax-product-filter/',
                            //translators: [Your Store Tools TAB] Plugin description.
                            'description' => __( 'Help your customers to easily find the products they are looking for and improve the user experience of your shop.', 'yith-woocommerce-product-add-ons' ),
                            'is_active'   => defined( 'YITH_WCAN_PREMIUM' ),
                        ),
                        'wishlist' => array(
                            'name'        => 'Wishlist',
                            'icon_url'    => YITH_WAPO_ASSETS_URL . '/img/plugins/wishlist.svg',
                            'url'         => '//yithemes.com/themes/plugins/yith-woocommerce-wishlist/',
                            //translators: [Your Store Tools TAB] Plugin description.
                            'description' => __( 'Allow your customers to create lists of products they want and share them with family and friends.', 'yith-woocommerce-product-add-ons' ),
                            'is_active'   => defined( 'YITH_WCWL_PREMIUM' ),
                        ),
                        'subscription' => array(
                            'name'        => 'Subscription',
                            'icon_url'    => YITH_WAPO_ASSETS_URL . '/img/plugins/subscription.svg',
                            'url'         => '//yithemes.com/themes/plugins/yith-woocommerce-subscription/',
                            //translators: [Your Store Tools TAB] Plugin description.
                            'description' => __( 'Sell products with a subscription plan in your e-commerce and loyalize your customers.', 'yith-woocommerce-product-add-ons' ),
                            'is_active'   => defined( 'YITH_YWSBS_PREMIUM' ),
                        ),
                        'membership' => array(
                            'name'        => 'Membership',
                            'icon_url'    => YITH_WAPO_ASSETS_URL . '/img/plugins/membership.svg',
                            'url'         => '//yithemes.com/themes/plugins/yith-woocommerce-membership/',
                            //translators: [Your Store Tools TAB] Plugin description.
                            'description' => __( 'Activate some sections of your e-commerce with restricted access so as to create memberships in your store.', 'yith-woocommerce-product-add-ons' ),
                            'is_active'   => defined( 'YITH_WCMBS_PREMIUM' ),
                        ),
                        'customize-account' => array(
                            'name'        => 'Customize My Account Page',
                            'icon_url'    => YITH_WAPO_ASSETS_URL . '/img/plugins/customize-my-account.svg',
                            'url'         => '//yithemes.com/themes/plugins/yith-woocommerce-customize-myaccount-page/',
                            //translators: [Your Store Tools TAB] Plugin description.
                            'description' => __( 'Customize the My Account page of your customers by creating custom sections with promotions and ad-hoc content based on your needs.', 'yith-woocommerce-product-add-ons' ),
                            'is_active'   => defined( 'YITH_WCMAP_PREMIUM' ),
                        ),
                    ),
                ),

            );

            $args = apply_filters( 'yith_wapo_register_panel_args', $args );

            $this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
        }


        /**
         * Add some tabs to the add-on editor.
         *
         * @param array $tabs The tabs of the add-on editor (Populate options, Options configuration, ...)
         * @return void
         */
        public function add_additional_tabs( $tabs ) {

            $display_tab = array(
                'display'           => array(
                    'id'    => 'display-settings',
                    'class' => '',
                    // translators: Display & Style tab of the add-on configuration
                    'label' => esc_html__( 'Display & Style', 'yith-woocommerce-product-add-ons' ),
                ),
            );

            $tabs = yith_wapo_array_insert_after( $tabs, 'advanced', $display_tab );

            return $tabs;


        }

		/**
		 * Show extra options before Add image option.
		 *
		 * @param array  $addon The add-on.
		 * @param int    $index The index of the current add-on.
		 * @param string $addon_type The add-on type.
		 * @return void
		 */
		public function show_additional_options_before_add_image( $addon, $index, $addon_type ) {

			if ( 'number' === $addon_type ) {
				yith_wapo_get_view(
					'extra-options/' . $addon_type . '.php',
					array(
						'addon'      => $addon,
						'addon_type' => $addon_type,
						'index'      => $index,
					),
                    defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''
				);
			}
		}


		/**
		 * New date rule template to add via JS with wp.template
		 *
		 * @return void
		 */
		public function yith_wapo_date_rule_template_js() {
			?>
			<script type="text/html" id="tmpl-yith-wapo-date-rule-template">
				<div class="rule" style="margin-bottom: 10px;">
					<div class="field what">
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'      => 'date-rule-what-{{data.addon_id}}-{{data.option_id}}',
								'name'    => 'options[date_rule_what][{{data.addon_id}}][]',
								'class'   => 'micro select_what wc-enhanced-select',
								'type'    => 'select',
								'value'   => 'days',
								'options' => array(
									'days'     => esc_html__( 'Days', 'yith-woocommerce-product-add-ons' ),
									'daysweek' => esc_html__( 'Days of the week', 'yith-woocommerce-product-add-ons' ),
									'months'   => esc_html__( 'Months', 'yith-woocommerce-product-add-ons' ),
									'years'    => esc_html__( 'Years', 'yith-woocommerce-product-add-ons' ),
								),
							),
							true
						);
						?>
					</div>

					<div class="field days">
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'    => 'date-rule-value-days-{{data.addon_id}}-{{data.option_id}}',
								'name'  => 'options[date_rule_value_days][{{data.addon_id}}][{{data.option_id}}]',
								'type'  => 'datepicker',
								'value' => '',
								'data'  => array(
									'date-format' => 'yy-mm-dd',
								),
							),
							true
						);
						?>
					</div>
					<div class="field daysweek" style="display: none";>
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'       => 'date-rule-value-daysweek-{{data.addon_id}}-{{data.option_id}}',
								'name'     => 'options[date_rule_value_daysweek][{{data.addon_id}}][{{data.option_id}}]',
								'type'     => 'select',
								'multiple' => true,
								'class'    => 'wc-enhanced-select',
								'options'  => array(
									// translators: [ADMIN] Option of add-on type Date
									'1' => esc_html__( 'Monday', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'2' => esc_html__( 'Tuesday', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'3' => esc_html__( 'Wednesday', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'4' => esc_html__( 'Thursday', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'5' => esc_html__( 'Friday', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'6' => esc_html__( 'Saturday', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'0' => esc_html__( 'Sunday', 'yith-woocommerce-product-add-ons' ),
								),
								'value'    => '',
							),
							true
						);
						?>
					</div>

					<div class="field months" style="display: none";>
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'       => 'date-rule-value-months-{{data.addon_id}}-{{data.option_id}}',
								'name'     => 'options[date_rule_value_months][{{data.addon_id}}][{{data.option_id}}]',
								'type'     => 'select',
								'multiple' => true,
								'class'    => 'wc-enhanced-select',
								'options'  => array(
									// translators: [ADMIN] Option of add-on type Date
									'1'  => esc_html__( 'January', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'2'  => esc_html__( 'February', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'3'  => esc_html__( 'March', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'4'  => esc_html__( 'April', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'5'  => esc_html__( 'May', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'6'  => esc_html__( 'June', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'7'  => esc_html__( 'July', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'8'  => esc_html__( 'August', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'9'  => esc_html__( 'September', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'10' => esc_html__( 'October', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'11' => esc_html__( 'November', 'yith-woocommerce-product-add-ons' ),
									// translators: [ADMIN] Option of add-on type Date
									'12' => esc_html__( 'December', 'yith-woocommerce-product-add-ons' ),
								),
								'value'    => '',
							),
							true
						);
						?>
					</div>

					<div class="field years" style="display: none";>
						<?php
						$years = array();
						$datey = gmdate( 'Y' );
						for ( $yy = $datey; $yy < $datey + 10; $yy++ ) {
							$years[ $yy ] = $yy;
						}
						yith_plugin_fw_get_field(
							array(
								'id'       => 'date-rule-value-years{{data.addon_id}}-{{data.option_id}}',
								'name'     => 'options[date_rule_value_years][{{data.addon_id}}][{{data.option_id}}]',
								'type'     => 'select',
								'multiple' => true,
								'class'    => 'wc-enhanced-select',
								'options'  => $years,
								'value'    => '',
							),
							true
						);
						?>
					</div>

					<img src="<?php echo esc_attr( YITH_WAPO_URL ); ?>/assets/img/delete.png" class="delete-rule">

					<div class="clear"></div>
				</div>
			</script>
			<?php
		}

		/**
		 * Add a custom message in the add-on field.
		 *
		 * @param array $field Array with the field info.
		 * @return void
		 */
		public function yith_plugin_fw_get_field_after_addons( $field ) {

			$custom_message = $field['custom_message'] ?? '';
			if ( ! empty( $custom_message ) ) {
				echo '<span class="description">' . esc_html( $custom_message ) . '</span>';
			}
		}

		/**
		 * Extra options for General options tab.
		 *
		 * @param array $general_settings The settings.
		 * @return array mixed
		 */
		public function add_extra_general_premium_options( $general_settings ) {

			$upload_options = array(
				'upload-options'            => array(
					'id'    => 'yith-wapo-upload-options',
					// translators: [ADMIN] General Settings tab option
					'title' => __( 'Upload options', 'yith-woocommerce-product-add-ons' ),
					'type'  => 'title',
					'desc'  => '',
				),
				'uploads-folder'            => array(
					'id'      => 'yith_wapo_uploads_folder',
					// translators: [ADMIN] General Settings tab option
					'name'    => __( 'Uploads folder', 'yith-woocommerce-product-add-ons' ),
					// translators: [ADMIN] General Settings tab option
					'desc'    => __( 'Enter the name of the folder used to storage the files uploaded by users.', 'yith-woocommerce-product-add-ons' ),
					'type'    => 'text',
					'default' => 'yith_advanced_product_options',
				),
				'upload-allowed-file-types' => array(
					'id'      => 'yith_wapo_upload_allowed_file_types',
					// translators: [ADMIN] General Settings tab option
					'name'    => __( 'Allowed file types', 'yith-woocommerce-product-add-ons' ),
					// translators: [ADMIN] General Settings tab option
					'desc'    => __( 'Enter which file types can be uploaded by users.', 'yith-woocommerce-product-add-ons' ) . '<br />'
						. __( 'Separate each file type with a comma. Example: .jpg, .png, .pdf', 'yith-woocommerce-product-add-ons' ),
					'type'    => 'text',
					'default' => '.jpg, .jpeg, .pdf, .png, .rar, .zip',
				),

				'upload-max-file-size'      => array(
					'id'      => 'yith_wapo_upload_max_file_size',
					// translators: [ADMIN] General Settings tab option
					'name'    => __( 'Max file size allowed (MB)', 'yith-woocommerce-product-add-ons' ),
					// translators: [ADMIN] General Settings tab option
					'desc'    => __( 'Enter the maximum allowed size for files uploaded by users.', 'yith-woocommerce-product-add-ons' ),
					'type'    => 'number',
					'default' => '5',
				),
				'attach-file-to-email'      => array(
					'id'        => 'yith_wapo_attach_file_to_email',
					// translators: [ADMIN] General Settings tab option
					'name'      => __( 'Attach uploaded files to order emails', 'yith-woocommerce-product-add-ons' ),
					// translators: [ADMIN] General Settings tab option
					'desc'      => __( 'Enable if you want to receive the files uploaded by users also in the orders emails.', 'yith-woocommerce-product-add-ons' ),
					'type'      => 'yith-field',
					'yith-type' => 'onoff',
					'default'   => 'no',
				),
				'upload-options-end'        => array(
					'id'   => 'yith-wapo-upload-option',
					'type' => 'sectionend',
				),

                // DATE OPTIONS
                'date-options'            => array(
                    'id'    => 'yith-wapo-date-options',
                    // translators: [ADMIN] General Settings tab option
                    'title' => __( 'Date options', 'yith-woocommerce-product-add-ons' ),
                    'type'  => 'title',
                    'desc'  => '',
                ),
                'date-24-hours-format'  => array(
                    'id'        => 'yith_wapo_enable_24_hour_format',
                    // translators: [ADMIN] General Settings tab option
                    'name'      => __( '24-hour time format', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] General Settings tab option
                    'desc'      => __( 'Enable to display the time picker in 24-hour format.', 'yith-woocommerce-product-add-ons' ),
                    'type'      => 'yith-field',
                    'yith-type' => 'onoff',
                    'default'   => 'no',
                ),
                'date-options-end'        => array(
                    'id'   => 'yith-wapo-date-option',
                    'type' => 'sectionend',
                ),
			);

			$general_settings['settings-general'] = isset( $general_settings['settings-general'] ) ? array_merge( $general_settings['settings-general'], $upload_options ) : $general_settings;

			return $general_settings;

		}

		/**
		 * Extra options for Style options tab.
		 *
		 * @param array $style The style settings.
		 * @return array mixed
		 */
		public function add_premium_panel_style_options( $style ) {

            $toggle_style = array(
                // Toggle.

                'toggle-section'            => array(
                    'id'    => 'yith_wapo_style_options',
                    // translators: [ADMIN] Style tab option
                    'title' => __( 'Toggle', 'yith-woocommerce-product-add-ons' ),
                    'type'  => 'title',
                    'desc'  => '',
                ),

                'show-in-toggle'             => array(
                    'id'        => 'yith_wapo_show_in_toggle',
                    // translators: [ADMIN] Style tab option
                    'name'      => __( 'Show options in toggle', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Style tab option
                    'desc'      => __( 'Enable to show the options blocks in toggle sections.', 'yith-woocommerce-product-add-ons' ),
                    'type'      => 'yith-field',
                    'yith-type' => 'onoff',
                    'default'   => 'no',
                ),

                'show-toggle-opened'         => array(
                    'id'        => 'yith_wapo_show_toggle_opened',
                    // translators: [ADMIN] Style tab option
                    'name'      => __( 'Show toggle opened by default', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Style tab option
                    'desc'      => __( 'Enable to show the toggle opened by default.', 'yith-woocommerce-product-add-ons' ),
                    'type'      => 'yith-field',
                    'yith-type' => 'onoff',
                    'default'   => 'no',
                    'deps'      => array(
                        'id'    => 'yith_wapo_show_in_toggle',
                        'value' => 'yes',
                        'type'  => 'fade',
                    ),
                ),

                'toggle-section-end'        => array(
                    'id'   => 'yith-wapo-style-options',
                    'type' => 'sectionend',
                ),
            );

			$premium_style = array(

				// Price box options.
				'price-box-section'          => array(
					'id'    => 'yith_wapo_style_price_box_options',
					// translators: [ADMIN] Style tab option
					'title' => __( 'Price box style', 'yith-woocommerce-product-add-ons' ),
					'type'  => 'title',
					'desc'  => '',
				),

				'style-price-box-colors'     => array(
					'id'           => 'yith_wapo_price_box_colors',
					// translators: [ADMIN] Style tab option
					'name'         => __( 'Price box colors', 'yith-woocommerce-product-add-ons' ),
					// translators: [ADMIN] Style tab option
					'desc'         => __( 'Set the color of the price box.', 'yith-woocommerce-product-add-ons' ),
					'type'         => 'yith-field',
					'yith-type'    => 'multi-colorpicker',
					'colorpickers' => array(
						array(
							'name'          => 'Text',
							'id'            => 'text',
							'default'       => '#474747',
							'alpha_enabled' => false,
						),
						array(
							'name'          => 'BACKGROUND',
							'id'            => 'background',
							'default'       => '#FFFFFF',
							'alpha_enabled' => false,
						),
					),
				),

				'price-box-section-end'      => array(
					'id'   => 'yith_wapo_style_price_box_options',
					'type' => 'sectionend',
				),

				// Color Swatches.

				'style-section-2'            => array(
					'id'    => 'yith_wapo_style_options',
					'title' => __( 'Color swatches', 'yith-woocommerce-product-add-ons' ),
					'type'  => 'title',
					'desc'  => '',
				),

				'style-color-swatch-style'   => array(
					'id'        => 'yith_wapo_style_color_swatch_style',
					// translators: [ADMIN] Style tab option
					'name'      => __( 'Color swatch style', 'yith-woocommerce-product-add-ons' ),
					// translators: [ADMIN] Style tab option
					'desc'      => __( 'Choose the style for color thumbnails.', 'yith-woocommerce-product-add-ons' ),
					'type'      => 'yith-field',
					'yith-type' => 'radio',
					'default'   => 'rounded',
					'options'   => array(
						// translators: [ADMIN] Style tab option
						'rounded' => __( 'Rounded', 'yith-woocommerce-product-add-ons' ),
						// translators: [ADMIN] Style tab option
						'square'  => __( 'Square', 'yith-woocommerce-product-add-ons' ),
					),
				),

				'style-color-swatch-size'    => array(
					'id'        => 'yith_wapo_style_color_swatch_size',
					// translators: [ADMIN] Style tab option
					'name'      => __( 'Color swatch size', 'yith-woocommerce-product-add-ons' ) . ' (px)',
					// translators: [ADMIN] Style tab option
					'desc'      => __( 'Set the size of the color thumbnails.', 'yith-woocommerce-product-add-ons' ),
					'type'      => 'yith-field',
					'yith-type' => 'number',
					'default'   => '40',
				),

				'style-section-2-end'        => array(
					'id'   => 'yith-wapo-style-options',
					'type' => 'sectionend',
				),

				// Label / Images.

				'style-section-3'            => array(
					'id'    => 'yith_wapo_style_options',
					// translators: [ADMIN] Style tab option
					'title' => __( 'Labels & Images', 'yith-woocommerce-product-add-ons' ),
					'type'  => 'title',
					'desc'  => '',
				),

				'style-images-position'      => array(
					'id'      => 'yith_wapo_style_images_position',
					// translators: [ADMIN] Style tab option
					'name'    => __( 'Image position', 'yith-woocommerce-product-add-ons' ),
					'type'    => 'select',
					'class'   => 'wc-enhanced-select',
					'default' => 'above',
					'options' => array(
						// translators: [ADMIN] Style tab option
						'above' => __( 'Above label', 'yith-woocommerce-product-add-ons' ),
						// translators: [ADMIN] Style tab option
						'under' => __( 'Under label', 'yith-woocommerce-product-add-ons' ),
						// translators: [ADMIN] Style tab option
						'left'  => __( 'Left side', 'yith-woocommerce-product-add-ons' ),
						// translators: [ADMIN] Style tab option
						'right' => __( 'Right side', 'yith-woocommerce-product-add-ons' ),
					),
				),

				'style-images-equal-height'  => array(
					'id'        => 'yith_wapo_style_images_equal_height',
					// translators: [ADMIN] Style tab option
					'name'      => __( 'Force image equal heights', 'yith-woocommerce-product-add-ons' ),
					'type'      => 'yith-field',
					'yith-type' => 'onoff',
					'default'   => 'no',
				),

				'style-images-height'        => array(
					'id'        => 'yith_wapo_style_images_height',
					// translators: [ADMIN] Style tab option
					'name'      => __( 'Image heights', 'yith-woocommerce-product-add-ons' ) . ' (px)',
					'type'      => 'yith-field',
					'yith-type' => 'number',
					'default'   => '',
					'deps'      => array(
						'id'    => 'yith_wapo_style_images_equal_height',
						'value' => 'yes',
						'type'  => 'fade',
					),
				),

				'style-label-position'       => array(
					'id'      => 'yith_wapo_style_label_position',
					// translators: [ADMIN] Style tab option
					'name'    => __( 'Label position', 'yith-woocommerce-product-add-ons' ),
					'type'    => 'select',
					'class'   => 'wc-enhanced-select',
					'default' => 'inside',
					'options' => array(
						// translators: [ADMIN] Style tab option
						'inside'  => __( 'Inside borders', 'yith-woocommerce-product-add-ons' ),
						// translators: [ADMIN] Style tab option
						'outside' => __( 'Outside borders', 'yith-woocommerce-product-add-ons' ),
					),
				),

				'style-description-position' => array(
					'id'      => 'yith_wapo_style_description_position',
					'name'    => __( 'Description position', 'yith-woocommerce-product-add-ons' ),
					'type'    => 'select',
					'class'   => 'wc-enhanced-select',
					'default' => 'outside',
					'options' => array(
						'inside'  => __( 'Inside borders', 'yith-woocommerce-product-add-ons' ),
						'outside' => __( 'Outside borders', 'yith-woocommerce-product-add-ons' ),
					),
				),

				'style-label-padding'        => array(
					'id'        => 'yith_wapo_style_label_padding',
					// translators: [ADMIN] Style tab option
					'name'      => __( 'Padding', 'yith-woocommerce-product-add-ons' ) . ' (px)',
					'type'      => 'yith-field',
					'yith-type' => 'dimensions',
					'default'   => array(
						'dimensions' => array(
							'top'    => 10,
							'right'  => 10,
							'bottom' => 10,
							'left'   => 10,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'units'     => array(),
				),

				'style-section-3-end'        => array(
					'id'   => 'yith-wapo-style-options',
					'type' => 'sectionend',
				),

				// Tooltip.

				'style-section-4'            => array(
					'id'    => 'yith_wapo_style_options',
					// translators: [ADMIN] Style tab option
					'title' => __( 'Tooltip', 'yith-woocommerce-product-add-ons' ),
					'type'  => 'title',
					'desc'  => '',
				),

				'show-tooltips'              => array(
					'id'        => 'yith_wapo_show_tooltips',
					// translators: [ADMIN] Style tab option
					'name'      => __( 'Show tooltips', 'yith-woocommerce-product-add-ons' ),
					// translators: [ADMIN] Style tab option
					'desc'      => __( 'Enable to show the tooltips in product options.', 'yith-woocommerce-product-add-ons' ),
					'type'      => 'yith-field',
					'yith-type' => 'onoff',
					'default'   => 'yes',
				),

				'tooltip-color'              => array(
					'id'           => 'yith_wapo_tooltip_color',
					// translators: [ADMIN] Style tab option
					'name'         => __( 'Tooltip color', 'yith-woocommerce-product-add-ons' ),
					// translators: [ADMIN] Style tab option
					'desc'         => __( 'Set the color for tooltips.', 'yith-woocommerce-product-add-ons' ),
					'type'         => 'yith-field',
					'yith-type'    => 'multi-colorpicker',
					'colorpickers' => array(
						array(
							// translators: [ADMIN] Style tab option
							'name'          => __( 'Background', 'yith-woocommerce-product-add-ons' ),
							'id'            => 'background',
							'default'       => '#03bfac',
							'alpha_enabled' => false,
						),
						array(
							// translators: [ADMIN] Style tab option
							'name'          => __( 'Text', 'yith-woocommerce-product-add-ons' ),
							'id'            => 'text',
							'default'       => '#ffffff',
							'alpha_enabled' => false,
						),
					),
					'deps'         => array(
						'id'    => 'yith_wapo_show_tooltips',
						'value' => 'yes',
						'type'  => 'fade',
					),
				),

				'tooltip-position'           => array(
					'id'        => 'yith_wapo_tooltip_position',
					// translators: [ADMIN] Style tab option
					'name'      => __( 'Tooltip position', 'yith-woocommerce-product-add-ons' ),
					// translators: [ADMIN] Style tab option
					'desc'      => __( 'Choose the default position for tooltips.', 'yith-woocommerce-product-add-ons' ),
					'type'      => 'yith-field',
					'yith-type' => 'radio',
					'default'   => 'top',
					'options'   => array(
						// translators: [ADMIN] Style tab option
						'top'    => __( 'Top', 'yith-woocommerce-product-add-ons' ),
						// translators: [ADMIN] Style tab option
						'bottom' => __( 'Bottom', 'yith-woocommerce-product-add-ons' ),
					),
					'deps'      => array(
						'id'    => 'yith_wapo_show_tooltips',
						'value' => 'yes',
						'type'  => 'fade',
					),
				),

				'style-section-4-end'        => array(
					'id'   => 'yith-wapo-style-options',
					'type' => 'sectionend',
				),

				// Uploads.

				'style-section-5'            => array(
					'id'    => 'yith_wapo_style_options',
					// translators: [ADMIN] Style tab option
					'title' => __( 'Upload file', 'yith-woocommerce-product-add-ons' ),
					'type'  => 'title',
					'desc'  => '',
				),

				'uploads-text-to-show'       => array(
					'id'      => 'yith_wapo_uploads_text_to_show',
					// translators: [ADMIN] Style tab option
					'name'    => __( 'Text to show', 'yith-woocommerce-product-add-ons' ),
					'type'    => 'text',
					// translators: [ADMIN] Style tab option
					'default' => __( 'Drop files to upload or', 'yith-woocommerce-product-add-ons' ),
				),
				'uploads-link-to-show'       => array(
					'id'      => 'yith_wapo_uploads_link_to_show',
					// translators: [ADMIN] Style tab option
					'name'    => __( 'Link to show', 'yith-woocommerce-product-add-ons' ),
					'type'    => 'select',
					'class'   => 'wc-enhanced-select',
					'default' => 'button',
					'options' => array(
						// translators: [ADMIN] Style tab option
						'text'   => __( 'Textual "browse"', 'yith-woocommerce-product-add-ons' ),
						// translators: [ADMIN] Style tab option
						'button' => __( 'Button "upload"', 'yith-woocommerce-product-add-ons' ),
					),
				),

				'uploads-file-colors'        => array(
					'id'           => 'yith_wapo_upload_file_colors',
					// translators: [ADMIN] Style tab option
					'name'         => __( 'Colors', 'yith-woocommerce-product-add-ons' ),
					'type'         => 'yith-field',
					'yith-type'    => 'multi-colorpicker',
					'colorpickers' => array(
						array(
							// translators: [ADMIN] Style tab option
							'name'          => __( 'Background', 'yith-woocommerce-product-add-ons' ),
							'id'            => 'background',
							'default'       => '#f3f3f3',
							'alpha_enabled' => false,
						),
						array(
							// translators: [ADMIN] Style tab option
							'name'          => __( 'Border', 'yith-woocommerce-product-add-ons' ),
							'id'            => 'border',
							'default'       => '#c4c4c4',
							'alpha_enabled' => false,
						),
					),
					'deps'         => array(
						'id'    => 'yith_wapo_show_tooltips',
						'value' => 'yes',
						'type'  => 'fade',
					),
				),

				'style-section-5-end'        => array(
					'id'   => 'yith-wapo-style-options',
					'type' => 'sectionend',
				),
			);

			$required_array = array(
				'style-required-option-color' => array(
					'id'            => 'yith_wapo_required_option_color',
					// translators: [ADMIN] Style tab option
					'title'         => __( 'Required option color', 'yith-woocommerce-product-add-ons' ),
					// translators: [ADMIN] Style tab option
					'desc'          => __( 'Set the color to use for the required option message.', 'yith-woocommerce-product-add-ons' ),
					'type'          => 'yith-field',
					'yith-type'     => 'colorpicker',
					'default'       => '#AF2323',
					'alpha_enabled' => false,
				),

				'style-required-option-text'  => array(
					'id'        => 'yith_wapo_required_option_text',
					// translators: [ADMIN] Style tab option
					'name'      => __( 'Required option text', 'yith-woocommerce-product-add-ons' ) . ' (px)',
					// translators: [ADMIN] Style tab option
					'desc'      => __( 'Enter the text to identify a required option.', 'yith-woocommerce-product-add-ons' ),
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => __( 'This option is required.', 'yith-woocommerce-product-add-ons' ),
				),
			);

            $style['style'] = isset( $style['style'] ) ? array_merge( $style['style'], $premium_style ) : $style['style'];
            $style['style'] = yith_wapo_array_insert_after( $style['style'], 'style-description-font-size', $required_array );
            $style['style'] = yith_wapo_array_insert_after( $style['style'], 'style-section-end', $toggle_style );

			return $style;
		}

		/**
		 * Manage the re-stock on the product type addons refund
		 *
		 * @param int $order_id order ID.
		 * @param int $refund_id refund ID.
		 * @return void
		 */
		public function manage_refunded_product_type_addons( $order_id, $refund_id ) {

			$refund_order = wc_get_order( $refund_id );
			$order        = wc_get_order( $order_id );

			$refunded_items = $refund_order->get_items();

			if ( empty( $refunded_items ) ) {
				$refunded_items = $order->get_items();
			}

			foreach ( $refunded_items as $item_id => $item ) {

				$main_item_id = $item->get_meta( '_refunded_item_id', true );
				$item_id      = ! empty( $main_item_id ) ? $main_item_id : $item_id;

				$meta_data     = wc_get_order_item_meta( $item_id, '_ywapo_meta_data', true );
				$quantity_data = wc_get_order_item_meta( $item_id, '_ywapo_product_addon_qty', true );

				if ( $meta_data && is_array( $meta_data ) ) {
					foreach ( $meta_data as $index => $option ) {
						foreach ( $option as $key => $value ) {
							if ( $key && '' !== $value ) {
								if ( is_string( $value ) ) {
									$value   = stripslashes( $value );
									$explode = explode( '-', $value );

									if ( isset( $explode[0] ) && 'product' === $explode[0] ) {
										$product_id   = $explode[1];
										$product      = wc_get_product( $product_id );
										$product_name = $product instanceof WC_Product ? $product->get_title() : '';
										$quantity     = $quantity_data[ $key ];
										wc_update_product_stock( $product_id, $quantity, 'increase' );
										// translators: [ADMIN] Message when restock the Product quantity due to order cancelled
										$order->add_order_note( __( 'Stock levels increased for add-ons type product:', 'yith-woocommerce-product-add-ons' ) . ' ' . $product_name );
									}
								}
							}
						}
					}
				}
			}
		}
		/**
		 * Manage the re-stock on the product type addons when order is cancelled
		 *
		 * @param WC_Order $order Order.
		 * @return void
		 */
		public function restore_addons_type_product_stock( $order ) {

			if ( $order && $order instanceof WC_Order ) {
				$items = $order->get_items();
				foreach ( $items as $item_id => $item ) {
					$meta_data = wc_get_order_item_meta( $item_id, '_ywapo_meta_data', true );
					if ( $meta_data && is_array( $meta_data ) ) {
						foreach ( $meta_data as $index => $option ) {
							foreach ( $option as $key => $value ) {
								if ( $key && '' !== $value ) {
									if ( is_string( $value ) ) {
										$value   = stripslashes( $value );
										$explode = explode( '-', $value );

										if ( isset( $explode[0] ) && 'product' === $explode[0] ) {
											$quantity_data = wc_get_order_item_meta( $item_id, '_ywapo_product_addon_qty', true );
											$product_id    = $explode[1];
											$product       = wc_get_product( $product_id );
											$product_name  = $product instanceof WC_Product ? $product->get_title() : '';
											$quantity      = $quantity_data[ $key ];
											$stock         = wc_update_product_stock( $product_id, $quantity, 'increase' );
											// translators: [ADMIN] Message when restock the Product quantity due to order cancelled
											$order->add_order_note( __( 'Stock levels increased for add-ons type product:', 'yith-woocommerce-product-add-ons' ) . ' ' . $product_name );
										}
									}
								}
							}
						}
					}
				}
			}
		}
		/**
		 * Manage the reduce on the product type addons when order is completed
		 *
		 * @param WC_Order $order Order.
		 * @return void
		 */
		public function reduce_addons_type_product_stock( $order ) {
			if ( $order && $order instanceof WC_Order ) {
				$items = $order->get_items();
				foreach ( $items as $item_id => $item ) {
					$meta_data = wc_get_order_item_meta( $item_id, '_ywapo_meta_data', true );
					if ( $meta_data && is_array( $meta_data ) ) {
						foreach ( $meta_data as $index => $option ) {
							foreach ( $option as $key => $value ) {
								if ( $key && '' !== $value ) {
									if ( is_string( $value ) ) {
										$value   = stripslashes( $value );
										$explode = explode( '-', $value );

										if ( isset( $explode[0] ) && 'product' === $explode[0] ) {
											$quantity_data = wc_get_order_item_meta( $item_id, '_ywapo_product_addon_qty', true );
											$product_id    = $explode[1];
											$quantity      = $quantity_data[ $key ];
											$stock         = wc_update_product_stock( $product_id, $quantity, 'decrease' );
											// translators: [ADMIN] Message added to order notes when add-on type Product has stock
											$order->add_order_note( __( 'Stock levels reduced for addons type product:', 'yith-woocommerce-product-add-ons' ) . ' ' . $product_id );
										}
									}
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Adding admin panel tabs.
		 *
		 * @param array  $admin_tabs The admin tabs array.
		 * @param string $capability The capability of the user.
		 * @return mixed
		 */
		public function yith_wapo_admin_panel_tabs_premium( $admin_tabs, $capability ) {

            if ( isset( $_REQUEST['tab'] ) && 'debug' === $_REQUEST['tab'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $admin_tabs['debug'] = array(
                    // translators: [ADMIN] Options tab.
                    'title'       => __( 'Debug', 'yith-woocommerce-product-add-ons' ),
                    'icon'        => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"></path>
</svg>',
                    'description' => 'The debug tab, used for administration purposes only',
                );
            }

			return $admin_tabs;

		}

        /**
         * Show debug tab
         *
         * @return void
         */
        public function show_debug_tab() {
            yith_wapo_get_view(
                'debug/debug.php',
                array(),
                defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''
            );
        }


	}
}
