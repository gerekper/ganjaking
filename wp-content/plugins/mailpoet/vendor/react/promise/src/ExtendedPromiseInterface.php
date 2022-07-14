<?php
namespace React\Promise;
if (!defined('ABSPATH')) exit;
interface ExtendedPromiseInterface extends PromiseInterface
{
 public function done(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null);
 public function otherwise(callable $onRejected);
 public function always(callable $onFulfilledOrRejected);
 public function progress(callable $onProgress);
}
