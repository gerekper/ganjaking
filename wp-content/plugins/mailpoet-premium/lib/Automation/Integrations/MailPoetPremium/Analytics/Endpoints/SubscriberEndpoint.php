<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Endpoints;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Exceptions\NotFoundException;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Entities\Query;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Controller\SubscriberController;
use MailPoet\Validator\Builder;

class SubscriberEndpoint extends Endpoint {
  /** @var AutomationStorage */
  private $automationStorage;

  /** @var SubscriberController */
  private $subscriberController;

  public function __construct(
    AutomationStorage $automationStorage,
    SubscriberController $subscriberController
  ) {
    $this->automationStorage = $automationStorage;
    $this->subscriberController = $subscriberController;
  }

  public static function getRequestSchema(): array {
    return [
      'id' => Builder::integer()->required(),
      'query' => Query::getRequestSchema(),
    ];
  }

  public function handle(Request $request): Response {
    $automation = $this->automationStorage->getAutomation(absint($request->getParam('id')));
    if (!$automation) {
      throw new NotFoundException(__('Automation not found', 'mailpoet-premium'));
    }

    $query = Query::fromRequest($request);
    $result = $this->subscriberController->getSubscribersForAutomation($automation, $query);
    return new Response($result);
  }
}
