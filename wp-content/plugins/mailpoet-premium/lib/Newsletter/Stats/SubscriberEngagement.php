<?php

namespace MailPoet\Premium\Newsletter\Stats;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterLinkEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\StatisticsNewsletterEntity;
use MailPoet\Entities\StatisticsOpenEntity;
use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\UserAgentEntity;
use MailPoet\Listing;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Premium\Newsletter\StatisticsClicksRepository;
use MailPoet\Premium\Newsletter\StatisticsOpensRepository;
use MailPoet\Premium\Newsletter\StatisticsUnsubscribesRepository;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Doctrine\DBAL\Driver\Statement;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class SubscriberEngagement {
  const STATUS_OPENED = 'opened';
  const STATUS_MACHINE_OPENED = 'machine-opened';
  const STATUS_CLICKED = 'clicked';
  const STATUS_UNSUBSCRIBED = 'unsubscribed';
  const STATUS_UNOPENED = 'unopened';

  /** @var Listing\Handler */
  private $listingHandler;

  /** @var EntityManager */
  private $entityManager;

  /** @var StatisticsClicksRepository */
  private $statisticsClicksRepository;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var StatisticsOpensRepository */
  private $statisticsOpensRepository;

  /** @var StatisticsUnsubscribesRepository */
  private $statisticsUnsubscribesRepository;

  /** @var NewsletterLinkRepository */
  private $newsletterLinkRepository;

  public function __construct(
    Listing\Handler $listingHandler,
    EntityManager $entityManager,
    StatisticsClicksRepository $statisticsClicksRepository,
    StatisticsOpensRepository $statisticsOpensRepository,
    StatisticsUnsubscribesRepository $statisticsUnsubscribesRepository,
    NewsletterLinkRepository $newsletterLinkRepository,
    NewslettersRepository $newslettersRepository
  ) {
    $this->listingHandler = $listingHandler;
    $this->entityManager = $entityManager;
    $this->statisticsClicksRepository = $statisticsClicksRepository;
    $this->newslettersRepository = $newslettersRepository;
    $this->statisticsOpensRepository = $statisticsOpensRepository;
    $this->statisticsUnsubscribesRepository = $statisticsUnsubscribesRepository;
    $this->newsletterLinkRepository = $newsletterLinkRepository;
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
    if (!empty($data['filter']['link'])) {
      $data['group'] = self::STATUS_CLICKED;
    }
    return $this->listingHandler->getListingDefinition($data);
  }

  /**
   * @param array<string, mixed> $data
   *
   * @return array<string, mixed>
   */
  public function get($data = []): array {
    $definition = $this->parseData($data);
    $newsletterId = $definition->getParameters()['id'];
    $newsletter = $this->newslettersRepository->findOneById($newsletterId);

    $countQuery = $this->getStatsQuery($definition, true);
    if ($countQuery) {
      $query = 'SELECT COUNT(*) as cnt FROM ( ' . $countQuery . ' ) t ';
      $count = (int)$this->entityManager->getConnection()->executeQuery($query)->fetchOne();

      $statsQuery = $this->getStatsQuery($definition);
      $query = $statsQuery . " ORDER BY {$definition->getSortBy()} {$definition->getSortOrder()} LIMIT :limit OFFSET :offset ";
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
      'filters' => $this->filters($newsletter),
      'groups' => $this->groups($definition, $newsletter),
      'items' => $items,
    ];
  }

  private function getStatsQuery(Listing\ListingDefinition $definition, $count = false, $group = null, $applyConstraints = true) {
    $filterConstraint = '';
    $searchConstraint = '';
    $newsletterId = intval($definition->getParameters()['id']);

    $subscriberTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $opensTable = $this->entityManager->getClassMetadata(StatisticsOpenEntity::class)->getTableName();
    $clicksTable = $this->entityManager->getClassMetadata(StatisticsClickEntity::class)->getTableName();
    $unsubscribeTable = $this->entityManager->getClassMetadata(StatisticsUnsubscribeEntity::class)->getTableName();
    $statisticsNewsletterTable = $this->entityManager->getClassMetadata(StatisticsNewsletterEntity::class)->getTableName();

    if ($applyConstraints) {
      $filterConstraint = $this->getFilterConstraint($definition);
      $searchConstraint = $this->getSearchConstraint($definition);
      if (($searchConstraint) === false) {
        // Nothing was found by search
        return false;
      }
    }

    $queries = [];

    $fields = [
      'opens.id',
      'opens.subscriber_id',
      '"' . self::STATUS_OPENED . '" as status',
      'opens.created_at',
      'subscribers.email',
      'subscribers.first_name',
      'subscribers.last_name',
    ];

    $queries[self::STATUS_OPENED] = '(SELECT '
      . self::getColumnList($fields, $count) . ' '
      . 'FROM ' . $opensTable . ' opens '
      . 'LEFT JOIN ' . $subscriberTable . ' subscribers ON subscribers.id = opens.subscriber_id '
      . 'WHERE opens.newsletter_id = "' . $newsletterId . '" ' . $searchConstraint . ' '
      . 'AND opens.user_agent_type = "' . UserAgentEntity::USER_AGENT_TYPE_HUMAN . '") ';

    $fields = [
      'opens.id',
      'opens.subscriber_id',
      '"' . self::STATUS_MACHINE_OPENED . '" as status',
      'opens.created_at',
      'subscribers.email',
      'subscribers.first_name',
      'subscribers.last_name',
    ];

    $queries[self::STATUS_MACHINE_OPENED] = '(SELECT '
      . self::getColumnList($fields, $count) . ' '
      . 'FROM ' . $opensTable . ' opens '
      . 'LEFT JOIN ' . $subscriberTable . ' subscribers ON subscribers.id = opens.subscriber_id '
      . 'WHERE opens.newsletter_id = "' . $newsletterId . '" ' . $searchConstraint . ' '
      . 'AND opens.user_agent_type = "' . UserAgentEntity::USER_AGENT_TYPE_MACHINE . '") ';

    $fields = [
      'clicks.id',
      'clicks.subscriber_id',
      '"' . self::STATUS_CLICKED . '" as status',
      'clicks.created_at',
      'subscribers.email',
      'subscribers.first_name',
      'subscribers.last_name',
    ];

    $queries[self::STATUS_CLICKED] = '(SELECT '
      . self::getColumnList($fields, $count) . ' '
      . 'FROM ' . $clicksTable . ' clicks '
      . 'LEFT JOIN ' . $subscriberTable . ' subscribers ON subscribers.id = clicks.subscriber_id '
      . 'WHERE clicks.newsletter_id = "' . $newsletterId . '" ' . $searchConstraint . $filterConstraint . ') ';

    $fields = [
      'unsubscribes.id',
      'unsubscribes.subscriber_id',
      '"' . self::STATUS_UNSUBSCRIBED . '" as status',
      'unsubscribes.created_at',
      'subscribers.email',
      'subscribers.first_name',
      'subscribers.last_name',
    ];

    $queries[self::STATUS_UNSUBSCRIBED] = '(SELECT '
      . self::getColumnList($fields, $count) . ' '
      . 'FROM ' . $unsubscribeTable . ' unsubscribes '
      . 'LEFT JOIN ' . $subscriberTable . ' subscribers ON subscribers.id = unsubscribes.subscriber_id '
      . 'WHERE unsubscribes.newsletter_id = "' . $newsletterId . '" ' . $searchConstraint . ') ';

    $fields = [
      'sent.id',
      'sent.subscriber_id',
      '"' . self::STATUS_UNOPENED . '" as status',
      'sent.sent_at as created_at',
      'subscribers.email',
      'subscribers.first_name',
      'subscribers.last_name',
    ];

    $queries[self::STATUS_UNOPENED] = '(SELECT '
      . self::getColumnList($fields, $count) . ' '
      . 'FROM ' . $statisticsNewsletterTable . ' sent '
      . 'LEFT JOIN ' . $subscriberTable . ' subscribers ON subscribers.id = sent.subscriber_id '
      . 'LEFT JOIN ' . $opensTable . ' opens ON sent.subscriber_id = opens.subscriber_id '
      . ' AND opens.newsletter_id = sent.newsletter_id ' . 'WHERE sent.newsletter_id = "' . $newsletterId . '" '
      . ' AND opens.id IS NULL ' . $searchConstraint . ') ';

    $group = $group ?: $definition->getGroup();

    if (isset($queries[$group])) {
      $statsQuery = $queries[$group];
    } else {
      $statsQuery = join(
        ' UNION ALL ',
        [
          $queries[self::STATUS_OPENED],
          $queries[self::STATUS_MACHINE_OPENED],
          $queries[self::STATUS_CLICKED],
          $queries[self::STATUS_UNSUBSCRIBED],
        ]
      );
    }

    return $statsQuery;
  }

  private function getFilterConstraint(Listing\ListingDefinition $definition): string {
    // Filter by link clicked
    $linkConstraint = '';
    $filters = $definition->getFilters();
    if (!empty($filters['link'])) {
      $link = $this->newsletterLinkRepository->findOneById((int)$filters['link']);
      if ($link instanceof NewsletterLinkEntity) {
        $linkConstraint = ' AND clicks.link_id = "' . $link->getId() . '"';
      }
    }

    return $linkConstraint;
  }

  private function getSearchConstraint(Listing\ListingDefinition $definition) {
    // Search recipients
    $subscriberIds = [];
    if (!empty($definition->getSearch())) {
      $search = trim($definition->getSearch());
      $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search); // escape for 'LIKE'
      $qb = $this->entityManager->getConnection()->createQueryBuilder();
      $qb
        ->addSelect('id')
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
        return false;
      }
    }
    $subscribersConstraint = '';
    if (!empty($subscriberIds)) {
      $subscribersConstraint = sprintf(
        ' AND subscribers.id IN (%s) ',
        join(',', array_map('intval', $subscriberIds))
      );
    }

    return $subscribersConstraint;
  }

  /**
   * @param array<int, mixed> $fields
   * @param bool $count
   *
   * @return string
   */
  private static function getColumnList(array $fields, $count = false): string {
    // Select ID field only for counting
    return $count ? reset($fields) : join(', ', $fields);
  }

  /**
   * @param NewsletterEntity $newsletter
   *
   * @return array[]
   */
  private function filters(NewsletterEntity $newsletter): array {
    $clicks = $this->statisticsClicksRepository->getClickedLinksForFilter($newsletter);


    $linkList = [];
    $linkList[] = [
      'label' => WPFunctions::get()->__('Filter by link clicked', 'mailpoet-premium'),
      'value' => '',
    ];

    foreach ($clicks as $link) {
      $label = sprintf(
        '%s (%s)',
        $link['url'],
        number_format($link['cnt'])
      );

      $linkList[] = [
        'label' => $label,
        'value' => $link['link_id'],
      ];
    }

    return [
      'link' => $linkList,
    ];
  }

  /**
   * @param Listing\ListingDefinition $definition
   * @param NewsletterEntity $newsletter
   *
   * @return array[]
   */
  private function groups(Listing\ListingDefinition $definition, NewsletterEntity $newsletter): array {
    $groups = [
      [
        'name' => self::STATUS_CLICKED,
        'label' => WPFunctions::get()->_x('Clicked', 'Subscriber engagement filter - filter those who clicked on a newsletter link', 'mailpoet-premium'),
        'count' => $this->statisticsClicksRepository->countBy(['newsletter' => $newsletter]),
      ],
      [
        'name' => self::STATUS_OPENED,
        'label' => WPFunctions::get()->_x('Opened', 'Subscriber engagement filter - filter those who opened a newsletter', 'mailpoet-premium'),
        'count' => $this->statisticsOpensRepository->countBy(['newsletter' => $newsletter, 'userAgentType' => UserAgentEntity::USER_AGENT_TYPE_HUMAN]),
      ],
      [
        'name' => self::STATUS_MACHINE_OPENED,
        'label' => WPFunctions::get()->_x('Machine-opened', 'Subscriber engagement filter - shows machine-opens for a given newsletter', 'mailpoet-premium'),
        'count' => $this->statisticsOpensRepository->countBy(['newsletter' => $newsletter, 'userAgentType' => UserAgentEntity::USER_AGENT_TYPE_MACHINE]),
      ],
      [
        'name' => self::STATUS_UNSUBSCRIBED,
        'label' => WPFunctions::get()->_x('Unsubscribed', 'Subscriber engagement filter - filter those who unsubscribed from a newsletter', 'mailpoet-premium'),
        'count' => $this->statisticsUnsubscribesRepository->countBy(['newsletter' => $newsletter]),
      ],
    ];

    array_unshift(
      $groups,
      [
        'name' => 'all',
        'label' => WPFunctions::get()->_x('All engaged', 'Subscriber engagement filter - filter those who performed any action (e.g., clicked, opened, unsubscribed)', 'mailpoet-premium'),
        'count' => array_sum(array_column($groups, 'count')),
      ]
    );

    $subQuery = $this->getStatsQuery($definition, true, self::STATUS_UNOPENED, false);
    $query = ' SELECT COUNT(*) as cnt FROM ( ' . $subQuery . ' ) t ';
    $unopenedCount = (int)$this->entityManager->getConnection()->executeQuery($query)->fetchOne();

    $groups[] = [
      'name' => self::STATUS_UNOPENED,
      'label' => WPFunctions::get()->_x('Unopened', 'Subscriber engagement filter - filter those who did not open a newsletter', 'mailpoet-premium'),
      'count' => $unopenedCount,
    ];

    return $groups;
  }
}
