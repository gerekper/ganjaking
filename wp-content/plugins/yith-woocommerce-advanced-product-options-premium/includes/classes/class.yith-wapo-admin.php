<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WAPO' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WAPO_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WAPO_Admin {
		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version;

		/* @var YIT_Plugin_Panel_WooCommerce */
		protected $_panel;

		/**
		 * @var string Main Panel Option
		 */
		protected $_main_panel_option;

		/**
         * @var $_premium string Premium tab template file name
         */
        protected $_premium = 'premium.php';

		/**
		 * @var string The panel page
		 */
		protected $_panel_page = 'yith_wapo_panel';

		/**
		 * @var string Official plugin documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-product-add-ons';

		/**
		 * @var string Official plugin landing page
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-product-add-ons';

		/**
		 * @var string Official live demo
		 */
		protected $_premium_live = 'http://plugins.yithemes.com/yith-woocommerce-product-add-ons';

		public static $variations_chosen_list = array();

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct( $version ) {

			$this->version = $version;

			// Actions
			add_action( 'init', array( $this, 'init' ) );

			// Admin Menu
			add_filter( 'ywapo_edit_advanced_product_options_capability' , array( $this, 'ywapo_get_capability' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5) ;
			
			// WooCommerce Product Data Tab
			add_action( 'admin_init', array( $this, 'add_wc_product_data_tab' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'woo_add_custom_general_fields_save' ) );

			if ( isset( $_GET['page'] ) && (
				$_GET['page'] == 'yith_wapo_groups' ||
				$_GET['page'] == 'yith_wapo_group' ||
				$_GET['page'] == 'yith_wapo_group_addons'
			) ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
			}

			if ( ! defined( 'YITH_WAPO_PREMIUM' ) || ! YITH_WAPO_PREMIUM ) {
				// Type Options Template
				add_action( 'yith_wapo_type_options_template', array( $this, 'type_options_template' ), 10, 1 );
				// Depend Variations Template
				add_action( 'yith_wapo_depend_variations_template', array( $this, 'depend_variations_template' ), 10, 2 );
				// Addon Operator Template
				add_action( 'yith_wapo_addon_operator_template', array( $this, 'addon_operator_template' ), 10, 1 );
				// Addon Options Template
				add_action( 'yith_wapo_addon_options_template', array( $this, 'addon_options_template' ), 10, 1 );
			} else {
				// Register plugin to licence/update system
				add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
				add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			}

			// Admin Init
			add_action( 'admin_init', array( $this, 'items_update' ), 9 );

			add_action( 'wp_ajax_ywcp_add_new_option', array( $this, 'add_new_option' ) );

			// Show Plugin Information
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WAPO_DIR . '/' . basename( YITH_WAPO_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// YITH WAPO Loaded
			do_action( 'yith_wapo_loaded' );

		}


		/**
		 * Init method:
		 *  - default options
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function init() { }

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general'       => __( 'General', 'yith-woocommerce-product-add-ons' ),
			);
			if ( ! defined( 'YITH_WAPO_PREMIUM' ) || ! YITH_WAPO_PREMIUM ) {
				$admin_tabs['premium'] = __( 'Premium Version', 'yith-woocommerce-product-add-ons' );
				add_action( 'ywapo_premium_tab', array( $this, 'premium_tab' ) );
			} else if ( defined( 'YITH_WAPO_WCCL' ) ) {
				$admin_tabs['variations'] = __( 'Variations', 'yith-woocommerce-product-add-ons' );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Product Add-Ons', 'yith-woocommerce-product-add-ons' ),
				'menu_title'       => __( 'Product Add-Ons', 'yith-woocommerce-product-add-ons' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'links'            => $this->get_panel_sidebar_links(),
				'admin-tabs'       => apply_filters( 'yith-wapo-admin-tabs', $admin_tabs ),
				'options-path'     => YITH_WAPO_DIR . '/plugin-options'
			);

			/* === Fixed: not updated theme  === */
			if( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WAPO_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

			add_action( 'woocommerce_admin_field_yith_wapo_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );

		}

        public function premium_tab() {
            $premium_tab_template = YITH_WAPO_TEMPLATE_ADMIN_PATH  . $this->_premium;
            if ( file_exists( $premium_tab_template ) ) {
                include_once( $premium_tab_template );
            }
        }

		/**
			* @return array
		 */
		public function get_panel_sidebar_links() {
			return array(
				array(
					'url' => $this->_official_documentation,
					'title' => __( 'Plugin Documentation' , 'yith-woocommerce-product-add-ons' ),
				),
				array(
					'url' => 'https://yithemes.com/my-account/support/dashboard',
					'title' => __( 'Support platform' , 'yith-woocommerce-product-add-ons' ),
				),
				array(
					'url' => $this->_official_documentation.'/changelog',
					'title' => 'Changelog ( '.YITH_WAPO_VERSION.' )',
				)
			);
		}

		/**
		 * @author Andre Frascaspata
		 * @param $capability
		 * @return string
		 */
		public function ywapo_get_capability( $capability ) {

			if( YITH_WAPO::$is_vendor_installed ) {

				$vendor = yith_get_vendor('current', 'user');

				if( $vendor->is_valid() && $vendor->has_limited_access() && YITH_WAPO::is_plugin_enabled_for_vendors() ) {
					$capability = YITH_Vendors()->admin->get_special_cap();
				}

			}

			return $capability;

		}

		/**
		 * Admin menu
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function admin_menu() {

			$capability = apply_filters( 'ywapo_edit_advanced_product_options_capability', 'manage_woocommerce' );

			$page = add_submenu_page(
				'edit.php?post_type=product',
				__( 'Add-ons', 'yith-woocommerce-product-add-ons' ),
				__( 'Add-ons', 'yith-woocommerce-product-add-ons' ),
				$capability,
				'yith_wapo_groups',
				array( $this, 'yith_wapo_groups' )
			);
			$page = add_submenu_page(
				null,
				__( 'Add-ons Group', 'yith-woocommerce-product-add-ons' ),
				__( 'Add-ons Group', 'yith-woocommerce-product-add-ons' ),
				$capability,
				'yith_wapo_group',
				array( $this, 'yith_wapo_group' )
			);
			$page = add_submenu_page(
				null,
				__( 'Add-ons Options', 'yith-woocommerce-product-add-ons' ),
				__( 'Add-ons Options', 'yith-woocommerce-product-add-ons' ),
				$capability,
				'yith_wapo_group_addons',
				array( $this, 'yith_wapo_group_addons' )
			);
		}

		/**
		 * WAPO Admin
		 *
		 * @access public
		 * @since 1.0.0
		 */
		function yith_wapo_groups() { require YITH_WAPO_DIR . '/templates/admin/yith-wapo-groups.php'; }
		function yith_wapo_group() { require YITH_WAPO_DIR . '/templates/admin/yith-wapo-group.php'; }
		function yith_wapo_group_addons() { require YITH_WAPO_DIR . '/templates/admin/yith-wapo-group-addons.php'; }

		/**
		 * Items update
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function items_update() {

			global $wpdb;

			$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : '' ;
			$group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : '';
			$act = isset( $_POST['act'] ) ? $_POST['act'] : '';
			$class = isset( $_POST['class'] ) ? $_POST['class'] : ( isset( $_GET['class'] ) ? $_GET['class'] : '' );

			// Delete Group
			$delete_group_id = isset( $_GET['delete_group_id'] ) ? $_GET['delete_group_id'] : 0;
			if ( $delete_group_id > 0 ) {
				$object = new YITH_WAPO_Group( $delete_group_id );
				$object->delete( $delete_group_id );
				wp_redirect( 'edit.php?post_type=product&page=yith_wapo_groups' );
				exit;
			}

			// Duplicate Group
			$duplicate_group_id = isset( $_GET['duplicate_group_id'] ) ? $_GET['duplicate_group_id'] : 0;
			if ( $duplicate_group_id > 0 ) {
				$group = new YITH_WAPO_Group( $duplicate_group_id );
				$group->duplicate();
				wp_redirect( 'edit.php?post_type=product&page=yith_wapo_groups' );
				exit;
			}

			// Delete Add-on
			$delete_addon_id = isset( $_GET['delete_addon_id'] ) ? $_GET['delete_addon_id'] : 0;
			if ( $delete_addon_id > 0 ) {
				$object = new YITH_WAPO_Type( $delete_addon_id );
				$object->delete( $delete_addon_id );
				wp_redirect( 'edit.php?post_type=product&page=yith_wapo_group_addons&id=' . $id );
				exit;
			}

			// Duplicate Add-on
			$duplicate_addon_id = isset( $_GET['duplicate_addon_id'] ) ? $_GET['duplicate_addon_id'] : 0;
			if ( $duplicate_addon_id > 0 ) {
				$object = new YITH_WAPO_Type( $duplicate_addon_id );
				$object->duplicate();
				wp_redirect( 'edit.php?post_type=product&page=yith_wapo_group_addons&id=' . $id );
				exit;
			}

			if ( class_exists( $class ) ) {
				$object = new $class( $id );
				if ( $act == 'new' ) {
					$object->insert();
					$id = $class == 'YITH_WAPO_Group' ? $wpdb->insert_id : $group_id;
				} else if ( $act == 'update' ) {
					$object->update( $id );
					$id = $class == 'YITH_WAPO_Group' ? $id : $object->group_id;
				} else if ( $act == 'update-order' ) {
					if ( isset($_POST['types-order']) && $_POST['types-order'] != '' ){ YITH_WAPO_Type::update_priorities( $_POST['types-order'] ); }
					$id = $class == 'YITH_WAPO_Group' ? $id : $object->group_id;
				}
				
				if ( $class == 'YITH_WAPO_Group' ) { $object = new YITH_WAPO_Group( $id ); }
				$redirect_url = $id > 0 && $object->del != 1 ?
					( $group_id > 0 ? 'edit.php?post_type=product&page=yith_wapo_group_addons&id=' . $id : 'edit.php?post_type=product&page=yith_wapo_group&id=' . $id )
					: 'edit.php?post_type=product&page=yith_wapo_groups';

				wp_redirect( $redirect_url );
				exit;

			}

		}

		/**
		 * Enqueue admin styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles_scripts() {
			
			global $pagenow;

			$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
			
			/*
			 *  Js
			 */

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui', YITH_WAPO_URL . 'assets/js/jquery-ui/jquery-ui.min.js' );

			wp_enqueue_script( 'jquery-blockui', YITH_WAPO_URL . 'assets/js/jquery-ui/jquery.blockUI.min.js', array( 'jquery' ), false, true );

			wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select.min.js', array( 'jquery', 'select2', 'selectWoo' ) );
			wp_enqueue_script( 'wc-enhanced-select');

			wp_register_script( 'wc-tooltip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery', 'select2', 'selectWoo' ) );
			wp_enqueue_script( 'wc-tooltip' );

			wp_register_script( 'yith_wapo_admin', YITH_WAPO_URL . 'assets/js/yith-wapo-admin' . $suffix . '.js', array( 'jquery'  ), YITH_WAPO_VERSION );
			wp_enqueue_script( 'yith_wapo_admin' );

			$script_params = array(
				'ajax_url'				=> admin_url( 'admin-ajax.php', 'relative' ),
				'wc_ajax_url'			=> WC_AJAX::get_endpoint( "%%endpoint%%" ),
				'confirm_text'			=> __( 'Are you sure?' , 'yith-woocommerce-product-add-ons' ),
				'uploader_title'		=> __( 'Custom Image' , 'yith-woocommerce-product-add-ons' ),
				'uploader_button_text'	=> __( 'Upload Image' , 'yith-woocommerce-product-add-ons' ),
				'place_holder_url'		=> YITH_WAPO_URL . 'assets/img/placeholder.png'
			);

			wp_localize_script( 'yith_wapo_admin', 'yith_wapo_general', $script_params );

			/*
			 *  Css
			 */

			wp_enqueue_style( 'jquery-ui' );
			wp_enqueue_style( 'bootstrap-css' );
			wp_enqueue_style( 'font-awesome' );
			wp_enqueue_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css' );
			wp_enqueue_style( 'wapo-admin', YITH_WAPO_URL . 'assets/css/yith-wapo-admin.css' );

		}

		function type_options_template( $field_type ) {?>
			<option value="checkbox" <?php selected( $field_type , 'checkbox' ); ?>><?php _e( 'Checkbox' , 'yith-woocommerce-product-add-ons' )  ?></option>
			<option value="radio" <?php selected( $field_type , 'radio'); ?>><?php _e( 'Radio Button' , 'yith-woocommerce-product-add-ons' )  ?></option>
			<option value="text" <?php selected( $field_type , 'text'); ?>><?php _e( 'Text' , 'yith-woocommerce-product-add-ons' )  ?></option>
			<?php
		}

		function depend_variations_template( $type, $group ) { ?>
			<label for="variations">
				<?php _e( 'Variations Requirements', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Show this add-on to users only if they have first selected one of the following variations.', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?>"></span>
			</label>
			<select disabled="disabled" class="depend-select2" multiple="multiple" placeholder="<?php echo __( 'Choose required variations', 'yith-woocommerce-product-add-ons' ); ?>..."></select>
			<?php
		}

		function addon_operator_template( $type ) { ?>
			<label for="depend">
				<?php _e( 'Operator', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Select the operator for Options Requirements. Default: OR', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?>"></span>
			</label>
			<select disabled="disabled" name="operator"></select>
			<?php
		}

		function addon_options_template( $options ) {
			?>
			<div class="first_options_free">
				<?php echo __( 'The first', 'yith-woocommerce-product-add-ons' ); ?>
				<input type="number" disabled="disabled" class="regular-text" min="0">
				<?php echo __( 'options are free', 'yith-woocommerce-product-add-ons' ); ?>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
			</div>
			<div class="max_item_selected">
				<input type="number" disabled="disabled" class="regular-text" min="0">
				<?php echo __( 'Limit selectable elements', 'yith-woocommerce-product-add-ons' ); //@since 1.1.3 ?>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Set the maximum number of elements that users can select for this add-on, 0 means no limits (works only with checkboxes)', 'yith-woocommerce-product-add-ons' ); //@since 1.1.3 ?>"></span>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
			</div>
			<div class="max_input_values_amount">
				<input type="number" disabled="disabled" class="regular-text" min="0">
				<?php echo __( 'Max input values amount', 'yith-woocommerce-product-add-ons' ); //@since 1.1.3 ?>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Set the maximum amount for the sum of the input values', 'yith-woocommerce-product-add-ons' ); //@since 1.1.3 ?>"></span>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
			</div>
			<div class="min_input_values_amount">
				<input type="number" disabled="disabled" class="regular-text" min="0">
				<?php echo __( 'Min input values amount', 'yith-woocommerce-product-add-ons' ); //@since 1.1.3 ?>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Set the minimum amount for the sum of the input values', 'yith-woocommerce-product-add-ons' ); //@since 1.1.3 ?>"></span>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
			</div>
			<div class="sold_individually">
				<input type="checkbox" disabled="disabled">
				<?php echo __( 'Sold individually', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Check this box if you want that the selected add-ons are not increased as the product quantity changes.', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?>"></span>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
			</div>
			<div class="change_featured_image">
				<input type="checkbox" disabled="disabled">
				<?php echo __( 'Replace the product image', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Check this box if you want that the selected add-ons replace the product image.', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?>"></span>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
			</div>
			<div class="calculate_quantity_sum">
				<input type="checkbox" disabled="disabled">
				<?php echo __( 'Calculate quantity by values amount', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Check this box if you want that the quanity input will be updated with the sum of all add-ons values.', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?>"></span>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
			</div>
			<div class="required">
				<input type="checkbox" disabled="disabled">
				<?php echo __( 'Required', 'yith-woocommerce-product-add-ons' ); ?>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Check this option if you want that the add-on have to be selected', 'yith-woocommerce-product-add-ons' ); //@since 1.1.3 ?>"></span>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
			</div>
            <div class="required_all_options">
                <input type="checkbox" disabled="disabled">
				<?php echo __( 'All options required', 'yith-woocommerce-product-add-ons' ); ?>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Check this option if you want that the add-on have to be all options required', 'yith-woocommerce-product-add-ons' ); //@since 1.1.3 ?>"></span>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
            </div>
            <div class="collapsed">
                <input type="checkbox" disabled="disabled">
				<?php echo __( 'Collapsed by default', 'yith-woocommerce-product-add-ons' ); ?>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'If not selected it will take settings in Admin > YITH Plugins > Product Add-ons', 'yith-woocommerce-product-add-ons' ); //@since 1.1.3 ?>"></span>
				<strong>(<?php _e( 'premium', 'yith-woocommerce-product-add-ons' ); ?>)</strong>
            </div>
			<?php
		}

		public static function add_wc_product_data_tab() {

			$current_vendor = YITH_WAPO::get_current_multivendor();
			if ( isset( $current_vendor ) && is_object( $current_vendor ) && $current_vendor->has_limited_access() && ! YITH_WAPO::is_plugin_enabled_for_vendors() ) {
				return;
			}

			add_filter( 'woocommerce_product_data_tabs', 'wapo_product_data_tab' );
			if ( ! function_exists( 'wapo_product_data_tab' ) ) {
				function wapo_product_data_tab( $product_data_tabs ) {
					$product_data_tabs['wapo-product-options'] = array(
						'label' => __( 'Product Add-Ons', 'yith-woocommerce-product-add-ons' ),
						'target' => 'my_custom_product_data',
						'class' =>  array( 'yith_wapo_tab_class' ),
					);
					return $product_data_tabs;
				}
			}

			add_action( 'woocommerce_product_data_panels', 'wapo_product_data_fields' );
			if ( ! function_exists( 'wapo_product_data_fields' ) ) {
				function wapo_product_data_fields() {
					global $woocommerce, $post, $wpdb; ?>

					<div id="my_custom_product_data" class="panel woocommerce_options_panel">

						<div class="options_group wapo-plugin" style="padding: 10px;">

							<div style="margin-bottom: 10px;">
								<label><?php echo __( 'Name', 'yith-woocommerce-product-add-ons' ); ?></label>
								<input type="text" name="wapo-group-name" id="wapo-group-name" placeholder="<?php echo __( 'Group name', 'yith-woocommerce-product-add-ons' ); ?>" style="width: 200px;">
								<input type="button" class="button button-primary wapo-add-group" value="<?php echo __( 'Add Group', 'yith-woocommerce-product-add-ons' ); ?>">
							</div>
							
							<ul id="sortable-list" class="sortable">
								<?php
								$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}yith_wapo_groups WHERE FIND_IN_SET( {$post->ID} , products_id ) AND del='0' ORDER BY visibility DESC, priority ASC" );
								foreach ( $rows as $key => $value ) :
									$visibility = '';
									switch (  $value->visibility ) {
										case 0: $visibility = __( 'hidden group.', 'yith-woocommerce-product-add-ons' ); break;
										case 1: $visibility = __( 'private, visible to administrators only.', 'yith-woocommerce-product-add-ons' ); break;
										case 9: $visibility = __( 'public, visible to everyone.', 'yith-woocommerce-product-add-ons' ); break;
										default: $visibility = __( 'public, visible to everyone.', 'yith-woocommerce-product-add-ons' ); break;
									} ?>
									<li class="group-row">
										<span class="dashicons dashicons-exerpt-view" style="margin: 5px 5px 0px 0px;"></span>
										<strong class="wapo-group-edit"><?php echo __( 'Group', 'yith-woocommerce-product-add-ons' ); ?> "<?php echo $value->name; ?>"</strong> - <i><?php echo $visibility; ?></i>
										<a href="edit.php?post_type=product&page=yith_wapo_group&id=<?php echo $value->id; ?>&KeepThis=true&TB_iframe=true&modal=false" onclick="return false;" class="thickbox button manage"><?php echo __( 'Manage', 'yith-woocommerce-product-add-ons' ); ?> &raquo;</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>

						<div class="options_group">

							<?php
							woocommerce_wp_checkbox(
								array( 
									'id'            => '_wapo_disable_global', 
									'wrapper_class' => 'wapo-disable-global', 
									'label'         => __( 'Disable Globals', 'yith-woocommerce-product-add-ons' ),
									'description'   => __( 'Check this box if you want to disable global groups and use only the above ones!', 'yith-woocommerce-product-add-ons' ),
									'default'       => '0',
									'desc_tip'      => false,
								)
							);
							?>
						</div>

						<div class="options_group">
							<p>
								<a href="<?php echo site_url(); ?>/wp-admin/edit.php?post_type=product&page=yith_wapo_groups&KeepThis=true&TB_iframe=true&modal=false" onclick="return false;" class="thickbox button button-primary">
									<?php echo __( 'Manage all groups', 'yith-woocommerce-product-add-ons' ); ?> &raquo;
								</a>
							</p>
						</div>
						
					</div>

					<?php
				}
			}

			add_action( 'admin_footer', 'yit_wapo_my_action_javascript' );
			if ( ! function_exists( 'yit_wapo_my_action_javascript' ) ) {
				function yit_wapo_my_action_javascript() {
					global $post; ?>
					<script type="text/javascript" >
						jQuery(document).ready(function($) {
							jQuery('.wapo-add-group').click( function(){
								var data = {
									'action': 'wapo_save_group',
									'group_name': jQuery('#wapo-group-name').val(),
									'post_id': <?php echo isset( $post->ID ) ? $post->ID : 0; ?>
								};
								jQuery.post(ajaxurl, data, function(response) {
									if ( response == '::no_name' ) { alert( '<?php echo __( 'NO NAME', 'yith-woocommerce-product-add-ons' ); ?>' ); }
									else if ( response == '::db_error' ) { alert( '<?php echo __( 'DB ERROR', 'yith-woocommerce-product-add-ons' ); ?>' ); }
									else {

										response = response.split(',');
										var group_name = response[0];
										var post_id = response[1];

										var new_row = '<li class="group-row"><span class="dashicons dashicons-exerpt-view" style="margin: 5px 5px 0px 0px;"></span><strong class="wapo-group-edit"><?php echo __( 'Group', 'yith-woocommerce-product-add-ons' ); ?> "' + group_name + '</strong>" - <i><?php echo __( 'public, visible to everyone.', 'yith-woocommerce-product-add-ons' ); ?></i>';
										new_row += '<a href="edit.php?post_type=product&page=yith_wapo_group&id=' + post_id + '&KeepThis=true&TB_iframe=true&modal=false" class="thickbox button manage"> <?php echo __( 'Manage', 'yith-woocommerce-product-add-ons' ); ?> &raquo;</a></li>';

										jQuery('.wapo-plugin #sortable-list').prepend( new_row );
										jQuery('#wapo-group-name').val('');

									}
								});
							});
						});
					</script><?php
				}
			}

			add_action( 'wp_ajax_wapo_save_group', 'wapo_save_group_callback' );
			if ( ! function_exists( 'wapo_save_group_callback' ) ) {
				function wapo_save_group_callback() {
					global $wpdb;
					if ( isset( $_POST['group_name'] ) && $_POST['group_name'] != '' ) {
						$group_name = $_POST['group_name'];
						$user_id = get_current_user_id();
						$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
						$groups_table_name = YITH_WAPO_Group::$table_name;
						$sql = "INSERT INTO {$wpdb->prefix}$groups_table_name ( id, name, user_id, vendor_id, products_id, products_exclude_id, categories_id, attributes_id, priority, visibility, del, reg_date )
								VALUES ('', '$group_name', '$user_id', '0', '$post_id', '', '', '', '1', '9', '0', CURRENT_TIMESTAMP)";
						$result = $wpdb->query( $sql );
						echo $result ? $group_name . ',' . $wpdb->insert_id : '::db_error';
					} else { echo '::no_name'; }
					wp_die();
				}
			}

		}
		
		public static function woo_add_custom_general_fields_save( $post_id ){
			
			// Checkbox
			$woocommerce_checkbox = isset( $_POST['_wapo_disable_global'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, '_wapo_disable_global', $woocommerce_checkbox );

		}
		
		function add_new_option(){
			
			require ( YITH_WAPO_TEMPLATE_ADMIN_PATH . 'yith-wapo-new-option.php' );

			wp_die();
		}

		/**
		 *	Action Links
		 *
		 *	Add the action links to plugin admin page
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, false );
			return $links;
		}

		/**
		 *	Plugin row meta
		 *
		 *	Add the action links to plugin admin page
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WAPO_FREE_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WAPO_SLUG;
			}

			return $new_row_meta_args;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_activation() {

			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WAPO_DIR . 'plugin-fw/licence/lib/yit-licence.php' );
				require_once( YITH_WAPO_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php' );
			}

			YIT_Plugin_Licence()->register( YITH_WAPO_INIT, YITH_WAPO_SECRET_KEY, YITH_WAPO_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_updates() {

			if( ! class_exists( 'YIT_Plugin_Licence' ) ){
				require_once( YITH_WAPO_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}

			YIT_Upgrade()->register( YITH_WAPO_SLUG, YITH_WAPO_INIT );
		}

		/**
		 * @param $wpdb
		 * @param $group
		 * @param $type
		 * @param $is_edit
		 *
		 * @return string
		 */
		public static function getDependeciesQuery( $wpdb, $group ,$type , $is_edit ) {

			$dependecies_query = "SELECT id,label,depend,options FROM {$wpdb->prefix}yith_wapo_types";

			if( ! $is_edit ) {

				$dependecies_query .= " WHERE group_id='{$group->id}' AND del='0'";
			  
			}  else {

				$dependecies_query .= " WHERE id!='{$type->id}' AND group_id='{$group->id}' AND del='0'";
			  
			}

			$dependecies_query.= ' ORDER BY label ASC';

			return $dependecies_query;

		}


		/**
		 * @param $product_ids
		 * @param $categories_ids
		 *
		 * @return array
		 */
		public static function getProductsQueryArgs( $product_ids, $categories_ids ) {

			$atts = array(
				'orderby'  => 'title',
				'order'    => 'asc',
			);

			// Default ordering args
			$ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );

			$product_cat_query = array(
				'taxonomy' => 'product_cat',
				'field'    => 'ids',
				'operator' => 'IN'
			);

			if( $categories_ids ) {

				if( is_array( $categories_ids ) ) {
					$product_cat_query['terms'] = $categories_ids;
				} else {
					$product_cat_query['terms'] = explode( ',' , $categories_ids);
				}

			}

			$args = array(
				'post_type' => 'product',
				'tax_query' => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'variable', 'variable-subscription', 'grouped' ),
					),
					// $product_cat_query
				),
				'ignore_sticky_posts' => 1,
				'post_status'         => array( 'publish' ),
				'orderby'             => $ordering_args['orderby'],
				'order'               => $ordering_args['order'],
				'posts_per_page'      => -1
			);
			
			if ( $product_ids ) {
				$ids              = explode( ',', $product_ids );
				$ids              = array_map( 'trim', $ids );
				$args['post__in'] = $ids;
			}

			if ( isset( $ordering_args['meta_key'] ) ) {
				$args['meta_key'] = $ordering_args['meta_key'];
			}

			// Prevent WPML filter
			$args['suppress_filters'] = true;
			
			return $args;

		}

		public static function echo_product_chosen_list( $product_ids, $categories_ids, $options_value = array() ) {
			$args = self::getProductsQueryArgs( $product_ids, $categories_ids );

			global $sitepress;
			if ( is_object( $sitepress ) && method_exists( $sitepress, 'get_current_language' ) ) {

				$current_lang = $sitepress->get_current_language();
				$languages = icl_get_languages('skip_missing=0&orderby=code');
				foreach ( $languages as $key => $value ) {
					$sitepress->switch_lang( $key );
					$loop = new WP_Query( $args );
					if ( $loop->have_posts() ) {
						while ( $loop->have_posts() ) {
							$loop->the_post();
							global $product;
							if ( isset( $product ) ) {
								if ( ! $product->is_purchasable() ) { continue; }
								$post_id = yit_get_base_product_id( $product );
								$title = $product->get_title();
								$variations = self::get_product_variations_chosen_list( $post_id );
								foreach ( $variations as $variation_id ) {
									$title_variation = $title . ': ' . self::get_product_variation_title( $variation_id );
									self::printSelectOptionValue( $variation_id, $options_value, $title_variation );
								}
							}
						}
					}
					wp_reset_postdata();
				}
				$sitepress->switch_lang( $current_lang );

			} else {

				$loop = new WP_Query( $args );
				if ( $loop->have_posts() ) {
					while ( $loop->have_posts() ) {
						$loop->the_post();
						global $product;
						if ( isset( $product ) ) {
							if ( ! $product->is_purchasable() ) { continue; }
							$post_id = yit_get_base_product_id( $product );
							$title = $product->get_title();
							$variations = self::get_product_variations_chosen_list( $post_id );
							foreach ( $variations as $variation_id ) {
								$title_variation = $title . ': ' . self::get_product_variation_title( $variation_id );
								self::printSelectOptionValue( $variation_id, $options_value, $title_variation );
							}
						}
					}
				}
				wp_reset_postdata();
				
			}
		}

		/**
		 * @param $post_id
		 * @param $options_value
		 * @param $title
		 */
		private static function printSelectOptionValue( $post_id , $options_value , $title ) {
			echo '<option value="' . $post_id. '" ' . ( in_array( $post_id, $options_value ) ? 'selected="selected"' : '' ) . '>' . '#' . $post_id . ' ' . $title . '</option>';
		}

		/**
		 * @param $item_id
		 *
		 * @return array
		 */
		private static function get_product_variations_chosen_list( $item_id ) {
			// If variations haven't already been recovered
			if ( ! is_array( self::$variations_chosen_list[ $item_id ] ) || ! count( self::$variations_chosen_list[ $item_id ] ) > 0 ) {
				$variations = array();
				if ( $item_id ) {
					$args = array(
						'post_type'   => 'product_variation',
						'post_status' => array( 'publish' ),
						'numberposts' => apply_filters( 'yith_product_variations_chosen_list_limit', 10 ),
						'orderby'     => 'menu_order',
						'order'       => 'asc',
						'post_parent' => $item_id,
						'fields'      => 'ids'
					);
					$variations = get_posts( $args );
				}
				self::$variations_chosen_list[ $item_id ] = $variations;
			}
			return self::$variations_chosen_list[ $item_id ];
		}

		/**
		 * @param      $variation_id
		 * @param bool $print_father_title
		 *
		 * @return bool
		 */
		private static function get_product_variation_title( $variation_id , $print_father_title = false ) {

			$description = '';

			if ( is_object( $variation_id ) ) {
				$variation = $variation_id;
			} else {
				$variation = wc_get_product( $variation_id );
			}

			if ( ! $variation ) {
				return false;
			}

			if( $print_father_title ) {
				$description = $variation->get_title().' - ';
			}

			$attribute_description = wc_get_formatted_variation( $variation , true );

			return  $description .= $attribute_description;
		}

		/**
		 * @param $rows_dep
		 * @param $value
		 * 
		 */
		public static function printChosenDependencies( $rows_dep , $value ) {

			$depsinarray = array();

			foreach ( $rows_dep as $key_dep => $value_dep ) {
				$depend_array = explode( ',', $value->depend );

				if ( in_array( $value_dep->id, $depend_array ) ) { $depsinarray[] = '#' . $value_dep->id . ' ' . $value_dep->label; }

				$options_values = maybe_unserialize( $value_dep->options );

				if( isset( $options_values['label'] ) ) {

					foreach ( $options_values['label'] as $option_key => $option_value ) {
						$attribute_value = 'option_' . $value_dep->id . '_'.$option_key;

						if( in_array( $attribute_value, $depend_array ) ) {
							$depsinarray[]= '#' . $value_dep->id . ' ' . esc_html( $value_dep->label ).' [ <b>'. esc_html( $option_value ) . '</b> ]';
						}
					}

				}

			}

			if ( count( $depsinarray ) > 0 ) {
				echo __( 'Add-On Requirements: ', 'yith-woocommerce-product-add-ons' );
				foreach ( $depsinarray as $key_dep => $value_dep ) {
					echo '<i>' . $value_dep . '</i>';
				}
			}

		}

		/**
		 * @param $value
		 */
		public static function printChosenDependenciesVariations( $variations ) {

			$variations_array = explode( ',', $variations );

			if ( count( $variations_array ) > 0 ) {
				echo _x( 'Variations Requirements: ', 'admin labels for add-ons list' , 'yith-woocommerce-product-add-ons' );
				foreach ( $variations_array as $value_dep ) {
					$variation_title = self::get_product_variation_title( $value_dep , true );
					if( $variation_title ) {
						echo '<i>' . self::get_product_variation_title( $value_dep , true ) . '</i>';
					}
				}
			}

		}

		public static function printProductsIdSelect2( $title , $name , $value , $is_less_than_2_7 ){

			?>

			<tr>
				<th scope="row"><label for="<?php echo $name; ?>"><?php echo $title; ?></label></th>
				<td>

				 <?php if( $is_less_than_2_7 ) : ?>

					<input type="text" class="wc-product-search" style="width: 350px;" id="<?php echo $name; ?>" name="<?php echo $name; ?>"
						   data-placeholder="<?php esc_attr_e( 'Applied to...', 'yith-woocommerce-product-add-ons' ); ?>"
						   data-action="woocommerce_json_search_products"
						   data-multiple="true"
						   data-exclude=""
						   data-selected="<?php

						   $product_ids = array_filter( array_map( 'absint', explode( ',', $value ) ) );
						   $json_ids    = array();

						   foreach ( $product_ids as $product_id ) {
							   $product = wc_get_product( $product_id );
							   if ( is_object( $product ) ) {
								   $json_ids[ $product_id ] = wp_kses_post( html_entity_decode( $product->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
							   }
						   }

						   echo esc_attr( json_encode( $json_ids ) );
						   ?>"
						   value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>"
					/>

				<?php else: ?>

					 <select class="wc-product-search" multiple="multiple" style="width: 50%;" name="<?php echo $name; ?>[]" data-placeholder="<?php esc_attr_e( 'Applied to...', 'yith-woocommerce-product-add-ons' ); ?>" data-action="woocommerce_json_search_products" data-multiple="true" data-exclude="">
						 <?php

						 $product_ids = array_filter( array_map( 'absint', explode( ',', $value ) ) );

						 foreach ( $product_ids as $product_id ) {
							 $product = wc_get_product( $product_id );
							 if ( is_object( $product ) ) {
								 echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
							 }
						 }
						 ?>
					 </select>

				<?php endif ?>
				
				</td>
			</tr>

			<?php

		}

		/**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri(){
            return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing .'?refer_id=1030585';
        }

	}
}
