<?php


function feml_models_posts_insertMetas($id, $metas) {
  global $wpdb;

  foreach ($metas as $meta) {
    $query = "
      INSERT INTO $wpdb->postmeta
      SET post_id = %d,
          meta_key = %s,
          meta_value = %s
    ";

    $params = array($id, $meta->meta_key, $meta->meta_value);

    $res = $wpdb->query($wpdb->prepare($query, $params));
    if ($res === false) feml_error($wpdb->last_error);
  }
}
