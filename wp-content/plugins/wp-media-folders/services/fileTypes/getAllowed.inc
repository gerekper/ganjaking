<?php


function feml_services_fileTypes_getAllowed() {
  $mimeTypes = array();

  foreach (get_allowed_mime_types() as $exp => $mime) {
    foreach (explode('|', $exp) as $ext) {
      $mimeTypes[$ext] = $mime;
    }
  }

  return $mimeTypes;
}
