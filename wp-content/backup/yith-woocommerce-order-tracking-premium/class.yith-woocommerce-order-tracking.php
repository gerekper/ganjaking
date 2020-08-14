<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_WooCommerce_Order_Tracking' ) ) {
	
	/**
	 * Implements features of Yith WooCommerce Order Tracking
	 *
	 * @class   Yith_WooCommerce_Order_Tracking
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_WooCommerce_Order_Tracking {
		
		/**
		 * @var array order ids with pickedup statsus change
		 */
		protected $pickedup_status_changed = array();
		
		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;
		
		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';
		
		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'http://yithemes.com/themes/plugins/yith-woocommerce-order-tracking/';
		
		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-order-tracking/';
		
		/**
		 * @var string Yith WooCommerce Order Tracking panel page
		 */
		protected $_panel_page = 'yith_woocommerce_order_tracking_panel';
		
		
		/**
		 * @var mixed|void  Default carrier name
		 */
		protected $default_carrier;
		
		/**
		 * @var string  Customizable text to be shown on orders
		 */
		protected $orders_pattern;
		
		/**
		 * @var position of text related to order details page
		 */
		protected $order_text_position;
		
		
		/**
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 */
		public function __construct() {
			
			add_action( 'admin_init', array( $this, 'register_pointer' ) );
			
			$this->initialize_settings();
			/**
			 *  Create YIT menu items for current plugin
			 */
			$this->create_menu_items();
			
			/**
			 * Add metabox on order, to let vendor add order tracking code and carrier
			 */
			add_action( 'add_meta_boxes', array( $this, 'add_order_tracking_metabox' ), 10, 2 );

			/**
			 * Save Order Meta Boxes
			 * */
			add_action( 'woocommerce_process_shop_order_meta', array(
				$this,
				'save_order_tracking_metabox'
			), 30, 2 );
			
			/**
			 * register action to show tracking information on customer order page
			 */
			$this->register_order_tracking_actions();
			
			/**
			 * Show icon on order list for picked up orders
			 */
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'prepare_picked_up_icon' ) );
			
			/**
			 * Set default carrier name on new orders
			 */
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'set_default_carrier' ) );
			
			add_action( 'yith_order_tracking_premium', array( $this, 'premium_tab' ) );
			
			/**
			 * Show shipped icon on my orders page
			 */
			add_action( 'woocommerce_my_account_my_orders_actions', array(
				$this,
				'show_picked_up_icon_on_orders',
			), 99, 2 );
			
			/**
			 * Enqueue scripts and styles
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
		
		/**
		 * Set values from plugin settings page
		 */
		public function initialize_settings() {
			$this->default_carrier     = get_option( 'ywot_carrier_default_name' );
			$this->orders_pattern      = get_option( 'ywot_order_tracking_text' );
			$this->order_text_position = get_option( 'ywot_order_tracking_text_position' );
		}
		
		/**
		 * Add scripts
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 */
		public function enqueue_scripts() {
			global $post;
			
			wp_register_script( "tooltipster",
				YITH_YWOT_URL . 'assets/js/jquery.tooltipster.min.js',
				array( 'jquery' ),
				false,
				true );
			wp_enqueue_script( 'tooltipster' );
			
			wp_enqueue_style( 'ywot_style', YITH_YWOT_URL . 'assets/css/ywot_style.css' );
            wp_enqueue_style( 'ywot_font', YITH_YWOT_URL . 'assets/css/fonts.css' );

			wp_register_script( "ywot_script",
                apply_filters( 'yith_wc_order_tracking_ywot_js_path', YITH_YWOT_URL . 'assets/js/ywot.js' ),
				array( 'jquery-form', 'jquery' ),
				YITH_YWOT_VERSION,
				true );
			
			$premium = defined( 'YITH_YWOT_PREMIUM' );
			
			wp_localize_script( 'ywot_script', 'ywot', array(
				'p'        => $premium,
				'ajax_url' => admin_url( 'admin-ajax.php' ),
                'tooltip'  => apply_filters( 'yith_wc_order_tracking_tooltip', 'yes' ),
			) );

			wp_enqueue_script( 'ywot_script' );
		}
		
		/**
		 * Set default carrier name when an order is created (if related option is set).
		 *
		 * @param int $post_id post id being created
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		public function set_default_carrier( $post_id ) {

            if ( isset( $this->default_carrier ) && ( strlen ( $this->default_carrier ) > 0 ) ) {
                $order = wc_get_order ( $post_id );
                if ( $order ) {

                    if ( defined ( 'YITH_YWOT_PREMIUM' ) ) {
                        yit_save_prop ( $order, array( 'ywot_carrier_id' => $this->default_carrier ) );
                    } else {
                        yit_save_prop ( $order, array( 'ywot_carrier_name' => $this->default_carrier ) );
                    }
                }
            }
		}
		
		/**
		 * Show a picked up icon on backend orders table
		 *
		 * @param   $column the column of backend order table being elaborated
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		public function prepare_picked_up_icon( $column ) {
			
			//  If column is not of type order_status, skip it
			if ( 'order_status' !== $column ) {
				return;
			}
			
			global $the_order;
			
			//  if current order is not flagged as picked up, skip
			if ( ! $this->is_order_shipped( $the_order ) ) {
				return;
			}
			
			$this->show_picked_up_icon( $the_order );
		}
		
		/**
		 *
		 * Build a text which indicates order tracking information
		 *
		 * @param WC_Order $order   post meta for current order
		 * @param string   $pattern text pattern to be used
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @return string
		 */
		public function get_picked_up_message( $order, $pattern = '' ) {
			/**
			 * Base function not useful
			 */
			return;
			
		}
		
		/**
		 * Show a image link, stating the order has been picked up
		 *
		 * @param int|WC_Order $order     the order to show
		 * @param string       $css_class the custom CSS
		 */
		public function show_picked_up_icon( $order, $css_class = '' ) {
			if ( ! $this->is_order_shipped( $order ) ) {
				return;
			}
			
			$message = $this->get_picked_up_message( $order );
			?>
			<a class="button track-button <?php echo $css_class; ?>" href="#" data-title="<?php echo $message; ?>">
                <span class="ywot-icon-delivery track-button track-icon" data-title="<?php echo $message; ?>"></span>
				<?php _e( 'Track', 'yith-woocommerce-order-tracking' ); ?>
			</a>
			<?php
		}
		
		
		/**
		 * Show on my orders page, a link image stating the order has been picked
		 *
		 * @param array    $actions others actions registered to the same hook
		 * @param WC_Order $order   the order being shown
		 *
		 * @return mixed    action passed as arguments
		 */
		public function show_picked_up_icon_on_orders( $actions, $order ) {
			
			if ( function_exists( 'yith_wcmv_is_premium' ) && yith_wcmv_is_premium() ) {
				$order_id = yit_get_prop( $order, 'id' );
				
				$sub_orders = apply_filters( 'yith_wcmv_suborder_icon_on_orders', YITH_Orders_Premium::get_suborder( $order_id ) );
				
				if ( $sub_orders ) {
					$message  = esc_html__( 'This order contains shipping costs from more than one seller. Click here to see details.', 'yith-woocommerce-order-tracking' );
					$shipped  = false;
					$url      = $order->get_view_order_url() . '#tracking_details';
					$to_print = '<a class="track-button button" style="display:inline-block;height:25px; padding-top:0; padding-bottom:0" href="' . $url . '" data-title="' . $message . '"><img class="track-button" style="height:25px;" src="' . YITH_YWOT_ASSETS_URL . '/images/delivery.svg" data-title="' . $message . '" /></a>';
					
					if ( $this->is_order_shipped( $order ) ) {
						$shipped = true;
					} else {
						foreach ( $sub_orders as $sub_order ) {
							if ( $this->is_order_shipped( $sub_order ) ) {
								
								$shipped = true;
								break;
							}
						}
					}
					
					if ( $shipped ) {
						echo $to_print;
						
						return $actions;
					}
				}
			}
			
			if ( $this->is_order_shipped( $order ) ) {
				$this->show_picked_up_icon( $order, 'button' );
			}
			
			return $actions;
		}
		
		
		/**
		 * Add callback to show shipping details on order page, in the position choosen from plugin settings
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		public function register_order_tracking_actions() {

			if ( ! isset( $this->order_text_position ) || ( 1 == $this->order_text_position ) ) {
				if( version_compare( WC()->version,'3.0.0','<' ) ){
					add_action( 'woocommerce_order_items_table', array( $this, 'add_order_shipping_details' ) );
				}else{
					add_action( 'woocommerce_order_details_after_order_table_items', array( $this, 'add_order_shipping_details' ) );
				}

			} else {
				add_action( 'woocommerce_order_details_after_order_table', array(
					$this,
					'add_order_shipping_details',
				) );

			}
		}
		
		
		/**
		 * Show order tracking information on user order page when the order is set to "completed"
		 *
		 * @param $order WC_Order    the order whose tracking information have to be shown
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		function add_order_shipping_details( $order ) {
			$order_id = yit_get_prop( $order, 'id' );
			
			if ( function_exists( 'yith_wcmv_is_premium' ) && yith_wcmv_is_premium() ) {
				//  Show a message stating that shipping details entered refers to the admin products only
				if ( YITH_Orders_Premium::get_suborder( $order_id ) ) {
					return;
				}
			}
			
			$container_class = "ywot_order_details";
			//  add top or bottom class, depending on the value of related option
			if ( 1 == $this->order_text_position ) {
				$container_class .= " top";
			} else {
				$container_class .= " bottom";
			}
			
			echo '<div class="' . $container_class . '">' . $this->show_tracking_information( $order, $this->orders_pattern, '' ) . '</div>';
		}
		
		/**
		 * Show message about the order tracking details.
		 *
		 * @param WC_Order $order   the order whose tracking information have to be shown
		 * @param string   $pattern custom text to be shown
		 * @param string   $prefix  Prefix to be shown before custom text
		 *
		 * @since  1.0
		 * @access public
		 * @return void|string
		 */
		function show_tracking_information( $order, $pattern, $prefix = '', $output = 'order' ) {
			$order_id = yit_get_prop( $order, 'id' );
			
			if ( ! $this->is_order_shipped( $order_id ) ) {
				return '';
			}
			
			$message = $this->get_picked_up_message( $order, $pattern );
			
			return $prefix . $message;
		}
		
		/**
		 * Register actions and filters to be used for creating an entry on YIT Plugin menu
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		private function create_menu_items() {
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			

            /* === Show Plugin Information === */
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWOT_DIR . '/' . basename( YITH_YWOT_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );


			
			//  Add stylesheets and scripts files
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
		}
		
		/**
		 * Load YIT core plugin
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}
		
		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {
			
			if ( ! empty( $this->_panel ) ) {
				return;
			}
			
			$admin_tabs = array(
				'general' => esc_html__( 'General', 'yith-woocommerce-order-tracking' ),
			);
			
			if ( defined( 'YITH_YWOT_PREMIUM' ) ) {
				$admin_tabs['carriers'] = esc_html__( 'Carriers', 'yith-woocommerce-order-tracking' );
			} else {
				$admin_tabs['premium-landing'] = esc_html__( 'Premium Version', 'yith-woocommerce-order-tracking' );
			}
			
			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'Order Tracking',
				'menu_title'       => 'Order Tracking',
				'capability'       => apply_filters( 'ywot_panel_capabilities', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWOT_DIR . '/plugin-options',
                'class'            => yith_set_wrapper_class(),
			);
			
			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				
				require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}
			
			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}
		
		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function premium_tab() {
			
			$premium_tab_template = YITH_YWOT_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}
		
		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 */
        public function action_links( $links ) {
            $links = yith_add_action_links( $links, $this->_panel_page, false );
            return $links;
        }


		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWOT_FREE_INIT' ) {
            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $new_row_meta_args['slug'] = YITH_YWOT_SLUG;
            }

            return $new_row_meta_args;
        }


        public function register_pointer() {
			if ( ! class_exists( 'YIT_Pointers' ) ) {
				include_once( 'plugin-fw/lib/yit-pointers.php' );
			}
			
			$premium_message = defined( 'YITH_YWOT_PREMIUM' )
				? ''
				: esc_html__( 'YITH WooCommerce Order Tracking is available in an outstanding PREMIUM version with many new options, discover it now.', 'yith-woocommerce-order-tracking' ) .
				  ' <a href="' . $this->get_premium_landing_uri() . '">' . esc_html__( 'Premium version', 'yith-woocommerce-order-tracking' ) . '</a>';
			
			$args[] = array(
				'screen_id'  => 'plugins',
				'pointer_id' => 'yith_woocommerce_order_tracking',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					esc_html__( 'YITH WooCommerce Order Tracking', 'yith-woocommerce-order-tracking' ),
					esc_html__( 'In YIT Plugins tab you can find YITH WooCommerce Order Tracking options. From this menu you can access all settings of YITH plugins activated.', 'yith-woocommerce-order-tracking' ) . '<br>' . $premium_message
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'       => defined( 'YITH_YWOT_PREMIUM' ) ? YITH_YWOT_INIT : YITH_YWOT_FREE_INIT,
			);
			
			YIT_Pointers()->register( $args );
		}
		
		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
            return $this->premium_landing_url;
		}
		
		/**
		 * Add a metabox on backend order page, to be filled with order tracking information
		 *
		 * @param string  $post_type the current post type
		 * @param WP_Post $post
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		function add_order_tracking_metabox( $post_type, $post ) {

			if ( "shop_order" != $post_type ) {
				return;
			}

			$order_id = $post instanceof WP_Post ? $post->ID : $post;
			
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return;
			}
			
			if ( ! apply_filters( 'yith_woocommerce_order_tracking_for_vendor_enabled', true, $order ) ) {
				return;
			}

			add_meta_box( 'yith-order-tracking-information', _x( 'Order tracking', 'Order tracking metabox title' , 'yith-woocommerce-order-tracking' ), array(
				$this,
				'show_order_tracking_metabox',
			), 'shop_order', 'side', 'high' );
		}
		
		/**
		 * Show metabox content for tracking information on backend order page
		 *
		 * @param WP_Post $post the order object that is currently shown
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		function show_order_tracking_metabox( $post ) {
			
		}
		
		/**
		 * Save additional data to the order its going to be saved. We add tracking code, carrier name and data of picking.
		 *
		 * @param int     $post_id the order id
		 * @param WP_Post $post
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		function save_order_tracking_metabox( $post_id, $post ) {
			if ( 'shop_order' != $post->post_type ) {
				return;
			}
			
			$order = wc_get_order( $post_id );
			$this->update_order_status( $order );
		}
		
		/**
		 * @param WC_Order $order
		 */
		public function update_order_status( $order ) {
			
			$picked_up_val = isset( $_POST['ywot_picked_up'] );
			$tracking_code = stripslashes( sanitize_text_field( $_POST['ywot_tracking_code'] ) );
			$tracking_postcode = stripslashes( sanitize_text_field( $_POST['ywot_tracking_postcode'] ) );
			$carrier_id    = stripslashes( sanitize_text_field( $_POST['ywot_carrier_id'] ) );
			$pick_up_date  = stripslashes( sanitize_text_field( $_POST['ywot_pick_up_date'] ) );
			
			$track_data         = new YITH_Tracking_Data( $order );
			$prev_picked_status = $track_data->is_pickedup();
			
			$track_data->set(
				array(
					'ywot_tracking_code' => $tracking_code,
					'ywot_tracking_postcode' => $tracking_postcode,
					'ywot_carrier_id'    => $carrier_id,
					'ywot_pick_up_date'  => $pick_up_date,
					'ywot_picked_up'     => $picked_up_val
				) );
			
			$track_data->save();
			
			if ( $track_data->is_pickedup() && ! $prev_picked_status ) {
				$this->pickedup_status_changed[] = $order;
			}
		}
		
		/**
		 * Check if the order is flagged as shipped
		 *
		 * @param WC_Order|int $order the order to check
		 *
		 * @return bool
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 *
		 */
		public function is_order_shipped( $order ) {
			
			$data = YITH_Tracking_Data::get( $order );
			
			return $data->is_pickedup();
		}
	}
}