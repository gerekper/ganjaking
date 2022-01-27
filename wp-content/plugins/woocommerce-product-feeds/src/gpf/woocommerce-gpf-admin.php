<?php

/**
 * Admin class.
 *
 * Handles the display of the settings page, product meta boxes, and category meta.
 */
class WoocommerceGpfAdmin {

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $common;

	/**
	 * @var WoocommerceGpfTemplateLoader
	 */
	protected $template_loader;

	/**
	 * @var WoocommerceGpfCacheStatus
	 */
	protected $cache_status;

	/**
	 * @var WoocommerceProductFeedsFeedImageManager
	 */
	protected $feed_image_manager;

	/**
	 * @var WoocommerceProductFeedsWoocommerceAdminIntegration
	 */
	protected $wc_admin_integration;

	/**
	 * @var WoocommerceProductFeedsFeedConfigRepository
	 */
	protected $feed_config_repository;

	/**
	 * @var WoocommerceProductFeedsFeedManager
	 */
	protected $feed_manager;

	/**
	 * @var array
	 */
	private $settings = array();

	/**
	 * @var array
	 */
	private $product_fields = array();

	/**
	 * @var WoocommerceGpfCache
	 */
	private $cache;

	/**
	 * Base directory of the plugin.
	 * @var string
	 */
	private $base_dir;

	/**
	 * WoocommerceGpfAdmin constructor.
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WoocommerceGpfTemplateLoader $woocommerce_gpf_template_loader
	 * @param WoocommerceGpfCache $woocommerce_gpf_cache
	 * @param WoocommerceGpfCacheStatus $woocommerce_gpf_cache_status
	 * @param WoocommerceProductFeedsFeedImageManager $woocommerce_product_feeds_feed_image_manager
	 * @param WoocommerceProductFeedsWoocommerceAdminIntegration $woocommerce_product_feeds_woocommerce_admin_integration
	 * @param WoocommerceProductFeedsFeedConfigRepository $feed_config_repository
	 * @param WoocommerceProductFeedsFeedManager $feed_manager
	 */
	public function __construct(
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfTemplateLoader $woocommerce_gpf_template_loader,
		WoocommerceGpfCache $woocommerce_gpf_cache,
		WoocommerceGpfCacheStatus $woocommerce_gpf_cache_status,
		WoocommerceProductFeedsFeedImageManager $woocommerce_product_feeds_feed_image_manager,
		WoocommerceProductFeedsWoocommerceAdminIntegration $woocommerce_product_feeds_woocommerce_admin_integration,
		WoocommerceProductFeedsFeedConfigRepository $feed_config_repository,
		WoocommerceProductFeedsFeedManager $feed_manager
	) {
		$this->common                 = $woocommerce_gpf_common;
		$this->template_loader        = $woocommerce_gpf_template_loader;
		$this->cache                  = $woocommerce_gpf_cache;
		$this->cache_status           = $woocommerce_gpf_cache_status;
		$this->feed_image_manager     = $woocommerce_product_feeds_feed_image_manager;
		$this->wc_admin_integration   = $woocommerce_product_feeds_woocommerce_admin_integration;
		$this->feed_config_repository = $feed_config_repository;
		$this->feed_manager           = $feed_manager;
	}

	/**
	 * Set up the relevant hooks actions, and load the settings
	 *
	 * @access public
	 */
	public function initialise() {

		$this->settings = get_option( 'woocommerce_gpf_config', array() );
		$this->base_dir = dirname( dirname( dirname( __FILE__ ) ) );

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ), 11 );
		add_action( 'admin_print_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_print_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'woocommerce_init', array( $this->wc_admin_integration, 'initialise' ) );

		// Extend category admin page.
		add_action( 'product_cat_add_form_fields', array( $this, 'category_meta_box' ), 99, 2 ); // After left-col
		add_action( 'product_cat_edit_form_fields', array( $this, 'category_meta_box' ), 99, 2 ); // After left-col
		add_action( 'created_product_cat', array( $this, 'save_category' ), 15, 2 ); //After created
		add_action( 'edited_product_cat', array( $this, 'save_category' ), 15, 2 ); //After saved

		// Variation form input.
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variation_input_fields' ), 90, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation' ), 10, 2 );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_woocommerce_settings_tab' ), 99 );
		add_action( 'woocommerce_settings_gpf', array( $this, 'config_page' ) );
		add_action( 'woocommerce_update_options_gpf', array( $this, 'save_settings' ) );

		$this->feed_image_manager->initialise();
		$this->feed_manager->initialise();
	}

	/**
	 * Handle ajax callbacks for Google and bing category lookups
	 * Set up localisation
	 *
	 * @access public
	 */
	public function init() {

		// Add a Settings link to the plugin page.
		$plugin_file = basename( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/woocommerce-gpf.php';
		add_filter( 'plugin_action_links_' . $plugin_file, array( $this, 'add_settings_link' ), 11 );

		// Handle ajax requests for the google taxonomy search
		if ( isset( $_GET['woocommerce_gpf_search'] ) ) {
			$this->ajax_handler( $_GET['query'] );
			exit();
		}

		// Handle ajax requests for the bing taxonomy search
		if ( isset( $_GET['woocommerce_gpf_bing_search'] ) ) {
			$this->bing_ajax_handler( $_GET['query'] );
			exit();
		}

		// Read in the field data.
		$this->product_fields = apply_filters( 'woocommerce_gpf_product_fields', $this->common->product_fields );

		// Set up i18n for the admin screens.
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce_gpf' );
		load_textdomain( 'woocommerce_gpf', WP_LANG_DIR . '/woocommerce-google-product-feed/woocommerce_gpf-' . $locale . '.mo' );
		load_plugin_textdomain( 'woocommerce_gpf', false, $this->base_dir . '/languages/' );
	}

	/**
	 * Add a "Settings" link next to the plugin on the Plugins page.
	 *
	 * @param array $links The existing plugin links.
	 *
	 * @return  array          The revised list of plugin links.
	 */
	public function add_settings_link( $links ) {
		$settings_url = add_query_arg(
			[
				'page' => 'wc-settings',
				'tab'  => 'gpf',
			],
			admin_url( 'admin.php' )
		);
		$links[]      = sprintf(
			'<a href="%s">%s</a>',
			esc_attr( $settings_url ),
			__( 'Settings', 'woocommerce_gpf' )
		);
		$links[]      = sprintf(
			'<a href="https://woocommerce.com/document/google-product-feed-setting-up-your-feed-google-merchant-centre/">%s</a>',
			__( 'Set up guide', 'woocommerce_gpf' )
		);
		$links[]      = sprintf(
			'<a href="https://woocommerce.com/document/google-product-feed-troubleshooting/">%s</a>',
			__( 'Troubleshooting', 'woocommerce_gpf' )
		);

		return $links;
	}

	/**
	 * Extend Product Edit Page
	 *
	 * @access public
	 */
	public function admin_init() {
		$this->maybe_refresh_google_taxonomy();
		add_action( 'save_post_product', array( $this, 'save_product' ) );
		if ( isset( $this->settings['product_fields'] ) && count( $this->settings['product_fields'] ) ) {
			add_meta_box(
				'woocommerce-gpf-product-fields',
				__( 'Product Feed Information', 'woocommerce_gpf' ),
				array( $this, 'product_meta_box' ),
				'product',
				'advanced',
				'high'
			);
		}
		if ( isset( $_GET['gpf_action'] ) && 'rebuild_cache' === $_GET['gpf_action'] ) {
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'gpf_rebuild_cache' ) ) {
				$this->cache->flush_all();
			}
			wp_safe_redirect(
				add_query_arg(
					array(
						'page' => 'wc-settings',
						'tab'  => 'gpf',
					),
					admin_url( 'admin.php' )
				)
			);
			exit;
		}
		if ( isset( $_GET['gpf_action'] ) && 'refresh_custom_fields' === $_GET['gpf_action'] ) {
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'gpf_refresh_custom_fields' ) ) {
				delete_transient( 'woocommerce_gpf_meta_prepopulate_options' );
				wp_safe_redirect(
					add_query_arg(
						array(
							'page' => 'wc-settings',
							'tab'  => 'gpf',
						),
						admin_url( 'admin.php' )
					)
				);
				exit;
			} else {
				wp_die( 'Invalid request' );
			}
		}
	}

	/**
	 * Handle ajax requests for the google taxonomy search. Returns a JSON encoded list of matches
	 * and terminates execution.
	 *
	 * @access public
	 *
	 * @param string $query The user input to search for
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	private function ajax_handler( $query ) {

		global $wpdb, $table_prefix;

		$locales = $this->get_google_taxonomy_locales();
		$sql     = "SELECT taxonomy_term
                          FROM ${table_prefix}woocommerce_gpf_google_taxonomy
                         WHERE search_term LIKE %s";

		$search_term = '%' . strtolower( $query ) . '%';
		if ( count( $locales ) > 1 ) {
			$sql .= ' AND locale in (' . implode( ',', array_fill( 0, count( $locales ), '%s' ) ) . ')';

			$query_arguments = $locales;
			array_unshift( $query_arguments, $search_term );

			$prepared_query = $wpdb->prepare( $sql, $query_arguments );
		} else {
			$sql .= ' AND locale = %s';

			$prepared_query = $wpdb->prepare( $sql, [ $search_term, $locales[0] ?? '' ] );
		}
		$results     = $wpdb->get_results( $prepared_query );
		$suggestions = array();
		foreach ( $results as $match ) {
			$suggestions[] = $match->taxonomy_term;
		}
		$results = array(
			'query'       => $query,
			'suggestions' => $suggestions,
			'data'        => $suggestions,
		);
		echo wp_json_encode( $results );
		exit();
	}

	/**
	 * Handle ajax requests for the Bing taxonomy search. eturns a JSON encoded list of matches
	 * and terminates execution.
	 *
	 * @access public
	 *
	 * @param string $query The user input to search for
	 */
	private function bing_ajax_handler( $query ) {
		$taxonomy = array(
			'Arts & Crafts',
			'Baby & Nursery',
			'Beauty & Fragrance',
			'Books & Magazines',
			'Cameras & Optics',
			'Car & Garage',
			'Clothing & Shoes',
			'Collectibles & Memorabilia',
			'Computing',
			'Electronics',
			'Flowers',
			'Gourmet Food & Chocolate',
			'Health & Wellness',
			'Home Furnishings',
			'Jewelry & Watches',
			'Kitchen & Housewares',
			'Lawn & Garden',
			'Miscellaneous',
			'Movies',
			'Music',
			'Musical Instruments',
			'Office Products',
			'Pet Supplies',
			'Software',
			'Sports & Outdoors',
			'Tools & Hardware',
			'Toys',
			'Travel',
			'Vehicles',
			'Video Games',
		);

		$suggestions = array();
		foreach ( $taxonomy as $b_cat ) {
			if ( stristr( $b_cat, $query ) ) {
				$suggestions[] = $b_cat;
			}
		}
		$results = array(
			'query'       => $query,
			'suggestions' => $suggestions,
			'data'        => $suggestions,
		);
		echo wp_json_encode( $results );
		exit();
	}

	/*
	 * Enqueue CSS needed for product pages
	 *
	 * @access public
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'woocommerce_gpf_admin',
			plugins_url( basename( $this->base_dir ) . '/css/woocommerce-gpf.css' ),
			[],
			WOOCOMMERCE_GPF_VERSION
		);
		wp_enqueue_style(
			'wooautocomplete',
			plugins_url( basename( $this->base_dir ) . '/js/jquery.autocomplete.css' ),
			[],
			WOOCOMMERCE_GPF_VERSION
		);
	}

	/**
	 * Enqueue javascript for product_type / google_product_category selector
	 *
	 * @access public
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'wooautocomplete',
			plugins_url( basename( $this->base_dir ) ) . '/js/jquery.autocomplete.js',
			[ 'jquery', 'jquery-ui-core' ],
			WOOCOMMERCE_GPF_VERSION,
			true
		);
		wp_enqueue_script(
			'woocommerce_gpf',
			plugins_url( basename( $this->base_dir ) ) . '/js/woocommerce-gpf.js',
			[ 'jquery', 'jquery-ui-datepicker' ],
			WOOCOMMERCE_GPF_VERSION,
			true
		);
	}

	/**
	 * Render the form to allow users to set defaults per-category
	 *
	 * @access public
	 *
	 * @param unknown $termortax
	 * @param unknown $taxonomy (optional)
	 */
	public function category_meta_box( $termortax, $taxonomy = null ) {
		// So we can use the same function for add and edit forms
		if ( is_null( $taxonomy ) ) {
			$taxonomy = $termortax;
			$term     = null;
		} else {
			$term = $termortax;
		}
		if ( $term ) {
			$current_data = get_term_meta( $term->term_id, '_woocommerce_gpf_data', true );
		} else {
			$current_data = array();
		}
		$this->template_loader->output_template_with_variables(
			'woo-gpf',
			'meta-edit-intro',
			[ 'loop_idx' => '' ]
		);

		foreach ( $this->product_fields as $key => $fieldinfo ) {
			// Skip if not enabled & not mandatory.
			if ( ! isset( $this->settings['product_fields'][ $key ] ) &&
				 ( ! isset( $this->product_fields[ $key ]['mandatory'] ) || ! $this->product_fields[ $key ]['mandatory'] )
			) {
				continue;
			}
			// Skip if not to be shown on product pages.
			if ( isset( $this->product_fields[ $key ]['skip_on_category_pages'] ) &&
				 $this->product_fields[ $key ]['skip_on_category_pages']
			) {
				continue;
			}

			$header_vars = array();
			$def_vars    = array();
			$row_vars    = array();
			$variables   = array();

			$header_vars['row_title'] = esc_html( $fieldinfo['desc'] );
			$header_vars['key']       = esc_html( $key );

			$header_vars['default_text'] = '';
			$placeholder                 = '';
			if ( isset( $fieldinfo['can_default'] ) && ! empty( $this->settings['product_defaults'][ $key ] ) ) {
				$header_vars['default_text'] .= '<span class="woocommerce_gpf_default_label">(' .
												__( 'Default: ', 'woocommerce_gpf' ) .
												esc_html( $this->settings['product_defaults'][ $key ] ) .
												')</span>';
				$placeholder                  = __( 'Use default', 'woo_gpf' );
			}
			$row_vars['header_content'] = $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'meta-field-row-header',
				$header_vars
			);

			$current_value            = isset( $current_data[ $key ] ) ? $current_data[ $key ] : '';
			$def_vars['defaultinput'] = $this->render_field_default_input( $key, 'category', $current_value, $placeholder, null );
			$def_vars['key']          = $key;
			$variables['defaults']    = $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'meta-field-row-defaults',
				$def_vars
			);
			$row_vars['data_content'] = $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'meta-field-row-data',
				$variables
			);
			$this->template_loader->output_template_with_variables(
				'woo-gpf',
				'meta-field-row',
				$row_vars
			);
		}
	}

	/**
	 * Store the per-category defaults
	 *
	 * @access public
	 *
	 * @param unknown $term_id
	 */
	public function save_category( $term_id ) {
		if ( isset( $_POST['_woocommerce_gpf_data'] ) ) {
			foreach ( $_POST['_woocommerce_gpf_data'] as $key => $value ) {
				$_POST['_woocommerce_gpf_data'][ $key ] = stripslashes_deep( $value );
			}
			update_term_meta( $term_id, '_woocommerce_gpf_data', $_POST['_woocommerce_gpf_data'] );
		}
	}

	/**
	 * @param $loop_idx
	 * @param $variation_data
	 * @param $variation
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function variation_input_fields( $loop_idx, $variation_data, $variation ) {

		echo '<div class="wc_gpf_metabox closed">';
		echo '<h2><strong>';
		echo __( 'Product Feed Information', 'woocommerce_gpf' );
		echo '</strong><div class="handlediv" aria-label="Click to toggle"></div>';
		echo '</h2>';
		echo '<div class="wc_gpf_metabox_content" style="display:none;">';
		echo '<p>' . __( 'Set values here if you want to override the information for this specific variation. If information should apply to all variations, then set it against the main product.', 'woocommerce_gpf' ) . '</p>';
		$current_data     = get_post_meta( $variation->ID, '_woocommerce_gpf_data', true );
		$product_defaults = $this->common->get_defaults_for_product( $variation->ID, 'all' );

		$this->render_exclude_product(
			'exclude_product',
			'variation',
			! empty( $current_data['exclude_product'] ) ? true : false,
			null,
			$loop_idx
		);
		$style = ! empty( $current_data['exclude_product'] ) ?
			'style="display: none;"' :
			'';

		$this->template_loader->output_template_with_variables(
			'woo-gpf',
			'product-meta-edit-intro',
			[
				'loop_idx' => $loop_idx,
				'style'    => $style,
			]
		);
		foreach ( $this->product_fields as $key => $fieldinfo ) {
			if ( ! isset( $this->settings['product_fields'][ $key ] ) || 'description' === $key ) {
				continue;
			}
			$variables                      = $this->default_field_variables( $key, $loop_idx );
			$variables['field_description'] = esc_html( $fieldinfo['desc'] );
			$variables['field_defaults']    = '<br>';
			$placeholder                    = '';
			if ( isset( $fieldinfo['can_prepopulate'] ) && ! empty( $this->settings['product_prepopulate'][ $key ] ) ) {
				$prepopulate_vars             = array();
				$prepopulate_vars['label']    = $this->get_prepopulate_label( $this->settings['product_prepopulate'][ $key ] );
				$variables['field_defaults'] .= $this->template_loader->get_template_with_variables(
					'woo-gpf',
					'product-meta-prepopulate-text',
					$prepopulate_vars
				);
			}
			if ( isset( $fieldinfo['can_default'] ) && ! empty( $product_defaults[ $key ] ) ) {
				$variables['field_defaults'] .= $this->template_loader->get_template_with_variables(
					'woo-gpf',
					'variation-meta-default-text',
					array(
						'default' => sprintf(
							'Defaults to value from main product, or &quot;%s&quot;.',
							esc_html( $product_defaults[ $key ] )
						),
					)
				);
				$placeholder                  = __( 'Use default', 'woo_gpf' );
			}
			if ( ! isset( $fieldinfo['callback'] ) || ! is_callable( array( &$this, $fieldinfo['callback'] ) ) ) {
				$current_value            = ! empty( $current_data[ $key ] ) ? $current_data[ $key ] : '';
				$variables['field_input'] = $this->render_field_default_input(
					$key,
					'variation',
					$current_value,
					$placeholder,
					$loop_idx
				);
			} else {
				if ( isset( $current_data[ $key ] ) ) {
					$variables['field_input'] = call_user_func(
						array( $this, $fieldinfo['callback'] ),
						$key,
						'variation',
						$current_data[ $key ],
						$placeholder,
						$loop_idx
					);
				} else {
					$variables['field_input'] = call_user_func(
						array( $this, $fieldinfo['callback'] ),
						$key,
						'variation',
						null,
						$placeholder,
						$loop_idx
					);
				}
			}
			$this->template_loader->output_template_with_variables( 'woo-gpf', 'product-meta-field-row', $variables );
		}
		$this->template_loader->output_template_with_variables( 'woo-gpf', 'product-meta-edit-footer', array() );
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Meta box on product pages for setting per-product information
	 *
	 * @access public
	 */
	public function product_meta_box( $post ) {
		$current_data     = get_post_meta( $post->ID, '_woocommerce_gpf_data', true );
		$product_defaults = $this->common->get_defaults_for_product( $post->ID, 'all' );

		$this->render_exclude_product(
			'exclude_product',
			'product',
			! empty( $current_data['exclude_product'] ) ? true : false
		);

		$this->feed_image_manager->render_summary( $post );

		$this->template_loader->output_template_with_variables(
			'woo-gpf',
			'product-meta-edit-intro',
			[
				'loop_idx' => '',
				'style'    => '',
			]
		);
		foreach ( $this->product_fields as $key => $fieldinfo ) {
			// Skip if not enabled & not mandatory.
			if ( ! isset( $this->settings['product_fields'][ $key ] ) &&
				 ( ! isset( $this->product_fields[ $key ]['mandatory'] ) || ! $this->product_fields[ $key ]['mandatory'] )
			) {
				continue;
			}
			// Skip if not to be shown on product pages.
			if ( isset( $this->product_fields[ $key ]['skip_on_product_pages'] ) &&
				 $this->product_fields[ $key ]['skip_on_product_pages']
			) {
				continue;
			}

			$variables                      = $this->default_field_variables( $key );
			$variables['field_description'] = esc_html( $fieldinfo['desc'] );
			$variables['field_defaults']    = '';
			$placeholder                    = '';
			if ( isset( $fieldinfo['can_prepopulate'] ) && ! empty( $this->settings['product_prepopulate'][ $key ] ) ) {
				$prepopulate_vars          = array();
				$prepopulate_vars['label'] = $this->get_prepopulate_label( $this->settings['product_prepopulate'][ $key ] );
				if ( ! empty( $prepopulate_vars['label'] ) ) {
					$variables['field_defaults'] .= $this->template_loader->get_template_with_variables(
						'woo-gpf',
						'product-meta-prepopulate-text',
						$prepopulate_vars
					);
				}
			}
			if ( isset( $fieldinfo['can_default'] ) && ! empty( $product_defaults[ $key ] ) ) {
				$variables['field_defaults'] .= $this->template_loader->get_template_with_variables(
					'woo-gpf',
					'product-meta-default-text',
					array(
						'default' => '(' . __( 'Default: ', 'woocommerce_gpf' ) . esc_html( $product_defaults[ $key ] ) . ')',
					)
				);
				$placeholder                  = __( 'Use default', 'woo_gpf' );
			}
			if ( ! isset( $fieldinfo['callback'] ) || ! is_callable( array( &$this, $fieldinfo['callback'] ) ) ) {
				$current_value            = isset( $current_data[ $key ] ) ?
					$current_data[ $key ] :
					'';
				$variables['field_input'] = $this->render_field_default_input(
					$key,
					'product',
					$current_value,
					$placeholder,
					null
				);
			} else {
				$current_value            = isset( $current_data[ $key ] ) ?
					$current_data[ $key ] :
					null;
				$variables['field_input'] = call_user_func(
					array( $this, $fieldinfo['callback'] ),
					$key,
					'product',
					$current_value,
					$placeholder,
					null
				);
			}
			$this->template_loader->output_template_with_variables( 'woo-gpf', 'product-meta-field-row', $variables );
		}
		$this->template_loader->output_template_with_variables( 'woo-gpf', 'product-meta-edit-footer', array() );
	}

	/**
	 * Store the per-product meta information.
	 *
	 * @access public
	 *
	 * @param unknown $product_id
	 */
	public function save_product( $product_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( empty( $_POST['_woocommerce_gpf_data'] ) ) {
			return;
		}
		$current_data = get_post_meta( $product_id, '_woocommerce_gpf_data', true );
		if ( ! $current_data ) {
			$current_data = array();
		}
		$post_data = $_POST['_woocommerce_gpf_data'];
		// Remove entries that are blanked out
		foreach ( $post_data as $key => $value ) {
			if ( is_numeric( $key ) ) {
				// This is the variation data, ignore it.
				unset( $post_data[ $key ] );
				continue;
			}
			if ( empty( $value ) && '0' !== $value ) {
				unset( $post_data[ $key ] );
				if ( isset( $current_data[ $key ] ) ) {
					unset( $current_data[ $key ] );
				}
			} else {
				if ( is_array( $value ) ) {
					$post_data[ $key ] = stripslashes_deep( $value );
				} else {
					$post_data[ $key ] = stripslashes( $value );
				}
			}
		}
		// Including missing checkboxes
		if ( ! isset( $post_data['exclude_product'] ) ) {
			unset( $current_data['exclude_product'] );
		}
		if ( ! isset( $post_data['is_bundle'] ) ) {
			unset( $current_data['is_bundle'] );
		}
		$current_data = array_merge( $current_data, $post_data );
		// Fix legacy data which may contain variation data.
		foreach ( array_keys( $current_data ) as $key ) {
			if ( is_numeric( $key ) ) {
				unset( $current_data[ $key ] );
			}
		}
		update_post_meta( $product_id, '_woocommerce_gpf_data', $current_data );
	}

	/**
	 * Store GPF data set specifically against the variation.
	 */
	public function save_variation( $product_id, $idx ) {
		if ( empty( $_POST['_woocommerce_gpf_data'][ $idx ] ) ) {
			return;
		}
		$current_data = get_post_meta( $product_id, '_woocommerce_gpf_data', true );
		if ( ! $current_data ) {
			$current_data = array();
		}
		// Remove entries that are blanked out
		foreach ( $_POST['_woocommerce_gpf_data'][ $idx ] as $key => $value ) {
			if ( empty( $value ) ) {
				unset( $_POST['_woocommerce_gpf_data'][ $idx ][ $key ] );
				if ( isset( $current_data[ $key ] ) ) {
					unset( $current_data[ $key ] );
				}
			} else {
				$_POST['_woocommerce_gpf_data'][ $idx ][ $key ] = stripslashes_deep( $value );
			}
		}
		// Including missing checkboxes
		if ( ! isset( $_POST['_woocommerce_gpf_data'][ $idx ]['exclude_product'] ) ) {
			unset( $current_data['exclude_product'] );
		}
		$current_data = array_merge( $current_data, $_POST['_woocommerce_gpf_data'][ $idx ] );
		update_post_meta( $product_id, '_woocommerce_gpf_data', $current_data );
	}

	/**
	 * Produce a default variables array for passing to a field's default template.
	 *
	 * @param string $key The key being processed.
	 *
	 * @return array        The default variables array.
	 */
	private function default_field_variables( $key, $loop_idx = null ) {
		$variables            = array();
		$variables['raw_key'] = esc_attr( $key );
		if ( is_null( $loop_idx ) ) {
			$variables['key'] = esc_attr( $key );
		} else {
			$variables['key'] = $loop_idx . '][' . esc_attr( $key );
		}
		if ( isset( $_REQUEST['post'] ) ||
			 isset( $_REQUEST['taxonomy'] ) ||
			 isset( $_REQUEST['post_type'] ) ||
			 ! is_null( $loop_idx ) ) {
			$variables['emptytext'] = __( 'Use default', 'woocommerce_gpf' );
		} else {
			$variables['emptytext'] = __( 'No default', 'woocommerce_gpf' );
		}

		return $variables;
	}

	/**
	 * Loop through the available choices and set a variable for each choice indicating if it is
	 * selected or not.
	 *
	 * @param array $choices Array of choice values.
	 * @param string $current_data The current selected value.
	 * @param array $variables The template variables array to add to.
	 *
	 * @return array                The modified template variables array.
	 */
	private function default_selected_choices( $choices, $current_data, $variables ) {
		foreach ( $choices as $choice ) {
			if ( $choice === $current_data ) {
				$variables[ $choice . '-selected' ] = ' selected';
			} else {
				$variables[ $choice . '-selected' ] = '';
			}
		}

		return $variables;
	}

	/**
	 * Render the options for the exclude product checkbox.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed.
	 * @param string $current_data The current value of this key.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_exclude_product( $key, $context, $current_data = false, $placeholder = null, $loop_idx = null ) {
		$variables            = $this->default_field_variables( $key );
		$variables['checked'] = '';
		if ( $current_data ) {
			$variables['checked'] = ' checked="checked"';
		}
		$variables['hide_product_text'] = __( 'Hide this product from the feed', 'woocommerce_gpf' );
		if ( ! is_null( $loop_idx ) ) {
			$variables['loop_idx'] = '[' . $loop_idx . ']';
			$variables['loop_num'] = $loop_idx;
		} else {
			$variables['loop_idx'] = '';
			$variables['loop_num'] = '';
		}
		$this->template_loader->output_template_with_variables(
			'woo-gpf',
			'meta-exclude-product',
			$variables
		);
	}

	/**
	 * Render the options for the identifier_exists attribute
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_i_exists( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables = $this->default_field_variables( $key, $loop_idx );
		$variables = $this->default_selected_choices(
			array( 'included', 'not-included' ),
			$current_data,
			$variables
		);

		return $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'field-row-default-iexists',
			$variables
		);
	}

	/**
	 * Render large text box for title field.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_title( $key, $context, $current_data = null, $placeholder = '', $loop_idx = null ) {
		$variables = $this->default_field_variables( $key, $loop_idx );
		if ( ! empty( $placeholder ) ) {
			$variables['placeholder'] = ' placeholder="' . esc_attr( $placeholder ) . '"';
		} else {
			$variables['placeholder'] = '';
		}
		$variables['current_data'] = esc_attr( $current_data );

		return $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'field-row-default-title',
			$variables
		);
	}

	/**
	 *  NULL render since we can't (yet) override description.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_description( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		return '';
	}

	/**
	 * Let people choose whether a product is_bundle.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_is_bundle( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables          = $this->default_field_variables( $key, $loop_idx );
		$variables['value'] = checked( 'on', $current_data, false );

		return $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'field-row-default-is-bundle',
			$variables
		);
	}


	/**
	 * Let people choose an availability date for a product.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_availability_date( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables          = $this->default_field_variables( $key, $loop_idx );
		$variables['value'] = esc_attr( $current_data );
		if ( ! empty( $placeholder ) ) {
			$variables['placeholder'] = ' placeholder="' . esc_attr( $placeholder ) . '"';
		} else {
			$variables['placeholder'] = '';
		}

		return $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'field-row-default-availability-date',
			$variables
		);
	}

	/**
	 * Used to render the drop-down of valid size types
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_generic_select( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables                = $this->default_field_variables( $key, $loop_idx );
		$variables['options']     = $this->build_select_options(
			$this->product_fields[ $key ]['options_callback'](),
			$current_data
		);
		$variables['emptyoption'] = '';
		$optional                 = ! isset( $this->product_fields[ $key ]['mandatory'] ) ||
									! $this->product_fields[ $key ]['mandatory'];
		// Mandatory fields are only mandatory on the main config screen. If this is some other context (category,
		// product, variation), or the field is optional, we need the "use default"/"no default" option.
		if ( 'config' !== $context || $optional ) {
			$variables['emptyoption'] = $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-default-generic-select-empty-option',
				$variables
			);
		}

		return $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'field-row-default-generic-select',
			$variables
		);
	}

	/**
	 * Used to render the fields for instalments.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_installment( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables                       = $this->default_field_variables( $key, $loop_idx );
		$variables['defaultvaluemonths'] = ! empty( $current_data[0]['months'] ) ? $current_data[0]['months'] : '';
		$variables['defaultvalueamount'] = ! empty( $current_data[0]['amount'] ) ? $current_data[0]['amount'] : '';

		return $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'field-row-default-installment',
			$variables
		);
	}

	/**
	 * Used to render the fields for consumer_notice.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_consumer_notice( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables = $this->default_field_variables( $key, $loop_idx );

		$output       = '';
		$subidx_limit = apply_filters( 'woocommerce_gpf_number_of_consumer_notice_fields', 2 );
		for ( $subidx = 0; $subidx < $subidx_limit; $subidx++ ) {
			$variables['subidx']                    = $subidx;
			$variables['notice_message']            = '';
			$variables['US_CA_PROP_65_selected']    = '';
			$variables['safety_warning_selected']   = '';
			$variables['legal_disclaimer_selected'] = '';
			if ( isset( $current_data[ $subidx ] ) ) {
				$variables['notice_message']                                        = $current_data[ $subidx ]['notice_message'];
				$variables[ $current_data[ $subidx ]['notice_type'] . '_selected' ] = 'selected';
			}
			$output .= $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-default-consumer-notice',
				$variables
			);
		}

		return $output;
	}

	/**
	 * Used to render the fields for product highlight.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_product_highlight( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables = $this->default_field_variables( $key, $loop_idx );

		$output       = '';
		$subidx_limit = apply_filters( 'woocommerce_gpf_number_of_product_highlight_fields', 5 );
		for ( $subidx = 0; $subidx < $subidx_limit; $subidx++ ) {
			$variables['subidx']    = $subidx;
			$variables['highlight'] = '';
			if ( isset( $current_data[ $subidx ] ) ) {
				$variables['highlight'] = is_array( $current_data[ $subidx ] ) ?
					$current_data[ $subidx ]['highlight'] :
					$current_data[ $subidx ];
			}
			$output .= $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-default-product-highlight',
				$variables
			);
		}

		return $output;
	}

	/**
	 * Used to render the fields for product detail.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_product_detail( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables = $this->default_field_variables( $key, $loop_idx );

		$output       = '';
		$subidx_limit = apply_filters( 'woocommerce_gpf_number_of_product_detail_fields', 2 );
		for ( $subidx = 0; $subidx < $subidx_limit; $subidx++ ) {
			$variables['subidx']          = $subidx;
			$variables['section_name']    = '';
			$variables['attribute_name']  = '';
			$variables['attribute_value'] = '';
			if ( isset( $current_data[ $subidx ] ) ) {
				$variables['section_name']    = $current_data[ $subidx ]['section_name'];
				$variables['attribute_name']  = $current_data[ $subidx ]['attribute_name'];
				$variables['attribute_value'] = $current_data[ $subidx ]['attribute_value'];
			}
			$output .= $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-default-product-detail',
				$variables
			);
		}

		return $output;
	}

	/**
	 * Used to render the fields for consumer datasheet.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_consumer_datasheet( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables = $this->default_field_variables( $key, $loop_idx );

		$output       = '';
		$subidx_limit = apply_filters( 'woocommerce_gpf_number_of_consumer_datasheet_fields', 2 );
		for ( $subidx = 0; $subidx < $subidx_limit; $subidx++ ) {
			$variables['subidx']          = $subidx;
			$variables['attribute_name']  = '';
			$variables['attribute_value'] = '';
			if ( isset( $current_data[ $subidx ] ) ) {
				$variables['attribute_name']  = $current_data[ $subidx ]['attribute_name'];
				$variables['attribute_value'] = $current_data[ $subidx ]['attribute_value'];
			}
			$output .= $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-default-consumer-datasheet',
				$variables
			);
		}

		return $output;
	}

	/**
	 * Used to render the fields for product fee.
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_product_fee( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables = $this->default_field_variables( $key, $loop_idx );

		$output       = '';
		$subidx_limit = apply_filters( 'woocommerce_gpf_number_of_product_fee_fields', 1 );
		for ( $subidx = 0; $subidx < $subidx_limit; $subidx++ ) {
			$variables['subidx'] = $subidx;
			$variables['type']   = '';
			$variables['amount'] = '';
			if ( isset( $current_data[ $subidx ] ) ) {
				$variables['type']   = $current_data[ $subidx ]['type'];
				$variables['amount'] = $current_data[ $subidx ]['amount'];
			}
			$output .= $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-default-product-fee',
				$variables
			);
		}

		return $output;
	}

	private function get_google_taxonomy_locales() {
		$country_locales = array(
			// Argentina is en-US rather than es-ES per https://support.google.com/merchants/answer/6324436?hl=es-419
			'AR' => [], // Argentina
			'AU' => [ 'en-AU' ], // Australia
			'AT' => [ 'de-DE' ], // Austria
			'BH' => [], // Bahrain - no specific mapping clear
			'BY' => [ 'ru-RU' ], // Belarus
			'BE' => [ 'fr-FR', 'de-DE', 'nl-NL' ], // Belgium
			'BR' => [ 'pt-BR' ], // Brazil
			'CA' => [ 'fr-FR' ], // Canada
			// Chile is en-US rather than es-ES per https://support.google.com/merchants/answer/6324436?hl=es-419
			'CL' => [], // Chile
			'CN' => [ 'zh-CN' ], // China
			// Colombia is en-US rather than es-ES per https://support.google.com/merchants/answer/6324436?hl=es-419
			'CO' => [], // Colombia
			'CZ' => [ 'cs-CZ' ], // Czech Republic
			'DK' => [ 'da-DK' ], // Denmark
			// Ecuador is en-US rather than es-ES per https://support.google.com/merchants/answer/6324436?hl=es-419
			'EC' => [], // Ecuador
			'EG' => [ 'ar-SA' ], // Egypt
			'FI' => [ 'fi-FI', 'sv-SE' ], // Finland
			'FR' => [ 'fr-FR' ], // France
			'GE' => [], // Georgian not currently supported language
			'DE' => [ 'de-DE' ], // Germany
			'GR' => [], // Greek not currently supported
			'HK' => [ 'zh-CN' ], // Hong-Kong
			'HU' => [], // Hungarian not currently supported
			'IN' => [], // India
			'ID' => [], // Indonesia
			'IE' => [ 'en-GB' ], // Ireland
			'IL' => [], // Israel
			'IT' => [ 'it-IT' ], // Italy
			'JP' => [ 'ja-JP' ], // Japan
			'JO' => [ 'ar-SA' ], // Jordan
			'KZ' => [ 'ru-RU' ], // Kazakhstan
			'KW' => [ 'ar-SA' ], // Kuwait
			'LB' => [ 'ar-SA' ], // Lebanon
			'MY' => [], // Malaysia
			'MX' => [], // Mexico
			'NL' => [ 'nl-NL' ], // Netherlands
			'NZ' => [], // New Zealand
			'NO' => [ 'no-NO' ], // Norway
			'OM' => [ 'ar-SA' ], // Oman
			'PY' => [], // Paraguay
			'PE' => [], // Peru
			'PH' => [], // Philippines
			'PL' => [ 'pl-PL' ], // Poland
			'PT' => [ 'pt-BR' ], // Portugal
			'RO' => [ '' ], // Romania
			'RU' => [ 'ru-RU' ], // Russia
			'SG' => [], // Singapore
			'SK' => [], // Slovakia
			'ZA' => [], // South Africa
			'ES' => [ 'es-ES' ], // Spain
			'SE' => [ 'sv-SE' ], // Sweden
			'CH' => [ 'de-DE', 'fr-FR', 'it-IT' ], // Switzerland
			'TW' => [], // Taiwan
			'TR' => [ 'tr-TR' ], // Turkey
			'AE' => [ 'ar-SA' ], // United Arab Emirates
			'GB' => [ 'en-GB' ], // United Kingdom
			'US' => [], // United States of America
			'UY' => [], // Uruguay
			'UZ' => [], // Uzbekistan
			'VN' => [ 'vi-VN' ], // Vietnam
		);

		$base_country = WC()->countries->get_base_country();
		$locales      = $country_locales[ $base_country ] ?? [];
		if ( ! in_array( 'en-US', $locales, true ) && ! in_array( 'en-GB', $locales, true ) ) {
			$locales[] = 'en-US';
		}

		return apply_filters( 'woocommerce_gpf_google_taxonomy_locales', $locales );
	}

	/**
	 * Refresh any Google taxonomies that need it.
	 *
	 * @return void
	 */
	private function maybe_refresh_google_taxonomy() {
		global $wpdb, $table_prefix;
		// Do not attempt to refresh before DB updates completed.
		$current_db_version = (int) get_option( 'woocommerce_gpf_db_version', 1 );
		if ( $current_db_version < 13 ) {
			return;
		}
		$required_locales = $this->get_google_taxonomy_locales();
		$table_name       = $table_prefix . 'woocommerce_gpf_google_taxonomy';
		$locale_counts    = $wpdb->get_results( "SELECT locale, COUNT(*) FROM $table_name GROUP BY locale", OBJECT_K );
		foreach ( $required_locales as $locale ) {
			// If we have no data for this locale, trigger a refresh.
			if ( empty( $locale_counts[ $locale ] ) ) {
				$this->refresh_google_taxonomy( $locale );
				continue;
			}
			// If we have data, but the transient has expired, refresh it.
			$cache_key     = 'woocommerce_gpf_tax_' . $locale;
			$locale_cached = get_transient( $cache_key );
			if ( empty( $locale_cached ) ) {
				$this->refresh_google_taxonomy( $locale );
			}
		}
		foreach ( array_keys( $locale_counts ) as $cached_locale ) {
			if ( ! in_array( $cached_locale, $required_locales, true ) ) {
				$this->clear_google_taxonomy( $cached_locale );
			}
		}
	}

	/**
	 * Clear the Google taxonomy cache for a specific locale.
	 */
	private function clear_google_taxonomy( $locale ) {
		$pending = as_get_scheduled_actions(
			[
				'hook'     => 'woocommerce_product_feeds_clear_google_taxonomy',
				'args'     => [ $locale ],
				'status'   => [ \ActionScheduler_Store::STATUS_PENDING, \ActionScheduler_Store::STATUS_RUNNING ],
				'per_page' => 1,
				'orderby'  => 'none',
			],
			'ids'
		);
		if ( empty( $pending ) ) {
			as_schedule_single_action(
				null,
				'woocommerce_product_feeds_clear_google_taxonomy',
				[ $locale ],
				'woocommerce-product-feeds'
			);
		}
	}

	/**
	 * Retrieve the Google taxonomy for a specific locale and cache it to allow users to choose from it.
	 */
	private function refresh_google_taxonomy( $locale ) {
		$pending = as_get_scheduled_actions(
			[
				'hook'     => 'woocommerce_product_feeds_refresh_google_taxonomy',
				'args'     => [ $locale ],
				'status'   => [ \ActionScheduler_Store::STATUS_PENDING, \ActionScheduler_Store::STATUS_RUNNING ],
				'per_page' => 1,
				'orderby'  => 'none',
			],
			'ids'
		);
		if ( empty( $pending ) ) {
			as_schedule_single_action(
				null,
				'woocommerce_product_feeds_refresh_google_taxonomy',
				[ $locale ],
				'woocommerce-product-feeds'
			);
		}
	}


	/**
	 * Let people choose from the Google taxonomy for the product_type tag
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_product_type( $key, $context, $current_data = null, $placeholder = '', $loop_idx = null ) {
		$variables = $this->default_field_variables( $key, $loop_idx );
		if ( ! empty( $placeholder ) ) {
			$variables['placeholder'] = ' placeholder="' . esc_attr( $placeholder ) . '"';
		} else {
			$variables['placeholder'] = '';
		}
		$variables['current_data'] = esc_attr( $current_data );
		$variables['locale_list']  = implode(
			', ',
			array_map( [ $this, 'map_google_taxonomy_locale_names' ], $this->get_google_taxonomy_locales() )
		);

		return $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'field-row-default-product-type',
			$variables
		);
	}

	/**
	 * @param $locale
	 *
	 * @return mixed
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function map_google_taxonomy_locale_names( $locale ) {
		$map = [
			'ar-SA' => __( 'Arabic', 'woocommerce_gpf' ),
			'cs-CZ' => __( 'Czech', 'woocommerce_gpf' ),
			'da-DK' => __( 'Danish', 'woocommerce_gpf' ),
			'de-DE' => __( 'German', 'woocommerce_gpf' ),
			'en-AU' => __( 'English (Australian)', 'woocommerce_gpf' ),
			'en-GB' => __( 'English (British)', 'woocommerce_gpf' ),
			'en-US' => __( 'English (US)', 'woocommerce_gpf' ),
			'es-ES' => __( 'Spanish', 'woocommerce_gpf' ),
			'fi-FI' => __( 'Finnish', 'woocommerce_gpf' ),
			'fr-FR' => __( 'French', 'woocommerce_gpf' ),
			'it-IT' => __( 'Italian', 'woocommerce_gpf' ),
			'ja-JP' => __( 'Japanese', 'woocommerce_gpf' ),
			'nl-NL' => __( 'Dutch', 'woocommerce_gpf' ),
			'no-NO' => __( 'Norwegian', 'woocommerce_gpf' ),
			'pl-PL' => __( 'Polish', 'woocommerce_gpf' ),
			'pt-BR' => __( 'Portuguese', 'woocommerce_gpf' ),
			'ru-RU' => __( 'Russian', 'woocommerce_gpf' ),
			'sv-SE' => __( 'Swedish', 'woocommerce_gpf' ),
			'tr-TR' => __( 'Turkish', 'woocommerce_gpf' ),
			'vi-VN' => __( 'Vietnamese', 'woocommerce_gpf' ),
			'zh-CN' => __( 'Chinese', 'woocommerce_gpf' ),
		];
		return $map[ $locale ] ?? $locale;
	}

	/**
	 * Let people choose from the Bing taxonomy for the bing_category tag
	 *
	 * @access private
	 *
	 * @param string $key The key being processed
	 * @param string $current_data The current value of this key
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function render_b_category( $key, $context, $current_data = null, $placeholder = null, $loop_idx = null ) {
		$variables                 = $this->default_field_variables( $key, $loop_idx );
		$variables['current_data'] = esc_attr( $current_data );
		if ( ! empty( $placeholder ) ) {
			$variables['placeholder'] = ' placeholder="' . esc_attr( $placeholder ) . '"';
		} else {
			$variables['placeholder'] = '';
		}

		return $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'field-row-default-bing-category',
			$variables
		);
	}


	/**
	 * Add a tab to the WooCommerce settings pages
	 *
	 * @access public
	 *
	 * @param array $tabs The current list of settings tabs
	 *
	 * @return array       The tabs array with the new item added
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	public function add_woocommerce_settings_tab( $tabs ) {
		$tabs['gpf'] = __( 'Product Feeds', 'woocommerce_gpf' );

		return $tabs;
	}

	/**
	 * Generate the feed icons for the field on the main settings page.
	 */
	private function feed_images_for_field( $key ) {
		$results        = '';
		$all_feed_types = $this->common->get_feed_types();
		foreach ( $this->product_fields[ $key ]['feed_types'] as $feed_type ) {
			$image_url = isset( $all_feed_types[ $feed_type ]['icon'] ) ?
				$all_feed_types[ $feed_type ]['icon'] :
				null;
			if ( is_null( $image_url ) ) {
				continue;
			}
			$results .= $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'admin-feed-image',
				array(
					'image_url' => $image_url,
					'alt_text'  => esc_attr( $all_feed_types[ $feed_type ]['name'] ),
				)
			);
		}

		return $results;
	}

	/**
	 * Show the preopulate selector for a field.
	 *
	 * @param string $prepopulate_key The key of the current field.
	 *
	 * @return string      The markup for the selector.
	 */
	private function prepopulate_selector_for_field( $prepopulate_key ) {

		if ( empty( $this->product_fields[ $prepopulate_key ]['can_prepopulate'] ) ) {
			return '';
		}
		$options        = $this->common->get_prepopulate_options( $prepopulate_key );
		$selected_value = ! empty( $this->settings['product_prepopulate'][ $prepopulate_key ] ) ?
			$this->settings['product_prepopulate'][ $prepopulate_key ] :
			'';

		$variables               = [];
		$variables['key']        = esc_attr( $prepopulate_key );
		$variables['intro_text'] = __( 'Use value from existing product field: ', 'woo_gpf' );

		// Create the list of options.
		$variables['options'] = '';
		if ( 'description' !== $prepopulate_key ) {
			$variables['options'] = '<option value="">' . __( 'No', 'woo_gpf' ) . '</option>';
		}
		foreach ( $options as $key => $value ) {
			if ( 0 === stripos( $key, 'disabled' ) ) {
				$disabled = ' disabled';
			} else {
				$disabled = '';
			}
			$variables['options'] .= $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-prepopulate-option',
				array(
					'key'      => esc_attr( $key ),
					'value'    => esc_html( $value ),
					'disabled' => $disabled,
					'selected' => selected( $key, $selected_value, false ),
				)
			);
		}

		return $this->template_loader->get_template_with_variables( 'woo-gpf', 'field-row-prepopulate', $variables );
	}

	/**
	 * Get the label descriptor for a given field.
	 *
	 * @param string $field The field to find the label for.
	 *
	 * @return string          The field label.
	 */
	private function get_label_descriptor_for_field( $field ) {

		$prepopulate_options = $this->common->get_prepopulate_options();
		if ( ! empty( $prepopulate_options[ 'field:' . $field ] ) ) {
			return $prepopulate_options[ 'field:' . $field ];
		} else {
			return $field;
		}
	}

	/**
	 * Get the label descriptor for a given meta pre-population rule.
	 *
	 * @param string $meta The meta to find the label for.
	 *
	 * @return string          The field label.
	 */
	private function get_label_descriptor_for_meta( $meta ) {

		$prepopulate_options = $this->common->get_prepopulate_options();
		if ( ! empty( $prepopulate_options[ 'meta:' . $meta ] ) ) {
			return $prepopulate_options[ 'meta:' . $meta ];
		} else {
			return $meta;
		}
	}

	/**
	 * Get the label describing how a field is prepopulated.
	 *
	 * @param string $key The prepopulate value for the field
	 *
	 * @return string       The label text.
	 */
	private function get_prepopulate_label( $key ) {
		list( $type, $value ) = explode( ':', $key );
		$descriptor           = '';
		switch ( $type ) {
			case 'tax':
				$taxonomy = get_taxonomy( $value );
				if ( $taxonomy ) {
					// Translators: %s is the name of the taxonomy
					$descriptor = sprintf( __( '<em>%s</em> taxonomy', 'woo_gpf' ), $taxonomy->labels->singular_name );
				}
				break;
			case 'field':
				$label = $this->get_label_descriptor_for_field( $value );
				// Translators: %s is the name of the field
				$descriptor = sprintf( __( '<em>%s</em> field', 'woo_gpf' ), $label );
				break;
			case 'meta':
				$label = $this->get_label_descriptor_for_meta( $value );
				// Translators: %1$s is the name of the meta field, %2$s is the raw meta key
				$descriptor = sprintf( __( '<em>%1$s</em> meta (%2$s)', 'woo_gpf' ), $label, $value );
				break;
			default:
				$descriptor = apply_filters( 'woocommerce_gpf_prepopulate_label', $descriptor, $key );
				break;
		}
		if ( empty( $descriptor ) ) {
			return '';
		}

		return sprintf(
		// Translators: %s is the description of where the data will be pulled from.
			__( 'Uses value from %s if set.', 'woo_gpf' ),
			$descriptor
		);
	}

	/**
	 * Show config page, and process form submission
	 *
	 * @access public
	 */
	public function config_page() {

		// Output the header.
		$variables = array();

		$feed_types                      = $this->common->get_feed_types();
		$settings_url                    = add_query_arg(
			array(
				'page' => 'wc-settings',
				'tab'  => 'gpf',
			),
			admin_url( 'admin.php' )
		);
		$variables['cache_status']       = apply_filters( 'woocommerce_gpf_cache_status', '', $settings_url );
		$variables['refresh_fields_url'] = wp_nonce_url(
			add_query_arg(
				array(
					'gpf_action' => 'refresh_custom_fields',
				),
				$settings_url
			),
			'gpf_refresh_custom_fields'
		);

		$feed_types   = $this->common->get_feed_types();
		$feed_configs = $this->feed_config_repository->all();

		$variables['active_feeds'] = $this->template_loader->get_template_with_variables( 'woo-gpf', 'admin-feeds-intro', array() );
		foreach ( $feed_configs as $config ) {
			$feed_type      = $feed_types[ $config->type ];
			$feed_url       = get_home_url( null, '/woocommerce_gpf/' . $config->id );
			$feed_variables = [
				'html_name' => esc_html( $config->name ),
				'icon'      => $feed_type['icon'],
				'attr_url'  => esc_attr( $feed_url ),
				'html_url'  => esc_html( $feed_url ),
			];

			$variables['active_feeds'] .= $this->template_loader->get_template_with_variables( 'woo-gpf', 'admin-feeds-feed', $feed_variables );
		}
		$variables['active_feeds'] .= $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'admin-feeds-footer',
			[
				'manage_url' => esc_attr( admin_url( 'admin.php?page=woocommerce-gpf-manage-feeds' ) ),
			]
		);

		$this->template_loader->output_template_with_variables( 'woo-gpf', 'admin-intro', $variables );
		$this->template_loader->output_template_with_variables( 'woo-gpf', 'admin-feed-fields-intro', array() );

		// Output the fields.
		foreach ( $this->product_fields as $key => $info ) {

			$variables                  = array();
			$row_vars                   = array();
			$def_vars                   = array();
			$variables['row_title']     = esc_html( $info['desc'] );
			$variables['feed_images']   = $this->feed_images_for_field( $key );
			$row_vars['header_content'] = $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-header',
				$variables
			);

			$variables            = array();
			$variables['key']     = esc_attr( $key );
			$variables['checked'] = '';
			if ( isset( $this->settings['product_fields'][ $key ] ) ) {
				$variables['checked'] = 'checked="checked"';
			}

			$variables['full_desc'] = esc_html( $info['full_desc'] );

			if ( isset( $this->product_fields[ $key ]['can_default'] ) ) {
				$def_vars['defaultinput'] = __( 'Store default: <br>', 'woocommerce_gpf' ) .
											$this->render_field_default_input( $key, 'config' );
			} else {
				$def_vars['defaultinput'] = '';
			}
			$def_vars['prepopulates'] = $this->prepopulate_selector_for_field( $key );
			$def_vars['key']          = $key;
			$def_vars['displaynone']  = '';
			if ( ! isset( $this->settings['product_fields'][ $key ] ) ) {
				$def_vars['displaynone'] = ' style="display:none;"';
			}
			$variables['class_mandatory'] = '';
			if ( isset( $this->product_fields[ $key ]['mandatory'] ) && $this->product_fields[ $key ]['mandatory'] ) {
				$variables['checked']         = 'checked="checked"';
				$variables['class_mandatory'] = 'woocommerce_gpf_field_selector_mandatory"';
				$def_vars['displaynone']      = '';
			}

			$variables['defaults']    = $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-defaults',
				$def_vars
			);
			$row_vars['data_content'] = $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-data',
				$variables
			);
			$this->template_loader->output_template_with_variables(
				'woo-gpf',
				'field-row',
				$row_vars
			);
		}
		$variables                                = array();
		$variables['include_variations_selected'] = checked(
			'on',
			isset( $this->settings['include_variations'] ) ? $this->settings['include_variations'] : '',
			false
		);
		$variables['include_variations']          = $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'admin-include-variations',
			$variables
		);
		$variables['send_item_group_id_selected'] = checked(
			'on',
			isset( $this->settings['send_item_group_id'] ) ? $this->settings['send_item_group_id'] : '',
			false
		);
		$variables['send_item_group_id']          = $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'admin-send-item-group-id',
			$variables
		);
		$variables['expanded_schema_selected']    = checked(
			'on',
			isset( $this->settings['expanded_schema'] ) ? $this->settings['expanded_schema'] : '',
			false
		);
		$variables['expanded_schema']             = $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'admin-expanded-schema',
			$variables
		);
		$variables['shop_code']                   = $this->template_loader->get_template_with_variables(
			'woo-gpf',
			'admin-shop-code',
			array(
				'shop_code' => ! empty( $this->settings['shop_code'] ) ?
					esc_attr( $this->settings['shop_code'] ) :
					'',
			)
		);
		$this->template_loader->output_template_with_variables( 'woo-gpf', 'admin-footer', $variables );
	}

	/**
	 * Renders the output for the "default" box for a field.
	 *
	 * @param string $key The field being rendered.
	 * @param string $context The page being rendered: config, category, variation, or product
	 * @param string $current_data The current value. If not provided, the default will be used
	 *                              from the store wide settings.
	 * @param string $placeholder Placeholder text to use, leave blank for no placeholder.
	 * @param int $loop_idx The loop idx of the variation being output.
	 *
	 * @return string
	 */
	private function render_field_default_input( $key, $context, $current_data = false, $placeholder = '', $loop_idx = null ) {
		$variables = array();
		if ( null === $loop_idx ) {
			$variables['key'] = $key;
		} else {
			$variables['key'] = $loop_idx . '][' . $key;
		}
		if ( ! empty( $placeholder ) ) {
			$variables['placeholder'] = ' placeholder="' . esc_attr( $placeholder ) . '"';
		} else {
			$variables['placeholder'] = '';
		}
		if ( false === $current_data ) {
			$current_data = isset( $this->settings['product_defaults'][ $key ] ) ? $this->settings['product_defaults'][ $key ] : '';
		}
		if ( ! isset( $this->{'product_fields'}[ $key ]['callback'] ) ||
			 ! is_callable( array( $this, $this->{'product_fields'}[ $key ]['callback'] ) ) ) {
			$variables['defaultvalue'] = esc_attr( $current_data );

			return $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-default-generic',
				$variables
			);
		} else {
			return call_user_func(
				array(
					$this,
					$this->{'product_fields'}[ $key ]['callback'],
				),
				$key,
				$context,
				$current_data,
				$placeholder,
				$loop_idx
			);
		}
	}

	/**
	 * Save the settings from the config page
	 *
	 * @access public
	 */
	public function save_settings() {

		// Check nonce
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'woocommerce-settings' ) ) {
			die( __( 'Action failed. Please refresh the page and retry.', 'woothemes' ) );
		}

		if ( ! $this->settings ) {
			$this->settings = array();
			add_option( 'woocommerce_gpf_config', $this->settings, '', 'yes' );
		}

		if ( ! empty( $_POST['_woocommerce_gpf_data'] ) ) {
			// We do these so we can re-use the same form field rendering code for the fields
			foreach ( $_POST['_woocommerce_gpf_data'] as $key => $value ) {
				$_POST['_woocommerce_gpf_data'][ $key ] = stripslashes( $value );
			}
			$_POST['woocommerce_gpf_config']['product_defaults'] = $_POST['_woocommerce_gpf_data'];
			unset( $_POST['_woocommerce_gpf_data'] );
		}

		if ( ! empty( $_POST['_woocommerce_gpf_prepopulate'] ) ) {
			// We do these so we can re-use the same form field rendering code for the fields
			foreach ( $_POST['_woocommerce_gpf_prepopulate'] as $key => $value ) {
				$_POST['_woocommerce_gpf_prepopulate'][ $key ] = stripslashes( $value );
			}
			$_POST['woocommerce_gpf_config']['product_prepopulate'] = $_POST['_woocommerce_gpf_prepopulate'];
			unset( $_POST['_woocommerce_gpf_prepopulate'] );
		}
		$this->settings = $_POST['woocommerce_gpf_config'];
		update_option( 'woocommerce_gpf_config', $this->settings );
	}

	/**
	 * @param $options
	 *
	 * @return string
	 */
	private function build_select_options( $options, $current_data ) {
		$result = '';
		foreach ( $options as $value => $description ) {
			$result .= $this->template_loader->get_template_with_variables(
				'woo-gpf',
				'field-row-default-generic-select-option',
				[
					'value'       => $value,
					'description' => $description,
					'selected'    => ( $current_data === $value ) ? 'selected' : '',
				]
			);
		}

		return $result;
	}
}
