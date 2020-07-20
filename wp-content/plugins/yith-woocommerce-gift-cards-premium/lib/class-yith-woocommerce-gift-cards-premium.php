<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YITH_WooCommerce_Gift_Cards_Premium' ) ) {

	/**
	 *
	 * @class   YITH_WooCommerce_Gift_Cards_Premium
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_WooCommerce_Gift_Cards_Premium extends YITH_WooCommerce_Gift_Cards {

		/**
		 * @var int The default product of type gift card
		 */
		public $default_gift_card_id = - 1;


		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null ( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function includes() {

			parent::includes ();

			/**
			 * Include third-party integration classes
			 */

			//  YITH Dynamic Pricing
			defined ( 'YITH_YWDPD_VERSION' ) && require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-dynamic-pricing.php' );

			//  YITH Points and Rewards
			defined ( 'YITH_YWPAR_VERSION' ) && require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-points-and-rewards.php' );

			//  YITH Multi Vendor
			defined ( 'YITH_WPV_PREMIUM' ) && require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-multi-vendor-module.php' );

			if ( get_option('ywgc_aelia_integration_option') == 'yes' ) {
				//  Aelia Currency Switcher
				class_exists('WC_Aelia_CurrencySwitcher') && require_once(YITH_YWGC_DIR . 'lib/third-party/class-ywgc-AeliaCS-module.php');
			}

			//  YITH Quick View
			defined ( 'YITH_WCQV_PREMIUM' ) && require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-general-integrations.php' );

			if ( get_option('ywgc_wpml_integration_option') == 'yes' ){
				//  WPML
				global $woocommerce_wpml;
				if ( $woocommerce_wpml ) {
					require_once ( YITH_YWGC_DIR . 'lib/third-party/class-ywgc-wpml.php' );
				}
			}

			//  Elementor Widgets integration
			if ( defined('ELEMENTOR_VERSION') ) {
				require_once ( YITH_YWGC_DIR . 'lib/third-party/elementor/class-ywgc-elementor.php' );
			}

			//  Flatsome Theme compatibility
			$wp_theme = wp_get_theme();
			if( $wp_theme instanceof WP_Theme ){
				$parent_theme = $wp_theme->parent();

				if ( 'flatsome' == $wp_theme->get('TextDomain') ){
					require_once ( YITH_YWGC_DIR . 'lib/third-party/themes/class-ywgc-flatsome-theme.php' );

				}else if(!empty($parent_theme)){
					if ('flatsome' == $wp_theme->parent()->get('TextDomain')){
						require_once ( YITH_YWGC_DIR . 'lib/third-party/themes/class-ywgc-flatsome-theme.php' );

					}
				}
			}

			include_once( YITH_YWGC_DIR . 'assets/vendor/jquery.fine-uploader/php-traditional-server/handler.php' );


		}

		public function init_hooks() {

			parent::init_hooks ();

			/**
			 * Add attachments to the email sent of the gif card
			 */
			add_filter( 'woocommerce_email_attachments', array( $this, 'attach_documents_to_email' ), 99, 3 );

			/**
			 * Including the GDRP
			 */
			add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

			$this->register_custom_post_statuses ();

			$save_product_meta_hook = 'woocommerce_process_product_meta';

			/**
			 * Saving disable gift this product
			 */
			add_action( $save_product_meta_hook, array( $this, 'disable_gift_this_product_woocommerce_process_product_meta' ) );

			/**
			 * Customize a gift card with data entered by the customer on product page
			 */
//			add_filter ( 'yith_gift_cards_before_add_to_cart', array( $this, 'customize_card_before_add_to_cart' ) );

			/**
			 * Add an option to let the admin set the gift card as a physical good or digital goods
			 */
			add_filter ( 'product_type_options', array( $this, 'add_type_option' ) );

			/**
			 * When the default gift card image is changed from the plugin setting, update the product image
			 * of the default gift card
			 */
			add_action ( 'yit_panel_wc_after_update', array( $this, 'update_default_gift_card' ) );


			/**
			 * Append CSS for the email being sent to the customer
			 */
			add_action ( 'yith_gift_cards_template_before_add_to_cart_form', array( $this, 'append_css_files' ) );

			/**
			 * Set gift card expiration for gift card created manually
			 */
			add_action('save_post', array( $this,'set_expiration_date_for_gift_card_created_manually'),10,3 );

			/**
			 * create the _ywgc_delivery_send_date postmeta when the gift card is created manualy
			 */
			add_action('save_post', array( $this,'create_send_date_on_save'),10,3 );
			/**
			 * Add taxonomy and assign it to gift card products
			 */
			add_action ( 'init', array( $this, 'create_gift_cards_category' ) );

			/**
			 * remove the view button in the gift card taxonomy
			 */
			add_filter( 'giftcard-category_row_actions',  array( $this, 'ywgc_taxonomy_remove_view_row_actions' ), 10, 1 );

			/**
			 * Set the manual amount status for gift cards that are linked to the global value
			 * */
			add_filter ( 'yith_gift_cards_is_manual_amount_enabled', array(
				$this,
				'is_manual_amount_enabled'
			), 10, 2 );

			add_filter ( 'yith_ywgc_get_product_instance', array(
				$this,
				'get_product_instance'
			), 10, 2 );

			/**
			 * Hide the default gift card product for gift this product on the admin products list
			 * */
			add_action ( 'pre_get_posts', array(
				$this,
				'ywcg_pre_get_posts_hide_default_gift_card'
			) );

			add_filter ( 'wp_count_posts', array(
				$this,
				'ywgc_wp_count_posts_hide_default_gift_card'
			), 10, 3 );

			if( $this->allow_product_as_present() ){
				/**
				 * Display in the admin product page the option "Disable Gift this product"
				 */
				add_action ( 'add_meta_boxes', array(
					$this,
					'ywgc_add_meta_boxes_disable_gift_this_product'
				), 10, 2 );

			}

			/**
			 * Select the date format option
			 */
			add_filter('yith_wcgc_date_format', array(
				$this,
				'yith_ywgc_date_format_callback'
			), 10, 1);


			/**
			 * Display the YITH Product Addons in the gift card template
			 */
			if ( defined( 'YITH_WAPO_PREMIUM' ) ) {
				add_action( 'yith_wcgc_template_after_code', array( $this, 'ywgc_display_product_addons' ) );
			}

		}


		/**
		 * Add option select the date format
		 *
		 * @author Francisco Mendoza
		 * @since  2.0.5
		 */

		public function yith_ywgc_date_format_callback( $date_format ) {

			$date_format_in_js = get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' );

			$js_to_php_date_format = array(
				'd' => 'j',
				'dd' => 'd',
				'o' => 'z',
				'D' => 'D',
				'DD' => 'l',
				'm' => 'n',
				'mm' => 'm',
				'M' => 'M',
				'MM' => 'F',
				'y' => 'y',
				'yy' => 'Y',
			);

			$date_format_in_php = strtr($date_format_in_js, $js_to_php_date_format);


			return $date_format_in_php;
		}


		/**
		 * Add option to the admin product page to disable the gift the product
		 *
		 */
		public function ywgc_add_meta_boxes_disable_gift_this_product( $post_type, $post ) {

			if  ( get_post_type( $post ) != 'product' ){
				return;
			}

			$product = wc_get_product( $post->ID );

			if ( ! $product instanceof WC_Product_Gift_Card && $post_type = 'product' && apply_filters( 'yith_gift_card_display_disable_gift_this_product_option', true, $post_type, $post ) ) {

				add_filter( 'product_type_options', array( $this, 'disable_gift_this_product_product_type_options' ), 100, 1 );

			}

		}

		/**
		 * Avoid to show the default gift card product
		 *
		 * @param array  $query
		 *
		 * @return array
		 * @author Daniel Sanchez
		 * @since  2.0.1
		 */
		public function ywgc_wp_count_posts_hide_default_gift_card( $counts, $type, $perm ) {

			if ( $type == 'product' ){

				global $pagenow;

				if ( $default_gift_product = wc_get_product( get_option ( YWGC_PRODUCT_PLACEHOLDER ) ) ){

					$status = $default_gift_product->get_status();

					if ( isset( $counts->$status ) && is_admin() && $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'product' && apply_filters( 'ywgc_wp_count_posts_hide_default_gift_card_filter', true, $counts, $type, $perm ) )
						$counts->$status = $counts->$status - 1;

				}

			}

			return $counts;

		}

		/**
		 * Avoid to show the default gift card product
		 *
		 * @param array  $query
		 *
		 * @return array
		 * @author Daniel Sanchez
		 * @since  2.0.1
		 */
		public function ywcg_pre_get_posts_hide_default_gift_card( $query ) {

			global $pagenow;

			if ( $query->is_admin && $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'product' && apply_filters( 'ywcg_pre_get_posts_hide_default_gift_card_filter', true, $query ) )
				$query->set('post__not_in', array( get_option( YWGC_PRODUCT_PLACEHOLDER ) ) );

		}

		/**
		 * Create gift card pdf file
		 *
		 * @param mixed  $object
		 *
		 * @return array
		 * @author Daniel Sanchez <daniel.sanchez@yithemes.com>
		 * @since  2.0.3
		 */
		public function create_gift_card_pdf_file( $object ) {

			require_once __DIR__ . '/vendor/autoload.php';

			$mpdf_args = apply_filters( 'yith_ywgc_mpdf_args', array() );

			if( is_array( $mpdf_args ) ){
				$mpdf = new \Mpdf\Mpdf($mpdf_args);
			}else{
				$mpdf = new \Mpdf\Mpdf();
			}

			ob_start();
			wc_get_template( 'yith-gift-cards/pdf-style.css',
				null,
				'',
				YITH_YWGC_TEMPLATES_DIR );
			$style = ob_get_clean();

			ob_start();

			$this->preview_digital_gift_cards( $object, 'pdf' );
			$html = ob_get_clean();
			$html = apply_filters( 'yith_ywgc_before_rendering_gift_card_html', $html );

			$direction = is_rtl() ? 'rtl' : 'ltr';

			$mpdf->directionality = apply_filters( 'yith_ywgc_mpdf_directionality', $direction );

			$mpdf->WriteHTML( $style, 1 );

			$mpdf->WriteHTML( $html, 2 );

			$pdf = $mpdf->Output( 'document', 'S' );

			$old_file = get_post_meta( $object->ID, 'ywgc_pdf_file', true );

			if ( file_exists( $old_file ) )
				unlink( $old_file );

			$pdf_filename = get_option( 'ywgc_pdf_file_name', 'yith-gift-card-[giftcardid]-[uniqid]' );

			$formatted_pdf_filename = apply_filters('yith_ywgc_formatted_pdf_filename',
				str_replace (
					array( '[giftcardid]', '[uniqid]' ),
					array( $object->ID, uniqid() ),
					$pdf_filename ),$object,$pdf_filename);

			$new_file = apply_filters( 'yith_ywgc_pdf_new_file_path', YITH_YWGC_SAVE_DIR . $formatted_pdf_filename . ".pdf", $object->ID );

			file_put_contents( $new_file, $pdf );

			update_post_meta( $object->ID, 'ywgc_pdf_file', $new_file );

			return $new_file;

		}

		/**
		 * Attach the documents to the email
		 *
		 * @param array  $attachments
		 * @param string $status
		 * @param mixed  $object
		 *
		 * @return array
		 * @author Daniel Sanchez
		 * @since  2.0.0
		 */
		public function attach_documents_to_email( $attachments, $status, $object ) {

			if ( get_option( 'ywgc_attach_pdf_to_gift_card_code_email', 'no' ) != 'yes' ) {
				return $attachments;
			}

			if ( ! $object instanceof YWGC_Gift_Card_Premium ) {
				return $attachments;
			}

			if ( $status != 'ywgc-email-send-gift-card' ) {
				return $attachments;
			}

			$attachments[] = $this->create_gift_card_pdf_file( $object );

			return $attachments;
		}

		/**
		 * Add option to the admin product page to disable gift the product
		 */
		public function disable_gift_this_product_product_type_options( $options ) {

			$options[ 'yith_wcgc_disable_gift_this_product' ] = array(
				'id'            => '_yith_wcgc_disable_gift_this_product',
				'wrapper_class' => 'show_if_simple show_if_variable',
				'label'         => esc_html__( 'Disable gift this product', 'yith-woocommerce-gift-cards' ),
				'description'   => esc_html__( 'Check this option if you want to disable the option "gift this product" on this product.', 'yith-woocommerce-gift-cards' ),
				'default'       => 'no'
			);

			return $options;
		}


		/**
		 * Saving dsiable gift this product
		 */
		public function disable_gift_this_product_woocommerce_process_product_meta( $product_id ){

			if (!$product_id)
				return;

			$product = wc_get_product( $product_id );

			$disable_gift_this_product = isset($_POST['_yith_wcgc_disable_gift_this_product']) ? 'yes' : 'no';

			yit_save_prop($product, '_yith_wcgc_disable_gift_this_product', $disable_gift_this_product, true);
		}

		/**
		 * Including the GDRP
		 */
		public function load_privacy() {

			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) )
				require_once( YITH_YWGC_DIR . 'lib/class.yith-woocommerce-gift-cards-privacy.php' );

		}

		public function start() {
			//  Init the backend
			$this->backend = YITH_YWGC_Backend_Premium::get_instance ();

			//  Init the frontend
			$this->frontend = YITH_YWGC_Frontend_Premium::get_instance ();
		}

		// register new taxonomy which applies to attachments
		public function create_gift_cards_category() {

			$labels = array(
				'name'              => esc_html__('Gift card image categories','yith-woocommerce-gift-cards'),
				'singular_name'     => esc_html__('Gift card image category','yith-woocommerce-gift-cards'),
				'search_items'      => esc_html__('Search image categories','yith-woocommerce-gift-cards'),
				'all_items'         => esc_html__('All image categories','yith-woocommerce-gift-cards'),
				'parent_item'       => esc_html__('Parent image category','yith-woocommerce-gift-cards'),
				'parent_item_colon' => esc_html__('Parent image category:','yith-woocommerce-gift-cards'),
				'edit_item'         => esc_html__('Edit image category','yith-woocommerce-gift-cards'),
				'update_item'       => esc_html__('Update gift card image category','yith-woocommerce-gift-cards'),
				'add_new_item'      => esc_html__('Add new image category','yith-woocommerce-gift-cards'),
				'new_item_name'     => esc_html__('New image category name','yith-woocommerce-gift-cards'),
				'menu_name'         => esc_html__('Gift card image category','yith-woocommerce-gift-cards')
			);

			$args = array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'query_var'         => true,
				'rewrite'           => true,
				'show_admin_column' => true,
				'show_in_menu'      => false, //hide in the WordPress dashboard
				'show_ui'           => true,
				'public'            => true,
				'show_in_rest'      => true,
			);

			register_taxonomy ( YWGC_CATEGORY_TAXONOMY, array( 'attachment', 'product' ), $args );

			if ( !term_exists( 'none', YWGC_CATEGORY_TAXONOMY ) ){
				wp_insert_term(
					__('None','yith-woocommerce-gift-cards'),
					YWGC_CATEGORY_TAXONOMY,
					array(
						'description' => __('Select this category in your gift card product if you do not want to display images in your gift card gallery','yith-woocommerce-gift-cards'),
						'slug' => 'none',
					)
				);
			}


			if (  !term_exists( 'all', YWGC_CATEGORY_TAXONOMY ) ) {
				wp_insert_term(
					__( 'All', 'yith-woocommerce-gift-cards' ),
					YWGC_CATEGORY_TAXONOMY,
					array(
						'description' => __( 'Select this category in your gift card product if you want to display all the images categories in your gift card gallery', 'yith-woocommerce-gift-cards' ),
						'slug'        => 'all',
					)
				);
			}
		}

		// remove the view button in the gift card taxonomy
		public function ywgc_taxonomy_remove_view_row_actions( $actions ){

			unset( $actions['view'] );
			return $actions;
		}


		/**
		 * Register all the custom post statuses of gift cards
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function register_custom_post_statuses() {

			register_post_status ( YWGC_Gift_Card_Premium::STATUS_DISABLED, array(
					'label'                     => esc_html__( 'Disabled', 'yith-woocommerce-gift-cards' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'post_type'                 => array( 'gift_card' ),
					'label_count'               => _n_noop ( esc_html__( 'Disabled', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', esc_html__( 'Disabled', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);

			register_post_status ( YWGC_Gift_Card_Premium::STATUS_DISMISSED, array(
					'label'                     => esc_html__( 'Dismissed', 'yith-woocommerce-gift-cards' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'post_type'                 => array( 'gift_card' ),
					'label_count'               => _n_noop ( esc_html__( 'Dismissed', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', esc_html__( 'Dismissed', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);

			register_post_status ( YWGC_Gift_Card_Premium::STATUS_CODE_NOT_VALID, array(
					'label'                     => esc_html__( 'Code not valid', 'yith-woocommerce-gift-cards' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'post_type'                 => array( 'gift_card' ),
					'label_count'               => _n_noop ( esc_html__( 'Code not valid', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', esc_html__( 'Code not valid', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);

			register_post_status ( YWGC_Gift_Card_Premium::STATUS_PRE_PRINTED, array(
					'label'                     => esc_html__( 'Pre-Printed', 'yith-woocommerce-gift-cards' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => false,
					'show_in_admin_status_list' => true,
					'post_type'                 => array( 'gift_card' ),
					'label_count'               => _n_noop ( esc_html__( 'Pre-Printed', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', esc_html__( 'Pre-Printed', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);
		}


		/**
		 * Append CSS for the email being sent to the customer
		 */
		public function append_css_files() {
			YITH_YWGC ()->frontend->enqueue_frontend_style ();
		}


		/**
		 * When the default gift card image is changed from the plugin setting, update the product image
		 * of the default gift card
		 */
		public function update_default_gift_card() {
			if ( isset( $_POST["ywgc_gift_card_header_url-yith-attachment-id"] ) ) {
				yit_save_prop ( wc_get_product ( $this->default_gift_card_id ), "_thumbnail_id", $_POST["ywgc_gift_card_header_url-yith-attachment-id"] );
			}
		}

		/**
		 * Hash the gift card code so it could be used for security checks
		 *
		 * @param YWGC_Gift_Card_Premium $gift_card
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function hash_gift_card( $gift_card ) {

			return hash ( 'md5', $gift_card->gift_card_number . $gift_card->ID );
		}


		/**
		 * Add an option to let the admin set the gift card as a physical good or digital goods.
		 *
		 * @param array $array
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_type_option( $array ) {
			if ( isset( $array["virtual"] ) ) {
				$css_class     = $array["virtual"]["wrapper_class"];
				$add_css_class = 'show_if_gift-card';
				$class         = empty( $css_class ) ? $add_css_class : $css_class .= ' ' . $add_css_class;

				$array["virtual"]["wrapper_class"] = $class;
			}

			return $array;
		}

		/**
		 * Create a product of type gift card to be used as placeholder. Should not be visible on shop page.
		 */
		public function initialize_products() {
			//  Search for a product with meta YWGC_PRODUCT_PLACEHOLDER
			$this->default_gift_card_id = get_option ( YWGC_PRODUCT_PLACEHOLDER, - 1 );

			if ( - 1 == $this->default_gift_card_id ) {

				//  Create a default gift card product
				$args = array(
					'post_title'     => esc_html__( 'Gift this product', 'yith-woocommerce-gift-cards' ),
					'post_name'      => 'default_gift_this_product',
					'post_content'   => esc_html__( 'This product has been automatically created by the plugin YITH Gift Cards.You must not edit it, or the plugin could not work properly. The main functionality of this product is to be used for the feature "Gift this product"', 'yith-woocommerce-gift-cards' ),
					'post_status'    => 'private',
					'post_date'      => date ( 'Y-m-d H:i:s' ),
					'post_author'    => 0,
					'post_type'      => 'product',
					'comment_status' => 'closed',
				);

				$this->default_gift_card_id = wp_insert_post ( $args );
				update_option ( YWGC_PRODUCT_PLACEHOLDER, $this->default_gift_card_id );

				//  Create a taxonomy for products of type YWGC_GIFT_CARD_PRODUCT_TYPE and
				//  set the product created to the new taxonomy
				//  Create product type
				$term = wp_insert_term ( YWGC_GIFT_CARD_PRODUCT_TYPE, 'product_type' );

				$term_id = - 1;
				if ( $term instanceof WP_Error ) {
					$error_code = $term->get_error_code ();
					if ( "term_exists" == $error_code ) {
						$term_id = $term->get_error_data ( $error_code );
					}
				} else {
					$term_id = $term["term_id"];
				}

				if ( $term_id != - 1 ) {
					wp_set_object_terms ( $this->default_gift_card_id, $term_id, 'product_type' );
				} else {
					wp_die ( esc_html__( "An error occurred, you cannot use the plugin", 'yith-woocommerce-gift-cards' ) );
				}

				//  set this default gift card product as virtual
				$product = wc_get_product ( $this->default_gift_card_id );
				if ( $product ) {
					yit_save_prop ( $product, '_virtual', 'yes' );
					yit_save_prop ( $product, '_visibility', 'hidden' );
				}
			}
			else{
				$product = wc_get_product ( $this->default_gift_card_id );
				if ( $product && $product->get_type() != 'gift-card'){
					wp_set_object_terms($product->get_id(), 'gift-card','product_type');
				}
			}
		}

		/**
		 * Initialize plugin settings
		 */
		public function init_plugin() {

			$this->initialize_products ();
		}

		/**
		 * Getter option allow manual amount
		 *
		 * @return bool
		 * @author Carlos Rodríguez
		 * @since  2.2.6
		 */
		public function allow_manual_amount() {

			return  ( "yes" == get_option ( 'ywgc_permit_free_amount', 'no' ) ) ;
		}

		/**
		 * Getter option allow product as present
		 *
		 * @return bool
		 * @author Carlos Rodríguez
		 * @since  2.2.6
		 */
		public function allow_product_as_present() {

			return  ( "yes" == get_option ( 'ywgc_permit_its_a_present', 'no' ) ) ;
		}

		/**
		 * Getter option allow multiple recipients
		 *
		 * @return bool
		 * @author Carlos Rodríguez
		 * @since  2.2.6
		 */
		public function allow_multiple_recipients() {

			return ( "yes" == get_option ( "ywgc_allow_multi_recipients", 'no' ) );
		}
		/**
		 * Getter option order cancelled action
		 *
		 * @return bool
		 * @author Carlos Rodríguez
		 * @since  2.2.6
		 */
		public function order_cancelled_action() {

			return get_option ( "ywgc_order_cancelled_action", 'nothing' );
		}
		/**
		 * Getter option order refunded action
		 *
		 * @return bool
		 * @author Carlos Rodríguez
		 * @since  2.2.6
		 */
		public function order_refunded_action() {

			return get_option ( "ywgc_order_refunded_action", 'nothing' );
		}
		/**
		 * Getter option mandatory recipient
		 *
		 * @return bool
		 * @author Carlos Rodríguez
		 * @since  2.2.6
		 */
		public function mandatory_recipient() {

			return ( "yes" == get_option ( 'ywgc_recipient_mandatory', 'no' ) );
		}
		/**
		 * Getter option gift this product label
		 *
		 * @return bool
		 * @author Carlos Rodríguez
		 * @since  2.2.6
		 */
		public function ywgc_gift_this_product_label() {

			$get_option_ywgc_gift_this_product_label = get_option ( 'ywgc_gift_this_product_label',  esc_html__( 'Gift this product', 'yith-woocommerce-gift-cards' ) );

			return ( empty( $get_option_ywgc_gift_this_product_label ) ? 'Gift this product' : $get_option_ywgc_gift_this_product_label );
		}


		/**
		 * Generate a new gift card code
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function generate_gift_card_code() {

			//  Create a new gift card number
			$numeric_code     = (string) mt_rand ( 99999999, mt_getrandmax () );
			$numeric_code_len = strlen ( $numeric_code );

			$code     = apply_filters( 'ywgc_random_generate_gift_card_code', strtoupper ( sha1 ( uniqid ( mt_rand () ) ) ) );
			$code_len = strlen ( $code );
			$pattern     = get_option ( 'ywgc_code_pattern', '****-****-****-****' );
			$pattern_len = strlen ( $pattern );

			for ( $i = 0; $i < $pattern_len; $i ++ ) {

				if ( '*' == $pattern[ $i ] ) {
					//  replace all '*'s with one letter from the unique $code generated
					$pattern[ $i ] = $code[ $i % $code_len ];
				} elseif ( 'D' == $pattern[ $i ] ) {
					//  replace all 'D's with one digit from the unique integer $numeric_code generated
					$pattern[ $i ] = $numeric_code[ $i % $numeric_code_len ];
				}
			}

			return $pattern;
		}

		/**
		 * Retrieve if the gift cards should be updated on order refunded
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function change_status_on_refund() {
			return $this->disable_on_refund () || $this->dismiss_on_refund ();
		}

		/**
		 * Retrieve if the gift cards should be updated on order cancelled
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function change_status_on_cancelled() {
			return $this->disable_on_cancelled () || $this->dismiss_on_cancelled ();
		}

		/**
		 * Retrieve if a gift card should be set as dismissed if an order change its status
		 * to refunded
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function dismiss_on_refund() {
			return 'dismiss' == $this->order_refunded_action();
		}

		/**
		 * Retrieve if a gift card should be set as disabled if an order change its status
		 * to refunded
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function disable_on_refund() {
			return 'disable' == $this->order_refunded_action();
		}

		/**
		 * Retrieve if a gift card should be set as dismissed if an order change its status
		 * to cancelled
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function dismiss_on_cancelled() {
			return 'dismiss' == $this->order_cancelled_action();
		}

		/**
		 * Retrieve if a gift card should be set as disabled if an order change its status
		 * to cancelled
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function disable_on_cancelled() {
			return 'disable' == $this->order_cancelled_action();
		}

		public function on_plugin_init() {
			parent::on_plugin_init();
			$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
			if ( is_admin() && !$is_ajax ) {
				$this->init_metabox();
			}
		}

		public function init_metabox() {

			$args = array(
				'label'    => esc_html__( 'Gift card detail', 'yith-woocommerce-gift-cards' ),
				'pages'    => YWGC_CUSTOM_POST_TYPE_NAME,   //or array( 'post-type1', 'post-type2')
				'context'  => 'normal', //('normal', 'advanced', or 'side')
				'priority' => 'high',
				'tabs'     => array(
					'General' => array( //tab
						'label'  => esc_html__( 'General', 'yith-woocommerce-gift-cards' ),
						'fields' => apply_filters ( 'yith_ywgc_gift_card_instance_metabox_custom_fields',
							array(

								YITH_YWGC_Gift_Card::META_AMOUNT_TOTAL  => array(
									'label'   => esc_html__( 'Purchased amount', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The amount purchased by the customer.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => ''
								),
								YITH_YWGC_Gift_Card::META_BALANCE_TOTAL => array(
									'label'   => esc_html__( 'Current balance', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The current amount available for the customer.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => ''
								),
								'_ywgc_is_digital'                      => array(
									'label'   => esc_html__( 'Digital', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'Check if the gift card will be sent via email. Leave it unchecked to make this work as a physical product.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'checkbox',
									'private' => false,
									'std'     => ''
								),
								'_ywgc_sender_name'                     => array(
									'label'   => esc_html__( 'Sender\'s name', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The name of the digital gift card sender, if any.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
									'css'     => 'width: 80px;',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
								'_ywgc_recipient'                       => array(
									'label'   => esc_html__( 'Recipient\'s email', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The email address of the digital gift card recipient.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
								'_ywgc_recipient_name'                       => array(
									'label'   => esc_html__( 'Recipient\'s name', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The name of the digital gift card recipient.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
								'_ywgc_message'                         => array(
									'label'   => esc_html__( 'Message', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The message attached to the gift card.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'textarea',
									'private' => false,
									'std'     => '',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),

								'_ywgc_delivery_date'                   => array(
									'label'   => esc_html__( 'Delivery date', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The date when the digital gift card will be sent to the recipient.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
								),
								'_ywgc_delivery_date_formatted' => array(
									'label' => esc_html__( 'Delivery date', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The date when the digital gift card will be sent to the recipient.', 'yith-woocommerce-gift-cards' ),
									'type'  => 'datepicker',
									'id'  => '_ywgc_delivery_date_formatted',
									'private' => false,
									'std'     => '',
									'data'    => array(
										'date-format' => get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' ),
										'min-date' => 0
									),
								),

								'_ywgc_expiration'                   => array(
									'label'   => esc_html__( 'Expiration date', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The date when the digital gift card will expire.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
								),
								'_ywgc_expiration_date_formatted' =>array(
									'label'   => esc_html__( 'Expiration date', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The date when the digital gift card will expire.', 'yith-woocommerce-gift-cards' ),
									'type'  => 'datepicker',
									'id'  => '_ywgc_expiration_date_formatted',
									'private' => false,
									'std'     => '',
									'data'    => array(
										'date-format' => get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' ),
										'min-date' => 0
									),
								),

								'_ywgc_internal_notes' =>array(
									'label'   => esc_html__( 'Internal notes', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'Enter your notes here. This will only be visible to the admin.', 'yith-woocommerce-gift-cards' ),
									'type'    => 'textarea',
									'private' => false,
									'std'     => '',
								),

							) ),
					)
				)
			) ;

			$metabox = YIT_Metabox ( 'yit-metabox-id' );
			$metabox->init ( $args );

		}

		/**
		 * Register the custom post type
		 */
		public function init_post_type() {
			$args = array(
				'labels'        => array(
					'name'               => _x ( 'All Gift Cards', 'post type general name', 'yith-woocommerce-gift-cards' ),
					'singular_name'      => _x ( 'Gift Card', 'post type singular name', 'yith-woocommerce-gift-cards' ),
					'menu_name'          => _x ( 'Gift Cards', 'admin menu', 'yith-woocommerce-gift-cards' ),
					'name_admin_bar'     => _x ( 'Gift Card', 'add new on admin bar', 'yith-woocommerce-gift-cards' ),
					'add_new'            => _x ( 'Create Code', 'admin menu item', 'yith-woocommerce-gift-cards' ),
					'add_new_item'       => esc_html__( 'Create Gift Card Code', 'yith-woocommerce-gift-cards' ),
					'new_item'           => esc_html__( 'New Gift Card', 'yith-woocommerce-gift-cards' ),
					'edit_item'          => esc_html__( 'Edit Gift Card', 'yith-woocommerce-gift-cards' ),
					'view_item'          => esc_html__( 'View Gift Card', 'yith-woocommerce-gift-cards' ),
					'all_items'          => esc_html__( 'All gift cards', 'yith-woocommerce-gift-cards' ),
					'search_items'       => esc_html__( 'Search gift cards', 'yith-woocommerce-gift-cards' ),
					'parent_item_colon'  => esc_html__( 'Parent gift cards:', 'yith-woocommerce-gift-cards' ),
					'not_found'          => esc_html__( 'No gift cards found.', 'yith-woocommerce-gift-cards' ),
					'not_found_in_trash' => esc_html__( 'No gift cards found in Trash.', 'yith-woocommerce-gift-cards' )
				),
				'label'         => esc_html__( 'Gift Cards', 'yith-woocommerce-gift-cards' ),
				'description'   => esc_html__( 'Gift Cards', 'yith-woocommerce-gift-cards' ),
				// Features this CPT supports in Post Editor
				'supports'      => array( 'title' ),
				'hierarchical'  => false,
				'capability_type'     => 'product',
				'capabilities'        => array(
					'delete_post'        => 'edit_posts',
					'delete_posts'       => 'edit_posts',
				),
				'public'        => false,
				'show_in_menu'  => apply_filters('yith_wcgc_show_in_menu_cpt', false), //hide in the WordPress dashboard
				'show_ui'       => true,
				'menu_position' => 9,
				'can_export'    => true,
				'has_archive'   => false,
				'menu_icon'     => 'dashicons-clipboard',
				'query_var'     => false,
			);

			// Registering your Custom Post Type
			register_post_type ( YWGC_CUSTOM_POST_TYPE_NAME, $args );


		}


		/**
		 * Retrieve a gift card product instance from the gift card code
		 *
		 * @param $code string the card code to search for
		 *
		 * @return YWGC_Gift_Card_Premium
		 */
		public function get_gift_card_by_code( $code ) {

			return new YWGC_Gift_Card_Premium( array( 'gift_card_number' => $code ) );
		}


		/**
		 * Retrieve the real picture to be used on the gift card preview
		 *
		 * @param YWGC_Gift_Card_Premium $object
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 *
		 */
		public function get_gift_card_header_url( $object ) {
			//  Choose a valid gift card image header
			if ( $object->has_custom_design ) {
				//  There is a custom header image or a template chosen by the customer?
				if ( is_numeric ( $object->design ) ) {
					//  a template was chosen, retrieve the picture associated
					$header_image_url = yith_get_attachment_image_url( $object->design, apply_filters( 'ywgc_email_image_size', 'full' ) );
				} else {
					$header_image_url = YITH_YWGC_SAVE_URL . $object->design;
				}
			} else {
				if ( ! empty( $this->gift_card_header_url ) ) {
					$header_image_url = $this->gift_card_header_url;
				} else {
					$header_image_url = YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.jpg';
				}
			}

			return $header_image_url;
		}

		/**
		 * Retrieve the image to be used as a main image for the gift card
		 *
		 * @param WC_product $product
		 *
		 * @return string
		 */
		public function get_header_image_for_product( $product ) {
			$header_image_url = '';

			if ( $product ) {

				$product_id = yit_get_product_id ( $product );
				if ( $product instanceof WC_Product_Gift_Card ) {
					$header_image_url = $product->get_manual_header_image ();
				}

				if ( ( '' == $header_image_url ) && has_post_thumbnail ( $product_id ) ) {
					$image            = wp_get_attachment_image_src ( get_post_thumbnail_id ( $product_id ), apply_filters( 'ywgc_email_image_size', 'full' ) );
					$header_image_url = $image[0];
				}
			}
			return $header_image_url;
		}

		public function get_default_header_image() {

			$default_header_image_url = get_option ( "ywgc_gift_card_header_url", YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.jpg' );

			return $default_header_image_url ? $default_header_image_url : YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.jpg';
		}

		/**
		 * Retrieve the default image, configured from the plugin settings, to be used as gift card header image
		 *
		 * @param YWGC_Gift_Card_Premium|WC_Product $obj
		 *
		 * @return mixed|string|void
		 */
		public function get_header_image( $obj = null ) {

			$header_image_url = '';
			if ( $obj instanceof YWGC_Gift_Card_Premium ) {

				if ( $obj->has_custom_design ) {
					//  There is a custom header image or a template chosen by the customer?
					if ( is_numeric ( $obj->design ) ) {
						//  a template was chosen, retrieve the picture associated
						$header_image_url = yith_get_attachment_image_url ( $obj->design, apply_filters( 'ywgc_email_image_size', 'full' ) );

					} else {
						$header_image_url = YITH_YWGC_SAVE_URL . $obj->design;

					}
				} else {
					$product          = wc_get_product ( $obj->product_id );
					$header_image_url = $this->get_header_image_for_product ( $product );

				}
			}

			if ( is_object( $obj ) ){
				if ( get_class( $obj ) == 'WC_Product_Gift_Card' ){

					$image_id = $obj->get_manual_header_image ( $obj->get_id(), 'id' );
					$header_image_url = wp_get_attachment_url( $image_id );

				}
			}

			if ( ! $header_image_url ) {
				$header_image_url = $this->get_default_header_image ();

			}

			return $header_image_url;
		}

		/**
		 * Output a gift cards template filled with real data or with sample data to start editing it
		 * on product page
		 *
		 * @param WC_Product|YWGC_Gift_Card_Premium $object
		 * @param string                            $context
		 */
		public function preview_digital_gift_cards( $object, $context = 'shop', $case = 'recipient' ) {

			if ( $object instanceof YWGC_Gift_Card_Premium ) {

				$header_image_url = $this->get_header_image ( $object );

				$amount          = $object->total_amount;
				$formatted_price = apply_filters ( 'yith_ywgc_gift_card_template_amount', wc_price ( $amount ), $object, $amount );

				$gift_card_code = $object->gift_card_number;
				$message        = $object->message;

			}

			// Checking if the image sent is a product image, if so then we set $header_image_url with correct url
			if ( isset( $header_image_url ) ){
				if ( strpos( $header_image_url, '-yith_wc_gift_card_premium_separator_ywgc_template_design-') !== false ) {
					$array_header_image_url = explode( "-yith_wc_gift_card_premium_separator_ywgc_template_design-", $header_image_url );
					$header_image_url = $array_header_image_url['1'];
				}
			}

			$product_id = isset($object->product_id) ? $object->product_id : '';

			$args = array(
				'company_logo_url' => ( "yes" == get_option ( "ywgc_shop_logo_on_gift_card", 'no' ) ) ? get_option ( "ywgc_shop_logo_url", YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.png' ) : '',
				'header_image_url' => $header_image_url,
				'default_header_image_url' => $this->get_default_header_image(),
				'formatted_price'  => $formatted_price,
				'gift_card_code'   => $gift_card_code,
				'message'          => $message,
				'context'          => $context,
				'object'		   => $object,
				'product_id'	   => $product_id,
				'case'             => $case,

			);

			wc_get_template ( 'yith-gift-cards/ywgc-gift-card-template.php', $args, '', trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );

		}

		/**
		 * Start the scheduling that let gift cards to be sent on expected date
		 */
		public static function start_gift_cards_scheduling() {

			if ( get_option( 'ywgc_delivery_mode', 'hourly' ) == 'hourly' ){
				wp_schedule_event( time() , 'hourly', 'ywgc_start_gift_cards_sending' );

			}
			else{
				$hour = strtotime( get_option( 'ywgc_delivery_hour', '00:00' ) );

				wp_schedule_event(strtotime('-' . get_option( 'gmt_offset' ) . ' hours', $hour ) , 'daily', 'ywgc_start_gift_cards_sending' );
			}
		}

		/**
		 * Stop the scheduling that let gift cards to be sent on expected date
		 */
		public static function end_gift_cards_scheduling() {
			wp_clear_scheduled_hook ( 'ywgc_start_gift_cards_sending' );
		}

		/**
		 * Perform some check to a gift card that should be applied to the cart
		 * and retrieve a message code
		 *
		 * @param YWGC_Gift_Card $gift
		 *
		 * @return bool
		 */
		public function check_gift_card( $gift, $remove = false ) {
			$err_code = '';

			if ( ! $gift->exists () ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_NOT_EXIST;
			} elseif ( ! $gift->is_owner ( get_current_user_id () ) ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_NOT_YOURS;
			} elseif ( isset( WC ()->cart->applied_gift_cards[ $gift->get_code () ] ) ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_ALREADY_APPLIED;
			} elseif ( $gift->is_expired () ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_EXPIRED;
			} elseif ( $gift->is_disabled () ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_DISABLED;
			} elseif ( $gift->is_dismissed () ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_DISMISSED;
			} elseif( apply_filters('yith_wcgc_deny_usage_of_gift_cards_to_purchase_gift_cards',false ) ){

				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

					$product = $cart_item['data'];

					if( $product instanceof WC_Product_Gift_Card ){
						$err_code = YITH_YWGC_Gift_Card::GIFT_CARD_NOT_ALLOWED_FOR_PURCHASING_GIFT_CARD;
						break;
					}

				}
			}
			/**
			 * If the flag $remove is true and there is an error,
			 * the gift card will be removed from the cart, then we set the general
			 * error message here.
			 * */
			if ( $err_code && $remove ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_INVALID_REMOVED;
			}

			$err_code = apply_filters ( 'yith_ywgc_check_gift_card', $err_code, $gift );
			if ( $err_code ) {

				if ( $err_msg = $gift->get_gift_card_error ( $err_code ) ) {

					wc_add_notice ( $err_msg, 'error' );
				}

				return false;
			}

			if ( $gift->get_balance() < pow( 10, - wc_get_price_decimals() ) ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_EXPIRED;
				$err_msg  = $gift->get_gift_card_error( $err_code );
				wc_add_notice( $err_msg, 'error' );

				return false;
			}

			if ( ! $remove ){

				$ywgc_minimal_car_total = get_option ( 'ywgc_minimal_car_total' );

				if ( WC()->cart->total < $ywgc_minimal_car_total ) {
					wc_add_notice( esc_html__( 'In order to use the gift card, the minimum total amount in the cart has to be ' . $ywgc_minimal_car_total . get_woocommerce_currency_symbol(), 'yith-woocommerce-gift-cards'), 'error' );

					return false;
				}

			}

			$items = WC()->cart->get_cart();
			foreach ( $items as $cart_item_key => $values ) {
				$product = $values['data'];
				if ( apply_filters('yith_ywgc_check_subscription_product_on_cart',true) && class_exists('WC_Subscriptions_Product') && WC_Subscriptions_Product::is_subscription( $product ) ) {
					wc_add_notice( esc_html__( 'It is not possible to add any gift card if the cart contains a subscription-based product', 'yith-woocommerce-gift-cards'), 'error' );

					return false;
				}
			}


			foreach( WC()->cart->get_coupons() as $coupon ){

				$coupon_code = strtoupper($coupon->get_code()) ;
				$gift_code = strtoupper($gift->get_code()) ;

				if ( $gift_code ==  $coupon_code ) {
					wc_add_notice( esc_html__( 'This code is already applied', 'yith-woocommerce-gift-cards'), 'error' );

					return false;
				}
			}


			return apply_filters('yith_ywgc_check_gift_card_return', true, $gift );
		}

		/**
		 * Set the manual amount status for gift cards that are linked to the global value
		 *
		 * @param bool   $enabled
		 * @param string $status
		 *
		 * @return bool
		 */
		public function is_manual_amount_enabled( $enabled, $status ) {

			if ( 'global' == $status ) {
				$enabled = $this->allow_manual_amount();
			}

			return $enabled;
		}

		/**
		 * Retrieve the product instance
		 *
		 * @param WC_Product_Gift_Card $product
		 *
		 * @return null|WC_Product
		 */
		public function get_product_instance( $product ) {

			global $sitepress;

			if ( $sitepress ) {
				$_wcml_settings = get_option ( '_wcml_settings' );
				if ( isset( $_wcml_settings['trnsl_interface'] ) && '1' == $_wcml_settings['trnsl_interface'] ) {
					$product_id = yit_get_prop ( $product, 'id' );

					if ( $product_id ) {
						$id = yit_wpml_object_id ( $product_id, 'product', true, $sitepress->get_default_language () );

						if ( $id != $product_id ) {
							$product = wc_get_product ( $id );
						}
					}
				}
			}

			return $product;
		}

		public function set_expiration_date_for_gift_card_created_manually( $post_id, $post, $update ){

			if( $post->post_type == 'gift_card'  && isset( $_POST['yit_metaboxes'] ) ) {
				$saved_format = get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' );

				$delivery_date_timestamp   = '';
				$expiration_date_timestamp = '';

				// Delivery date.
				$delivery_date = isset( $_POST['yit_metaboxes'] ) && isset( $_POST['yit_metaboxes']['_ywgc_delivery_date_formatted'] ) ? $_POST['yit_metaboxes']['_ywgc_delivery_date_formatted'] : time();

				// Expiration date.
				$expiration_date = isset( $_POST['yit_metaboxes'] ) && isset( $_POST['yit_metaboxes']['_ywgc_expiration_date_formatted'] ) ? $_POST['yit_metaboxes']['_ywgc_expiration_date_formatted'] : time();

				if ( $saved_format == 'MM d, yy' ) {
					$delivery_date_timestamp   = strtotime( $delivery_date );
					$expiration_date_timestamp = strtotime( $expiration_date );
				} else {
					$search                 = array( '.', ', ', '/', ' ', ',', 'MM', 'yy', 'mm', 'dd' );
					$replace                = array( '-', '-', '-', '-', '-', 'M', 'y', 'm', 'd' );
					$saved_format_formatted = str_replace( $search, $replace, $saved_format );

					// Delivery date.
					$date_formatted = str_replace( $search, $replace, $delivery_date );
					$delivery_date  = '' !== $date_formatted ? 'mm/dd/yy' !== $saved_format ? date( $saved_format_formatted, strtotime( $date_formatted ) ) : date( $saved_format_formatted, strtotime( $delivery_date ) ) : '';

					if ( $delivery_date = !empty( $delivery_date ) ? DateTime::createFromFormat( $saved_format_formatted, $delivery_date ) : '' ) {
						$delivery_date_timestamp = $delivery_date->getTimestamp();
					}

					// Expiration date.
					$date_formatted  = str_replace( $search, $replace, $expiration_date );
					$expiration_date = '' !== $date_formatted ? 'mm/dd/yy' !== $saved_format ? date( $saved_format_formatted, strtotime( $date_formatted ) ) : date( $saved_format_formatted, strtotime( $expiration_date ) ) : '';

					if ( $expiration_date = !empty( $expiration_date ) ? DateTime::createFromFormat( $saved_format_formatted, $expiration_date ) : '' ) {
						$expiration_date_timestamp = $expiration_date->getTimestamp();
					}
				}

				$_POST['yit_metaboxes']['_ywgc_delivery_date'] = $delivery_date_timestamp;
				$_POST['yit_metaboxes']['_ywgc_expiration']    = $expiration_date_timestamp;

				update_post_meta( $post_id, '_ywgc_delivery_date', $delivery_date_timestamp );
				update_post_meta( $post_id, '_ywgc_expiration', $expiration_date_timestamp );
			}
		}

		public function create_send_date_on_save( $post_ID, $post, $update ){
			if( $post->post_type == 'gift_card' ){

				$delivery_send_date = get_post_meta( $post_ID, '_ywgc_delivery_send_date', true );

				if ( $delivery_send_date == '' )
					update_post_meta( $post_ID, '_ywgc_delivery_send_date', '' );

			}
		}


		public function ywgc_display_product_addons( $object ){
			$order = wc_get_order( $object->order_id );

			if ( is_object( $order ) ) {

				$order_items = $order->get_items();

				if ( is_array( $order_items ) ) {

					foreach ( $order_items as $item_id => $item ) {
						$addons_array_serialized   = wc_get_order_item_meta( $item_id, '_ywapo_meta_data', true );
						$addons_array_unserialized = maybe_unserialize( $addons_array_serialized );

						if ( is_array( $addons_array_unserialized ) || isset( $addons_array_unserialized ) ) {
							foreach ( $addons_array_unserialized as $addon ) {
								if ( isset( $addon['name'] ) && isset( $addon['value'] ) )
									?><tr><td colspan="2" ><?php echo $addon['name'] . ': ' . $addon['value']; ?></td>
								</tr><?php
							}
						}
					}
				}
			}
		}

		/**
		 * Action links
		 *
		 *
		 * @return void
		 * @since    2.0.5
		 * @author   Daniel Sanchez <daniel.sanchez@yithemes.com>
		 */
		public function action_links( $links ) {

			$links = is_array($links) ? $links : array();
			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;

		}

		/**
		 * Plugin Row Meta
		 *
		 *
		 * @return void
		 * @since    2.0.5
		 * @author   Daniel Sanchez <daniel.sanchez@yithemes.com>
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWGC_INIT' ) {

			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

	}
}
