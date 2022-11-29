<?php
/**
 *  BSF Analytics Stats
 *
 * @package BSF_Core
 */

/**
 * Delete these transients/options for debugging
 * set_site_transient( 'update_plugins', null );
 * set_site_transient( 'update_themes', null );
 * delete_option( 'brainstrom_products' );
 */

if ( ! class_exists( 'BSF_Update_Manager' ) ) {

	/**
	 * Update Manager Class
	 *
	 * @class BSF_Update_Manager
	 */
	class BSF_Update_Manager {

		/**
		 * Constructor function that initializes required sections
		 */
		public function __construct() {
			// update data to WordPress's transient.
			add_filter(
				'pre_set_site_transient_update_plugins',
				array(
					$this,
					'brainstorm_update_plugins_transient',
				)
			);
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'brainstorm_update_themes_transient' ) );

			// display changelog in update details.
			add_filter( 'plugins_api', array( $this, 'bsf_get_plugin_information' ), 10, 3 );

			// display correct error messages.
			add_action( 'load-plugins.php', array( $this, 'bsf_update_display_license_link' ) );

			add_filter( 'upgrader_pre_download', array( $this, 'modify_download_package_message' ), 20, 3 );

			add_action( 'bsf_get_plugin_information', array( $this, 'plugin_information' ) );
		}

		/**
		 * Function to update plugin's transient.
		 *
		 * @param obj $_transient_data Transient Data.
		 * @return $_transient_data.
		 */
		public function brainstorm_update_plugins_transient( $_transient_data ) {

			global $pagenow;

			if ( ! is_object( $_transient_data ) ) {
				$_transient_data = new stdClass();
			}

			$update_data = $this->bsf_update_transient_data( 'plugins' );

			foreach ( $update_data as $key => $product ) {

				if ( isset( $product['template'] ) && '' !== $product['template'] ) {
					$template = $product['template'];
				} elseif ( isset( $product['init'] ) && '' !== $product['init'] ) {
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

				if ( BSF_License_Manager::bsf_is_active_license( $product['id'] ) === true ) {
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

				$plugin->tested       = isset( $product['tested'] ) ? $product['tested'] : '';
				$plugin->requires_php = isset( $product['php_version'] ) ? $product['php_version'] : '';

				$plugin->icons = apply_filters(
					"bsf_product_icons_{$product['id']}",
					array(
						'1x'      => ( isset( $product['product_image'] ) ) ? $product['product_image'] : '',
						'2x'      => ( isset( $product['product_image'] ) ) ? $product['product_image'] : '',
						'default' => ( isset( $product['product_image'] ) ) ? $product['product_image'] : '',
					)
				);

				$_transient_data->last_checked          = time();
				$_transient_data->response[ $template ] = $plugin;
			}

			return $_transient_data;
		}

		/**
		 * Function to update theme's transient.
		 *
		 * @param obj $_transient_data Transient Data.
		 * @return $_transient_data.
		 */
		public function brainstorm_update_themes_transient( $_transient_data ) {

			global $pagenow;

			if ( ! is_object( $_transient_data ) ) {
				$_transient_data = new stdClass();
			}

			if ( 'themes.php' !== $pagenow && 'update-core.php' !== $pagenow ) {
				return $_transient_data;
			}

			$update_data = $this->bsf_update_transient_data( 'themes' );

			foreach ( $update_data as $key => $product ) {

				if ( false === $this->enable_auto_updates( $product['id'] ) ) {
					continue;
				}

				if ( isset( $product['template'] ) && '' !== $product['template'] ) {
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
				if ( BSF_License_Manager::bsf_is_active_license( $product['id'] ) === true ) {
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
		 *
		 * Updates information on the "View version x.x details" page with custom data.
		 *
		 * @uses api_request()
		 *
		 * @param mixed  $_data Data.
		 * @param string $_action Action.
		 * @param object $_args Arguments.
		 *
		 * @return object $_data
		 */
		public function bsf_get_plugin_information( $_data, $_action = '', $_args = null ) {

			if ( 'plugin_information' !== $_action ) {

				return $_data;

			}

			$brainstrom_products = apply_filters( 'bsf_get_plugin_information', get_option( 'brainstrom_products', array() ) );

			$plugins      = isset( $brainstrom_products['plugins'] ) ? $brainstrom_products['plugins'] : array();
			$themes       = isset( $brainstrom_products['themes'] ) ? $brainstrom_products['themes'] : array();
			$all_products = $plugins + $themes;

			foreach ( $all_products as $key => $product ) {

				$product_slug = isset( $product['slug'] ) ? $product['slug'] : '';

				if ( $product_slug === $_args->slug ) {

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

					if ( BSF_License_Manager::bsf_is_active_license( $id ) === true ) {
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

		/**
		 * Check if product is bundled.
		 *
		 * @param array  $bsf_product Product.
		 * @param string $search_by Search By.
		 * @return $product_parent.
		 */
		public static function bsf_is_product_bundled( $bsf_product, $search_by = 'id' ) {
			$brainstrom_bundled_products = get_option( 'brainstrom_bundled_products', array() );
			$product_parent              = array();

			foreach ( $brainstrom_bundled_products as $parent => $products ) {

				foreach ( $products as $key => $product ) {

					if ( 'init' === $search_by ) {

						if ( $product->init === $bsf_product ) {
							$product_parent[] = $parent;
						}
					} elseif ( 'id' === $search_by ) {

						if ( $product->id === $bsf_product ) {
							$product_parent[] = $parent;
						}
					} elseif ( 'name' === $search_by ) {

						if ( strcasecmp( $product->name, $bsf_product ) === 0 ) {
							$product_parent[] = $parent;
						}
					}
				}
			}

			$product_parent = apply_filters( 'bsf_is_product_bundled', array_unique( $product_parent ), $bsf_product, $search_by );

			return $product_parent;
		}
		/**
		 * Get package URL
		 *
		 * @param int $product_id Product Id.
		 * @return string $download_path.
		 */
		public function bsf_get_package_uri( $product_id ) {

			$product       = get_brainstorm_product( $product_id );
			$status        = BSF_License_Manager::bsf_is_active_license( $product_id );
			$download_path = '';

			if ( $this->use_beta_version( $product_id ) ) {
				$download_file = isset( $product['download_url_beta'] ) ? $product['download_url_beta'] : '';
			} else {
				$download_file = isset( $product['download_url'] ) ? $product['download_url'] : '';
			}

			if ( '' !== $download_file ) {

				if ( false === $status ) {
					return '';
				}

				$timezone      = date_default_timezone_get();
				$hashtime_date = new DateTime( 'now', new DateTimeZone( $timezone ) );
				$hash          = 'file=' . $download_file . '&hashtime=' . strtotime( $hashtime_date->format( 'd-m-Y h:i:s a' ) ) . '&timezone=' . $timezone;

				$get_path      = 'https://downloads.brainstormforce.com/';
				$download_path = rtrim( $get_path, '/' ) . '/download.php?' . $hash . '&base=ignore';

				return $download_path;
			}

			return $download_path;
		}
		/**
		 *  Update transient Data.
		 *
		 *  @param string $product_type Product Type.
		 *  @return $update_required.
		 */
		public function bsf_update_transient_data( $product_type ) {

			$this->maybe_force_check_bsf_product_updates();

			$all_products    = array();
			$update_required = array();

			if ( 'plugins' === $product_type ) {
				$all_products = $this->prepare_plugins_for_update( brainstorm_get_all_products( false, true, false ) );
			}

			if ( 'themes' === $product_type ) {
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
		 * @param Array $plugins Plugins.
		 * @return Array of plugins.
		 */
		public static function prepare_plugins_for_update( $plugins ) {
			foreach ( $plugins as $key => $plugin ) {
				if ( isset( $plugin['template'] ) && ! file_exists( dirname( realpath( WP_PLUGIN_DIR . '/' . $plugin['template'] ) ) ) ) {
					unset( $plugins[ $key ] );
				}
				if ( isset( $plugin['init'] ) && ! file_exists( dirname( realpath( WP_PLUGIN_DIR . '/' . $plugin['init'] ) ) ) ) {
					unset( $plugins[ $key ] );
				}
			}

			return $plugins;
		}
		/**
		 * Force check BSF Product updates.
		 */
		public function maybe_force_check_bsf_product_updates() {
			if ( true === bsf_time_since_last_versioncheck( 2, 'bsf_last_update_check' ) ) {
				global $ultimate_referer;
				$ultimate_referer = 'on-transient-delete-2-hours';
				bsf_check_product_update();
				update_option( 'bsf_last_update_check', (string) current_time( 'timestamp' ) );
			}
		}

		/**
		 * Use Beta version.
		 *
		 * @param int $product_id Product ID.
		 * @return bool.
		 */
		public function use_beta_version( $product_id ) {

			$product = get_brainstorm_product( $product_id );
			$stable  = isset( $product['remote'] ) ? $product['remote'] : '';
			$beta    = isset( $product['version_beta'] ) ? $product['version_beta'] : '';

			// If beta version is not set, return.
			if ( '' === $beta ) {
				return false;
			}

			if ( version_compare( $stable, $beta, '<' ) &&
				self::bsf_allow_beta_updates( $product_id ) ) {

				return true;
			}

			return false;
		}

		/**
		 * Beta version normalized.
		 *
		 * @param array $beta Beta.
		 * @return $version.
		 */
		public function beta_version_normalized( $beta ) {
			$beta_explode = explode( '-', $beta );

			$version = $beta_explode[0] . '.' . str_replace( 'beta', '', $beta_explode[1] );

			return $version;
		}

		/**
		 * Allow Beta updates.
		 *
		 * @param array $product_id Product ID.
		 * @return bool.
		 */
		public static function bsf_allow_beta_updates( $product_id ) {
			return apply_filters( "bsf_allow_beta_updates_{$product_id}", false );
		}

		/**
		 * Get Plugin's slug.
		 *
		 * @param array $template Template.
		 * @return $slug.
		 */
		public function bsf_get_plugin_slug( $template ) {
			$slug = explode( '/', $template );

			if ( isset( $slug[0] ) ) {
				return $slug[0];
			}

			return '';
		}

		/**
		 * Update display license link.
		 */
		public function bsf_update_display_license_link() {
			$brainstorm_all_products = $this->brainstorm_all_products();

			foreach ( $brainstorm_all_products as $key => $product ) {

				if ( isset( $product['id'] ) ) {
					$id = $product['id'];

					if ( isset( $product['template'] ) && '' !== $product['template'] ) {
						$template = $product['template'];
					} elseif ( isset( $product['init'] ) && '' !== $product['init'] ) {
						$template = $product['init'];
					}

					if ( BSF_License_Manager::bsf_is_active_license( $id ) === false ) {

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
		/**
		 *  Brainstorm All Products.
		 */
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

			// array of all the products.
			$all_products = $brainstrom_products_plugins + $brainstrom_products_themes + $bundled;

			return $all_products;
		}
		/**
		 *  Add Registration message.
		 *
		 *  @param array $plugin_data Plugin data.
		 *  @param array $response Response.
		 */
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
				/* translators: %1$s: $parent_name %2%s: $registration_page */
				$message = sprintf( __( ' <br>This plugin is came bundled with the <i>%1$s</i>. For receiving updates, you need to register license of <i>%2$s</i> <a href="%3$s">here</a>.', 'bsf' ), $parent_name, $parent_name, $registration_page );
			} else {
				/* translators: %1$s: $registration_page %2%s: search term */
				$message = sprintf( ' <i>%s</i>', sprintf( __( 'Please <a href="%1$s">activate your license</a> to update the plugin.', 'bsf' ), $registration_page ) );
			}

			if ( true === self::bsf_allow_beta_updates( $product_id ) && $this->is_beta_version( $plugin_data['new_version'] ) ) {
				$message = $message . ' <i>It is recommended to use the beta version on a staging enviornment only.</i>';
			}

			echo wp_kses_post( $message );

		}
		/**
		 * Add Beta update message.
		 *
		 * @param array $plugin_data plugin data.
		 * @param array $response Response.
		 */
		public function add_beta_update_message( $plugin_data, $response ) {
			$plugin_init = isset( $plugin_data['plugin'] ) ? $plugin_data['plugin'] : '';

			if ( '' !== $plugin_init ) {
				$product_id = brainstrom_product_id_by_init( $plugin_init );
			} else {
				$product_id = brainstrom_product_id_by_name( $plugin_name );
			}

			if ( true === self::bsf_allow_beta_updates( $product_id ) && $this->is_beta_version( $plugin_data['new_version'] ) ) {
				echo ' <i>It is recommended to use the beta version on a staging enviornment only.</i>';
			}
		}
		/**
		 * Is Beta version
		 *
		 * @param string $version Version.
		 * @return bool.
		 */
		private function is_beta_version( $version ) {
			return strpos( $version, 'beta' ) ||
				strpos( $version, 'alpha' );
		}

		/**
		 * Modify download package message to hide download URL.
		 *
		 * @param string $reply Reply.
		 * @param string $package Package.
		 * @param string $current Current.
		 * @return string $reply.
		 */
		public function modify_download_package_message( $reply, $package, $current ) {

			// Read atts into separate veriables so that easy to reference below.
			$strings = $current->strings;

			if ( isset( $current->skin->plugin_info ) ) {
				$plugin_info = $current->skin->plugin_info;

				if ( ( isset( $plugin_info['author'] ) && 'Brainstorm Force' === $plugin_info['author'] ) || ( isset( $plugin_info['AuthorName'] ) && 'Brainstorm Force' === $plugin_info['AuthorName'] ) ) {
					$strings['downloading_package'] = __( 'Downloading the update...' );
				}
			} elseif ( isset( $current->skin->theme_info ) ) {

				$theme_info   = $current->skin->theme_info;
				$theme_author = $theme_info->get( 'Author' );

				if ( 'Brainstorm Force' === $theme_author ) {
					$strings['downloading_package'] = __( 'Downloading the update...' );
				}
			}

			// restore the strings back to WP_Upgrader.
			$current->strings = $strings;

			// We are not changing teh return parameter.
			return $reply;
		}

		/**
		 * Install Pluigns Filter
		 *
		 * Add brainstorm bundle products in plugin installer list though filter.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $brainstrom_products   Brainstorm Products.
		 * @return array                        Brainstorm Products merged with Brainstorm Bundle Products.
		 */
		public function plugin_information( $brainstrom_products = array() ) {

			$main_products = (array) get_option( 'brainstrom_bundled_products', array() );

			foreach ( $main_products as $single_product_key => $single_product ) {
				foreach ( $single_product as $bundle_product_key => $bundle_product ) {

					if ( is_object( $bundle_product ) ) {
						$type = $bundle_product->type;
						$slug = $bundle_product->slug;
					} else {
						$type = $bundle_product['type'];
						$slug = $bundle_product['slug'];
					}

					// Add bundled plugin in installer list.
					if ( 'plugin' === $type ) {
						$brainstrom_products['plugins'][ $slug ] = (array) $bundle_product;
					}
				}
			}

			return $brainstrom_products;
		}

	} // class BSF_Update_Manager

	new BSF_Update_Manager();
}
