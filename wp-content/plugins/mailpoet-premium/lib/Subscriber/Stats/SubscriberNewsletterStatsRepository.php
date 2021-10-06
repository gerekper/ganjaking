<?php

namespace MailPoet\Premium\Subscriber\Stats;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\StatisticsOpenEntity;
use MailPoet\Listing\ListingDefinition;
use MailPoet\Listing\ListingRepository;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

class SubscriberNewsletterStatsRepository extends ListingRepository {
  /**
   * @return SubscriberNewsletterStats[]
   */
  public function getData(ListingDefinition $definition): array {
    $opens = parent::getData($definition);
    $subscriberId = $definition->getParameters()['id'] ?? null;
    $newsletterIds = [];
    foreach ($opens as $open) {
      $newsletter = $open->getNewsletter();
      if (!$newsletter instanceof NewsletterEntity) {
        continue;
      }
      $newsletterIds[] = $newsletter->getId();
    }

    $queryBuilder = clone $this->queryBuilder;
    /** @var StatisticsClickEntity[] $clicks */
    $clicks = $queryBuilder
      ->select('c, l, wp')
      ->from(StatisticsClickEntity::class, 'c')
      ->join('c.link', 'l')
      ->leftJoin('c.wooCommercePurchases', 'wp')
      ->where('c.subscriber = :subscriber')
      ->andWhere('c.newsletter IN (:newsletters)')
      ->setParameter(':subscriber', $subscriberId)
      ->setParameter(':newsletters', $newsletterIds)
      ->orderBy('c.createdAt', 'asc')
      ->getQuery()
      ->getResult();

    $clicksMap = [];
    foreach ($clicks as $click) {
      $newsletter = $click->getNewsletter();
      if (!$newsletter instanceof NewsletterEntity) {
        continue;
      }
      if (!isset($clicksMap[$newsletter->getId()])) {
        $clicksMap[$newsletter->getId()] = [];
      }
      $clicksMap[$newsletter->getId()][] = $click;
    }

    $result = [];
    foreach ($opens as $open) {
      $newsletter = $open->getNewsletter();
      if (!$newsletter instanceof NewsletterEntity) {
        continue;
      }
      $result[] = new SubscriberNewsletterStats($newsletter, $open, $clicksMap[$newsletter->getId()] ?? []);
    }
    return $result;
  }

  protected function applySelectClause(QueryBuilder $queryBuilder) {
    $queryBuilder->select('o, n');
  }

  protected function applyFromClause(QueryBuilder $queryBuilder) {
    $queryBuilder->from(StatisticsOpenEntity::class, 'o')
      ->join('o.newsletter', 'n');
  }

  protected function applyGroup(QueryBuilder $queryBuilder, string $group) {
    // No groups for this listing
  }

  protected function applySearch(QueryBuilder $queryBuilder, string $search) {
    $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search); // escape for 'LIKE'
    $queryBuilder
      ->andWhere('n.subject LIKE :search')
      ->setParameter('search', "%$search%");
  }

  /**
   * @param QueryBuilder $queryBuilder
   * @param array<int|string> $filters
   */
  protected function applyFilters(QueryBuilder $queryBuilder, array $filters) {
    // No filters for this listing
  }

  /**
   * @param QueryBuilder $queryBuilder
   * @param array<int|string> $parameters
   */
  protected function applyParameters(QueryBuilder $queryBuilder, array $parameters) {
    $subscriberId = $parameters['id'] ?? null;
    if ($subscriberId) {
      $queryBuilder
        ->andWhere('o.subscriber = :subscriber')
        ->setParameter('subscriber', $subscriberId);
    }
  }

  protected function applySorting(QueryBuilder $queryBuilder, string $sortBy, string $sortOrder) {
    $queryBuilder->addOrderBy('n.sentAt', 'desc');
  }
}
