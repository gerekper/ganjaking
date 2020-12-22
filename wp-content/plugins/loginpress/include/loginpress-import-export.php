<?php
/**
 * LoginPress Import Export Page Content.
 * @package LoginPress
 * @since 1.0.19
 * @version 1.1.14
 */

$loginpress_import_nonce = wp_create_nonce('loginpress-import-nonce');
$loginpress_export_nonce = wp_create_nonce('loginpress-export-nonce');
?>
<div class="loginpress-import-export-page">
  <h2><?php esc_html_e( 'Import/Export LoginPress Settings', 'loginpress' ); ?></h2>
  <div class=""><?php esc_html_e( "Import/Export your LoginPress Settings for/from other sites. This will export/import all the settings including Customizer settings as well.", 'loginpress' ); ?></div>
  <table class="form-table">
    <tbody>
    <tr class="import_setting">
        <th scope="row">
          <label for="loginpress_configure[import_setting]"><?php esc_html_e( 'Import Settings:', 'loginpress' ); ?></label>
        </th>
        <td>
          <input type="file" name="loginPressImport" id="loginPressImport">
          <input type="button" class="button loginpress-import" value="<?php esc_html_e( 'Import', 'loginpress' ); ?>" multiple="multiple" disabled="disabled">
          <input type="hidden" class="loginpress_import_nonce" name="loginpress_import_nonce" value="<?php echo $loginpress_import_nonce; ?>">
          <span class="import-sniper">
            <img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>">
          </span>
          <span class="import-text"><?php esc_html_e( 'LoginPress Settings Imported Successfully.', 'loginpress' ); ?></span>
          <span class="wrong-import"></span>
          <p class="description"><?php esc_html_e( 'Select a file and click on Import to start processing.', 'loginpress' ); ?></p>
        </td>
      </tr>
      <tr class="export_setting">
        <th scope="row">
          <label for="loginpress_configure[export_setting]"><?php esc_html_e( 'Export Settings:', 'loginpress' ); ?></label>
        </th>
        <td>
          <input type="button" class="button loginpress-export" value="<?php esc_html_e( 'Export', 'loginpress' ); ?>">
          <input type="hidden" class="loginpress_export_nonce" name="loginpress_export_nonce" value="<?php echo $loginpress_export_nonce; ?>">
          <span class="export-sniper">
            <img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>">
          </span>
          <span class="export-text"><?php esc_html_e( 'LoginPress Settings Exported Successfully!', 'loginpress' ); ?></span>
          <p class="description"><?php esc_html_e( 'Export LoginPress Settings.', 'loginpress' ) ?></p>
        </td>
      </tr>
    </tbody>
  </table>
</div>
