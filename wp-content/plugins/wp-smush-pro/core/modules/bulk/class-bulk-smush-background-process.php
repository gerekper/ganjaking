<?php

namespace Smush\Core\Modules\Bulk;

use Smush\Core\Core;
use Smush\Core\Helper;
use Smush\Core\Modules\Background\Background_Process;
use Smush\Core\Modules\Smush;
use WP_Smush;

class Bulk_Smush_Background_Process extends Background_Process {
	public function __construct( $identifier ) {
		parent::__construct( $identifier );

		$this->set_logger( Helper::logger() );
	}

	/**
	 * @param $task Smush_Background_Task
	 *
	 * @return boolean
	 */
	protected function task( $task ) {
		if ( ! is_a( $task, Smush_Background_Task::class ) || ! $task->is_valid() ) {
			Helper::logger()->error( 'An invalid background task was encountered.' );

			return false;
		}

		$attachment_id = $task->get_image_id();

		$smush = WP_Smush::get_instance()->core()->mod->smush;
		$smush->smushit( $attachment_id, $meta, $errors );

		/**
		 * @var $errors \WP_Error
		 */
		$errors_encountered = $errors && is_wp_error( $errors ) && $errors->has_errors();
		if ( $errors_encountered ) {
			Helper::logger()->error( "Error encountered while smushing attachment ID $attachment_id:" . $errors->get_error_message() );

			// TODO: save the error in meta for the media library filters

			return false;
		}

		if ( $task->get_type() === Smush_Background_Task::TASK_TYPE_RESMUSH ) {
			$smush->update_resmush_list( $attachment_id );
		} else {
			Core::add_to_smushed_list( $attachment_id );
		}

		$smush_data         = get_post_meta( $attachment_id, Smush::$smushed_meta_key, true );
		$resize_savings     = get_post_meta( $attachment_id, 'wp-smush-resize_savings', true );
		$conversion_savings = Helper::get_pngjpg_savings( $attachment_id );

		do_action(
			'image_smushed',
			$attachment_id,
			array(
				'count'              => ! empty( $smush_data['sizes'] ) ? count( $smush_data['sizes'] ) : 0,
				'size_before'        => ! empty( $smush_data['stats'] ) ? $smush_data['stats']['size_before'] : 0,
				'size_after'         => ! empty( $smush_data['stats'] ) ? $smush_data['stats']['size_after'] : 0,
				'savings_resize'     => max( $resize_savings, 0 ),
				'savings_conversion' => $conversion_savings['bytes'] > 0 ? $conversion_savings : 0,
				'is_lossy'           => ! empty( $smush_data ['stats'] ) ? $smush_data['stats']['lossy'] : false,
			)
		);

		return true;
	}

	/**
	 * Email when bulk smush complete.
	 */
	protected function complete() {
		parent::complete();
		// Send email.
		if ( $this->get_status()->get_total_items() ) {
			$mail = new Mail( 'wp_smush_background' );
			if ( $mail->reporting_email_enabled() ) {
				if ( $mail->send_email() ) {
					Helper::logger()->notice(
						sprintf(
							'Bulk Smush completed for %s, and sent a summary email to %s at %s.',
							get_site_url(),
							join( ',', $mail->get_mail_recipients() ),
							wp_date( 'd/m/y H:i:s' )
						)
					);
				} else {
					Helper::logger()->error(
						sprintf(
							'Bulk Smush completed for %s, but could not send a summary email to %s at %s.',
							get_site_url(),
							join( ',', $mail->get_mail_recipients() ),
							wp_date( 'd/m/y H:i:s' )
						)
					);
				}
			} else {
				Helper::logger()->info( sprintf( 'Bulk Smush completed for %s, and reporting email is disabled.', get_site_url() ) );
			}
		}
	}
}
