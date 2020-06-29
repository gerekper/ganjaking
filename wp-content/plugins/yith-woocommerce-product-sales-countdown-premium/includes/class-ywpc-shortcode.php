<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YWPC_Shortcode' ) ) {

	/**
	 * Implements shortcode for Product Countdown plugin
	 *
	 * @class   YWPC_Shortcode
	 * @since   1.0.0
	 * @author  Alberto Ruggiero
	 *
	 */
	class YWPC_Shortcode {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'add_shortcodes_button' ), 20 );
			add_action( 'init', array( $this, 'gutenberg_block' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_shortcode_scripts' ) );
			add_action( 'admin_action_ywpc_shortcodes_panel', array( $this, 'add_shortcodes_panel' ) );
			add_shortcode( 'ywpc_shortcode', array( $this, 'set_shortcode' ) );

		}

		/**
		 * Add scripts and styles
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function admin_shortcode_scripts( $hook ) {

			global $pagenow;

			wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
			wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . ywpc_get_minified() . '.js', array( 'jquery' ) );
			wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . ywpc_get_minified() . '.js', array( 'jquery', 'selectWoo' ), WC_VERSION );
			wp_register_script( 'ywpc-shortcode', YWPC_ASSETS_URL . '/js/ywpc-shortcode' . ywpc_get_minified() . '.js', array( 'jquery' ), YWPC_VERSION );


			if ( ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) && $this->can_show_shortcode_buttons() ) {

				wp_enqueue_script( 'ywpc-shortcode' );

				global $post_ID, $temp_ID;

				$query_args = array(
					'action'    => 'ywpc_shortcodes_panel',
					'post_id'   => (int) ( 0 == $post_ID ? $temp_ID : $post_ID ),
					'KeepThis'  => true,
					'TB_iframe' => true
				);

				wp_localize_script( 'ywpc-shortcode', 'ywpc_shortcode', array(
					'lightbox_url'   => add_query_arg( $query_args, admin_url( 'admin.php' ) ),
					'lightbox_title' => esc_html__( 'Add YITH WooCommerce Product Countdown shortcode', 'yith-woocommerce-product-countdown' ),

				) );

			}

			if ( $hook == 'ywpc-shortcode' ) {

				wp_enqueue_style( 'woocommerce_admin_styles' );
				wp_enqueue_script( 'wc-enhanced-select' );
				wp_localize_script( 'wc-enhanced-select', 'wc_enhanced_select_params', array(
					'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
					'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
					'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
					'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
					'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
					'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
					'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
					'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
					'ajax_url'                  => admin_url( 'admin-ajax.php' ),
					'search_products_nonce'     => wp_create_nonce( 'search-products' ),
					'search_customers_nonce'    => wp_create_nonce( 'search-customers' )
				) );

			}

		}

		/**
		 * Add shortcode button to TinyMCE editor, adding filter on mce_external_plugins
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_shortcodes_button() {

			global $post;

			if ( ! $post ) {
				return;
			}

			add_filter( 'mce_external_plugins', array( &$this, 'add_shortcodes_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( &$this, 'register_shortcodes_button' ) );
			add_action( 'media_buttons_context', array( &$this, 'media_buttons_context' ) );

		}

		/**
		 * Add a script to TinyMCE script list
		 *
		 * @since   1.0.0
		 *
		 * @param   $plugin_array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function add_shortcodes_tinymce_plugin( $plugin_array ) {

			if ( $this->can_show_shortcode_buttons() ) {

				$plugin_array['ywpc_shortcode'] = YWPC_ASSETS_URL . '/js/ywpc-tinymce' . ywpc_get_minified() . '.js';

			}

			return $plugin_array;

		}

		/**
		 * Make TinyMCE know a new button was included in its toolbar
		 *
		 * @since   1.0.0
		 *
		 * @param   $buttons
		 *
		 * @return  array()
		 * @author  Alberto Ruggiero
		 */
		public function register_shortcodes_button( $buttons ) {

			if ( $this->can_show_shortcode_buttons() ) {

				array_push( $buttons, "|", "ywpc_shortcode" );
			}

			return $buttons;

		}

		/**
		 * The markup of shortcode
		 *
		 * @since   1.0.0
		 *
		 * @param   $context
		 *
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function media_buttons_context( $context ) {

			if ( $this->can_show_shortcode_buttons() ) {
				$context .= '<a id="ywpc_shortcode" href="#" class="hide-if-no-js" title=""></a>';
			}

			return $context;

		}

		/**
		 * Get shortcode panel
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_shortcodes_panel() {

			@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );

			?>
            <html xmlns="http://www.w3.org/1999/xhtml" <?php do_action( 'admin_xml_ns' ); ?> <?php language_attributes(); ?>>
            <head>
                <meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
                <title><?php ?></title>
                <script type="text/javascript">
                    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ) ?>';
                </script>

				<?php

				$hook_suffix = 'ywpc-shortcode';

				wp_admin_css( 'wp-admin', true );
				do_action( 'admin_enqueue_scripts', $hook_suffix );
				do_action( 'admin_print_styles' );
				do_action( 'admin_print_scripts' );
				do_action( 'admin_head' ); ?>
                <style type="text/css">

                    body {
                        padding: 10px;
                    }

                    html, body {
                        background: #fff;
                    }

                    .button {
                        background: #00a0d2;
                        border-color: #0073aa;
                        -webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, .5), 0 1px 0 rgba(0, 0, 0, .15);
                        box-shadow: inset 0 1px 0 rgba(120, 200, 230, .5), 0 1px 0 rgba(0, 0, 0, .15);
                        color: #fff;
                        text-decoration: none;
                        display: inline-block;
                        font-size: 13px;
                        line-height: 26px;
                        height: 28px;
                        margin: 0;
                        padding: 0 10px 1px;
                        cursor: pointer;
                        border-width: 1px;
                        border-style: solid;
                        -webkit-appearance: none;
                        -webkit-border-radius: 3px;
                        border-radius: 3px;
                        white-space: nowrap;
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                        font-family: inherit;
                        font-weight: inherit;
                    }

                    .button:focus {
                        border-color: #0e3950;
                        -webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6), 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, .8);
                        box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6), 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, .8);
                    }

                    .button:hover {
                        background: #0091cd;
                        border-color: #0073aa;
                        -webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6);
                        box-shadow: inset 0 1px 0 rgba(120, 200, 230, .6);
                        color: #fff;
                    }

                    input[type="radio"] {
                        height: 16px;
                        width: 16px;
                    }

                    input[type="radio"]:checked:before {
                        vertical-align: middle;
                        width: 6px;
                        height: 6px;
                        margin: 4px;
                        line-height: 16px;
                    }

                </style>

            </head>
            <body class="shortcode-lightbox">
            <div class="widget-content">
                <p style="margin-top:0;">
					<?php esc_html_e( 'Shortcode Style', 'yith-woocommerce-product-countdown' ); ?><br />
                    <label style="margin-right: 10px">
                        <input name="ywpc_shortcode_style" value="multiple" type="radio" checked="checked"><?php esc_html_e( 'Multiple product', 'yith-woocommerce-product-countdown' ); ?>
                    </label>
                    <label>
                        <input name="ywpc_shortcode_style" value="single" type="radio"><?php esc_html_e( 'Single product', 'yith-woocommerce-product-countdown' ); ?>
                    </label>
                </p>

                <p class="ywpc-select-multiple">
                    <label for="ywpc_product_search"><?php esc_html_e( 'Products to show', 'yith-woocommerce-product-countdown' ); ?></label>
					<?php

					$select_args = array(
						'class'            => 'wc-product-search',
						'id'               => 'ywpc_product_search_multiple',
						'name'             => 'ywpc_product_search',
						'data-placeholder' => esc_html__( 'Search for a product&hellip;', 'yith-woocommerce-product-countdown' ),
						'data-allow_clear' => false,
						'data-selected'    => '',
						'data-multiple'    => true,
						'data-action'      => 'woocommerce_json_search_products',
						'value'            => '',
						'style'            => 'width: 100%'
					);

					yit_add_select2_fields( $select_args )

					?>
                </p>

                <p class="ywpc-select-single" style="display: none;">
                    <label for="ywpc_product_search"><?php esc_html_e( 'Products to show', 'yith-woocommerce-product-countdown' ); ?></label>
					<?php

					$select_args = array(
						'class'            => 'wc-product-search',
						'id'               => 'ywpc_product_search_single',
						'name'             => 'ywpc_product_search',
						'data-placeholder' => esc_html__( 'Search for a product&hellip;', 'yith-woocommerce-product-countdown' ),
						'data-allow_clear' => false,
						'data-selected'    => '',
						'data-multiple'    => false,
						'data-action'      => 'woocommerce_json_search_products',
						'value'            => '',
						'style'            => 'width: 100%'
					);

					yit_add_select2_fields( $select_args )

					?>
                </p>
            </div>
            <div class="widget-control-actions">
                <div class="alignright">
                    <input type="submit" name="ywpc_shortcode_insert" id="ywpc_shortcode_insert" class="button" value="<?php esc_html_e( 'Insert', 'yith-woocommerce-product-countdown' ); ?>">
                </div>
                <br class="clear">
            </div>
            <script type="text/javascript">

                jQuery(function ($) {

                    $(document).on('click', '.button', function () {

                        var active = '';

                        $('input[type="radio"]').each(function () {

                            if ($(this).is(':checked')) {

                                active = $(this).val();

                            }

                        });


                        var code = $('#ywpc_product_search_' + active).val(),
                            str = '',
                            win = window.dialogArguments || opener || parent || top;

                        if (active === 'single') {

                            if (code === '') {

                                window.alert('<?php esc_html_e( 'You should select at least a product', 'yith-woocommerce-product-countdown' ); ?>');

                            } else {

                                str = '[ywpc_shortcode id="' + code + '" type="single"]';

                            }

                        } else {

                            if (code === '') {

                                str = '[ywpc_shortcode]';

                            } else {

                                str = '[ywpc_shortcode id="' + code + '"]';

                            }

                        }

                        if (str !== '') {

                            win.send_to_editor(str);
                            var ed;

                            if (typeof tinyMCE !== 'undefined' && (ed = tinyMCE.activeEditor) && !ed.isHidden()) {
                                ed.setContent(ed.getContent());
                            }

                        }

                    });

                    $(document).on('click', 'input[type="radio"]', function () {

                        if ($(this).val() === 'single') {
                            $('.ywpc-select-single').show();
                            $('.ywpc-select-multiple').hide();
                        } else {
                            $('.ywpc-select-multiple').show();
                            $('.ywpc-select-single').hide();
                        }

                    });

                });

            </script>
			<?php do_action( 'admin_print_footer_scripts' ); ?>
            </body>
            </html>
			<?php

		}

		/**
		 * Check if shortcode buttons can be shown on the edit page
		 *
		 * @since   1.0.0
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function can_show_shortcode_buttons() {

			return ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) );

		}

		/**
		 * Set ywpc shortcode
		 *
		 * @since   1.0.0
		 *
		 * @param   $atts
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function set_shortcode( $atts ) {

			shortcode_atts( array(
				                'id'   => '',
				                'type' => ''
			                ), $atts );

			add_filter( 'widget_text', 'shortcode_unautop' );
			add_filter( 'widget_text', 'do_shortcode' );

			$ids = '';

			if ( isset( $atts['id'] ) ) {
				$ids = explode( ',', str_replace( ' ', '', $atts['id'] ) );
				$ids = array_map( 'trim', $ids );
			}

			ob_start();

			if ( isset( $atts['type'] ) && $atts['type'] == 'single' ) {

				if ( $ids == '' || $ids[0] == 'null' ) {
					global $post;
					$product_id = apply_filters( 'ywpc_manipulate_current_post', $post->ID );

				} else {
					$product_id = $ids[0];
				}

				ywpc_get_template( $product_id, 'single-product' );

			} else {

				$options = array(
					'show_title'     => get_option( 'ywpc_shortcode_title', 'yes' ),
					'show_rating'    => get_option( 'ywpc_shortcode_rating', 'yes' ),
					'show_price'     => get_option( 'ywpc_shortcode_price', 'yes' ),
					'show_image'     => get_option( 'ywpc_shortcode_image', 'yes' ),
					'show_addtocart' => get_option( 'ywpc_shortcode_addtocart', 'yes' ),
				);

				YITH_WPC()->get_ywpc_custom_loop( $ids, 'shortcode', $options );

			}

			return ob_get_clean();

		}

		/**
		 * Set shortcode
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function gutenberg_block() {

			$blocks = array(
				'ywpc-shortcode' => array(
					'style'          => 'ywpc-frontend',
					'title'          => _x( 'Product Countdown', '[gutenberg]: block name', 'yith-woocommerce-product-countdown' ),
					'description'    => _x( 'Add the Product Countdown shortcode', '[gutenberg]: block description', 'yith-woocommerce-product-countdown' ),
					'shortcode_name' => 'ywpc_shortcode',
					'do_shortcode'   => true,
					'keywords'       => array(
						_x( 'Product Countdown', '[gutenberg]: keywords', 'yith-woocommerce-product-countdown' ),
					),
					'attributes'     => array(
						'type' => array(
							'type'    => 'select',
							'label'   => _x( 'Select type', '[gutenberg]: block description', 'yith-woocommerce-product-countdown' ),
							'options' => array(
								'single'   => _x( 'Single product counter', '[gutenberg]: inspector description', 'yith-woocommerce-product-countdown' ),
								'multiple' => _x( 'Multiple product counter', '[gutenberg]: inspector description', 'yith-woocommerce-product-countdown' ),
							),
							'default' => 'multiple',
						),
						'id'   => array(
							'type'  => 'text',
							'label' => _x( 'Select product(s)', '[gutenberg]: block description', 'yith-woocommerce-product-countdown' ),
						)
					),
				),
			);

			yith_plugin_fw_gutenberg_add_blocks( $blocks );

		}

	}

}

new YWPC_Shortcode();
