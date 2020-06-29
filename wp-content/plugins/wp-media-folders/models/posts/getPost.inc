<?php


function feml_models_posts_getPost($id) {
  global $wpdb;

  $query = "
    SELECT *
    FROM $wpdb->posts
    WHERE ID = %d
  ";

  $res = $wpdb->query($wpdb->prepare($query, array($id)));
  if ($res === false) feml_error($wpdb->last_error);

  if (count($wpdb->last_result) === 0) {
    return false;
  }

  $post = $wpdb->last_result[0];

  return $post;
}
