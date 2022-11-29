<?php
/**
 * Product update functions.
 *
 * @package BSF core
 */

// Alternative function for wp_remote_get.
if ( ! function_exists( 'bsf_get_remote_version' ) ) {
	/**
	 * Get remote version for product
	 *
	 * @param array $products products data.
	 * @return array
	 */
	function bsf_get_remote_version( $products ) {
		global $ultimate_referer;

		$path = bsf_get_api_url() . '?referer=' . $ultimate_referer;

		$data = array(
			'action'   => 'bsf_get_product_versions',
			'ids'      => $products,
			'site_url' => get_site_url(),
		);

		$request = wp_remote_post(
			$path,
			array(
				'body'    => $data,
				'timeout' => '10',
			)
		);

		// Request http URL if the https version fails.
		if ( is_wp_error( $request ) && 200 !== wp_remote_retrieve_response_code( $request ) ) {
			$path    = bsf_get_api_url( true ) . '?referer=' . $ultimate_referer;
			$request = wp_remote_post(
				$path,
				array(
					'body'    => $data,
					'timeout' => '8',
				)
			);
		}

		if ( ! is_wp_error( $request ) || 200 === wp_remote_retrieve_response_code( $request ) ) {
			$result = json_decode( wp_remote_retrieve_body( $request ) );
			if ( ! empty( $result ) ) {
				if ( empty( $result->error ) ) {
					return $result->updated_versions;
				} else {
					return $result->error;
				}
			}
		}
	}
}

if ( ! function_exists( 'bsf_check_product_update' ) ) {
	/**
	 * Check product updates.
	 *
	 * @return void
	 */
	function bsf_check_product_update() {
		$is_update    = true;
		$registered   = array();
		$all_products = brainstorm_get_all_products( false, false, true );

		foreach ( $all_products as $key => $product ) {
			if ( ! isset( $product['id'] ) ) {
				continue;
			}
			$constant = strtoupper( str_replace( '-', '_', $product['id'] ) );
			$constant = 'BSF_' . $constant . '_CHECK_UPDATES';
			if ( defined( $constant ) && ( 'false' === constant( $constant ) || false === constant( $constant ) ) ) {
				continue;
			}
			$registered[] = $product['id'];
		}

		$remote_versions = bsf_get_remote_version( $registered );

		$brainstrom_products         = get_option( 'brainstrom_products', array() );
		$brainstrom_bundled_products = get_option( 'brainstrom_bundled_products', array() );

		$bsf_product_plugins = isset( $brainstrom_products['plugins'] ) ? $brainstrom_products['plugins'] : array();
		$bsf_product_themes  = isset( $brainstrom_products['themes'] ) ? $brainstrom_products['themes'] : array();

		if ( false !== $remote_versions ) {
			if ( ! empty( $remote_versions ) ) {
				$is_bundled_update = false;
				foreach ( $remote_versions as $rkey => $remote_data ) {
					$rid               = ( isset( $remote_data->id ) ) ? (string) $remote_data->id : '';
					$remote_version    = ( isset( $remote_data->remote_version ) ) ? $remote_data->remote_version : '';
					$in_house          = ( isset( $remote_data->in_house ) ) ? $remote_data->in_house : '';
					$on_market         = ( isset( $remote_data->on_market ) ) ? $remote_data->on_market : '';
					$is_product_free   = ( isset( $remote_data->is_product_free ) ) ? $remote_data->is_product_free : '';
					$short_name        = ( isset( $remote_data->short_name ) ) ? $remote_data->short_name : '';
					$changelog_url     = ( isset( $remote_data->changelog_url ) ) ? $remote_data->changelog_url : '';
					$purchase_url      = ( isset( $remote_data->purchase_url ) ) ? $remote_data->purchase_url : '';
					$version_beta      = ( isset( $remote_data->version_beta ) ) ? $remote_data->version_beta : '';
					$download_url      = ( isset( $remote_data->download_url ) ) ? $remote_data->download_url : '';
					$download_url_beta = ( isset( $remote_data->download_url_beta ) ) ? $remote_data->download_url_beta : '';
					$tested_upto       = ( isset( $remote_data->tested ) ) ? $remote_data->tested : '';
					foreach ( $bsf_product_plugins as $key => $plugin ) {
						if ( ! isset( $plugin['id'] ) ) {
							continue;
						}
						$pid = (string) $plugin['id'];
						if ( $pid === $rid ) {
							$brainstrom_products['plugins'][ $key ]['remote']            = $remote_version;
							$brainstrom_products['plugins'][ $key ]['in_house']          = $in_house;
							$brainstrom_products['plugins'][ $key ]['on_market']         = $on_market;
							$brainstrom_products['plugins'][ $key ]['is_product_free']   = $is_product_free;
							$brainstrom_products['plugins'][ $key ]['short_name']        = $short_name;
							$brainstrom_products['plugins'][ $key ]['changelog_url']     = $changelog_url;
							$brainstrom_products['plugins'][ $key ]['purchase_url']      = $purchase_url;
							$brainstrom_products['plugins'][ $key ]['version_beta']      = $version_beta;
							$brainstrom_products['plugins'][ $key ]['download_url_beta'] = $download_url_beta;
							$brainstrom_products['plugins'][ $key ]['download_url']      = $download_url;
							$brainstrom_products['plugins'][ $key ]['tested']            = $tested_upto;

							// Deregister status for plugin.
							if ( isset( $remote_data->status ) && 0 === $remote_data->status ) {
								$brainstrom_products['plugins'][ $key ]['status'] = 'not-registered';
							} else {
								$brainstrom_products['plugins'][ $key ]['status'] = 'registered';
							}

							$is_update = true;
						}
					}

					foreach ( $bsf_product_themes as $key => $theme ) {
						if ( ! isset( $theme['id'] ) ) {
							continue;
						}
						$pid = $theme['id'];
						if ( $pid === $rid ) {
							$brainstrom_products['themes'][ $key ]['remote']            = $remote_version;
							$brainstrom_products['themes'][ $key ]['in_house']          = $in_house;
							$brainstrom_products['themes'][ $key ]['on_market']         = $on_market;
							$brainstrom_products['themes'][ $key ]['is_product_free']   = $is_product_free;
							$brainstrom_products['themes'][ $key ]['short_name']        = $short_name;
							$brainstrom_products['themes'][ $key ]['changelog_url']     = $changelog_url;
							$brainstrom_products['themes'][ $key ]['purchase_url']      = $purchase_url;
							$brainstrom_products['themes'][ $key ]['version_beta']      = $version_beta;
							$brainstrom_products['themes'][ $key ]['download_url']      = $download_url;
							$brainstrom_products['themes'][ $key ]['download_url_beta'] = $download_url_beta;
							$is_update = true;

							// Deregister status for theme.
							if ( isset( $remote_data->status ) && 0 === $remote_data->status ) {
								$brainstrom_products['themes'][ $key ]['status'] = 'not-registered';
							} else {
								$brainstrom_products['themes'][ $key ]['status'] = 'registered';
							}
						}
					}

					if ( isset( $remote_data->bundled_products ) && ! empty( $remote_data->bundled_products ) ) {
						if ( ! empty( $brainstrom_bundled_products ) && is_array( $brainstrom_bundled_products ) ) {
							foreach ( $brainstrom_bundled_products as $bkeys => $bps ) {
								foreach ( $bps as $bkey => $bp ) {
									if ( ! isset( $bp->id ) ) {
										continue;
									}
									foreach ( $remote_data->bundled_products as $rbp ) {
										if ( ! isset( $rbp->id ) ) {
											continue;
										}
										if ( $rbp->id === $bp->id ) {
											$bprd = $brainstrom_bundled_products[ $bkeys ];
											$brainstrom_bundled_products[ $bkeys ][ $bkey ]->remote        = $rbp->remote_version;
											$brainstrom_bundled_products[ $bkeys ][ $bkey ]->parent        = $rbp->parent;
											$brainstrom_bundled_products[ $bkeys ][ $bkey ]->short_name    = $rbp->short_name;
											$brainstrom_bundled_products[ $bkeys ][ $bkey ]->changelog_url = $rbp->changelog_url;

											if ( isset( $rbp->download_url ) ) {
												$brainstrom_bundled_products[ $bkeys ][ $bkey ]->download_url = $rbp->download_url;
											}

											if ( isset( $rbp->download_url_beta ) ) {
												$brainstrom_bundled_products[ $bkeys ][ $bkey ]->download_url_beta = $rbp->download_url_beta;
											}

											$is_bundled_update = true;
										}
									}
								}
							}
						}
					}
				}

				if ( $is_bundled_update ) {
					update_option( 'brainstrom_bundled_products', $brainstrom_bundled_products );
				}
			}
		}

		if ( $is_update ) {
			update_option( 'brainstrom_products', $brainstrom_products );
		}
	}
}
