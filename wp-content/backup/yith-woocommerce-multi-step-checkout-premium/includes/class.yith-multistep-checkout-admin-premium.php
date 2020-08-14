<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCMS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Multistep_Checkout_Admin_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Andrea Grillo <andrea.grillo@yithemes.com>
 *
 */

if ( ! class_exists( 'YITH_Multistep_Checkout_Admin_Premium' ) ) {
	/**
	 * Class YITH_Multistep_Checkout_Admin_Premium
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 */
	class YITH_Multistep_Checkout_Admin_Premium extends YITH_Multistep_Checkout_Admin {

		/**
		 * Construct
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 */
		public function __construct() {
			$this->show_premium_landing = false;

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			/* === Premium Options === */
			add_filter( 'yith_wcms_admin_tabs', array( $this, 'admin_tabs' ) );

			/* === Enqueue Scripts === */
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );

			/* === WooCommerce Options Customizzation === */
			add_action( 'woocommerce_admin_field_yith_wcms_media_upload', array( $this, 'option_media_upload' ) );

			parent::__construct();
		}

		/**
		 * Add premium admin tabs options
		 *
		 * @param $free Array The tabs array
		 *
		 * @return Array The tabs array
		 * @since 1.0.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function admin_tabs( $free ) {
			$premium = array(
				'steps'    => esc_html_x( 'Steps options', 'Admin: Page title', 'yith-woocommerce-multi-step-checkout' ),
				'buttons'  => esc_html_x( 'Buttons options', 'Admin: Page title', 'yith-woocommerce-multi-step-checkout' ),
				'thankyou' => esc_html_x( 'Pages Options', 'Admin: Page title', 'yith-woocommerce-multi-step-checkout' ),
			);

			return array_merge( $free, $premium );
		}

		/**
		 * Admin enqueue scripts
		 *
		 *
		 * @return void
		 * @since 1.0.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function enqueue_scripts() {
			$is_plugin_panel = ! empty( $_GET['page'] ) && $_GET['page'] == $this->get_panel_page();
			$current_tab     = ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'steps';
			$script_version  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? rand() : YITH_WCMS_VERSION;
			$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$localize = array(
				'current_tab' => ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'steps',
				'icons_url'  => YITH_WCMS_ASSETS_URL . 'images/icons/'
			);
		    wp_register_script( 'yith-wcms-admin', YITH_WCMS_ASSETS_URL . 'js/admin' . $prefix .'.js', array( 'jquery' ), $script_version, true );
			wp_localize_script( 'yith-wcms-admin', 'yith_wcms_admin', $localize );

			if ( $is_plugin_panel ) {
				$css    = '';
				$handle = 'yith-plugin-fw-fields';

			    if( 'steps' == $current_tab || 'buttons' == $current_tab ){
					$css .= '.yith-wcms-disabled{display:none!important} .yith-wcms-default-icon-item{margin-right: 5px; vertical-align: text-bottom;} .yith-wcms-default-icon-wrapper {text-transform: capitalize;}';
					$css .= '#yith_wcms_timeline_template-wrapper .yith-plugin-fw-select-images__item.horizontal {width: 25%; display: inline-block;}';
					$css .= '#yith_wcms_timeline_template-wrapper .yith-plugin-fw-select-images__list {display:block;}';
					$css .= '#yith_wcms_timeline_template-wrapper .yith-plugin-fw-select-images__item.vertical {display: inline-block;width: calc(25% - 10px);max-height: 250px;max-width: 180px;}';
					$css .= '@media(max-width: 768px){#yith_wcms_timeline_template-wrapper .yith-plugin-fw-select-images__item {width: auto;}}';
					wp_enqueue_script('yith-wcms-admin' );
				}

				$css = apply_filters( 'yith_wcms_add_inline_styles', $css );

				if( ! empty( $css ) ){
					wp_add_inline_style( $handle, $css );
				}
			}
		}

		/**
		 * Custom WooCommerce upload option
		 *
		 * @param $value array The option value array
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0.0
		 *
		 */
		public function option_media_upload( $value ) {
			$image_id = get_option( $value['id'], $value['default'] );
			$args     = array(
				'image_wrapper_id'    => 'yith_wcms_image_wrapper_id_' . $value['custom_attributes']['data-step'],
				'hidden_field_id'     => 'yith_wcms_hidden_field_id_' . $value['custom_attributes']['data-step'],
				'hidden_field_name'   => 'yith_wcms_hidden_field_name_' . $value['custom_attributes']['data-step'],
				'remove_image_button' => 'yith_wcms_remove_image_button_' . $value['custom_attributes']['data-step'],
				'upload_image_button' => 'yith_wcms_upload_image_button',
			);

			extract( $args );
			ob_start(); ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                </th>
                <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
                    <div id="<?php echo $args['image_wrapper_id']; ?>" class="yith-wcms-icon-preview"
                         style="background-color: #e2e2e2; padding: 5px; display: inline-block; margin: 0 10px 10px 0;">
                        <img src="<?php echo ! is_numeric( $image_id ) ? $value['default'] : wp_get_attachment_url( $image_id ); ?>"
                             style="max-height: 50px; width: auto;"/>
                    </div>

                    <input type="hidden" id="<?php echo $value['id'] ?>" name="<?php echo $value['id'] ?>"
                           value="<?php echo is_numeric( $image_id ) ? $image_id : '' ?>"
                           data-default="<?php echo is_numeric( $image_id ) ? 'no' : 'yes' ?>"/>
                    <button style="vertical-align: bottom; margin-bottom: 10px;" type="button"
                            class="<?php echo $upload_image_button; ?> button"
                            data-step="<?php echo $value['custom_attributes']['data-step']; ?>"><?php _e( 'Upload/Add Icon', 'yith-woocommerce-multi-step-checkout' ); ?></button>
                    <button style="vertical-align: bottom; margin-bottom: 10px;" type="button"
                            id="<?php echo $remove_image_button ?>" class="button yith_wcms_remove_image_button"
                            data-step="<?php echo $value['custom_attributes']['data-step']; ?>"
                            data-default="<?php echo is_numeric( $image_id ) ? 'no' : 'yes' ?>"><?php _e( 'Restore default icon', 'yith-woocommerce-multi-step-checkout' ); ?></button>
                    <span class="description" style="display: block;"><?php echo $value['desc']; ?></span>
                </td>
            </tr>
			<?php echo ob_get_clean();
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_activation() {
			YIT_Plugin_Licence()->register( YITH_WCMS_INIT, YITH_WCMS_SECRETKEY, YITH_WCMS_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_updates() {
			YIT_Upgrade()->register( YITH_WCMS_SLUG, YITH_WCMS_INIT );
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6.5
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
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   Array
		 * @since    1.6.5
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCMS_INIT' ) {
			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Get the default icons list
		 *
		 * @return string[] The icons list
		 * @since 2.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function get_default_icons_list() {
			return array(
				'login'     => 'login',
				'login2'    => 'login2',
				'billing'   => 'billing',
				'billing2'  => 'billing2',
				'shipping'  => 'shipping',
				'shipping2' => 'shipping2',
				'order'     => 'order',
				'order2'    => 'order2',
				'payment'   => 'payment',
				'payment2'  => 'payment2',
			);
		}
	}
}
