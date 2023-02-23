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
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\UpdateSubscriberAction;

class MailPoetPremiumIntegration implements Integration {
  /** @var ContextFactory */
  private $contextFactory;

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

  /** @var UpdateSubscriberAction */
  private $updateSubscriberAction;

  public function __construct(
    ContextFactory $contextFactory,
    UnsubscribeAction $unsubscribeAction,
    AddTagAction $addTagAction,
    RemoveTagAction $removeTagAction,
    AddToListAction $addToListAction,
    RemoveFromListAction $removeFromListAction,
    UpdateSubscriberAction $updateSubscriberAction
  ) {
    $this->contextFactory = $contextFactory;
    $this->unsubscribeAction = $unsubscribeAction;
    $this->addTagAction = $addTagAction;
    $this->removeTagAction = $removeTagAction;
    $this->addToListAction = $addToListAction;
    $this->removeFromListAction = $removeFromListAction;
    $this->updateSubscriberAction = $updateSubscriberAction;
  }

  public function register(Registry $registry): void {
    $registry->addContextFactory('mailpoet-premium', function () {
      return $this->contextFactory->getContextData();
    });

    $registry->addAction($this->unsubscribeAction);
    $registry->addAction($this->addTagAction);
    $registry->addAction($this->removeTagAction);
    $registry->addAction($this->addToListAction);
    $registry->addAction($this->removeFromListAction);
    $registry->addAction($this->updateSubscriberAction);
  }
}
