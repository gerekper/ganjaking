<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Utilities handler.
 *
 * @since 1.10.0
 */
class WC_Memberships_Utilities {


	/** @var \WC_Memberships_Grant_Retroactive_Access instance */
	private $grant_retroactive_access;

	/** @var \WC_Memberships_CSV_Export_User_Memberships instance */
	private $user_memberships_export;

	/** @var \WC_Memberships_CSV_Import_User_Memberships instance */
	private $user_memberships_import;

	/** @var \WC_Memberships_User_Memberships_Reschedule_Events instance */
	private $user_memberships_reschedule_events;


	/**
	 * Load utilities.
	 *
	 * Background jobs and batch jobs handlers need to be always loaded to run properly.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		$plugin = wc_memberships();

		// load abstracts
		require_once( $plugin->get_plugin_path() . '/includes/utilities/abstract-wc-memberships-job-handler.php' );
		// load handlers
		$this->grant_retroactive_access           = $plugin->load_class( '/includes/utilities/class-wc-memberships-grant-retroactive-access.php', 'WC_Memberships_Grant_Retroactive_Access' );
		$this->user_memberships_import            = $plugin->load_class( '/includes/utilities/class-wc-memberships-csv-import-user-memberships.php', 'WC_Memberships_CSV_Import_User_Memberships' );
		$this->user_memberships_export            = $plugin->load_class( '/includes/utilities/class-wc-memberships-csv-export-user-memberships.php', 'WC_Memberships_CSV_Export_User_Memberships' );
		$this->user_memberships_reschedule_events = $plugin->load_class( '/includes/utilities/class-wc-memberships-user-memberships-reschedule-events.php', 'WC_Memberships_User_Memberships_Reschedule_Events' );
	}


	/**
	 * Returns the grant access background job handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Memberships_Grant_Retroactive_Access
	 */
	public function get_grant_retroactive_access_instance() {
		return $this->grant_retroactive_access;
	}


	/**
	 * Returns the user memberships background import handler.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Memberships_CSV_Import_User_Memberships
	 */
	public function get_user_memberships_import_instance() {
		return $this->user_memberships_import;
	}


	/**
	 * Returns the user memberships background export handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Memberships_CSV_Export_User_Memberships
	 */
	public function get_user_memberships_export_instance() {
		return $this->user_memberships_export;
	}


	/**
	 * Returns the background handler instance for rescheduling user memberships events.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Memberships_User_Memberships_Reschedule_Events
	 */
	public function get_user_memberships_reschedule_events_instance() {
		return $this->user_memberships_reschedule_events;
	}


	/**
	 * Returns a job handler by job name.
	 *
	 * @since 1.10.0
	 *
	 * @param string $job_name job type identifier
	 * @return null|\WC_Memberships_Job_Handler handler or null on error
	 */
	public function get_job_handler( $job_name ) {

		switch ( $job_name ) {
			case 'csv_import_user_memberships' :
				$handler = $this->get_user_memberships_import_instance();
			break;
			case 'csv_export_user_memberships' :
				$handler = $this->get_user_memberships_export_instance();
			break;
			case 'grant_retroactive_access' :
				$handler = $this->get_grant_retroactive_access_instance();
			break;
			case 'user_memberships_reschedule_events' :
				$handler = $this->get_user_memberships_reschedule_events_instance();
			break;
			default:
				$handler = null;
			break;
		}

		return $handler;
	}


	/**
	 * Returns a background job object by job name and ID.
	 *
	 * @since 1.10.0
	 *
	 * @param string $job_name the job name
	 * @param int|string|\stdClass|null $job_id a job identifier
	 * @return false|null|\stdClass will return false on error, null when job cannot be determined
	 */
	public function get_job_object( $job_name, $job_id ) {

		$job     = false;
		$handler = $this->get_job_handler( $job_name );

		if ( null !== $handler ) {
			$job = $handler->get_job( $job_id );
		}

		return $job;
	}


}
