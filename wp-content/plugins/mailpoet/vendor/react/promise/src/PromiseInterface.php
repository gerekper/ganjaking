<?php
namespace React\Promise;
if (!defined('ABSPATH')) exit;
interface PromiseInterface
{
 public function then(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null);
}
