<?php


function feml_services_fileTypes_getSystem() {
  $mimeTypes = array();

  foreach (wp_get_mime_types() as $exp => $mime) {
    foreach (explode('|', $exp) as $ext) {
      $mimeTypes[$ext] = $mime;
    }
  }

  return $mimeTypes;
}
