<?php

namespace MailPoet\Premium\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\StatisticsClickEntity;

/**
 * @extends Repository<StatisticsClickEntity>
 */
class StatisticsClicksRepository extends Repository {
  protected function getEntityClassName() {
    return StatisticsClickEntity::class;
  }

  /**
   * @param NewsletterEntity $newsletter
   * @return mixed[]
   */
  public function getClickedLinks(NewsletterEntity $newsletter) {
    $query = $this->doctrineRepository
      ->createQueryBuilder('clicks')
      ->select('COUNT(DISTINCT clicks.subscriber) as cnt')
      ->join('clicks.link', 'links')
      ->addSelect('links.url as url')
      ->where('clicks.newsletter = :newsletter')
      ->setParameter('newsletter', $newsletter)
      ->orderBy('cnt', 'desc');
    if ($newsletter->getType() === NewsletterEntity::TYPE_WELCOME) {
      $query->groupBy('url');
    } else {
      $query->groupBy('links.id');
    }
    return $query->getQuery()->getArrayResult();
  }

  /**
   * @param NewsletterEntity $newsletter
   * @return mixed
   */
  public function getClickedLinksForFilter(NewsletterEntity $newsletter) {
    $query = $this->doctrineRepository
      ->createQueryBuilder('clicks')
      ->select('COUNT(DISTINCT clicks.subscriber) as cnt')
      ->join('clicks.link', 'links')
      ->addSelect('links.url as url')
      ->addSelect('links.id as link_id')
      ->where('clicks.newsletter = :newsletter')
      ->setParameter('newsletter', $newsletter)
      ->orderBy('url', 'asc')
      ->groupBy('links.id');
    return $query->getQuery()->getResult();
  }
}
