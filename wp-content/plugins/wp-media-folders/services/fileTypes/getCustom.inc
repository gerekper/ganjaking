<?php


function feml_services_fileTypes_getCustom() {
  $mimeTypes = get_option('feml_mime_types', serialize(array()));
  $mimeTypes = unserialize($mimeTypes);

  return $mimeTypes;
}
