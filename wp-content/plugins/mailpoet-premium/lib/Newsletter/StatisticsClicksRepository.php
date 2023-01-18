<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

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
   * @return array<int, array{cnt: int, url: string}>
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

    /**
     *@var array<int, array{cnt: int, url: string}> $result
     */
    $result = $query->getQuery()->getArrayResult();

    return $result;
  }

  /**
   * @param NewsletterEntity $newsletter
   * @return array<int, array{cnt: int, url: string, link_id: string}>
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

    /** @var array<int, array{cnt: int, url: string, link_id: string}> $result */
    $result = $query->getQuery()->getArrayResult();
    return $result;
  }
}
