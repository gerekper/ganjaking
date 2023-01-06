<?php
/** @var $wpml_core_plugin OTGS_Installer_WPML_Core_Plugin **/

/*
 * WPML checks to make pre-selection / extra steps on the installer
 * this is part of a temporary solution until the installer wizard
 * is released.
 */
$is_wpml = $repository_id === 'wpml';
$is_wpml_installed = $wpml_core_plugin->is_installed();
$is_wpml_active = $wpml_core_plugin->is_active();

/*
 * Toolset checks to make Toolset Types and Blocks pre-selected if no Toolset
 * plugin is installed so far. As for WPML just a temporary solution.
 */
$is_toolset = $repository_id === 'toolset';
$is_any_toolset_plugin_installed = false;
if( $is_toolset ) {
    $plugins = get_plugins(); // get_plugins() is used before and cached by WP.
    foreach ( $plugins as $plugin_id => $plugin ) {
        if ( strpos( $plugin['Name'], 'Toolset' ) === 0 ) {
            $is_any_toolset_plugin_installed = true;
            break;
        }
    }
}

/*
 * Download button should always be disabled except when for WPML the core
 * plugin is not installed or for Toolset no Toolset plugin.
 * For the first plugins to install also the activate checkbox should be
 * preselected.
 */
$download_button_disabled = ' disabled';
$activate_checkbox_checked = '';
if(
    ( $is_wpml && ! $is_wpml_installed )
    || ( $is_toolset && ! $is_any_toolset_plugin_installed )
) {
    $download_button_disabled = '';
    $activate_checkbox_checked = ' checked';
}
?>
<form method="post" class="otgsi_downloads_form">
	<?php

	use OTGS\Installer\CommercialTab\DownloadFilter;
	use OTGS\Installer\CommercialTab\DownloadsList;

	$sections = $this->get_plugins_sections( $repository_id, $package['downloads'] );
	if ( count( $sections ) === 1 ) {
		?>
        <div class="installer-table-wrap">
        <table class="widefat installer-plugins">
            <thead>
            <tr>
                <th>&nbsp;</th>
                <th><?php _e( 'Plugin', 'installer' ) ?></th>
                <th><?php _e( 'Installed', 'installer' ) ?></th>
                <th><?php _e( 'Current', 'installer' ) ?></th>
                <th><?php _e( 'Released', 'installer' ) ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody><?php
			foreach ( reset( $sections )['downloads'] as $download_id => $download ) {
				if ( DownloadFilter::shouldDisplayRecord($download_id)) {
					echo DownloadsList::getDownloadRow( $download_id, $download, $site_key, $repository_id );
				}
			}
			?>
            </tbody>
        </table>
        </div><?php
	} else {
		foreach ( $sections as $section ) {
			if ( ! empty( $section['downloads'] ) ) {
				?>
                <div class="installer-table-wrap">
                <table class="widefat installer-plugins">
                    <thead>
                    <tr>
                        <th colspan="9"><strong><?php echo $section['name'] ?></strong></th>
                    </tr>
                    <tr>
                        <th>&nbsp;</th>
                        <th><?php _e( 'Plugin', 'installer' ) ?></th>
                        <th><?php _e( 'Installed', 'installer' ) ?></th>
                        <th><?php _e( 'Current', 'installer' ) ?></th>
                        <th><?php _e( 'Released', 'installer' ) ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody><?php
					foreach ( $section['downloads'] as $download_id => $download ) {
					    if ( DownloadFilter::shouldDisplayRecord($download_id)) {
						    $checked = ( $is_wpml && ! $is_wpml_active && $download_id === "sitepress-multilingual-cms" ) // WPML Sitepress Multilingual
						        || ( $is_toolset && ! $is_any_toolset_plugin_installed && in_array( $download_id, [ 'types', 'toolset-blocks' ] ) ); // Toolset Types or Toolset Blocks
						    echo DownloadsList::getDownloadRow( $download_id, $download, $site_key, $repository_id, $checked );
					    }
					}
					?>
                    </tbody>
                </table>
                </div><?php
			}
		}
	}
	?>

    <br/>

    <div class="installer-error-box">
		<?php if ( ! WP_Installer()->dependencies->is_uploading_allowed() ): ?>
            <p><?php printf( __( 'Downloading is not possible because WordPress cannot write into the plugins folder. %sHow to fix%s.', 'installer' ),
					'<a href="http://codex.wordpress.org/Changing_File_Permissions">', '</a>' ) ?></p>
		<?php elseif ( WP_Installer()->dependencies->is_win_paths_exception( $repository_id ) ): ?>
            <p><?php echo WP_Installer()->dependencies->win_paths_exception_message() ?></p>
		<?php endif; ?>
    </div>

    <input type="submit" class="button-secondary" value="<?php esc_attr_e( 'Download', 'installer' ) ?>"
		<?php echo $download_button_disabled ?> />
    &nbsp;
    <label><input name="activate" type="checkbox" value="1"
			<?php echo $download_button_disabled.$activate_checkbox_checked ?> />&nbsp;<?php _e( 'Activate after download', 'installer' ) ?></label>

    <div class="installer-download-progress-status"></div>

    <div class="installer-status-success"><?php _e( 'Operation complete!', 'installer' ) ?></div>

    <span class="installer-revalidate-message hidden"><?php _e( "Download failed!\n\nPlease refresh the page and try again.", 'installer' ) ?></span>
</form>

