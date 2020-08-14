<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWBC_Backend' ) ) {

	/**
	 *
	 * @class   YITH_YWBC_Backend
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_YWBC_Backend {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

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
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {
			$this->init_hooks();

			/** Gutenberg Support **/
			add_action('plugins_loaded', array($this,'load_gutenberg_compatibility'),20);

		}


		/**
		 * Initialize all hooks used by the plugin affecting the back-end behaviour
		 */
		public function init_hooks() {
			/**
			 * Enqueue scripts and styles for admin pages
			 */
			add_action( 'admin_enqueue_scripts', array(
				$this,
				'enqueue_scripts'
			) );

			/**
			 * Enqueue scripts and styles for admin pages
			 */
			add_action( 'admin_enqueue_scripts', array(
				$this,
				'enqueue_style'
			) );

			/**
			 * Create the barcode for orders when they are created
			 */
			add_action( 'woocommerce_checkout_order_processed', array(
				$this,
				'on_new_order'
			) );

			if ( class_exists( 'WeDevs_Dokan' ) ){
				add_action( 'dokan_checkout_update_order_meta', array(
					$this,
					'on_new_order'
				) );
			}

			/**
			 * Create the barcode for products when they are created
			 */
			add_action( 'transition_post_status', array(
				$this,
				'on_new_product'
			), 10, 3 );

			/**
			 * Add the search by barcode value on back-end product list
			 */
			add_filter( 'posts_join', array(
				$this,
				'query_barcode_join'
			), 10, 2 );

			/**
			 * Add the search by barcode value on back-end product list
			 */
			add_filter( 'posts_where', array(
				$this,
				'query_barcode_where'
			), 10, 2 );

			/**
			 * Add the barcode fields for the order search
			 */
			add_filter( 'woocommerce_shop_order_search_fields', array(
				$this,
				'search_by_order_fields'
			), 10, 2 );

			/**
			 * Manage a request from product bulk actions
			 */
			add_action( 'load-edit.php', array(
				$this,
				'generate_bulk_action'
			) );

			/**
			 * Add a metabox showing the barcode for the order
			 */
			add_action( 'add_meta_boxes', array(
				$this,
				'add_barcode_metabox'
			) );

			/**
			 * If a manual value is entered on the product barcode fields, use it as the current barcode value
			 */
			add_action( 'save_post_product', array(
				$this,
				'save_manual_barcode'
			), 10, 3 );

			add_action( 'wp_ajax_apply_barcode_to_products', array(
				$this,
				'apply_barcode_to_products'
			) );

            add_action( 'wp_ajax_nopriv_apply_barcode_to_products', array(
                $this,
                'apply_barcode_to_products'
            ) );

            add_action( 'wp_ajax_print_product_barcodes_document', array(
                $this,
                'print_product_barcodes_document'
            ) );

            add_action( 'wp_ajax_nopriv_print_product_barcodes_document', array(
                $this,
                'print_product_barcodes_document'
            ) );

			add_action( 'wp_ajax_print_barcodes_by_product_document', array(
				$this,
				'print_barcodes_by_product_document'
			) );

			add_action( 'wp_ajax_nopriv_print_barcodes_by_product_document', array(
				$this,
				'print_barcodes_by_product_document'
			) );


			/**
			 * Add barcode to variations
			 */
			add_action( 'woocommerce_product_after_variable_attributes', array(
				$this,
				'woocommerce_product_after_variable_attributes'
			), 10, 3 );

			add_action( 'wp_ajax_create_barcode', array(
				$this,
				'create_barcode_callback'
			) );

		}

		public function create_barcode_callback() {

			if ( ! current_user_can( 'manage_woocommerce' ) && apply_filters( 'yith_barcode_role_check', true ) ) {
				return;
			}

			$result = '';

			if ( isset( $_POST["id"] ) &&
			     isset( $_POST["type"] )
			) {

				$id    = sanitize_text_field( $_POST["id"] );
				$type  = sanitize_text_field( $_POST["type"] );
				$value = sanitize_text_field( $_POST["value"] );

				if ( 'shop_order' == $type ) {
					$this->create_order_barcode( $id, '', $value );
				} elseif ( ( 'product' == $type ) || ( 'product_variation' == $type ) ) {
					$this->create_product_barcode( $id, '', $value );
				}

				ob_start();
				$post = get_post( $id );
				$this->show_barcode_generation_section( $post );

				$result = ob_get_clean();
			}
			wp_send_json( $result );
		}

		/**
		 * @param $loop
		 * @param $variation_data
		 * @param $variation
		 */
		public function woocommerce_product_after_variable_attributes( $loop, $variation_data, $variation ) {
			/* @var WP_Post $variation */
			$this->show_barcode_generation_section( $variation );
		}


		/**
		 * If a manual value is entered on the product barcode fields, use it as the current barcode value
		 */
		public function save_manual_barcode( $post_ID, $post, $update ) {
			if ( isset( $_POST['ywbc-value'] ) && ! empty( $_POST['ywbc-value'] ) ) {
				//  save the custom value
				$barcode  = YITH_Barcode::get( $post_ID );
				$protocol = YITH_YWBC()->products_protocol;

				$value = $_POST['ywbc-value'];

				$image_path = YITH_YWBC()->get_server_file_path( $post_ID, $protocol, $value );

				$barcode->generate( $protocol, $value, $image_path );
				$barcode->save();
			}
		}

		public function enqueue_style( $hook ) {
			/**
			 * Add styles
			 */
			$screen_id = get_current_screen()->id;

			if ( ( 'product' == $screen_id ) || ( 'shop_order' == $screen_id ) || ( 'yith-plugins_page_yith_woocommerce_barcodes_panel' == $screen_id ) ) {
				wp_enqueue_style( 'ywbc-style',
					YITH_YWBC_ASSETS_URL . '/css/ywbc-style.css',
					array(),
					YITH_YWBC_VERSION );
			}
		}

		/**
		 * Enqueue scripts and styles for the back-end
		 *
		 * @param string $hook
		 *
		 */
		public function enqueue_scripts( $hook ) {

			$screen_id = get_current_screen()->id;

			if ( 'edit-product' == $screen_id ) {
				wp_register_script( "ywbc-bulk-actions",
					YITH_YWBC_SCRIPTS_URL . yit_load_js_file( 'ywbc-bulk-actions.js' ),
					array(
						'jquery',
					),
					YITH_YWBC_VERSION,
					true );

				wp_localize_script( 'ywbc-bulk-actions',
					'ywbc_bk_data',
					array(
						'action_options' => '<option value="ywbc-generate">' . esc_html__( 'Generate barcode', 'yith-woocommerce-barcodes' ) . '</option>',
					) );

				wp_enqueue_script( "ywbc-bulk-actions" );
			}

			if ( ( isset( $_GET['page'] ) && 'yith_woocommerce_barcodes_panel' == $_GET['page'] ) ) {

				wp_register_script( 'ywbc-ajax-apply-barcode', YITH_YWBC_SCRIPTS_URL . yit_load_js_file( 'ywbc-ajax-apply-barcode.js' ), array(
					'jquery',
					'jquery-ui-progressbar'
				), YITH_YWBC_VERSION, true );

				$ywbc_params = array(
					'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'messages' => array(
						'complete_task' => esc_html__( 'Barcodes applied successfully', 'yith-woocommerce-barcodes' ),
						'error_task'    => esc_html__( 'It is not possible to complete the task', 'yith-woocommerce-barcodes' )
					)
				);

				wp_localize_script( 'ywbc-ajax-apply-barcode', 'ywbc_params', $ywbc_params );
				wp_enqueue_script( 'ywbc-ajax-apply-barcode' );
			}

			wp_register_script( "ywbc-backend",
				YITH_YWBC_SCRIPTS_URL . yit_load_js_file( 'ywbc-backend.js' ),
				array(
					'jquery',
				),
				YITH_YWBC_VERSION,
				true );

			wp_localize_script( 'ywbc-backend',
				'ywbc_data',
				array(
					'loader'   => apply_filters( 'yith_ywbc_loader', YITH_YWBC_ASSETS_URL . '/images/loading.gif' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				) );

			wp_enqueue_script( "ywbc-backend" );

		}


		/**
		 * Create barcode for new orders if needed
		 *
		 * @param int $order_id
		 */
		public function on_new_order( $order_id ) {

			//  Check if barcode are enabled for orders
			if ( ! YITH_YWBC()->enable_on_orders ) {
				return;
			}

			//  Check if barcode should be create automatically
			if ( ! YITH_YWBC()->create_on_orders ) {
				return;
			}

			if ( apply_filters( 'ywbc_before_create_order_barcode', true, $order_id ) ){
                $this->create_order_barcode( $order_id );
            }

		}

		/**
		 * Create the barcode values for the order
		 *
		 * @param int    $order_id
		 * @param string $protocol
		 * @param string $value
		 */
		public function create_order_barcode( $order_id, $protocol = '', $value = '' ) {
			$protocol  = $protocol ? $protocol : YITH_YWBC()->orders_protocol;

			$order = wc_get_order( $order_id );

			$value_option = get_option( 'ywbc_order_barcode_type', 'id');

			if ( $value_option == 'number' ){
				$value_type = $order->get_order_number();
			}
			elseif ( $value_option == 'custom_field' ){
				$custom_field = get_option( 'ywbc_order_barcode_type_custom_field');
				$value_type = get_post_meta( $order_id, $custom_field , true );
			}
			else{
				$value_type = $order_id;
			}

			$the_value = $value ? $value : $value_type;
			$the_value = apply_filters( 'yith_barcode_new_order_value', $the_value, $order_id, $protocol, $value );

			$this->generate_barcode_image( $order_id, $protocol, $the_value );
		}

		/**
		 * Generate a new barcode instance
		 *
		 * @param int    $object_id the id of the object(WC_Product or WC_Order) associated to this barcode
		 * @param string $protocol  the protocol to use
		 * @param string $value     the value to use as the barcode value
		 *
		 * @return YITH_Barcode
		 */
		public function generate_barcode_image( $object_id, $protocol, $value ) {
			$barcode = new YITH_Barcode( $object_id );

			$image_path = YITH_YWBC()->get_server_file_path( $object_id, $protocol, $value );

			$res        = $barcode->generate( $protocol, $value, $image_path );
			$barcode->save();

			return $barcode;

		}

		/**
		 * Create barcode for new products if needed
		 *
		 * @param string  $new_status
		 * @param string  $old_status
		 * @param WP_Post $post
		 */
		public function on_new_product( $new_status, $old_status, $post ) {

			$post_type_allowed = apply_filters('yith_barcodes_post_type_allowed',array('product'));
			if( ! in_array($post->post_type,$post_type_allowed) ){
				return;
			}

			//  Check if barcode are enabled for products
			if ( ! YITH_YWBC()->enable_on_products ) {
				return;
			}

			//  Check if barcode should be create automatically
			if ( ! YITH_YWBC()->create_on_products ) {
				return;
			}

			//  Work only on published posts
			if ( 'new' !== $old_status ) {
				return;
			}

			$this->create_product_barcode( $post->ID );
		}

		/**
		 * Create the barcode values for the order
		 *
		 * @param int    $product_id
		 * @param string $protocol
		 * @param string $value
		 *
		 * @return YITH_Barcode
		 */
		public function create_product_barcode( $product_id, $protocol = '', $value = '' ) {

			$protocol  = $protocol ? $protocol : YITH_YWBC()->products_protocol;

			$product = wc_get_product( $product_id );

			$value_option = get_option( 'ywbc_product_barcode_type', 'id');

			if ( $value_option == 'sku' ){
				$value_type = $product->get_sku();
			}
			elseif ( $value_option == 'custom_field' ){
				$custom_field = get_option( 'ywbc_product_barcode_type_custom_field');

				if ( $custom_field == 'product_url' && $protocol == 'QRcode' ){
					$value_type =get_permalink( $product_id );
				}
				else{
					$value_type = get_post_meta( $product_id, $custom_field , true );
				}
			}
			else{
				$value_type = $product_id;
			}

			$the_value = $value ? $value : $value_type;
			$the_value = apply_filters( 'yith_barcode_new_product_value', $the_value, $product_id, $protocol, $value );

			return $this->generate_barcode_image( $product_id, $protocol, $the_value );
		}

		/**
		 * Manage a request from product bulk actions
		 */
		public function generate_bulk_action() {

			global $typenow;
			$post_type = $typenow;
			$sendback  = admin_url( "edit.php?post_type=$post_type" );

			// 1. get the action
			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action        = $wp_list_table->current_action();

			if ( $action == 'ywbc-generate' ) {
				$post_ids = $_GET['post'];
				check_admin_referer( 'bulk-posts' );

				foreach ( $post_ids as $post_id ) {
					$this->create_product_barcode( $post_id );
				}

				// build the redirect url
				//$sendback = add_query_arg( array( 'done' => $done, 'ids' => join( ',', $post_ids ) ), $sendback );

				wp_redirect( $sendback );

				exit();
			}

			// 4. Redirect client

		}

		/**
		 * Set the join part of the query used for filtering products
		 *
		 * @param string   $join
		 * @param WP_Query $par2
		 *
		 * @return string
		 */
		public function query_barcode_join( $join, $par2 ) {

			if ( empty( $_GET["s"] ) ) {
				return $join;
			}

			//  check for necessary arguments
			if ( ! isset( $par2 ) || ! isset( $par2->query["post_type"] ) ) {
				return $join;
			}

			//  Do something only for products and orders
			if ( ( "product" != $par2->query["post_type"] ) &&
			     ( "shop_order" != $par2->query["post_type"] )
			) {
				return $join;
			}


			global $wpdb;

			$join .= sprintf( " LEFT JOIN {$wpdb->postmeta} ps_meta ON {$wpdb->posts}.ID = ps_meta.post_id and ps_meta.meta_key = '_ywbc_barcode_display_value'" );

			return $join;
		}

		public function search_by_order_fields( $fields ) {
			$fields[] = YITH_Barcode::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE;

			return $fields;
		}

		/**
		 * Set the where part of the query used for filtering products
		 *
		 * @param string   $where
		 * @param WP_Query $par2
		 *
		 * @return string
		 */
		public function query_barcode_where( $where, $par2 ) {

			if ( empty( $_GET["s"] ) ) {
				return $where;
			}

			$search_value = $_GET["s"];

			//  check for necessary arguments
			if ( ! isset( $par2 ) || ! isset( $par2->query["post_type"] ) ) {
				return $where;
			}

			//  Do something only for products and orders
			if ( ( "product" != $par2->query["post_type"] ) &&
			     ( "shop_order" != $par2->query["post_type"] )
			) {
				return $where;
			}


			global $wpdb;

			$meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->postmeta." WHERE meta_key=%s AND meta_value=%s", '_ywbc_barcode_display_value', $_GET["s"] ) );

			$product = isset( $meta[0]->post_id ) ? wc_get_product ( $meta[0]->post_id ) : '';

			if ( $product && $product->get_type() == 'variation'  ){

				$product_parent = wc_get_product( $product->get_parent_id() );

				$search_value = $product_parent->get_id();

			}

			$where .= sprintf( " or (ps_meta.meta_value like '%%%s%%') ", $search_value );

			return $where;
		}

		/**
		 * Show the order metabox
		 */
		function add_barcode_metabox() {
			if ( YITH_YWBC()->enable_on_orders ) {
				//  Add metabox on order page
				add_meta_box( 'ywbc_barcode',
					esc_html__( 'YITH Barcodes', 'yith-woocommerce-barcodes' ), array(
						$this,
						'show_barcode_generation_section',
					), 'shop_order', 'side', 'high' );
			}

			if ( YITH_YWBC()->enable_on_products ) {
				//  Add metabox on order page
				add_meta_box( 'ywbc_barcode',
					esc_html__( 'YITH Barcodes', 'yith-woocommerce-barcodes' ), array(
						$this,
						'show_barcode_generation_section',
					), 'product', 'side', 'high' );
			}
		}

		/**
		 * Display the barcode metabox
		 *
		 * @param WP_Post $post
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_barcode_generation_section( $post ) {
			if ( ( "shop_order" == $post->post_type ) ||
			     ( "product" == $post->post_type ) ||
			     ( "product_variation" == $post->post_type )
			) {
				?>
				<div class="ywbc-barcode-generation">
					<?php
					YITH_YWBC()->show_barcode( $post->ID );

					if ( get_option ( 'ywbc_product_manual_barcode_product', 'no' ) == 'yes' && $post->post_type == 'product' || $post->post_type == 'product_variation' ) {

                        $this->show_generate_barcode_button( $post->post_type, $post->ID );
                    }
					else if (get_option ( 'ywbc_enable_on_orders', 'yes' ) == 'yes' && $post->post_type == 'shop_order' ){

						$this->show_generate_barcode_button(       $post->post_type, $post->ID );
					}

					?>
				</div>
				<?php
			}
		}


		/**
		 * Show a button that let the admin to generate a new barcode for the order
		 *
		 * @param string $type the type of object for which the action generate is intended for
		 * @param int    $obj_id
		 */
		public function show_generate_barcode_button( $type = 'shop_order', $obj_id ) {
			?>
			<div class="ywbc-generate-barcode">
				<label for="ywbc-value"><?php esc_html_e( 'Code', 'yith-woocommerce-barcodes' ); ?></label>
				<input type="text" name="ywbc-value" class="ywbc-value-field"/>
				<div>
					<span style="font-size: smaller"><?php esc_html_e( 'Enter the code or leave empty for automatic code', 'yith-woocommerce-barcodes' ); ?></span>
					<button class="button button-primary ywbc-generate"
					        data-id="<?php echo $obj_id; ?>"
					        data-type="<?php echo $type; ?>"><?php esc_html_e( 'Generate', 'yith-woocommerce-barcodes' ); ?></button>
				</div>
			</div>
			<?php
		}


		/**
		 * apply barcode to product
		 * @since 1.0.2
		 */
		public function apply_barcode_to_products() {

			$item_id = isset( $_POST['ywbc_item_id'] ) ? $_POST['ywbc_item_id'] : false;
			$result  = 'error_on_create_barcode';
			if ( $item_id ) {
				/**
				 * @var YITH_Barcode $barcode
				 */
				$barcode = $this->create_product_barcode( $item_id );

				if ( $barcode->object_id ) {

					$result = 'barcode_created';
				}
			}
			wp_send_json( array( 'result' => $result ) );
		}

        /**
         * print all products barcodes
         * @since 1.0.2
         */
        public function print_product_barcodes_document() {

            $item_ids = isset( $_POST['ywbc_item_ids'] ) ? $_POST['ywbc_item_ids'] : false;

            $args = array(
                'item_ids'   => $item_ids,
            );

            ob_start();
            wc_get_template( '/print/print-barcodes-template.php', $args, '', YITH_YWBC_TEMPLATE_PATH );
            $print_template = ob_get_clean();

            wp_send_json( array( 'result' => $print_template ) );

        }

		/**
		 * print barcodes by product
		 * @since 2.0.0
		 */
		public function print_barcodes_by_product_document() {

			$product_id = isset( $_POST['ywbc_product_id'] ) ? $_POST['ywbc_product_id'] : '';
			$quantity = isset( $_POST['ywbc_product_quantity'] ) ? $_POST['ywbc_product_quantity'] : 1;

			$args = array(
				'product_id'   => $product_id,
				'quantity'   => $quantity,
			);

			ob_start();
			wc_get_template( '/print/print-barcodes-by-product-template.php', $args, '', YITH_YWBC_TEMPLATE_PATH );
			$print_template = ob_get_clean();

			wp_send_json( array( 'result' => $print_template ) );

		}


		public function load_gutenberg_compatibility(){
            $blocks = array(
                'yith-product-barcode' => array(
                    'style'          => 'ywbc-style',
                    'script'        => 'ywbc-frontend',
                    'title'          => _x( 'Product Barcode', '[gutenberg]: block name', 'yith-woocommerce-barcodes' ),
                    'description'    => _x( 'With this block you can search a product by barcode and manage the stock', '[gutenberg]: block description', 'yith-woocommerce-barcodes' ),
                    'shortcode_name' => 'yith_product_barcode',
                    'callback'       => 'yith_product_barcode',
                    'do_shortcode'   => true,
                    'keywords'       => array(
                        _x( 'Product', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                        _x( 'Barcode', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                        _x( 'Stock', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                        _x( 'Search', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                    ),
                    'attributes'     => array(
                        'capability'      => array(
                            'type'    => 'text',
                            'label'   => _x( 'Capability', '[gutenberg]: Option title', 'yith-woocommerce-barcodes' ),
                            'default' => 'manage_woocommerce',
                        ),
                        'actions'    => array(
                            'type'    => 'select',
                            'label'   => _x( 'Actions', '[gutenberg]: attribute description', 'yith-woocommerce-barcodes' ),
                            'default' => array('search'),
                            'multiple'=> true,
                            'options'   => array(
                                'search'   => _x( 'Search', '[gutenberg]: Help text', 'yith-woocommerce-barcodes' ),
                                'increase-stock' => _x( 'Increase stock', '[gutenberg]: Help text', 'yith-woocommerce-barcodes' ),
                                'decrease-stock' => _x( 'Decrease stock', '[gutenberg]: Help text', 'yith-woocommerce-barcodes' ),
                            ),
                        ),
                    )
                ),
                'yith-order-barcode' => array(
                    'style'          => 'ywbc-style',
                    'title'          => _x( 'Order Barcode', '[gutenberg]: block name', 'yith-woocommerce-barcodes' ),
                    'description'    => _x( 'With this block you can search an order by barcode and manage its status', '[gutenberg]: block description', 'yith-woocommerce-barcodes' ),
                    'shortcode_name' => 'yith_order_barcode',
                    'callback'       => 'yith_order_barcode',
                    'do_shortcode'   => true,
                    'keywords'       => array(
                        _x( 'Order', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                        _x( 'Barcode', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                        _x( 'Status', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                        _x( 'Search', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                    ),
                    'attributes'     => array(
                        'search_type'      => array(
                            'type'    => 'text',
                            'label'   => _x( 'Search type', '[gutenberg]: Option title', 'yith-woocommerce-barcodes' ),
                            'default' => 'shop_order',
                        ),
                        'style'      => array(
                            'type'    => 'text',
                            'label'   => _x( 'Style', '[gutenberg]: Option title', 'yith-woocommerce-barcodes' ),
                            'default' => 'buttons',
                        ),
                        'capability'      => array(
                            'type'    => 'text',
                            'label'   => _x( 'Capability', '[gutenberg]: Option title', 'yith-woocommerce-barcodes' ),
                            'default' => 'manage_woocommerce',
                        ),
                        'actions'    => array(
                            'type'    => 'select',
                            'label'   => _x( 'Actions', '[gutenberg]: attribute description', 'yith-woocommerce-barcodes' ),
                            'default' => array('completed'),
                            'multiple'=> true,
                            'options'   => array(
                                'completed'   => _x( 'Set status as "Complete"', '[gutenberg]: Help text', 'yith-woocommerce-barcodes' ),
                                'processing' => _x( 'Set status as "Processing"', '[gutenberg]: Help text', 'yith-woocommerce-barcodes' ),
                            ),
                        ),
                    )
                ),
                'yith-render-barcode' => array(
                    'style'          => 'ywbc-style',
                    'title'          => _x( 'Render Barcode', '[gutenberg]: block name', 'yith-woocommerce-barcodes' ),
                    'description'    => _x( 'With this block you can show a barcode by product or order ID', '[gutenberg]: block description', 'yith-woocommerce-barcodes' ),
                    'shortcode_name' => 'yith_render_barcode',
                    'callback'       => 'yith_render_barcode',
                    'do_shortcode'   => true,
                    'keywords'       => array(
                        _x( 'Order', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                        _x( 'Barcode', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                        _x( 'Status', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                        _x( 'Render', '[gutenberg]: keywords', 'yith-woocommerce-barcodes' ),
                    ),
                    'attributes'     => array(
                        'id'      => array(
                            'type'    => 'text',
                            'label'   => _x( 'ID', '[gutenberg]: Option title', 'yith-woocommerce-barcodes' ),
                            'default' => 0,
                        ),
                        'hide_if_empty'      => array(
                            'type'    => 'checkbox',
                            'label'   => _x( 'Hide if empty', '[gutenberg]: Option title', 'yith-woocommerce-barcodes' ),
                            'default' => true,
                            'helps'   => array(
                                'checked'   => _x( 'Hide if empty', '[gutenberg]: Help text', 'yith-woocommerce-barcodes' ),
                                'unchecked' => _x( 'Show if empty', '[gutenberg]: Help text', 'yith-woocommerce-barcodes' ),
                            ),
                        ),
                        'value'      => array(
                            'type'    => 'text',
                            'label'   => _x( 'Value', '[gutenberg]: Option title', 'yith-woocommerce-barcodes' ),
                            'default' => '',
                        ),
                        'protocol'       => array(
                            'type'    => 'radio',
                            'label'   => _x( 'Select protocol to use', '[gutenberg]: block description', 'yith-woocommerce-barcodes' ),
                            'options' => array(
                                'EAN13' => _x( 'EAN-13', '[gutenberg]: inspector description', 'yith-woocommerce-barcodes' ),
                                'EAN8' => _x( 'EAN-8', '[gutenberg]: inspector description', 'yith-woocommerce-barcodes' ),
                                'STD25' => _x( 'STD 25', '[gutenberg]: inspector description', 'yith-woocommerce-barcodes' ),
                                'INT25' => _x( 'INT 25', '[gutenberg]: inspector description', 'yith-woocommerce-barcodes' ),
                                'CODE39' => _x( 'CODE 39', '[gutenberg]: inspector description', 'yith-woocommerce-barcodes' ),
                                'code93' => _x( 'CODE 93', '[gutenberg]: inspector description', 'yith-woocommerce-barcodes' ),
                                'code128' => _x( 'CODE 128', '[gutenberg]: inspector description', 'yith-woocommerce-barcodes' ),
                                'Codabar' => _x( 'Codabar', '[gutenberg]: inspector description', 'yith-woocommerce-barcodes' ),
                                'QRcode' => _x( 'QR code', '[gutenberg]: inspector description', 'yith-woocommerce-barcodes' ),
                            ),
                            'default' => 'EAN13'
                        ),
                        'inline_css'      => array(
                            'type'    => 'text',
                            'label'   => _x( 'Inline CSS', '[gutenberg]: Option title', 'yith-woocommerce-barcodes' ),
                            'default' => '',
                        )
                    )
                )
            );

            yith_plugin_fw_gutenberg_add_blocks( $blocks );
        }


	}
}

YITH_YWBC_Backend::get_instance();
