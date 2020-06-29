<?php


register_activation_hook(FEML_FILE, function($network_wide) {
  global $wpdb;

  if (is_multisite() && $network_wide) {
    $blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    foreach ($blogs as $blog) {
      switch_to_blog($blog);
      feml_activate_for_site();
      restore_current_blog();
    }
  } else {
    feml_activate_for_site();
  }
});


add_action('wpmu_new_blog', function($blog) {
  $femlFile = explode('/', FEML_FILE);
  $femlFile = array_slice($femlFile, -2);
  $femlFile = implode('/', $femlFile);

  if (is_plugin_active_for_network($femlFile)) {
    switch_to_blog($blog);
    feml_activate_for_site();
    restore_current_blog();
  }
}, 10, 1);


function feml_activate_for_site() {
  feml_register_taxonomy();

  if (feml_models_folders_getCount() !== 0) return;

  $names = array(
    __('Documents'),
    __('Images'),
    __('Music'),
    __('Videos')
  );

  foreach ($names as $name) {
    $options = array(
      'slug' => 'feml-folder-' . mt_rand(1000000000, 9999999999)
    );

    $res = wp_insert_term($name, 'feml-folder', $options);

    $options = array(
      'term_group' => get_current_user_id(),
      'parent' => 0
    );

    $res = wp_update_term($res['term_id'], 'feml-folder', $options);
  }
}
