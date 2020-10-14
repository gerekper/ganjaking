<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class MpDelightfulDownloads {
  public function __construct() {
    add_action('ddownload_download_before', array($this, 'override_download'));
  }

  public function override_download($dl_id) {
    $download = get_post($dl_id);

    if(MeprRule::is_locked($download)) {
      MeprUtils::wp_redirect($this->get_redirect_url());
    }
  }

  public function get_redirect_url() {
    $uri = $_SERVER['REQUEST_URI'];
    $mepr_options = MeprOptions::fetch();
    $delim = MeprAppCtrl::get_param_delimiter_char($mepr_options->unauthorized_redirect_url);

    if($mepr_options->redirect_on_unauthorized) { //Send to unauth page
      $redirect_to = "{$mepr_options->unauthorized_redirect_url}{$delim}action=mepr_unauthorized&redirect_to={$uri}";
    }
    else { //Send to login page
      $redirect_to = $mepr_options->login_page_url("action=mepr_unauthorized&redirect_to=".urlencode($uri));
    }

    //Handle SSL
    $redirect_to = (MeprUtils::is_ssl())?str_replace('http:', 'https:', $redirect_to):$redirect_to;

    return $redirect_to;
  }
} //end class
