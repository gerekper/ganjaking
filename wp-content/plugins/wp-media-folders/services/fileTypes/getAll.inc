<?php


function feml_services_fileTypes_getAll() {
  $mimeTypes = get_site_option('feml_mime_types', serialize(array()));
  $mimeTypes = unserialize($mimeTypes);

  foreach (wp_get_mime_types() as $exp => $mime) {
    foreach (explode('|', $exp) as $ext) {
      $mimeTypes[$ext] = $mime;
    }
  }

  return $mimeTypes;
}
