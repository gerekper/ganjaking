<?php


function feml_models_posts_insertPost($post) {
  global $wpdb;

  $data = array();
  $params = array();

  foreach ($post as $name => $value) {
    $data[] = $name . ' = %s';
    $params[] = $value;
  }

  $data = implode(', ', $data);

  $query = "
    INSERT INTO $wpdb->posts
    SET $data
  ";

  $res = $wpdb->query($wpdb->prepare($query, $params));
  if ($res === false) feml_error($wpdb->last_error);

  $id = intval($wpdb->insert_id);

  return $id;
}
