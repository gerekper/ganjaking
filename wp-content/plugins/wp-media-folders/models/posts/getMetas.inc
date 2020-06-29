<?php


function feml_models_posts_getMetas($id) {
  global $wpdb;

  $query = "
    SELECT meta_key, meta_value
    FROM $wpdb->postmeta
    WHERE post_id = %d
  ";

  $res = $wpdb->query($wpdb->prepare($query, array($id)));
  if ($res === false) feml_error($wpdb->last_error);

  $metas = $wpdb->last_result;

  return $metas;
}
