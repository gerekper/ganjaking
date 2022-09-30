<?php
/**
 * Class for showing backward switch menu since 1.4.0
 * This will be removed in 1.5.0
 *
 * @author  YITH
 * @package YITH WooCommerce Membership
 */

! defined( 'YITH_WCMBS' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Legacy_Elements' ) ) {
	/**
	 * YITH_WCMBS_Legacy_Menu
	 * Class for showing backward switch menu and "Set access" meta-box since 1.4.0
	 *
	 * @since    1.4.0
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCMBS_Legacy_Elements {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCMBS_Legacy_Elements
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCMBS_Legacy_Elements
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 */
		protected function __construct() {
			if ( current_user_can( 'manage_options' ) ) {
				$show_membership_menu = 'yes' === get_option( 'yith-wcmbs-legacy-show-membership-menu-in-wp-menu', 'no' );
				if ( $show_membership_menu ) {
					add_action( 'admin_menu', array( $this, 'show_legacy_menu' ) );
					add_action( 'admin_init', array( $this, 'remove_menu_handler' ) );
				}

				$show_set_access_metabox = 'yes' === get_option( 'yith-wcmbs-legacy-show-set-access-metabox', 'no' );
				if ( $show_set_access_metabox ) {
					add_action( 'add_meta_boxes', array( $this, 'add_legacy_set_access_meta_box' ) );
					add_action( 'wp_ajax_yith_wcmbs_legacy_remove_set_access_meta_box', array( $this, 'remove_set_access_meta_box' ) );
				}
			}
		}


		/**
		 * Check for legacy elements on Activation hook
		 */
		public static function check_for_legacy_elements() {
			$is_update = false !== get_option( 'yith-wcmbs-hide-contents', false );
			if ( false === get_option( 'yith-wcmbs-legacy-show-membership-menu-in-wp-menu', false ) ) {
				update_option( 'yith-wcmbs-legacy-show-membership-menu-in-wp-menu', $is_update ? 'yes' : 'no' );
			}

			if ( false === get_option( 'yith-wcmbs-legacy-show-set-access-metabox', false ) ) {
				update_option( 'yith-wcmbs-legacy-show-set-access-metabox', $is_update ? 'yes' : 'no' );
			}
		}

		/**
		 * Maybe show the legacy menu
		 */
		public function show_legacy_menu() {
			add_menu_page(
				__( 'Membership', 'yith-woocommerce-membership' ),
				__( 'Membership', 'yith-woocommerce-membership' ),
				'manage_options',
				'yith-wcmbs-membership-legacy-menu',
				array( $this, 'print_legacy_menu' ),
				'dashicons-groups',
				30
			);
		}

		/**
		 * Print the legacy menu page
		 */
		public function print_legacy_menu() {
			$remove_url = add_query_arg( array( 'yith-wcmbs-legacy-membership-menu-remove' => wp_create_nonce( 'remove-legacy-membership-menu' ) ) );
			?>
			<style>
                .yith-wcmbs-membership-legacy-menu__notice {
                    padding    : 60px 25px;
                    margin     : 20px 20px 20px 0;
                    background : #fff;
                    text-align : center;
                    font-size  : 1.2em;
                }

                .yith-wcmbs-membership-legacy-menu__notice__text {
                    max-width   : 820px;
                    margin      : 0 auto;
                    line-height : 2em;
                }

                .yith-wcmbs-membership-legacy-menu__notice__cta {
                    display         : inline-block;
                    margin          : 60px 0 20px;
                    padding         : 20px 25px;
                    background      : #0073aa;
                    color           : #fff;
                    text-decoration : none;
                    font-weight     : 700;
                    text-transform  : uppercase;
                    border-radius   : 4px;
                }

                .yith-wcmbs-membership-legacy-menu__notice__cta:hover,
                .yith-wcmbs-membership-legacy-menu__notice__cta:active,
                .yith-wcmbs-membership-legacy-menu__notice__cta:focus {
                    background : #0083c1;
                    color      : #fff;
                }
			</style>
			<div class="yith-wcmbs-membership-legacy-menu-wrap">
				<div class="yith-wcmbs-membership-legacy-menu__notice">
					<div class="yith-wcmbs-membership-legacy-menu__notice__text">
						<?php
						echo sprintf(
						// translators: 1. plugin version; 2. plugin name; 3. menu name with link.
							esc_html__( 'Since version %1$s of %2$s we moved all membership settings to a new panel that you can find in %3$s, so you can access to all plugin settings from there.', 'yith-woocommerce-membership' ),
							'<strong>1.4.0</strong>',
							'<strong>YITH WooCommerce Membership</strong>',
							'<strong>YITH > Membership</strong>'
						);
						?>
					</div>
					<a class="yith-wcmbs-membership-legacy-menu__notice__cta" href="<?php echo esc_url_raw( $remove_url ); ?>"><?php esc_html_e( 'Go to the new panel', 'yith-woocommerce-membership' ) ?></a>
				</div>
			</div>
			<?php
		}

		/**
		 * Remove menu and redirect to the new panel
		 */
		public function remove_menu_handler() {
			if ( ! empty( $_REQUEST['yith-wcmbs-legacy-membership-menu-remove'] ) && wp_verify_nonce( $_REQUEST['yith-wcmbs-legacy-membership-menu-remove'], 'remove-legacy-membership-menu' ) ) {
				update_option( 'yith-wcmbs-legacy-show-membership-menu-in-wp-menu', 'no' );
				wp_safe_redirect( admin_url( 'admin.php?page=yith_wcmbs_panel' ) );
				exit;
			}
		}

		/**
		 * Add the legacy "Set access" meta-box
		 */
		public function add_legacy_set_access_meta_box() {
			add_meta_box(
				'yith_wcmbs_legacy_set_access_meta_box',
				__( 'Set access', 'yith-woocommerce-membership' ),
				array( $this, 'render_legacy_set_access_meta_box' ),
				YITH_WCMBS_Manager()->post_types,
				'side',
				'high'
			);
		}

		/**
		 * Render Legacy "Set access" meta-box
		 */
		public function render_legacy_set_access_meta_box() {
			?>
			<style>
                #yith_wcmbs_legacy_set_access_meta_box {
                    border        : none;
                    box-shadow    : 1px 2px 10px 0 rgba(0, 0, 0, 0.1);
                    border-radius : 8px;
                    text-align    : center;
                }

                #yith_wcmbs_legacy_set_access_meta_box > .postbox-header {
                    padding : 15px 15px 0;
                }

                #yith_wcmbs_legacy_set_access_meta_box > .inside {
                    padding : 0 22px 28px;
                }

                #yith_wcmbs_legacy_set_access_meta_box .handle-actions {
                    display : none;
                }

                #yith_wcmbs_legacy_set_access_meta_box .postbox-header {
                    border : none;
                }

                #yith_wcmbs_legacy_set_access_meta_box .postbox-header > h2,
                #yith_wcmbs_legacy_set_access_meta_box .postbox-header > h2.hndle {
                    text-align     : center;
                    display        : block;
                    text-transform : uppercase;
                    color          : #2a8db0;
                    border         : none;
                }

                .yith-wcmbs-legacy-set-access__notice__cta {
                    display     : block;
                    margin      : 20px auto 0;
                    font-weight : 600;
                    box-shadow  : none;
                }

                a.yith-wcmbs-legacy-set-access__notice__cta:focus {
                    box-shadow : none;
                }
			</style>
			<div class="yith-wcmbs-legacy-set-access">
				<div class="yith-wcmbs-legacy-set-access__notice">

					<?php
					echo sprintf(
					// translators: 1. plugin version; 2. plugin name; 3. box name.
						esc_html__( 'Since version %1$s of %2$s we moved the options of the "Set access" box to the new "%3$s" box.', 'yith-woocommerce-membership' ),
						'<strong>1.4.0</strong>',
						'<strong>YITH WooCommerce Membership</strong>',
						'<strong>' . esc_html__( 'Membership options', 'yith-woocommerce-membership' ) . '</strong>'
					);
					?>
					<a class="yith-wcmbs-legacy-set-access__notice__cta" href="#"><?php esc_html_e( 'Go to "Membership options"', 'yith-woocommerce-membership' ) ?></a>
				</div>
			</div>

			<script type="text/javascript">
				jQuery( function ( $ ) {
					var legacyMetaBox = $( '#yith_wcmbs_legacy_set_access_meta_box' ),
						newMetaBox    = $( '#yith-wcmbs-membership-options' ),
						scrollTo      = function ( _element, disableInGutenberg ) {
							disableInGutenberg = typeof disableInGutenberg !== 'undefined' ? disableInGutenberg : false;
							var _offset        = _element.offset(),
								root           = $( '.interface-interface-skeleton__content' ).first(),
								isInGutenberg  = true;

							if ( !root.length ) {
								root          = $( 'html, body' );
								isInGutenberg = false;
							}

							if ( isInGutenberg && disableInGutenberg ) {
								return;
							}

							if ( _offset && _offset.top ) {
								root.animate( { scrollTop: _offset.top - 32 - 70 } );
							}
						};


					$( '.yith-wcmbs-legacy-set-access__notice__cta' ).on( 'click', function ( e ) {
						e.preventDefault();

						scrollTo( newMetaBox );
						$.ajax( {
									url     : ajaxurl,
									type    : 'POST',
									data    : {
										action  : 'yith_wcmbs_legacy_remove_set_access_meta_box',
										security: '<?php echo esc_html( wp_create_nonce( 'remove-legacy-set-access-meta-box' ) ); ?>'
									},
									complete: function () {
										scrollTo( newMetaBox, true );
										legacyMetaBox.slideUp();
									}
								} );
					} );
				} );
			</script>
			<?php
		}

		/**
		 * Remove the "Set access" metabox
		 */
		public function remove_set_access_meta_box() {
			check_ajax_referer( 'remove-legacy-set-access-meta-box', 'security' );

			if ( current_user_can( 'manage_options' ) ) {
				update_option( 'yith-wcmbs-legacy-show-set-access-metabox', 'no' );
			}
		}

	}
}
