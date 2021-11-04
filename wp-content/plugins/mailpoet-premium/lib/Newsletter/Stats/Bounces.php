<?php

namespace MailPoet\Premium\Newsletter\Stats;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\StatisticsBounceEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Listing;
use MailPoetVendor\Doctrine\DBAL\Driver\Statement;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class Bounces {
  const STATUS_BOUNCED = 'bounced';

  /** @var Listing\Handler */
  private $listingHandler;

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    Listing\Handler $listingHandler,
    EntityManager $entityManager
  ) {
    $this->listingHandler = $listingHandler;
    $this->entityManager = $entityManager;
  }

  /**
   * @param array<string, mixed> $data
   * @return Listing\ListingDefinition
   */
  private function parseData($data): Listing\ListingDefinition {
    // check if sort order was specified or default to "desc"
    $data['sort_order'] = ($data['sort_order'] ?? null) === 'asc' ? 'asc' : 'desc';

    // sanitize sort by
    $sortableColumns = ['email', 'status', 'created_at'];
    $sortBy = (!empty($data['sort_by']) && in_array($data['sort_by'], $sortableColumns, true))
      ? $data['sort_by']
      : '';

    if (empty($sortBy)) {
      $sortBy = 'created_at';
    }
    $data['sort_by'] = $sortBy;
    return $this->listingHandler->getListingDefinition($data);
  }

  /**
   * @param array<string, mixed> $data
   *
   * @return array<string, mixed>
   */
  public function get($data = []): array {
    $definition = $this->parseData($data);

    $countQuery = $this->getBouncesQuery($definition, true);
    if ($countQuery) {
      $query = 'SELECT COUNT(*) as cnt FROM ( ' . $countQuery . ' ) t ';
      $count = (int)$this->entityManager->getConnection()->executeQuery($query)->fetchOne();

      $query = $this->getBouncesQuery($definition);
      $query .= " ORDER BY {$definition->getSortBy()} {$definition->getSortOrder()} LIMIT :limit OFFSET :offset ";
      $items = $this
        ->entityManager
        ->getConnection()
        ->executeQuery($query, [
          'limit' => $definition->getLimit(),
          'offset' => $definition->getOffset(),
        ], [
          'limit' => \PDO::PARAM_INT,
          'offset' => \PDO::PARAM_INT,
        ])
        ->fetchAllAssociative();
    } else {
      $count = 0;
      $items = [];
    }

    return [
      'count' => $count,
      'filters' => [],
      'groups' => [],
      'items' => $items,
    ];
  }

  private function getBouncesQuery(Listing\ListingDefinition $definition, bool $count = false): ?string {
    $searchConstraint = '';
    $newsletterId = intval($definition->getParameters()['id']);

    $subscriberTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $bouncesTable = $this->entityManager->getClassMetadata(StatisticsBounceEntity::class)->getTableName();

    if (!empty($definition->getSearch())) {
      $searchConstraint = $this->getSearchConstraint($definition);
      if ($searchConstraint === null) {
        // Nothing was found by search
        return null;
      }
    }

    $fields = [
      'bounces.id',
      'bounces.created_at',
      'bounces.subscriber_id',
      '"' . self::STATUS_BOUNCED . '" as status',
      'subscribers.email',
      'subscribers.first_name',
      'subscribers.last_name',
    ];

    return 'SELECT '
      . self::getColumnList($fields, $count) . ' '
      . 'FROM ' . $bouncesTable . ' bounces '
      . 'LEFT JOIN ' . $subscriberTable . ' subscribers ON subscribers.id = bounces.subscriber_id '
      . 'WHERE bounces.newsletter_id = "' . $newsletterId . '" ' . $searchConstraint;
  }

  private function getSearchConstraint(Listing\ListingDefinition $definition): ?string {
    // Search recipients
    $search = trim($definition->getSearch());
    $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search); // escape for 'LIKE'
    $qb = $this->entityManager->getConnection()->createQueryBuilder();
    $qb->addSelect('id')
      ->from($this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName())
      ->orWhere($qb->expr()->like('email', ':search'))
      ->orWhere($qb->expr()->like('first_name', ':search'))
      ->orWhere($qb->expr()->like('last_name', ':search'))
      ->setParameter('search', '%' . $search . '%');
    $statement = $qb->execute();
    assert($statement instanceof Statement);// for PHPStan, it doesn't know execute always returns Statement for SELECT queries
    $subscriberIds = $statement->fetchAll();

    $subscriberIds = array_column($subscriberIds, 'id');
    if (empty($subscriberIds)) {
      return null;
    }

    return sprintf(
      ' AND subscribers.id IN (%s) ',
      join(',', array_map('intval', $subscriberIds))
    );
  }

  /**
   * @param array<int, mixed> $fields
   * @param bool $count
   *
   * @return string
   */
  private static function getColumnList(array $fields, bool $count = false): string {
    // Select ID field only for counting
    return $count ? reset($fields) : join(', ', $fields);
  }
}
