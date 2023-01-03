<?php
/**
 * Handle mail for background process.
 *
 * @package Smush\Core\Modules\Helpers
 */

namespace Smush\Core\Modules\Bulk;

use Smush\Core\Settings;
use WP_Smush;
use Smush\Core\Modules\Helpers;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mail
 */
class Mail extends Helpers\Mail {
	/**
	 * Plugin id.
	 */
	const PLUGIN_ID = 912164;

	/**
	 * View class.
	 *
	 * @var Helpers\View
	 */
	private $view;

	/**
	 * Constructor.
	 *
	 * @param string $identifier Identifier.
	 */
	public function __construct( $identifier ) {
		parent::__construct( $identifier );
		$this->view = new Helpers\View();
		$this->view->set_template_dir( WP_SMUSH_DIR . 'app/' );
	}

	/**
	 * Whether to receive email or not.
	 *
	 * @return bool
	 */
	public function reporting_email_enabled() {
		return Settings::get_instance()->get( 'background_email' );
	}

	/**
	 * Get sender name.
	 *
	 * @return string
	 */
	protected function get_sender_name() {
		if ( WP_Smush::is_pro() && $this->whitelabel->enabled() ) {
			$plugin_label = $this->whitelabel->get_plugin_name( self::PLUGIN_ID );
			if ( empty( $plugin_label ) ) {
				$plugin_label = __( 'Bulk Compression', 'wp-smushit' );
			}
		} else {
			$plugin_label = WP_Smush::is_pro() ? __( 'Smush Pro', 'wp-smushit' ) : __( 'Smush', 'wp-smushit' );
		}

		return $plugin_label;
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	protected function get_mail_subject() {
		$site_url = get_site_url();
		$site_url = preg_replace( '#http(s)?://(www.)?#', '', $site_url );
		if ( $this->whitelabel->enabled() ) {
			/* translators: %s - Site Url */
			return sprintf( __( 'Bulk compression completed for %s', 'wp-smushit' ), esc_html( $site_url ) );
		}
		/* translators: %s - Site Url */
		return sprintf( __( 'Bulk Smush completed for %s', 'wp-smushit' ), esc_html( $site_url ) );
	}
	/**
	 * Get email message.
	 *
	 * @return string
	 */
	protected function get_mail_message() {
		if ( $this->whitelabel->enabled() ) {
			$title          = __( 'Bulk Compression', 'wp-smushit' );
			$temp_file_name = 'email/index-whitelabel';
		} else {
			$title          = __( 'Bulk Smush', 'wp-smushit' );
			$temp_file_name = 'email/index';
		}

		return $this->view->get_template_content(
			$temp_file_name,
			array(
				'title'          => $title,
				'content_body'   => $this->get_summary_content(),
				'content_upsell' => $this->get_upsell_content(),
			)
		);
	}
	/**
	 * Get the summary content of bulk smush.
	 *
	 * @return string
	 */
	private function get_summary_content() {
		$bg_optimization = WP_Smush::get_instance()->core()->mod->bg_optimization;
		$site_url        = get_site_url();
		$total_items     = $bg_optimization->get_total_items();
		$failed_items    = $bg_optimization->get_failed_items();
		if ( empty( $failed_items ) ) {
			$redirect_url = is_network_admin() ? network_admin_url( 'admin.php?page=smush' ) : admin_url( 'admin.php?page=smush-bulk' );
		} else {
			$redirect_url = admin_url( 'upload.php?mode=list&attachment-filter=post_mime_type:image&m=0&smush-filter=failed_processing' );
		}
		return $this->view->get_template_content(
			'email/bulk-smush',
			array_merge(
				array(
					'site_url'      => $site_url,
					'name'          => $this->get_recipient_name(),
					'total_items'   => $total_items,
					'failed_items'  => $failed_items,
					'smushed_items' => $total_items - $failed_items,
					'redirect_url'  => $redirect_url,
				),
				$this->get_summary_template_args()
			)
		);
	}

	/**
	 * Get extra template arguments.
	 *
	 * @return array
	 */
	private function get_summary_template_args() {
		$bg_optimization = WP_Smush::get_instance()->core()->mod->bg_optimization;
		$failed_items    = $bg_optimization->get_failed_items();
		if ( $failed_items > 0 ) {
			$failed_msg = __( 'The number of images unsuccessfully compressed (find out why below).', 'wp-smushit' );
		} else {
			$failed_msg = __( 'The number of images unsuccessfully compressed.', 'wp-smushit' );
		}
		if ( $this->whitelabel->enabled() ) {
			return array(
				/* Translators: %s: Site URL */
				'mail_title'    => __( 'Bulk compression completed for %s', 'wp-smushit' ),
				'mail_desc'     => __( 'The bulk compress you actioned has successfully completed. Here’s a quick summary of the results:', 'wp-smushit' ),
				'total_title'   => __( 'Total image attachments', 'wp-smushit' ),
				'total_desc'    => __( 'The number of images analyzed during the bulk compress.', 'wp-smushit' ),
				'smushed_title' => __( 'Images compressed successfully', 'wp-smushit' ),
				'smushed_desc'  => __( 'The number of images successfully compressed.', 'wp-smushit' ),
				'failed_title'  => __( 'Images failed to compress', 'wp-smushit' ),
				'failed_desc'   => $failed_msg,
			);
		}

		return array(
			/* Translators: %s: Site URL */
			'mail_title'    => __( 'Bulk Smush completed for %s', 'wp-smushit' ),
			'mail_desc'     => __( 'The bulk smush you actioned has successfully completed. Here’s a quick summary of the results:', 'wp-smushit' ),
			'total_title'   => __( 'Total image attachments', 'wp-smushit' ),
			'total_desc'    => __( 'The number of images analyzed during the bulk smush.', 'wp-smushit' ),
			'smushed_title' => __( 'Images smushed successfully', 'wp-smushit' ),
			'smushed_desc'  => __( 'The number of images successfully compressed.', 'wp-smushit' ),
			'failed_title'  => __( 'Images failed to smush', 'wp-smushit' ),
			'failed_desc'   => $failed_msg,
		);
	}

	/**
	 * Get upsell CDN content.
	 */
	private function get_upsell_content() {
		if ( WP_Smush::is_pro() ) {
			return;
		}
		$upsell_url = add_query_arg(
			array(
				'coupon'       => 'SMUSH30OFF',
				'checkout'     => 0,
				'utm_source'   => 'smush',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'smush_bulksmush_bo_email',
			),
			'https://wpmudev.com/project/wp-smush-pro/'
		);
		return $this->view->get_template_content(
			'email/upsell-cdn',
			array(
				'upsell_url' => $upsell_url,
			)
		);
	}
}
