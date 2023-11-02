<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerce\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Integrations\WordPress\Payloads\CommentPayload;

class ReviewPayload extends CommentPayload {
  public function getRating(): int {
    $comment = $this->getComment();
    if (!$comment) {
      return 0;
    }

    //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $rating = $this->wp->getCommentMeta((int)$comment->comment_ID, 'rating', true);
    return is_numeric($rating) ? (int)$rating : 0;
  }
}
