<?php


add_filter('upload_mimes', function($mimes) {
  $uploadMimes = feml_services_fileTypes_getUpload();

  foreach ($uploadMimes['add'] as $ext => $mime) {
    $mimes[$ext] = $mime;
  }

  foreach ($uploadMimes['remove'] as $ext => $mime) {
    if (!isset($mimes[$ext])) {
      foreach ($mimes as $exp => $mime2) {
        if (in_array($ext, explode('|', $exp))) {
          $newExp = implode('|', array_diff(explode('|', $exp), array($ext)));
          $mimes[$newExp] = $mime2;
          unset($mimes[$exp]);
        }
      }
    } else {
      unset($mimes[$ext]);
    }
  }

  return $mimes;
});
