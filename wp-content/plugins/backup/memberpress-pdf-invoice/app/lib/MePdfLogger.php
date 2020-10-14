<?php

class MePdfLogger extends \Psr\Log\AbstractLogger {

  public function log( $level, $message, array $context = array() ) {
    MeprUtils::debug_log( $message );
  }
}
