<?php
  include '../../../../../wp-load.php';
  if ( !function_exists('media_handle_upload') ) {
    include_once( "../../../../../wp-admin" . '/includes/image.php');
    include_once( "../../../../../wp-admin" . '/includes/file.php');
    include_once( "../../../../../wp-admin" . '/includes/media.php');
  }

  if ( !function_exists('media_handle_upload') ) {
    if ( defined( 'ABSPATH' ) ) {
      include_once(ABSPATH . "wp-admin" . '/includes/image.php');
      include_once(ABSPATH . "wp-admin" . '/includes/file.php');
      include_once(ABSPATH . "wp-admin" . '/includes/media.php');
    }
  }

  /*******************************************************
   * Only these origins will be allowed to upload images *
   ******************************************************/
  $accepted_origins = array(get_bloginfo('url'));

  /*********************************************
   * Change this line to set the upload folder *
   *********************************************/
  $imageFolder = "images/";

  reset ($_FILES);
  $temp = current($_FILES);
  //if (is_uploaded_file($temp['tmp_name'])){
    // if (isset($_SERVER['HTTP_ORIGIN'])) {
    //   // same-origin requests won't set an origin. If the origin is set, it must be valid.
    //   if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
    //     header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    //   } else {
    //     header("HTTP/1.0 403 Origin Denied");
    //     return;
    //   }
    // }

    /*
      If your script needs to receive cookies, set images_upload_credentials : true in
      the configuration and enable the following two headers.
    */
    // header('Access-Control-Allow-Credentials: true');
    // header('P3P: CP="There is no P3P policy."');

    // Sanitize input
    // if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
    //     header("HTTP/1.0 500 Invalid file name.");
    //     return;
    // }

    // Verify extension
    if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("jpeg","gif", "jpg", "png"))) {
        header("HTTP/1.0 500 Invalid extension.");
        return;
    }

    // Accept upload if there was no origin, or if it is an accepted origin
    // $filetowrite = $imageFolder . $temp['name'];
    // move_uploaded_file($temp['tmp_name'], $filetowrite);
    if( $temp['size'] > 0
      && $temp['type'] == "image/jpeg"
      || $temp['type'] == "image/png"
      || $temp['type'] == "image/jpg"
      || $temp['type'] == "image/gif") {
      $checkimg = getimagesize($temp['tmp_name']);
      if ($checkimg) {
        $image_id = media_handle_upload( array_keys($_FILES, $temp)[0], 0 );
        if ( is_wp_error( $image_id ) ) {
        } else {
          $filetowrite = wp_get_attachment_image_src( $image_id, 'full' )[0];
        }
      }
    }

    // Respond to the successful upload with JSON.
    // Use a location key to specify the path to the saved image resource.
    // { location : '/your/uploaded/image/file'}
    echo json_encode(array('location' => $filetowrite, 'id' => $image_id));
  //} else {
    // Notify editor that the upload failed
    //header("HTTP/1.0 500 Server Error");
  //}
?>
