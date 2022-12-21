<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\AddTagAction;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\AddToListAction;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\RemoveFromListAction;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\RemoveTagAction;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\UnsubscribeAction;

class MailPoetPremiumIntegration implements Integration {

  /** @var UnsubscribeAction */
  private $unsubscribeAction;

  /** @var AddTagAction */
  private $addTagAction;

  /** @var RemoveTagAction  */
  private $removeTagAction;

  /** @var AddToListAction */
  private $addToListAction;

  /** @var RemoveFromListAction */
  private $removeFromListAction;

  public function __construct(
    UnsubscribeAction $unsubscribeAction,
    AddTagAction $addTagAction,
    RemoveTagAction $removeTagAction,
    AddToListAction $addToListAction,
    RemoveFromListAction $removeFromListAction
  ) {
    $this->unsubscribeAction = $unsubscribeAction;
    $this->addTagAction = $addTagAction;
    $this->removeTagAction = $removeTagAction;
    $this->addToListAction = $addToListAction;
    $this->removeFromListAction = $removeFromListAction;
  }

  public function register(Registry $registry): void {
    $registry->addAction($this->unsubscribeAction);
    $registry->addAction($this->addTagAction);
    $registry->addAction($this->removeTagAction);
    $registry->addAction($this->addToListAction);
    $registry->addAction($this->removeFromListAction);
  }
}
