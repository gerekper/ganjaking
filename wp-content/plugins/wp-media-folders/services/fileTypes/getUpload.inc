<?php


function feml_services_fileTypes_getUpload() {
  $uploadMimes = get_option('feml_upload_mimes', serialize(array('add' => array(), 'remove' => array())));
  $uploadMimes = unserialize($uploadMimes);

  return $uploadMimes;
}
