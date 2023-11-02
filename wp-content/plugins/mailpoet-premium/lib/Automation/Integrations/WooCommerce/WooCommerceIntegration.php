<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Premium\Automation\Integrations\WooCommerce\Subjects\ReviewSubject;
use MailPoet\Premium\Automation\Integrations\WooCommerce\Triggers\MadeAReviewTrigger;

class WooCommerceIntegration implements Integration {

  /** @var MadeAReviewTrigger */
  private $madeAReview;

  /** @var ReviewSubject */
  private $reviewSubject;

  public function __construct(
    MadeAReviewTrigger $madeAReview,
    ReviewSubject $reviewSubject
  ) {
    $this->madeAReview = $madeAReview;
    $this->reviewSubject = $reviewSubject;
  }

  public function register(Registry $registry): void {
    $registry->addTrigger($this->madeAReview);
    $registry->addSubject($this->reviewSubject);
  }
}
