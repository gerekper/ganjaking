<?php

// delete these transients/options for debugging
// set_site_transient( 'update_plugins', null );
// set_site_transient( 'update_themes', null );
// delete_option( 'brainstrom_products' );

/**
 *
 */
if ( ! class_exists( 'BSF_Update_Manager' ) ) {

	class BSF_Update_Manager {


		public function __construct() {
			// update data to WordPress's transient
			add_filter(
				'pre_set_site_transient_update_plugins',
				array(
					$this,
					'brainstorm_update_plugins_transient',
				)
			);
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'brainstorm_update_themes_transient' ) );

			// display changelog in update details
			add_filter( 'plugins_api', array( $this, 'bsf_get_plugin_information' ), 10, 3 );

			// display correct error messages
			add_action( 'load-plugins.php', array( $this, 'bsf_update_display_license_link' ) );
			add_filter( 'upgrader_pre_download', array( $this, 'bsf_change_no_package_message' ), 20, 3 );
		}

		public function brainstorm_update_plugins_transient( $_transient_data ) {

			global $pagenow;

			if ( ! is_object( $_transient_data ) ) {
				$_transient_data = new stdClass();
			}

			$update_data = $this->bsf_update_transient_data( 'plugins' );

			foreach ( $update_data as $key => $product ) {

				if ( isset( $product['template'] ) && $product['template'] != '' ) {
					$template = $product['template'];
				} elseif ( isset( $product['init'] ) && $product['init'] != '' ) {
					$template = $product['init'];
				}

				if ( isset( $_transient_data->response[ $template ] ) ) {
					continue;
				}

				if ( false === $this->enable_auto_updates( $product['id'] ) ) {
					continue;
				}

				$plugin                 = new stdClass();
				$plugin->id             = isset( $product['id'] ) ? $product['id'] : '';
				$plugin->slug           = $this->bsf_get_plugin_slug( $template );
				$plugin->plugin         = isset( $template ) ? $template : '';
				$plugin->upgrade_notice = '';

				if ( $this->use_beta_version( $plugin->id ) ) {
					$plugin->new_version     = isset( $product['version_beta'] ) ? $product['version_beta'] : '';
					$plugin->upgrade_notice .= 'It is recommended to use the beta version on a staging enviornment only.';
				} else {
					$plugin->new_version = isset( $product['remote'] ) ? $product['remote'] : '';
				}

				$plugin->url = isset( $product['purchase_url'] ) ? $product['purchase_url'] : '';

				if ( BSF_License_Manager::bsf_is_active_license( $product['id'] ) == 'registered' ) {
					$plugin->package = $this->bsf_get_package_uri( $product['id'] );
				} else {
					$plugin->package = '';
					$bundled         = self::bsf_is_product_bundled( $plugin->id );
					if ( ! empty( $bundled ) ) {
						$parent_id              = $bundled[0];
						$parent_name            = brainstrom_product_name( $parent_id );
						$plugin->upgrade_notice = 'This plugin is came bundled with the ' . $parent_name . '. For receiving updates, you need to register license of ' . $parent_name . '.';
					} else {
						$plugin->upgrade_notice .= ' Please activate your license to receive automatic updates.';
					}
				}

				$plugin->tested = isset( $product['tested'] ) ? $product['tested'] : '';
				$plugin->requires_php = isset( $product['php_version'] ) ? $product['php_version'] : '';

				$plugin->icons = apply_filters( "bsf_product_icons_{$product['id']}", array(
					'1x'      => ( isset( $product['product_image'] ) ) ? $product['product_image'] : '',
					'2x'      => ( isset( $product['product_image'] ) ) ? $product['product_image'] : '',
					'default' => ( isset( $product['product_image'] ) ) ? $product['product_image'] : '',
				) );

				$_transient_data->last_checked          = time();
				$_transient_data->response[ $template ] = $plugin;
			}

			return $_transient_data;
		}

		public function brainstorm_update_themes_transient( $_transient_data ) {

			global $pagenow;

			if ( ! is_object( $_transient_data ) ) {
				$_transient_data = new stdClass();
			}

			if ( 'themes.php' != $pagenow && 'update-core.php' !== $pagenow ) {
				return $_transient_data;
			}

			$update_data = $this->bsf_update_transient_data( 'themes' );

			foreach ( $update_data as $key => $product ) {

				if ( false === $this->enable_auto_updates( $product['id'] ) ) {
					continue;
				}

				if ( isset( $product['template'] ) && $product['template'] != '' ) {
					$template = $product['template'];
				}

				$themes          = array();
				$themes['theme'] = isset( $template ) ? $template : '';

				if ( $this->use_beta_version( $product['id'] ) ) {
					$themes['new_version'] = isset( $product['version_beta'] ) ? $product['version_beta'] : '';
				} else {
					$themes['new_version'] = isset( $product['remote'] ) ? $product['remote'] : '';
				}

				$themes['url'] = isset( $product['purchase_url'] ) ? $product['purchase_url'] : '';
				if ( BSF_License_Manager::bsf_is_active_license( $product['id'] ) == 'registered' ) {
					$themes['package'] = $this->bsf_get_package_uri( $product['id'] );
				} else {
					$themes['package']        = '';
					$themes['upgrade_notice'] = 'Please activate your license to receive automatic updates.';
				}
				$_transient_data->last_checked          = time();
				$_transient_data->response[ $template ] = $themes;
			}

			return $_transient_data;
		}

		/**
		 * Allow autoupdates to be enabled/disabled per product basis.
		 *
		 * @param String $product_id - Product ID.
		 * @return boolean True - IF updates are to be enabled. False if updates are to be disabled.
		 */
		private function enable_auto_updates( $product_id ) {
			return apply_filters( "bsf_enable_product_autoupdates_{$product_id}", true );
		}

		/**
		 * Updates information on the "View version x.x details" page with custom data.
		 *
		 * @uses api_request()
		 *
		 * @param mixed  $_data
		 * @param string $_action
		 * @param object $_args
		 *
		 * @return object $_data
		 */
		public function bsf_get_plugin_information( $_data, $_action = '', $_args = null ) {

			if ( $_action != 'plugin_information' ) {

				return $_data;

			}

			$brainstrom_products = apply_filters( 'bsf_get_plugin_information', get_option( 'brainstrom_products', array() ) );

			$plugins      = isset( $brainstrom_products['plugins'] ) ? $brainstrom_products['plugins'] : array();
			$themes       = isset( $brainstrom_products['themes'] ) ? $brainstrom_products['themes'] : array();
			$all_products = $plugins + $themes;

			foreach ( $all_products as $key => $product ) {

				$product_slug = isset( $product['slug'] ) ? $product['slug'] : '';

				if ( $product_slug == $_args->slug ) {

					$id = isset( $product['id'] ) ? $product['id'] : '';

					$info = new stdClass();

					if ( $this->use_beta_version( $id ) ) {
						$info->new_version = isset( $product['version_beta'] ) ? $product['version_beta'] : '';
					} else {
						$info->new_version = isset( $product['remote'] ) ? $product['remote'] : '';
					}

					$product_name   = isset( $product['name'] ) ? $product['name'] : '';
					$info->name     = apply_filters( "bsf_product_name_{$id}", $product_name );
					$info->slug     = $product_slug;
					$info->version  = isset( $product['remote'] ) ? $product['remote'] : '';
					$info->author   = apply_filters( "bsf_product_author_{$id}", 'Brainstorm Force' );
					$info->url      = isset( $product['changelog_url'] ) ? apply_filters( "bsf_product_url_{$id}", $product['changelog_url'] ) : apply_filters( "bsf_product_url_{$id}", '' );
					$info->homepage = isset( $product['purchase_url'] ) ? apply_filters( "bsf_product_homepage_{$id}", $product['purchase_url'] ) : apply_filters( "bsf_product_homepage_{$id}", '' );

					if ( BSF_License_Manager::bsf_is_active_license( $id ) == true ) {
						$package_url         = $this->bsf_get_package_uri( $id );
						$info->package       = $package_url;
						$info->download_link = $package_url;
					}

					$info->sections                = array();
					$product_decription            = isset( $product['description'] ) ? $product['description'] : '';
					$info->sections['description'] = apply_filters( "bsf_product_description_{$id}", $product_decription );
					$product_changelog             = 'Thank you for using ' . $info->name . '. </br></br>To make your experience using ' . $info->name . ' better we release updates regularly, you can view the full changelog <a href="' . $info->url . '">here</a>';
					$info->sections['changelog']   = apply_filters( "bsf_product_changelog_{$id}", $product_changelog );

					$_data = $info;
				}
			}

			return $_data;
		}

		// helpers
		public static function bsf_is_product_bundled( $bsf_product, $search_by = 'id' ) {
			$brainstrom_bundled_products = get_option( 'brainstrom_bundled_products', array() );
			$product_parent              = array();

			foreach ( $brainstrom_bundled_products as $parent => $products ) {

				foreach ( $products as $key => $product ) {

					if ( $search_by == 'init' ) {

						if ( $product->init == $bsf_product ) {
							$product_parent[] = $parent;
						}
					} elseif ( $search_by == 'id' ) {

						if ( $product->id == $bsf_product ) {
							$product_parent[] = $parent;
						}
					} elseif ( $search_by == 'name' ) {

						if ( strcasecmp( $product->name, $bsf_product ) == 0 ) {
							$product_parent[] = $parent;
						}
					}
				}
			}

			$product_parent = apply_filters( 'bsf_is_product_bundled', array_unique( $product_parent ), $bsf_product, $search_by );

			return $product_parent;
		}

		public function bsf_get_package_uri( $product_id ) {

			$product = get_brainstorm_product( $product_id );
			$status  = BSF_License_Manager::bsf_is_active_license( $product_id );

			if ( $this->use_beta_version( $product_id ) ) {
				$download_file = isset( $product['download_url_beta'] ) ? $product['download_url_beta'] : '';
			} else {
				$download_file = isset( $product['download_url'] ) ? $product['download_url'] : '';
			}

			if ( $download_file !== '' ) {

				if ( $status == false ) {
					return '';
				}

				$timezone = date_default_timezone_get();
				$hash     = 'file=' . $download_file . '&hashtime=' . strtotime( date( 'd-m-Y h:i:s a' ) ) . '&timezone=' . $timezone;

				$get_path      = 'http://downloads.brainstormforce.com/';
				$download_path = rtrim( $get_path, '/' ) . '/download.php?' . $hash . '&base=ignore';

				return $download_path;
			}
		}

		public function bsf_update_transient_data( $product_type ) {

			$this->_maybe_force_check_bsf_product_updates();

			$all_products    = array();
			$update_required = array();

			if ( $product_type == 'plugins' ) {
				$all_products = $this->prepare_plugins_for_update( brainstorm_get_all_products( false, true, false ) );
			}

			if ( $product_type == 'themes' ) {
				$all_products = brainstorm_get_all_products( true, false, true );
			}

			foreach ( $all_products as $key => $product ) {

				$product_id = isset( $product['id'] ) ? $product['id'] : '';

				$constant = strtoupper( str_replace( '-', '_', $product_id ) );
				$constant = 'BSF_' . $constant . '_CHECK_UPDATES';

				if ( defined( $constant ) && ( constant( $constant ) === 'false' || constant( $constant ) === false ) ) {
					continue;
				}

				$remote       = isset( $product['remote'] ) ? $product['remote'] : '';
				$local        = isset( $product['version'] ) ? $product['version'] : '';
				$version_beta = isset( $product['version_beta'] ) ? $product['version_beta'] : $remote;

				if ( $this->use_beta_version( $product_id ) ) {
					$remote = $version_beta;
				}

				if ( version_compare( $remote, $local, '>' ) ) {
					array_push( $update_required, $product );
				}
			}

			return $update_required;
		}

		/**
		 * Remove plugins from the updates array which are not installed.
		 *
		 * @param Array $plugins
		 * @return Array of plugins.
		 */
		public static function prepare_plugins_for_update( $plugins ) {
			foreach ( $plugins as $key => $plugin ) {
				if ( isset( $plugin[ 'template' ] ) && ! file_exists( dirname( realpath( WP_PLUGIN_DIR . '/' . $plugin[ 'template' ] ) ) ) ) {
					unset( $plugins[ $key ] );
				}
				if ( isset( $plugin[ 'init' ] ) && ! file_exists( dirname( realpath( WP_PLUGIN_DIR . '/' . $plugin[ 'init' ] ) ) ) ) {
					unset( $plugins[ $key ] );
				}
			}

			return $plugins;
		}

		public function _maybe_force_check_bsf_product_updates() {
			if ( true === bsf_time_since_last_versioncheck( 2, 'bsf_local_transient' ) ) {
				global $ultimate_referer;
				$ultimate_referer = 'on-transient-delete-2-hours';
				bsf_check_product_update();
				update_option( 'bsf_local_transient', (string) current_time( 'timestamp' ) );
				set_transient( 'bsf_check_product_updates', true, 2 * DAY_IN_SECONDS );
			}

		}

		public function use_beta_version( $product_id ) {

			$product = get_brainstorm_product( $product_id );
			$stable  = isset( $product['remote'] ) ? $product['remote'] : '';
			$beta    = isset( $product['version_beta'] ) ? $product['version_beta'] : '';

			// If beta version is not set, return
			if ( $beta == '' ) {
				return false;
			}

			if ( version_compare( $stable, $beta, '<' ) &&
				self::bsf_allow_beta_updates( $product_id ) ) {

				return true;
			}

			return false;
		}

		public function beta_version_normalized( $beta ) {
			$beta_explode = explode( '-', $beta );

			$version = $beta_explode[0] . '.' . str_replace( 'beta', '', $beta_explode[1] );

			return $version;
		}

		public static function bsf_allow_beta_updates( $product_id ) {
			return apply_filters( "bsf_allow_beta_updates_{$product_id}", false );
		}

		public function bsf_get_plugin_slug( $template ) {
			$slug = explode( '/', $template );

			if ( isset( $slug[0] ) ) {
				return $slug[0];
			}

			return '';
		}

		public function bsf_update_display_license_link() {
			$brainstorm_all_products = $this->brainstorm_all_products();

			foreach ( $brainstorm_all_products as $key => $product ) {

				if ( isset( $product['id'] ) ) {
					$id = $product['id'];

					if ( isset( $product['template'] ) && $product['template'] != '' ) {
						$template = $product['template'];
					} elseif ( isset( $product['init'] ) && $product['init'] != '' ) {
						$template = $product['init'];
					}

					if ( BSF_License_Manager::bsf_is_active_license( $id ) == false ) {

						if ( is_plugin_active( $template ) ) {
							add_action(
								"in_plugin_update_message-$template",
								array(
									$this,
									'bsf_add_registration_message',
								),
								9,
								2
							);
						}
					} else {
						add_action(
							"in_plugin_update_message-$template",
							array(
								$this,
								'add_beta_update_message',
							),
							9,
							2
						);
					}
				}
			}

		}

		public function brainstorm_all_products() {
			$brainstrom_products         = get_option( 'brainstrom_products', array() );
			$brainstrom_products_plugins = isset( $brainstrom_products['plugins'] ) ? $brainstrom_products['plugins'] : array();
			$brainstrom_products_themes  = isset( $brainstrom_products['themes'] ) ? $brainstrom_products['themes'] : array();
			$brainstrom_bundled_products = get_option( 'brainstrom_bundled_products', array() );

			$bundled = array();

			foreach ( $brainstrom_bundled_products as $parent => $children ) {

				foreach ( $children as $key => $product ) {
					$bundled[ $product->id ] = (array) $product;
				}
			}

			// array of all the products
			$all_products = $brainstrom_products_plugins + $brainstrom_products_themes + $bundled;

			return $all_products;
		}

		public function bsf_add_registration_message( $plugin_data, $response ) {

			$plugin_init = isset( $plugin_data['plugin'] ) ? $plugin_data['plugin'] : '';

			if ( '' !== $plugin_init ) {
				$product_id        = brainstrom_product_id_by_init( $plugin_init );
				$bundled           = self::bsf_is_product_bundled( $plugin_init, 'init' );
				$registration_page = bsf_registration_page_url( '', $product_id );
			} else {
				$plugin_name       = isset( $plugin_data['name'] ) ? $plugin_data['name'] : '';
				$product_id        = brainstrom_product_id_by_name( $plugin_name );
				$bundled           = self::bsf_is_product_bundled( $plugin_name, 'name' );
				$registration_page = bsf_registration_page_url( '', $product_id );
			}

			if ( ! empty( $bundled ) ) {
				$parent_id         = $bundled[0];
				$registration_page = bsf_registration_page_url( '', $parent_id );
				$parent_name       = apply_filters( "bsf_product_name_{$parent_id}", brainstrom_product_name( $parent_id ) );
				$message           = sprintf( __( ' <br>This plugin is came bundled with the <i>%1$s</i>. For receiving updates, you need to register license of <i>%2$s</i> <a href="%3$s">here</a>.' ), $parent_name, $parent_name, $registration_page );
			} else {
				$message = sprintf( __( ' <i>Please <a href="%1$s">activate your license</a> to update the plugin.</i>' ), $registration_page );
			}

			if ( true == self::bsf_allow_beta_updates( $product_id ) && $this->is_beta_version( $plugin_data['new_version'] ) ) {
				$message = $message . ' <i>It is recommended to use the beta version on a staging enviornment only.</i>';
			}

			echo $message;

		}

		public function add_beta_update_message( $plugin_data, $response ) {
			$plugin_init = isset( $plugin_data['plugin'] ) ? $plugin_data['plugin'] : '';

			if ( '' !== $plugin_init ) {
				$product_id = brainstrom_product_id_by_init( $plugin_init );
			} else {
				$product_id = brainstrom_product_id_by_name( $plugin_name );
			}

			if ( true == self::bsf_allow_beta_updates( $product_id ) && $this->is_beta_version( $plugin_data['new_version'] ) ) {
				echo ' <i>It is recommended to use the beta version on a staging enviornment only.</i>';
			}
		}

		private function is_beta_version( $version ) {
			return strpos( $version, 'beta' ) ||
				strpos( $version, 'alpha' );
		}

		public function bsf_change_no_package_message( $reply, $package, $current ) {

			// Read atts into separate veriables so that easy to reference below.
			$strings = $current->strings;

			if ( isset( $current->skin->plugin_info ) ) {
				$plugin_info = $current->skin->plugin_info;

				$plugin_name           = $plugin_info['Name'];
				$product_id            = brainstrom_product_id_by_name( $plugin_name );
				$plugin_name           = apply_filters( "bsf_product_name_{$product_id}", $plugin_name );
				$is_bundled            = self::bsf_is_product_bundled( $plugin_name, 'name' );
				$registration_page_url = bsf_registration_page_url( '', $product_id );

				if ( empty( $is_bundled ) ) {
					if ( strcasecmp( $plugin_info['Author'], 'Brainstorm Force' ) !== 0 ) {

						// This is not our product, let's leave.
						return $reply;
					}
				} else {
					$is_bundled            = isset( $is_bundled[0] ) ? $is_bundled[0] : $plugin_name;
					$plugin_name           = apply_filters( "bsf_product_name_{$is_bundled}", brainstrom_product_name( $is_bundled ) );
					$registration_page_url = bsf_registration_page_url( '', $is_bundled );
				}

				$strings['downloading_package'] = 'Downloading the package...';

				if ( $plugin_info['Author'] == 'Brainstorm Force' ) {

					$plugin_init = BSF_License_Manager::instance()->bsf_get_product_info( $product_id, 'template' );

					if ( is_plugin_active( $plugin_init ) ) {
						$strings['no_package'] = sprintf(
							__( 'Click <a target="_blank" href="%1$1s">here</a> to activate license of <i>%2$2s</i> to receive automatic updates.' ),
							$registration_page_url,
							$plugin_name
						);
					} else {
						$strings['no_package'] = sprintf(
							__( 'Activate license of <i>%2s</i> to receive automatic updates.' ),
							$plugin_name
						);
					}
				} elseif ( $is_bundled !== '' ) {
					$strings['no_package'] = sprintf(
						__( 'This plugin is came bundled with the <i>%1$1s</i>. For receiving updates, you need to register license of <i>%2$2s</i> <a target="_blank" href="%3$3s">here</a>.' ),
						$plugin_name,
						$plugin_name,
						$registration_page_url
					);
				}
			} elseif ( isset( $current->skin->theme_info ) ) {
				$theme_info   = $current->skin->theme_info;
				$theme_author = $theme_info->get( 'Author' );
				$theme_name   = $theme_info->get( 'Name' );
				$product_id   = brainstrom_product_id_by_name( $theme_name );

				if ( $theme_author == 'Brainstorm Force' ) {
					$strings['downloading_package'] = 'Downloading the package...';
					$strings['no_package']          = sprintf(
						__( 'Click <a target="_blank" href="%1$1s">here</a> to activate license of <i>%2$2s</i> to receive automatic updates.' ),
						bsf_registration_page_url( '', $product_id ),
						$theme_name
					);
				}
			}

			// restore the strings back to WP_Upgrader
			$current->strings = $strings;

			// We are not changing teh return parameter.
			return $reply;
		}

	} // class BSF_Update_Manager

	new BSF_Update_Manager();
}
