<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class Mepr_Sub_Account_Signup_Email extends MeprBaseOptionsUserEmail {
  /** Set the default enabled, title, subject & body */
  public function set_defaults($args = array()) {
    $this->title = __('<b>Sub Account Signup Email</b>', 'memberpress-corporate');
    $this->description = __('This email is sent when a new sub account signs up using the signup URL.', 'memberpress-corporate');
    $this->ui_order = 0;

    $enabled = $use_template = $this->show_form = true;
    $subject = __('** Welcome to {$blog_name}', 'memberpress', 'memberpress-corporate');
    $body = $this->body_partial();

    $this->defaults = compact( 'enabled', 'subject', 'body', 'use_template' );
    $this->variables = MeprTransactionsHelper::get_email_vars();
  }

  public function send_sub_account_signup_email($transaction) {
    $params = MeprTransactionsHelper::get_email_params($transaction);
    $sub_account = $transaction->user();
    $this->to = $sub_account->formatted_email();
    $this->send($params);
  }

  public function body_partial($vars = array()) {
    ob_start();
    require(MeprView::file('/emails/sub_account_signup'));
    $view = ob_get_clean();

    return $view;
  }
}
