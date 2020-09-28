<?php

namespace MailPoet\Premium\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\API\JSON\Response;
use MailPoet\Config\AccessControl;
use MailPoet\Listing;
use MailPoet\Premium\API\JSON\v1\ResponseBuilders\SubscriberDetailedStatsResponseBuilder;
use MailPoet\Premium\Subscriber\Stats\SubscriberNewsletterStatsRepository;

class SubscriberDetailedStats extends APIEndpoint {
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_SUBSCRIBERS,
  ];

  /** @var SubscriberNewsletterStatsRepository */
  private $subscriberStatsRepository;

  /** @var SubscriberDetailedStatsResponseBuilder */
  private $responseBuilder;

  /** @var Listing\Handler */
  private $listingHandler;

  public function __construct(
    SubscriberNewsletterStatsRepository $subscriberStatsRepository,
    SubscriberDetailedStatsResponseBuilder $responseBuilder,
    Listing\Handler $listingHandler
  ) {
    $this->subscriberStatsRepository = $subscriberStatsRepository;
    $this->responseBuilder = $responseBuilder;
    $this->listingHandler = $listingHandler;
  }

  public function listing($data = []) {
    $subscriberId = $data['params']['id'] ?? null;
    if (!isset($subscriberId)) {
      return $this->errorResponse(
        [
          APIError::BAD_REQUEST => __('Missing subscriber id.', 'mailpoet-premium'),
        ], [], Response::STATUS_BAD_REQUEST
      );
    }
    $definition = $this->listingHandler->getListingDefinition($data);
    $data = $this->subscriberStatsRepository->getData($definition);
    $statsData = $this->responseBuilder->build($data);
    return $this->successResponse(
      $statsData,
      [
        'count' => $this->subscriberStatsRepository->getCount($definition),
        'filters' => [],
        'groups' => [],
      ]
    );
  }
}
