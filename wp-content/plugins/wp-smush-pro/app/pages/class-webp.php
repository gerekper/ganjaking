<?php
/**
 * Local WebP page.
 *
 * @package Smush\App\Pages
 */

namespace Smush\App\Pages;

use Smush\App\Abstract_Summary_Page;
use Smush\App\Interface_Page;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class WebP
 */
class WebP extends Abstract_Summary_Page implements Interface_Page {
	/**
	 * Register meta boxes.
	 */
	public function register_meta_boxes() {
		parent::register_meta_boxes();

		if ( ! WP_Smush::is_pro() ) {
			$this->add_meta_box(
				'webp/upsell',
				__( 'Local WebP', 'wp-smushit' ),
				null,
				array( $this, 'webp_meta_box_header' )
			);

			return;
		}

		if ( ! $this->settings->get( 'webp_mod' ) ) {
			$this->add_meta_box(
				'webp/disabled',
				__( 'Local WebP', 'wp-smushit' ),
				null,
				array( $this, 'webp_meta_box_header' )
			);

			return;
		}

		$this->add_meta_box(
			'webp/webp',
			__( 'Local WebP', 'wp-smushit' ),
			null,
			array( $this, 'webp_meta_box_header' )
		);

		if ( true !== WP_Smush::get_instance()->core()->mod->webp->is_configured() ) {
			$this->add_meta_box(
				'webp_config',
				__( 'Configurations', 'wp-smushit' ),
				array( $this, 'webp_config_meta_box' )
			);
		}

		$this->modals['webp-delete-all'] = array();
	}

	/**
	 * WebP meta box header.
	 *
	 * @since 3.8.0
	 */
	public function webp_meta_box_header() {
		$this->view(
			'webp/meta-box-header',
			array(
				'is_disabled'   => ! $this->settings->get( 'webp_mod' ) || ! WP_Smush::get_instance()->core()->s3->setting_status(),
				'is_configured' => true === WP_Smush::get_instance()->core()->mod->webp->is_configured(),
			)
		);
	}

	/**
	 * WebP meta box.
	 *
	 * @since 3.8.0
	 */
	public function webp_config_meta_box() {
		$webp    = WP_Smush::get_instance()->core()->mod->webp;
		$servers = $webp->get_servers();
		// WebP module does not support iss and cloudflare server.
		unset( $servers['iis'], $servers['cloudflare'] );

		$server_type          = strtolower( $webp->get_server_type() );
		$detected_server      = '';
		$detected_server_name = '';

		if ( isset( $servers[ $server_type ] ) ) {
			$detected_server      = $server_type;
			$detected_server_name = $servers[ $server_type ];
		}

		$this->view(
			'webp/config-meta-box',
			array(
				'servers'              => $servers,
				'detected_server'      => $detected_server,
				'detected_server_name' => $detected_server_name,
				'nginx_config_code'    => $webp->get_nginx_code(),
				'apache_htaccess_code' => $webp->get_apache_code_to_print(),
				'is_htaccess_written'  => $webp->is_htaccess_written(),
			)
		);
	}

}