<?php

/**
 * Initialize Visual Composer
 *
 * @since 5.5.0
 */

use VisualComposer\Helpers\Traits\WpFiltersActions;
use VisualComposer\Helpers\Traits\EventsFilters;

if ( ! class_exists( 'Porto_VC_Elements_Setup' ) ) :

	class Porto_VC_Elements_Setup extends VisualComposer\Framework\Container {
		use WpFiltersActions;
		use EventsFilters;

		private $elements = array(
			'row',
			'portoInfoBox',
			'portoHeading',
			'portoUltimateHeading',
			'portoBlock',
			'portoCounter',
			'portoSidebarMenu',
			'portoGoogleMap',
			'portoTimer',
			'portoBanner',
			'portoBannerLayer',
			'portoBlog',
			'portoRecentPost',
			'portoButton',
			'portoCarousel',
			'portoMasonry',
			'portoMasonryItem',
			'portoMember',
			'portoRecentMember',
			'portoPortfolios',
			'portoRecentPortfolios',
			'portoTestimonial',
			'portoPricingTable',
			'portoModal',
			'portoProductsWidget',
			'portoHotspot',
			'portoProgressBar',
			'portoTab',
			'portoTabContent',
			'portoShareIcon',
			'portoCircularBar',
			'portoPageHeader',
			'portoFancyText',
		);

		private $woo_elements = array(
			'portoProducts',
			'portoProductCategories',
			'portoProductsFilter',
		);

		private $porto_metas = array(
			'porto_default',
			'porto_container',
			'porto_layout',
			'porto_sidebar',
			'porto_sidebar2',
			'porto_header_type',
		);

		/**
		 * Register VC Elements
		 */
		public function __construct() {
			if ( ! defined( 'VCV_VERSION' ) ) {
				return;
			}

			add_action(
				'vcv:api',
				function( $api ) {
					/**
					 * @var \VisualComposer\Modules\Elements\ApiController $elementsApi
					*/
					$elements_api = $api->elements;

					foreach ( $this->elements as $tag ) {
						$manifest_path = PORTO_VC_ADDON_PATH . '/elements/' . $tag . '/manifest.json';
						$element_url   = PORTO_VC_ADDON_URL . '/elements/' . $tag;
						$elements_api->add( $manifest_path, $element_url );
					}

					if ( class_exists( 'Woocommerce' ) ) {
						foreach ( $this->woo_elements as $tag ) {
							$manifest_path = PORTO_VC_ADDON_PATH . '/elements/' . $tag . '/manifest.json';
							$element_url   = PORTO_VC_ADDON_URL . '/elements/' . $tag;
							$elements_api->add( $manifest_path, $element_url );
						}
					}
				},
				8
			);

			if ( is_admin() && ( wp_doing_ajax() || ( isset( $_REQUEST['vcv-action'] ) && 'frontend' == $_REQUEST['vcv-action'] ) ) ) {
				$this->addFilter( 'vcv:dataAjax:setData', 'set_porto_settings' );
				$this->addFilter( 'vcv:dataAjax:getData', 'get_porto_settings' );

				/* Popup Builder */

				if ( ! empty( $_REQUEST['post'] ) && 'porto_builder' == get_post_type( $_REQUEST['post'] ) && 'popup' == get_post_meta( $_REQUEST['post'], PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
					add_action(
						'vcv:api',
						function( $api ) {
							$elements_api  = $api->elements;
							$manifest_path = PORTO_VC_ADDON_PATH . '/elements/portoPopupContainer/manifest.json';
							$element_url   = PORTO_VC_ADDON_URL . '/elements/portoPopupContainer';
							$elements_api->add( $manifest_path, $element_url );
						},
						12
					);
					$this->addFilter(
						'vcv:editor:variables',
						function( $variables ) {
							$editorType = false;
							$key        = 'VCV_EDITOR_TYPE';
							foreach ( $variables as $i => $variable ) {
								if ( $variable['key'] === $key ) {
									$variables[ $i ] = [
										'key'   => 'VCV_EDITOR_TYPE',
										'value' => 'portoPopup',
										'type'  => 'constant',
									];
									$editorType      = true;
								}
							}

							if ( ! $editorType ) {
								$variables[] = [
									'key'   => $key,
									'value' => 'portoPopup',
									'type'  => 'constant',
								];
							}

							return $variables;
						}
					);
				}
			}

			if ( ! is_admin() && isset( $_GET['vcv-ajax'] ) ) {
				/* update editor ajax response */
				$this->addFilter( 'vcv:ajax:elements:ajaxShortcode:adminNonce', 'update_ajax_render', -1 );
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'load_elements_js' ), 1008 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_element_editor_js' ) );

			$this->wpAddFilter(
				'template_include',
				'set_temp_porto_settings',
				15
			);

			add_action( 'save_post', array( $this, 'post_refresh_carousel' ), 20, 2 );

			/* enqueue vc block styles */
			add_action( 'porto_enqueue_css', array( $this, 'enqueue_block_styles' ) );
			/*$this->addEvent(
				'vcv:assets:enqueueVendorAssets',
				'enqueue_block_styles'
			);*/
		}

		protected function set_temp_porto_settings( $t, VisualComposer\Helpers\Frontend $frontendHelper ) {
			if ( $frontendHelper->isPageEditable() ) {
				if ( isset( $_COOKIE['porto_layout_settings'] ) ) {
					$settings = json_decode( wp_unslash( $_COOKIE['porto_layout_settings'] ), true );
					add_filter(
						'porto_meta_use_default',
						function( $val ) {
							$settings = json_decode( wp_unslash( $_COOKIE['porto_layout_settings'] ), true );
							if ( ! empty( $settings['porto_default'] ) ) {
								return false;
							} elseif ( isset( $settings['porto_default'] ) ) {
								return true;
							}
							return true;
						}
					);

					if ( ! empty( $settings['porto_default'] ) ) {
						add_filter(
							'porto_meta_layout',
							function( $arr ) {
								$settings = json_decode( wp_unslash( $_COOKIE['porto_layout_settings'] ), true );
								if ( ! empty( $settings['porto_layout'] ) ) {
									$arr[0] = sanitize_text_field( $settings['porto_layout'] );
								}
								if ( ! empty( $settings['porto_sidebar'] ) ) {
									$arr[1] = sanitize_text_field( $settings['porto_sidebar'] );
								}
								if ( ! empty( $settings['porto_sidebar2'] ) ) {
									$arr[2] = sanitize_text_field( $settings['porto_sidebar2'] );
								}
								return $arr;
							}
						);
					}
				}
			}
			return $t;
		}

		protected function set_porto_settings( $response, $payload, VisualComposer\Helpers\Request $requestHelper, VisualComposer\Helpers\Frontend $frontendHelper ) {
			$post_id = $payload['sourceId'];
			if ( $frontendHelper->isPreview() ) {
				$preview = wp_get_post_autosave( $post_id );
				if ( is_object( $preview ) ) {
					$post_id = $preview->ID;
				} else {
					$post_id = false;
				}
			}
			$settings = $requestHelper->input( 'vcv-extra' );
			if ( ! empty( $settings ) && $post_id ) {
				foreach ( $this->porto_metas as $meta ) {
					if ( ! empty( $settings[ $meta ] ) ) {
						$val = porto_strip_script_tags( $settings[ $meta ] );
						if ( 'porto_default' == $meta ) {
							$val = 'default';
						}

						update_post_meta( $post_id, str_replace( 'porto_', '', $meta ), wp_slash( $val ) );
					} else {
						delete_post_meta( $post_id, str_replace( 'porto_', '', $meta ) );
					}
				}
				// Save popup options
				if ( 'porto_builder' == get_post_type( $post_id ) && 'popup' == get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
					if ( ! empty( $settings['popup_options'] ) ) {
						$val = porto_strip_script_tags( $settings['popup_options'] );
						update_post_meta( $post_id, 'popup_options', wp_slash( $val ) );
					} else {
						delete_post_meta( $post_id, 'popup_options' );
					}
				}
			}

			if ( $post_id && isset( $payload['post'] ) && isset( $payload['post']->post_content ) && preg_match_all( '/\[porto_block\s[^]]*(id|name)="([^"]*)"/', $payload['post']->post_content, $matches ) && ! empty( $matches[2] ) ) {
				$block_slugs = $this->get_block_ids_from_slug( $matches[2] );
				$used_blocks = get_theme_mod( '_used_blocks', array() );
				if ( ! isset( $used_blocks['vc'] ) ) {
					$used_blocks['vc'] = array();
				}
				if ( ! isset( $used_blocks['vc']['post_c'] ) ) {
					$used_blocks['vc']['post_c'] = array();
				}
				if ( ! empty( $block_slugs ) ) {
					$used_blocks['vc']['post_c'][ $post_id ] = array_map( 'intval', $block_slugs );
				} else {
					unset( $used_blocks['vc']['post_c'][ $post_id ] );
				}
				set_theme_mod( '_used_blocks', $used_blocks );
			}
			return $response;
		}

		protected function get_porto_settings( $response, $payload ) {
			global $post;

			if ( ! isset( $response['saveExtraArgs'] ) ) {
				$response['saveExtraArgs'] = array();
			}
			foreach ( $this->porto_metas as $meta ) {
				$val = get_post_meta( $post->ID, str_replace( 'porto_', '', $meta ), true );
				if ( 'porto_default' == $meta && 'default' == $val ) {
					$val = '1';
				}
				$response['saveExtraArgs'][ $meta ] = esc_js( $val );
			}
			return $response;
		}

		protected function update_ajax_render( VisualComposer\Helpers\Request $requestHelper ) {
			if ( $requestHelper->exists( 'vcv-shortcode-string' ) && $requestHelper->exists( 'vcv-is-porto-shortcode' ) ) {
				remove_all_actions( 'wp_head' );
				remove_all_actions( 'wp_footer' );
			}
		}

		public function load_element_editor_js() {
			if ( current_user_can( 'edit_posts' ) && isset( $_REQUEST['vcv-editable'] ) && ! empty( $_REQUEST['vcv-source-id'] ) ) {
				wp_enqueue_script( 'porto-vc-editor', PORTO_VC_ADDON_URL . '/assets/live_update.js', array(), PORTO_VC_ADDON_VERSION, true );
			}
		}

		public function load_elements_js() {
			if ( isset( $_REQUEST['vcv-action'] ) && 'frontend' == $_REQUEST['vcv-action'] ) {
				$masonry_layouts  = porto_sh_commons( 'masonry_layouts' );
				$creative_layouts = array();
				for ( $index = 1; $index <= count( $masonry_layouts ); $index++ ) {
					$layout = porto_creative_grid_layout( '' . $index );
					if ( is_array( $layout ) ) {
						$creative_layouts[ $index ] = array();
						foreach ( $layout as $pl ) {
							$creative_layouts[ $index ][] = esc_js( 'grid-col-' . $pl['width'] . ' grid-col-md-' . $pl['width_md'] . ( isset( $pl['width_lg'] ) ? ' grid-col-lg-' . $pl['width_lg'] : '' ) . ( isset( $pl['height'] ) ? ' grid-height-' . $pl['height'] : '' ) );
						}
					}
				}
				$image_sizes = array();
				foreach ( porto_sh_commons( 'image_sizes' ) as $value => $key ) {
					$image_sizes[] = array(
						'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
						'value' => esc_js( $key ),
					);
				}

				//wp_enqueue_script( 'wp-api-fetch' );

				wp_enqueue_script( 'porto-vc-editor', PORTO_VC_ADDON_URL . '/settings/public/dist/element.bundle.js', array( 'vcv:editors:frontend:script' ), PORTO_VC_ADDON_VERSION, true );

				global $porto_settings;

				if ( ! empty( $porto_settings ) ) {
					$vars = array(
						'ajax_url'          => esc_js( admin_url( 'admin-ajax.php' ) ),
						'nonce'             => wp_create_nonce( 'porto-nonce' ),
						'product_type'      => esc_js( $porto_settings['category-addlinks-pos'] ),
						'creative_layouts'  => $creative_layouts,
						'post_type'         => get_post_type(),

						'gmt_offset'        => get_option( 'gmt_offset' ),
						'grid_gutter_width' => intval( $porto_settings['grid-gutter-width'] ),
						'image_sizes'       => $image_sizes,
						'page_layouts'      => porto_ct_layouts(),
						'sidebars'          => porto_ct_sidebars(),
						'i18n'              => array(
							'porto_settings'     => esc_html__( 'Porto Settings', 'porto-vc-addon' ),
							'porto_default'      => esc_html__( 'Layout & Sidebar', 'porto-vc-addon' ),
							'apply_changes'      => esc_html__( 'Apply Changes', 'porto-vc-addon' ),
							'porto_default_desc' => esc_html__( 'Use selected layout and sidebar options.', 'porto-vc-addon' ),
							'container'          => esc_html__( 'Wrap as Container', 'porto-vc-addon' ),
							'layout'             => esc_html__( 'Layout', 'porto-vc-addon' ),
							'sidebar'            => esc_html__( 'Sidebar', 'porto-vc-addon' ),
							'sidebar_desc'       => esc_html__( 'You can create the sidebar under Appearance - Sidebars.', 'porto-vc-addon' ),
						),
					);
					if ( 'porto_builder' == get_post_type() ) {
						$terms = wp_get_post_terms( get_the_ID(), 'porto_builder_type', array( 'fields' => 'names' ) );
						if ( ! empty( $terms ) ) {
							$vars['builder_type']        = esc_js( $terms[0] );
							$vars['i18n']['header_type'] = esc_html__( 'Header Type', 'porto-vc-addon' );
						}
						if ( class_exists( 'Woocommerce' ) ) {
							$vars['myaccount_url'] = esc_url( wc_get_page_permalink( 'myaccount' ) );
						}
					}
					wp_localize_script(
						'porto-admin',
						'porto_vc_vars',
						$vars
					);
				}
			}
		}

		public function enqueue_block_styles() {
			if ( ! function_exists( 'porto_check_using_page_builder_block' ) ) {
				return;
			}
			$vc_blocks = porto_check_using_page_builder_block( 'vc' );
			if ( ! empty( $vc_blocks ) ) {
				foreach ( $vc_blocks as $post_id ) {
					vchelper( 'AssetsEnqueue' )->enqueueAssets( $post_id );
					$bundle_url = get_post_meta( $post_id, 'vcvSourceCssFileUrl', true );
					if ( $bundle_url ) {
						$version = get_post_meta( $post_id, '_' . VCV_PREFIX . 'sourceChecksum', true );

						if ( 0 !== strpos( $bundle_url, 'http' ) ) {
							if ( false === strpos( $bundle_url, 'assets-bundles' ) ) {
								$bundle_url = '/assets-bundles/' . $bundle_url;
							}
						}

						$handle = 'vcv:assets:source:main:styles:' . vchelper( 'Str' )->slugify( $bundle_url );
						wp_enqueue_style(
							$handle,
							vchelper( 'Assets' )->getAssetUrl( $bundle_url ),
							array(),
							VCV_VERSION . '.' . $version . '-' . $post_id
						);
					}
				}
			}
		}

		private function get_block_ids_from_slug( $porto_blocks ) {
			if ( empty( $porto_blocks ) ) {
				return array();
			}
			$result = array();
			global $wpdb;
			foreach ( $porto_blocks as $s ) {
				$where   = is_numeric( $s ) ? 'ID' : 'post_name';
				$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'porto_builder' AND $where = %s", sanitize_text_field( $s ) ) );
				if ( $post_id && 'fe' == get_post_meta( $post_id, 'vcv-be-editor', true ) && get_post_meta( $post_id, 'vcvSourceCssFileUrl', true ) ) {
					$result[] = (int) $post_id;
				}
			}
			return array_unique( $result );
		}

		public function post_refresh_carousel( $post_id, $post ) {
			// filter content to remove auto added carousel divs
			if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'vcv-admin-ajax' == $_REQUEST['action'] && isset( $_REQUEST['vcv-admin-ajax'] ) && isset( $_REQUEST['vcv-zip'] ) ) {
				if ( false !== $post && $post->post_content && 'inherit' != $post->post_status && ! defined( 'PORTO_VCV_POST_FILTERED' ) && ( preg_match( '/<div class="owl-carousel[^>]*><div class="owl-stage-outer">/', $post->post_content ) || false !== strpos( $post->post_content, 'porto-vc-progressbar' ) ) ) {
					ob_start();
					try {
						$no_format = false;
						if ( 0 === strpos( $post->post_content, '<!--vcv no format-->' ) ) {
							$no_format = true;
						}
						$dom = new DOMDocument;
						$dom->loadHTML( $post->post_content );
						$dom          = $this->refresh_carousel( $dom );
						$content_node = $dom->getElementsByTagName( 'body' );
						if ( count( $content_node ) ) {
							$post_content = str_replace( array( '<body>', '</body>' ), '', $dom->saveHTML( $content_node[0] ) );
						} else {
							$post_content = $dom->saveHTML();
						}
						$post->post_content = rawurldecode( utf8_decode( $post_content ) );
						if ( $no_format && 0 !== strpos( $post->post_content, '<!--vcv no format-->' ) ) {
							$post->post_content = '<!--vcv no format-->' . $post->post_content;
						}
						$post->post_content = str_replace( 'porto-carousel manual has-ccols', 'porto-carousel has-ccols', $post->post_content );
						define( 'PORTO_VCV_POST_FILTERED', true );
						$result = wp_update_post( $post, true );
					} catch ( Exception $e ) {
					}
					ob_end_clean();
				}
			}
		}

		private function refresh_carousel( $dom ) {
			if ( ! $dom->childNodes ) {
				return;
			}
			foreach ( $dom->childNodes as $e ) {
				if ( method_exists( $e, 'getAttribute' ) ) {
					$cls = $e->getAttribute( 'class' );
				} else {
					$cls = '';
				}

				if ( $e->hasChildNodes() ) {
					if ( 'owl-stage-outer' == $cls ) {
						if ( $e->nextSibling ) {
							if ( $e->nextSibling->nextSibling ) {
								$e->parentNode->removeChild( $e->nextSibling->nextSibling );
							}
							$e->parentNode->removeChild( $e->nextSibling );
						}
						$items = $e->childNodes[0]->childNodes;
						foreach ( $items as $node ) {
							if ( false !== strpos( $node->getAttribute( 'class' ), 'owl-item' ) ) {
								$e->parentNode->appendChild( $node->childNodes[0] );
							} else {
								$e->parentNode->appendChild( $node );
							}
						}
						$e->parentNode->removeChild( $e );
					} elseif ( false !== strpos( $cls, 'owl-loaded' ) && false !== strpos( $cls, 'owl-carousel' ) ) {
						$e->setAttribute( 'class', str_replace( ' owl-loaded', '', $cls ) );
					}
					$this->refresh_carousel( $e );
				}
				if ( false !== strpos( $cls, 'progress-bar' ) ) {
					$e->removeAttribute( 'style' );
				}
			}
			return $dom;
		}
	}
endif;

new Porto_VC_Elements_Setup;
