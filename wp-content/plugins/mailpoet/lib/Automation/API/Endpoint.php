<?php declare(strict_types = 1);

namespace MailPoet\Automation\API;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Exceptions;

abstract class Endpoint {
  public function get(Request $request): Response {
    throw Exceptions::apiMethodNotAllowed();
  }

  public function post(Request $request): Response {
    throw Exceptions::apiMethodNotAllowed();
  }

  public function put(Request $request): Response {
    throw Exceptions::apiMethodNotAllowed();
  }

  public function delete(Request $request): Response {
    throw Exceptions::apiMethodNotAllowed();
  }
}
