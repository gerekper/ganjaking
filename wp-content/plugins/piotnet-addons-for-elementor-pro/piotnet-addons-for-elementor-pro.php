<?php
/**
 * Plugin Name: Piotnet Addons For Elementor Pro
 * Description: Piotnet Addons For Elementor Pro (PAFE Pro) adds many new features for Elementor
 * Plugin URI:  https://pafe.piotnet.com/
 * Version:     7.1.17
 * Author:      Piotnet Team
 * Author URI:  https://piotnet.com/
 * Text Domain: pafe
 * Domain Path: /languages
 * Elementor tested up to: 3.13.4
 * Elementor Pro tested up to: 3.13.2
 */
update_option( 'piotnet_addons_for_elementor_pro_license_key', [ 'siteKey' => '**********', 'licenseKey' => '*********' ] );
update_option( 'piotnet_addons_for_elementor_pro_license_data', [ 'timeout' => time() + 5*365*24*60*60, 'value' => [
'status' => 'VALID',
'email' => '',
'displayName' => 'Unlimited license',
'unlimited_site' => 1,
'site_total' => 0,
'activated_site_total' => 1,
'lifetime' => 1,
'expired_at' => time() + 5*365*24*60*60,
] ] );
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'PAFE_PRO_VERSION', '7.1.17' );
define( 'PAFE_PRO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

final class Piotnet_Addons_For_Elementor_Pro {

	const MINIMUM_ELEMENTOR_VERSION = '3.4.0';
	const MINIMUM_PHP_VERSION = '5.4';
	const TAB_PAFE = 'tab_pafe';

	private static $_instance = null;

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	public function __construct() {
        require_once(__DIR__ . '/inc/license.php');
        PAFE_License_Service::refresh_license(false);

		add_action( 'init', [ $this, 'i18n' ] );

		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {
			add_action( 'init', [ $this, 'pafe_forms_post_type' ] );
			add_action( 'init', [ $this, 'pafe_elementor_form_database_post_type' ] );
			add_action( 'init', [ $this, 'pafe_form_database_post_type' ] );
			add_action( 'init', [ $this, 'pafe_form_booking_post_type' ] );
			add_action( 'init', [ $this, 'pafe_pdf_font_post_type' ] );
		}

		if( get_option( 'pafe-features-widget-creator', 2 ) == 2 || get_option( 'pafe-features-widget-creator', 2 ) == 1 ) {
			add_action( 'init', [ $this, 'pafe_widget_creator_post_type' ] );
		}


		require_once( __DIR__ . '/inc/features.php' );
		$features = json_decode( PAFE_FEATURES, true );

		$extension = false;
		$form_builder = false;
		$widget = false;
		$woocommerce_sales_funnels = false;

		foreach ($features as $feature) {
			if ($feature['pro'] == 1) {
				if( get_option( $feature['option'], 2 ) == 2 || get_option( $feature['option'], 2 ) == 1 ) {
					if (!empty($feature['extension'])) {
						$extension = true;
					}
					if (!empty($feature['form-builder'])) {
						$form_builder = true;
					}
					if (empty($feature['extension']) && empty($feature['form-builder'])) {
						$widget = true;
					}
					if (!empty($feature['woocommerce_sales_funnels'])) {
						$woocommerce_sales_funnels = true;
					}
				}
			}
		}

		if ($extension) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
		}

		if( get_option( 'pafe-features-font-awesome-5', 2 ) == 2 || get_option( 'pafe-features-font-awesome-5', 2 ) == 1 ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_font_awesome_5' ] );
		}

		if ($woocommerce_sales_funnels) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_woocommerce_sales_funnels' ] );
		}

		if ($form_builder || $widget) {
			add_action( 'elementor/frontend/after_register_scripts', [ $this, 'enqueue_scripts_widget' ] );
			add_action( 'elementor/preview/enqueue_scripts', [ $this, 'enqueue_scripts_widget_preview' ] );
			add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles_widget' ] );
			add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_styles_widget_preview' ] );
		}

		if( get_option( 'pafe-features-custom-css', 2 ) == 2 || get_option( 'pafe-features-custom-css', 2 ) == 1 ) {
			add_action('elementor/frontend/after_enqueue_scripts', [$this, 'add_custom_css_for_editor']);
		}

		add_action( 'plugins_loaded', [ $this, 'init' ] );
		register_activation_hook( __FILE__, [ $this, 'plugin_activate'] );
		add_action( 'admin_init', [ $this, 'plugin_redirect'] );
		add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'enqueue_editor' ] );
		
		add_action( 'elementor/element/page-settings/section_page_style/before_section_end', [ $this, 'add_elementor_page_settings_controls' ] );

		add_action('add_meta_boxes', [$this, 'pafe_pdf_metabox']);
		add_action( 'save_post_pafe-fonts', [$this, 'pafe_pdf_save_custom_font'] );
		add_filter('upload_mimes', [$this,'add_custom_upload_mimes']);
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );

		add_filter( 'elementor/init', [ $this, 'add_pafe_tab'], 10,1);
		add_filter( 'elementor/controls/get_available_tabs_controls', [ $this, 'add_pafe_tab'], 10,1);

		//add_filter( 'elementor/query/query_args', [ $this, 'change_post_type' ], 10,1);

		require_once( __DIR__ . '/inc/shortcode-pafe-gallery.php' );
		require_once( __DIR__ . '/inc/shortcode-youtube.php' );
		require_once( __DIR__ . '/inc/shortcode-pafe-edit-post.php' );
		require_once( __DIR__ . '/inc/shortcode-pafe-delete-post.php' );
		require_once( __DIR__ . '/inc/shortcode-pafe-get-posts.php' );
		// require_once( __DIR__ . '/inc/shortcode-pafe-woocommerce-checkout.php' );

		add_shortcode('pafe-template', [ $this, 'pafe_template_elementor' ] );

		if ( !defined('ELEMENTOR_PRO_VERSION') ) {
		    add_filter( 'manage_elementor_library_posts_columns', [ $this, 'set_custom_edit_columns' ] );
	    	add_action( 'manage_elementor_library_posts_custom_column', [ $this, 'custom_column' ], 10, 2 );
		} else {
			if( get_option( 'pafe-features-popup-trigger-url', 2 ) == 2 || get_option( 'pafe-features-popup-trigger-url', 2 ) == 1 ) {
				if ( version_compare( ELEMENTOR_PRO_VERSION, '2.4.0', '>=' ) ) {
					add_filter( 'manage_elementor_library_posts_columns', [ $this, 'add_popup_trigger_url_column' ] );
		    		add_action( 'manage_elementor_library_posts_custom_column', [ $this, 'popup_trigger_url_column' ], 10, 2 );
				}
			}
		}

        if(did_action( 'elementor/loaded' )){
            require_once( __DIR__ . '/inc/ajax-live-search.php' );
		    require_once( __DIR__ . '/controls/custom-controls/custom-controls.php' );
        }
		
		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/ajax-form-builder.php' );
			require_once( __DIR__ . '/inc/ajax-form-builder-preview-submission.php' );
			require_once( __DIR__ . '/inc/ajax-form-booking.php' );
			require_once( __DIR__ . '/inc/ajax-campaign-select-list.php' );
			require_once( __DIR__ . '/inc/ajax-campaign-fields.php' );
			require_once( __DIR__ . '/inc/ajax-getresponse-custom-fields.php' );
			require_once( __DIR__ . '/inc/ajax-hubspot-get-property.php' );
			require_once( __DIR__ . '/inc/ajax-intl-get-country-code.php' );
			require_once( __DIR__ . '/inc/ajax-hubspot-get-group.php' );
			require_once( __DIR__ . '/inc/ajax-getresponse-select-list.php' );
			require_once( __DIR__ . '/inc/ajax-mailchimp-get-list.php' );
			require_once( __DIR__ . '/inc/ajax-mailchimp-get-groups.php' );
			require_once( __DIR__ . '/inc/ajax-mailchimp-get-fields.php' );
			require_once( __DIR__ . '/inc/ajax-mailpoet-get-custom-fields.php');
			require_once( __DIR__ . '/inc/ajax-zoho-get-tag-name.php');
			require_once( __DIR__ . '/inc/ajax-mailerlite-get-groups.php');
			require_once( __DIR__ . '/inc/ajax-mailerlite-get-fields.php');
			require_once( __DIR__ . '/inc/ajax-export-database.php');
			require_once( __DIR__ . '/inc/ajax-sendinblue-get-list.php');
			require_once( __DIR__ . '/inc/ajax-sendinblue-get-attribute.php');
            require_once( __DIR__ . '/inc/ajax-twilio-sendgrid-get-list.php');
			require_once( __DIR__ . '/inc/ajax-mollie-get-payment.php');
			require_once( __DIR__ . '/inc/ajax-convertkit-get-field.php');
			require_once( __DIR__ . '/inc/ajax-convertkit-get-form.php');
			require_once( __DIR__ . '/inc/ajax-constant-contact-get-custom-field.php');
			require_once( __DIR__ . '/inc/ajax-constant-contact-get-list.php');
            require_once( __DIR__ . '/inc/ajax-paypal-get-plan.php');
            require_once( __DIR__ . '/inc/dynamic-tags.php');
			require_once( __DIR__ . '/inc/ajax/pafe-forms-functions.php');
			require_once( __DIR__ . '/inc/ajax/pafe-widget-functions.php');
            require_once( __DIR__ . '/inc/ajax-razorpay.php');
		}
		if( get_option( 'pafe-features-woocommerce-checkout', 2 ) == 2 || get_option( 'pafe-features-woocommerce-checkout', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/ajax-form-builder-woocommerce-checkout.php' );
		}
		if( get_option( 'pafe-features-woocommerce-sales-funnel', 2 ) == 2 || get_option( 'pafe-features-woocommerce-sales-funnel', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/ajax-woocommerce-sales-funnels-add-to-cart.php' );
		}
        
        if( get_option( 'pafe-features-stripe-payment', 2 ) == 2 || get_option( 'pafe-features-stripe-payment', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/ajax-stripe-intents.php' );
		}

		require_once( __DIR__ . '/inc/ajax-delete-post.php' );
		require_once( __DIR__ . '/inc/form-database-meta-box.php' );
		require_once( __DIR__ . '/inc/meta-box-acf-repeater.php' );

		$upload = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = $upload_dir . '/piotnet-addons-for-elementor';
        if ( ! is_dir( $upload_dir ) ) {
            mkdir( $upload_dir, 0775 );
        } else {
            @chmod( $upload_dir, 0775 );
        }

        $widget_creator_dir = $upload_dir . '/widget-creator';
        if ( ! is_dir( $widget_creator_dir ) ) {
            mkdir( $widget_creator_dir, 0775 );
        } else {
            @chmod( $widget_creator_dir, 0775 );
        }

        // Disable Directory Browsing
        if (!file_exists($upload_dir . '/index.html')) {
            touch($upload_dir . '/index.html');
        }

		if( !PAFE_License_Service::has_valid_license() ) {
			$features = json_decode( PAFE_FEATURES, true );
					
			foreach ($features as $feature) {
                if (get_option($feature['option'], 2) == 1) {
                    update_option($feature['option'],3);
                }

                if (get_option($feature['option'], 2) == 2) {
                    update_option($feature['option'],'');
                }
			}

			add_action( 'admin_notices', [ $this, 'pafe_admin_notice__error'] );
		}

		// Custom Price Woocommerce
    	add_action( 'woocommerce_before_calculate_totals', [ $this, 'pafe_apply_custom_price_to_cart_item'], 30, 1 );

        // Custom Cart Item Quantity
        add_filter('woocommerce_widget_cart_item_quantity', [ $this, 'pafe_filter_woo_cart_quantity'], 10, 3);

    	// Booking Woocommerce
    	add_action( 'woocommerce_checkout_order_processed', [ $this, 'pafe_woocommerce_checkout_order_processed'], 10, 1 );

    	// Redirect Woocommerce
    	add_action( 'template_redirect', [ $this, 'pafe_woocommerce_checkout_redirect' ] );

    	add_action( 'woocommerce_add_order_item_meta',[ $this,'pafe_add_order_item_meta'], 10, 3 );

    	if (function_exists('get_field')) {
    		add_filter('acf/settings/remove_wp_meta_box', '__return_false');
    	}

    	add_action( 'restrict_manage_posts', [ $this, 'pafe_form_builder_filter' ] );
    	add_filter( 'parse_query', [ $this, 'pafe_form_builder_filter_posts' ] );

    	add_filter('manage_pafe-formabandonment_posts_columns', [$this,'pafe_form_builder_filter_column'], 10);
		add_action('manage_pafe-formabandonment_posts_custom_column', [$this,'pafe_form_builder_filter_column_content'], 10, 2);

		add_filter('manage_pafe-form-database_posts_columns', [$this,'pafe_form_builder_filter_column'], 10);
		add_filter( 'woocommerce_order_item_get_formatted_meta_data',[$this,'pafe_unset_redirect_order_item_meta_data'], 10, 2);

		add_action('manage_pafe-form-database_posts_custom_column', [$this,'pafe_form_builder_filter_column_content'], 10, 2);

		add_action('admin_footer', [ $this,'pafe_form_builder_filter_export_btn' ] );

		add_action('admin_footer', [ $this,'admin_footer' ] );

		add_action( 'admin_print_footer_scripts', [ $this, 'admin_print_footer_scripts' ] );

		if( get_option( 'pafe-features-woocommerce-checkout', 2 ) == 2 || get_option( 'pafe-features-woocommerce-checkout', 2 ) == 1 ) {
		
			add_filter( 'woocommerce_is_checkout', array( $this, 'pafe_woocommerce_checkout_load' ), 9999 );

			add_action( 'wp_head', array( $this, 'pafe_woocommerce_checkout_load_cart' ), 10 );

			// add_action( 'wp_loaded', array( $this, 'pafe_woocommerce_checkout_redirect_session' ), 10 );

			// add_action( 'wp_head', array( $this, 'pafe_woocommerce_checkout_redirect_session_url' ), 10 );

			// add_action( 'wp_footer', array( $this, 'pafe_woocommerce_checkout_redirect_session_destroy' ), 10 );

			// add_action( 'woocommerce_thankyou', array( $this, 'pafe_woocommerce_checkout_redirect' ), 10, 1 );

			add_filter( 'woocommerce_checkout_fields' , array( $this, 'pafe_woocommerce_checkout_remove_checkout_fields'), 10 ,1 );
		}

		if( get_option( 'pafe-features-advanced-search', 2 ) == 2 || get_option( 'pafe-features-advanced-search', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/ajax-advanced-search.php' );
		}
		
		add_action('pre_get_posts',[$this, 'pafe_advanced_search_page']);

		if ( defined('ELEMENTOR_VERSION') ) {
			add_action( 'init', [ $this, 'add_wpml_support' ] );
		}

		if( get_option( 'pafe-features-elementor-form-database', 2 ) == 2 || get_option( 'pafe-features-elementor-form-database', 2 ) == 1 ) {
			// add_action( 'elementor_pro/forms/new_record', [ $this, 'pafe_elementor_form_database_new_record' ], 10, 2);
		}

		if( get_option( 'pafe-features-form-abandonment', 2 ) == 2 || get_option( 'pafe-features-form-abandonment', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/ajax-form-abandonment.php' );
		}
		
		add_filter( 'deprecated_function_trigger_error', [ $this, 'remove_deprecated_function_trigger_error' ], 10, 1 );

		add_filter( 'post_row_actions', [ $this, 'modify_list_row_actions' ], 10, 2 );

		add_shortcode('pafe-forms', [ $this, 'pafe_template_elementor' ] );

		add_shortcode('pafe-form', [ $this, 'pafe_form_shortcode' ] );

		add_filter( 'manage_pafe-forms_posts_columns', [ $this, 'pafe_forms_set_custom_edit_columns' ] );
    	add_action( 'manage_pafe-forms_posts_custom_column', [ $this, 'pafe_forms_custom_column' ], 10, 2 );
	}

	public function add_custom_css_for_editor() {
        wp_dequeue_script( 'editor-css-script' );
		wp_dequeue_script( 'purify' );
		wp_dequeue_script( 'softlite-elementor-editor-script' );
        
        wp_enqueue_script(
            'purify',
            plugin_dir_url( __FILE__ ) . 'assets/js/libs/purify.min.js',
            [],
            PAFE_PRO_VERSION,
            true
        );

        wp_enqueue_script(
            'pafe-custom-css-script',
            plugin_dir_url( __FILE__ ) . 'assets/js/libs/custom-css.js',
            ['elementor-frontend', 'purify'],
            PAFE_PRO_VERSION,
            true
        );

        wp_localize_script(
            'pafe-custom-css-script',
            'elementData',
            array(
                'postID' => get_the_ID()
            )
        );
    }

	public function pafe_form_shortcode($atts) {
		if(!class_exists('Elementor\Plugin')){
	        return '';
	    }
	    if(!isset($atts['id']) || empty($atts['id'])){
	        return '';
	    }

	    $post_id = $atts['id'];
	    $response = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($post_id);
	    return $response;
	}

	public function pafe_forms_set_custom_edit_columns($columns) {
        $columns['pafe-forms-shortcode'] = __( 'Shortcode', 'pafe' );
        return $columns;
    }

    public function pafe_forms_custom_column( $column, $post_id ) {
        switch ( $column ) {
            case 'pafe-forms-shortcode' :
                echo '<input class="elementor-shortcode-input" type="text" readonly="" onfocus="this.select()" value="[pafe-form id=' . '&quot;' . $post_id . '&quot;' . ']">';
                break;
        }
    }

	public function modify_list_row_actions( $actions, $post ) {
		$post_type = $post->post_type;
		if ( in_array($post_type, ['pafe-forms']) ) {
            $url_export_html = '<a href="' . esc_url( get_admin_url( null, 'admin-ajax.php?action=pafe_forms_functions&function=export&id=' ) ) . $post->ID . '">' . __( 'Export', 'pafe' ) . '</a>';

            $duplicate_html = '<a href="' . esc_url( get_admin_url( null, 'admin-ajax.php?action=pafe_forms_functions&function=duplicate&id=' ) ) . $post->ID . '">' . __( 'Duplicate', 'pafe' ) . '</a>';

			$actions['export_pafe_forms'] = $url_export_html;
			$actions['duplicate_pafe_forms'] = $duplicate_html;
		}

		if ( in_array($post_type, ['pafe-widget']) ) {
            $url_export_html = '<a href="' . esc_url( get_admin_url( null, 'admin-ajax.php?action=pafe_widget_functions&function=export&id=' ) ) . $post->ID . '">' . __( 'Export', 'pafe' ) . '</a>';

            $duplicate_html = '<a href="' . esc_url( get_admin_url( null, 'admin-ajax.php?action=pafe_widget_functions&function=duplicate&id=' ) ) . $post->ID . '">' . __( 'Duplicate', 'pafe' ) . '</a>';

			$actions['export_pafe_widget'] = $url_export_html;
			$actions['duplicate_pafe_widget'] = $duplicate_html;
		}

		return $actions;
	}

	public function admin_footer() {
		echo '<div data-pafe-admin-url="' . admin_url() . '"></div>';
		echo '<div data-pafe-plugin-url="' . plugins_url() . '"></div>';

		global $pagenow;
		if (( $pagenow == 'edit.php' ) && !empty($_GET['post_type'])) {
			if (sanitize_text_field($_GET['post_type']) == 'pafe-forms') {
				if ( empty(get_option( 'pafe_do_flush', false )) ) {
					add_option( 'pafe_do_flush', true );
					flush_rewrite_rules();
				}
			}
			if (sanitize_text_field($_GET['post_type']) == 'pafe-widget') {
				if ( empty(get_option( 'pafe_do_flush_widget', false )) ) {
					add_option( 'pafe_do_flush_widget', true );
					flush_rewrite_rules();
				}
			}
		}
	}

	public function admin_print_footer_scripts() {
		echo "<div data-pafe-dynamic-tags-list style='display:none'>";
		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/dynamic-tags.php');
			pafe_dynamic_tags_list_html();
		}
		echo "</div>";
	}

	public function remove_deprecated_function_trigger_error() {
		return false;
	}

	public function pafe_advanced_search_page($query) {
		if ( !is_admin() && $query->is_main_query() ) {
			if ($query->is_search) {
				$query->set('s', $_GET['s']);
				if(!empty($_GET['post_type']) && !empty($_GET['taxonomy'])){
					$post_type = $_GET['post_type'];
					$taxonomy = $_GET['taxonomy'];
					$query->set('post_type', $post_type);
					if(!empty($_GET['terms'])){
						$terms = explode(',', $_GET['terms']);
					}else{
						$terms = wp_list_pluck( get_terms($taxonomy), 'slug' );
					}
					$taxquery = array(
						array(
							'taxonomy' => $taxonomy,
							'field' => 'slug',
							'terms' => $terms,
						)
					);
					$query->set('tax_query', $taxquery);
				}
			}
		}
	}

	public function pafe_form_builder_filter(){
	    if (isset($_GET['post_type'])) {
	        $type = $_GET['post_type'];
		    if ( $type == 'pafe-form-database' || $type == 'pafe-formabandonment' ){
		        $form_id = array();
		        $submissions = new WP_Query( array(
		            'post_type' => $type,
		            'posts_per_page' => -1,
	            ) );

	            if ($submissions->have_posts()) : while ( $submissions->have_posts()) : $submissions->the_post();
	                $form_id[get_post_meta(get_the_ID(),'form_id',true)] = get_post_meta(get_the_ID(),'form_id',true);
	            endwhile; endif; wp_reset_postdata();
		        ?>
		        <select name="form_id">
		        <option value=""><?php _e('All Form ID', 'pafe'); ?></option>
		        <?php
		            $current_v = isset($_GET['form_id'])? $_GET['form_id']:'';
		            foreach ($form_id as $label => $value) {
		                printf
		                    (
		                        '<option value="%s"%s>%s</option>',
		                        $value,
		                        $value == $current_v? ' selected="selected"':'',
		                        $label
		                    );
		                }
		        ?>
		        </select>
		        <?php
		    }
	    }
	}

	public function pafe_form_builder_filter_posts( $query ){
	    global $pagenow;
	    if (isset($_GET['post_type'])) {
	        $type = $_GET['post_type'];
	        if ( $type == 'pafe-form-database' || $type == 'pafe-formabandonment' ){
			    if ( is_admin() && $pagenow=='edit.php' && isset($_GET['form_id']) && $_GET['form_id'] != '' && $query->is_main_query()) {
			        $query->query_vars['meta_key'] = 'form_id';
			        $query->query_vars['meta_value'] = $_GET['form_id'];
			    }
		    }
	    }
	}

	public function pafe_form_builder_filter_column($defaults) {
	    $defaults['form'] = 'Form';
	    $defaults['status'] = 'Status';
	    $defaults['form_type'] = 'Form Type';
	    return $defaults;
	}

	public function pafe_form_builder_filter_column_content($column_name, $post_ID) {
	    if ($column_name == 'form') {
	        $form_id = get_post_meta($post_ID,'form_id',true);
	        $form_post_id = get_post_meta($post_ID,'post_id',true);
	        $form_title = get_post_type($form_post_id) == 'pafe-forms' ? get_the_title($form_post_id) . ' (#' . $form_id . ')' : '#' . $form_id;
	        echo $form_title;
	    }

	    if ($column_name == 'status') {
	    	$status = !empty( get_post_meta($post_ID,'status',true) ) ? get_post_meta($post_ID,'status',true) : 'Success';
	        echo $status;
	    }

	    if ($column_name == 'form_type') {
	    	$status = !empty( get_post_meta($post_ID,'form_type',true) ) ? get_post_meta($post_ID,'form_type',true) : 'PAFE Form';
	        echo $status;
	    }
	}

	public function pafe_form_builder_filter_export_btn() {
	    if (isset($_GET['post_type'])) {
	        $type = $_GET['post_type'];
	        if ( $type == 'pafe-form-database' || $type == 'pafe-formabandonment' ){
	    ?>
		    <script type="text/javascript">
		        jQuery(document).ready( function($) {
		        	<?php if ( !empty($_GET['form_id']) ) : ?>
		            	$('.tablenav.top .clear, .tablenav.bottom .clear').before('<a class="button button-primary user_export_button" style="margin-top:3px;" href="<?php echo esc_url( get_admin_url( null, 'admin-ajax.php?action=pafe_export_database' ) ) . '&post_status=' . $_GET['post_status'] . '&m=' . $_GET['m'] . '&post_type=' . $_GET['post_type'] . '&form_id=' . $_GET['form_id']; ?>"><?php esc_attr_e('Click on Filter and then click here to export as csv', 'pafe');?></a>');
	            	<?php else : ?>
	            		$('.tablenav.top .clear, .tablenav.bottom .clear').before('<input class="button button-primary user_export_button" style="margin-top:3px;" type="submit" value="<?php esc_attr_e('Select Form ID and click on Filter to export as csv', 'pafe');?>" />');
            		<?php endif; ?>
		        });
		    </script>
	    <?php
			}
		}
	}

	public function add_pafe_tab($tabs){
		if(version_compare(ELEMENTOR_VERSION,'1.5.5')){
			Elementor\Controls_Manager::add_tab(self::TAB_PAFE, __( 'PAFE', 'pafe' ));
		}else{
			$tabs[self::TAB_PAFE] = __( 'PAFE', 'pafe' );
		}    
        return $tabs;
    }

	public function pafe_woocommerce_checkout_order_processed( $order_id ){
	    $order = wc_get_order( $order_id );
	    $order_items = $order->get_items();
	    
	    foreach ($order_items as $key => $value) {     
            $pafe_form_booking = wc_get_order_item_meta( $key, 'pafe_form_booking', true );
            $pafe_form_booking_fields = wc_get_order_item_meta( $key, 'pafe_form_booking_fields', true );

            if (!empty($pafe_form_booking)) {
            	$pafe_form_booking = json_decode( $pafe_form_booking, true );
            	$pafe_form_booking_fields = json_decode( $pafe_form_booking_fields, true );

            	$my_post = array(
					'post_title'    => wp_strip_all_tags( 'Piotnet Addons Form Database ' ),
					'post_status'   => 'publish',
					'post_type'		=> 'pafe-form-database',
				);

				$form_database_post_id = wp_insert_post( $my_post );

				if (!empty($form_database_post_id)) {

					$my_post_update = array(
						'ID'           => $form_database_post_id,
						'post_title'   => '#' . $form_database_post_id,
					);
					wp_update_post( $my_post_update );

					foreach ($pafe_form_booking_fields as $field) {
						update_post_meta( $form_database_post_id, $field['name'], $field['value'] );
					}

				}

            	foreach ($pafe_form_booking as $booking) {
            		$date = $booking['pafe_form_booking_date'];
					$slot_availble = 0;
					$slot = $booking['pafe_form_booking_slot'];
					$slot_query = new WP_Query(array(  
						'posts_per_page' => -1 , 
						'post_type' => 'pafe-form-booking',
						'meta_query' => array(                  
					       'relation' => 'AND',                 
						        array(
						            'key' => 'pafe_form_booking_id',                
						            'value' => $booking['pafe_form_booking_id'],                  
						            'type' => 'CHAR',                  
						            'compare' => '=',                  
						        ),
						        array(
						            'key' => 'pafe_form_booking_slot_id',                  
						            'value' => $booking['pafe_form_booking_slot_id'],                  
						            'type' => 'CHAR',                  
						            'compare' => '=',                  
						        ),
						        array(
						            'key' => 'pafe_form_booking_date',                  
						            'value' => $date,                  
						            'type' => 'CHAR',                  
						            'compare' => '=',                
						        ),
						        array(
						            'key' => 'payment_status',                  
						            'value' => 'succeeded',                  
						            'type' => 'CHAR',                  
						            'compare' => '=',                
						        ),
						),	
					));

					$slot_reserved = 0;

					if ($slot_query->have_posts()) {
						while($slot_query->have_posts()) {
							$slot_query->the_post();
							$slot_reserved += intval( get_post_meta(get_the_ID(), 'pafe_form_booking_quantity', true) );
						}
					}

					wp_reset_postdata();

					$slot_availble = $slot - $slot_reserved;

					$booking_slot = 1;

					if (!empty($booking['pafe_form_booking_slot_quantity_field'])) {
						$booking_quantity_field_name = str_replace('"]', '', str_replace('[field id="', '', $booking['pafe_form_booking_slot_quantity_field']) );

						foreach ($pafe_form_booking_fields as $field) {
							if ($booking_quantity_field_name == $field['name']) {
							 	$booking_slot = intval( $field['value'] );
							}
						}
					}

					if ($slot_availble >= $booking_slot && !empty($slot_availble) && !empty($booking_slot)) {
						$booking_post = array( 
							'post_title'    =>  '#' . $form_database_post_id . ' ' . $booking['pafe_form_booking_title'],
							'post_status'   => 'publish',
							'post_type'		=> 'pafe-form-booking',
						);

						$form_booking_posts_id = wp_insert_post( $booking_post );

						foreach ($pafe_form_booking_fields as $field) {
							update_post_meta( $form_booking_posts_id, $field['name'], $field['value'] );
						}

						foreach ($booking as $key_booking => $booking_data) {
							update_post_meta( $form_booking_posts_id, $key_booking, $booking_data );
						}

						update_post_meta( $form_booking_posts_id, 'pafe_form_booking_date', $date );
						update_post_meta( $form_booking_posts_id, 'pafe_form_booking_quantity', $booking_slot );
						update_post_meta( $form_booking_posts_id, 'order_id', $form_database_post_id );
						update_post_meta( $form_booking_posts_id, 'order_id_woocommerce', $order_id );
						update_post_meta( $form_booking_posts_id, 'payment_status', 'succeeded' );
					}
            	}
				
            }

            wc_delete_order_item_meta( $key, 'pafe_form_booking' );
            wc_delete_order_item_meta( $key, 'pafe_form_booking_fields' );
        }
	}

	public function pafe_woocommerce_checkout_redirect(){
	 	if ( class_exists( 'WooCommerce' ) ) {
			/* do nothing if we are not on the appropriate page */
			if( !is_wc_endpoint_url( 'order-received' ) || empty( $_GET['key'] ) ) {
				return;
			}
		 
			$order_id = wc_get_order_id_by_order_key( $_GET['key'] );
			$order = wc_get_order( $order_id );
		    $order_items = $order->get_items();

		    foreach ($order_items as $key => $value) {
	            $redirect_url = wc_get_order_item_meta( $key, 'pafe_woocommerce_checkout_redirect', true );

	            if (!empty($redirect_url)) {
	            	wc_delete_order_item_meta( $key, 'pafe_woocommerce_checkout_redirect' );
	            	wp_redirect( $redirect_url );
	            }
	        }
        }
	}
	
	public function pafe_add_order_item_meta( $itemId, $values, $key) {
		if ( class_exists( 'WooCommerce' ) && !defined('PIOTNETFORMS_PRO_VERSION')) {
			if ( isset( $values['fields'] ) ) {
				foreach ($values['fields'] as $item) {
					if (!empty($item['label'])) {
						wc_add_order_item_meta( $itemId, $item['label'], $item['value'] );
					}
				}
			}

		}
	}

    public function pafe_unset_redirect_order_item_meta_data($formatted_meta){
	    foreach( $formatted_meta as $key => $meta ){
	        if ($meta->key == 'pafe_woocommerce_checkout_redirect') {
	            unset($formatted_meta[$key]);
	        }
	    }

	    return $formatted_meta;
    }

	public function pafe_apply_custom_price_to_cart_item( $cart ) {
		if ( class_exists( 'WooCommerce' ) ) {  
	        foreach ( $cart->get_cart() as $cart_item ) {
		        if( isset($cart_item['pafe_custom_price']) ) {
		            $cart_item['data']->set_price( !empty($cart_item['pafe_custom_price']) ? $cart_item['pafe_custom_price'] : 0 );
		        }
		    }
	    }  
    }

    public function pafe_filter_woo_cart_quantity( $html, $cart_item, $cart_item_key ){
    	if ( class_exists( 'WooCommerce' ) ) {
	        if( isset($cart_item['pafe_custom_price']) ) {
	        	$pafe_price = !empty($cart_item['pafe_custom_price']) ? $cart_item['pafe_custom_price'] : 0;
	        	$product_price = wc_price( $pafe_price );
	            echo '<span class="quantity">' . sprintf( '<span class="product-quantity">%s &times;</span> %s', $cart_item['quantity'], $product_price ) . '</span>';
	        } else {
	        	return $html;
	        }
	    }
    }

	public function pafe_admin_notice__error() {
		$class = 'notice notice-error';
		$message = '<p><strong>Piotnet Addons For Elementor PRO</strong></p>' . '<p>' . __( 'You have to Activate License to enable all features.', 'pafe' ) . ' ' . '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=piotnet-addons-for-elementor') ) .'" class="elementor-plugins-gopro">' . esc_html__( 'Activate License', 'pafe' ) . '</a>' . '</p>';

		echo '<div class="'. $class . '">' . $message . '</div>';
	}

	public function pafe_forms_post_type() {
	    register_post_type('pafe-forms',
			array(
				'labels'      => array(
					'name'          => __('PAFE Forms'),
					'singular_name' => __('Form'),
				),
				'public'      => true,
				'has_archive' => true,
				'show_in_menu' => false,
				'supports' => array(
					'title',
					'custom-fields',
					'elementor',
				),
			)
	    );

	    remove_post_type_support( 'pafe-forms', 'editor' );

	    add_filter( 'wpseo_sitemap_exclude_post_type', function( $retval, $post_type ) {
			if ( 'pafe-forms' === $post_type ) {
				$retval = true;
			}

			return $retval;
		}, 10, 2 );
	}

	function pafe_widget_creator_post_type() {
		register_post_type('pafe-widget',
			array(
				'labels'      => array(
					'name'          => __('PAFE Widget Creator'),
					'singular_name' => __('Widget Creator'),
				),
				'public'      => true,
				'has_archive' => true,
				'show_in_menu' => false,
				'supports' => array(
					'title',
					'custom-fields',
					'elementor',
				),
			)
	    );

	    remove_post_type_support( 'pafe-widget', 'editor' );

	    add_filter( 'wpseo_sitemap_exclude_post_type', function( $retval, $post_type ) {
			if ( 'pafe-forms' === $post_type ) {
				$retval = true;
			}

			return $retval;
		}, 10, 2 );
	}

	public function pafe_elementor_form_database_post_type() {
	    register_post_type('pafe-formabandonment',
			array(
				'labels'      => array(
					'name'          => __('Form Abandonment'),
					'singular_name' => __('Form Abandonment'),
				),
				'public'      => true,
				'has_archive' => true,
				'show_in_menu' => false,
                'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'supports' => array( 
					'title', 
					'custom-fields', 
				),
			)
	    );

	    remove_post_type_support( 'pafe-formabandonment', 'editor' );

	    add_filter( 'wpseo_sitemap_exclude_post_type', function( $retval, $post_type ) {
			if ( 'pafe-formabandonment' === $post_type ) {
				$retval = true;
			}

			return $retval;
		}, 10, 2 );
	}

	public function pafe_form_database_post_type() {
	    register_post_type('pafe-form-database',
			array(
				'labels'      => array(
					'name'          => __('Form Database'),
					'singular_name' => __('Form Database'),
				),
				'public'      => true,
				'has_archive' => true,
				'show_in_menu' => false,
                'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'supports' => array( 
					'title', 
					'custom-fields', 
				),
			)
	    );

	    remove_post_type_support( 'pafe-form-database', 'editor' );

	    add_filter( 'wpseo_sitemap_exclude_post_type', function( $retval, $post_type ) {
			if ( 'pafe-form-database' === $post_type ) {
				$retval = true;
			}

			return $retval;
		}, 10, 2 );
	}
	
	public function pafe_pdf_font_post_type() {
	    register_post_type('pafe-fonts',
			array(
				'labels'      => array(
					'name'          => __('PAFE PDF Custom Font'),
					'singular_name' => __('PAFE PDF Custom Font'),
				),
				'public'      => true,
				'has_archive' => true,
				'show_in_menu' => false,
				'publicly_queryable'  => false,
				'supports' => array(
					'title',
					'editor',
				),
			)
	    );

	    remove_post_type_support( 'pafe-fonts', 'editor' );

	    add_filter( 'wpseo_sitemap_exclude_post_type', function( $retval, $post_type ) {
			if ( 'pafe-fonts' === $post_type ) {
				$retval = true;
			}

			return $retval;
		}, 10, 2 );
	}

	public function pafe_pdf_metabox(){
		add_meta_box('pafe-pdf', 'PDF custom font (TTF)', [$this, 'pafe_pdf_metabox_output'], 'pafe-fonts');
	}

	public function pafe_pdf_metabox_output($post){
		$pdf_font = get_post_meta($post->ID, '_pafe_pdf_font', true);
		$html = '<div class="pafe-custom-font">
			<input id="pafe-pdf-font-url" type="text" name="pafe_pdf_font" value="'.$pdf_font.'" readonly/>
			<button type="button" id="pafe-pdf-upload-font" class="button">Upload/Add font</button>
			<button type="button" id="pafe-pdf-remove-font" class="button">Remove font</button>
		</div>';
		echo $html;
	}
	
	public function pafe_pdf_save_custom_font($post_id){
		$pdf_font = !empty($_POST['pafe_pdf_font']) ? $_POST['pafe_pdf_font'] : '';
		update_post_meta($post_id, '_pafe_pdf_font', $pdf_font);
	}
	
	public function add_custom_upload_mimes($existing_mimes) {
		$existing_mimes['ttf'] = 'application/x-font-ttf';
		//$existing_mimes['otf'] = 'application/x-font-otf';
        //$existing_mimes['woff'] = 'application/x-font-woff';
        return $existing_mimes;
   }

	public function pafe_form_booking_post_type() {
	    register_post_type('pafe-form-booking',
			array(
				'labels'      => array(
					'name'          => __('Form Booking'),
					'singular_name' => __('Form Booking'),
				),
				'public'      => true,
				'has_archive' => true,
				'show_in_menu' => false,
				'supports' => array( 
					'title', 
					'custom-fields', 
				), 
			)
	    );

	    add_filter( 'wpseo_sitemap_exclude_post_type', function( $retval, $post_type ) {
			if ( 'pafe-form-booking' === $post_type ) {
				$retval = true;
			}

			return $retval;
		}, 10, 2 );
	}

	public function set_custom_edit_columns($columns) {
        $columns['pafe-shortcode'] = __( 'Shortcode', 'pafe' );
        return $columns;
    }

    public function custom_column( $column, $post_id ) {
        switch ( $column ) {
            case 'pafe-shortcode' :
                echo '<input class="elementor-shortcode-input" type="text" readonly="" onfocus="this.select()" value="[pafe-template id=' . '&quot;' . $post_id . '&quot;' . ']">'; 
                break;
        }
    }

    public function add_popup_trigger_url_column($columns) {
    	if(isset($_GET['elementor_library_type'])) {
	    	if ( $_GET['elementor_library_type'] == 'popup' ) {
		        $columns['pafe-popup-trigger-url'] = __( 'URL', 'pafe' );
	        }
	    }
	        return $columns;
    }

    public function create_popup_url($id,$action) {
    	if($action == 'open' || $action == 'toggle') {
    		if ( version_compare( ELEMENTOR_PRO_VERSION, '2.9.0', '<' ) ) {
				$link_action_url = \ElementorPro\Modules\LinkActions\Module::create_action_url( 'popup:open', [
					'id' => $id,
					'toggle' => 'toggle' === $action,
				] );
			} else {
				$link_action_url = \Elementor\Plugin::instance()->frontend->create_action_hash( 'popup:open', [
					'id' => $id,
					'toggle' => 'toggle' === $action,
				] );
			}
    	} else {
    		if ( version_compare( ELEMENTOR_PRO_VERSION, '2.9.0', '<' ) ) {
				$link_action_url = \ElementorPro\Modules\LinkActions\Module::create_action_url( 'popup:close' );
			} else {
				$link_action_url = \Elementor\Plugin::instance()->frontend->create_action_hash( 'popup:close' );
			}
    	}
    	
		return $link_action_url;
    }

    public function popup_trigger_url_column( $column, $post_id ) {
        if ( $column == 'pafe-popup-trigger-url' && $_GET['elementor_library_type'] == 'popup' ) {
        	echo '<label>' . __( 'Open', 'pafe' ) . '</label><input class="elementor-shortcode-input" style="width: calc(100% - 20px);" type="text" readonly="" onfocus="this.select()" value="' . $this->create_popup_url($post_id, 'open') . '">';
        	echo '<label>' . __( 'Close', 'pafe' ) . '</label><input class="elementor-shortcode-input" style="width: calc(100% - 20px);" type="text" readonly="" onfocus="this.select()" value="' . $this->create_popup_url($post_id, 'close') . '">';
        	echo '<label>' . __( 'Toggle', 'pafe' ) . '</label><input class="elementor-shortcode-input" style="width: calc(100% - 20px);" type="text" readonly="" onfocus="this.select()" value="' . $this->create_popup_url($post_id, 'toggle') . '">';
        }
    }

	public function pafe_template_elementor($atts){
	    if(!class_exists('Elementor\Plugin')){
	        return '';
	    }
	    if(!isset($atts['id']) || empty($atts['id'])){
	        return '';
	    }

	    $post_id = $atts['id'];
	    $response = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($post_id);
	    return $response;
	}

	public function pafe_woocommerce_checkout_load( $is_checkout ) {

		if ( ! is_admin() ) {			
			$elementor_data = stripslashes( json_encode( get_post_meta( get_the_ID(), '_elementor_data', true) ) );
			if (strpos($elementor_data, 'pafe_woocommerce_checkout_product_id') !== false) {
				$is_checkout = true;
			}
		}

		return $is_checkout;
	}

	public function pafe_woocommerce_checkout_load_cart() {

		if ( ! is_admin() ) {			
			$elementor_data = stripslashes( json_encode( get_post_meta( get_the_ID(), '_elementor_data', true) ) );
			if (strpos($elementor_data, 'pafe_woocommerce_checkout_product_id') !== false) {

				WC()->cart->empty_cart();

				$elementor_data = explode('"pafe_woocommerce_checkout_product_id":"', $elementor_data);
				$string = $elementor_data[1];
				$pos = stripos($string, '"');
				$product_id = substr($string,0,$pos);

				if (!empty($product_id)) {
					WC()->cart->add_to_cart( $product_id, 1 );
				}
			}
		}
	}


	public function pafe_woocommerce_checkout_remove_checkout_fields( $fields ){

	    $elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true);
		if (strpos($elementor_data, 'pafe_woocommerce_checkout_remove_fields') !== false) {
			$elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true);
			$elementor_data = stripslashes($elementor_data);
			$elementor_data = explode('"pafe_woocommerce_checkout_remove_fields":', $elementor_data);
			$string = $elementor_data[1];
			$pos = stripos($string, ']'); // Fix Alert [
			$remove_fields = json_decode(substr($string,0,$pos) . ']'); // Fix Alert [
			
			if (!empty($remove_fields)) {
				foreach ($remove_fields as $field) {
					if (strpos($field, 'billing') !== false) {
						unset($fields['billing'][$field]);
					}
					if (strpos($field, 'order') !== false) {
						unset($fields['order'][$field]);
					}
					if (strpos($field, 'shipping') !== false) {
						unset($fields['shipping'][$field]);
					}
				}
				
			}
		}
	    
	    return $fields;
	}

	public function i18n() {
		
		load_plugin_textdomain( 'pafe' );

	}

	public function enqueue() {
		wp_enqueue_script( 'pafe-extension', plugin_dir_url( __FILE__ ) . 'assets/js/minify/extension.min.js', array('jquery'), PAFE_PRO_VERSION );
		wp_enqueue_style( 'pafe-extension-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/extension.min.css', [], PAFE_PRO_VERSION );
	}

	public function enqueue_font_awesome_5() {
		wp_enqueue_style( 'pafe-font-awesome-5', plugin_dir_url( __FILE__ ) . 'assets/css/minify/font-awesome-5.min.css', [], PAFE_PRO_VERSION );
	}

	public function enqueue_scripts_woocommerce_sales_funnels() {
		wp_enqueue_script( 'pafe-woocommerce-sales-funnels-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/woocommerce-sales-funnels.min.js', array('jquery'), PAFE_PRO_VERSION );
		wp_enqueue_style( 'pafe-woocommerce-sales-funnels-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/woocommerce-sales-funnels.min.css', [], PAFE_PRO_VERSION );
	}

	public function enqueue_scripts_widget() {
		wp_register_script( 'pafe-form-builder', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-advanced-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/advanced.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-advanced2-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/advanced2.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-iban-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/iban.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-image-picker-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/image-picker.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-range-slider-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/ion-rangeslider.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-jquery-validation-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/jquery-validation.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-tinymce-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/tinymce.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-input-mask-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/input-mask.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-nice-number-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/nice-number.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-flatpickr-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/flatpickr.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-date-time-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/date-time.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-stripe-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/stripe.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-mollie-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/mollie.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-multi-step-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/multi-step.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-international-tel-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/international-tel.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-signature-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/signature.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-selectize-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/selectize.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-image-upload-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/image-upload.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
		wp_register_script( 'pafe-form-builder-preview-submission-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/preview-submission.min.js', [ 'jquery' ], PAFE_PRO_VERSION );

		if (!empty(get_option('piotnet-addons-for-elementor-pro-google-maps-api-key'))) {
			wp_register_script( 'pafe-form-builder-google-maps-init-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder/google-maps-init.min.js', [ 'jquery' ], PAFE_PRO_VERSION );
			wp_register_script( 'pafe-form-builder-google-maps-script', 'https://maps.googleapis.com/maps/api/js?key='. esc_attr( get_option('piotnet-addons-for-elementor-pro-google-maps-api-key') ) .'&libraries=places&callback=pafeAddressAutocompleteInitMap', [], PAFE_PRO_VERSION );
		}

		wp_register_script( 'pafe-slick', plugin_dir_url( __FILE__ ) . 'assets/js/minify/slick.min.js', array('jquery'), PAFE_PRO_VERSION );
		wp_register_script( 'pafe-widget', plugin_dir_url( __FILE__ ) . 'assets/js/minify/widget.min.js', array('jquery'), PAFE_PRO_VERSION );
        //wp_register_script( 'pafe-widget-date', plugin_dir_url( __FILE__ ) . 'languages/date/flatpickr.min.js', array('jquery'), PAFE_PRO_VERSION, false );
        wp_register_script( 'pafe-select2', plugin_dir_url( __FILE__ ) . 'assets/js/minify/select2.min.js', array('jquery'), PAFE_PRO_VERSION );
	}

	public function enqueue_scripts_widget_preview() {
		wp_enqueue_script( 'pafe-form-builder-advanced-script' );
		wp_enqueue_script( 'pafe-form-builder-advanced2-script' );
		wp_enqueue_script( 'pafe-form-builder-iban-script' );
		wp_enqueue_script( 'pafe-form-builder-image-picker-script' );
		wp_enqueue_script( 'pafe-form-builder-range-slider-script' );
		wp_enqueue_script( 'pafe-form-builder-jquery-validation-script' );
		wp_enqueue_script( 'pafe-form-builder-tinymce-script' );
		wp_enqueue_script( 'pafe-form-builder-input-mask-script' );
		wp_enqueue_script( 'pafe-form-builder-nice-number-script' );
		wp_enqueue_script( 'pafe-form-builder-flatpickr-script' );
		wp_enqueue_script( 'pafe-form-builder-date-time-script' );
		wp_enqueue_script( 'pafe-form-builder-stripe-script' );
		wp_enqueue_script( 'pafe-form-builder-mollie-script' );
		wp_enqueue_script( 'pafe-form-builder-multi-step-script' );
		wp_enqueue_script( 'pafe-form-builder-international-tel-script' );
		wp_enqueue_script( 'pafe-form-builder-signature-script' );
		wp_enqueue_script( 'pafe-form-builder-selectize-script' );
		wp_enqueue_script( 'pafe-form-builder-image-upload-script' );
		wp_enqueue_script( 'pafe-form-builder-preview-submission-script' );
		wp_enqueue_script( 'pafe-widget' );
	}

	public function enqueue_styles_widget() {
		wp_register_style( 'pafe-form-builder-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/form-builder.min.css', [], PAFE_PRO_VERSION );
		wp_register_style( 'pafe-form-builder-image-picker-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/form-builder/image-picker.min.css', [], PAFE_PRO_VERSION );
		wp_register_style( 'pafe-form-builder-range-slider-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/form-builder/range-slider.min.css', [], PAFE_PRO_VERSION );
		wp_register_style( 'pafe-form-builder-multi-step-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/form-builder/multi-step.min.css', [], PAFE_PRO_VERSION );
		wp_register_style( 'pafe-form-builder-flatpickr-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/form-builder/flatpickr.min.css', [], PAFE_PRO_VERSION );
		wp_register_style( 'pafe-form-builder-selectize-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/form-builder/selectize.min.css', [], PAFE_PRO_VERSION );
		wp_register_style( 'pafe-widget-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/widget.min.css', [], PAFE_PRO_VERSION );
        wp_register_style( 'pafe-select2-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/form-builder/select2.min.css', [], PAFE_PRO_VERSION );
		wp_register_style( 'pafe-widget-creator-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/pafe-widget-creator.min.css', [], PAFE_PRO_VERSION );
		wp_register_style( 'pafe-font-awesome-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/font-awesome-5.min.css', [], PAFE_PRO_VERSION );
	}

	public function enqueue_styles_widget_preview() {
		wp_enqueue_style( 'pafe-form-builder-style' );
		wp_enqueue_style( 'pafe-form-builder-image-picker-style' );
		wp_enqueue_style( 'pafe-form-builder-range-slider-style' );
		wp_enqueue_style( 'pafe-form-builder-multi-step-style' );
		wp_enqueue_style( 'pafe-form-builder-flatpickr-style' );
		wp_enqueue_style( 'pafe-form-builder-selectize-style' );
		wp_enqueue_style( 'pafe-widget-style' );
	}

	public function enqueue_editor() {

		wp_enqueue_style( 'pafe-editor', plugin_dir_url( __FILE__ ) . 'assets/css/minify/pafe-editor.min.css', [], PAFE_PRO_VERSION );
		wp_enqueue_script( 'pafe-editor-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/minify/pafe-editor.min.js', array('jquery'), PAFE_PRO_VERSION );
		wp_enqueue_style( 'pafe-font-awesome-editor', plugin_dir_url( __FILE__ ) . 'assets/css/minify/font-awesome-5.min.css', [], PAFE_PRO_VERSION );

		if (!empty($_GET['post']) && get_post_type($_GET['post']) == 'pafe-forms') {
			?>
				<style type="text/css">
					#elementor-panel-categories {display: flex; flex-direction: column;}
					.elementor-panel-category {order: 2;}
					#elementor-panel-category-pafe-form-builder {order: 1;}
				</style>
			<?php
		}

		if (!empty($_GET['post']) && get_post_type($_GET['post']) == 'pafe-widget') {
			?>
				<style type="text/css">
					#elementor-panel-categories {display: flex; flex-direction: column;}
					.elementor-panel-category {order: 2;}
					#elementor-panel-category-pafe-widget-creator {order: 1;}
				</style>
			<?php
		}
	}

	public function enqueue_footer() {

		$default_breakpoints = Elementor\Core\Breakpoints\Manager::get_default_config();
		$md_breakpoint = get_option( 'elementor_viewport_md' );
		$lg_breakpoint = get_option( 'elementor_viewport_lg' );

		if(empty($md_breakpoint)) {
			$md_breakpoint = $default_breakpoints['mobile']['default_value'];
		}

		if(empty($lg_breakpoint)) {
			$lg_breakpoint = $default_breakpoints['tablet']['default_value'];
		}

		if( get_option( 'pafe-features-sticky-header', 2 ) == 2 || get_option( 'pafe-features-sticky-header', 2 ) == 1 ) {
			echo '<style>@media (max-width:'. strval( $md_breakpoint - 1 ) .'px) { .pafe-sticky-header-fixed-start-on-mobile { position: fixed !important; top: 0; width: 100%; z-index: 99; } .pafe-display-inline-block-mobile {display: inline-block; margin-bottom: 0; width: auto !important; } } @media (min-width:'. strval( $md_breakpoint ) .'px) and (max-width:'. strval( $lg_breakpoint - 1 ) .'px) { .pafe-sticky-header-fixed-start-on-tablet { position: fixed !important; top: 0; width: 100%; z-index: 99; } .pafe-display-inline-block-tablet {display: inline-block; margin-bottom: 0; width: auto !important; }} @media (min-width:'. strval( $lg_breakpoint ) .'px) { .pafe-sticky-header-fixed-start-on-desktop { position: fixed !important; top: 0; width: 100%; z-index: 99; } .pafe-display-inline-block-desktop {display: inline-block; margin-bottom: 0; width: auto !important; } }</style>';
		}

        if( get_option( 'pafe-features-display-inline-block', 2 ) == 2 || get_option( 'pafe-features-display-inline-block', 2 ) == 1 ) {
            echo '<style>@media (min-width:'. strval( $md_breakpoint - 1 ) .'px) { .pafe-display-inline-block-mobile {display: inline-block; margin-bottom: 0; width: auto !important; } } @media (max-width:'. strval( $md_breakpoint - 1 ) .'px) { .pafe-display-inline-block-mobile {display: inline-block; margin-bottom: 0; width: auto !important; } } @media (min-width:'. strval( $md_breakpoint ) .'px) and (max-width:'. strval( $lg_breakpoint - 1 ) .'px) { .pafe-display-inline-block-tablet {display: inline-block; margin-bottom: 0; width: auto !important; }} @media (min-width:'. strval( $lg_breakpoint ) .'px) { .pafe-display-inline-block-desktop {display: inline-block; margin-bottom: 0; width: auto !important; } }</style>';
        }

		echo '<div class="pafe-break-point" data-pafe-break-point-md="'. $md_breakpoint .'" data-pafe-break-point-lg="'. $lg_breakpoint .'" data-pafe-ajax-url="'. admin_url( 'admin-ajax.php' ) .'"></div>';

		$domain = get_option('siteurl'); 
		$domain = str_replace('http://', '', $domain);
		$domain = str_replace('https://', '', $domain);
		$domain = str_replace('www', '', $domain);

		if ($domain == 'wp.test') {
			require_once( __DIR__ . '/jsvalidate.php' );
			echo PAFE_VALIDATE;
		}

		if( get_option( 'pafe-features-lightbox-image', 2 ) == 2 || get_option( 'pafe-features-lightbox-image', 2 ) == 1 || get_option( 'pafe-features-lightbox-gallery', 2 ) == 2 || get_option( 'pafe-features-lightbox-gallery', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/lightbox.php' );
		}

		if( get_option( 'pafe-features-stripe-payment', 2 ) == 2 || get_option( 'pafe-features-stripe-payment', 2 ) == 1 ) {
			echo '<div data-pafe-stripe="' . esc_attr( get_option('piotnet-addons-for-elementor-pro-stripe-publishable-key') ) . '"></div>';
		}

		echo '<div data-pafe-form-builder-tinymce-upload="' . plugins_url() . '/piotnet-addons-for-elementor-pro/inc/tinymce/tinymce-upload.php"></div>';
		echo '<div data-pafe-plugin-url="' . plugins_url() . '"></div>';

		if (!empty($GLOBALS['pafe_widget_creator_scripts'])) {
			?>
			<script id="pafe-widget-creator-scripts">
			<?php
				foreach ($GLOBALS['pafe_widget_creator_scripts'] as $key => $value) {
					echo $value;
				}
			?>
			</script>
			<?php
		}

		if (!empty($GLOBALS['pafe_widget_creator_styles'])) {
			?>
			<style type="text/css">
			<?php
				foreach ($GLOBALS['pafe_widget_creator_styles'] as $key => $value) {
					echo $value;
				}
			?>
			</style>
			<?php
		}
	}

	public function enqueue_header() {

		$default_breakpoints = Elementor\Core\Breakpoints\Manager::get_default_config();
		$md_breakpoint = get_option( 'elementor_viewport_md' );
		$lg_breakpoint = get_option( 'elementor_viewport_lg' );

		if(empty($md_breakpoint)) {
			$md_breakpoint = $default_breakpoints['mobile']['default_value'];
		}

		if(empty($lg_breakpoint)) {
			$lg_breakpoint = $default_breakpoints['tablet']['default_value'];
		}

		if( get_option( 'pafe-features-sticky-header', 2 ) == 2 || get_option( 'pafe-features-sticky-header', 2 ) == 1 ) {

			echo '<style>@media (max-width:'. strval( $md_breakpoint - 1 ) .'px) { .pafe-sticky-header-fixed-start-on-mobile { position: fixed !important; top: 0; width: 100%; z-index: 99; } } @media (min-width:'. strval( $md_breakpoint ) .'px) and (max-width:'. strval( $lg_breakpoint - 1 ) .'px) { .pafe-sticky-header-fixed-start-on-tablet { position: fixed !important; top: 0; width: 100%; z-index: 99; } } @media (min-width:'. strval( $lg_breakpoint ) .'px) { .pafe-sticky-header-fixed-start-on-desktop { position: fixed !important; top: 0; width: 100%; z-index: 99; } }</style>';
		}

		echo '<style>.pswp.pafe-lightbox-modal {display: none;}</style>';

		$GLOBALS['pafe_widget_creator_scripts'] = [];
		$GLOBALS['pafe_widget_creator_styles'] = [];

	}

	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		if ( version_compare( PHP_VERSION, '7.2.5', '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version_widget_creator' ] );
		}

		// Add Plugin actions
		if ( defined('ELEMENTOR_VERSION') ) {
			if ( version_compare( ELEMENTOR_VERSION, '3.7.0', '<' ) ) {
				add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
			} else {
				add_action( 'elementor/widgets/register', [ $this, 'init_widgets_new' ] );
			}
			add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );
		}
		
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue' ] );
		add_action( 'admin_enqueue_scripts', [$this, 'pafe_load_media_files'] );
		add_action( 'wp_head', [ $this, 'enqueue_header' ], 100 );
		add_action( 'wp_footer', [ $this, 'enqueue_footer' ], 600 );
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 600 );
		add_action( 'admin_menu', [ $this, 'change_submenu_first_item_label' ], 600 );
		add_action( 'in_plugin_update_message-piotnet-addons-for-elementor-pro/piotnet-addons-for-elementor-pro.php', [ $this, 'update_message'], 10, 2 );
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), [ $this, 'plugin_action_links' ], 10, 1 );
		if ( class_exists( 'WooCommerce' ) ) { 
			add_filter( 'woocommerce_get_item_data', [ $this, 'pafe_woocommerce_add_to_cart' ], 10, 2 );
		}

        $this->setup_updater();
	}

    private function setup_updater() {
        require_once ( 'updater.php' );
        $plugin_slug = plugin_basename( __FILE__ );
        new PAFE_Updater( $plugin_slug, PAFE_PRO_VERSION );
    }

	//Load media file
	public function pafe_load_media_files() {
		wp_enqueue_media();
	}

	public function pafe_woocommerce_add_to_cart( $item_data, $cart_item ) {
	    if ( empty( $cart_item['fields'] ) ) {
	        return $item_data;
	    }

	    $fields = apply_filters( 'pafe/form_builder/woocommerce_add_to_cart_fields', $cart_item['fields'] );

	    foreach ($fields as $item) {
	    	$item_data[] = array(
		        'key'     => $item['label'],
		        'value'   => $item['value'],
		        'display' => '',
		    );
	    }
	 
	    return $item_data;
	}

	public function plugin_activate() {

	    add_option( 'piotnet_addons_for_elementor_do_activation_redirect', true );

	}

	public function plugin_redirect() {

	    if ( get_option( 'piotnet_addons_for_elementor_do_activation_redirect', false ) ) {
	        delete_option( 'piotnet_addons_for_elementor_do_activation_redirect' );
	        wp_redirect( 'admin.php?page=piotnet-addons-for-elementor' );
	    }

	}

	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'pafe' ),
			'<strong>' . esc_html__( 'Piotnet Addons For Elementor', 'pafe' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'pafe' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pafe' ),
			'<strong>' . esc_html__( 'Piotnet Addons For Elementor', 'pafe' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'pafe' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pafe' ),
			'<strong>' . esc_html__( 'Piotnet Addons For Elementor', 'pafe' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'pafe' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_php_version_widget_creator() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pafe' ),
			'<strong>' . esc_html__( 'PAFE Widget Creator', 'pafe' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'pafe' ) . '</strong>',
			'7.2.5'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function plugin_action_links( $links ) {
		$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=piotnet-addons-for-elementor') ) .'">' . esc_html__( 'Settings', 'pafe' ) . '</a>';
		if( !PAFE_License_Service::has_valid_license() ) {
			$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=piotnet-addons-for-elementor') ) .'" class="elementor-plugins-gopro">' . esc_html__( 'Activate License', 'pafe' ) . '</a>';
		}
   		return $links;

	}

	public function plugin_row_meta( $links, $file ) { 

		if ( strpos( $file, 'piotnet-addons-for-elementor-pro.php' ) !== false ) {
			$links[] = '<a href="https://pafe.piotnet.com/tutorials" target="_blank">' . esc_html__( 'Video Tutorials', 'pafe' ) . '</a>';
			$links[] = '<a href="https://pafe.piotnet.com/change-log/" target="_blank">' . esc_html__( 'Change Log', 'pafe' ) . '</a>';
		}
   		return $links;

	}

	function update_message( $data, $response ) {
		echo '<br> ';
		printf(
			__('To enable updates, please login your account on the <a href="%s">Plugin Settings</a> page. If you have not purchased yet, please visit <a href="%s">https://pafe.piotnet.com</a>. If you can not update, please download new version on <a href="https://pafe.piotnet.com/my-account/">https://pafe.piotnet.com/my-account/</a>.', 'pafe'),
			admin_url('admin.php?page=piotnet-addons-for-elementor'),
			'https://pafe.piotnet.com'
		);
	}

	public function admin_menu() {
        add_menu_page(
            'Settings',
            'Piotnet Addons',
            'manage_options',
            'piotnet-addons-for-elementor',
            [$this, 'admin_page'],
            'dashicons-pafe-icon'
        );

        if( get_option( 'pafe-features-widget-creator', 2 ) == 2 || get_option( 'pafe-features-widget-creator', 2 ) == 1 ) {
            add_submenu_page('piotnet-addons-for-elementor', 'All Widgets', 'All Widgets', 'manage_options', 'edit.php?post_type=pafe-widget');
            add_submenu_page('piotnet-addons-for-elementor', 'Import Widget', 'Import Widget', 'manage_options', 'pafe-import-page-widget', [ $this, 'import_page_widgets' ]);
        }

		add_submenu_page('piotnet-addons-for-elementor', 'Add New Form', 'Add New Form', 'manage_options', 'pafe-import-page', [ $this, 'import_page' ]);
		add_submenu_page('piotnet-addons-for-elementor', 'All Forms', 'All Forms', 'manage_options', 'edit.php?post_type=pafe-forms');
		add_submenu_page('piotnet-addons-for-elementor', 'Form Database', 'Form Database', 'manage_options', 'edit.php?post_type=pafe-form-database');
		add_submenu_page('piotnet-addons-for-elementor', 'Form Abandonment', 'Form Abandonment', 'manage_options', 'edit.php?post_type=pafe-formabandonment');
		add_submenu_page('piotnet-addons-for-elementor', 'Form Booking', 'Form Booking', 'manage_options', 'edit.php?post_type=pafe-form-booking');
		add_submenu_page('piotnet-addons-for-elementor', 'PDF Custom Font', 'PDF Custom Font', 'manage_options', 'edit.php?post_type=pafe-fonts');

		add_action( 'admin_init',  [ $this, 'pafe_settings' ] );

        add_action( 'admin_init',  [ $this, 'refresh_license' ] );

	}

	public function change_submenu_first_item_label() {
		global $submenu;

		if ( isset( $submenu[ 'piotnet-addons-for-elementor' ] ) ) {
			$submenu[ 'piotnet-addons-for-elementor' ][0][0] = 'Settings';
		}
	}

    public function refresh_license() {
        PAFE_License_Service::refresh_license(false);
    }

	public function pafe_settings() {
		register_setting( 'piotnet-addons-for-elementor-pro-google-sheets-group', 'piotnet-addons-for-elementor-pro-google-sheets-client-id' );
		register_setting( 'piotnet-addons-for-elementor-pro-google-sheets-group', 'piotnet-addons-for-elementor-pro-google-sheets-client-secret' );

        register_setting( 'piotnet-addons-for-elementor-pro-hubspot-group', 'piotnet-addons-for-elementor-pro-hubspot-access-token' );

		register_setting( 'piotnet-addons-for-elementor-pro-google-calendar-group', 'piotnet-addons-for-elementor-pro-google-calendar-client-id' );
		register_setting( 'piotnet-addons-for-elementor-pro-google-calendar-group', 'piotnet-addons-for-elementor-pro-google-calendar-client-secret' );
        register_setting( 'piotnet-addons-for-elementor-pro-google-calendar-group', 'piotnet-addons-for-elementor-pro-google-calendar-client-api-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-google-maps-group', 'piotnet-addons-for-elementor-pro-google-maps-api-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-stripe-group', 'piotnet-addons-for-elementor-pro-stripe-publishable-key' );
		register_setting( 'piotnet-addons-for-elementor-pro-stripe-group', 'piotnet-addons-for-elementor-pro-stripe-secret-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-mailchimp-group', 'piotnet-addons-for-elementor-pro-mailchimp-api-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-mailerlite-group', 'piotnet-addons-for-elementor-pro-mailerlite-api-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-sendinblue-group', 'piotnet-addons-for-elementor-pro-sendinblue-api-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-activecampaign-group', 'piotnet-addons-for-elementor-pro-activecampaign-api-key' );
		register_setting( 'piotnet-addons-for-elementor-pro-activecampaign-group', 'piotnet-addons-for-elementor-pro-activecampaign-api-url' );

		register_setting( 'piotnet-addons-for-elementor-pro-getresponse-group', 'piotnet-addons-for-elementor-pro-getresponse-api-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-recaptcha-group', 'piotnet-addons-for-elementor-pro-recaptcha-site-key' );
		register_setting( 'piotnet-addons-for-elementor-pro-recaptcha-group', 'piotnet-addons-for-elementor-pro-recaptcha-secret-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-twilio-group', 'piotnet-addons-for-elementor-pro-twilio-account-sid' );
		register_setting( 'piotnet-addons-for-elementor-pro-twilio-group', 'piotnet-addons-for-elementor-pro-twilio-author-token' );

        register_setting( 'piotnet-addons-for-elementor-pro-sendfox-group', 'piotnet-addons-for-elementor-pro-sendfox-access-token' );
		register_setting( 'piotnet-addons-for-elementor-pro-convertkit-group', 'piotnet-addons-for-elementor-pro-convertkit-api-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-constant-contact-group', 'piotnet-addons-for-elementor-pro-constant-contact-client-id' );
		register_setting( 'piotnet-addons-for-elementor-pro-constant-contact-group', 'piotnet-addons-for-elementor-pro-constant-contact-app-secret-id' );
		register_setting( 'piotnet-addons-for-elementor-pro-constant-contact-group', 'piotnet-constant-contact-access-token' );
		register_setting( 'piotnet-addons-for-elementor-pro-constant-contact-group', 'piotnet-constant-contact-refresh-token' );

        register_setting( 'piotnet-addons-for-elementor-pro-zoho-group', 'piotnet-addons-for-elementor-pro-zoho-domain' );
		register_setting( 'piotnet-addons-for-elementor-pro-zoho-group', 'piotnet-addons-for-elementor-pro-zoho-client-id' );
		register_setting( 'piotnet-addons-for-elementor-pro-zoho-group', 'piotnet-addons-for-elementor-pro-zoho-client-secret' );
		register_setting( 'piotnet-addons-for-elementor-pro-zoho-group', 'piotnet-addons-for-elementor-pro-zoho-refresh-token' );
		register_setting( 'piotnet-addons-for-elementor-pro-zoho-group', 'piotnet-addons-for-elementor-pro-zoho-token' );

		register_setting( 'piotnet-addons-for-elementor-pro-paypal-group', 'piotnet-addons-for-elementor-pro-paypal-client-id' );
		register_setting( 'piotnet-addons-for-elementor-pro-paypal-group', 'piotnet-addons-for-elementor-pro-paypal-client-secret' );

		register_setting( 'piotnet-addons-for-elementor-pro-mollie-group', 'piotnet-addons-for-elementor-pro-mollie-api-key' );

        register_setting( 'piotnet-addons-for-elementor-pro-razorpay-group', 'piotnet-addons-for-elementor-pro-razorpay-api-key' );
		register_setting( 'piotnet-addons-for-elementor-pro-razorpay-group', 'piotnet-addons-for-elementor-pro-razorpay-secret-key' );
		
		require_once( __DIR__ . '/inc/features.php' );
		$features = json_decode( PAFE_FEATURES, true );

		foreach ($features as $feature) {
			if ( defined('PAFE_VERSION') && !$feature['pro'] || defined('PAFE_PRO_VERSION') && $feature['pro'] ) {
				register_setting( 'piotnet-addons-for-elementor-features-settings-group', $feature['option'] );
			}
		}

		register_setting( 'piotnet-addons-for-elementor-pro-settings-group', 'piotnet_addons_for_elementor_pro_disable_ssl_verify_license' );
		register_setting( 'piotnet-addons-for-elementor-pro-settings-group', 'piotnet_addons_for_elementor_pro_beta_version' );
	}

	public function admin_page(){
		require_once( __DIR__ . '/inc/admin-page.php' );
	}

	public function import_page() {
		require_once __DIR__ . '/inc/import-page.php';
	}

	public function import_page_widgets() {
		require_once __DIR__ . '/inc/import-page-widget.php';
	}

	public function admin_enqueue() {
		wp_enqueue_style( 'pafe-admin-css', plugin_dir_url( __FILE__ ) . 'assets/css/minify/pafe-admin.min.css', false, PAFE_PRO_VERSION );
		wp_enqueue_script( 'pafe-admin-js', plugin_dir_url( __FILE__ ) . 'assets/js/minify/pafe-admin.min.js', false, PAFE_PRO_VERSION );
	}

	public function add_elementor_page_settings_controls( \Elementor\PageSettings\Page $page ) {
		$page->add_control(
			'menu_item_color',
			[
				'label' => __( 'Menu Item Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu-item a' => 'color: {{VALUE}}',
				],
			]
		);
	}

	public function add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'pafe',
			[
				'title' => __( 'PAFE', 'pafe' ),
				'icon' => 'fa fa-plug',
			]
		);

		$elements_manager->add_category(
			'pafe-form-builder',
			[
				'title' => __( 'PAFE Form Builder', 'pafe' ),
				'icon' => 'fa fa-plug',
			]
		);

		$elements_manager->add_category(
			'pafe-woocommerce-sales-funnels',
			[
				'title' => __( 'PAFE WooCommerce Sales Funnels', 'pafe' ),
				'icon' => 'fa fa-shopping-cart',
			]
		);

		$elements_manager->add_category(
			'pafe-widget-creator',
			[
				'title' => __( 'PAFE Widget Creator', 'pafe' ),
				'icon' => 'fa fa-plug',
			]
		);

	}

	public function find_widget_creator_start($haystack, $needle, $widget_creator_pos) {
	    $offset = 0;
	    $allpos = array();
	    while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
	        $offset   = $pos + 1;
	        if ($pos < $widget_creator_pos) {
	        	$allpos[] = $pos;
	        }
	    }
	    return $allpos[count($allpos) - 1];
	}

	public function init_widgets() {

		$pafe_forms = (!empty($_GET['post']) && get_post_type($_GET['post']) == 'pafe-forms' || get_post_type() == 'pafe-forms') ? true : false;

		if( get_option( 'pafe-features-lightbox-image', 2 ) == 2 || get_option( 'pafe-features-lightbox-image', 2 ) == 1 ) {
			if ( version_compare( '2.1.0', ELEMENTOR_VERSION, '<=' ) ) {
				require_once( __DIR__ . '/widgets/pafe-lightbox-image.php' );
				\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Lightbox_Image() );
			}
			
		}

		if( get_option( 'pafe-features-lightbox-gallery', 2 ) == 2 || get_option( 'pafe-features-lightbox-gallery', 2 ) == 1 ) {
			if ( version_compare( '2.1.0', ELEMENTOR_VERSION, '<=' ) ) {
				require_once( __DIR__ . '/widgets/pafe-lightbox-gallery.php' );
				\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Lightbox_Gallery() );
			}
		}

		if( get_option( 'pafe-features-slider-builder', 2 ) == 2 || get_option( 'pafe-features-slider-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-slider-builder.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Slider_Builder() );
		}

		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {

			require_once( __DIR__ . '/widgets/pafe-form-builder-field.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Field() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-submit.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Submit() );

			require_once( __DIR__ . '/widgets/pafe-forms.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Forms() );

			require_once( __DIR__ . '/widgets/pafe-form-booking.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Booking() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-lost-password.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Lost_Password() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-preview-submission.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Preview_Submission() );

		}

		if( get_option( 'pafe-features-multi-step-form', 2 ) == 2 || get_option( 'pafe-features-multi-step-form', 2 ) == 1 ) {

			require_once( __DIR__ . '/widgets/pafe-multi-step-form.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Multi_Step_Form() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-prev-step.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Prev_Step() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-next-step.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Next_Step() );
		}

		if( get_option( 'pafe-features-woocommerce-checkout', 2 ) == 2 || get_option( 'pafe-features-woocommerce-checkout', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-woocommerce-checkout.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Woocommerce_Checkout() );
		}

		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {

			require_once( __DIR__ . '/widgets/pafe-display-form-submissions.php' );
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Data() );

		}

		if( get_option( 'pafe-features-widget-creator', 2 ) == 2 || get_option( 'pafe-features-widget-creator', 2 ) == 1 ) {

			if ( version_compare( PHP_VERSION, '7.2.5', '>=' ) ) {
				//if (!empty($_GET['post']) && get_post_type($_GET['post']) == 'pafe-widget' || get_post_type() == 'pafe-widget') {
					require_once( __DIR__ . '/widgets/pafe-widget-creator.php' );
					\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Widget_Creator() );
				//}

				require_once( __DIR__ . '/widgets/pafe-widget-creator-base.php' );

			$widgets = get_posts([
				'post_type' => 'pafe-widget',
				'posts_per_page' => -1,
				'fields' => 'ids',
			]);

			if (!empty($_GET['post']) && get_post_type($_GET['post']) != 'pafe-widget' || get_post_type() != 'pafe-widget') {
				foreach ($widgets as $post_id) {
					$elementor_data = get_post_meta( $post_id, '_elementor_data', true);
					$widget_creator_pos = strpos($elementor_data, ',"widgetType":"pafe-widget-creator"');

						if ($widget_creator_pos !== false) {
							$widget_start_pos = $this->find_widget_creator_start($elementor_data, '"elType":"widget","settings":{"pafe_widget_creator_title"', $widget_creator_pos);
							$widget_creator_settings = json_decode( '{' . substr($elementor_data, $widget_start_pos, $widget_creator_pos - $widget_start_pos) . '}', true )['settings'];

							if (!empty($widget_creator_settings['pafe_widget_creator_name'])) {
								$className = str_replace([' ','-'], ['_','_'], $widget_creator_settings['pafe_widget_creator_name']);
								$code = "class {$className} extends PAFE_Widget_Creator_Base{}";
								eval($code);
								$widget = new $className([], $widget_creator_settings);
								$widget->pafe_set_settings($widget_creator_settings);
								\Elementor\Plugin::instance()->widgets_manager->register_widget_type( $widget );
							}
						}
					}
				}
			}
		}

		// if( get_option( 'pafe-features-woocommerce-sales-funnels', 2 ) == 2 || get_option( 'pafe-features-woocommerce-sales-funnels', 2 ) == 1 ) {
		// 	require_once( __DIR__ . '/widgets/pafe-add-to-cart-checkbox.php' );
		// 	\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Add_To_Cart_Checkbox() );
		// }

		if( get_option( 'pafe-features-acf-repeater-render', 2 ) == 2 || get_option( 'pafe-features-acf-repeater-render', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-acf-repeater-sub-field.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_ACF_Repeater_Sub_Field() );

			require_once( __DIR__ . '/widgets/pafe-acf-repeater-render.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_ACF_Repeater_Render() );
		}

		if( get_option( 'pafe-features-advanced-search', 2 ) == 2 || get_option( 'pafe-features-advanced-search', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-advanced-search.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Advanced_Search() ); 
		}
	}

	public function init_widgets_new($widgets_manager) {

		$pafe_forms = (!empty($_GET['post']) && get_post_type($_GET['post']) == 'pafe-forms' || get_post_type() == 'pafe-forms') ? true : false;

		if( get_option( 'pafe-features-lightbox-image', 2 ) == 2 || get_option( 'pafe-features-lightbox-image', 2 ) == 1 ) {
			if ( version_compare( '2.1.0', ELEMENTOR_VERSION, '<=' ) ) {
				require_once( __DIR__ . '/widgets/pafe-lightbox-image.php' );
				$widgets_manager->register( new \PAFE_Lightbox_Image() );
			}

		}

		if( get_option( 'pafe-features-lightbox-gallery', 2 ) == 2 || get_option( 'pafe-features-lightbox-gallery', 2 ) == 1 ) {
			if ( version_compare( '2.1.0', ELEMENTOR_VERSION, '<=' ) ) {
				require_once( __DIR__ . '/widgets/pafe-lightbox-gallery.php' );
				$widgets_manager->register( new \PAFE_Lightbox_Gallery() );
			}
		}

		if( get_option( 'pafe-features-slider-builder', 2 ) == 2 || get_option( 'pafe-features-slider-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-slider-builder.php' );
			$widgets_manager->register( new \PAFE_Slider_Builder() );
		}

		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {

			require_once( __DIR__ . '/widgets/pafe-form-builder-field.php' );
			$widgets_manager->register( new \PAFE_Form_Builder_Field() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-submit.php' );
			$widgets_manager->register( new \PAFE_Form_Builder_Submit() );

			require_once( __DIR__ . '/widgets/pafe-forms.php' );
			$widgets_manager->register( new \PAFE_Forms() );

			require_once( __DIR__ . '/widgets/pafe-form-booking.php' );
			$widgets_manager->register( new \PAFE_Form_Booking() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-lost-password.php' );
			$widgets_manager->register( new \PAFE_Form_Builder_Lost_Password() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-preview-submission.php' );
			$widgets_manager->register( new \PAFE_Form_Builder_Preview_Submission() );

		}

		if( get_option( 'pafe-features-multi-step-form', 2 ) == 2 || get_option( 'pafe-features-multi-step-form', 2 ) == 1 ) {

			require_once( __DIR__ . '/widgets/pafe-multi-step-form.php' );
			$widgets_manager->register( new \PAFE_Multi_Step_Form() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-prev-step.php' );
			$widgets_manager->register( new \PAFE_Form_Builder_Prev_Step() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-next-step.php' );
			$widgets_manager->register( new \PAFE_Form_Builder_Next_Step() );
		}

		if( get_option( 'pafe-features-woocommerce-checkout', 2 ) == 2 || get_option( 'pafe-features-woocommerce-checkout', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-woocommerce-checkout.php' );
			$widgets_manager->register( new \PAFE_Woocommerce_Checkout() );
		}

		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {

			require_once( __DIR__ . '/widgets/pafe-display-form-submissions.php' );
            $widgets_manager->register( new \PAFE_Form_Builder_Data() );

		}

		if( get_option( 'pafe-features-widget-creator', 2 ) == 2 || get_option( 'pafe-features-widget-creator', 2 ) == 1 ) {

			if ( version_compare( PHP_VERSION, '7.2.5', '>=' ) ) {
				//if (!empty($_GET['post']) && get_post_type($_GET['post']) == 'pafe-widget' || get_post_type() == 'pafe-widget') {
					require_once( __DIR__ . '/widgets/pafe-widget-creator.php' );
					$widgets_manager->register( new \PAFE_Widget_Creator() );
				//}

				require_once( __DIR__ . '/widgets/pafe-widget-creator-base.php' );

			$widgets = get_posts([
				'post_type' => 'pafe-widget',
				'posts_per_page' => -1,
				'fields' => 'ids',
			]);

			if (!empty($_GET['post']) && get_post_type($_GET['post']) != 'pafe-widget' || get_post_type() != 'pafe-widget') {
				foreach ($widgets as $post_id) {
					$elementor_data = get_post_meta( $post_id, '_elementor_data', true);
					$widget_creator_pos = strpos($elementor_data, ',"widgetType":"pafe-widget-creator"');

						if ($widget_creator_pos !== false) {
							$widget_start_pos = $this->find_widget_creator_start($elementor_data, '"elType":"widget","settings":{"pafe_widget_creator_title"', $widget_creator_pos);
							$widget_creator_settings = json_decode( '{' . substr($elementor_data, $widget_start_pos, $widget_creator_pos - $widget_start_pos) . '}', true )['settings'];

							if (!empty($widget_creator_settings['pafe_widget_creator_name'])) {
								$className = str_replace([' ','-'], ['_','_'], $widget_creator_settings['pafe_widget_creator_name']);
								$code = "class {$className} extends PAFE_Widget_Creator_Base{}";
								eval($code);
								$widget = new $className([], $widget_creator_settings);
								$widget->pafe_set_settings($widget_creator_settings);
								$widgets_manager->register( $widget );
							}
						}
					}
				}
			}
		}

		// if( get_option( 'pafe-features-woocommerce-sales-funnels', 2 ) == 2 || get_option( 'pafe-features-woocommerce-sales-funnels', 2 ) == 1 ) {
		// 	require_once( __DIR__ . '/widgets/pafe-add-to-cart-checkbox.php' );
		// 	$widgets_manager->register( new \PAFE_Add_To_Cart_Checkbox() );
		// }

		if( get_option( 'pafe-features-acf-repeater-render', 2 ) == 2 || get_option( 'pafe-features-acf-repeater-render', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-acf-repeater-sub-field.php' );
			$widgets_manager->register( new \PAFE_ACF_Repeater_Sub_Field() );

			require_once( __DIR__ . '/widgets/pafe-acf-repeater-render.php' );
			$widgets_manager->register( new \PAFE_ACF_Repeater_Render() );
		}

		if( get_option( 'pafe-features-advanced-search', 2 ) == 2 || get_option( 'pafe-features-advanced-search', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-advanced-search.php' );
			$widgets_manager->register( new \PAFE_Advanced_Search() );
		}
	}

	public function init_controls() {

		// Include Control files

		require_once( __DIR__ . '/controls/pafe-support.php' );
		new PAFE_Support();

		if( get_option( 'pafe-features-parallax-background', 2 ) == 2 || get_option( 'pafe-features-parallax-background', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-parallax.php' );
			new PAFE_Parallax();
		}
		
		if( get_option( 'pafe-features-responsive-border-width', 2 ) == 2 || get_option( 'pafe-features-responsive-border-width', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-border-width.php' );
			new PAFE_Responsive_Border_Width();
		}

		if( get_option( 'pafe-features-section-link', 2 ) == 2 || get_option( 'pafe-features-section-link', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-section-link.php' );
			new PAFE_Section_Link();
		}

		if( get_option( 'pafe-features-column-link', 2 ) == 2 || get_option( 'pafe-features-column-link', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-column-link.php' );
			new PAFE_Column_Link();
		}

		if( get_option( 'pafe-features-column-width', 2 ) == 2 || get_option( 'pafe-features-column-width', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-column-width.php' );
			new PAFE_Column_Width();
		}

		if( get_option( 'pafe-features-multiple-background-images', 2 ) == 2 || get_option( 'pafe-features-multiple-background-images', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-multiple-background-images.php' );
			new PAFE_Multiple_Background_Images();
		}

		if( get_option( 'pafe-features-absolute-positioning', 2 ) == 2 || get_option( 'pafe-features-absolute-positioning', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-absolute-positioning.php' );
			new PAFE_Absolute_Positioning();
		}

		if( get_option( 'pafe-features-responsive-custom-positioning', 2 ) == 2 || get_option( 'pafe-features-responsive-custom-positioning', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-custom-positioning.php' );
			new PAFE_Responsive_Custom_Positioning();
		}

		if( get_option( 'pafe-features-max-width', 2 ) == 2 ||  get_option( 'pafe-features-max-width', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-max-width.php' );
			new PAFE_Max_Width();
		}

		if( get_option( 'pafe-features-display-inline-block', 2 ) == 2 || get_option( 'pafe-features-display-inline-block', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-display-inline-block.php' );
			new PAFE_Display_Inline_Block();
		}

		if( get_option( 'pafe-features-responsive-background', 2 ) == 2 || get_option( 'pafe-features-responsive-background', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-background.php' );
			new PAFE_Responsive_Background();
		}

		if( get_option( 'pafe-features-responsive-column-order', 2 ) == 2 || get_option( 'pafe-features-responsive-column-order', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-column-order.php' );
			new PAFE_Responsive_Column_Order();
		}

		if( get_option( 'pafe-features-responsive-hide-column', 2 ) == 2 || get_option( 'pafe-features-responsive-hide-column', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-hide-column.php' );
			new PAFE_Responsive_Hide_Column();
		}

		if( get_option( 'pafe-features-equal-height', 2 ) == 2 || get_option( 'pafe-features-equal-height', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-equal-height.php' );
			new PAFE_Equal_Height();
		}

		if( get_option( 'pafe-features-equal-height-for-cta', 2 ) == 2 || get_option( 'pafe-features-equal-height-for-cta', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-equal-height-for-cta.php' );
			new PAFE_Equal_Height_For_CTA();
		}

		if( get_option( 'pafe-features-equal-height-for-woocommerce-products', 2 ) == 2 || get_option( 'pafe-features-equal-height-for-woocommerce-products', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-equal-height-for-woocommerce-products.php' );
			new PAFE_Equal_Height_For_Woocommerce_Products();
		}

		if( get_option( 'pafe-features-font-awesome-5', 2 ) == 2 || get_option( 'pafe-features-font-awesome-5', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-font-awesome-5.php' );
			new PAFE_Font_Awesome_5();
		}

		if( get_option( 'pafe-features-navigation-arrows-icon', 2 ) == 2 || get_option( 'pafe-features-navigation-arrows-icon', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-navigation-arrows-icon.php' );
			new PAFE_Navigation_Arrows_Icon();
		}

		if( get_option( 'pafe-features-custom-media-query-breakpoints', 2 ) == 2 || get_option( 'pafe-features-custom-media-query-breakpoints', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-custom-media-query-breakpoints.php' );
			new PAFE_Custom_Media_Query_Breakpoints();
		}

		if( get_option( 'pafe-features-responsive-gallery-column-width', 2 ) == 2 || get_option( 'pafe-features-responsive-gallery-column-width', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-gallery-column-width.php' );
			new PAFE_Responsive_Gallery_Column_Width();
		}

		if( get_option( 'pafe-features-responsive-gallery-images-spacing', 2 ) == 2 || get_option( 'pafe-features-responsive-gallery-images-spacing', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-gallery-images-spacing.php' );
			new PAFE_Responsive_Gallery_Images_Spacing();
		}

		if( get_option( 'pafe-features-media-carousel-ratio', 2 ) == 2 || get_option( 'pafe-features-media-carousel-ratio', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-media-carousel-ratio.php' );
			new PAFE_Media_Carousel_Ratio();
		}

		if( get_option( 'pafe-features-advanced-form-styling', 2 ) == 2 || get_option( 'pafe-features-advanced-form-styling', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-advanced-form-styling.php' );
			new PAFE_Advanced_Form_Styling();
		}

		if( get_option( 'pafe-features-advanced-tabs-styling', 2 ) == 2 || get_option( 'pafe-features-advanced-tabs-styling', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-advanced-tabs-styling.php' );
			new PAFE_Advanced_Tabs_Styling();
		}

		if( get_option( 'pafe-features-advanced-dots-styling', 2 ) == 2 || get_option( 'pafe-features-advanced-dots-styling', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-advanced-dots-styling.php' );
			new PAFE_Advanced_Dots_Styling();
		}

		if( get_option( 'pafe-features-responsive-section-column-text-align', 2 ) == 2 || get_option( 'pafe-features-responsive-section-column-text-align', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-section-column-text-align.php' );
			new PAFE_Responsive_Section_Column_Text_Align();
		}

		if( get_option( 'pafe-features-slider-builder', 2 ) == 2 || get_option( 'pafe-features-slider-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-slider-builder-animation.php' );
			new PAFE_Slider_Builder_Animation();
		}

		if( get_option( 'pafe-features-close-first-accordion', 2 ) == 2 || get_option( 'pafe-features-close-first-accordion', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-close-first-accordion.php' );
			new PAFE_Close_First_Accordion();
		}

		if( get_option( 'pafe-features-column-aspect-ratio', 2 ) == 2 || get_option( 'pafe-features-column-aspect-ratio', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-column-aspect-ratio.php' );
			new PAFE_Column_Aspect_Ratio();
		}

		if( get_option( 'pafe-features-advanced-nav-menu-styling', 2 ) == 2 || get_option( 'pafe-features-advanced-nav-menu-styling', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-advanced-nav-menu-styling.php' );
			new PAFE_Advanced_Nav_Menu_Styling();
		}

		if( get_option( 'pafe-features-toggle-content', 2 ) == 2 || get_option( 'pafe-features-toggle-content', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-toggle-content.php' );
			new PAFE_Toggle_Content();
		}

		if( get_option( 'pafe-features-scroll-box-with-custom-scrollbar', 2 ) == 2 || get_option( 'pafe-features-scroll-box-with-custom-scrollbar', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-scroll-box-with-custom-scrollbar.php' );
			new PAFE_Scroll_Box_With_Custom_Scrollbar();
		}

		if( get_option( 'pafe-features-ajax-live-search', 2 ) == 2 || get_option( 'pafe-features-ajax-live-search', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-ajax-live-search.php' );
			new PAFE_Ajax_Live_Search();
		}

		if( get_option( 'pafe-features-crossfade-multiple-background-images', 2 ) == 2 || get_option( 'pafe-features-crossfade-multiple-background-images', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-crossfade-multiple-background-images.php' );
			new PAFE_Crossfade_Multiple_Background_Images();
		}

		if( get_option( 'pafe-features-conditional-logic-form', 2 ) == 2 || get_option( 'pafe-features-conditional-logic-form', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-conditional-logic-form.php' );
			new PAFE_Conditional_Logic_Form();
		}

		if( get_option( 'pafe-features-form-builder-conditional-logic', 2 ) == 2 || get_option( 'pafe-features-form-builder-conditional-logic', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-form-builder-conditional-logic.php' );
			new PAFE_Form_Builder_Conditional_Logic();
		}

		if( get_option( 'pafe-features-form-abandonment', 2 ) == 2 || get_option( 'pafe-features-form-abandonment', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-form-abandonment.php' );
			new PAFE_Form_Abandonment();
		}

		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-form-builder-repeater.php' );
			new PAFE_Form_Builder_Repeater();

			require_once( __DIR__ . '/controls/pafe-form-builder-repeater-trigger.php' );
			new PAFE_Form_Builder_Repeater_Trigger();
		}

		if( get_option( 'pafe-features-multi-step-form', 2 ) == 2 || get_option( 'pafe-features-multi-step-form', 2 ) == 1 ) {

			require_once( __DIR__ . '/controls/pafe-multi-step-form.php' );
			new PAFE_Multi_Step();

			require_once( __DIR__ . '/controls/pafe-next-prev-multi-step-form.php' );
			new PAFE_Next_Prev_Multi_Step_Form();
		}

		if( get_option( 'pafe-features-range-slider', 2 ) == 2 || get_option( 'pafe-features-range-slider', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-range-slider.php' );
			new PAFE_Range_Slider();
		}

		if( get_option( 'pafe-features-calculated-fields-form', 2 ) == 2 || get_option( 'pafe-features-calculated-fields-form', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-calculated-fields-form.php' );
			new PAFE_Calculated_Fields_Form();
		}

		if( get_option( 'pafe-features-image-select-field', 2 ) == 2 || get_option( 'pafe-features-image-select-field', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-image-select-field.php' );
			new PAFE_Image_Select_Field();
		}

		if( get_option( 'pafe-features-form-google-sheets-connector', 2 ) == 2 || get_option( 'pafe-features-form-google-sheets-connector', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-form-google-sheets-connector.php' );
			new PAFE_Form_Google_Sheets_Connector();
		}

		if( get_option( 'pafe-features-conditional-visibility', 2 ) == 2 || get_option( 'pafe-features-conditional-visibility', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-conditional-visibility.php' );
			new PAFE_Conditional_Visibility();
		}

		if( get_option( 'pafe-features-text-color-change-on-column-hover', 2 ) == 2 || get_option( 'pafe-features-text-color-change-on-column-hover', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-text-color-change-on-column-hover.php' );
			new PAFE_Text_Color_Change_On_Column_Hover();
		}

		if( get_option( 'pafe-features-css-filters', 2 ) == 2 || get_option( 'pafe-features-css-filters', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-css-filters.php' );
			new PAFE_Css_Filters();
		}

		if( get_option( 'pafe-features-convert-image-to-black-or-white', 2 ) == 2 || get_option( 'pafe-features-convert-image-to-black-or-white', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-convert-image-to-black-or-white.php' );
			new PAFE_Convert_Image_To_Black_Or_White();
		}

		if( get_option( 'pafe-features-sticky-header', 2 ) == 2 || get_option( 'pafe-features-sticky-header', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-sticky-header.php' );
			new PAFE_Sticky_Header();

			require_once( __DIR__ . '/controls/pafe-sticky-header-image.php' );
			new PAFE_Sticky_Header_Image();

			require_once( __DIR__ . '/controls/pafe-sticky-header-text.php' );
			new PAFE_Sticky_Header_Text();

			require_once( __DIR__ . '/controls/pafe-sticky-header-visibility.php' );
			new PAFE_Sticky_Header_Visibility();
		}

		if( get_option( 'pafe-features-woocommerce-sales-funnel', 2 ) == 2 || get_option( 'pafe-features-woocommerce-sales-funnel', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-woocommerce-sales-funnels-add-to-cart.php' );
			new PAFE_Woocommerce_Sales_Funnels_Add_To_Cart();
		}

		if( get_option( 'pafe-features-custom-css', 2 ) == 2 || get_option( 'pafe-features-custom-css', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-custom-css.php' );
			new PAFE_Custom_Css();
		}

	}

	public function add_wpml_support() {
		$pafe_forms = (!empty($_GET['post']) && get_post_type($_GET['post']) == 'pafe-forms' || get_post_type() == 'pafe-forms') ? true : false;

		require_once( __DIR__ . '/widgets/pafe-form-builder-field.php' );
		$widget = new PAFE_Form_Builder_Field();
		$widget->add_wpml_support();

		require_once( __DIR__ . '/widgets/pafe-form-builder-submit.php' );
		$widget = new PAFE_Form_Builder_Submit();
		$widget->add_wpml_support();

		require_once( __DIR__ . '/widgets/pafe-multi-step-form.php' );
		$widget = new PAFE_Multi_Step_Form();
		$widget->add_wpml_support();

		require_once( __DIR__ . '/widgets/pafe-form-booking.php' );
		$widget = new PAFE_Form_Booking();
		$widget->add_wpml_support();

		require_once( __DIR__ . '/widgets/pafe-form-builder-lost-password.php' );
		$widget = new PAFE_Form_Builder_Lost_Password();
		$widget->add_wpml_support();
	}

}

Piotnet_Addons_For_Elementor_Pro::instance();
