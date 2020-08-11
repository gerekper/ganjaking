<?php
defined('WYSIJA') or die('Restricted access');

if (!class_exists('SMTP')) {
  if (is_readable(ABSPATH . WPINC . '/PHPMailer/SMTP.php')) {
    // WordPress 5.5+
    require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
    class_alias('\PHPMailer\PHPMailer\SMTP', 'SMTP');
  } else {
    // WordPress < 5.5
    require_once ABSPATH . WPINC . '/class-smtp.php';
  }
}

class WYSIJA_MySMTP extends SMTP {
  public function StartTLS() {
    stream_context_set_option($this->smtp_conn, "ssl", "verify_peer", false);
    stream_context_set_option($this->smtp_conn, "ssl", "verify_peer_name", false);
    stream_context_set_option($this->smtp_conn, "ssl", "allow_self_signed", true);
    return parent::StartTLS();
  }
}
