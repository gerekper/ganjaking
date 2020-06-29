<?php

namespace MailPoet\Premium\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Models\Newsletter;
use MailPoet\Models\StatisticsClicks;

class Stats {
  public static function getClickedLinks(Newsletter $newsletter) {
    $groupBy = 'clicks.link_id';
    if ($newsletter->type === Newsletter::TYPE_WELCOME) {
      $groupBy = 'links.url'; // the same URL can have multiple link IDs
    }

    return StatisticsClicks::tableAlias('clicks')
      ->selectExpr(
        'links.url, COUNT(DISTINCT clicks.subscriber_id) as cnt'
      )
      ->join(
        MP_NEWSLETTER_LINKS_TABLE,
        'links.id = clicks.link_id',
        'links'
      )
      ->where('newsletter_id', $newsletter->id)
      ->groupBy($groupBy)
      ->orderByDesc('cnt')
      ->findArray();
  }
}
