<?php
namespace React\Promise;
if (!defined('ABSPATH')) exit;
interface CancellablePromiseInterface extends PromiseInterface
{
 public function cancel();
}
