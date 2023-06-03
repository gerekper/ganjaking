<?php
namespace Perfmatters;

use WP_CLI;

class CLI {

	/**
	 * Activates a license key.
	 * 
	 * ## OPTIONS
     *
     * [<key>]
     * : The license key to add and activate.
     * 
	 * @subcommand activate-license
	 * 
	 */
	public function activate_license($args, $assoc_args) {

		$network = is_multisite() && empty(WP_CLI::get_config()['url']);

		if(!empty($args[0])) {
			$network ? update_site_option('perfmatters_edd_license_key', trim($args[0])) : update_option('perfmatters_edd_license_key', trim($args[0]));
		}

		if(is_multisite()) {

			$license_info = perfmatters_check_license($network);

			if(empty($license_info->activations_left) || $license_info->activations_left !== 'unlimited') {
				WP_CLI::warning(__('Unlimited site license required.', 'perfmatters'));
				return;
			}
		}

		if(perfmatters_activate_license($network)) {
			WP_CLI::success(__('License activated!', 'perfmatters'));
		}
		else {
			WP_CLI::warning(__('License could not be activated.', 'perfmatters'));
		}
	}

	/**
	 * Deactivates a license key.
	 * 
	 * @subcommand deactivate-license
	 */
	public function deactivate_license() {

		$network = is_multisite() && empty(WP_CLI::get_config()['url']);

		if(perfmatters_deactivate_license($network)) {
			WP_CLI::success(__('License deactivated!', 'perfmatters'));
		}
		else {
			WP_CLI::warning(__('License could not be deactivated.', 'perfmatters'));
		}
	}

	/**
	 * Deactivates and removes a license key.
	 * 
	 * @subcommand remove-license
	 */
	public function remove_license() {

		$network = is_multisite() && empty(WP_CLI::get_config()['url']);

		if(perfmatters_deactivate_license($network)) {
			WP_CLI::success('License deactivated!');
		}

		$removed = $network ? delete_site_option('perfmatters_edd_license_key') : delete_option('perfmatters_edd_license_key');

		if($removed) {
			WP_CLI::success(__('License removed!', 'perfmatters'));
		}
		else {
			WP_CLI::warning(__('License could not be removed.', 'perfmatters'));
		}
	}
}