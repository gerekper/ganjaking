<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Main class
 *
 * @class   YITH_Livechat_Premium
 * @since   1.0.0
 * @author  Your Inspiration Themes
 * @package Yithemes
 */

if ( ! class_exists( 'YITH_Livechat_Premium' ) ) {

	class YITH_Livechat_Premium extends YITH_Livechat {

		/**
		 * @var string Yith Live Chat Offline Messages
		 */
		protected $_offline_messages_page = 'ylc_offline_messages';

		/**
		 * @var string Yith Live Chat Logs
		 */
		protected $_chat_logs_page = 'ylc_chat_logs';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_Livechat_Premium
		 * @since 1.1.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self;

			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			parent::__construct();

			$this->includes_premium();


			add_filter( 'ylc_vendor', array( $this, 'get_vendor_info' ) );
			add_filter( 'ylc_vendor_only', array( $this, 'set_vendor_only' ) );
			add_filter( 'ylc_gdpr_compliance', array( $this, 'set_gdpr_compliance' ) );
			add_filter( 'ylc_chat_gdpr_compliance', array( $this, 'set_chat_gdpr_compliance' ) );

			add_filter( 'ylc_nickname', array( $this, 'get_nickname' ) );
			add_filter( 'ylc_avatar_type', array( $this, 'get_avatar_type' ) );
			add_filter( 'ylc_avatar_image', array( $this, 'get_avatar_image' ) );
			add_filter( 'ylc_console_avatar', array( $this, 'get_console_avatar' ) );
			add_filter( 'ylc_default_avatar', array( $this, 'encoded_default_avatar_url' ), 10, 2 );
			add_filter( 'ylc_company_avatar', array( $this, 'get_company_avatar' ) );

			add_action( 'init', array( $this, 'register_styles_scripts_premium' ), 27 );

			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {

				add_action( 'wp_enqueue_scripts', array( $this, 'custom_css' ) );
				add_filter( 'ylc_plugin_opts_premium', array( $this, 'get_frontend_premium_options' ) );
				add_filter( 'ylc_max_guests', array( $this, 'get_max_guests' ) );
				add_filter( 'ylc_frontend_opts', array( $this, 'set_frontend_opts' ), 10 );
				add_filter( 'ylc_busy_form', array( $this, 'set_busy_form' ) );
				add_filter( 'ylc_can_show_chat', array( $this, 'show_chat_button' ), 10, 1 );

			} else {

				add_action( 'ylc_additional_init_setup', array( $this, 'add_premium_menu' ) );

				add_action( 'init', array( $this, 'update_operators' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'premium_admin_scripts' ), 100 );

				add_filter( 'yith_wcmv_live_chat_caps', array( $this, 'add_vendor_capability' ) );
				add_filter( 'yith_wpv_vendor_menu_items', array( $this, 'activate_vendor' ) );

				if ( current_user_can( 'answer_chat' ) ) {
					add_action( 'show_user_profile', array( &$this, 'custom_operator_fields' ), 10 );
					add_action( 'personal_options_update', array( &$this, 'save_custom_operator_fields' ) );
				}

				add_action( 'edit_user_profile', array( &$this, 'custom_operator_fields' ), 10 );
				add_action( 'edit_user_profile_update', array( &$this, 'save_custom_operator_fields' ) );

			}

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

		}

		function add_premium_menu() {
			add_action( 'admin_menu', array( $this, 'add_console_submenu' ), 10 );
		}

		/**
		 * Include required core files
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function includes_premium() {

			// Back-end includes
			if ( is_admin() ) {
				include_once( 'includes/admin/class-yith-custom-table.php' );
				include_once( 'templates/admin/ylc-offline-table.php' );
				include_once( 'templates/admin/ylc-chat-log-table.php' );
			}

			include_once( 'includes/functions-ylc-gdpr.php' );
			include_once( 'includes/class-ylc-macro.php' );
			include_once( 'includes/class-ylc-mailer.php' );
			include_once( 'includes/class-ylc-logger.php' );
			include_once( 'includes/functions-ylc-ajax-premium.php' );
			include_once( 'includes/functions-ylc-email-premium.php' );
			include_once( 'includes/functions-ylc-commons-premium.php' );

		}

		/**
		 * Add styles and scripts for Chat Console or Chat Frontend
		 *
		 * @return  void
		 * @since   1.1.0
		 * @author  Alberto Ruggiero
		 */
		public function register_styles_scripts_premium() {

			wp_register_style( 'ylc-tiptip', yit_load_css_file( YLC_ASSETS_URL . '/css/tipTip.css' ), array(), YLC_VERSION );

			wp_register_script( 'jquery-tiptip', yit_load_js_file( YLC_ASSETS_URL . '/js/jquery-tipTip.js' ), array( 'jquery' ), YLC_VERSION, true );
			wp_register_script( 'ylc-admin-premium', yit_load_js_file( YLC_ASSETS_URL . '/js/ylc-admin-premium.js' ), array( 'jquery', 'wp-color-picker', 'select2' ), YLC_VERSION, true );
			wp_register_script( 'ylc-admin-premium-table', yit_load_js_file( YLC_ASSETS_URL . '/js/ylc-admin-premium-table.js' ), array( 'jquery', 'jquery-tiptip' ), YLC_VERSION, true );

			$localization = array(
				'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'yith-live-chat' ),
				'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'yith-live-chat' ),
				'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'yith-live-chat' ),
				'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'yith-live-chat' ),
				'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'yith-live-chat' ),
				'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'yith-live-chat' ),
				'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'yith-live-chat' ),
				'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'yith-live-chat' ),
				'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'yith-live-chat' ),
				'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'yith-live-chat' ),
				'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'yith-live-chat' ),
				'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'yith-live-chat' ),
			);
			wp_localize_script( 'ylc-admin-premium', 'ylc', $localization );

		}

		/**
		 * Get vendor info (if is active YITH WooCommerce Multi Vendor premium)
		 *
		 * @return  array|int
		 * @since   1.1.0
		 * @author  Alberto Ruggiero
		 */
		public function get_vendor_info() {

			$result = array(
				'vendor_id'   => 0,
				'vendor_name' => ''
			);

			$vendor = '';

			if ( defined( 'YITH_WPV_PREMIUM' ) ) {

				if ( is_admin() || ylc_frontend_manager() ) {
					$vendor = yith_get_vendor( 'current', 'user' );

				} else {

					if ( YITH_Vendors()->frontend->is_vendor_page() ) {
						$vendor = yith_get_vendor( get_query_var( 'term' ) );
					} else {

						global $post;

						if ( $post && 'product' == $post->post_type ) {
							$_product = is_singular( 'product' ) ? WC()->product_factory->get_product( absint( $post->ID ) ) : __return_null();
							$vendor   = yith_get_vendor( $_product, 'product' );
						} elseif ( $post && 'product' != $post->post_type ) {
							$vendor = yith_get_vendor( $post->post_author, 'user' );
						}

					}

				}

				if ( $vendor ) {
					$result['vendor_id']   = $vendor->id;
					$result['vendor_name'] = ( $vendor->id != 0 ) ? $vendor->term->name : '';
				}

			}

			return $result;

		}

		/**
		 * Encode default avatar URLs for Gravatar
		 *
		 * @param   $value string
		 * @param   $type  string
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function encoded_default_avatar_url( $value = '', $type ) {

			$value .= esc_html( YLC_ASSETS_URL . '/images/default-avatar-' . $type . '.png' );

			return $value;
		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Add YITH Live Chat to vendor admin panel (if is active YITH WooCommerce Multi Vendor premium)
		 *
		 * @param   $pages array
		 *
		 * @return  array
		 * @since   1.1.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function activate_vendor( $pages ) {

			$pages[] = $this->_console_page;
			$pages[] = $this->_offline_messages_page;
			$pages[] = $this->_chat_logs_page;

			return $pages;

		}

		/**
		 * Add chat capability to vendor operators (if is active YITH WooCommerce Multi Vendor premium)
		 *
		 * @return  array|int
		 * @since   1.1.0
		 * @author  Alberto Ruggiero
		 */
		public function add_vendor_capability() {
			return array( 'answer_chat' => true );
		}

		/**
		 * Add submenu under YITH Live Chat console page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_console_submenu() {

			if ( current_user_can( 'answer_chat' ) ) {

				if ( ! ylc_multivendor_check() ) {
					return;
				}

				add_submenu_page( $this->_console_page, esc_html__( 'Chat console', 'yith-live-chat' ), esc_html__( 'Chat console', 'yith-live-chat' ), 'answer_chat', $this->_console_page, array( $this, 'get_console_template' ) );
				add_submenu_page( $this->_console_page, esc_html__( 'Offline messages', 'yith-live-chat' ), esc_html__( 'Offline messages', 'yith-live-chat' ), 'answer_chat', $this->_offline_messages_page, array( YLC_Offline_Messages(), 'output' ) );
				add_submenu_page( $this->_console_page, esc_html__( 'Chat logs', 'yith-live-chat' ), esc_html__( 'Chat logs', 'yith-live-chat' ), 'answer_chat', $this->_chat_logs_page, array( YLC_Chat_Logs(), 'output' ) );
				add_submenu_page( $this->_console_page, esc_html__( 'Chat macros', 'yith-live-chat' ), esc_html__( 'Chat macros', 'yith-live-chat' ), 'answer_chat', 'edit.php?post_type=ylc-macro' );

			}

		}

		/**
		 * Add styles and scripts for options panel
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function premium_admin_scripts() {

			switch ( ylc_get_current_page() ) {

				case $this->_panel_page:

					yith_plugin_fw_enqueue_enhanced_select();
					wp_enqueue_script( 'ylc-admin-premium' );

					break;

				case $this->_offline_messages_page:
				case $this->_chat_logs_page:
				case 'profile.php':
				case 'user-edit.php':

					wp_enqueue_media();
					wp_enqueue_style( 'ylc-tiptip' );
					wp_enqueue_style( 'ylc-styles' );
					wp_enqueue_script( 'ylc-admin-premium-table' );

					break;

				case $this->_console_page:

					yith_plugin_fw_enqueue_enhanced_select();

					break;

			}

		}

		/**
		 * Get user nickname
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_nickname() {

			$nickname = wp_get_current_user()->get( 'ylc_operator_nickname' );

			if ( ! $nickname ) {
				$nickname = wp_get_current_user()->nickname;

			}

			return $nickname;

		}

		/**
		 * Add custom operator fields
		 *
		 * @param   $user WP_User
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function custom_operator_fields( $user ) {

			if ( ! ylc_multivendor_check() || ! current_user_can( 'edit_user', $user->ID ) ) {
				return;
			}

			$options = array(
				'default'  => esc_html__( 'Default Avatar', 'yith-live-chat' ),
				'image'    => esc_html__( 'Uploaded Image', 'yith-live-chat' ),
				'gravatar' => esc_html__( 'Gravatar', 'yith-live-chat' ),
			)

			?>

			<h3>
				YITH Live Chat
			</h3>
			<span class="description"><?php esc_html_e( 'Remember to refresh the chat console page after updating the operator name or avatar', 'yith-live-chat' ); ?></span>
			<table class="form-table">
				<tr class="ylc-op-nickname">
					<th>
						<label for="ylc_operator_nickname">
							<?php esc_html_e( 'Operator Nickname', 'yith-live-chat' ); ?>
						</label>
					</th>
					<td>
						<input type="text" name="ylc_operator_nickname" id="ylc_operator_nickname" value="<?php echo esc_attr( get_the_author_meta( 'ylc_operator_nickname', $user->ID ) ); ?>" class="regular-text" />
						<br>

						<p class="description">
							<?php esc_html_e( 'If not specified, the system will use the default user nickname.', 'yith-live-chat' ); ?>
						</p>
					</td>
				</tr>
				<tr class="ylc-op-avatar">
					<th>
						<label for="ylc_operator_avatar_type">
							<?php esc_html_e( 'Operator Avatar', 'yith-live-chat' ); ?>
						</label>
					</th>
					<td>
						<div class="avatar">
							<div class="preview">
								<?php

								$file = ylc_get_image( get_the_author_meta( 'ylc_operator_avatar_type', $user->ID ), $user );

								?>
								<img src="<?php echo $file; ?>" />
							</div>
							<select name="ylc_operator_avatar_type" id="ylc_operator_avatar_type">
								<?php foreach ( $options as $val => $opt ) : ?>
									<option value="<?php echo $val ?>"<?php selected( get_the_author_meta( 'ylc_operator_avatar_type', $user->ID ), $val ); ?>>
										<?php echo $opt; ?>
									</option>
								<?php endforeach; ?>
							</select>

							<div class="upload">
								<label>
									<input type="text" name="ylc_operator_avatar" id="ylc_operator_avatar" value="<?php echo esc_attr( get_the_author_meta( 'ylc_operator_avatar', $user->ID ) ); ?>" />
									<input type="button" value="<?php esc_html_e( 'Upload', 'yith-live-chat' ) ?>" id="ylc_operator_avatar_button" class="button" />
								</label>
							</div>
							<input type="hidden" id="ylc_image" value="<?php echo ylc_get_image( 'image', $user ); ?>" />
							<input type="hidden" id="ylc_gravatar" value="<?php echo ylc_get_image( 'gravatar', $user ); ?>" />
							<input type="hidden" id="ylc_default" value="<?php echo ylc_get_image( '', $user ); ?>" />
						</div>
					</td>
				</tr>
			</table>
			<?php
		}

		/**
		 * Save custom operator fields
		 *
		 * @param   $user_id integer
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_custom_operator_fields( $user_id ) {

			if ( ! ylc_multivendor_check() || ! current_user_can( 'edit_user', $user_id ) ) {
				return;
			}

			if ( empty( $_POST['ylc_operator_nickname'] ) ) {

				$op_name = get_the_author_meta( 'nickname', $user_id );

			} else {
				$op_name = $_POST['ylc_operator_nickname'];
			}

			// Update user meta now
			update_user_meta( $user_id, 'ylc_operator_nickname', $op_name );
			update_user_meta( $user_id, 'ylc_operator_avatar_type', $_POST['ylc_operator_avatar_type'] );
			update_user_meta( $user_id, 'ylc_operator_avatar', $_POST['ylc_operator_avatar'] );

		}

		/**
		 * Updates operator roles
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function update_operators() {

			if ( isset( $_GET['settings-updated'] ) && 'true' == $_GET['settings-updated'] ) {

				$inherit_role = get_option( 'ylc_op_prev_role' );

				if ( $inherit_role != ylc_get_option( 'operator-role', ylc_get_default( 'operator-role' ) ) ) {

					update_option( 'ylc_op_prev_role', ylc_get_option( 'operator-role', ylc_get_default( 'operator-role' ) ) );

					$this->ylc_operator_role( ylc_get_option( 'operator-role', ylc_get_default( 'operator-role' ) ) );

				}

			}

			if ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_wpv_panel' ) {

				$role = get_role( 'yith_vendor' );

				if ( isset( $_POST['yith_wpv_vendors_option_live_chat_management'] ) ) {
					$role->add_cap( 'answer_chat' );
				} else {
					$role->remove_cap( 'answer_chat' );
				}

			}

		}

		/**
		 * Get user avatar image
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_avatar_image() {

			$avatar_type = get_the_author_meta( 'ylc_operator_avatar_type', $this->user->ID );

			if ( $avatar_type == 'default' ) {

				$company_avatar = $this->get_company_avatar();

				if ( $company_avatar != '' ) {
					return $company_avatar;

				} else {

					return '';
				}

			} else {

				return get_the_author_meta( 'ylc_operator_avatar', $this->user->ID );

			}

		}

		/**
		 * Get user avatar type
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_avatar_type() {

			$avatar_type = get_the_author_meta( 'ylc_operator_avatar_type', $this->user->ID );

			if ( $avatar_type == 'default' ) {

				$company_avatar = $this->get_company_avatar();

				if ( $company_avatar != '' ) {
					$avatar_type = 'image';
				}

			}

			return $avatar_type;

		}

		/**
		 * Get operator console avatar
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_console_avatar() {

			$avatar_type = apply_filters( 'ylc_avatar_type', 'default' );
			$user        = wp_get_current_user();

			return ylc_get_image( $avatar_type, $user );

		}

		/**
		 * Get company avatar
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_company_avatar() {

			return esc_html( ylc_get_option( 'operator-avatar', '' ) );

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Get Premium Options
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_frontend_premium_options() {

			return array(
				'bg_color'       => ylc_get_option( 'header-button-color', ylc_get_default( 'header-button-color' ) ),
				'x_pos'          => $this->get_chat_position( 'x' ),
				'y_pos'          => $this->get_chat_position( 'y' ),
				'border_radius'  => $this->set_round_corners( ylc_get_option( 'border-radius', ylc_get_default( 'border-radius' ) ), ylc_get_option( 'chat-position', ylc_get_default( 'chat-position' ) ) ),
				'popup_width'    => ylc_get_option( 'chat-conversation-width', ylc_get_default( 'chat-conversation-width' ) ),
				'btn_type'       => $this->get_chat_button_type(),
				'btn_width'      => ( $this->get_chat_button_type() == 'round' ? ylc_get_option( 'chat-button-diameter', ylc_get_default( 'chat-button-diameter' ) ) : ylc_get_option( 'chat-button-width', ylc_get_default( 'chat-button-width' ) ) ),
				'btn_height'     => ( $this->get_chat_button_type() == 'round' ? ylc_get_option( 'chat-button-diameter', ylc_get_default( 'chat-button-diameter' ) ) : 0 ),
				'form_width'     => ylc_get_option( 'form-width', ylc_get_default( 'form-width' ) ),
				'animation_type' => ylc_get_option( 'chat-animation', ylc_get_default( 'chat-animation' ) ),
				'autoplay_delay' => $this->set_autoplay_delay( ylc_get_option( 'autoplay-delay', ylc_get_default( 'autoplay-delay' ) ) )
			);

		}

		/**
		 * Set Frontend Options
		 *
		 * @return  array
		 * @since   1.2.1
		 * @author  Alberto Ruggiero
		 */
		public function set_frontend_opts() {

			return array(
				'button_type' => $this->get_chat_button_type(),
				'button_pos'  => $this->get_chat_position( 'y' ),
				'form_width'  => 'width: ' . ylc_get_option( 'form-width', ylc_get_default( 'form-width' ) ) . 'px;',
				'chat_width'  => 'width: ' . ylc_get_option( 'chat-conversation-width', ylc_get_default( 'chat-conversation-width' ) ) . 'px;',
			);

		}

		/**
		 * Get chat position
		 *
		 * @param   $pos string
		 *
		 * @return  string
		 * @since   1.2.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_chat_position( $pos ) {

			$positions = explode( '-', apply_filters( 'ylc_frontend_position', ylc_get_option( 'chat-position', ylc_get_default( 'chat-position' ) ) ) );

			return ( $pos == 'x' ? $positions[0] : $positions[1] );

		}

		/**
		 * Get chat button type
		 *
		 * @return  string
		 * @since   1.2.0
		 * @author  Alberto Ruggiero
		 */
		public function get_chat_button_type() {

			return apply_filters( 'ylc_frontend_button_type', ylc_get_option( 'chat-button-type', ylc_get_default( 'chat-button-type' ) ) );

		}

		/**
		 * Set border radius
		 *
		 * @param   $pos    string
		 * @param   $radius integer
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function set_round_corners( $radius, $pos ) {

			if ( $radius != 0 ) {

				$positions = explode( '-', apply_filters( 'ylc_frontend_position', $pos ) );

				if ( $positions[1] == 'bottom' ) {

					return $radius . 'px ' . $radius . 'px 0 0';

				} else {

					return '0 0 ' . $radius . 'px ' . $radius . 'px';

				}

			} else {

				return 0;

			}

		}

		/**
		 * Set chat autoplay delay
		 *
		 * @param   $delay integer
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function set_autoplay_delay( $delay ) {

			return $delay * 1000;

		}

		/**
		 * Add Custom CSS
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function custom_css() {

			if ( apply_filters( 'ylc_can_show_chat', true ) ) {

				$custom_css = ylc_get_option( 'custom-css' );

				if ( $custom_css != '' ) {

					wp_add_inline_style( 'ylc-frontend', stripslashes( $custom_css ) );

				}

			}

		}

		/**
		 * Get max chat users
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_max_guests() {

			return ylc_get_option( 'max-chat-users', ylc_get_default( 'max-chat-users' ) );

		}

		/**
		 * Set offline form when busy
		 *
		 * @return  boolean
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function set_busy_form() {

			return ylc_get_option( 'offline-busy', ylc_get_default( 'offline-busy' ) ) == 'yes';
		}

		/**
		 * Set chat for vendor sections only (if is active YITH WooCommerce Multi Vendor premium)
		 *
		 * @return  boolean
		 * @since   1.1.2
		 * @author  Alberto Ruggiero
		 */
		public function set_vendor_only() {

			return ylc_get_option( 'only-vendor-chat', ylc_get_default( 'only-vendor-chat' ) ) == 'yes';

		}

		/**
		 * Set chat offline form GDPR compliance
		 *
		 * @return  boolean
		 * @since   1.2.6
		 * @author  Alberto Ruggiero
		 */
		public function set_gdpr_compliance() {

			return ylc_get_option( 'offline-gdpr-compliance', ylc_get_default( 'offline-gdpr-compliance' ) ) == 'yes';

		}

		/**
		 * Set chat GDPR compliance
		 *
		 * @return  boolean
		 * @since   1.2.7
		 * @author  Alberto Ruggiero
		 */
		public function set_chat_gdpr_compliance() {

			return ylc_get_option( 'chat-gdpr-compliance', ylc_get_default( 'chat-gdpr-compliance' ) ) == 'yes';

		}

		/**
		 * Checks if chat can be showed
		 *
		 * @param   $show boolean
		 *
		 * @return  boolean
		 * @since   1.1.3
		 *
		 * @author  Alberto Ruggiero
		 */
		public function show_chat_button( $show ) {

			if ( is_admin() ) {
				return false;
			}

			//hide is operatore are ffline
			if ( ylc_get_option( 'hide-chat-offline', ylc_get_default( 'hide-chat-offline' ) == 'yes' ) ) {

				$response = wp_remote_get( 'https://' . ylc_get_option( 'firebase-appurl', ylc_get_default( 'firebase-appurl' ) ) . '.firebaseio.com/active_admins.json' );

				if ( is_wp_error( $response ) ) {
					return false;
				}

				$online_ops = json_decode( $response['body'] );

				if ( ( $online_ops < 1 ) || empty( $online_ops ) || $online_ops === 'null' || $online_ops === null ) {

					return false;

				}

			}

			//hide on mobile devices
			if ( ylc_get_option( 'hide-mobile', 'no' ) == 'yes' && wp_is_mobile() ) {
				return false;
			}

			//hide for non-logged users
			if ( ylc_get_option( 'hide-guest', 'no' ) == 'yes' && ! is_user_logged_in() ) {
				return false;
			}

			if ( defined( 'YITH_WPV_PREMIUM' ) && ylc_get_option( 'only-vendor-chat', ylc_get_default( 'only-vendor-chat' ) ) == 'yes' ) {

				$vendor_info = $this->get_vendor_info();

				if ( $vendor_info['vendor_id'] == 0 ) {

					return false;

				}

			}

			if ( ylc_get_option( 'showing-pages-all', ylc_get_default( 'showing-pages-all' ) ) != 'yes' ) {

				//Check if homepage is enabled
				if ( ylc_get_option( 'showing-home-page', 'no' ) == 'yes' && ylc_check_current_page( 'home' ) ) {
					return true;
				}

				//Check if blog is enabled
				if ( ylc_get_option( 'showing-blog', 'no' ) == 'yes' && ylc_check_current_page( 'blog' ) ) {
					return true;
				}

				//Check if shop & product pages are enabled
				if ( function_exists( 'WC' ) && ylc_get_option( 'showing-shop', 'no' ) == 'yes' && ylc_check_current_page( 'shop' ) ) {
					return true;
				}

				global $post;
				$allowed_pages = ylc_get_option( 'showing-pages', array() );

				//check if current page is enabled
				if ( ! empty( $allowed_pages ) && in_array( $post->ID, $allowed_pages ) ) {
					return true;
				}

				$show = false;

			}

			return $show;

		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Register plugins for activation tab
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YLC_INIT, YLC_SECRET_KEY, YLC_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YLC_SLUG, YLC_INIT );
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $links | links plugin array
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $new_row_meta_args
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YLC_INIT' ) {

			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

	}

}