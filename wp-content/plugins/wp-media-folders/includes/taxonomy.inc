<?php


add_action('init', function() {
  feml_register_taxonomy();
});


function feml_register_taxonomy() {
  register_taxonomy('feml-folder', 'attachment', array(
    'public' => false,
    'rewrite' => false,
    'hierarchical' => true,
    'update_count_callback' => '_update_generic_term_count'
  ));
}


add_action('add_attachment', function($id) {
  if (!isset($_POST['parent'])) return;
  $parent = intval($_POST['parent']);

  $res = wp_set_post_terms($id, array($parent), 'feml-folder');
  if (is_wp_error($res)) feml_error_wp($res);
});


add_action('delete_attachment', function($id) {
  global $feml_delete_shortcut;

  $meta = wp_get_attachment_metadata($id);

  if (isset($meta['feml_shortcut_childs'])) {
    foreach ($meta['feml_shortcut_childs'] as $childID) {
      wp_delete_attachment($childID, true);
    }
  }

  if (isset($meta['feml_shortcut_parent'])) {
    $parentMeta = wp_get_attachment_metadata($meta['feml_shortcut_parent']);

    if (isset($parentMeta['feml_shortcut_childs'])) {
      $parentMeta['feml_shortcut_childs'] = array_diff($parentMeta['feml_shortcut_childs'], array($id));
      wp_update_attachment_metadata($meta['feml_shortcut_parent'], $parentMeta);
    }
  }

  $feml_delete_shortcut = isset($meta['feml_shortcut_parent']);
});


add_filter('wp_delete_file', function($path) {
  global $feml_delete_shortcut;

  if ($feml_delete_shortcut) return '';
  return $path;
});
