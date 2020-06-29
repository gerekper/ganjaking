<?php


function feml_services_users_isAdmin($id = null) {
  $user = $id === null
    ? wp_get_current_user()
    : get_userdata($id);

  $isAdmin = isset($user->caps['administrator']);

  if ($id === null && defined('FEML_PREVIEW')) {
    $isAdmin = true;
  }

  return $isAdmin;
}
