<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class Mepr_Sub_Account_Welcome_Email extends MeprBaseOptionsUserEmail {
  /** Set the default enabled, title, subject & body */
  public function set_defaults($args = array()) {
    $this->title = __('<b>Sub Account Welcome Email</b>', 'memberpress-corporate');
    $this->description = __('This email is sent when a new sub account is added through the manage sub accounts page.', 'memberpress-corporate');
    $this->ui_order = 0;

    $enabled = $use_template = $this->show_form = true;
    $subject = __('** Welcome to {$blog_name}', 'memberpress', 'memberpress-corporate');
    $body = $this->body_partial();

    $this->defaults = compact( 'enabled', 'subject', 'body', 'use_template' );
    $this->variables = MeprTransactionsHelper::get_email_vars();
  }

  public function send_sub_account_welcome_email( $transaction ) {
    $params = MeprTransactionsHelper::get_email_params($transaction);
    $sub_account = $transaction->user();
    $params['reset_password_link'] = $sub_account->reset_password_link();
    $this->to = $sub_account->formatted_email();
    $this->send($params);
  }

  public function body_partial($vars = array()) {
    ob_start();
    require(MPCA_VIEWS_PATH . '/emails/sub_account_welcome.php');
    $view = ob_get_clean();

    return $view;
  }
}
