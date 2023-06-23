<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 * Modified by woocommerce on 14-June-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Automattic\WooCommerce\Bookings\Vendor\Google\Service\Calendar\Resource;

use Automattic\WooCommerce\Bookings\Vendor\Google\Service\Calendar\FreeBusyRequest;
use Automattic\WooCommerce\Bookings\Vendor\Google\Service\Calendar\FreeBusyResponse;

/**
 * The "freebusy" collection of methods.
 * Typical usage is:
 *  <code>
 *   $calendarService = new Automattic\WooCommerce\Bookings\Vendor\Google\Service\Calendar(...);
 *   $freebusy = $calendarService->freebusy;
 *  </code>
 */
class Freebusy extends \Automattic\WooCommerce\Bookings\Vendor\Google\Service\Resource
{
  /**
   * Returns free/busy information for a set of calendars. (freebusy.query)
   *
   * @param FreeBusyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return FreeBusyResponse
   */
  public function query(FreeBusyRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('query', [$params], FreeBusyResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Freebusy::class, 'Google_Service_Calendar_Resource_Freebusy');
