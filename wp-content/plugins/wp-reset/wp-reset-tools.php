<?php

/**
 * WP Reset PRO
 * https://wpreset.com/
 * (c) WebFactory Ltd, 2015 - 2021
 */


// include only file
if (!defined('ABSPATH')) {
  die('Do not open this file directly.');
}


class WP_Reset_Tools
{
  /**
   * Purge all cache for popular caching plugins
   *
   * @return bool true
   */
  function do_purge_cache()
  {
    global $wp_reset;

    wp_cache_flush();
    $wp_reset->do_delete_transients();
    
    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain();
    }

    if (function_exists('w3tc_flush_all')) {
      w3tc_flush_all();
    }
    if (function_exists('wp_cache_clear_cache')) {
      wp_cache_clear_cache();
    }
    if (method_exists('LiteSpeed_Cache_API', 'purge_all')) {
      LiteSpeed_Cache_API::purge_all();
    }
    if (class_exists('Endurance_Page_Cache')) {
      $epc = new Endurance_Page_Cache;
      $epc->purge_all();
    }
    if (class_exists('SG_CachePress_Supercacher') && method_exists('SG_CachePress_Supercacher', 'purge_cache')) {
      SG_CachePress_Supercacher::purge_cache(true);
    }
    if (class_exists('SiteGround_Optimizer\Supercacher\Supercacher')) {
      SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
    }
    if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
      $GLOBALS['wp_fastest_cache']->deleteCache(true);
    }
    if (is_callable(array('Swift_Performance_Cache', 'clear_all_cache'))) {
      Swift_Performance_Cache::clear_all_cache();
    }
    if (is_callable(array('Hummingbird\WP_Hummingbird', 'flush_cache'))) {
      Hummingbird\WP_Hummingbird::flush_cache(true, false);
    }

    do_action('wp_reset_purge_cache');

    return true;
  } // do_purge_cache


  /**
   * Delete all widgets.
   *
   * @return int  Number of deleted widgets
   */
  function do_delete_widgets()
  {
    global $wpdb;

    do_action('wp_reset_before_delete_widgets');

    $count = (int) delete_option('sidebars_widgets');
    $count += $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'widget\_%'");

    do_action('wp_reset_delete_widgets', $count);

    return $count;
  } // do_delete_widgets


  /**
   * Delete content for selected post types.
   *
   * @return int  Number of deleted database rows
   */
  function do_delete_content($params = array())
  {
    global $wpdb;
    $count = 0;
    $params = shortcode_atts(array('types' => array()), (array) $params);

    if (empty($params['types'])) {
      do_action('wp_reset_delete_content', $params, 0);
      return 0;
    }

    do_action('wp_reset_before_delete_content', $params);

    foreach ($params['types'] as $type) {
      if (empty($type)) {
        continue;
      }

      if ($type == '_comments') {
        $count += (int) $wpdb->query("DELETE FROM $wpdb->comments");
        $wpdb->query("DELETE FROM $wpdb->commentmeta");
        $wpdb->query("UPDATE $wpdb->posts SET comment_count = 0");
      } elseif ($type == '_users') {
        if (!function_exists('wp_delete_user')) {
          require_once ABSPATH . 'wp-admin/includes/user.php';
        }

        $current_user = wp_get_current_user();
        $users = $wpdb->get_results($wpdb->prepare("SELECT id FROM $wpdb->users WHERE id != %d", array($current_user->ID)));
        foreach ($users as $user) {
          // double check just in case
          if ($user->ID == $current_user->ID) {
            continue;
          }
          wp_delete_user($user->id, $current_user->ID);
          $count++;
        } // foreach users
      } elseif (substr($type, 0, 5) == '_tax_' && strlen($type) > 5) {
        $type = substr($type, 5);
        $terms = get_terms($type, array('fields' => 'ids', 'hide_empty' => false));
        foreach ($terms as $term) {
          wp_delete_term($term, $type);
        }
        $count += sizeof($terms);
      } else {
        // a few left joins would be faster but for clarity we'll do 5 queries
        $count += (int) $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_type=%s", array(trim($type))));
        $wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT ID FROM $wpdb->posts)");
        $wpdb->query("DELETE FROM $wpdb->term_relationships WHERE object_id NOT IN (SELECT ID FROM $wpdb->posts)");
        $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_post_ID NOT IN (SELECT ID FROM $wpdb->posts)");
        $wpdb->query("DELETE FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_ID FROM $wpdb->comments)");
        $wpdb->query($wpdb->prepare("UPDATE $wpdb->term_taxonomy tt
        SET count =
        (SELECT count(p.ID) FROM $wpdb->term_relationships  tr
        LEFT JOIN $wpdb->posts p
        ON (p.ID = tr.object_id AND p.post_type = %s AND p.post_status = 'publish')
        WHERE tr.term_taxonomy_id = tt.term_taxonomy_id)", array($type)));
      }
    } // foreach types

    do_action('wp_reset_delete_content', $params, $count);

    return $count;
  } // do_delete_content

  /**
   * Delete All Must Use Plugins
   *
   * @return int  Number of mu plugins deleted
   */
  function do_delete_mu_plugins($params = array())
  {
    global $wp_reset;
    $mu_plugins = get_mu_plugins();

    if(empty($mu_plugins)){
      return 0;
    }

    $tmp = $wp_reset->delete_folder(WPMU_PLUGIN_DIR, WPMU_PLUGIN_DIR);

    do_action('wp_reset_delete_mu_plugins', $mu_plugins, $tmp);

    if ($tmp) {
      return count($mu_plugins);
    } else {
      return new WP_Error(1, 'Could not delete MU plugins.');
    }
  }

  /**
   * Delete All Dropins
   *
   * @return int  Number of dropins deleted
   */
  function do_delete_dropins($params = array())
  {
    $dropins = _get_dropins();
    $deleted_dropins = 0;
    $found_dropins = 0;
    foreach ($dropins as $file => $details) {
      if (file_exists(trailingslashit(WP_CONTENT_DIR) . $file)) {
        $found_dropins++;
        if (unlink(trailingslashit(WP_CONTENT_DIR) . $file)) {
          $deleted_dropins++;
        }
      }
    }

    do_action('wp_reset_delete_dropins', $dropins, $found_dropins, $deleted_dropins);

    if ($found_dropins - $deleted_dropins != 0) {
      return new WP_Error(1, ($found_dropins - $deleted_dropins) . ' dropins could not be deleted.');
    }

    return $deleted_dropins;
  }

  /**
   * Reset all plugins options by resetting the options table.
   *
   * @return int  Number of deleted widgets
   */
  function do_reset_options($params = array())
  {
    global $wpdb, $wp_reset;

    if (!function_exists('populate_options')) {
      require_once ABSPATH . 'wp-admin/includes/upgrade.php';
      require_once ABSPATH . 'wp-admin/includes/schema.php';
    }

    if (!function_exists('activate_plugin')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
      require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
      require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    }

    $old_options_table = $wpdb->options;

    if (!$old_options_table) {
      return new WP_Error(1, 'Unable to access old options table.');
    }

    if (!function_exists('populate_options') || !function_exists('activate_plugin')) {
      return new WP_Error(1, 'Functions required to rebuild options table are not available.');
    }

    $GLOBALS['wpr_autosnapshot_done'] = true;
    do_action('wp_reset_before_reset_options', $params);

    $old_options = array();
    $old_options['wp-reset'] = $wp_reset->get_all_options();
    $old_options['wp-reset-snapshots'] = $wp_reset->get_snapshots();
    $old_options['wf_licensing_wpr'] = get_option('wf_licensing_wpr');

    $old_options['blogname'] = get_option('blogname');
    $old_options['blog_public'] = get_option('blog_public');
    $old_options['WPLANG'] = get_option('WPLANG');
    $old_options['siteurl'] = get_option('siteurl');
    $old_options['home'] = get_option('home');
    $old_options['gmt_offset'] = get_option('gmt_offset');
    $old_options['timezone_string'] = get_option('timezone_string');

    $active_plugins = get_option('active_plugins');
    $active_theme = wp_get_theme();

    $wpdb->options = $wpdb->options . '_' . $wp_reset->generate_snapshot_uid();
    $wpdb->query("CREATE TABLE $wpdb->options LIKE $old_options_table");

    populate_options($old_options);
    wp_cache_flush();
    populate_roles();

    if ($wpdb->get_var("SELECT COUNT(option_id) FROM $wpdb->options") < 90) {
      return new WP_Error(1, 'Unable to generate new options table.');
    }

    $wpdb->query("DROP TABLE $old_options_table");
    $wpdb->query("RENAME TABLE $wpdb->options TO $old_options_table");
    $wpdb->options = $old_options_table;
    wp_cache_flush();

    // reactivate theme
    if (!empty($params['reactivate_theme'])) {
      switch_theme($active_theme->get_stylesheet());
    }

    // reactivate all plugins
    if (!empty($params['reactivate_plugins'])) {
      foreach ($active_plugins as $plugin_file) {
        activate_plugin($plugin_file);
      }
    } else {
      // reactivate only WPR
      activate_plugin(plugin_basename(WP_RESET_FILE));
    }

    do_action('wp_reset_reset_options');

    return true;
  } // do_reset_options


  /**
   * Get a list of WordPress versions available for installation
   *
   * @return array
   */
  function get_wordpress_versions($force = false)
  {
    global $wp_reset_licensing;

    $wordpress_versions = get_transient('wp_reset_wordpress_versions');

    if ($force || !is_array($wordpress_versions) || empty($wordpress_versions)) {
      $response = $wp_reset_licensing->query_licensing_server('wordpress_versions');

      if (is_wp_error($response)) {
        return $response->get_error_message();
      }

      $wordpress_versions = array_reverse($response['data']);

      if (is_array($wordpress_versions)) {
        set_transient('wp_reset_wordpress_versions', $wordpress_versions, DAY_IN_SECONDS);
      } else {
        return false;
      }
    }

    return $wordpress_versions;
  } // get_wordpress_versions


  /**
   * Switch WP version
   *
   * @return bool
   */
  function do_switch_wp_version($params)
  {
    $GLOBALS['wpr_autosnapshot_done'] = true;
    @require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    $params = shortcode_atts(array('version' => array()), (array) $params);

    if (empty($params['version'])) {
      return new WP_Error(1, 'No version selected.');
    }

    $locale = get_locale() . '/';
    if ($locale == 'en_US/' || $locale == 'en/') {
      $locale = '';
    }

    $update = new stdClass();
    $update->packages = new stdClass();
    $update->current = new stdClass();

    if ($params['version'] == 'bleeding') {
      $update->download = 'https://wordpress.org/nightly-builds/wordpress-latest.zip';
      $update->packages->full = 'https://wordpress.org/nightly-builds/wordpress-latest.zip';
    } else if (strpos($params['version'], 'point') === 0) {
      $update->download = 'https://wordpress.org/nightly-builds/wordpress-' . str_ireplace('point-', '', $params['version']) . '-latest.zip';
      $update->packages->full = 'https://wordpress.org/nightly-builds/wordpress-' . str_ireplace('point-', '', $params['version']) . '-latest.zip';
    } else {
      $update->download = 'https://downloads.wordpress.org/release/' . $locale . 'wordpress-' . $params['version'] . '.zip';
      $update->packages->full = 'https://downloads.wordpress.org/release/' . $locale . 'wordpress-' . $params['version'] . '.zip';
    }

    $update->packages->partial = false;
    $update->packages->no_content = '';
    $update->packages->new_bundled = '';
    $update->current = $params['version'];
    $update->version = $params['version'];
    $update->response = 'reinstall';

    $skin = new WP_Ajax_Upgrader_Skin();
    $upgrader = new Core_Upgrader($skin);
    $result = $upgrader->upgrade(
      $update,
      array(
        'allow_relaxed_file_ownership' => false,
      )
    );

    do_action('wp_reset_change_wp_version', $params, $result);

    if ($result == $params['version'] || $params['version'] == 'bleeding' || strpos($params['version'], 'point') === 0) {
      return $result;
    } else {
      return new WP_Error(1, 'Unable to switch WP version.');
    }
  } // do_switch_wp_version


  /**
   * Edits snapshot name for provided snapshot UID.
   *
   * @param array $params Accepts: new_name, uid
   * @return string New snapshot name
   */
  function edit_snapshot_name($params)
  {
    global $wp_reset;
    $params = shortcode_atts(array('new_name' => '', 'uid' => ''), (array) $params);
    $params['new_name'] = substr(strip_tags($params['new_name']), 0, 256);

    $snapshots = $wp_reset->get_snapshots();

    if (strlen($params['uid']) != 4 && strlen($params['uid']) != 6) {
      return new WP_Error(1, 'Bad snapshot ID format. Please reload the page.');
    }

    if (!isset($snapshots[$params['uid']])) {
      return new WP_Error(1, 'Unknown snapshot ID. Please reload the page.');
    }

    $snapshots[$params['uid']]['name'] = $params['new_name'];
    update_option('wp-reset-snapshots', $snapshots);

    return stripslashes($params['new_name']);
  } // edit_snapshot_name


  /**
   * Saves snapshot related options.
   *
   * @param array $params
   * @return bool
   */
  function save_snapshot_options($params)
  {
    global $wp_reset;
    $options = $wp_reset->get_options();
    $params = shortcode_atts(array('tools_snapshots' => false, 'events_snapshots' => false, 'snapshots_autoupload' => false, 'autosnapshots_autoupload' => false, 'snapshots_upload_delete' => false, 'scheduled_snapshots' => false, 'prune_snapshots' => false, 'prune_snapshots_details' => false, 'prune_cloud_snapshots' => false, 'prune_cloud_snapshots_details' => false, 'adminbar_snapshots' => false, 'optimize_tables' => false, 'snapshots_size_alert' => 1000, 'throttle_ajax' => false, 'fix_datetime' => false, 'alternate_db_connection' => false, 'ajax_snapshots_export' => false, 'cloud_service' => 'wpr'), (array) $params);

    $options['events_snapshots'] = (int) $params['events_snapshots'];
    $options['snapshots_autoupload'] = (int) $params['snapshots_autoupload'];
    $options['autosnapshots_autoupload'] = (int) $params['autosnapshots_autoupload'];
    $options['snapshots_upload_delete'] = (int) $params['snapshots_upload_delete'];
    $options['tools_snapshots'] = (int) $params['tools_snapshots'];
    $options['scheduled_snapshots'] = (int) $params['scheduled_snapshots'];
    $options['prune_snapshots'] = (int) $params['prune_snapshots'];
    $options['prune_snapshots_details'] = trim($params['prune_snapshots_details']);
    $options['prune_cloud_snapshots'] = (int) $params['prune_cloud_snapshots'];
    $options['prune_cloud_snapshots_details'] = trim($params['prune_cloud_snapshots_details']);
    $options['adminbar_snapshots'] = (int) $params['adminbar_snapshots'];
    $options['optimize_tables'] = (int) $params['optimize_tables'];
    $options['snapshots_size_alert'] = (int) $params['snapshots_size_alert'];
    $options['throttle_ajax'] = (int) $params['throttle_ajax'];
    $options['fix_datetime'] = (int) $params['fix_datetime'];
    $options['alternate_db_connection'] = (int) $params['alternate_db_connection'];
    $options['ajax_snapshots_export'] = (int) $params['ajax_snapshots_export'];
    $wp_reset->update_options('options', $options);

    return true;
  } // save_snapshot_options
} // WP_Reset_Tools

global $wp_reset_tools;
$wp_reset_tools = new WP_Reset_Tools();
