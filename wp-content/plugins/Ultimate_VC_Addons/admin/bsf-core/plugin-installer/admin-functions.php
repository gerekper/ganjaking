<?php
/**
 * Admin functions for bsf core.
 *
 * @package BSF core
 */

/**
 * Prevent direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! function_exists( 'check_bsf_product_status' ) ) {
	/**
	 * Get status of product.
	 *
	 * @param  int $id product id.
	 * @return bool
	 */
	function check_bsf_product_status( $id ) {
		$brainstrom_products = ( get_option( 'brainstrom_products' ) ) ? get_option( 'brainstrom_products' ) : array();
		$bsf_product_themes  = ( isset( $brainstrom_products['themes'] ) ) ? $brainstrom_products['themes'] : array();

		if ( empty( $brainstrom_products ) ) {
			return false;
		}

		$status = false;
		foreach ( $brainstrom_products as $products ) {
			foreach ( $products as $key => $product ) {
				if ( $product['id'] === $id ) {
					$status = ( isset( $product['status'] ) ) ? $product['status'] : '';
					break;
				}
			}
		}

		return $status;
	}
}

if ( ! function_exists( 'get_bundled_plugins' ) ) {


	/**
	 * Retrieves bundled plugin data.
	 *
	 * @param  string $template product template.
	 * @return void
	 */
	function get_bundled_plugins( $template = '' ) {

		global $ultimate_referer;

		$brainstrom_products = get_option( 'brainstrom_products', array() );

		$prd_ids = array();

		// If brainstrom_products is not yet set, call bsf core init.
		if ( is_array( $brainstrom_products ) ) {
			init_bsf_core();
			$brainstrom_products = get_option( 'brainstrom_products', array() );
		}

		foreach ( $brainstrom_products as $key => $value ) {
			foreach ( $value as $key => $value2 ) {
				array_push( $prd_ids, $key );
			}
		}

		$path = bsf_get_api_url() . '?referer=' . $ultimate_referer;

		$data = array(
			'action' => 'bsf_fetch_brainstorm_products',
			'id'     => $prd_ids,
		);

		$request = wp_remote_post(
			$path,
			array(
				'body'    => $data,
				'timeout' => '10',
			)
		);

		// Request http URL if the https version fails.
		if ( is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) !== 200 ) {
			$path    = bsf_get_api_url( true ) . '?referer=' . $ultimate_referer;
			$request = wp_remote_post(
				$path,
				array(
					'body'    => $data,
					'timeout' => '8',
				)
			);
		}

		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			$result  = json_decode( $request['body'] );
			$bundled = array();
			$simple  = array();

			if ( ! empty( $result ) ) {
				if ( ! empty( $result->bundled ) ) {
					$bundled = $result->bundled;
				}
				if ( ! empty( $result->simple ) ) {
					$simple = $result->simple;
				}
			}

			foreach ( $bundled as $key => $value ) {
				if ( empty( $value ) ) {
					unset( $bundled->$key );
				}
			}

			$brainstrom_bundled_products = (array) $bundled;
			update_option( 'brainstrom_bundled_products', $brainstrom_bundled_products );

			// update 'brainstorm_products'.
			$simple = json_decode( wp_json_encode( $simple ), 1 );

			foreach ( $brainstrom_products as $type => $products ) {

				foreach ( $products as $key => $product ) {
					$old_id = isset( $product['id'] ) ? $product['id'] : '';

					$simple[ $type ][ $old_id ]['template']     = isset( $brainstrom_products[ $type ][ $old_id ]['template'] ) ? $brainstrom_products[ $type ][ $old_id ]['template'] : '';
					$simple[ $type ][ $old_id ]['remote']       = isset( $simple[ $type ][ $old_id ]['version'] ) ? $simple[ $type ][ $old_id ]['version'] : '';
					$simple[ $type ][ $old_id ]['version']      = isset( $brainstrom_products[ $type ][ $old_id ]['version'] ) ? $brainstrom_products[ $type ][ $old_id ]['version'] : '';
					$simple[ $type ][ $old_id ]['purchase_key'] = isset( $brainstrom_products[ $type ][ $old_id ]['purchase_key'] ) ? $brainstrom_products[ $type ][ $old_id ]['purchase_key'] : '';
					$simple[ $type ][ $old_id ]['status']       = isset( $brainstrom_products[ $type ][ $old_id ]['status'] ) ? $brainstrom_products[ $type ][ $old_id ]['status'] : '';
					$simple[ $type ][ $old_id ]['message']      = isset( $brainstrom_products[ $type ][ $old_id ]['message'] ) ? $brainstrom_products[ $type ][ $old_id ]['message'] : '';
				}
			}

			update_option( 'brainstrom_products', $simple );
		}
	}
}

/**
 * Displays bundled product list for product.
 *
 * @param string $product_id Product ID.
 * @param bool   $installed Show installed products?.
 * @return string
 */
function bsf_render_bundled_products( $product_id, $installed ) {

	$product_status = check_bsf_product_status( $product_id );

	$brainstrom_bundled_products = get_option( 'brainstrom_bundled_products', array() );

	if ( isset( $brainstrom_bundled_products[ $product_id ] ) ) {
		$brainstrom_bundled_products = $brainstrom_bundled_products[ $product_id ];
	}

	usort( $brainstrom_bundled_products, 'bsf_sort' );

	$global_plugin_installed = 0;
	$global_plugin_activated = 0;
	$total_bundled_plugins   = count( $brainstrom_bundled_products );
	foreach ( $brainstrom_bundled_products as $key => $product ) {
		if ( ! isset( $product->id ) || empty( $product->id ) ) {
			continue;
		}
		if ( isset( $request_product_id ) && $request_product_id !== $product->id ) {
			continue;
		}
		$plugin_abs_path = WP_PLUGIN_DIR . '/' . $product->init;
		if ( is_file( $plugin_abs_path ) ) {
			$global_plugin_installed++;

			if ( is_plugin_active( $product->init ) ) {
				$global_plugin_activated++;
			}
		}
	}

	ob_start();
	if ( $total_bundled_plugins === $global_plugin_installed ) {
		?>
		<div class="bsf-extensions-no-active">
			<div class="bsf-extensions-title-icon"><span class="dashicons dashicons-smiley"></span></div>
			<p class="bsf-text-light"><em><?php esc_html_e( 'All available extensions have been installed!', 'bsf' ); ?></em></p>
		</div>
		<?php
		return ob_get_clean();
	}

	if ( empty( $brainstrom_bundled_products ) ) {
		?>

		<div class="bsf-extensions-no-active">
			<div class="bsf-extensions-title-icon"><span class="dashicons dashicons-download"></span></div>
			<p class="bsf-text-light"><em><?php esc_html_e( 'No extensions available yet!', 'bsf' ); ?></em></p>

			<div class="bsf-cp-rem-bundle" style="margin-top: 30px;">
				<a class="button-primary" href="<?php echo esc_url( $reset_bundled_url ); ?>"><?php esc_html_e( 'Refresh Bundled Products', 'bsf' ); ?></a>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	foreach ( $brainstrom_bundled_products as $key => $product ) {

		if ( ! isset( $product->id ) || empty( $product->id ) ) {
			continue;
		}

		if ( isset( $request_product_id ) && $request_product_id !== $product->id ) {
			continue;
		}

		$is_plugin_installed = false;
		$is_plugin_activated = false;

		$plugin_abs_path = WP_PLUGIN_DIR . '/' . $product->init;
		if ( is_file( $plugin_abs_path ) ) {
			$is_plugin_installed = true;

			if ( is_plugin_active( $product->init ) ) {
				$is_plugin_activated = true;
			}
		}

		if ( ( $is_plugin_installed && ! $installed ) || ( ! $is_plugin_installed && $installed ) ) {
			continue;
		}

		if ( $is_plugin_installed && $is_plugin_activated ) {
			$class = 'active-plugin';
		} elseif ( $is_plugin_installed && ! $is_plugin_activated ) {
			$class = 'inactive-plugin';
		} else {
			$class = 'plugin-not-installed';
		}
		?>
		<li id="ext-<?php echo esc_attr( $key ); ?>" class="bsf-extension <?php echo esc_attr( $class ); ?> bsf-extension-<?php echo esc_attr( $product->slug ); ?>" data-init="<?php echo esc_attr( $product->init ); ?>">
			<?php if ( ! $is_plugin_installed ) { ?>
				<div class="bsf-extension-start-install">
					<div class="bsf-extension-start-install-content">
						<h2><?php esc_html_e( 'Downloading', 'bsf' ); ?><div class="bsf-css-loader"></div></h2>
					</div>
				</div>
			<?php } ?>
			<div class="top-section">
				<?php if ( ! empty( $product->product_image ) ) { ?>
					<div class="bsf-extension-product-image">
						<div class="bsf-extension-product-image-stick">
							<img src="<?php echo esc_url( $product->product_image ); ?>" class="img" alt="image"/>
						</div>
					</div>
				<?php } ?>
				<div class="bsf-extension-info">
					<?php $name = ( isset( $product->short_name ) ) ? $product->short_name : $product->name; ?>
					<h4 class="title"><?php echo esc_html( $name ); ?></h4>
					<p class="desc"><?php echo esc_html( $product->description ); ?><span class="author"><cite>By <?php echo esc_html( $product->author ); ?></cite></span></p>
				</div>
			</div>
			<div class="bottom-section">
				<?php
				$button_class = '';
				if ( ! $is_plugin_installed ) {
					if ( ( ! $product->licence_require || 'false' === $product->licence_require ) || 'registered' === $product_status ) {

						$installer_url = bsf_exension_installer_url( $product_id );
						$button        = __( 'Install', 'bsf' );
						$button_class  = 'bsf-install-button install-now';
					} elseif ( ( $product->licence_require || 'true' === $product->licence_require ) && 'registered' !== $product_status ) {

						$installer_url = bsf_registration_page_url( '&id=' . $product_id, $product_id );
						$button        = __( 'Validate Purchase', 'bsf' );
						$button_class  = 'bsf-validate-licence-button';
					}
				} else {
					$current_name = strtolower( bsf_get_current_name( $product->init, $product->type ) );
					$current_name = preg_replace( '![^a-z0-9]+!i', '-', $current_name );
					if ( is_multisite() ) {
						$installer_url = network_admin_url( 'plugins.php#' . $current_name );
					} else {
						$installer_url = admin_url( 'plugins.php#' . $current_name );
					}
					$button = __( 'Installed', 'bsf' );
				}

				?>
				<a class="button button-primary extension-button <?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_url( $installer_url ); ?>" data-slug="<?php echo esc_html( $product->slug ); ?>" data-ext="<?php echo esc_attr( $key ); ?>" data-pid="<?php echo esc_attr( $product->id ); ?>" data-bundled="true" data-action="install"><?php echo esc_html( $button ); ?></a>
			</div>
		</li>
		<?php
	}

	return ob_get_clean();
}
