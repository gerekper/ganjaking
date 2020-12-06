<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpLinksController {
  public static function route()
  {
    $action = (isset($_REQUEST['action'])?$_REQUEST['action']:null);
    if($action=='process-form')
      return self::update_links();
    else
      return self::display_links();
  }

  public static function display_links()
  {
    $links = WafpLink::get_all_objects('image, id');
    global $wafp_options;
    $default_link_id = (isset($wafp_options->default_link_id) && $wafp_options->default_link_id > 0)?$wafp_options->default_link_id:0;
    require(WAFP_VIEWS_PATH . "/links/list.php");
  }

  public static function update_links()
  {
    $errors = array();

    if(!empty($_POST['wafp_link_url'])) //Paul added this check
      foreach( $_POST['wafp_link_url'] as $link_id => $link_url )
        WafpLink::validate( array( 'id' => $link_id, 'target_url' => $link_url,
                                   'slug' => $_POST['wafp_link_slug'][$link_id] ), $errors );

    if(empty($errors))
    {
      // Add New Links
      if(isset($_POST['wafp_new_link_url']) and !empty($_POST['wafp_new_link_url']))
      {
        if( isset($_POST['wafp_new_link_image']) and
            !empty($_POST['wafp_new_link_image']) )
        {
          extract( WafpLink::add_file( $_POST['wafp_new_link_image'] ) );
          $target_url = $_POST['wafp_new_link_url'];
          $info = $_POST['wafp_new_link_info'];
          $slug = $_POST['wafp_new_link_slug'];
          $description = '';
          $link_id = WafpLink::create( compact( 'description', 'target_url', 'info', 'slug', 'image', 'width', 'height' ));
        }
        else
        {
          $link_id = WafpLink::create( array( 'target_url' => $_POST['wafp_new_link_url'],
                                              'info' => $_POST['wafp_new_link_info'],
                                              'slug' => $_POST['wafp_new_link_slug'],
                                              'description' => $_POST['wafp_new_link_description'] ));
        }

      }

      // Update Links
      if (!empty($_POST['wafp_link_url'])) //Paul added this check
      {
        foreach( $_POST['wafp_link_url'] as $id => $target_url )
        {
          $link = WafpLink::get_stored_object($id);

          if(isset($link->rec->image) and !empty($link->rec->image)) {
            $file_info = array( 'image'  => $link->rec->image,
                                'width'  => $link->rec->width,
                                'height' => $link->rec->height );
          }
          else
            $file_info = array( 'image' => '', 'width' => '', 'height' => '' );

          if( isset($_POST['wafp_link_image'][$id]) and
              !empty($_POST['wafp_link_image'][$id]) )
          {
            $file_info = WafpLink::add_file( $_POST['wafp_link_image'][$id] );
            extract($file_info);

            $slug = $_POST['wafp_link_slug'][$id];
            $info = $_POST['wafp_link_info'][$id];

            WafpLink::update( $id, compact( 'target_url', 'slug', 'info', 'image', 'width', 'height' ));
          }
          else
          {
            $description = $_POST['wafp_link_description'][$id];
            $slug = $_POST['wafp_link_slug'][$id];
            $info = $_POST['wafp_link_info'][$id];

            WafpLink::update( $id, compact( 'target_url', 'slug', 'info', 'description' ));
          }
        }
      }

      // Default link
      global $wafp_options;
      $wafp_options->default_link_id = isset($_POST['wafp_new_default_link']) ? $link_id : $_POST['wafp_default_link'];
      $wafp_options->custom_default_redirect = isset($_POST['wafp_custom_default_redirect']);
      $wafp_options->custom_default_redirect_url = $_POST['wafp_custom_default_redirect_url'];
      $wafp_options->store();
      $default_link_id = $wafp_options->default_link_id;

      // Display form again...
      $links = WafpLink::get_all_objects('image, id', '', true);
      require(WAFP_VIEWS_PATH . "/links/links_saved.php");
      require(WAFP_VIEWS_PATH . "/links/list.php");
    }
    else
    {
      $links = WafpLink::get_all_objects('image, id', '', true);
      global $wafp_options;
      $default_link_id = $wafp_options->default_link_id;

      require(WAFP_VIEWS_PATH . "/shared/errors.php");
      require(WAFP_VIEWS_PATH . "/links/list.php");
    }
  }

  public static function redirect_link($link_id, $affiliate_id)
  {
    $link = WafpLink::get_stored_object($link_id);
    $link->track_and_redirect($affiliate_id);
  }

  public static function track_link($affiliate_id)
  {
    WafpLink::track($affiliate_id);
    exit; // This method just tracks link and bails
  }

  public static function delete_link($id)
  {
    WafpLink::delete($id);
  }
}
