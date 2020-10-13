<?php

namespace MailPoet\Form;

if (!defined('ABSPATH')) exit;


class BlockWrapperRenderer {
  public function render(array $block, string $blockContent): string {
    $classes = isset($block['params']['class_name']) ? " " . $block['params']['class_name'] : '';
    return '<div class="mailpoet_paragraph' . $classes . '">' . $blockContent . '</div>';
  }
}
