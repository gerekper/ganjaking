<?php

namespace MailPoet\Form;

if (!defined('ABSPATH')) exit;


class PreviewWidget extends \WP_Widget {

  /** @var string */
  private $formHtml;

  public function __construct($formHtml) {
    $this->formHtml = $formHtml;
    parent::__construct(
      'mailpoet_form_preview',
      'Dummy form form preview',
      []
    );
  }

  /**
   * Output the widget itself.
   */
  public function widget($args, $instance = null) {
    echo $this->formHtml;
  }
}
